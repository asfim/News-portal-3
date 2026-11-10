<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\News;
use App\Models\NewsStatusHistory;
use App\Models\PublicationEvent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PublishScheduledNews extends Command
{
    protected $signature = 'news:publish-scheduled';
    protected $description = 'Publish due scheduled news articles idempotently';

    public function handle(): int
    {
        $nowUtc = now();
        $this->info("Checking for scheduled news due before {$nowUtc->toDateTimeString()} UTC...");

        $dueNews = News::where('status', 'scheduled')
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '<=', $nowUtc)
            ->get();

        if ($dueNews->isEmpty()) {
            $this->info("No due scheduled news found.");
            return Command::SUCCESS;
        }

        $count = 0;
        foreach ($dueNews as $news) {
            DB::transaction(function () use ($news, &$count) {
                // Lock row to prevent race conditions across parallel runs
                $lockedNews = News::where('id', $news->id)
                    ->where('status', 'scheduled')
                    ->lockForUpdate()
                    ->first();

                if (!$lockedNews) {
                    return;
                }

                $lockedNews->update([
                    'status' => 'published',
                    'published_at' => $lockedNews->scheduled_at ?? now(),
                    'content_updated_at' => now(),
                    'lock_version' => $lockedNews->lock_version + 1,
                ]);

                // Append status history
                NewsStatusHistory::create([
                    'news_id' => $lockedNews->id,
                    'from_status' => 'scheduled',
                    'to_status' => 'published',
                    'actor_id' => $lockedNews->published_by ?? $lockedNews->created_by,
                    'reason' => 'Scheduled time reached (automated publisher)',
                ]);

                // Durable publication outbox event
                PublicationEvent::firstOrCreate(
                    [
                        'news_id' => $lockedNews->id,
                        'event_type' => 'published',
                    ],
                    [
                        'payload' => [
                            'public_id' => $lockedNews->public_id,
                            'title' => $lockedNews->title,
                            'published_at' => $lockedNews->published_at,
                        ],
                        'processed_at' => now(),
                        'attempts' => 1,
                    ]
                );

                $count++;
            });
        }

        if ($count > 0) {
            app(\App\Services\HomepageBuilderService::class)->invalidateCache();
        }

        $this->info("Successfully published {$count} scheduled news articles.");
        Log::info("Published {$count} scheduled news articles.");

        return Command::SUCCESS;
    }
}

