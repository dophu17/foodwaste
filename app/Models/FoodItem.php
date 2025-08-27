<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        'is_vegetarian',
        'is_vegan',
        'is_gluten_free',
        'image_path',
        'is_available',
        'stock_quantity',
        'min_stock_level',
        'ai_waste_prediction',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_vegetarian' => 'boolean',
        'is_vegan' => 'boolean',
        'is_gluten_free' => 'boolean',
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
     * Get the restaurant through the menu.
     */
    public function restaurant()
    {
        return $this->menu->restaurant;
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
     * Scope to get vegetarian food items.
     */
    public function scopeVegetarian($query)
    {
        return $query->where('is_vegetarian', true);
    }

    /**
     * Scope to get vegan food items.
     */
    public function scopeVegan($query)
    {
        return $query->where('is_vegan', true);
    }

    /**
     * Scope to get gluten-free food items.
     */
    public function scopeGlutenFree($query)
    {
        return $query->where('is_gluten_free', true);
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
}
