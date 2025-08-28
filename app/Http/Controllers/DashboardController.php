<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Restaurant;
use App\Models\WasteRecord;
use App\Models\FoodItem;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
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

        // Calculate total revenue (simulated for now)
        $totalRevenue = 500000; // ¥500,000 - this should come from actual orders

        // Calculate total orders (simulated for now)
        $totalOrders = 150; // This should come from actual orders

        // Waste statistics
        $monthlyWaste = WasteRecord::where('restaurant_id', $restaurant->id)
            ->whereBetween('waste_date', [$currentMonth, $currentMonthEnd])
            ->get();

        $totalWasteCost = $monthlyWaste->sum('cost_wasted');
        $wastePercentage = $totalRevenue > 0 ? ($totalWasteCost / $totalRevenue) * 100 : 0;

        // AI predictions accuracy
        $aiAccuracy = $restaurant->getWastePredictionAccuracy();

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

        // AI insights and recommendations
        $aiInsights = $this->generateAiInsights($restaurant, $monthlyWaste);

        return view('dashboard', compact(
            'restaurant',
            'totalRevenue',
            'totalOrders',
            'wastePercentage',
            'aiAccuracy',
            'lowStockItems',
            'recentWasteRecords',
            'wasteByCategory',
            'aiInsights',
            'totalMenus',
            'totalFoodItems'
        ));
    }

    public function wasteAnalytics()
    {
        $user = Auth::user();
        $restaurant = $user->restaurant;

        if (!$restaurant) {
            return redirect()->route('restaurant.create')
                ->with('warning', 'Please create restaurant information before using the system.');
        }

        // Get waste analytics data
        $currentMonth = Carbon::now()->startOfMonth();
        $currentMonthEnd = Carbon::now()->endOfMonth();

        $monthlyWaste = WasteRecord::where('restaurant_id', $restaurant->id)
            ->whereBetween('waste_date', [$currentMonth, $currentMonthEnd])
            ->get();

        $totalWasteCost = $monthlyWaste->sum('cost_wasted');
        $totalWasteQuantity = $monthlyWaste->sum('quantity_wasted');
        $wasteCount = $monthlyWaste->count();

        // Waste by category
        $wasteByCategory = WasteRecord::where('restaurant_id', $restaurant->id)
            ->whereBetween('waste_date', [$currentMonth, $currentMonthEnd])
            ->selectRaw('
                food_items.category,
                SUM(waste_records.quantity_wasted) as total_waste,
                waste_records.waste_unit,
                SUM(waste_records.cost_wasted) as total_cost,
                COUNT(*) as count
            ')
            ->join('food_items', 'waste_records.food_item_id', '=', 'food_items.id')
            ->groupBy('food_items.category', 'waste_records.waste_unit')
            ->get();

        // Daily waste trend
        $dailyWaste = WasteRecord::where('restaurant_id', $restaurant->id)
            ->whereBetween('waste_date', [$currentMonth, $currentMonthEnd])
            ->selectRaw('DATE(waste_date) as date, SUM(cost_wasted) as daily_cost')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('waste.analytics', compact(
            'restaurant',
            'totalWasteCost',
            'totalWasteQuantity',
            'wasteCount',
            'wasteByCategory',
            'dailyWaste'
        ));
    }

    public function aiInsights()
    {
        $user = Auth::user();
        $restaurant = $user->restaurant;

        if (!$restaurant) {
            return redirect()->route('restaurant.create')
                ->with('warning', 'Please create restaurant information before using the system.');
        }

        // Get AI insights data
        $currentMonth = Carbon::now()->startOfMonth();
        $currentMonthEnd = Carbon::now()->endOfMonth();

        $monthlyWaste = WasteRecord::where('restaurant_id', $restaurant->id)
            ->whereBetween('waste_date', [$currentMonth, $currentMonthEnd])
            ->get();

        $aiInsights = $this->generateAiInsights($restaurant, $monthlyWaste);

        // AI prediction accuracy
        $aiAccuracy = $restaurant->getWastePredictionAccuracy();

        // Top waste items
        $topWasteItems = WasteRecord::where('restaurant_id', $restaurant->id)
            ->whereBetween('waste_date', [$currentMonth, $currentMonthEnd])
            ->with('foodItem')
            ->orderBy('cost_wasted', 'desc')
            ->limit(10)
            ->get();

        return view('ai.insights', compact(
            'restaurant',
            'aiInsights',
            'aiAccuracy',
            'topWasteItems'
        ));
    }

    private function generateAiInsights($restaurant, $monthlyWaste)
    {
        $insights = [];

        // Analyze waste patterns
        if ($monthlyWaste->count() > 0) {
            $avgWasteCost = $monthlyWaste->avg('cost_wasted');
            $avgWasteQuantity = $monthlyWaste->avg('quantity_wasted');

            if ($avgWasteCost > 5000) { // ¥5000
                $insights[] = [
                    'title' => 'High Waste Cost Alert',
                    'description' => 'Food waste cost is high. Consider adjusting portion sizes and demand forecasting.',
                    'type' => 'warning'
                ];
            }

            if ($avgWasteQuantity > 100) { // 100g
                $insights[] = [
                    'title' => 'Moderate Waste Level',
                    'description' => 'Food waste is at moderate level. There is room for further optimization.',
                    'type' => 'info'
                ];
            }
        }

        // Check for low stock items
        $lowStockCount = FoodItem::whereHas('menu', function($query) use ($restaurant) {
            $query->where('restaurant_id', $restaurant->id);
        })->where('stock_quantity', '<=', 'min_stock_level')->count();

        if ($lowStockCount > 0) {
            $insights[] = [
                'title' => 'Low Stock Warning',
                'description' => "{$lowStockCount} food items are running low on stock. Please replenish ingredients.",
                'type' => 'danger'
            ];
        }

        // Seasonal recommendations
        $currentMonth = Carbon::now()->month;
        if (in_array($currentMonth, [6, 7, 8])) { // Summer months
            $insights[] = [
                'title' => 'Seasonal Menu Adjustment',
                'description' => 'Summer season - recommend increasing cold dishes, salads and reducing portion sizes.',
                'type' => 'success'
            ];
        }

        // If no insights, provide general tips
        if (empty($insights)) {
            $insights[] = [
                'title' => 'Great Performance!',
                'description' => 'Your restaurant is doing well with waste management. Keep up the good work!',
                'type' => 'success'
            ];
        }

        return $insights;
    }
}
