<?php

namespace App\Services;

use App\Models\NewsSource;
use App\Models\NewsImport;
use App\Models\NewsImportLog;
use App\Models\News;
use App\Models\Category;
use App\Models\Media;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NewsImportService
{
    public function __construct(
        protected RssFeedParserService $parserService,
        protected HomepageBuilderService $homepageBuilderService
    ) {}

    /**
     * Import news items from an authorized news source.
     *
     * @param NewsSource $source
     * @param int|null $maxItems
     * @return NewsImportLog
     */
    public function importFromSource(NewsSource $source, ?int $maxItems = null): NewsImportLog
    {
        $startTime = microtime(true);
        $itemsFound = 0;
        $itemsImported = 0;
        $itemsSkipped = 0;
        $itemsFailed = 0;

        try {
            $parsedItems = $this->parserService->fetch($source->feed_url);
            $itemsFound = count($parsedItems);

            if ($maxItems && $maxItems > 0) {
                $parsedItems = array_slice($parsedItems, 0, $maxItems);
            }

            // Fallback system user for automated imports
            $systemUser = User::whereHas('roles', fn($q) => $q->whereIn('name', ['super_admin', 'editor']))->first() 
                ?? User::first();

            foreach ($parsedItems as $item) {
                try {
                    $result = $this->processFeedItem($source, $item, $systemUser);
                    if ($result === 'imported') {
                        $itemsImported++;
                    } elseif ($result === 'duplicate') {
                        $itemsSkipped++;
                    } else {
                        $itemsFailed++;
                    }
                } catch (\Throwable $e) {
                    $itemsFailed++;
                    Log::warning("Failed to import individual item from source {$source->id}: " . $e->getMessage());
                }
            }

            $executionTime = round(microtime(true) - $startTime, 2);
            $logStatus = $itemsFailed > 0 ? ($itemsImported > 0 ? 'warning' : 'failed') : 'success';
            $message = "সফলভাবে {$itemsImported}টি সংবাদ ড্রাফট হিসেবে সংরক্ষিত হয়েছে, {$itemsSkipped}টি ডুপ্লিকেট বাদ দেওয়া হয়েছে।";

            $source->update([
                'last_fetched_at' => now(),
                'consecutive_failures' => 0,
                'last_error' => null,
            ]);

            return NewsImportLog::create([
                'source_id' => $source->id,
                'status' => $logStatus,
                'items_found' => $itemsFound,
                'items_imported' => $itemsImported,
                'items_skipped_duplicate' => $itemsSkipped,
                'items_failed' => $itemsFailed,
                'execution_time_seconds' => $executionTime,
                'message' => $message,
            ]);

        } catch (\Throwable $e) {
            $executionTime = round(microtime(true) - $startTime, 2);
            $source->increment('consecutive_failures');
            $source->update(['last_error' => $e->getMessage()]);

            return NewsImportLog::create([
                'source_id' => $source->id,
                'status' => 'failed',
                'items_found' => 0,
                'items_imported' => 0,
                'items_skipped_duplicate' => 0,
                'items_failed' => 0,
                'execution_time_seconds' => $executionTime,
                'message' => "ফিড সংগ্রহ ব্যর্থ হয়েছে: " . $e->getMessage(),
            ]);
        }
    }

    /**
     * Process an individual feed item with duplicate checks and draft insertion.
     */
    protected function processFeedItem(NewsSource $source, array $item, ?User $systemUser): string
    {
        $normalizedUrl = $item['source_url'];
        $urlHash = hash('sha256', $normalizedUrl);
        $guid = (string) $item['guid'];
        $guidHash = hash('sha256', $guid);
        $fingerprint = hash('sha256', mb_strtolower(trim($item['title'] . '|' . ($item['summary'] ?? ''))));

        // 1. Check for duplicate
        $existingImport = NewsImport::where('url_hash', $urlHash)
            ->orWhere(function ($q) use ($source, $guidHash, $guid) {
                $q->where('source_id', $source->id)
                  ->where(function ($sq) use ($guidHash, $guid) {
                      $sq->where('guid_hash', $guidHash)
                         ->orWhere('guid', $guid);
                  });
            })
            ->orWhere('content_fingerprint', $fingerprint)
            ->first();

        $existingNews = News::where('source_url', $normalizedUrl)->first();

        if ($existingImport || $existingNews) {
            // Already imported or exists
            if (!$existingImport) {
                NewsImport::create([
                    'source_id' => $source->id,
                    'news_id' => $existingNews?->id,
                    'guid' => $guid,
                    'guid_hash' => $guidHash,
                    'source_url' => $normalizedUrl,
                    'url_hash' => $urlHash,
                    'content_fingerprint' => $fingerprint,
                    'title' => Str::limit($item['title'], 500, ''),
                    'summary' => $item['summary'],
                    'image_url' => $item['image_url'],
                    'published_at' => $item['published_at'],
                    'status' => 'duplicate_skipped',
                    'duplicate_of_news_id' => $existingNews?->id,
                    'raw_payload' => $item['raw_payload'] ?? null,
                ]);
            }
            return 'duplicate';
        }

        // 2. Resolve Category
        $categoryId = $this->resolveCategory($source, $item);

        // 3. Resolve Image / Media
        $coverMediaId = null;
        if (!empty($item['image_url'])) {
            $media = Media::firstOrCreate(
                ['path' => $item['image_url']],
                [
                    'uploaded_by' => $systemUser?->id,
                    'disk' => 'public',
                    'original_name' => basename(parse_url($item['image_url'], PHP_URL_PATH) ?: 'feed-image.jpg'),
                    'mime_type' => 'image/jpeg',
                    'size_bytes' => 0,
                    'caption' => Str::limit($item['title'], 255, ''),
                    'credit' => $source->author_attribution ?: $source->name,
                    'status' => 'active',
                ]
            );
            $coverMediaId = $media->id;
        }

        // 4. Clean Google News / RSS Title & Apply AI Paraphrasing if enabled
        $rawTitle = $item['title'];
        $cleanTitle = preg_replace('/\s*-\s*[^-]+$/u', '', $rawTitle);
        if (empty($cleanTitle)) {
            $cleanTitle = $rawTitle;
        }

        $title = $cleanTitle;
        $summary = $item['summary'] ?? '';
        $bodyHtml = !empty($item['body_html']) ? $item['body_html'] : "<p>" . e($summary) . "</p>";

        $aiParaphrase = (bool) ($source->settings['ai_paraphrase'] ?? false);
        if ($aiParaphrase) {
            try {
                $aiService = app(\App\Services\AI\AIService::class);
                $rewritten = $aiService->paraphraseNews($cleanTitle, $summary, $bodyHtml);
                $title = !empty($rewritten['title']) ? $rewritten['title'] : $cleanTitle;
                $summary = !empty($rewritten['excerpt']) ? $rewritten['excerpt'] : $summary;
                $bodyHtml = !empty($rewritten['body_html']) ? $rewritten['body_html'] : $bodyHtml;
            } catch (\Throwable $e) {
                Log::warning("AI Paraphrasing failed for item '{$cleanTitle}': " . $e->getMessage());
            }
        }
        $bodyPlain = strip_tags($bodyHtml);

        // 5. Generate Unique Slug safely
        $slugBase = Str::slug($title);
        if (empty($slugBase)) {
            $slugBase = Str::lower((string) Str::ulid());
        }
        $slugBase = Str::limit($slugBase, 120, '');
        $slug = $slugBase;
        $counter = 1;
        while (News::where('slug', $slug)->exists()) {
            $slug = "{$slugBase}-{$counter}";
            $counter++;
        }

        // 6. Determine Editorial Status: defaults to review_pending ("সম্পাদনায় থাকবে") if configured, else draft
        $editorialStatus = $source->settings['editorial_status'] ?? ($source->auto_publish ? 'published' : 'draft');
        if ($source->auto_publish) {
            $status = 'published';
            $publishedAt = $item['published_at'] ?? now();
        } else {
            $status = in_array($editorialStatus, ['review_pending', 'draft', 'published']) ? $editorialStatus : 'review_pending';
            $publishedAt = $status === 'published' ? ($item['published_at'] ?? now()) : null;
        }

        return DB::transaction(function () use (
            $source, $item, $systemUser, $normalizedUrl, $urlHash, $guid, $guidHash, $fingerprint,
            $categoryId, $coverMediaId, $slug, $status, $publishedAt, $title, $summary, $bodyHtml, $bodyPlain
        ) {
            $news = News::create([
                'public_id' => (string) Str::ulid(),
                'slug' => $slug,
                'title' => Str::limit($title, 500, ''),
                'excerpt' => $summary ?: Str::limit($bodyPlain, 200),
                'body_html' => $bodyHtml,
                'body_plain' => $bodyPlain,
                'type' => 'article',
                'status' => $status,
                'primary_category_id' => $categoryId,
                'cover_media_id' => $coverMediaId,
                'source_name' => $source->name,
                'source_url' => $normalizedUrl,
                'created_by' => $systemUser?->id ?? 1,
                'published_by' => $status === 'published' ? $systemUser?->id : null,
                'approved_by' => $status === 'published' ? $systemUser?->id : null,
                'published_at' => $publishedAt,
                'content_updated_at' => now(),
                'comments_enabled' => true,
                'lock_version' => 1,
            ]);

            $news->categories()->sync([$categoryId]);

            NewsImport::create([
                'source_id' => $source->id,
                'news_id' => $news->id,
                'guid' => $guid,
                'guid_hash' => $guidHash,
                'source_url' => $normalizedUrl,
                'url_hash' => $urlHash,
                'content_fingerprint' => $fingerprint,
                'title' => Str::limit($title, 500, ''),
                'summary' => $summary,
                'image_url' => $item['image_url'],
                'published_at' => $item['published_at'],
                'status' => $status === 'published' ? 'published' : ($status === 'review_pending' ? 'review_pending' : 'draft_created'),
                'raw_payload' => $item['raw_payload'] ?? null,
            ]);

            // Fallback: If no cover image was found, auto-generate a professional branded cover poster
            if (!$coverMediaId && !app()->environment('testing')) {
                try {
                    $posterService = app(\App\Services\PosterGeneratorService::class);
                    $poster = $posterService->generate($news, '1200x630', null, null, $systemUser);
                    if ($poster && $poster->file_path) {
                        $posterMedia = Media::create([
                            'uploaded_by' => $systemUser?->id,
                            'disk' => 'public',
                            'path' => 'storage/' . $poster->file_path,
                            'original_name' => basename($poster->file_path),
                            'mime_type' => 'image/png',
                            'size_bytes' => 0,
                            'caption' => Str::limit($news->title, 255, ''),
                            'credit' => 'এইচ টেক নিউজ ডেস্ক',
                            'status' => 'active',
                        ]);
                        $news->update(['cover_media_id' => $posterMedia->id]);
                    }
                } catch (\Throwable $e) {
                    Log::warning("Auto poster cover generation failed: " . $e->getMessage());
                }
            }

            if ($status === 'published') {
                app(\App\Services\PublicationWorkflowService::class)->onNewsPublished($news);
            }

            return 'imported';
        });
    }

    /**
     * Match feed categories with system taxonomy.
     */
    protected function resolveCategory(NewsSource $source, array $item): int
    {
        if (!empty($item['categories'])) {
            foreach ($item['categories'] as $catName) {
                $matched = Category::where('name', $catName)
                    ->orWhere('slug', Str::slug($catName))
                    ->first();
                if ($matched) {
                    return $matched->id;
                }
            }
        }

        if ($source->default_category_id) {
            return $source->default_category_id;
        }

        $firstCategory = Category::active()->first();
        return $firstCategory ? $firstCategory->id : 1;
    }
}
