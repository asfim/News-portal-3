<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('news_imports', function (Blueprint $table) {
            $table->dropIndex('news_imports_guid_index');
            $table->text('guid')->change();
            $table->string('guid_hash', 64)->nullable()->after('guid')->index();
            $table->text('source_url')->change();
            $table->text('image_url')->nullable()->change();
        });

        Schema::table('news', function (Blueprint $table) {
            $table->text('source_url')->nullable()->change();
        });

        Schema::table('news_sources', function (Blueprint $table) {
            $table->text('feed_url')->change();
        });
    }

    public function down(): void
    {
        Schema::table('news_sources', function (Blueprint $table) {
            $table->string('feed_url', 500)->change();
        });

        Schema::table('news', function (Blueprint $table) {
            $table->string('source_url', 500)->nullable()->change();
        });

        Schema::table('news_imports', function (Blueprint $table) {
            $table->dropColumn('guid_hash');
            $table->string('image_url', 500)->nullable()->change();
            $table->string('source_url', 500)->change();
            $table->string('guid', 255)->change();
            $table->index('guid', 'news_imports_guid_index');
        });
    }
};

