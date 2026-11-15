<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Category;
use App\Models\NewsSource;
use App\Models\NewsImport;
use App\Models\News;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

class NewsAutomationTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::firstOrCreate(
            ['email' => 'admin_automation@example.com'],
            ['name' => 'Admin User', 'password' => bcrypt('password'), 'status' => 'active']
        );
        $adminRole = Role::firstOrCreate(['name' => 'super_admin'], ['label' => 'Super Administrator']);
        $this->admin->roles()->syncWithoutDetaching([$adminRole->id]);

        $this->category = Category::firstOrCreate(
            ['slug' => 'national'],
            ['name' => 'জাতীয়', 'is_active' => true]
        );
    }

    public function test_admin_can_create_news_source(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.automation.sources.store'), [
            'name' => 'টেস্ট নিউজ ফিড',
            'feed_url' => 'https://example.com/test-rss.xml',
            'type' => 'rss',
            'default_category_id' => $this->category->id,
            'language' => 'bn',
            'fetch_interval_minutes' => 30,
            'is_active' => 1,
            'auto_publish' => 0,
        ]);

        $response->assertRedirect(route('admin.automation.sources.index'));
        $this->assertDatabaseHas('news_sources', [
            'name' => 'টেস্ট নিউজ ফিড',
            'feed_url' => 'https://example.com/test-rss.xml',
        ]);
    }

    public function test_rss_import_service_parses_feed_and_deduplicates(): void
    {
        $sampleRss = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0" xmlns:media="http://search.yahoo.com/mrss/">
  <channel>
    <title>Sample Bengali Feed</title>
    <link>https://sample-news.com</link>
    <item>
      <title>জাতীয় নির্বাচনের চূড়ান্ত ফলাফল ঘোষণা</title>
      <link>https://sample-news.com/news/12345?utm_source=rss</link>
      <guid>sample-guid-12345</guid>
      <description>নির্বাচন কমিশন আজ জাতীয় নির্বাচনের পূর্ণাঙ্গ গেজেট প্রকাশ করেছে।</description>
      <pubDate>Tue, 10 Nov 2026 12:00:00 +0600</pubDate>
      <media:content url="https://sample-news.com/images/election.jpg" medium="image" />
      <category>জাতীয়</category>
    </item>
  </channel>
</rss>
XML;

        Http::fake([
            'https://sample-news.com/feed.xml*' => Http::response($sampleRss, 200),
        ]);

        $source = NewsSource::create([
            'name' => 'নমুনা সংবাদ',
            'feed_url' => 'https://sample-news.com/feed.xml',
            'type' => 'rss',
            'default_category_id' => $this->category->id,
            'language' => 'bn',
            'fetch_interval_minutes' => 60,
            'is_active' => true,
            'auto_publish' => false,
        ]);

        $importService = app(\App\Services\NewsImportService::class);
        $log1 = $importService->importFromSource($source);

        $this->assertEquals('success', $log1->status);
        $this->assertEquals(1, $log1->items_imported);
        $this->assertEquals(0, $log1->items_skipped_duplicate);

        // Verify draft news created
        $this->assertDatabaseHas('news', [
            'title' => 'জাতীয় নির্বাচনের চূড়ান্ত ফলাফল ঘোষণা',
            'status' => 'draft',
        ]);

        // Second import of same feed should detect duplicate
        $log2 = $importService->importFromSource($source);
        $this->assertEquals(0, $log2->items_imported);
        $this->assertEquals(1, $log2->items_skipped_duplicate);
    }

    public function test_google_news_auto_paraphrase_editorial_review_approval_poster_and_facebook_publish(): void
    {
        // 1. Connect an active Facebook Page for auto publishing
        $socialAccount = \App\Models\SocialAccount::create([
            'provider' => 'facebook',
            'account_id' => 'mock_page',
            'account_name' => 'দৈনিক প্রথম বার্তা ফেসবুক পেজ',
            'access_token' => 'mock_fb_access_token',
            'auto_publish' => true,
            'is_active' => true,
        ]);

        // 2. Admin creates a Google News RSS Source with AI Paraphrasing & Review Pending
        $sourceResponse = $this->actingAs($this->admin)->post(route('admin.automation.sources.store'), [
            'name' => 'গুগল নিউজ বাংলাদেশ শীর্ষ খবর',
            'feed_url' => 'https://news.google.com/rss?hl=bn&gl=BD&ceid=BD:bn',
            'type' => 'rss',
            'default_category_id' => $this->category->id,
            'language' => 'bn',
            'fetch_interval_minutes' => 30,
            'is_active' => 1,
            'auto_publish' => 0,
            'ai_paraphrase' => 1,
            'editorial_status' => 'review_pending',
        ]);
        $sourceResponse->assertRedirect(route('admin.automation.sources.index'));

        $source = NewsSource::where('feed_url', 'https://news.google.com/rss?hl=bn&gl=BD&ceid=BD:bn')->first();
        $this->assertNotNull($source);
        $this->assertTrue((bool)($source->settings['ai_paraphrase'] ?? false));
        $this->assertEquals('review_pending', $source->settings['editorial_status'] ?? null);

        // 3. Mock Google News RSS feed payload with image and trailing source attribution
        $googleNewsRss = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0" xmlns:media="http://search.yahoo.com/mrss/">
  <channel>
    <title>Google News - বাংলাদেশ</title>
    <link>https://news.google.com</link>
    <item>
      <title>জাতীয় অর্থনৈতিক পরিষদের বৈঠকে নতুন প্রকল্প অনুমোদন - প্রথম আলো</title>
      <link>https://news.google.com/articles/CAIiEO_test_link</link>
      <guid>google-news-guid-9988</guid>
      <description>প্রধানমন্ত্রী শেখ হাসিনার সভাপতিত্বে জাতীয় অর্থনৈতিক পরিষদের সভায় গুরুত্বপূর্ণ উন্নয়ন প্রকল্প অনুমোদিত হয়েছে।</description>
      <pubDate>Thu, 12 Nov 2026 10:00:00 +0600</pubDate>
      <media:content url="https://sample-news.com/images/economic_meeting.jpg" medium="image" />
    </item>
  </channel>
</rss>
XML;

        Http::fake([
            'https://news.google.com/rss*' => Http::response($googleNewsRss, 200),
            'https://sample-news.com/images/*' => Http::response('fake-image-bytes', 200, ['Content-Type' => 'image/jpeg']),
        ]);

        // 4. Run import service
        $importService = app(\App\Services\NewsImportService::class);
        $log = $importService->importFromSource($source);

        $this->assertEquals('success', $log->status);
        $this->assertEquals(1, $log->items_imported);

        // 5. Verify the news is saved in editorial review ("সম্পাদনায় থাকবে" i.e. status = review_pending)
        $news = News::where('status', 'review_pending')->latest()->first();
        $this->assertNotNull($news);
        $this->assertEquals('review_pending', $news->status);
        // Verify AI paraphrasing ran (Mock provider adds 'বিশেষ প্রতিবেদন:' prefix)
        $this->assertStringContainsString('বিশেষ প্রতিবেদন:', $news->title);

        // 6. Editor/Admin approves news via 1-click approve button
        $approveResponse = $this->actingAs($this->admin)->post(route('admin.news.approve', $news->id));
        $approveResponse->assertRedirect();

        // 7. Verify status transitioned to published
        $news->refresh();
        $this->assertEquals('published', $news->status);
        $this->assertNotNull($news->published_at);
        $this->assertEquals($this->admin->id, $news->approved_by);

        // 8. Verify automatic social card / poster was generated
        $poster = \App\Models\GeneratedPoster::where('news_id', $news->id)->first();
        $this->assertNotNull($poster);
        $this->assertEquals('1080x1080', $poster->dimensions);
        $this->assertFileExists(storage_path('app/public/' . $poster->file_path));

        // 9. Verify auto Facebook publishing completed with poster card
        $socialPost = \App\Models\SocialPost::where('news_id', $news->id)->first();
        $this->assertNotNull($socialPost);
        $this->assertEquals('published', $socialPost->status);
        $this->assertEquals($poster->url, $socialPost->media_url);
    }
}
