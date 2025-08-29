<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'food_item_id',
        'quantity_sold',
        'unit_price',
        'total_price',
        'notes'
    ];

    protected $casts = [
        'quantity_sold' => 'integer',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    /**
     * Get the order that owns the order item.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the food item associated with the order item.
     */
    public function foodItem(): BelongsTo
    {
        return $this->belongsTo(FoodItem::class);
    }

    /**
     * Get formatted unit price.
     */
    public function getFormattedUnitPriceAttribute()
    {
        return '¥' . number_format($this->unit_price, 2);
    }

    /**
     * Get formatted total price.
     */
    public function getFormattedTotalPriceAttribute()
    {
        return '¥' . number_format($this->total_price, 2);
    }

    /**
     * Calculate total price automatically.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($orderItem) {
            if (!$orderItem->total_price) {
                $orderItem->total_price = $orderItem->quantity_sold * $orderItem->unit_price;
            }
        });

        static::updating(function ($orderItem) {
            if ($orderItem->isDirty('quantity_sold') || $orderItem->isDirty('unit_price')) {
                $orderItem->total_price = $orderItem->quantity_sold * $orderItem->unit_price;
            }
        });
    }
}
