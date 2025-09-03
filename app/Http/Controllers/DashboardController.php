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


}
