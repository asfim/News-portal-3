<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Category;
use App\Models\News;
use App\Models\SocialAccount;
use App\Models\SocialPost;
use App\Models\GeneratedPoster;
use App\Services\PosterGeneratorService;
use App\Services\FacebookPublisherService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class PosterAndSocialPublishingTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected News $news;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::firstOrCreate(
            ['email' => 'admin_social@example.com'],
            ['name' => 'Admin Social', 'password' => bcrypt('password'), 'status' => 'active']
        );
        $adminRole = Role::firstOrCreate(['name' => 'super_admin'], ['label' => 'Super Administrator']);
        $this->admin->roles()->syncWithoutDetaching([$adminRole->id]);

        $category = Category::firstOrCreate(
            ['slug' => 'tech'],
            ['name' => 'প্রযুক্তি', 'is_active' => true]
        );

        $this->news = News::create([
            'title' => 'কৃত্রিম বুদ্ধিমত্তার নতুন দিগন্ত ও ভবিষ্যৎ সম্ভাবনা',
            'slug' => 'ai-future-possibilities',
            'body_html' => '<p>বিস্তারিত প্রতিবেদন...</p>',
            'body_plain' => 'বিস্তারিত প্রতিবেদন...',
            'excerpt' => 'সংক্ষিপ্ত সারসংক্ষেপ...',
            'primary_category_id' => $category->id,
            'status' => 'published',
            'published_at' => now(),
            'created_by' => $this->admin->id,
            'lock_version' => 1,
        ]);
    }

    public function test_poster_generator_service_creates_poster_record(): void
    {
        $service = app(PosterGeneratorService::class);
        $poster = $service->generate($this->news, '1080x1080', 'কাস্টম শিরোনাম');

        $this->assertInstanceOf(GeneratedPoster::class, $poster);
        $this->assertEquals($this->news->id, $poster->news_id);
        $this->assertEquals('1080x1080', $poster->dimensions);
        $this->assertNotEmpty($poster->file_path);
        $this->assertDatabaseHas('generated_posters', [
            'id' => $poster->id,
            'news_id' => $this->news->id,
        ]);
    }

    public function test_facebook_publisher_publishes_with_mock_and_idempotency(): void
    {
        $account = SocialAccount::create([
            'platform' => 'facebook',
            'account_name' => 'এইচ টেক অফিসিয়াল পেজ',
            'account_id' => 'mock_page',
            'access_token' => 'mock_token',
            'is_active' => true,
            'auto_publish' => true,
        ]);

        $service = app(FacebookPublisherService::class);
        $post = $service->publishArticle($account, $this->news);

        $this->assertInstanceOf(SocialPost::class, $post);
        $this->assertEquals('published', $post->status);
        $this->assertNotEmpty($post->external_post_id);
        $this->assertNotEmpty($post->idempotency_key);

        // Idempotency check: calling again returns the exact same published post without duplicate
        $postSecond = $service->publishArticle($account, $this->news);
        $this->assertEquals($post->id, $postSecond->id);
        $this->assertEquals(1, SocialPost::where('news_id', $this->news->id)->count());
    }

    public function test_admin_can_view_social_accounts_and_posters(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.social.accounts.index'));
        $response->assertStatus(200);

        $responsePosters = $this->actingAs($this->admin)->get(route('admin.posters.index'));
        $responsePosters->assertStatus(200);
    }
}

