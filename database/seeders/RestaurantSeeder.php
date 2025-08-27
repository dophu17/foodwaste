<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Restaurant;
use App\Models\Menu;
use App\Models\FoodItem;
use App\Models\WasteRecord;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class RestaurantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample user
        $user = User::create([
            'name' => 'Tanaka Restaurant Owner',
            'email' => 'tanaka@restaurant.com',
            'password' => Hash::make('password'),
            'phone' => '090-1234-5678',
        ]);

        // Create sample restaurant
        $restaurant = Restaurant::create([
            'user_id' => $user->id,
            'name' => 'Tanaka Sushi & Ramen',
            'address' => '2-1-1 Ginza, Chuo-ku, Tokyo 104-0061, Japan',
            'phone' => '03-1234-5678',
            'email' => 'info@tanaka-sushi.com',
            'description' => 'Authentic Japanese restaurant specializing in fresh sushi and traditional ramen. We focus on quality ingredients and minimal food waste.',
            'cuisine_type' => 'Traditional Japanese',
            'business_hours' => '11:00 - 22:00',
            'capacity' => 50,
            'status' => 'active',
        ]);

        // Create sample menus
        $lunchMenu = Menu::create([
            'restaurant_id' => $restaurant->id,
            'name' => 'Lunch Special',
            'description' => 'Affordable lunch options with fresh ingredients',
            'category' => 'Lunch',
            'is_active' => true,
            'valid_from' => Carbon::now()->startOfMonth(),
            'valid_until' => Carbon::now()->endOfMonth(),
        ]);

        $dinnerMenu = Menu::create([
            'restaurant_id' => $restaurant->id,
            'name' => 'Dinner Premium',
            'description' => 'Premium dinner selection with seasonal ingredients',
            'category' => 'Dinner',
            'is_active' => true,
            'valid_from' => Carbon::now()->startOfMonth(),
            'valid_until' => Carbon::now()->endOfMonth(),
        ]);

        // Create sample food items for lunch menu
        $sushiSet = FoodItem::create([
            'menu_id' => $lunchMenu->id,
            'name' => 'Sushi Set A',
            'description' => '8 pieces of fresh nigiri sushi with miso soup',
            'price' => 1200.00,
            'category' => 'Main Course',
            'cuisine_style' => 'Traditional Japanese',
            'ingredients' => 'Fresh fish, sushi rice, nori, wasabi, ginger',
            'allergens' => 'Fish, soy',
            'preparation_time' => '15',
            'is_vegetarian' => false,
            'is_vegan' => false,
            'is_gluten_free' => true,
            'is_available' => true,
            'stock_quantity' => 20,
            'min_stock_level' => 5,
            'ai_waste_prediction' => 8.5,
        ]);

        $ramen = FoodItem::create([
            'menu_id' => $lunchMenu->id,
            'name' => 'Tonkotsu Ramen',
            'description' => 'Rich pork bone broth ramen with chashu and soft-boiled egg',
            'price' => 980.00,
            'category' => 'Main Course',
            'cuisine_style' => 'Traditional Japanese',
            'ingredients' => 'Pork bone broth, ramen noodles, chashu, egg, green onions, bamboo shoots',
            'allergens' => 'Wheat, egg, soy',
            'preparation_time' => '20',
            'is_vegetarian' => false,
            'is_vegan' => false,
            'is_gluten_free' => false,
            'is_available' => true,
            'stock_quantity' => 15,
            'min_stock_level' => 3,
            'ai_waste_prediction' => 12.3,
        ]);

        // Create sample food items for dinner menu
        $premiumSushi = FoodItem::create([
            'menu_id' => $dinnerMenu->id,
            'name' => 'Premium Sushi Omakase',
            'description' => 'Chef\'s selection of premium sushi pieces',
            'price' => 3500.00,
            'category' => 'Main Course',
            'cuisine_style' => 'Traditional Japanese',
            'ingredients' => 'Premium fish, sushi rice, nori, wasabi, ginger, truffle oil',
            'allergens' => 'Fish, soy',
            'preparation_time' => '25',
            'is_vegetarian' => false,
            'is_vegan' => false,
            'is_gluten_free' => true,
            'is_available' => true,
            'stock_quantity' => 10,
            'min_stock_level' => 2,
            'ai_waste_prediction' => 5.2,
        ]);

        $wagyuSteak = FoodItem::create([
            'menu_id' => $dinnerMenu->id,
            'name' => 'Wagyu Beef Steak',
            'description' => 'Premium A5 Wagyu beef steak with seasonal vegetables',
            'price' => 8500.00,
            'category' => 'Main Course',
            'cuisine_style' => 'Modern Japanese',
            'ingredients' => 'A5 Wagyu beef, seasonal vegetables, garlic, butter, herbs',
            'allergens' => 'Dairy',
            'preparation_time' => '30',
            'is_vegetarian' => false,
            'is_vegan' => false,
            'is_gluten_free' => true,
            'is_available' => true,
            'stock_quantity' => 8,
            'min_stock_level' => 2,
            'ai_waste_prediction' => 3.1,
        ]);

        // Create sample waste records
        WasteRecord::create([
            'restaurant_id' => $restaurant->id,
            'food_item_id' => $sushiSet->id,
            'waste_date' => Carbon::now()->subDays(2),
            'quantity_wasted' => 150.00,
            'waste_unit' => 'grams',
            'cost_wasted' => 180.00,
            'waste_reason' => 'Customer rejection',
            'notes' => 'Customer found fish too cold',
            'ai_predicted_waste' => 120.00,
            'prediction_accuracy' => 80.0,
            'ai_insights' => ['Consider serving temperature', 'Reduce portion size'],
        ]);

        WasteRecord::create([
            'restaurant_id' => $restaurant->id,
            'food_item_id' => $ramen->id,
            'waste_date' => Carbon::now()->subDays(1),
            'quantity_wasted' => 200.00,
            'waste_unit' => 'grams',
            'cost_wasted' => 196.00,
            'waste_reason' => 'Overcooked',
            'notes' => 'Noodles became too soft during busy period',
            'ai_predicted_waste' => 180.00,
            'prediction_accuracy' => 90.0,
            'ai_insights' => ['Monitor cooking time', 'Adjust batch size'],
        ]);

        WasteRecord::create([
            'restaurant_id' => $restaurant->id,
            'food_item_id' => $premiumSushi->id,
            'waste_date' => Carbon::now(),
            'quantity_wasted' => 50.00,
            'waste_unit' => 'grams',
            'cost_wasted' => 175.00,
            'waste_reason' => 'Expired',
            'notes' => 'Fish not used within freshness window',
            'ai_predicted_waste' => 60.00,
            'prediction_accuracy' => 83.3,
            'ai_insights' => ['Better inventory management', 'Smaller batch preparation'],
        ]);

        $this->command->info('Sample restaurant data created successfully!');
        $this->command->info('Login credentials:');
        $this->command->info('Email: tanaka@restaurant.com');
        $this->command->info('Password: password');
    }
}
