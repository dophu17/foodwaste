<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FoodItem;
use App\Models\Menu;
use App\Models\Restaurant;

class FoodItemSeeder extends Seeder
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

        // Get menus for this restaurant
        $menus = $restaurant->menus;
        
        if ($menus->isEmpty()) {
            $this->command->info('No menus found. Please run MenuSeeder first.');
            return;
        }

        $foodItems = [
            // Appetizers
            [
                'name' => 'Gyoza (餃子)',
                'description' => 'Bánh bao nhân thịt heo và rau cải, chiên giòn bên ngoài, mềm bên trong',
                'price' => 45000,
                'category' => 'Appetizer',
                'cuisine_style' => 'Traditional Japanese',
                'ingredients' => 'Thịt heo xay, bắp cải, hành lá, bột gyoza, dầu mè',
                'allergens' => 'Gluten, mè',
                'preparation_time' => '8 phút',
                'is_available' => true,
                'stock_quantity' => 50,
                'min_stock_level' => 10,
                'ai_waste_prediction' => 8.5,
            ],
            [
                'name' => 'Edamame (枝豆)',
                'description' => 'Đậu nành non luộc với muối, món khai vị truyền thống Nhật Bản',
                'price' => 25000,
                'category' => 'Appetizer',
                'cuisine_style' => 'Traditional Japanese',
                'ingredients' => 'Đậu nành non, muối biển',
                'allergens' => 'Đậu nành',
                'preparation_time' => '5 phút',
                'is_available' => true,
                'stock_quantity' => 80,
                'min_stock_level' => 15,
                'ai_waste_prediction' => 5.2,
            ],
            [
                'name' => 'Miso Soup (味噌汁)',
                'description' => 'Súp miso truyền thống với đậu phụ, rong biển và hành lá',
                'price' => 30000,
                'category' => 'Appetizer',
                'cuisine_style' => 'Traditional Japanese',
                'ingredients' => 'Miso paste, đậu phụ, rong biển wakame, hành lá, nước dashi',
                'allergens' => 'Đậu nành, cá',
                'preparation_time' => '10 phút',
                'is_available' => true,
                'stock_quantity' => 60,
                'min_stock_level' => 12,
                'ai_waste_prediction' => 6.8,
            ],

            // Main Courses
            [
                'name' => 'Tonkatsu (とんかつ)',
                'description' => 'Thịt heo cốt lết tẩm bột chiên giòn, ăn kèm bắp cải và sốt tonkatsu',
                'price' => 120000,
                'category' => 'Main Course',
                'cuisine_style' => 'Traditional Japanese',
                'ingredients' => 'Thịt heo cốt lết, bột chiên, trứng, bánh mì vụn, bắp cải, sốt tonkatsu',
                'allergens' => 'Gluten, trứng, mè',
                'preparation_time' => '20 phút',
                'is_available' => true,
                'stock_quantity' => 30,
                'min_stock_level' => 8,
                'ai_waste_prediction' => 12.5,
            ],
            [
                'name' => 'Teriyaki Chicken (照り焼きチキン)',
                'description' => 'Gà nướng sốt teriyaki ngọt ngào, ăn kèm cơm và rau củ',
                'price' => 95000,
                'category' => 'Main Course',
                'cuisine_style' => 'Modern Japanese',
                'ingredients' => 'Ức gà, sốt teriyaki, mật ong, nước tương, gừng, tỏi, cơm trắng',
                'allergens' => 'Gluten, mật ong',
                'preparation_time' => '25 phút',
                'is_available' => true,
                'stock_quantity' => 40,
                'min_stock_level' => 10,
                'ai_waste_prediction' => 15.3,
            ],
            [
                'name' => 'Vegetable Tempura (野菜天ぷら)',
                'description' => 'Rau củ tẩm bột chiên giòn, bao gồm cà tím, bí ngòi, ớt chuông',
                'price' => 75000,
                'category' => 'Main Course',
                'cuisine_style' => 'Traditional Japanese',
                'ingredients' => 'Cà tím, bí ngòi, ớt chuông, bột tempura, nước sốt tentsuyu',
                'allergens' => 'Gluten, cá',
                'preparation_time' => '18 phút',
                'is_available' => true,
                'stock_quantity' => 35,
                'min_stock_level' => 8,
                'ai_waste_prediction' => 9.7,
            ],

            // Desserts
            [
                'name' => 'Mochi Ice Cream (もちアイス)',
                'description' => 'Kem mochi truyền thống với vị trà xanh, dâu tây và vani',
                'price' => 55000,
                'category' => 'Dessert',
                'cuisine_style' => 'Modern Japanese',
                'ingredients' => 'Bột gạo nếp, kem tươi, đường, trà xanh matcha, dâu tây, vani',
                'allergens' => 'Sữa, gluten',
                'preparation_time' => '15 phút',
                'is_available' => true,
                'stock_quantity' => 45,
                'min_stock_level' => 12,
                'ai_waste_prediction' => 7.2,
            ],
            [
                'name' => 'Dorayaki (どら焼き)',
                'description' => 'Bánh pancake Nhật Bản nhân đậu đỏ ngọt, món tráng miệng truyền thống',
                'price' => 40000,
                'category' => 'Dessert',
                'cuisine_style' => 'Traditional Japanese',
                'ingredients' => 'Bột mì, trứng, đường, sữa, đậu đỏ azuki, mật ong',
                'allergens' => 'Gluten, trứng, sữa',
                'preparation_time' => '12 phút',
                'is_available' => true,
                'stock_quantity' => 55,
                'min_stock_level' => 15,
                'ai_waste_prediction' => 6.1,
            ],

            // Beverages
            [
                'name' => 'Matcha Latte (抹茶ラテ)',
                'description' => 'Trà xanh matcha Nhật Bản pha với sữa tươi và bọt sữa',
                'price' => 65000,
                'category' => 'Beverage',
                'cuisine_style' => 'Modern Japanese',
                'ingredients' => 'Bột trà xanh matcha, sữa tươi, đường, bọt sữa',
                'allergens' => 'Sữa',
                'preparation_time' => '8 phút',
                'is_available' => true,
                'stock_quantity' => 70,
                'min_stock_level' => 20,
                'ai_waste_prediction' => 4.8,
            ],
            [
                'name' => 'Sakura Tea (桜茶)',
                'description' => 'Trà hoa anh đào Nhật Bản, hương vị nhẹ nhàng và thơm ngon',
                'price' => 35000,
                'category' => 'Beverage',
                'cuisine_style' => 'Traditional Japanese',
                'ingredients' => 'Lá trà xanh, hoa anh đào muối, nước nóng',
                'allergens' => 'Không có',
                'preparation_time' => '5 phút',
                'is_available' => true,
                'stock_quantity' => 90,
                'min_stock_level' => 25,
                'ai_waste_prediction' => 3.2,
            ],
        ];

        foreach ($foodItems as $foodItemData) {
            // Assign to a random menu from the restaurant
            $menu = $menus->random();
            
            FoodItem::create(array_merge($foodItemData, [
                'menu_id' => $menu->id,
            ]));
        }

        $this->command->info('Food items seeded successfully!');
    }
}
