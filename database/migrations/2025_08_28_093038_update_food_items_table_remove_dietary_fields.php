<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('food_items', function (Blueprint $table) {
            // Remove dietary fields
            $table->dropColumn(['is_vegetarian', 'is_vegan', 'is_gluten_free']);
            
            // Make category and cuisine_style nullable
            $table->string('category')->nullable()->change();
            $table->string('cuisine_style')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('food_items', function (Blueprint $table) {
            // Add back dietary fields
            $table->boolean('is_vegetarian')->default(false);
            $table->boolean('is_vegan')->default(false);
            $table->boolean('is_gluten_free')->default(false);
            
            // Make category and cuisine_style required again
            $table->string('category')->nullable(false)->change();
            $table->string('cuisine_style')->nullable(false)->change();
        });
    }
};
