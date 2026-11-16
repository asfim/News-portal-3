<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Category;
use App\Models\AuthorProfile;
use App\Models\Tag;
use App\Models\News;
use App\Models\NewsRevision;
use App\Models\PublicationEvent;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class EditorialPublishingTest extends TestCase
{
    use \Illuminate\Foundation\Testing\RefreshDatabase;

    protected User $superAdmin;
    protected User $reporter;
    protected User $reader;
    protected Category $category;
    protected AuthorProfile $author;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superAdmin = User::firstOrCreate(
            ['email' => 'admin@htechprovision.com'],
            ['name' => 'Admin', 'password' => bcrypt('Admin@123456'), 'status' => 'active']
        );
        $superRole = Role::firstOrCreate(['name' => 'super_admin'], ['label' => 'Super Administrator']);
        $this->superAdmin->roles()->syncWithoutDetaching([$superRole->id]);

        $this->reporter = User::firstOrCreate(
            ['email' => 'reporter@htechprovision.com'],
            ['name' => 'Reporter One', 'password' => bcrypt('Reporter@123'), 'status' => 'active']
        );
        $reporterRole = Role::firstOrCreate(['name' => 'reporter'], ['label' => 'Reporter']);
        $this->reporter->roles()->syncWithoutDetaching([$reporterRole->id]);

        $this->reader = User::firstOrCreate(
            ['email' => 'reader@example.com'],
            ['name' => 'Regular Reader', 'password' => bcrypt('Reader@123'), 'status' => 'active']
        );
        $readerRole = Role::firstOrCreate(['name' => 'reader'], ['label' => 'Reader']);
        $this->reader->roles()->syncWithoutDetaching([$readerRole->id]);

        $this->category = Category::firstOrCreate(
            ['slug' => 'test-category'],
            ['name' => 'টেস্ট ক্যাটাগরি', 'sort_order' => 1, 'is_active' => true]
        );

        $this->author = AuthorProfile::firstOrCreate(
            ['slug' => 'test-author'],
            ['name' => 'টেস্ট লেখক', 'is_active' => true]
        );
    }

    /** Test 1: Reader cannot access admin dashboard (403 forbidden) */
    public function test_reader_cannot_access_admin_dashboard(): void
    {
        $response = $this->actingAs($this->reader)->get('/admin/dashboard');
        $response->assertStatus(403);
    }

    /** Test 2: Guest is redirected to admin login */
    public function test_guest_is_redirected_to_admin_login(): void
    {
        $response = $this->get('/admin/dashboard');
        $response->assertRedirect('/admin/login');
    }

    /** Test 3: Super Admin can access admin dashboard */
    public function test_super_admin_can_access_admin_dashboard(): void
    {
        $response = $this->actingAs($this->superAdmin)->get('/admin/dashboard');
        $response->assertStatus(200);
        $response->assertSee('ড্যাশবোর্ড');

        $this->actingAs($this->superAdmin)->get('/admin/breaking-news')->assertStatus(200);
        $this->actingAs($this->superAdmin)->get('/admin/advertisements')->assertStatus(200);
        $this->actingAs($this->superAdmin)->get('/admin/homepage-sections')->assertStatus(200);
    }

    /** Test 4: Draft / Scheduled / Archived news are hidden from public queries */
    public function test_unpublished_news_hidden_from_public_surfaces(): void
    {
        $draftNews = News::create([
            'public_id' => (string) Str::ulid(),
            'slug' => 'secret-draft-news-' . Str::random(5),
            'title' => 'গোপন খসড়া সংবাদ',
            'body_html' => '<p>খসড়া বডি</p>',
            'body_plain' => 'খসড়া বডি',
            'status' => 'draft',
            'type' => 'article',
            'primary_category_id' => $this->category->id,
            'created_by' => $this->superAdmin->id,
            'lock_version' => 1,
        ]);

        // Attempt to access public show page
        $response = $this->get("/news/{$draftNews->public_id}/{$draftNews->slug}");
        $response->assertStatus(404);

        // Not present on home
        $homeResponse = $this->get('/');
        $homeResponse->assertDontSee($draftNews->title);
    }

    /** Test 5: Reporter submission creates review_pending status, not published */
    public function test_reporter_submission_cannot_directly_publish(): void
    {
        $response = $this->actingAs($this->reporter)->post('/admin/news', [
            'title' => 'রিপোর্টারের পাঠানো সংবাদ ' . Str::random(5),
            'body_html' => '<p>রিপোর্ট বিস্তারিত</p>',
            'type' => 'article',
            'primary_category_id' => $this->category->id,
            'action' => 'publish', // Even if reporter selects publish, policy enforces review_pending
        ]);

        $response->assertRedirect(route('admin.news.index'));

        $news = News::where('created_by', $this->reporter->id)->latest('id')->first();
        $this->assertNotNull($news);
        $this->assertEquals('review_pending', $news->status);
    }

    /** Test 6: Safe canonical 301 permanent redirect when slug does not match */
    public function test_canonical_301_redirect_on_slug_mismatch(): void
    {
        $news = News::create([
            'public_id' => (string) Str::ulid(),
            'slug' => 'published-story-' . Str::random(5),
            'title' => 'প্রকাশিত সংবাদ ' . Str::random(5),
            'body_html' => '<p>বিস্তারিত খবর</p>',
            'body_plain' => 'বিস্তারিত খবর',
            'status' => 'published',
            'published_at' => now(),
            'type' => 'article',
            'primary_category_id' => $this->category->id,
            'created_by' => $this->superAdmin->id,
            'lock_version' => 1,
        ]);

        $response = $this->get("/news/{$news->public_id}/old-obsolete-slug");
        $response->assertStatus(301);
        $response->assertRedirect(url("/news/{$news->public_id}/{$news->slug}"));
    }

    /** Test 7: Scheduled publication command is idempotent and safe */
    public function test_scheduled_publication_command_publishes_due_articles(): void
    {
        $scheduledNews = News::create([
            'public_id' => (string) Str::ulid(),
            'slug' => 'scheduled-story-' . Str::random(5),
            'title' => 'শিডিউলড স্টোরি ' . Str::random(5),
            'body_html' => '<p>শিডিউলড বডি</p>',
            'body_plain' => 'শিডিউলড বডি',
            'status' => 'scheduled',
            'scheduled_at' => now()->subMinute(), // due in the past
            'type' => 'article',
            'primary_category_id' => $this->category->id,
            'created_by' => $this->superAdmin->id,
            'lock_version' => 1,
        ]);

        $this->artisan('news:publish-scheduled')
            ->assertExitCode(0);

        $scheduledNews->refresh();
        $this->assertEquals('published', $scheduledNews->status);
        $this->assertNotNull($scheduledNews->published_at);

        // Second run must be idempotent (nothing to publish)
        $this->artisan('news:publish-scheduled')
            ->expectsOutput('No due scheduled news found.')
            ->assertExitCode(0);
    }

    /** Test 8: Admin news index supports Load More AJAX without full page reload */
    public function test_admin_news_index_supports_load_more_ajax(): void
    {
        $response = $this->actingAs($this->superAdmin)->get('/admin/news');
        $response->assertStatus(200);
        $response->assertSee('newsTableBody');

        $ajaxResponse = $this->actingAs($this->superAdmin)
            ->getJson('/admin/news?page=1&load_more=1');

        $ajaxResponse->assertStatus(200);
        $ajaxResponse->assertJsonStructure([
            'html',
            'has_more',
            'next_page',
            'current_page',
            'last_page',
            'total',
        ]);
    }

    /** Test 9: Admin can upload video file and it binds to homepage video section */
    public function test_admin_can_upload_video_file_and_it_binds_to_homepage_video_section(): void
    {
        Storage::fake('public');

        $videoFile = UploadedFile::fake()->create('special_report.mp4', 2048, 'video/mp4');

        $response = $this->actingAs($this->superAdmin)->post('/admin/news', [
            'title' => 'টেস্ট ভিডিও প্রতিবেদন আপলোড',
            'slug' => 'test-video-upload-story',
            'excerpt' => 'টেস্ট ভিডিও সারাংশ',
            'body_html' => '<p>এখানে বিস্তারিত ভিডিও সংবাদের বিবরণ থাকবে।</p>',
            'type' => 'article', // Backend will automatically upgrade to 'video'
            'primary_category_id' => $this->category->id,
            'action' => 'publish',
            'video_file' => $videoFile,
        ]);

        $response->assertRedirect('/admin/news');

        $news = News::where('slug', 'test-video-upload-story')->first();
        $this->assertNotNull($news);
        $this->assertEquals('video', $news->type);
        $this->assertEquals('published', $news->status);

        $this->assertDatabaseHas('news_videos', [
            'news_id' => $news->id,
            'provider' => 'local',
        ]);

        $this->assertNotNull($news->video);
        $this->assertNotNull($news->video->media);
        $this->assertStringContainsString('special_report.mp4', $news->video->media->original_name);
        $this->assertEquals('video/mp4', $news->video->media->mime_type);

        // Verify homepage video section displays the uploaded video story
        $homeResponse = $this->get('/');
        $homeResponse->assertStatus(200);
        $homeResponse->assertSee('টেস্ট ভিডিও প্রতিবেদন আপলোড');

        // Verify show page renders HTML5 video player
        $showResponse = $this->get("/news/{$news->public_id}/{$news->slug}");
        $showResponse->assertStatus(200);
        $showResponse->assertSee('<video controls', false);
        $showResponse->assertSee('storage/uploads/videos/video_');
        $showResponse->assertSee('type="video/mp4"', false);
    }

    /** Test 10: Admin can provide YouTube URL and it automatically extracts ID and binds to homepage */
    public function test_admin_can_provide_youtube_url_and_it_binds_to_homepage(): void
    {
        $response = $this->actingAs($this->superAdmin)->post('/admin/news', [
            'title' => 'ইউটিউব ভিডিও বিশ্লেষণ সংবাদ',
            'slug' => 'youtube-analysis-video-story',
            'excerpt' => 'ইউটিউব ভিডিও সারসংক্ষেপ',
            'body_html' => '<p>ইউটিউব ভিডিও এর বর্ণনা।</p>',
            'type' => 'article',
            'primary_category_id' => $this->category->id,
            'action' => 'publish',
            'video_provider' => 'youtube',
            'video_id' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        ]);

        $response->assertRedirect('/admin/news');

        $news = News::where('slug', 'youtube-analysis-video-story')->first();
        $this->assertNotNull($news);
        $this->assertEquals('video', $news->type);

        $this->assertDatabaseHas('news_videos', [
            'news_id' => $news->id,
            'provider' => 'youtube',
            'provider_video_id' => 'dQw4w9WgXcQ',
        ]);

        // Verify show page embeds YouTube player with extracted ID
        $showResponse = $this->get("/news/{$news->public_id}/{$news->slug}");
        $showResponse->assertStatus(200);
        $showResponse->assertSee('https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ', false);
    }
}
