<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        try {
            DB::statement('ALTER TABLE media DROP INDEX media_path_unique');
        } catch (\Throwable $e) {}

        try {
            DB::statement('ALTER TABLE media MODIFY path VARCHAR(1024) NOT NULL');
        } catch (\Throwable $e) {}

        try {
            DB::statement('ALTER TABLE media ADD INDEX media_path_index (path(255))');
        } catch (\Throwable $e) {}
    }

    public function down(): void
    {
        // Safe no-op
    }
};

