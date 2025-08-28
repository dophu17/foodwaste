<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;
use App\Models\Restaurant;
use Carbon\Carbon;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first restaurant
        $restaurant = Restaurant::first();
        
        if (!$restaurant) {
            $this->command->info('No restaurant found. Please run RestaurantSeeder first.');
            return;
        }

        $menus = [
            [
                'name' => 'Menu Chính - Traditional Japanese',
                'description' => 'Thực đơn chính với các món ăn truyền thống Nhật Bản, bao gồm sushi, sashimi và các món nóng',
                'category' => 'Traditional',
                'is_active' => true,
                'valid_from' => Carbon::now()->subDays(30),
                'valid_until' => Carbon::now()->addDays(60),
            ],
            [
                'name' => 'Menu Khai Vị - Appetizers',
                'description' => 'Bộ sưu tập các món khai vị Nhật Bản truyền thống, hoàn hảo để bắt đầu bữa ăn',
                'category' => 'Appetizers',
                'is_active' => true,
                'valid_from' => Carbon::now()->subDays(15),
                'valid_until' => Carbon::now()->addDays(45),
            ],
            [
                'name' => 'Menu Tráng Miệng - Desserts',
                'description' => 'Các món tráng miệng ngọt ngào Nhật Bản, từ mochi đến dorayaki truyền thống',
                'category' => 'Desserts',
                'is_active' => true,
                'valid_from' => Carbon::now()->subDays(10),
                'valid_until' => Carbon::now()->addDays(30),
            ],
            [
                'name' => 'Menu Đồ Uống - Beverages',
                'description' => 'Bộ sưu tập đồ uống Nhật Bản, từ trà truyền thống đến các loại nước hiện đại',
                'category' => 'Beverages',
                'is_active' => true,
                'valid_from' => Carbon::now()->subDays(5),
                'valid_until' => Carbon::now()->addDays(25),
            ],
            [
                'name' => 'Menu Chay - Vegetarian',
                'description' => 'Thực đơn chay với các món ăn Nhật Bản không thịt, phù hợp cho người ăn chay',
                'category' => 'Vegetarian',
                'is_active' => true,
                'valid_from' => Carbon::now()->subDays(20),
                'valid_until' => Carbon::now()->addDays(40),
            ],
        ];

        foreach ($menus as $menuData) {
            Menu::create(array_merge($menuData, [
                'restaurant_id' => $restaurant->id,
            ]));
        }

        $this->command->info('Menus seeded successfully!');
    }
}
