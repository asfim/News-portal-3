<?php

namespace App\Services;

use App\Models\News;
use App\Models\SocialAccount;
use App\Jobs\PublishNewsToFacebookJob;
use App\Jobs\GenerateNewsPosterJob;
use App\Jobs\SendNewsPushNotificationJob;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PublicationWorkflowService
{
    /**
     * Dispatch all dependent workflows after a news publication transaction commits.
     *
     * @param News $news
     * @param bool $isBreaking
     */
    public function onNewsPublished(News $news, bool $isBreaking = false): void
    {
        DB::afterCommit(function () use ($news, $isBreaking) {
            try {
                // 1. Invalidate homepage cache immediately
                app(HomepageBuilderService::class)->invalidateCache();

                // 2. Generate Social Media Poster Card with news image & headline
                $poster = null;
                try {
                    $poster = app(PosterGeneratorService::class)->generate($news, '1080x1080');
                } catch (\Throwable $e) {
                    Log::warning("Immediate poster generation failed for News #{$news->id}: " . $e->getMessage());
                }

                // 3. Auto-publish to connected active Facebook Pages with auto_publish enabled
                $autoSocialAccounts = SocialAccount::where('is_active', true)
                    ->where('auto_publish', true)
                    ->get();

                foreach ($autoSocialAccounts as $account) {
                    $job = new PublishNewsToFacebookJob(
                        $account,
                        $news,
                        customMediaUrl: $poster?->url
                    );

                    // Execute synchronously in local environment or sync queue so auto-publish never gets stuck in DB table
                    if (config('queue.default') === 'sync' || app()->environment('local')) {
                        dispatch_sync($job);
                    } else {
                        dispatch($job);
                    }
                }

                // 4. Send Web Push Notification if breaking news or requested
                if ($isBreaking || $news->kicker === 'ব্রেকিং নিউজ') {
                    dispatch(new SendNewsPushNotificationJob(
                        $news,
                        customTitle: "ব্রেকিং নিউজ: " . $news->title,
                        customBody: $news->excerpt ?: $news->title
                    ));
                }
            } catch (\Throwable $e) {
                Log::warning("Error in PublicationWorkflowService for News #{$news->id}: " . $e->getMessage());
            }
        });
    }
}

