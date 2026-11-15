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
        $responsePosters->assertSee('postersGrid');

        $ajaxResponse = $this->actingAs($this->admin)
            ->getJson(route('admin.posters.index', ['page' => 1, 'load_more' => 1]));

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

    public function test_admin_can_view_poster_create_page(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.posters.create'));
        $response->assertStatus(200);
        $response->assertSee('সোশ্যাল পোস্টার জেনারেটর');
        $response->assertSee($this->news->title);
    }

    public function test_admin_can_generate_poster_via_store(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.posters.store'), [
            'news_id' => $this->news->id,
            'dimensions' => '1080x1080',
            'headline_text' => 'কাস্টম দীর্ঘ শিরোনাম যা ২০০ অক্ষরের বেশি হতে পারে এবং সুন্দরভাবে কাজ করবে। ' . str_repeat('খবর ', 30),
        ]);

        $response->assertRedirect(route('admin.posters.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('generated_posters', [
            'news_id' => $this->news->id,
            'dimensions' => '1080x1080',
        ]);
    }

    public function test_poster_store_validation_fails_for_missing_news(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.posters.store'), [
            'news_id' => 999999,
            'dimensions' => '1080x1080',
        ]);

        $response->assertSessionHasErrors(['news_id']);
    }

    public function test_admin_can_view_social_account_edit_page(): void
    {
        $account = SocialAccount::create([
            'provider' => 'facebook',
            'account_id' => 'page_test_123',
            'account_name' => 'Original Page Name',
            'access_token' => 'original_secret_token',
            'auto_publish' => false,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.social.accounts.edit', $account->id));
        $response->assertStatus(200);
        $response->assertSee('ফেসবুক পেজ অ্যাকাউন্ট সম্পাদনা');
        $response->assertSee('Original Page Name');
        $response->assertSee('page_test_123');
    }

    public function test_admin_can_update_social_account(): void
    {
        $account = SocialAccount::create([
            'provider' => 'facebook',
            'account_id' => 'page_test_123',
            'account_name' => 'Original Page Name',
            'access_token' => 'original_secret_token',
            'auto_publish' => false,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->put(route('admin.social.accounts.update', $account->id), [
            'account_name' => 'Updated Page Name',
            'account_id' => 'page_test_updated_456',
            'auto_publish' => '1',
            'is_active' => '1',
            // access_token left empty to keep existing token
        ]);

        $response->assertRedirect(route('admin.social.accounts.index'));
        $response->assertSessionHas('success');

        $account->refresh();
        $this->assertEquals('Updated Page Name', $account->account_name);
        $this->assertEquals('page_test_updated_456', $account->account_id);
        $this->assertTrue($account->auto_publish);
        $this->assertEquals('original_secret_token', $account->access_token);
    }
}


