<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\News;
use App\Models\Media;
use App\Models\User;
use App\Services\PosterGeneratorService;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Pool;
use Illuminate\Support\Str;

class BackfillNewsImages extends Command
{
    protected $signature = 'news:backfill-images {--limit=100}';
    protected $description = 'Fetch and backfill cover images for news articles that lack them';

    public function handle(PosterGeneratorService $posterService): int
    {
        $limit = (int) $this->option('limit');
        $systemUser = User::first();

        $newsWithoutImages = News::where(function ($q) {
            $q->whereNull('cover_media_id')
              ->orWhereHas('coverMedia', function ($mq) {
                  $mq->where('path', 'like', '%googleusercontent.com%');
              });
        })
        ->whereNotNull('source_url')
        ->take($limit)
        ->get();

        if ($newsWithoutImages->isEmpty()) {
            $this->info("All news articles already have cover images.");
            return Command::SUCCESS;
        }

        $this->info("Resolving cover images for {$newsWithoutImages->count()} news articles...");

        $chunks = $newsWithoutImages->chunk(15);
        $updatedCount = 0;
        $posterCount = 0;

        foreach ($chunks as $chunk) {
            $responses = Http::pool(function (Pool $pool) use ($chunk) {
                foreach ($chunk as $news) {
                    $pool->as((string) $news->id)
                        ->timeout(6)
                        ->withHeaders([
                            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36',
                            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                        ])
                        ->get($news->source_url);
                }
            });

            foreach ($chunk as $news) {
                $img = null;
                $res = $responses[(string) $news->id] ?? null;
                if ($res && $res->successful()) {
                    $html = $res->body();
                    if (preg_match('/<meta[^>]+(?:property|name)=["\']og:image["\'][^>]+content=["\']([^"\']+)["\']/i', $html, $m)) {
                        $img = $m[1];
                    } elseif (preg_match('/content=["\']([^"\']+)["\'][^>]+(?:property|name)=["\']og:image["\']/i', $html, $m)) {
                        $img = $m[1];
                    } elseif (preg_match('/<meta[^>]+(?:property|name)=["\']twitter:image["\'][^>]+content=["\']([^"\']+)["\']/i', $html, $m)) {
                        $img = $m[1];
                    }

                    if ($img && (str_contains($img, 'googleusercontent.com') || str_contains($img, 'google.com/images'))) {
                        $img = null;
                    }
                }

                if ($img) {
                    $media = Media::firstOrCreate(
                        ['path' => $img],
                        [
                            'uploaded_by' => $systemUser?->id,
                            'disk' => 'public',
                            'original_name' => basename(parse_url($img, PHP_URL_PATH) ?: 'news-cover.jpg'),
                            'mime_type' => 'image/jpeg',
                            'size_bytes' => 0,
                            'caption' => Str::limit($news->title, 255, ''),
                            'credit' => $news->source_name ?: 'নিউজ ডেস্ক',
                            'status' => 'active',
                        ]
                    );
                    $news->update(['cover_media_id' => $media->id]);
                    $this->line("  ✓ #{$news->id} Got Photo: " . Str::limit($news->title, 40));
                    $updatedCount++;
                } else {
                    // Generate branded poster card
                    try {
                        $poster = $posterService->generate($news, '1200x630', null, null, $systemUser);
                        if ($poster && $poster->file_path) {
                            $media = Media::create([
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
                            $news->update(['cover_media_id' => $media->id]);
                            $this->line("  🖼️ #{$news->id} Generated Poster: " . Str::limit($news->title, 40));
                            $posterCount++;
                        }
                    } catch (\Throwable $e) {
                        $this->warn("  ✗ #{$news->id} Failed: " . $e->getMessage());
                    }
                }
            }
        }

        $this->info("Completed: {$updatedCount} photos assigned, {$posterCount} branded cards generated.");
        return Command::SUCCESS;
    }
}

