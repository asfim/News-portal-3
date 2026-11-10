<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\SocialAccount;
use App\Models\News;
use App\Services\FacebookPublisherService;

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

    public function handle(FacebookPublisherService $service): void
    {
        $service->publishArticle(
            $this->account,
            $this->news,
            $this->customMessage,
            $this->customMediaUrl
        );
    }
}
