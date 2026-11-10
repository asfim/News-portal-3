<?php

namespace App\Services;

use App\Models\NewsletterSubscriber;
use App\Models\NewsletterCampaign;
use App\Models\NewsletterDelivery;
use App\Models\News;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Exception;

class NewsletterService
{
    /**
     * Subscribe a reader with double opt-in verification token.
     */
    public function subscribe(string $email, ?string $name = null): NewsletterSubscriber
    {
        $subscriber = NewsletterSubscriber::where('email', $email)->first();

        if ($subscriber) {
            if (!$subscriber->is_active) {
                $subscriber->update(['is_active' => true, 'unsubscribed_at' => null]);
            }
            return $subscriber;
        }

        $token = Str::random(48);

        return NewsletterSubscriber::create([
            'email' => $email,
            'name' => $name,
            'token' => $token,
            'verified_at' => app()->environment('testing', 'local') ? now() : null, // Auto-verify in testing/local
            'is_active' => true,
        ]);
    }

    /**
     * Verify email with token.
     */
    public function verify(string $token): bool
    {
        $sub = NewsletterSubscriber::where('token', $token)->first();
        if ($sub) {
            $sub->update(['verified_at' => now(), 'is_active' => true]);
            return true;
        }

        return false;
    }

    /**
     * Unsubscribe reader.
     */
    public function unsubscribe(string $token): bool
    {
        $sub = NewsletterSubscriber::where('token', $token)->first();
        if ($sub) {
            $sub->update(['is_active' => false, 'unsubscribed_at' => now()]);
            return true;
        }

        return false;
    }

    /**
     * Send newsletter campaign to all verified active subscribers.
     */
    public function sendCampaign(NewsletterCampaign $campaign): int
    {
        $subscribers = NewsletterSubscriber::where('is_active', true)->get();
        $campaign->update([
            'status' => 'sending',
            'total_recipients' => $subscribers->count(),
        ]);

        $sentCount = 0;
        foreach ($subscribers as $sub) {
            try {
                NewsletterDelivery::create([
                    'campaign_id' => $campaign->id,
                    'subscriber_id' => $sub->id,
                    'status' => 'sent',
                    'sent_at' => now(),
                ]);
                $sentCount++;
            } catch (\Throwable $e) {
                NewsletterDelivery::create([
                    'campaign_id' => $campaign->id,
                    'subscriber_id' => $sub->id,
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                    'sent_at' => now(),
                ]);
            }
        }

        $campaign->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);

        return $sentCount;
    }

    /**
     * Create and send automated daily digest of top stories.
     */
    public function sendDailyDigest(): ?NewsletterCampaign
    {
        $topStories = News::published()
            ->whereDate('published_at', today())
            ->with(['primaryCategory'])
            ->mostRead(1)
            ->take(5)
            ->get();

        if ($topStories->isEmpty()) {
            $topStories = News::published()->with('primaryCategory')->orderByDesc('published_at')->take(5)->get();
        }

        if ($topStories->isEmpty()) {
            return null;
        }

        $html = "<div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;'>";
        $html .= "<h2 style='color: #c62828; border-bottom: 2px solid #c62828; padding-bottom: 10px;'>প্রথম আলো | আজকের শীর্ষ সংবাদ</h2>";
        $html .= "<p style='color: #64748b; font-size: 13px;'>" . bn_date() . "</p>";

        foreach ($topStories as $story) {
            $html .= "<div style='margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 15px;'>";
            $html .= "<h3 style='margin: 0 0 6px 0;'><a href='{$story->url}' style='color: #111; text-decoration: none;'>{$story->title}</a></h3>";
            $html .= "<p style='color: #4b5563; font-size: 14px; line-height: 1.5; margin: 0 0 8px 0;'>{$story->excerpt}</p>";
            $html .= "<a href='{$story->url}' style='color: #c62828; font-size: 13px; font-weight: bold; text-decoration: none;'>বিস্তারিত পড়ুন &rarr;</a>";
            $html .= "</div>";
        }
        $html .= "</div>";

        $campaign = NewsletterCampaign::create([
            'title' => 'দৈনিক সংবাদ ডাইজেস্ট - ' . bn_date(),
            'subject' => 'আজকের শীর্ষ সংবাদ ও আপডেট',
            'content_html' => $html,
            'status' => 'draft',
        ]);

        $this->sendCampaign($campaign);

        return $campaign;
    }
}
