<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ad_slots', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique(); // header_top, right_rectangle, middle_leaderboard, in_article, footer_banner
            $table->string('name');
            $table->unsignedInteger('desktop_width')->nullable();
            $table->unsignedInteger('desktop_height')->nullable();
            $table->unsignedInteger('mobile_width')->nullable();
            $table->unsignedInteger('mobile_height')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('advertisements', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->foreignId('media_id')->nullable()->constrained('media')->nullOnDelete();
            $table->string('destination_url', 500)->nullable();
            $table->string('provider_type', 50)->default('image'); // image, code, google
            $table->string('provider_slot_id')->nullable();
            $table->timestamp('starts_at')->useCurrent();
            $table->timestamp('ends_at')->nullable();
            $table->unsignedInteger('priority')->default(0);
            $table->string('status', 30)->default('active')->index();
            $table->string('sponsor_name')->nullable();
            $table->timestamps();
        });

        Schema::create('advertisement_slot', function (Blueprint $table) {
            $table->foreignId('advertisement_id')->constrained('advertisements')->cascadeOnDelete();
            $table->foreignId('ad_slot_id')->constrained('ad_slots')->cascadeOnDelete();
            $table->primary(['advertisement_id', 'ad_slot_id']);
        });

        Schema::create('advertisement_category', function (Blueprint $table) {
            $table->foreignId('advertisement_id')->constrained('advertisements')->cascadeOnDelete();
            $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
            $table->primary(['advertisement_id', 'category_id']);
        });

        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('news_id')->constrained('news')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('comments')->cascadeOnDelete();
            $table->text('body');
            $table->string('status', 30)->default('pending')->index(); // pending, approved, rejected
            $table->foreignId('moderated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('moderated_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();

            $table->index(['news_id', 'status', 'created_at']);
        });

        Schema::create('bookmarks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('news_id')->constrained('news')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'news_id']);
        });

        Schema::create('news_daily_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('news_id')->constrained('news')->cascadeOnDelete();
            $table->date('stat_date')->index();
            $table->unsignedInteger('views')->default(0);
            $table->unsignedInteger('unique_views')->default(0);
            $table->timestamps();

            $table->unique(['news_id', 'stat_date']);
        });

        Schema::create('contact_messages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('subject')->nullable();
            $table->text('message');
            $table->string('status', 30)->default('unread')->index();
            $table->foreignId('handled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action', 100);
            $table->string('subject_type');
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->json('before')->nullable();
            $table->json('after')->nullable();
            $table->string('request_id')->nullable();
            $table->timestamps();

            $table->index(['subject_type', 'subject_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('contact_messages');
        Schema::dropIfExists('news_daily_stats');
        Schema::dropIfExists('bookmarks');
        Schema::dropIfExists('comments');
        Schema::dropIfExists('advertisement_category');
        Schema::dropIfExists('advertisement_slot');
        Schema::dropIfExists('advertisements');
        Schema::dropIfExists('ad_slots');
    }
};

