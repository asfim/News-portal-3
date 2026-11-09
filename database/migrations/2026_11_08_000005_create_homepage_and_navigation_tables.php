<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('collections', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('collection_news', function (Blueprint $table) {
            $table->foreignId('collection_id')->constrained('collections')->cascadeOnDelete();
            $table->foreignId('news_id')->constrained('news')->cascadeOnDelete();
            $table->unsignedInteger('sort_order')->default(0);
            $table->primary(['collection_id', 'news_id']);
        });

        Schema::create('homepage_sections', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('title');
            $table->string('layout'); // hero, 3col, grid4, video_carousel, beige_tech, dark_video, tabs_sports
            $table->string('source_type'); // latest, category, collection, popular, manual
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->foreignId('collection_id')->nullable()->constrained('collections')->nullOnDelete();
            $table->unsignedInteger('item_limit')->default(6);
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->json('settings')->nullable();
            $table->timestamps();
        });

        Schema::create('homepage_section_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')->constrained('homepage_sections')->cascadeOnDelete();
            $table->foreignId('news_id')->constrained('news')->cascadeOnDelete();
            $table->unsignedInteger('position')->default(0);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamps();

            $table->unique(['section_id', 'news_id']);
        });

        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('key')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')->constrained('menus')->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('menu_items')->nullOnDelete();
            $table->string('label');
            $table->string('target_type')->default('category'); // category, page, collection, custom
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->unsignedBigInteger('page_id')->nullable();
            $table->foreignId('collection_id')->nullable()->constrained('collections')->nullOnDelete();
            $table->string('external_url', 500)->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->boolean('open_new_tab')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('breaking_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('news_id')->nullable()->constrained('news')->nullOnDelete();
            $table->string('label')->nullable();
            $table->string('url', 500)->nullable();
            $table->timestamp('starts_at')->useCurrent();
            $table->timestamp('ends_at')->nullable();
            $table->unsignedInteger('priority')->default(0);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('group', 50)->default('general');
            $table->string('key');
            $table->json('value')->nullable();
            $table->boolean('is_public')->default(true);
            $table->timestamps();

            $table->unique(['group', 'key']);
        });

        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('body_html')->nullable();
            $table->string('status', 30)->default('published');
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pages');
        Schema::dropIfExists('settings');
        Schema::dropIfExists('breaking_items');
        Schema::dropIfExists('menu_items');
        Schema::dropIfExists('menus');
        Schema::dropIfExists('homepage_section_items');
        Schema::dropIfExists('homepage_sections');
        Schema::dropIfExists('collection_news');
        Schema::dropIfExists('collections');
    }
};

