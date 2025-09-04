<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update ai_insights column collation to utf8mb4_unicode_ci
        DB::statement('ALTER TABLE waste_records MODIFY COLUMN ai_insights JSON COLLATE utf8mb4_unicode_ci');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to default collation
        DB::statement('ALTER TABLE waste_records MODIFY COLUMN ai_insights JSON');
    }
};