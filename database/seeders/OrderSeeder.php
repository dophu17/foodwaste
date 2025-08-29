<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Restaurant;
use App\Models\FoodItem;
use Carbon\Carbon;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $restaurant = Restaurant::first();
        
        if (!$restaurant) {
            $this->command->info('No restaurant found. Please run RestaurantSeeder first.');
            return;
        }

        $foodItems = FoodItem::whereHas('menu', function($query) use ($restaurant) {
            $query->where('restaurant_id', $restaurant->id);
        })->get();

        if ($foodItems->isEmpty()) {
            $this->command->info('No food items found. Please run FoodItemSeeder first.');
            return;
        }

        // Generate orders for the last 30 days
        for ($i = 29; $i >= 0; $i--) {
            $orderDate = Carbon::now()->subDays($i);
            $dayOfWeek = $orderDate->format('l');
            
            // Weekend vs weekday logic
            $isWeekend = in_array($dayOfWeek, ['Saturday', 'Sunday']);
            $baseCustomerCount = $isWeekend ? rand(25, 45) : rand(15, 35);
            
            // Weather conditions (simulated)
            $weatherConditions = ['Sunny', 'Cloudy', 'Rainy', 'Windy'];
            $weather = $weatherConditions[array_rand($weatherConditions)];
            
            // Holiday logic (simplified)
            $isHoliday = $orderDate->isWeekend() || rand(1, 10) === 1;
            
            // Special events
            $specialEvents = [null, 'Local Festival', 'Sports Event', 'Concert', 'Business Meeting'];
            $specialEvent = $specialEvents[array_rand($specialEvents)];
            
            // Adjust customer count based on factors
            $customerCount = $baseCustomerCount;
            if ($isHoliday) $customerCount += rand(5, 15);
            if ($specialEvent) $customerCount += rand(10, 25);
            if ($weather === 'Rainy') $customerCount -= rand(5, 10);
            
            // Create order
            $order = Order::create([
                'restaurant_id' => $restaurant->id,
                'order_date' => $orderDate,
                'total_amount' => 0, // Will be calculated
                'customer_count' => $customerCount,
                'weather_condition' => $weather,
                'day_of_week' => $dayOfWeek,
                'is_holiday' => $isHoliday,
                'special_event' => $specialEvent,
                'status' => 'completed',
                'notes' => $this->generateOrderNotes($weather, $specialEvent, $isHoliday)
            ]);

            // Create order items
            $orderTotal = 0;
            $itemsToOrder = rand(3, 8); // Random number of items per order
            
            for ($j = 0; $j < $itemsToOrder; $j++) {
                $foodItem = $foodItems->random();
                $quantity = rand(1, 4);
                $unitPrice = $foodItem->price;
                $totalPrice = $quantity * $unitPrice;
                
                OrderItem::create([
                    'order_id' => $order->id,
                    'food_item_id' => $foodItem->id,
                    'quantity_sold' => $quantity,
                    'unit_price' => $unitPrice,
                    'total_price' => $totalPrice,
                    'notes' => $this->generateItemNotes($foodItem, $quantity)
                ]);
                
                $orderTotal += $totalPrice;
            }
            
            // Update order total
            $order->update(['total_amount' => $orderTotal]);
        }

        $this->command->info('Sample orders created successfully!');
        $this->command->info('Generated orders for the last 30 days with realistic patterns.');
    }

    private function generateOrderNotes($weather, $specialEvent, $isHoliday)
    {
        $notes = [];
        
        if ($weather === 'Rainy') {
            $notes[] = 'Rainy weather affected customer turnout';
        }
        
        if ($specialEvent) {
            $notes[] = "Special event: {$specialEvent}";
        }
        
        if ($isHoliday) {
            $notes[] = 'Holiday period - increased demand';
        }
        
        return implode('; ', $notes);
    }

    private function generateItemNotes($foodItem, $quantity)
    {
        $notes = [];
        
        if ($quantity > 2) {
            $notes[] = 'Popular item - high demand';
        }
        
        if ($foodItem->category === 'Main Course') {
            $notes[] = 'Main course item';
        }
        
        return implode('; ', $notes);
    }
}
