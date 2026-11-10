<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('news_sources', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('feed_url', 500);
            $table->string('type', 30)->default('rss'); // rss, atom, json_api
            $table->foreignId('default_category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('language', 10)->default('bn');
            $table->unsignedInteger('fetch_interval_minutes')->default(60);
            $table->timestamp('last_fetched_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('auto_publish')->default(false); // Default false for editorial safety: draft review
            $table->string('author_attribution')->nullable();
            $table->json('headers')->nullable();
            $table->json('settings')->nullable();
            $table->unsignedInteger('consecutive_failures')->default(0);
            $table->text('last_error')->nullable();
            $table->timestamps();
        });

        Schema::create('news_imports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('source_id')->constrained('news_sources')->cascadeOnDelete();
            $table->foreignId('news_id')->nullable()->constrained('news')->nullOnDelete();
            $table->string('guid', 255)->index();
            $table->string('source_url', 500);
            $table->string('url_hash', 64)->index();
            $table->string('content_fingerprint', 64)->index();
            $table->string('title', 500);
            $table->text('summary')->nullable();
            $table->string('image_url', 500)->nullable();
            $table->timestamp('published_at')->nullable();
            $table->string('status', 30)->default('draft_created')->index(); // draft_created, duplicate_skipped, failed, published
            $table->foreignId('duplicate_of_news_id')->nullable()->constrained('news')->nullOnDelete();
            $table->text('error_message')->nullable();
            $table->json('raw_payload')->nullable();
            $table->timestamps();
        });

        Schema::create('news_import_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('source_id')->constrained('news_sources')->cascadeOnDelete();
            $table->string('status', 30)->default('success'); // success, warning, failed
            $table->unsignedInteger('items_found')->default(0);
            $table->unsignedInteger('items_imported')->default(0);
            $table->unsignedInteger('items_skipped_duplicate')->default(0);
            $table->unsignedInteger('items_failed')->default(0);
            $table->decimal('execution_time_seconds', 8, 2)->default(0);
            $table->text('message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('news_import_logs');
        Schema::dropIfExists('news_imports');
        Schema::dropIfExists('news_sources');
    }
};
