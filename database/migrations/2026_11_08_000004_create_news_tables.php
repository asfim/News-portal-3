<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->string('public_id', 36)->unique();
            $table->string('slug')->unique();
            $table->string('title', 500);
            $table->string('kicker')->nullable();
            $table->string('subtitle', 500)->nullable();
            $table->text('excerpt')->nullable();
            $table->longText('body_html')->nullable();
            $table->longText('body_plain')->nullable();
            $table->string('type', 50)->default('article')->index(); // article, opinion, photo, video, live
            $table->string('status', 50)->default('draft')->index(); // draft, review, scheduled, published, archived
            
            $table->foreignId('primary_category_id')->constrained('categories')->restrictOnDelete();
            $table->foreignId('location_id')->nullable()->constrained('locations')->nullOnDelete();
            $table->foreignId('cover_media_id')->nullable()->constrained('media')->nullOnDelete();
            
            $table->string('cover_alt')->nullable();
            $table->text('cover_caption')->nullable();
            $table->string('cover_credit')->nullable();
            $table->string('source_name')->nullable();
            $table->string('source_url', 500)->nullable();
            
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('published_by')->nullable()->constrained('users')->nullOnDelete();
            
            $table->timestamp('scheduled_at')->nullable()->index();
            $table->timestamp('published_at')->nullable()->index();
            $table->timestamp('content_updated_at')->nullable();
            
            $table->boolean('comments_enabled')->default(true);
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->foreignId('og_media_id')->nullable()->constrained('media')->nullOnDelete();
            
            $table->unsignedInteger('lock_version')->default(1);
            $table->softDeletes();
            $table->timestamps();

            // Composite indexes for query efficiency
            $table->index(['status', 'published_at', 'id']);
            $table->index(['primary_category_id', 'status', 'published_at']);
            $table->index(['location_id', 'status', 'published_at']);
            $table->index(['created_by', 'status']);
        });

        Schema::create('news_category', function (Blueprint $table) {
            $table->foreignId('news_id')->constrained('news')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->primary(['news_id', 'category_id']);
        });

        Schema::create('news_tag', function (Blueprint $table) {
            $table->foreignId('news_id')->constrained('news')->cascadeOnDelete();
            $table->foreignId('tag_id')->constrained('tags')->cascadeOnDelete();
            $table->primary(['news_id', 'tag_id']);
            $table->index(['tag_id', 'news_id']);
        });

        Schema::create('news_author', function (Blueprint $table) {
            $table->foreignId('news_id')->constrained('news')->cascadeOnDelete();
            $table->foreignId('author_profile_id')->constrained('author_profiles')->cascadeOnDelete();
            $table->unsignedInteger('sort_order')->default(0);
            $table->string('role', 50)->nullable(); // writer, photographer, reporter
            $table->primary(['news_id', 'author_profile_id']);
        });

        Schema::create('news_related', function (Blueprint $table) {
            $table->foreignId('news_id')->constrained('news')->cascadeOnDelete();
            $table->foreignId('related_news_id')->constrained('news')->cascadeOnDelete();
            $table->unsignedInteger('sort_order')->default(0);
            $table->primary(['news_id', 'related_news_id']);
        });

        Schema::create('news_revisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('news_id')->constrained('news')->cascadeOnDelete();
            $table->unsignedInteger('version');
            $table->json('snapshot');
            $table->string('status', 50)->default('pending'); // pending, approved, rejected
            $table->foreignId('edited_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('change_note')->nullable();
            $table->timestamps();

            $table->unique(['news_id', 'version']);
        });

        Schema::create('news_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('news_id')->constrained('news')->cascadeOnDelete();
            $table->string('from_status', 50)->nullable();
            $table->string('to_status', 50);
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('reason')->nullable();
            $table->unsignedBigInteger('revision_id')->nullable();
            $table->timestamps();
        });

        Schema::create('news_corrections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('news_id')->constrained('news')->cascadeOnDelete();
            $table->unsignedBigInteger('revision_id')->nullable();
            $table->text('public_note');
            $table->foreignId('corrected_by')->constrained('users')->cascadeOnDelete();
            $table->timestamp('corrected_at');
            $table->timestamps();
        });

        Schema::create('publication_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('news_id')->constrained('news')->cascadeOnDelete();
            $table->unsignedBigInteger('revision_id')->nullable();
            $table->string('event_type', 100); // published, updated, retracted
            $table->json('payload')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->unsignedInteger('attempts')->default(0);
            $table->timestamps();

            $table->index(['processed_at', 'attempts']);
        });

        Schema::create('news_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('news_id')->constrained('news')->cascadeOnDelete();
            $table->foreignId('media_id')->constrained('media')->cascadeOnDelete();
            $table->string('usage', 50)->default('gallery'); // gallery, inline
            $table->unsignedInteger('sort_order')->default(0);
            $table->string('alt_override')->nullable();
            $table->text('caption_override')->nullable();
            $table->string('credit_override')->nullable();
            $table->timestamps();
        });

        Schema::create('news_videos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('news_id')->unique()->constrained('news')->cascadeOnDelete();
            $table->string('provider', 50)->default('youtube'); // youtube, vimeo, custom
            $table->string('provider_video_id')->nullable();
            $table->foreignId('media_id')->nullable()->constrained('media')->nullOnDelete();
            $table->foreignId('thumbnail_media_id')->nullable()->constrained('media')->nullOnDelete();
            $table->unsignedInteger('duration_seconds')->nullable();
            $table->text('transcript')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('news_videos');
        Schema::dropIfExists('news_media');
        Schema::dropIfExists('publication_events');
        Schema::dropIfExists('news_corrections');
        Schema::dropIfExists('news_status_histories');
        Schema::dropIfExists('news_revisions');
        Schema::dropIfExists('news_related');
        Schema::dropIfExists('news_author');
        Schema::dropIfExists('news_tag');
        Schema::dropIfExists('news_category');
        Schema::dropIfExists('news');
    }
};

