<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WasteRecord extends Model
{
    protected $fillable = [
        'restaurant_id',
        'food_item_id',
        'waste_date',
        'quantity_wasted',
        'waste_unit',
        'cost_wasted',
        'waste_reason',
        'notes',
        'ai_predicted_waste',
        'actual_waste_percentage',
        'prediction_accuracy',
        'ai_insights',
    ];

    protected $casts = [
        'waste_date' => 'date',
        'quantity_wasted' => 'decimal:2',
        'cost_wasted' => 'decimal:2',
        'ai_predicted_waste' => 'decimal:2',
        'actual_waste_percentage' => 'decimal:2',
        'prediction_accuracy' => 'decimal:2',
        'ai_insights' => 'array',
    ];

    /**
     * Get the restaurant that owns the waste record.
     */
    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    /**
     * Get the food item associated with the waste record.
     */
    public function foodItem(): BelongsTo
    {
        return $this->belongsTo(FoodItem::class);
    }

    /**
     * Scope to get waste records for a specific date range.
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('waste_date', [$startDate, $endDate]);
    }

    /**
     * Scope to get waste records by reason.
     */
    public function scopeByReason($query, $reason)
    {
        return $query->where('waste_reason', $reason);
    }

    /**
     * Get formatted waste cost.
     */
    public function getFormattedWasteCostAttribute()
    {
        return '¥' . number_format($this->cost_wasted, 2);
    }

    /**
     * Get formatted quantity wasted.
     */
    public function getFormattedQuantityAttribute()
    {
        return number_format($this->quantity_wasted, 2) . ' ' . $this->waste_unit;
    }

    /**
     * Get AI insights as formatted text.
     */
    public function getFormattedAiInsightsAttribute()
    {
        if (!$this->ai_insights) {
            return 'No AI insights available';
        }

        return implode(', ', $this->ai_insights);
    }
}
