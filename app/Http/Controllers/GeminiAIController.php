<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\GeminiAIService;
use App\Models\Restaurant;
use App\Models\Order;
use App\Models\FoodItem;
use App\Models\WasteRecord;
use Carbon\Carbon;

class GeminiAIController extends Controller
{
    protected $geminiService;

    public function __construct(GeminiAIService $geminiService)
    {
        $this->middleware('auth');
        $this->geminiService = $geminiService;
    }

    /**
     * Test Gemini AI connection
     */
    public function testConnection()
    {
        try {
            $result = $this->geminiService->testConnection();
            
            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'],
                    'response' => $result['response']
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => $result['message']
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi kết nối: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate demand forecast using Gemini AI
     */
    public function generateDemandForecast(Request $request)
    {
        try {
            $user = Auth::user();
            $restaurant = $user->restaurant;
            
            if (!$restaurant) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy thông tin nhà hàng'
                ], 404);
            }

            $forecastPeriod = $request->get('period', 7);
            
            // Get restaurant data
            $restaurantData = [
                'name' => $restaurant->name,
                'cuisine_type' => $restaurant->cuisine_type ?? 'Không xác định',
                'capacity' => $restaurant->capacity ?? 0
            ];

            // Get sales data for last 14 days
            $salesData = $this->getSalesData($restaurant->id, 14);
            
            if (empty($salesData)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không có đủ dữ liệu bán hàng để dự báo'
                ], 400);
            }

            // Generate forecast using Gemini AI
            $forecast = $this->geminiService->generateDemandForecast(
                $restaurantData, 
                $salesData, 
                $forecastPeriod
            );

            if ($forecast) {
                return response()->json([
                    'success' => true,
                    'message' => 'Dự báo nhu cầu đã được tạo thành công',
                    'data' => $forecast,
                    'generated_at' => now()->toISOString()
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Không thể tạo dự báo nhu cầu'
            ], 500);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate waste insights using Gemini AI
     */
    public function generateWasteInsights(Request $request)
    {
        try {
            $user = Auth::user();
            $restaurant = $user->restaurant;
            
            if (!$restaurant) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy thông tin nhà hàng'
                ], 404);
            }

            // Get restaurant info
            $restaurantInfo = [
                'name' => $restaurant->name,
                'cuisine_type' => $restaurant->cuisine_type ?? 'Không xác định'
            ];

            // Get waste data for last 30 days
            $wasteData = $this->getWasteData($restaurant->id, 30);
            
            // Get sales data for last 30 days
            $salesData = $this->getSalesData($restaurant->id, 30);

            if (empty($wasteData)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không có dữ liệu lãng phí để phân tích'
                ], 400);
            }

            // Generate waste insights using Gemini AI
            $insights = $this->geminiService->generateWasteInsights(
                $wasteData, 
                $salesData, 
                $restaurantInfo
            );

            if ($insights) {
                return response()->json([
                    'success' => true,
                    'message' => 'Phân tích lãng phí đã được tạo thành công',
                    'data' => $insights,
                    'generated_at' => now()->toISOString()
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Không thể tạo phân tích lãng phí'
            ], 500);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate menu optimization using Gemini AI
     */
    public function generateMenuOptimization(Request $request)
    {
        try {
            $user = Auth::user();
            $restaurant = $user->restaurant;
            
            if (!$restaurant) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy thông tin nhà hàng'
                ], 404);
            }

            // Get menu data
            $menuData = $this->getMenuData($restaurant->id);
            
            // Get sales data for last 30 days
            $salesData = $this->getSalesData($restaurant->id, 30);
            
            // Get waste data for last 30 days
            $wasteData = $this->getWasteData($restaurant->id, 30);

            if (empty($menuData)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không có dữ liệu thực đơn để phân tích'
                ], 400);
            }

            // Generate menu optimization using Gemini AI
            $optimization = $this->geminiService->generateMenuOptimization(
                $menuData, 
                $salesData, 
                $wasteData
            );

            if ($optimization) {
                return response()->json([
                    'success' => true,
                    'message' => 'Tối ưu hóa thực đơn đã được tạo thành công',
                    'data' => $optimization,
                    'generated_at' => now()->toISOString()
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Không thể tạo tối ưu hóa thực đơn'
            ], 500);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get comprehensive AI analysis
     */
    public function getComprehensiveAnalysis(Request $request)
    {
        try {
            $user = Auth::user();
            $restaurant = $user->restaurant;
            
            if (!$restaurant) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không tìm thấy thông tin nhà hàng'
                ], 404);
            }

            $analysis = [
                'restaurant_info' => [
                    'name' => $restaurant->name,
                    'cuisine_type' => $restaurant->cuisine_type,
                    'capacity' => $restaurant->capacity,
                    'analysis_period' => '14 ngày gần nhất'
                ],
                'ai_insights' => [],
                'generated_at' => now()->toISOString()
            ];

            // Generate demand forecast
            $restaurantData = [
                'name' => $restaurant->name,
                'cuisine_type' => $restaurant->cuisine_type ?? 'Không xác định',
                'capacity' => $restaurant->capacity ?? 0
            ];
            
            $salesData = $this->getSalesData($restaurant->id, 14);
            if (!empty($salesData)) {
                $forecast = $this->geminiService->generateDemandForecast(
                    $restaurantData, 
                    $salesData, 
                    7
                );
                if ($forecast) {
                    $analysis['ai_insights']['demand_forecast'] = $forecast;
                }
            }

            // Generate waste insights
            $wasteData = $this->getWasteData($restaurant->id, 30);
            if (!empty($wasteData)) {
                $restaurantInfo = [
                    'name' => $restaurant->name,
                    'cuisine_type' => $restaurant->cuisine_type ?? 'Không xác định'
                ];
                
                $insights = $this->geminiService->generateWasteInsights(
                    $wasteData, 
                    $salesData, 
                    $restaurantInfo
                );
                if ($insights) {
                    $analysis['ai_insights']['waste_insights'] = $insights;
                }
            }

            // Generate menu optimization
            $menuData = $this->getMenuData($restaurant->id);
            if (!empty($menuData)) {
                $optimization = $this->geminiService->generateMenuOptimization(
                    $menuData, 
                    $salesData, 
                    $wasteData
                );
                if ($optimization) {
                    $analysis['ai_insights']['menu_optimization'] = $optimization;
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Phân tích AI toàn diện đã được tạo thành công',
                'data' => $analysis
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Lỗi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get sales data for analysis
     */
    protected function getSalesData($restaurantId, $days)
    {
        $startDate = Carbon::now()->subDays($days);
        
        $orders = Order::where('restaurant_id', $restaurantId)
            ->where('order_date', '>=', $startDate)
            ->selectRaw('DATE(order_date) as date, COUNT(*) as orders, SUM(total_amount) as revenue, SUM(customer_count) as customers')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $salesData = [];
        foreach ($orders as $order) {
            $salesData[$order->date] = [
                'orders' => $order->orders,
                'revenue' => $order->revenue,
                'customers' => $order->customers
            ];
        }

        return $salesData;
    }

    /**
     * Get waste data for analysis
     */
    protected function getWasteData($restaurantId, $days)
    {
        $startDate = Carbon::now()->subDays($days);
        
        $wasteRecords = WasteRecord::whereHas('foodItem.menu', function($query) use ($restaurantId) {
                $query->where('restaurant_id', $restaurantId);
            })
            ->where('waste_date', '>=', $startDate)
            ->selectRaw('food_items.category, SUM(waste_records.quantity_wasted) as quantity, waste_records.waste_unit, SUM(waste_records.cost_wasted) as cost')
            ->join('food_items', 'waste_records.food_item_id', '=', 'food_items.id')
            ->groupBy('food_items.category', 'waste_records.waste_unit')
            ->get();

        $wasteData = [];
        foreach ($wasteRecords as $record) {
            $wasteData[$record->category] = [
                'quantity' => $record->quantity,
                'unit' => $record->waste_unit,
                'cost' => $record->cost
            ];
        }

        return $wasteData;
    }

    /**
     * Get menu data for analysis
     */
    protected function getMenuData($restaurantId)
    {
        $foodItems = FoodItem::whereHas('menu', function($query) use ($restaurantId) {
                $query->where('restaurant_id', $restaurantId);
            })
            ->select('name', 'price', 'category', 'stock_quantity as stock')
            ->get();

        $menuData = [];
        foreach ($foodItems as $item) {
            $menuData[] = [
                'name' => $item->name,
                'price' => $item->price,
                'category' => $item->category,
                'stock' => $item->stock
            ];
        }

        return $menuData;
    }
}

