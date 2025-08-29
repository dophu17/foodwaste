<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\FoodItem;
use App\Models\Restaurant;
use Carbon\Carbon;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $restaurant = $user->restaurant;

        if (!$restaurant) {
            return redirect()->route('restaurant.create')
                ->with('warning', 'Please create restaurant information before viewing orders.');
        }

        // Build query
        $query = Order::where('restaurant_id', $restaurant->id)
            ->with(['orderItems.foodItem.menu.restaurant']);

        // Search functionality
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('id', 'like', "%{$searchTerm}%")
                  ->orWhere('day_of_week', 'like', "%{$searchTerm}%")
                  ->orWhere('weather_condition', 'like', "%{$searchTerm}%")
                  ->orWhere('special_event', 'like', "%{$searchTerm}%");
            });
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->where('order_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('order_date', '<=', $request->date_to);
        }

        // Filter by day of week
        if ($request->filled('day_of_week')) {
            $query->where('day_of_week', $request->day_of_week);
        }

        // Filter by weather
        if ($request->filled('weather')) {
            $query->where('weather_condition', $request->weather);
        }

        // Get orders with pagination
        $orders = $query->orderBy('order_date', 'desc')->paginate(15);

        // Get filter options
        $dayOfWeeks = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $weatherConditions = ['Sunny', 'Cloudy', 'Rainy', 'Windy'];

        return view('orders.index', compact('orders', 'dayOfWeeks', 'weatherConditions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        $restaurant = $user->restaurant;

        if (!$restaurant) {
            return redirect()->route('restaurant.create')
                ->with('warning', 'Please create restaurant information before creating orders.');
        }

        // Get available food items for this restaurant
        $foodItems = $restaurant->foodItems()
            ->where('is_available', true)
            ->with('menu')
            ->orderBy('name')
            ->get();

        // Get today's date and day of week
        $today = Carbon::now();
        $dayOfWeek = $today->format('l'); // Monday, Tuesday, etc.

        // Weather conditions (you can integrate with weather API later)
        $weatherConditions = ['Sunny', 'Cloudy', 'Rainy', 'Windy'];

        return view('orders.create', compact('foodItems', 'today', 'dayOfWeek', 'weatherConditions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $restaurant = $user->restaurant;

        if (!$restaurant) {
            return redirect()->route('restaurant.create')
                ->with('error', 'Restaurant not found.');
        }

        $request->validate([
            'order_date' => 'required|date',
            'customer_count' => 'required|integer|min:1',
            'weather_condition' => 'nullable|string',
            'day_of_week' => 'required|string',
            'is_holiday' => 'boolean',
            'special_event' => 'nullable|string',
            'notes' => 'nullable|string',
            'food_items' => 'required|array|min:1',
            'food_items.*.food_item_id' => 'required|exists:food_items,id',
            'food_items.*.quantity_sold' => 'required|integer|min:1',
            'food_items.*.unit_price' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // Create the order
            $order = Order::create([
                'restaurant_id' => $restaurant->id,
                'order_date' => $request->order_date,
                'total_amount' => 0, // Will be calculated
                'customer_count' => $request->customer_count,
                'weather_condition' => $request->weather_condition,
                'day_of_week' => $request->day_of_week,
                'is_holiday' => $request->is_holiday ?? false,
                'special_event' => $request->special_event,
                'status' => 'completed',
                'notes' => $request->notes,
            ]);

            $totalAmount = 0;

            // Create order items
            foreach ($request->food_items as $item) {
                $orderItem = OrderItem::create([
                    'order_id' => $order->id,
                    'food_item_id' => $item['food_item_id'],
                    'quantity_sold' => $item['quantity_sold'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['quantity_sold'] * $item['unit_price'],
                ]);

                $totalAmount += $orderItem->total_price;

                // Update food item stock
                $foodItem = FoodItem::find($item['food_item_id']);
                if ($foodItem) {
                    $foodItem->decrement('stock_quantity', $item['quantity_sold']);
                }
            }

            // Update order total amount
            $order->update(['total_amount' => $totalAmount]);

            DB::commit();

            return redirect()->route('orders.show', $order)
                ->with('success', 'Order created successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                ->with('error', 'Failed to create order: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = Auth::user();
        $restaurant = $user->restaurant;

        if (!$restaurant) {
            return redirect()->route('restaurant.create')
                ->with('warning', 'Please create restaurant information before viewing orders.');
        }

        $order = Order::where('restaurant_id', $restaurant->id)
            ->with(['orderItems.foodItem.menu.restaurant'])
            ->findOrFail($id);

        return view('orders.show', compact('order'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Get order statistics for dashboard
     */
    public function statistics()
    {
        $user = Auth::user();
        $restaurant = $user->restaurant;

        if (!$restaurant) {
            return response()->json(['error' => 'Restaurant not found'], 404);
        }

        // Get current month data
        $currentMonth = Carbon::now()->startOfMonth();
        $currentMonthEnd = Carbon::now()->endOfMonth();

        $monthlyStats = [
            'total_orders' => $restaurant->orders()->whereBetween('order_date', [$currentMonth, $currentMonthEnd])->count(),
            'total_revenue' => $restaurant->getTotalRevenue($currentMonth, $currentMonthEnd),
            'total_customers' => $restaurant->getTotalCustomers($currentMonth, $currentMonthEnd),
            'avg_order_value' => $restaurant->orders()->whereBetween('order_date', [$currentMonth, $currentMonthEnd])->avg('total_amount') ?: 0,
        ];

        return response()->json($monthlyStats);
    }
}
