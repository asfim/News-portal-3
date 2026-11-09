<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('disk')->default('public');
            $table->string('path')->unique();
            $table->string('original_name');
            $table->string('mime_type');
            $table->unsignedBigInteger('size_bytes');
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->string('checksum')->nullable();
            $table->string('alt_text')->nullable();
            $table->text('caption')->nullable();
            $table->string('credit')->nullable();
            $table->string('license_note')->nullable();
            $table->string('status', 30)->default('active');
            $table->timestamps();
        });

        Schema::create('media_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('media_id')->constrained('media')->cascadeOnDelete();
            $table->string('variant'); // thumb, card, cover
            $table->string('path');
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->string('format', 20)->default('jpg');
            $table->unsignedBigInteger('size_bytes')->nullable();
            $table->timestamps();

            $table->unique(['media_id', 'variant']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_variants');
        Schema::dropIfExists('media');
    }
};

