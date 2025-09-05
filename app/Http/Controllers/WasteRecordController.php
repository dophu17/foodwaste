<?php

namespace App\Http\Controllers;

use App\Models\WasteRecord;
use App\Models\FoodItem;
use App\Models\Restaurant;
use App\Services\GeminiAIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class WasteRecordController extends Controller
{
    protected $geminiService;

    public function __construct(GeminiAIService $geminiService)
    {
        $this->middleware('auth');
        $this->geminiService = $geminiService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Get user's restaurant
        $restaurant = $user->restaurant;
        
        if (!$restaurant) {
            return redirect()->route('restaurant.create')
                ->with('info', __('messages.Please create a restaurant first'));
        }
        
        // Get food items from user's restaurant
        $foodItems = FoodItem::whereHas('menu.restaurant', function($query) use ($restaurant) {
            $query->where('id', $restaurant->id);
        })->get();
        
        $query = WasteRecord::whereHas('foodItem.menu.restaurant', function($q) use ($restaurant) {
            $q->where('id', $restaurant->id);
        })->with(['foodItem.menu.restaurant']);
        
        // Filter by date range
        if ($request->filled('start_date')) {
            $query->where('waste_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->where('waste_date', '<=', $request->end_date);
        }
        
        // Filter by food item
        if ($request->filled('food_item_id')) {
            $query->where('food_item_id', $request->food_item_id);
        }
        
        // Filter by reason
        if ($request->filled('waste_reason')) {
            $query->where('waste_reason', $request->waste_reason);
        }
        
        $wasteRecords = $query->orderBy('waste_date', 'desc')->paginate(15);
        
        // Common waste reasons
        $wasteReasons = [
            'Food left over after meal time',
            'Expired ingredients',
            'Damaged during preparation',
            'Customer dissatisfaction',
            'Over-preparation',
            'Equipment malfunction',
            'End of day closure',
            'Special event cancelled',
            'Customer allergy',
            'Tasting during cooking'
        ];
        
        return view('waste-records.index', compact('wasteRecords', 'foodItems', 'restaurant', 'wasteReasons'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        
        // Get user's restaurant
        $restaurant = $user->restaurant;
        
        if (!$restaurant) {
            return redirect()->route('restaurant.create')
                ->with('info', __('messages.Please create a restaurant first'));
        }
        
        // Get food items from user's restaurant
        $foodItems = FoodItem::whereHas('menu.restaurant', function($query) use ($restaurant) {
            $query->where('id', $restaurant->id);
        })->with('menu.restaurant')->get();
        
        // Common waste reasons
        $wasteReasons = [
            'Food left over after meal time',
            'Expired ingredients',
            'Damaged during preparation',
            'Customer dissatisfaction',
            'Over-preparation',
            'Equipment malfunction',
            'End of day closure',
            'Special event cancelled',
            'Customer allergy',
            'Tasting during cooking'
        ];
        
        return view('waste-records.create', compact('foodItems', 'wasteReasons'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'food_item_id' => 'required|exists:food_items,id',
            'waste_date' => 'required|date',
            'quantity_wasted' => 'required|numeric|min:0.01',
            'waste_unit' => 'required|string|max:50',
            'cost_wasted' => 'required|numeric|min:0',
            'waste_reason' => 'required|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);
        
        // Verify the food item belongs to user's restaurant
        $user = Auth::user();
        $restaurant = $user->restaurant;
        if (!$restaurant) {
            abort(403, __('messages.Please create a restaurant first'));
        }
        
        $foodItem = FoodItem::whereHas('menu.restaurant', function($query) use ($restaurant) {
            $query->where('id', $restaurant->id);
        })->findOrFail($request->food_item_id);
        
        // Calculate AI fields
        $aiFields = $this->calculateAIFields($foodItem, $request);
        
        $wasteRecord = WasteRecord::create([
            'restaurant_id' => $restaurant->id,
            'food_item_id' => $request->food_item_id,
            'waste_date' => $request->waste_date,
            'quantity_wasted' => $request->quantity_wasted,
            'waste_unit' => $request->waste_unit,
            'cost_wasted' => $request->cost_wasted,
            'waste_reason' => $request->waste_reason,
            'notes' => $request->notes,
            'ai_predicted_waste' => $aiFields['ai_predicted_waste'],
            'actual_waste_percentage' => $aiFields['actual_waste_percentage'],
            'prediction_accuracy' => $aiFields['prediction_accuracy'],
            'ai_insights' => $aiFields['ai_insights'],
        ]);
        
        return redirect()->route('waste-records.index')
            ->with('success', __('messages.Waste record created successfully!'));
    }

    /**
     * Display the specified resource.
     */
    public function show(WasteRecord $wasteRecord)
    {
        // Verify ownership
        $user = Auth::user();
        $restaurant = $user->restaurant;
        
        if (!$restaurant || $wasteRecord->restaurant_id !== $restaurant->id) {
            abort(403, __('messages.Unauthorized access to this waste record.'));
        }
        
        $wasteRecord->load(['foodItem.menu.restaurant']);
        
        return view('waste-records.show', compact('wasteRecord'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(WasteRecord $wasteRecord)
    {
        // Verify ownership
        $user = Auth::user();
        $restaurant = $user->restaurant;
        
        if (!$restaurant || $wasteRecord->restaurant_id !== $restaurant->id) {
            abort(403, __('messages.Unauthorized access to this waste record.'));
        }
        
        // Get food items from user's restaurant
        $foodItems = FoodItem::whereHas('menu.restaurant', function($query) use ($restaurant) {
            $query->where('id', $restaurant->id);
        })->with('menu.restaurant')->get();
        
        // Common waste reasons
        $wasteReasons = [
            'Food left over after meal time',
            'Expired ingredients',
            'Damaged during preparation',
            'Customer dissatisfaction',
            'Over-preparation',
            'Equipment malfunction',
            'End of day closure',
            'Special event cancelled',
            'Customer allergy',
            'Tasting during cooking'
        ];
        
        return view('waste-records.edit', compact('wasteRecord', 'foodItems', 'wasteReasons'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, WasteRecord $wasteRecord)
    {
        // Verify ownership
        $user = Auth::user();
        $restaurant = $user->restaurant;
        
        if (!$restaurant || $wasteRecord->restaurant_id !== $restaurant->id) {
            abort(403, __('messages.Unauthorized access to this waste record.'));
        }
        
        $request->validate([
            'food_item_id' => 'required|exists:food_items,id',
            'waste_date' => 'required|date',
            'quantity_wasted' => 'required|numeric|min:0.01',
            'waste_unit' => 'required|string|max:50',
            'cost_wasted' => 'required|numeric|min:0',
            'waste_reason' => 'required|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);
        
        // Verify the food item belongs to user's restaurant
        $foodItem = FoodItem::whereHas('menu.restaurant', function($query) use ($restaurant) {
            $query->where('id', $restaurant->id);
        })->findOrFail($request->food_item_id);
        
        // Recalculate AI fields when updating
        $aiFields = $this->calculateAIFields($foodItem, $request);
        
        $wasteRecord->update([
            'food_item_id' => $request->food_item_id,
            'waste_date' => $request->waste_date,
            'quantity_wasted' => $request->quantity_wasted,
            'waste_unit' => $request->waste_unit,
            'cost_wasted' => $request->cost_wasted,
            'waste_reason' => $request->waste_reason,
            'notes' => $request->notes,
            'ai_predicted_waste' => $aiFields['ai_predicted_waste'],
            'actual_waste_percentage' => $aiFields['actual_waste_percentage'],
            'prediction_accuracy' => $aiFields['prediction_accuracy'],
            'ai_insights' => $aiFields['ai_insights'],
        ]);
        
        return redirect()->route('waste-records.index')
            ->with('success', __('messages.Waste record updated successfully!'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(WasteRecord $wasteRecord)
    {
        // Verify ownership
        $user = Auth::user();
        $restaurant = $user->restaurant;
        
        if (!$restaurant || $wasteRecord->restaurant_id !== $restaurant->id) {
            abort(403, __('messages.Unauthorized access to this waste record.'));
        }
        
        $wasteRecord->delete();
        
        return redirect()->route('waste-records.index')
            ->with('success', __('messages.Waste record deleted successfully!'));
    }

    /**
     * Calculate AI fields for waste record
     */
    private function calculateAIFields(FoodItem $foodItem, Request $request)
    {
        try {
            // 1. Get AI predicted waste from food item
            $aiPredictedWaste = $foodItem->ai_waste_prediction ?? 0;
            
            // 2. Calculate actual waste percentage
            // Use stock quantity as total prepared amount, or get from recent orders
            $totalPrepared = $this->getTotalPreparedAmount($foodItem, $request->waste_date);
            $actualWastePercentage = 0;
            if ($totalPrepared > 0) {
                $actualWastePercentage = ($request->quantity_wasted / $totalPrepared) * 100;
            }
            
            // 3. Calculate prediction accuracy
            $predictionAccuracy = 0;
            if ($aiPredictedWaste > 0 && $request->quantity_wasted > 0) {
                $accuracy = 100 - (abs($aiPredictedWaste - $request->quantity_wasted) / $request->quantity_wasted * 100);
                $predictionAccuracy = max(0, min(100, $accuracy)); // Clamp between 0-100
            }
            
            // 4. Generate simple insights
            $aiInsights = $this->getSimpleInsights($request->waste_reason, $request->quantity_wasted, $request->cost_wasted);
            
            return [
                'ai_predicted_waste' => $aiPredictedWaste,
                'actual_waste_percentage' => round($actualWastePercentage, 2),
                'prediction_accuracy' => round($predictionAccuracy, 2),
                'ai_insights' => $aiInsights,
            ];
            
        } catch (\Exception $e) {
            Log::error('Error calculating AI fields: ' . $e->getMessage());
            
            // Return default values if calculation fails
            return [
                'ai_predicted_waste' => 0,
                'actual_waste_percentage' => 0,
                'prediction_accuracy' => 0,
                'ai_insights' => [],
            ];
        }
    }

    /**
     * Get total prepared amount for a food item on a specific date
     */
    private function getTotalPreparedAmount(FoodItem $foodItem, $wasteDate)
    {
        try {
            // Try to get from recent orders first
            $totalSold = $foodItem->orderItems()
                ->whereHas('order', function($query) use ($wasteDate) {
                    $query->whereDate('order_date', $wasteDate);
                })
                ->sum('quantity_sold');
            
            // If no orders found, use stock quantity as fallback
            if ($totalSold == 0) {
                $totalSold = $foodItem->stock_quantity;
            }
            
            // Add some buffer for preparation (assume 20% more prepared than sold)
            return $totalSold * 1.2;
            
        } catch (\Exception $e) {
            Log::error('Error getting total prepared amount: ' . $e->getMessage());
            return $foodItem->stock_quantity;
        }
    }

    /**
     * Generate simple insights for waste record
     */
    private function getSimpleInsights($wasteReason, $quantityWasted, $costWasted)
    {
        $insights = [];
        
        // Base insights based on waste reason
        switch ($wasteReason) {
            case 'Thức ăn thừa sau giờ ăn':
                $insights[] = 'Giảm khẩu phần ăn';
                $insights[] = 'Theo dõi thời gian bán hàng';
                $insights[] = 'Điều chỉnh số lượng chuẩn bị';
                break;
                
            case 'Hết hạn sử dụng':
                $insights[] = 'Quản lý tồn kho tốt hơn';
                $insights[] = 'Sử dụng nguyên liệu cũ trước';
                $insights[] = 'Kiểm tra hạn sử dụng thường xuyên';
                break;
                
            case 'Làm hỏng trong quá trình chế biến':
                $insights[] = 'Cải thiện kỹ thuật nấu';
                $insights[] = 'Theo dõi thời gian nấu';
                $insights[] = 'Đào tạo nhân viên';
                break;
                
            case 'Khách hàng không hài lòng':
                $insights[] = 'Cải thiện chất lượng món ăn';
                $insights[] = 'Kiểm tra khẩu vị khách hàng';
                $insights[] = 'Điều chỉnh công thức';
                break;
                
            case 'Chuẩn bị quá nhiều':
                $insights[] = 'Dự báo nhu cầu chính xác hơn';
                $insights[] = 'Chuẩn bị theo đơn đặt hàng';
                $insights[] = 'Theo dõi xu hướng bán hàng';
                break;
                
            default:
                $insights[] = 'Theo dõi nguyên nhân lãng phí';
                $insights[] = 'Cải thiện quy trình quản lý';
                $insights[] = 'Tối ưu hóa chi phí';
        }
        
        // Add cost-based insights
        if ($costWasted > 100000) { // > 100k VNĐ
            $insights[] = 'Lãng phí chi phí cao - cần ưu tiên xử lý';
        } elseif ($costWasted > 50000) { // > 50k VNĐ
            $insights[] = 'Lãng phí chi phí trung bình - cần theo dõi';
        }
        
        // Add quantity-based insights
        if ($quantityWasted > 500) { // > 500g
            $insights[] = 'Số lượng lãng phí lớn - cần điều chỉnh khẩu phần';
        }
        
        return $insights;
    }


    /**
     * Get fallback insights when calculation fails
     */
    private function getFallbackInsights($wasteReason)
    {
        return [
            'Theo dõi nguyên nhân lãng phí',
            'Cải thiện quy trình quản lý',
            'Tối ưu hóa chi phí'
        ];
    }

    /**
     * Test method to check AI fields calculation
     */
    public function testAIFields()
    {
        try {
            // Get a sample food item
            $user = Auth::user();
            $restaurant = $user->restaurant;
            
            if (!$restaurant) {
                return response()->json(['error' => 'No restaurant found'], 404);
            }
            
            $foodItem = FoodItem::whereHas('menu.restaurant', function($query) use ($restaurant) {
                $query->where('id', $restaurant->id);
            })->first();
            
            if (!$foodItem) {
                return response()->json(['error' => 'No food items found'], 404);
            }
            
            // Create a test request
            $testRequest = new Request([
                'food_item_id' => $foodItem->id,
                'waste_date' => now()->format('Y-m-d'),
                'quantity_wasted' => 100,
                'waste_unit' => 'grams',
                'cost_wasted' => 50000,
                'waste_reason' => 'Thức ăn thừa sau giờ ăn',
                'notes' => 'Test AI fields calculation'
            ]);
            
            // Calculate AI fields
            $aiFields = $this->calculateAIFields($foodItem, $testRequest);
            
            return response()->json([
                'success' => true,
                'food_item' => $foodItem->name,
                'ai_fields' => $aiFields,
                'ai_insights_raw' => $aiFields['ai_insights'],
                'ai_insights_json' => json_encode($aiFields['ai_insights'], JSON_UNESCAPED_UNICODE),
                'message' => 'AI fields calculation test successful'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }
}