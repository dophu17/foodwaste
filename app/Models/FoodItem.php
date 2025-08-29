<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Carbon\Carbon;

class FoodItem extends Model
{
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
            return 'No AI prediction available';
        }

        if ($this->ai_waste_prediction > 20) {
            return 'High waste risk - consider reducing portion sizes or adjusting menu';
        } elseif ($this->ai_waste_prediction > 10) {
            return 'Moderate waste risk - monitor closely';
        } else {
            return 'Low waste risk - good management';
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
        return number_format($this->price) . ' VNĐ';
    }

    /**
     * Get stock status attribute.
     */
    public function getStockStatusAttribute(): string
    {
        if ($this->stock_quantity <= 0) {
            return 'Hết hàng';
        } elseif ($this->isStockLow()) {
            return 'Sắp hết';
        } else {
            return 'Còn hàng';
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
}
