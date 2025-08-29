<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Restaurant extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'address',
        'phone',
        'email',
        'description',
        'cuisine_type',
        'business_hours',
        'capacity',
        'status',
    ];

    protected $casts = [
        'capacity' => 'integer',
        'cuisine_type' => 'string',
    ];

    /**
     * Get the user that owns the restaurant.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the menus for the restaurant.
     */
    public function menus(): HasMany
    {
        return $this->hasMany(Menu::class);
    }

    /**
     * Get the waste records for the restaurant.
     */
    public function wasteRecords(): HasMany
    {
        return $this->hasMany(WasteRecord::class);
    }

    /**
     * Get all food items across all menus for this restaurant.
     */
    public function foodItems()
    {
        return $this->hasManyThrough(FoodItem::class, Menu::class);
    }

    /**
     * Get the orders for the restaurant.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get total revenue for a specific period.
     */
    public function getTotalRevenue($startDate = null, $endDate = null)
    {
        $query = $this->orders();
        
        if ($startDate) {
            $query->where('order_date', '>=', $startDate);
        }
        
        if ($endDate) {
            $query->where('order_date', '<=', $endDate);
        }
        
        $revenue = $query->sum('total_amount');
        return $revenue ?: 0; // Return 0 if null
    }

    /**
     * Get total customers for a specific period.
     */
    public function getTotalCustomers($startDate = null, $endDate = null)
    {
        $query = $this->orders();
        
        if ($startDate) {
            $query->where('order_date', '>=', $startDate);
        }
        
        if ($endDate) {
            $query->where('order_date', '<=', $endDate);
        }
        
        $customers = $query->sum('customer_count');
        return $customers ?: 0; // Return 0 if null
    }

    /**
     * Calculate total waste cost for a specific period.
     */
    public function getTotalWasteCost($startDate = null, $endDate = null)
    {
        $query = $this->wasteRecords();
        
        if ($startDate) {
            $query->where('waste_date', '>=', $startDate);
        }
        
        if ($endDate) {
            $query->where('waste_date', '<=', $endDate);
        }
        
        return $query->sum('cost_wasted');
    }

    /**
     * Get AI waste prediction accuracy.
     */
    public function getWastePredictionAccuracy()
    {
        $records = $this->wasteRecords()
            ->whereNotNull('ai_predicted_waste')
            ->whereNotNull('actual_waste_percentage');
        
        if ($records->count() === 0) {
            return 0;
        }
        
        $totalAccuracy = $records->sum('prediction_accuracy');
        return $totalAccuracy / $records->count();
    }
}
