<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class FoodItem extends Model
{
    /**
     * The "booted" method of the model.
     */
    protected static function booted()
    {
        // Auto-calculate AI waste prediction when creating a new food item
        static::creating(function ($foodItem) {
            $foodItem->autoCalculateWastePrediction();
        });

        // Auto-calculate AI waste prediction when updating a food item
        static::updating(function ($foodItem) {
            // Only recalculate if relevant fields have changed
            if ($foodItem->isDirty(['price', 'preparation_time', 'category', 'stock_quantity', 'min_stock_level'])) {
                $foodItem->autoCalculateWastePrediction();
            }
        });
    }

    protected $fillable = [
        'menu_id',
        'name',
        'description',
        'price',
        'category',
        'cuisine_style',
        'ingredients',
        'allergens',
        'preparation_time',
        'image_path',
        'is_available',
        'stock_quantity',
        'min_stock_level',
        'ai_waste_prediction',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_available' => 'boolean',
        'stock_quantity' => 'integer',
        'min_stock_level' => 'integer',
        'ai_waste_prediction' => 'decimal:2',
    ];

    /**
     * Get the menu that owns the food item.
     */
    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    /**
     * Get the waste records for the food item.
     */
    public function wasteRecords(): HasMany
    {
        return $this->hasMany(WasteRecord::class);
    }

    /**
     * Get the order items for the food item.
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get total quantity sold for a specific period.
     */
    public function getTotalQuantitySold($startDate = null, $endDate = null)
    {
        $query = $this->orderItems()->whereHas('order', function($q) use ($startDate, $endDate) {
            if ($startDate) {
                $q->where('order_date', '>=', $startDate);
            }
            if ($endDate) {
                $q->where('order_date', '<=', $endDate);
            }
        });
        
        return $query->sum('quantity_sold');
    }

    /**
     * Get average daily sales for a specific period.
     */
    public function getAverageDailySales($startDate = null, $endDate = null)
    {
        $totalSold = $this->getTotalQuantitySold($startDate, $endDate);
        $days = $startDate && $endDate ? $startDate->diffInDays($endDate) + 1 : 30;
        
        return $days > 0 ? $totalSold / $days : 0;
    }

    /**
     * Get the restaurant through the menu.
     */
    public function getRestaurantAttribute()
    {
        return $this->menu?->restaurant;
    }

    /**
     * Scope to get only available food items.
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    /**
     * Scope to get food items by category.
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }



    /**
     * Check if stock is low.
     */
    public function isStockLow()
    {
        return $this->stock_quantity <= $this->min_stock_level;
    }

    /**
     * Get AI waste prediction insights.
     */
    public function getWasteInsights()
    {
        if (!$this->ai_waste_prediction) {
            return __('messages.no_ai_prediction_available');
        }

        if ($this->ai_waste_prediction > 20) {
            return __('messages.high_waste_risk');
        } elseif ($this->ai_waste_prediction > 10) {
            return __('messages.moderate_waste_risk');
        } else {
            return __('messages.low_waste_risk');
        }
    }

    /**
     * Get recent sales data for AI prompt engineering (last 7-14 days)
     */
    public function getRecentSalesData($days = 14)
    {
        $startDate = Carbon::now()->subDays($days);
        
        return $this->orderItems()
            ->whereHas('order', function($query) use ($startDate) {
                $query->where('order_date', '>=', $startDate);
            })
            ->selectRaw('
                DATE(orders.order_date) as date,
                SUM(quantity_sold) as daily_quantity,
                AVG(unit_price) as avg_price,
                COUNT(DISTINCT orders.id) as days_with_sales
            ')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    /**
     * Get sales trend analysis for AI forecasting
     */
    public function getSalesTrendAnalysis($days = 14)
    {
        $startDate = Carbon::now()->subDays($days);
        
        $recentSales = $this->orderItems()
            ->whereHas('order', function($query) use ($startDate) {
                $query->where('order_date', '>=', $startDate);
            })
            ->selectRaw('
                DATE(orders.order_date) as date,
                SUM(quantity_sold) as daily_quantity,
                orders.day_of_week,
                orders.weather_condition,
                orders.is_holiday
            ')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->groupBy('date', 'orders.day_of_week', 'orders.weather_condition', 'orders.is_holiday')
            ->orderBy('date')
            ->get();

        // Calculate trend metrics
        $totalDays = $recentSales->count();
        $totalQuantity = $recentSales->sum('daily_quantity');
        $avgDailyQuantity = $totalDays > 0 ? $totalQuantity / $totalDays : 0;
        
        // Calculate day-of-week patterns
        $dayOfWeekPatterns = $recentSales->groupBy('day_of_week')
            ->map(function($daySales) {
                return $daySales->avg('daily_quantity');
            });

        return [
            'total_days' => $totalDays,
            'total_quantity' => $totalQuantity,
            'avg_daily_quantity' => $avgDailyQuantity,
            'day_of_week_patterns' => $dayOfWeekPatterns,
            'recent_sales' => $recentSales
        ];
    }

    /**
     * Get AI forecasting data for next day prediction
     */
    public function getAIForecastingData($days = 14)
    {
        $trendData = $this->getSalesTrendAnalysis($days);
        
        // Get tomorrow's day of week
        $tomorrow = Carbon::tomorrow();
        $tomorrowDayOfWeek = $tomorrow->format('l');
        
        // Get historical data for same day of week
        $sameDayHistory = $this->orderItems()
            ->whereHas('order', function($query) use ($days, $tomorrowDayOfWeek) {
                $query->where('order_date', '>=', Carbon::now()->subDays($days * 2)) // Look back further for same day patterns
                      ->where('day_of_week', $tomorrowDayOfWeek);
            })
            ->selectRaw('
                DATE(orders.order_date) as date,
                SUM(quantity_sold) as daily_quantity,
                orders.weather_condition,
                orders.is_holiday,
                orders.special_event
            ')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->groupBy('date', 'orders.weather_condition', 'orders.is_holiday', 'orders.special_event')
            ->orderBy('date')
            ->get();

        return [
            'tomorrow_date' => $tomorrow->format('Y-m-d'),
            'tomorrow_day_of_week' => $tomorrowDayOfWeek,
            'trend_data' => $trendData,
            'same_day_history' => $sameDayHistory,
            'forecasting_factors' => [
                'base_demand' => $trendData['avg_daily_quantity'],
                'day_of_week_multiplier' => $trendData['day_of_week_patterns'][$tomorrowDayOfWeek] ?? 1.0,
                'weather_impact' => $this->getWeatherImpactMultiplier(),
                'holiday_impact' => $this->getHolidayImpactMultiplier()
            ]
        ];
    }

    /**
     * Get weather impact multiplier for forecasting
     */
    private function getWeatherImpactMultiplier()
    {
        // This could be enhanced with actual weather API data
        $weatherMultipliers = [
            'Sunny' => 1.1,      // Sunny days typically have higher sales
            'Cloudy' => 1.0,     // Normal sales
            'Rainy' => 0.8,      // Rainy days typically have lower sales
            'Windy' => 0.9       // Slightly lower sales
        ];
        
        return $weatherMultipliers;
    }

    /**
     * Get holiday impact multiplier for forecasting
     */
    private function getHolidayImpactMultiplier()
    {
        // This could be enhanced with actual holiday calendar data
        return [
            'holiday' => 1.3,    // Holidays typically have higher sales
            'normal' => 1.0      // Normal days
        ];
    }

    /**
     * Get formatted price attribute.
     */
    public function getFormattedPriceAttribute(): string
    {
        return number_format($this->price) . __('messages.currency_suffix');
    }

    /**
     * Get stock status attribute.
     */
    public function getStockStatusAttribute(): string
    {
        if ($this->stock_quantity <= 0) {
            return __('messages.out_of_stock');
        } elseif ($this->isStockLow()) {
            return __('messages.low_stock');
        } else {
            return __('messages.in_stock');
        }
    }

    /**
     * Get stock status class for UI.
     */
    public function getStockStatusClassAttribute(): string
    {
        if ($this->stock_quantity <= 0) {
            return 'text-danger';
        } elseif ($this->isStockLow()) {
            return 'text-warning';
        } else {
            return 'text-success';
        }
    }

    /**
     * Check if food item is in stock.
     */
    public function isInStock(): bool
    {
        return $this->stock_quantity > 0;
    }

    /**
     * Get preparation time in minutes.
     */
    public function getPreparationTimeMinutesAttribute(): int
    {
        // Extract number from preparation_time string (e.g., "30 minutes" -> 30)
        preg_match('/(\d+)/', $this->preparation_time, $matches);
        return isset($matches[1]) ? (int) $matches[1] : 0;
    }

    /**
     * Scope to get food items by cuisine style.
     */
    public function scopeByCuisineStyle($query, $cuisineStyle)
    {
        return $query->where('cuisine_style', $cuisineStyle);
    }

    /**
     * Scope to get food items by price range.
     */
    public function scopeByPriceRange($query, $minPrice, $maxPrice)
    {
        return $query->whereBetween('price', [$minPrice, $maxPrice]);
    }

    /**
     * Scope to get food items with low stock.
     */
    public function scopeLowStock($query)
    {
        return $query->whereRaw('stock_quantity <= min_stock_level');
    }

    /**
     * Scope to get food items by preparation time.
     */
    public function scopeByPreparationTime($query, $maxMinutes)
    {
        return $query->whereRaw('CAST(SUBSTRING_INDEX(preparation_time, " ", 1) AS UNSIGNED) <= ?', [$maxMinutes]);
    }

    /**
     * Auto-calculate AI waste prediction (used in model events)
     */
    private function autoCalculateWastePrediction(): void
    {
        try {
            $wastePredictionService = app(\App\Services\WastePredictionService::class);
            
            // Check if all required factors are present
            $factorCheck = $wastePredictionService->hasRequiredFactors($this);
            
            if (!$factorCheck['has_all_factors']) {
                // Set ai_waste_prediction to null if missing required factors
                $this->attributes['ai_waste_prediction'] = null;
                Log::info(__('messages.ai_prediction_skipped', [
                    'id' => $this->id,
                    'missing_factors' => implode(', ', $factorCheck['missing_factors'])
                ]));
                return;
            }
            
            $newPrediction = $wastePredictionService->calculateWastePrediction($this);
            
            // Set the ai_waste_prediction attribute without triggering another update
            $this->attributes['ai_waste_prediction'] = round($newPrediction, 2);
        } catch (\Exception $e) {
            // Log error but don't break the save operation
            Log::warning(__('messages.ai_prediction_failed', ['message' => $e->getMessage()]));
            // Set to null if calculation fails due to missing factors
            $this->attributes['ai_waste_prediction'] = null;
        }
    }

    /**
     * Calculate and update AI waste prediction using WastePredictionService
     */
    public function calculateAndUpdateWastePrediction(): bool
    {
        $wastePredictionService = app(\App\Services\WastePredictionService::class);
        
        // Check if all required factors are present
        $factorCheck = $wastePredictionService->hasRequiredFactors($this);
        
        if (!$factorCheck['has_all_factors']) {
            // Set ai_waste_prediction to null if missing required factors
            $this->update(['ai_waste_prediction' => null]);
            return false;
        }
        
        return $wastePredictionService->updateWastePrediction($this);
    }

    /**
     * Get detailed breakdown of waste prediction calculation
     */
    public function getWastePredictionBreakdown(): array
    {
        $wastePredictionService = app(\App\Services\WastePredictionService::class);
        return $wastePredictionService->getPredictionBreakdown($this);
    }

    /**
     * Get waste prediction with detailed explanation
     */
    public function getWastePredictionExplanation(): string
    {
        $breakdown = $this->getWastePredictionBreakdown();
        
        $explanation = __('messages.ai_waste_prediction', ['prediction' => $breakdown['final_prediction']]) . "\n\n";
        $explanation .= __('messages.detailed_analysis') . "\n";
        $explanation .= __('messages.base_risk', ['risk' => $breakdown['base_risk']]) . "\n";
        $explanation .= __('messages.price_factor', ['factor' => $breakdown['price_factor'], 'price' => number_format($this->price)]) . "\n";
        $explanation .= __('messages.prep_time_factor', ['factor' => $breakdown['prep_time_factor'], 'prep_time' => $this->preparation_time]) . "\n";
        $explanation .= __('messages.category_factor', ['factor' => $breakdown['category_factor'], 'category' => $this->category]) . "\n";
        $explanation .= __('messages.stock_factor', ['factor' => $breakdown['stock_factor'], 'current' => $this->stock_quantity, 'min' => $this->min_stock_level]) . "\n";
        $explanation .= __('messages.historical_factor', ['factor' => $breakdown['historical_factor']]) . "\n";
        $explanation .= __('messages.sales_factor', ['factor' => $breakdown['sales_factor']]) . "\n";
        $explanation .= __('messages.seasonal_factor', ['factor' => $breakdown['seasonal_factor'], 'month' => Carbon::now()->month]) . "\n\n";
        $explanation .= __('messages.formula', ['calculation' => $breakdown['calculation']]);
        
        return $explanation;
    }
}
