<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('poster_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('dimensions', 20)->default('1080x1080'); // 1080x1080, 1200x630
            $table->string('background_color', 20)->default('#0f172a');
            $table->string('headline_color', 20)->default('#ffffff');
            $table->string('category_badge_color', 20)->default('#c62828');
            $table->decimal('overlay_opacity', 3, 2)->default(0.70);
            $table->string('font_family')->default('SolaimanLipi');
            $table->string('logo_path', 500)->nullable();
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('generated_posters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('news_id')->constrained('news')->cascadeOnDelete();
            $table->foreignId('template_id')->nullable()->constrained('poster_templates')->nullOnDelete();
            $table->text('headline_text');
            $table->string('file_path', 500);
            $table->string('dimensions', 20);
            $table->unsignedBigInteger('file_size_bytes')->default(0);
            $table->foreignId('generated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('generated_posters');
        Schema::dropIfExists('poster_templates');
    }
};
