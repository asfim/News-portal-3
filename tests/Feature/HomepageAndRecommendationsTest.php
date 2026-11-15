<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\News;
use App\Models\Category;
use App\Models\NewsDailyStat;
use App\Models\HomepageSection;
use App\Models\HomepageSectionItem;
use App\Services\NewsRecommendationService;
use App\Services\HomepageBuilderService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class HomepageAndRecommendationsTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Category $catSports;
    protected Category $catTech;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::firstOrCreate(
            ['email' => 'admin_home@example.com'],
            ['name' => 'Admin User', 'password' => bcrypt('password'), 'status' => 'active']
        );
        $adminRole = Role::firstOrCreate(['name' => 'super_admin'], ['label' => 'Super Administrator']);
        $this->admin->roles()->syncWithoutDetaching([$adminRole->id]);

        $this->catSports = Category::firstOrCreate(['slug' => 'sports'], ['name' => 'খেলা', 'is_active' => true]);
        $this->catTech = Category::firstOrCreate(['slug' => 'technology'], ['name' => 'প্রযুক্তি', 'is_active' => true]);
    }

    public function test_most_read_ranking_ranks_by_sum_of_views_not_row_counts(): void
    {
        // Article A has 3 stat rows with 10 views each = 30 views total
        $articleA = News::create([
            'title' => 'আর্টিকেল এ (৩০ ভিউ)',
            'slug' => 'article-a-30-views',
            'body_html' => '<p>টেস্ট</p>',
            'body_plain' => 'টেস্ট',
            'primary_category_id' => $this->catSports->id,
            'status' => 'published',
            'published_at' => now()->subDays(2),
            'created_by' => $this->admin->id,
            'lock_version' => 1,
        ]);
        NewsDailyStat::create(['news_id' => $articleA->id, 'stat_date' => now()->subDays(2)->toDateString(), 'views' => 10, 'unique_views' => 8]);
        NewsDailyStat::create(['news_id' => $articleA->id, 'stat_date' => now()->subDays(1)->toDateString(), 'views' => 10, 'unique_views' => 7]);
        NewsDailyStat::create(['news_id' => $articleA->id, 'stat_date' => now()->toDateString(), 'views' => 10, 'unique_views' => 9]);

        // Article B has only 1 stat row, but 500 views!
        // Under row counting, Article A would be #1 (3 rows vs 1 row).
        // Under SUM(views), Article B MUST rank #1 (500 views vs 30 views).
        $articleB = News::create([
            'title' => 'আর্টিকেল বি (৫০০ ভিউ)',
            'slug' => 'article-b-500-views',
            'body_html' => '<p>টেস্ট</p>',
            'body_plain' => 'টেস্ট',
            'primary_category_id' => $this->catTech->id,
            'status' => 'published',
            'published_at' => now()->subDay(),
            'created_by' => $this->admin->id,
            'lock_version' => 1,
        ]);
        NewsDailyStat::create(['news_id' => $articleB->id, 'stat_date' => now()->toDateString(), 'views' => 500, 'unique_views' => 450]);

        $mostRead = News::published()->mostRead(7)->get();

        $this->assertEquals($articleB->id, $mostRead->first()->id, 'Article with higher SUM(views) must rank first');
        $this->assertEquals(500, $mostRead->first()->total_views);
        $this->assertEquals(30, $mostRead->skip(1)->first()->total_views);
    }

    public function test_news_recommendation_service_adapts_to_reader_history(): void
    {
        $service = app(NewsRecommendationService::class);
        $sessionId = 'test_session_affinity_123';

        // Create tech news
        $techStory = News::create([
            'title' => 'প্রযুক্তির শীর্ষ খবর',
            'slug' => 'top-tech-story',
            'body_html' => '<p>টেস্ট</p>',
            'body_plain' => 'টেস্ট',
            'primary_category_id' => $this->catTech->id,
            'status' => 'published',
            'published_at' => now(),
            'created_by' => $this->admin->id,
            'lock_version' => 1,
        ]);

        // Record reading activity in Technology category 3 times
        $service->recordReading($techStory, null, $sessionId, 30);
        $service->recordReading($techStory, null, $sessionId, 45);

        // Fetch recommendations
        $recommended = $service->getRecommendations(null, $sessionId, 2);

        $this->assertNotEmpty($recommended);
        $this->assertTrue($recommended->contains('primary_category_id', $this->catTech->id));
    }

    public function test_admin_can_manage_homepage_sections_and_pinned_items(): void
    {
        $section = HomepageSection::create([
            'key' => 'custom_grid_section',
            'title' => 'বিশেষ ক্যাটাগরি ব্লক',
            'layout' => 'grid_4col',
            'source_type' => 'category',
            'category_id' => $this->catTech->id,
            'item_limit' => 4,
            'sort_order' => 15,
            'is_active' => true,
        ]);

        $news = News::create([
            'title' => 'পিন করার সংবাদ',
            'slug' => 'pinned-news-item',
            'body_html' => '<p>টেস্ট</p>',
            'body_plain' => 'টেস্ট',
            'primary_category_id' => $this->catTech->id,
            'status' => 'published',
            'published_at' => now(),
            'created_by' => $this->admin->id,
            'lock_version' => 1,
        ]);

        // Pin item to section
        $pinResponse = $this->actingAs($this->admin)->post(route('admin.homepage-sections.items.store', $section->id), [
            'news_id' => $news->id,
            'position' => 1,
        ]);
        $pinResponse->assertRedirect();

        $this->assertDatabaseHas('homepage_section_items', [
            'section_id' => $section->id,
            'news_id' => $news->id,
            'position' => 1,
        ]);

        // Check Analytics Page
        $analyticsResponse = $this->actingAs($this->admin)->get(route('admin.analytics.index', ['period' => '7']));
        $analyticsResponse->assertStatus(200);
        $analyticsResponse->assertSee('পাঠক অ্যানালিটিক্স');
    }
}

