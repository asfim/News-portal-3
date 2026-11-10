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
}
