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
        Schema::create('waste_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->onDelete('cascade');
            $table->foreignId('food_item_id')->constrained()->onDelete('cascade');
            $table->date('waste_date');
            $table->decimal('quantity_wasted', 8, 2); // in grams or pieces
            $table->string('waste_unit'); // grams, pieces, servings
            $table->decimal('cost_wasted', 8, 2); // monetary value of wasted food
            $table->string('waste_reason'); // Expired, Overcooked, Customer rejection, etc.
            $table->text('notes')->nullable();
            $table->decimal('ai_predicted_waste', 8, 2)->nullable(); // AI prediction
            $table->decimal('actual_waste_percentage', 5, 2)->nullable(); // Actual waste percentage
            $table->decimal('prediction_accuracy', 5, 2)->nullable(); // How accurate was the AI prediction
            $table->json('ai_insights')->nullable(); // AI recommendations and insights
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('waste_records');
    }
};
