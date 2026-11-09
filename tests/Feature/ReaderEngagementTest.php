<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Category;
use App\Models\News;
use App\Models\Bookmark;
use App\Models\Comment;
use App\Models\ContactMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

class ReaderEngagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $reader;
    protected User $admin;
    protected Category $category;
    protected News $publishedNews;

    protected function setUp(): void
    {
        parent::setUp();

        $readerRole = Role::firstOrCreate(['name' => 'reader'], ['label' => 'Reader']);
        $adminRole = Role::firstOrCreate(['name' => 'super_admin'], ['label' => 'Super Administrator']);

        $this->reader = User::create([
            'name' => 'সাদিয়া ইসলাম',
            'email' => 'reader_test@htechprovision.com',
            'password' => bcrypt('Secret@123'),
            'status' => 'active',
        ]);
        $this->reader->roles()->attach($readerRole->id);

        $this->admin = User::create([
            'name' => 'এডমিন ব্যবহারকারী',
            'email' => 'admin_test@htechprovision.com',
            'password' => bcrypt('Admin@123456'),
            'status' => 'active',
        ]);
        $this->admin->roles()->attach($adminRole->id);

        $this->category = Category::create([
            'name' => 'জাতীয়',
            'slug' => 'bangladesh',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $this->publishedNews = News::create([
            'public_id' => (string) Str::ulid(),
            'slug' => 'padma-bridge-milestone-update',
            'title' => 'পদ্মা সেতুতে নতুন রেকর্ড',
            'body_html' => '<p>বিস্তারিত প্রতিবেদন এখানে।</p>',
            'body_plain' => 'বিস্তারিত প্রতিবেদন এখানে।',
            'type' => 'article',
            'status' => 'published',
            'published_at' => now()->subHour(),
            'comments_enabled' => true,
            'primary_category_id' => $this->category->id,
            'created_by' => $this->admin->id,
            'lock_version' => 1,
        ]);
    }

    protected function tearDown(): void
    {
        while (ob_get_level() > 1) {
            @ob_end_clean();
        }
        parent::tearDown();
    }

    /** Test 0: Article show page loads */
    public function test_article_show_page_loads(): void
    {
        $response = $this->get("/news/{$this->publishedNews->public_id}/{$this->publishedNews->slug}");
        $response->assertStatus(200);
    }

    /** Test 1: Reader Registration */
    public function test_reader_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'নতুন পাঠক',
            'email' => 'newreader@example.com',
            'password' => 'Pass123456',
            'password_confirmation' => 'Pass123456',
        ]);

        $response->assertRedirect(route('home'));
        $this->assertDatabaseHas('users', ['email' => 'newreader@example.com']);
        $this->assertAuthenticated();
    }

    /** Test 2: Reader Login & Logout */
    public function test_reader_can_login_and_logout(): void
    {
        $response = $this->post('/login', [
            'email' => 'reader_test@htechprovision.com',
            'password' => 'Secret@123',
        ]);

        $response->assertRedirect(route('home'));
        $this->assertAuthenticatedAs($this->reader);

        $logoutResponse = $this->post('/logout');
        $logoutResponse->assertRedirect(route('home'));
        $this->assertGuest();
    }

    /** Test 3: Reader can toggle Bookmark */
    public function test_reader_can_toggle_bookmark(): void
    {
        $response = $this->actingAs($this->reader)
            ->post("/news/{$this->publishedNews->public_id}/bookmark");

        $response->assertStatus(302);
        $this->assertDatabaseHas('bookmarks', [
            'user_id' => $this->reader->id,
            'news_id' => $this->publishedNews->id,
        ]);

        // Verify bookmarks page displays saved article
        $bookmarkPage = $this->actingAs($this->reader)->get('/bookmarks');
        $bookmarkPage->assertStatus(200);
        $bookmarkPage->assertSee('পদ্মা সেতুতে নতুন রেকর্ড');

        // Toggle again to remove
        $this->actingAs($this->reader)
            ->post("/news/{$this->publishedNews->public_id}/bookmark");
        $this->assertDatabaseMissing('bookmarks', [
            'user_id' => $this->reader->id,
            'news_id' => $this->publishedNews->id,
        ]);
    }

    /** Test 4: Comment submission and pending moderation */
    public function test_reader_can_submit_comment_and_held_for_moderation(): void
    {
        $commentBody = 'এটি একটি চমৎকার প্রতিবেদন। শুভকামনা!';

        $response = $this->actingAs($this->reader)
            ->post("/news/{$this->publishedNews->public_id}/comment", [
                'body' => $commentBody,
            ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('comments', [
            'news_id' => $this->publishedNews->id,
            'user_id' => $this->reader->id,
            'body' => $commentBody,
            'status' => 'pending',
        ]);

        // Pending comment must not appear on public article page
        $articlePage = $this->get("/news/{$this->publishedNews->public_id}/{$this->publishedNews->slug}");
        $articlePage->assertStatus(200);
        $articlePage->assertDontSee($commentBody);
    }

    /** Test 5: Admin can approve comment and it becomes visible */
    public function test_admin_can_approve_comment_and_becomes_visible(): void
    {
        $comment = Comment::create([
            'news_id' => $this->publishedNews->id,
            'user_id' => $this->reader->id,
            'body' => 'অনুমোদিত মন্তব্য',
            'status' => 'pending',
        ]);

        // Admin approves
        $approveResponse = $this->actingAs($this->admin)
            ->post("/admin/comments/{$comment->id}/approve");

        $approveResponse->assertStatus(302);
        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'status' => 'approved',
            'moderated_by' => $this->admin->id,
        ]);

        // Live article page now shows the approved comment
        $articlePage = $this->get("/news/{$this->publishedNews->public_id}/{$this->publishedNews->slug}");
        $articlePage->assertStatus(200);
        $articlePage->assertSee('অনুমোদিত মন্তব্য');
    }

    /** Test 6: Visitor Contact Form submission */
    public function test_visitor_can_submit_contact_message(): void
    {
        $response = $this->post('/contact', [
            'name' => 'কামরুল হাসান',
            'email' => 'kamrul@example.com',
            'subject' => 'বিজ্ঞাপন বিষয়ে আলোচনা',
            'message' => 'আমরা আপনাদের পোর্টালে বিজ্ঞাপন প্রচার করতে আগ্রহী।',
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('contact_messages', [
            'email' => 'kamrul@example.com',
            'subject' => 'বিজ্ঞাপন বিষয়ে আলোচনা',
        ]);
    }

    /** Test 7: RSS and Sitemap Feeds */
    public function test_rss_and_sitemap_feeds_return_valid_xml(): void
    {
        $rssResponse = $this->get('/rss');
        $rssResponse->assertStatus(200);
        $rssResponse->assertHeader('Content-Type', 'application/rss+xml; charset=utf-8');
        $rssResponse->assertSee('<rss version="2.0"', false);
        $rssResponse->assertSee('পদ্মা সেতুতে নতুন রেকর্ড');

        $sitemapResponse = $this->get('/sitemap.xml');
        $sitemapResponse->assertStatus(200);
        $sitemapResponse->assertHeader('Content-Type', 'application/xml; charset=utf-8');
        $sitemapResponse->assertSee('<urlset', false);
        $sitemapResponse->assertSee('/news/' . $this->publishedNews->public_id);
    }
}
