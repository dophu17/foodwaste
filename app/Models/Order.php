<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Order extends Model
{
    protected $fillable = [
        'restaurant_id',
        'order_date',
        'total_amount',
        'customer_count',
        'weather_condition',
        'day_of_week',
        'is_holiday',
        'special_event',
        'status',
        'notes'
    ];

    protected $casts = [
        'order_date' => 'date',
        'total_amount' => 'decimal:2',
        'customer_count' => 'integer',
        'is_holiday' => 'boolean',
    ];

    /**
     * Get the restaurant that owns the order.
     */
    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    /**
     * Get the order items for the order.
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Scope to get orders for a specific date range.
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('order_date', [$startDate, $endDate]);
    }

    /**
     * Scope to get orders by day of week.
     */
    public function scopeByDayOfWeek($query, $dayOfWeek)
    {
        return $query->where('day_of_week', $dayOfWeek);
    }

    /**
     * Get formatted total amount.
     */
    public function getFormattedTotalAmountAttribute()
    {
        return '¥' . number_format($this->total_amount, 2);
    }

    /**
     * Get day of week name.
     */
    public function getDayOfWeekNameAttribute()
    {
        return Carbon::parse($this->order_date)->format('l');
    }

    /**
     * Calculate average order value.
     */
    public function getAverageOrderValueAttribute()
    {
        return $this->customer_count > 0 ? $this->total_amount / $this->customer_count : 0;
    }

    /**
     * Get daily sales trend for AI analysis
     */
    public static function getDailySalesTrend($restaurantId, $days = 14)
    {
        $startDate = Carbon::now()->subDays($days);
        
        return self::where('restaurant_id', $restaurantId)
            ->where('order_date', '>=', $startDate)
            ->selectRaw('
                DATE(order_date) as date,
                COUNT(*) as total_orders,
                SUM(total_amount) as daily_revenue,
                SUM(customer_count) as total_customers,
                AVG(total_amount) as avg_order_value
            ')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    /**
     * Get weekly sales pattern for AI analysis
     */
    public static function getWeeklySalesPattern($restaurantId, $weeks = 4)
    {
        $startDate = Carbon::now()->subWeeks($weeks);
        
        return self::where('restaurant_id', $restaurantId)
            ->where('order_date', '>=', $startDate)
            ->selectRaw('
                day_of_week,
                COUNT(*) as total_orders,
                SUM(total_amount) as total_revenue,
                AVG(total_amount) as avg_order_value,
                SUM(customer_count) as total_customers
            ')
            ->groupBy('day_of_week')
            ->orderByRaw('
                CASE day_of_week 
                    WHEN "Monday" THEN 1 
                    WHEN "Tuesday" THEN 2 
                    WHEN "Wednesday" THEN 3 
                    WHEN "Thursday" THEN 4 
                    WHEN "Friday" THEN 5 
                    WHEN "Saturday" THEN 6 
                    WHEN "Sunday" THEN 7 
                END
            ')
            ->get();
    }

    /**
     * Get weather impact analysis for AI
     */
    public static function getWeatherImpactAnalysis($restaurantId, $days = 30)
    {
        $startDate = Carbon::now()->subDays($days);
        
        return self::where('restaurant_id', $restaurantId)
            ->where('order_date', '>=', $startDate)
            ->whereNotNull('weather_condition')
            ->selectRaw('
                weather_condition,
                COUNT(*) as total_orders,
                SUM(total_amount) as total_revenue,
                AVG(total_amount) as avg_order_value,
                AVG(customer_count) as avg_customers
            ')
            ->groupBy('weather_condition')
            ->get();
    }

    /**
     * Get holiday and special event impact for AI
     */
    public static function getEventImpactAnalysis($restaurantId, $days = 90)
    {
        $startDate = Carbon::now()->subDays($days);
        
        return self::where('restaurant_id', $restaurantId)
            ->where('order_date', '>=', $startDate)
            ->where(function($query) {
                $query->where('is_holiday', true)
                      ->orWhereNotNull('special_event');
            })
            ->selectRaw('
                DATE(order_date) as date,
                is_holiday,
                special_event,
                COUNT(*) as total_orders,
                SUM(total_amount) as total_revenue,
                SUM(customer_count) as total_customers
            ')
            ->groupBy('date', 'is_holiday', 'special_event')
            ->orderBy('date')
            ->get();
    }
}
