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
}
