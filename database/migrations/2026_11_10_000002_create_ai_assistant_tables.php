<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_settings', function (Blueprint $table) {
            $table->id();
            $table->string('provider', 50)->default('gemini'); // gemini, openai, mock
            $table->text('api_key')->nullable();
            $table->string('model', 100)->default('gemini-1.5-flash');
            $table->boolean('is_enabled')->default(false);
            $table->unsignedInteger('daily_request_limit')->default(200);
            $table->unsignedInteger('today_requests_count')->default(0);
            $table->date('last_reset_date')->nullable();
            $table->json('options')->nullable();
            $table->timestamps();
        });

        Schema::create('ai_prompt_templates', function (Blueprint $table) {
            $table->id();
            $table->string('key', 50)->unique(); // headline, summary, improve, seo, tags, category, social_caption, draft
            $table->string('title');
            $table->text('system_prompt');
            $table->text('user_prompt_template');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('ai_generation_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('news_id')->nullable()->constrained('news')->nullOnDelete();
            $table->string('action', 50)->index(); // headline, summary, improve, seo, tags, draft, social_caption
            $table->string('provider', 50);
            $table->string('model', 100);
            $table->unsignedInteger('prompt_tokens')->default(0);
            $table->unsignedInteger('completion_tokens')->default(0);
            $table->mediumText('input_text');
            $table->mediumText('output_text')->nullable();
            $table->string('status', 30)->default('success'); // success, failed, rejected, accepted
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_generation_logs');
        Schema::dropIfExists('ai_prompt_templates');
        Schema::dropIfExists('ai_settings');
    }
};
