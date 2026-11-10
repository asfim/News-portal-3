<?php

namespace App\Services;

use App\Models\SocialAccount;
use App\Models\SocialPost;
use App\Models\SocialPostLog;
use App\Models\News;
use App\Models\GeneratedPoster;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Exception;

class FacebookPublisherService
{
    protected string $graphApiVersion = 'v20.0';

    /**
     * Publish a news article to a connected Facebook Page.
     *
     * @param SocialAccount $account
     * @param News $news
     * @param string|null $customMessage
     * @param string|null $customMediaUrl
     * @return SocialPost
     */
    public function publishArticle(
        SocialAccount $account,
        News $news,
        ?string $customMessage = null,
        ?string $customMediaUrl = null
    ): SocialPost {
        $idempotencyKey = hash('sha256', "fb_{$account->id}_{$news->id}");

        // Check if post already exists
        $socialPost = SocialPost::firstOrCreate(
            ['idempotency_key' => $idempotencyKey],
            [
                'social_account_id' => $account->id,
                'news_id' => $news->id,
                'post_text' => $customMessage ?: $this->formatPostMessage($news),
                'media_url' => $customMediaUrl ?: $this->resolveMediaUrl($news),
                'status' => 'publishing',
            ]
        );

        if ($socialPost->status === 'published') {
            return $socialPost;
        }

        $socialPost->update(['status' => 'publishing']);

        // Check if testing/mock
        if (app()->environment('testing') || empty($account->access_token) || $account->account_id === 'mock_page') {
            $mockPostId = "mock_post_" . Str::random(12);
            $socialPost->update([
                'status' => 'published',
                'external_post_id' => $mockPostId,
                'published_at' => now(),
                'last_error' => null,
            ]);

            SocialPostLog::create([
                'social_post_id' => $socialPost->id,
                'action' => 'mock_publish',
                'status' => 'success',
                'response_payload' => ['id' => $mockPostId],
            ]);

            return $socialPost;
        }

        try {
            $endpoint = "https://graph.facebook.com/{$this->graphApiVersion}/{$account->account_id}";
            $mediaUrl = $socialPost->media_url;

            if ($mediaUrl && filter_var($mediaUrl, FILTER_VALIDATE_URL)) {
                // Photo post with caption & article link
                $response = Http::timeout(25)
                    ->retry(3, 1000, throw: false)
                    ->post("{$endpoint}/photos", [
                        'access_token' => $account->access_token,
                        'url' => $mediaUrl,
                        'caption' => $socialPost->post_text . "\n\nবিস্তারিত পড়ুন: " . $news->url,
                    ]);
            } else {
                // Link feed post
                $response = Http::timeout(25)
                    ->retry(3, 1000, throw: false)
                    ->post("{$endpoint}/feed", [
                        'access_token' => $account->access_token,
                        'message' => $socialPost->post_text,
                        'link' => $news->url,
                    ]);
            }

            if (!$response->successful()) {
                $errorMsg = $response->json('error.message') ?? $response->body();
                throw new Exception("Meta Graph API Error: " . $errorMsg);
            }

            $responseData = $response->json();
            $externalId = $responseData['id'] ?? $responseData['post_id'] ?? null;

            $socialPost->update([
                'status' => 'published',
                'external_post_id' => $externalId,
                'published_at' => now(),
                'last_error' => null,
            ]);

            SocialPostLog::create([
                'social_post_id' => $socialPost->id,
                'action' => 'publish_success',
                'status' => 'success',
                'response_payload' => $responseData,
            ]);

            return $socialPost;

        } catch (\Throwable $e) {
            $socialPost->increment('retry_count');
            $socialPost->update([
                'status' => 'failed',
                'last_error' => $e->getMessage(),
            ]);

            SocialPostLog::create([
                'social_post_id' => $socialPost->id,
                'action' => 'publish_error',
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            return $socialPost;
        }
    }

    /**
     * Format engaging Bengali post message with headline and brief excerpt.
     */
    protected function formatPostMessage(News $news): string
    {
        $category = $news->primaryCategory?->name ?? 'জাতীয়';
        $excerpt = $news->excerpt ? Str::limit($news->excerpt, 140) : '';

        return "【{$category}】 {$news->title}\n\n{$excerpt}";
    }

    /**
     * Resolve best image (Generated Poster, then Cover Image).
     */
    protected function resolveMediaUrl(News $news): ?string
    {
        $poster = GeneratedPoster::where('news_id', $news->id)->latest()->first();
        if ($poster) {
            return $poster->url;
        }

        if ($news->coverMedia) {
            return $news->coverMedia->url;
        }

        return null;
    }
}
