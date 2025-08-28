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
