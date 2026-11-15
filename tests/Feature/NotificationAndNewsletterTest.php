<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\News;
use App\Models\Category;
use App\Models\PushSubscription;
use App\Models\NewsletterSubscriber;
use App\Models\NewsletterCampaign;
use App\Services\PushNotificationService;
use App\Services\NewsletterService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class NotificationAndNewsletterTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected News $news;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::firstOrCreate(
            ['email' => 'admin_notif@example.com'],
            ['name' => 'Admin User', 'password' => bcrypt('password'), 'status' => 'active']
        );
        $adminRole = Role::firstOrCreate(['name' => 'super_admin'], ['label' => 'Super Administrator']);
        $this->admin->roles()->syncWithoutDetaching([$adminRole->id]);

        $category = Category::firstOrCreate(
            ['slug' => 'national'],
            ['name' => 'জাতীয়', 'is_active' => true]
        );

        $this->news = News::create([
            'title' => 'নতুন প্রযুক্তি উদ্ভাবন',
            'slug' => 'new-tech-innovation',
            'body_html' => '<p>টেস্ট বডি</p>',
            'body_plain' => 'টেস্ট বডি',
            'primary_category_id' => $category->id,
            'status' => 'published',
            'published_at' => now(),
            'created_by' => $this->admin->id,
            'lock_version' => 1,
        ]);
    }

    public function test_reader_can_subscribe_and_unsubscribe_push(): void
    {
        $response = $this->postJson(route('api.push.subscribe'), [
            'endpoint' => 'https://updates.push.services.mozilla.com/wpush/v2/test_reader_endpoint',
            'keys' => [
                'p256dh' => 'sample_p256dh_key',
                'auth' => 'sample_auth_token',
            ],
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('push_subscriptions', [
            'endpoint' => 'https://updates.push.services.mozilla.com/wpush/v2/test_reader_endpoint',
            'is_active' => true,
        ]);

        // Unsubscribe
        $unsubResponse = $this->postJson(route('api.push.unsubscribe'), [
            'endpoint' => 'https://updates.push.services.mozilla.com/wpush/v2/test_reader_endpoint',
        ]);
        $unsubResponse->assertStatus(200);

        $this->assertDatabaseHas('push_subscriptions', [
            'endpoint' => 'https://updates.push.services.mozilla.com/wpush/v2/test_reader_endpoint',
            'is_active' => false,
        ]);
    }

    public function test_admin_can_send_push_notification_to_all(): void
    {
        PushSubscription::create([
            'endpoint' => 'https://push.example.com/sub1',
            'endpoint_hash' => hash('sha256', 'https://push.example.com/sub1'),
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.notifications.send'), [
            'title' => 'জরুরি সংবাদ অ্যালার্ট',
            'body' => 'ঢাকায় নতুন নির্দেশনা জারি করা হয়েছে।',
            'url' => 'http://localhost/news/123/slug',
            'news_id' => $this->news->id,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('push_notification_logs', [
            'title' => 'জরুরি সংবাদ অ্যালার্ট',
            'successful_sent' => 1,
        ]);
    }

    public function test_newsletter_double_opt_in_and_delivery(): void
    {
        $service = app(NewsletterService::class);
        $subscriber = $service->subscribe('reader@example.com', 'মোরশেদ আলম');

        $this->assertNotNull($subscriber);
        $this->assertDatabaseHas('newsletter_subscribers', [
            'email' => 'reader@example.com',
            'is_active' => true,
        ]);

        // Test campaign sending
        $campaign = NewsletterCampaign::create([
            'title' => 'সাপ্তাহিক বুলেটিন',
            'subject' => 'সপ্তাহের আলোচিত খবর',
            'content_html' => '<p>খবরের তালিকা</p>',
            'status' => 'draft',
            'created_by' => $this->admin->id,
        ]);

        $sentCount = $service->sendCampaign($campaign);
        $this->assertGreaterThanOrEqual(1, $sentCount);

        // Test Unsubscribe
        $unsub = $service->unsubscribe($subscriber->token);
        $this->assertTrue($unsub);
        $this->assertDatabaseHas('newsletter_subscribers', [
            'email' => 'reader@example.com',
            'is_active' => false,
        ]);
    }
}

