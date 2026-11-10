<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\News;
use App\Models\Category;
use App\Models\AiSetting;
use App\Models\AiGenerationLog;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AIAssistantTest extends TestCase
{
    use RefreshDatabase;

    protected User $editor;
    protected News $news;

    protected function setUp(): void
    {
        parent::setUp();

        $this->editor = User::firstOrCreate(
            ['email' => 'editor_ai@example.com'],
            ['name' => 'Editor AI', 'password' => bcrypt('password'), 'status' => 'active']
        );
        $editorRole = Role::firstOrCreate(['name' => 'editor'], ['label' => 'Editor']);
        $this->editor->roles()->syncWithoutDetaching([$editorRole->id]);

        $category = Category::firstOrCreate(
            ['slug' => 'national'],
            ['name' => 'জাতীয়', 'is_active' => true]
        );

        $this->news = News::create([
            'public_id' => '01JABCDEF1234567890ABCDEF1',
            'slug' => 'test-news-headline-01',
            'title' => 'বাংলাদেশে ডিজিটাল অর্থনীতির বিপ্লব ও সম্ভাবনা',
            'excerpt' => 'ডিজিটাল অর্থনীতির ব্যাপক প্রসার ঘটছে।',
            'body_html' => '<p>বাংলাদেশে গত কয়েক বছরে প্রযুক্তি খাতে অভূতপূর্ব রূপান্তর ঘটেছে।</p>',
            'body_plain' => 'বাংলাদেশে গত কয়েক বছরে প্রযুক্তি খাতে অভূতপূর্ব রূপান্তর ঘটেছে।',
            'type' => 'article',
            'status' => 'draft',
            'primary_category_id' => $category->id,
            'created_by' => $this->editor->id,
            'lock_version' => 1,
        ]);
    }

    public function test_editor_can_generate_headlines_with_ai(): void
    {
        $response = $this->actingAs($this->editor)->postJson(route('admin.ai.generate'), [
            'action' => 'headline',
            'input_text' => $this->news->body_plain,
            'news_id' => $this->news->id,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'text',
            ]);

        $this->assertTrue($response->json('success'));
        $this->assertNotEmpty($response->json('text'));

        // Verify request logged
        $this->assertDatabaseHas('ai_generation_logs', [
            'news_id' => $this->news->id,
            'action' => 'headline',
            'status' => 'success',
        ]);
    }

    public function test_editor_can_generate_summary_and_seo(): void
    {
        $response = $this->actingAs($this->editor)->postJson(route('admin.ai.generate'), [
            'action' => 'summary',
            'input_text' => $this->news->body_plain,
        ]);

        $response->assertStatus(200);
        $this->assertTrue($response->json('success'));

        $this->assertDatabaseHas('ai_generation_logs', [
            'action' => 'summary',
            'status' => 'success',
        ]);
    }

    public function test_admin_can_update_ai_settings(): void
    {
        $adminRole = Role::firstOrCreate(['name' => 'super_admin'], ['label' => 'Super Administrator']);
        $this->editor->roles()->syncWithoutDetaching([$adminRole->id]);

        $response = $this->actingAs($this->editor)->put(route('admin.ai.settings.update'), [
            'provider' => 'mock',
            'model' => 'mock-bengali-v1',
            'daily_request_limit' => 500,
            'is_enabled' => 1,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('ai_settings', [
            'provider' => 'mock',
            'model' => 'mock-bengali-v1',
            'daily_request_limit' => 500,
        ]);
    }
}
