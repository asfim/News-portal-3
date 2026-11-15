<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\News;
use App\Services\PushNotificationService;
use Illuminate\Support\Facades\Log;

class SendNewsPushNotificationJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public int $backoff = 30;

    public function __construct(
        public News $news,
        public ?string $customTitle = null,
        public ?string $customBody = null
    ) {}

    public function handle(PushNotificationService $service): void
    {
        try {
            $title = $this->customTitle ?: ($this->news->kicker ? "[{$this->news->kicker}] {$this->news->title}" : $this->news->title);
            $body = $this->customBody ?: ($this->news->excerpt ?: $this->news->title);
            $url = $this->news->url;
            $imageUrl = $this->news->cover_image_url;

            $service->sendToAll(
                title: $title,
                body: $body,
                url: $url,
                imageUrl: $imageUrl,
                newsId: $this->news->id
            );
        } catch (\Throwable $e) {
            Log::error("SendNewsPushNotificationJob failed for News #{$this->news->id}: " . $e->getMessage());
        }
    }
}

