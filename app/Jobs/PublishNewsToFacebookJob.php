<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\SocialAccount;
use App\Models\News;
use App\Services\FacebookPublisherService;
use App\Services\PosterGeneratorService;
use App\Models\GeneratedPoster;
use Illuminate\Support\Facades\Log;

class PublishNewsToFacebookJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(
        public SocialAccount $account,
        public News $news,
        public ?string $customMessage = null,
        public ?string $customMediaUrl = null
    ) {}

    public function handle(FacebookPublisherService $service, PosterGeneratorService $posterService): void
    {
        $mediaUrl = $this->customMediaUrl;

        // If no poster URL provided yet, check or generate on-demand
        if (empty($mediaUrl)) {
            $existingPoster = GeneratedPoster::where('news_id', $this->news->id)->latest()->first();
            if (!$existingPoster) {
                try {
                    $existingPoster = $posterService->generate($this->news, '1080x1080');
                } catch (\Throwable $e) {
                    Log::warning("On-demand poster generation in Facebook job failed for news {$this->news->id}: " . $e->getMessage());
                }
            }
            $mediaUrl = $existingPoster?->url;
        }

        $service->publishArticle(
            $this->account,
            $this->news,
            $this->customMessage,
            $mediaUrl
        );
    }
}
