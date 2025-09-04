<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Restaurant;
use App\Models\WasteRecord;
use App\Models\FoodItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\GeminiAIService;
use Carbon\Carbon;

class DashboardController extends Controller
{
    protected $geminiService;

    public function __construct(GeminiAIService $geminiService)
    {
        $this->middleware('auth');
        $this->geminiService = $geminiService;
    }

    public function index()
    {
        $user = Auth::user();
        $restaurant = $user->restaurant;

        if (!$restaurant) {
            return redirect()->route('restaurant.create')
                ->with('warning', 'Please create restaurant information before using the system.');
        }

        // Get current month data
        $currentMonth = Carbon::now()->startOfMonth();
        $currentMonthEnd = Carbon::now()->endOfMonth();

        // Calculate total revenue from actual orders
        $totalRevenue = $restaurant->getTotalRevenue($currentMonth, $currentMonthEnd);

        // Calculate total orders from actual orders
        $totalOrders = $restaurant->orders()
            ->whereBetween('order_date', [$currentMonth, $currentMonthEnd])
            ->count();

        // Waste statistics
        $monthlyWaste = WasteRecord::where('restaurant_id', $restaurant->id)
            ->whereBetween('waste_date', [$currentMonth, $currentMonthEnd])
            ->get();

        $totalWasteCost = $monthlyWaste->sum('cost_wasted');
        $wastePercentage = ($totalRevenue > 0 && is_numeric($totalRevenue)) ? ($totalWasteCost / $totalRevenue) * 100 : 0;

        // AI predictions accuracy
        $aiAccuracy = $restaurant->getWastePredictionAccuracy() ?: 0;

        // Low stock items
        $lowStockItems = FoodItem::whereHas('menu', function($query) use ($restaurant) {
            $query->where('restaurant_id', $restaurant->id);
        })->where('stock_quantity', '<=', 'min_stock_level')->get();

        // Recent waste records
        $recentWasteRecords = WasteRecord::where('restaurant_id', $restaurant->id)
            ->with('foodItem')
            ->orderBy('waste_date', 'desc')
            ->limit(5)
            ->get();

        // Waste by category with actual data
        $wasteByCategory = WasteRecord::where('restaurant_id', $restaurant->id)
            ->whereBetween('waste_date', [$currentMonth, $currentMonthEnd])
            ->whereNotNull('ai_predicted_waste')
            ->whereNotNull('actual_waste_percentage')
            ->selectRaw('
                food_items.category,
                SUM(waste_records.quantity_wasted) as total_waste,
                waste_records.waste_unit,
                SUM(waste_records.cost_wasted) as total_cost,
                AVG(waste_records.ai_predicted_waste) as ai_predicted_waste,
                AVG(waste_records.actual_waste_percentage) as actual_waste_percentage
            ')
            ->join('food_items', 'waste_records.food_item_id', '=', 'food_items.id')
            ->groupBy('food_items.category', 'waste_records.waste_unit')
            ->get();

        // Get menu and food item counts
        $totalMenus = $restaurant->menus()->count();
        $totalFoodItems = FoodItem::whereHas('menu', function($query) use ($restaurant) {
            $query->where('restaurant_id', $restaurant->id);
        })->count();

        return view('dashboard', compact(
            'restaurant',
            'totalRevenue',
            'totalOrders',
            'wastePercentage',
            'aiAccuracy',
            'lowStockItems',
            'recentWasteRecords',
            'wasteByCategory',
            'totalMenus',
            'totalFoodItems'
        ));
    }

    /**
     * Get AI analysis data as JSON for API calls
     */
    public function getAnalysisData(Request $request)
    {
        $user = Auth::user();
        $restaurant = $user->restaurant;

        if (!$restaurant) {
            return response()->json(['error' => 'Restaurant not found'], 404);
        }

        $days = $request->get('days', 14);
        
        // Get sales data
        $salesData = $this->getSalesData($restaurant, $days);
        
        // Get waste data
        $wasteData = $this->getWasteData($restaurant, $days);
        
        // Get menu data
        $menuData = $this->getMenuData($restaurant);
        
        // Restaurant info
        $restaurantInfo = [
            'name' => $restaurant->name,
            'cuisine_type' => $restaurant->cuisine_type,
            'capacity' => $restaurant->capacity
        ];

        return response()->json([
            'sales_data' => $salesData,
            'waste_data' => $wasteData,
            'menu_data' => $menuData,
            'restaurant_info' => $restaurantInfo,
            'days' => $days
        ]);
    }

    /**
     * Get forecasting data using Gemini AI
     */
    public function getForecastingData(Request $request)
    {
        $user = Auth::user();
        $restaurant = $user->restaurant;

        if (!$restaurant) {
            return response()->json(['error' => 'Restaurant not found'], 404);
        }

        $days = $request->get('days', 14);
        $forecastPeriod = $request->get('forecast_period', 7);
        
        // Get sales data
        $salesData = $this->getSalesData($restaurant, $days);
        
        // Restaurant data
        $restaurantData = [
            'name' => $restaurant->name,
            'cuisine_type' => $restaurant->cuisine_type,
            'capacity' => $restaurant->capacity
        ];

        // Generate AI forecast
        $forecast = $this->geminiService->generateDemandForecast($restaurantData, $salesData, $forecastPeriod);
        
        return response()->json([
            'forecast' => $forecast,
            'restaurant_data' => $restaurantData,
            'sales_data' => $salesData,
            'forecast_period' => $forecastPeriod
        ]);
    }

    /**
     * Get waste insights using Gemini AI
     */
    public function getWasteInsights(Request $request)
    {
        $user = Auth::user();
        $restaurant = $user->restaurant;

        if (!$restaurant) {
            return response()->json(['error' => 'Restaurant not found'], 404);
        }

        $days = $request->get('days', 14);
        
        // Get waste and sales data
        $wasteData = $this->getWasteData($restaurant, $days);
        $salesData = $this->getSalesData($restaurant, $days);
        
        // Restaurant info
        $restaurantInfo = [
            'name' => $restaurant->name,
            'cuisine_type' => $restaurant->cuisine_type,
            'capacity' => $restaurant->capacity
        ];

        // Generate AI waste insights
        $wasteInsights = $this->geminiService->generateWasteInsights($wasteData, $salesData, $restaurantInfo);
        
        return response()->json([
            'waste_insights' => $wasteInsights,
            'waste_data' => $wasteData,
            'sales_data' => $salesData,
            'restaurant_info' => $restaurantInfo
        ]);
    }

    /**
     * Get menu optimization using Gemini AI
     */
    public function getMenuOptimization(Request $request)
    {
        $user = Auth::user();
        $restaurant = $user->restaurant;

        if (!$restaurant) {
            return response()->json(['error' => 'Restaurant not found'], 404);
        }

        $days = $request->get('days', 14);
        
        // Get all data
        $menuData = $this->getMenuData($restaurant);
        $salesData = $this->getSalesData($restaurant, $days);
        $wasteData = $this->getWasteData($restaurant, $days);

        // Generate AI menu optimization
        $menuOptimization = $this->geminiService->generateMenuOptimization($menuData, $salesData, $wasteData);
        
        return response()->json([
            'menu_optimization' => $menuOptimization,
            'menu_data' => $menuData,
            'sales_data' => $salesData,
            'waste_data' => $wasteData
        ]);
    }

    /**
     * Test Gemini AI connection
     */
    public function testConnection()
    {
        $result = $this->geminiService->testConnection();
        return response()->json($result);
    }

    /**
     * Get sales data for the specified period
     */
    private function getSalesData($restaurant, $days)
    {
        $startDate = Carbon::now()->subDays($days);
        $endDate = Carbon::now();

        $salesData = [];
        
        for ($i = 0; $i < $days; $i++) {
            $date = $startDate->copy()->addDays($i);
            $dateStr = $date->format('Y-m-d');
            
            $orders = Order::where('restaurant_id', $restaurant->id)
                ->whereDate('created_at', $date)
                ->get();
            
            $revenue = $orders->sum('total_amount');
            $orderCount = $orders->count();
            $customerCount = $orders->sum('customer_count');
            
            $salesData[$dateStr] = [
                'orders' => $orderCount,
                'revenue' => $revenue,
                'customers' => $customerCount
            ];
        }
        
        return $salesData;
    }

    /**
     * Get waste data for the specified period
     */
    private function getWasteData($restaurant, $days)
    {
        $startDate = Carbon::now()->subDays($days);
        $endDate = Carbon::now();

        $wasteRecords = WasteRecord::where('restaurant_id', $restaurant->id)
            ->whereBetween('waste_date', [$startDate, $endDate])
            ->with('foodItem')
            ->get();

        $wasteData = [];
        
        foreach ($wasteRecords as $record) {
            $category = $record->foodItem->category ?? 'Unknown';
            
            if (!isset($wasteData[$category])) {
                $wasteData[$category] = [
                    'quantity' => 0,
                    'unit' => $record->waste_unit,
                    'cost' => 0
                ];
            }
            
            $wasteData[$category]['quantity'] += $record->quantity_wasted;
            $wasteData[$category]['cost'] += $record->cost_wasted;
        }
        
        return $wasteData;
    }

    /**
     * Get menu data
     */
    private function getMenuData($restaurant)
    {
        $foodItems = FoodItem::whereHas('menu', function($query) use ($restaurant) {
            $query->where('restaurant_id', $restaurant->id);
        })->get();

        $menuData = [];
        
        foreach ($foodItems as $item) {
            $menuData[] = [
                'name' => $item->name,
                'price' => $item->price,
                'category' => $item->category,
                'stock' => $item->stock_quantity
            ];
        }
        
        return $menuData;
    }
}
