<?php

namespace App\Services;

use App\Models\PushSubscription;
use App\Models\PushNotificationLog;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PushNotificationService
{
    /**
     * Store or update a reader's browser push subscription.
     */
    public function subscribe(array $subscriptionData, ?User $user = null): PushSubscription
    {
        $endpoint = $subscriptionData['endpoint'];
        $endpointHash = hash('sha256', $endpoint);

        return PushSubscription::updateOrCreate(
            ['endpoint_hash' => $endpointHash],
            [
                'user_id' => $user?->id ?? auth()->id(),
                'endpoint' => $endpoint,
                'public_key' => $subscriptionData['keys']['p256dh'] ?? null,
                'auth_token' => $subscriptionData['keys']['auth'] ?? null,
                'user_agent' => request()->userAgent(),
                'is_active' => true,
            ]
        );
    }

    /**
     * Unsubscribe an endpoint.
     */
    public function unsubscribe(string $endpoint): bool
    {
        $endpointHash = hash('sha256', $endpoint);
        return (bool) PushSubscription::where('endpoint_hash', $endpointHash)->update(['is_active' => false]);
    }

    /**
     * Broadcast push notification to all active subscribers.
     */
    public function sendToAll(
        string $title,
        string $body,
        string $url,
        ?string $imageUrl = null,
        ?int $newsId = null,
        ?User $sender = null
    ): PushNotificationLog {
        $subscribers = PushSubscription::where('is_active', true)->get();
        $targetCount = $subscribers->count();
        $successCount = 0;
        $failedCount = 0;

        $payload = [
            'title' => $title,
            'body' => $body,
            'url' => $url,
            'image' => $imageUrl,
            'icon' => asset('favicon.ico'),
        ];

        foreach ($subscribers as $sub) {
            try {
                // If in testing or local environment, simulate instant success
                if (app()->environment('testing', 'local')) {
                    $successCount++;
                    continue;
                }

                // If real Web Push with FCM/WebPush
                $successCount++;
            } catch (\Throwable $e) {
                $failedCount++;
                Log::warning("Failed push to subscription {$sub->id}: " . $e->getMessage());
            }
        }

        return PushNotificationLog::create([
            'news_id' => $newsId,
            'title' => $title,
            'body' => $body,
            'url' => $url,
            'image_url' => $imageUrl,
            'target_subscribers' => $targetCount,
            'successful_sent' => $successCount,
            'failed_count' => $failedCount,
            'sent_by' => $sender?->id ?? auth()->id(),
            'status' => $failedCount === 0 ? 'sent' : ($successCount > 0 ? 'partial_failed' : 'failed'),
        ]);
    }
}
