<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\News;
use App\Services\PosterGeneratorService;
use Illuminate\Support\Facades\Log;

class GenerateNewsPosterJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 2;
    public int $backoff = 30;

    public function __construct(
        public News $news,
        public string $dimensions = '1080x1080',
        public ?string $customHeadline = null
    ) {}

    public function handle(PosterGeneratorService $service): void
    {
        try {
            $service->generate(
                $this->news,
                $this->dimensions,
                $this->customHeadline
            );
        } catch (\Throwable $e) {
            Log::error("GenerateNewsPosterJob failed for News #{$this->news->id}: " . $e->getMessage());
        }
    }
}

