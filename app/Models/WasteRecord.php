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
     * Get AI insights as array (decode JSON if needed)
     */
    public function getAiInsightsAttribute($value)
    {
        if (is_string($value)) {
            return json_decode($value, true) ?: [];
        }
        return $value ?: [];
    }

    /**
     * Set AI insights (encode as JSON with UTF-8)
     */
    public function setAiInsightsAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['ai_insights'] = json_encode($value, JSON_UNESCAPED_UNICODE);
        } else {
            $this->attributes['ai_insights'] = $value;
        }
    }


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
        return number_format($this->cost_wasted) . ' VNĐ';
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
        $insights = $this->ai_insights;
        
        if (!$insights || !is_array($insights) || empty($insights)) {
            return 'No AI insights available';
        }

        return implode(', ', $insights);
    }
}
