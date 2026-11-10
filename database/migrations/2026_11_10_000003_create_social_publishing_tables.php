<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('social_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('provider', 50)->default('facebook');
            $table->string('account_id', 100); // Meta Page ID
            $table->string('account_name');
            $table->text('access_token'); // Encrypted
            $table->timestamp('token_expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('auto_publish')->default(false);
            $table->json('settings')->nullable();
            $table->timestamps();
        });

        Schema::create('social_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('social_account_id')->constrained('social_accounts')->cascadeOnDelete();
            $table->foreignId('news_id')->constrained('news')->cascadeOnDelete();
            $table->string('idempotency_key', 64)->unique();
            $table->text('post_text');
            $table->string('media_url', 500)->nullable();
            $table->string('external_post_id', 100)->nullable();
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->string('status', 30)->default('draft')->index(); // draft, scheduled, publishing, published, failed
            $table->unsignedInteger('retry_count')->default(0);
            $table->text('last_error')->nullable();
            $table->timestamps();
        });

        Schema::create('social_post_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('social_post_id')->constrained('social_posts')->cascadeOnDelete();
            $table->string('action', 50);
            $table->string('status', 30);
            $table->json('response_payload')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('social_post_logs');
        Schema::dropIfExists('social_posts');
        Schema::dropIfExists('social_accounts');
    }
};
