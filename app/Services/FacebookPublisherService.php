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
            $localFile = $this->resolveLocalFilePath($mediaUrl, $news);

            if ($localFile && file_exists($localFile)) {
                // Direct multipart file upload (works on localhost, private networks, and live servers)
                $response = Http::timeout(35)
                    ->retry(2, 1000, throw: false)
                    ->attach('source', file_get_contents($localFile), basename($localFile))
                    ->post("{$endpoint}/photos", [
                        'access_token' => $account->access_token,
                        'caption' => $socialPost->post_text . "\n\nবিস্তারিত পড়ুন: " . $news->url,
                    ]);
            } elseif ($mediaUrl && filter_var($mediaUrl, FILTER_VALIDATE_URL) && !str_contains($mediaUrl, '127.0.0.1') && !str_contains($mediaUrl, 'localhost')) {
                // Remote accessible public URL
                $response = Http::timeout(25)
                    ->retry(2, 1000, throw: false)
                    ->post("{$endpoint}/photos", [
                        'access_token' => $account->access_token,
                        'url' => $mediaUrl,
                        'caption' => $socialPost->post_text . "\n\nবিস্তারিত পড়ুন: " . $news->url,
                    ]);
            } else {
                // Link feed post fallback
                $response = Http::timeout(25)
                    ->retry(2, 1000, throw: false)
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

    /**
     * Resolve absolute local filesystem path for the media (poster or cover image).
     */
    protected function resolveLocalFilePath(?string $mediaUrl, News $news): ?string
    {
        // 1. Check generated poster for the article
        $poster = GeneratedPoster::where('news_id', $news->id)->latest()->first();
        if ($poster && $poster->file_path) {
            $path = storage_path('app/public/' . $poster->file_path);
            if (file_exists($path)) {
                return $path;
            }
        }

        // 2. Check cover media
        if ($news->coverMedia && $news->coverMedia->path) {
            $clean = str_replace(['storage/', 'public/'], '', $news->coverMedia->path);
            $path = storage_path('app/public/' . $clean);
            if (file_exists($path)) {
                return $path;
            }
            if (file_exists(public_path($news->coverMedia->path))) {
                return public_path($news->coverMedia->path);
            }
        }

        // 3. Check mediaUrl parsed path
        if ($mediaUrl) {
            $parsed = parse_url($mediaUrl, PHP_URL_PATH);
            if ($parsed) {
                $clean = preg_replace('#^/?(storage|public)/#', '', $parsed);
                $path = storage_path('app/public/' . $clean);
                if (file_exists($path)) {
                    return $path;
                }
            }
        }

        return null;
    }
}
