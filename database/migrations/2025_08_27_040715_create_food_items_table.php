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
        Schema::create('food_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 8, 2);
            $table->string('category'); // Appetizer, Main Course, Dessert, Beverage
            $table->string('cuisine_style'); // Traditional Japanese, Modern Japanese, Fusion
            $table->text('ingredients')->nullable();
            $table->text('allergens')->nullable();
            $table->string('preparation_time'); // in minutes
            $table->boolean('is_vegetarian')->default(false);
            $table->boolean('is_vegan')->default(false);
            $table->boolean('is_gluten_free')->default(false);
            $table->string('image_path')->nullable();
            $table->boolean('is_available')->default(true);
            $table->integer('stock_quantity')->default(0);
            $table->integer('min_stock_level')->default(5);
            $table->decimal('ai_waste_prediction', 5, 2)->nullable(); // AI predicted waste percentage
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('food_items');
    }
};
