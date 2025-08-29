<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Menu extends Model
{
    protected $fillable = [
        'restaurant_id',
        'name',
        'description',
        'category',
        'is_active',
        'valid_from',
        'valid_until',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'valid_from' => 'date',
        'valid_until' => 'date',
    ];

    protected $appends = [
        'status_text',
        'is_currently_valid',
        'days_remaining'
    ];

    /**
     * Get the restaurant that owns the menu.
     */
    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    /**
     * Get the food items for the menu.
     */
    public function foodItems(): HasMany
    {
        return $this->hasMany(FoodItem::class);
    }

    /**
     * Scope to get only active menus.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get menus by category.
     */
    public function scopeByCategory(Builder $query, string $category): Builder
    {
        if (empty($category)) {
            return $query->whereNull('category');
        }
        return $query->where('category', $category);
    }

    /**
     * Scope to get currently valid menus.
     */
    public function scopeCurrentlyValid(Builder $query): Builder
    {
        $now = Carbon::now();
        return $query->where(function ($q) use ($now) {
            $q->where(function ($subQ) use ($now) {
                $subQ->where('valid_from', '<=', $now)
                      ->where('valid_until', '>=', $now);
            })->orWhere(function ($subQ) use ($now) {
                $subQ->where('valid_from', '<=', $now)
                      ->whereNull('valid_until');
            })->orWhere(function ($subQ) use ($now) {
                $subQ->whereNull('valid_from')
                      ->where('valid_until', '>=', $now);
            })->orWhere(function ($subQ) {
                $subQ->whereNull('valid_from')
                      ->whereNull('valid_until');
            });
        });
    }

    /**
     * Scope to get expired menus.
     */
    public function scopeExpired(Builder $query): Builder
    {
        return $query->where('valid_until', '<', Carbon::now());
    }

    /**
     * Scope to get upcoming menus.
     */
    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('valid_from', '>', Carbon::now());
    }

    /**
     * Scope to get menus by restaurant.
     */
    public function scopeByRestaurant(Builder $query, int $restaurantId): Builder
    {
        return $query->where('restaurant_id', $restaurantId);
    }

    /**
     * Scope to search menus by name or description.
     */
    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%")
              ->orWhere(function ($subQ) use ($search) {
                  $subQ->whereNotNull('category')
                       ->where('category', 'like', "%{$search}%");
              });
        });
    }

    /**
     * Get status text attribute.
     */
    public function getStatusTextAttribute(): string
    {
        if (!$this->is_active) {
            return 'Vô hiệu hóa';
        }

        if ($this->is_currently_valid) {
            return 'Đang hoạt động';
        }

        if ($this->valid_from && $this->valid_from > Carbon::now()) {
            return 'Sắp tới';
        }

        if ($this->valid_until && $this->valid_until < Carbon::now()) {
            return 'Đã hết hạn';
        }

        return 'Đang hoạt động';
    }

    /**
     * Check if menu is currently valid.
     */
    public function getIsCurrentlyValidAttribute(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $now = Carbon::now();
        
        // Nếu không có ngày hiệu lực, menu luôn có hiệu lực
        if (!$this->valid_from && !$this->valid_until) {
            return true;
        }
        
        // Kiểm tra ngày bắt đầu
        if ($this->valid_from && $this->valid_from > $now) {
            return false;
        }
        
        // Kiểm tra ngày kết thúc
        if ($this->valid_until && $this->valid_until < $now) {
            return false;
        }
        
        return true;
    }

    /**
     * Get days remaining until menu expires.
     */
    public function getDaysRemainingAttribute(): ?int
    {
        if (!$this->is_active) {
            return null;
        }

        // Nếu không có ngày kết thúc, menu có hiệu lực mãi mãi
        if (!$this->valid_until) {
            return null;
        }

        $now = Carbon::now();
        if ($this->valid_until < $now) {
            return 0;
        }

        return $this->valid_until->diffInDays($now);
    }

    /**
     * Check if user can edit this menu.
     */
    public function canEditByUser($user): bool
    {
        if (!$user) {
            return false;
        }

        // Chỉ cho phép chủ nhà hàng chỉnh sửa
        return $this->restaurant->user_id === $user->id;
    }

    /**
     * Get formatted price range for the menu.
     */
    public function getPriceRangeAttribute(): string
    {
        $prices = $this->foodItems->pluck('price')->filter();
        
        if ($prices->isEmpty()) {
            return 'Chưa có giá';
        }

        $minPrice = $prices->min();
        $maxPrice = $prices->max();

        if ($minPrice === $maxPrice) {
            return number_format($minPrice) . ' VNĐ';
        }

        return number_format($minPrice) . ' - ' . number_format($maxPrice) . ' VNĐ';
    }

    /**
     * Get total food items count.
     */
    public function getTotalItemsAttribute(): int
    {
        return $this->foodItems->count();
    }

    /**
     * Check if menu has any food items.
     */
    public function hasFoodItems(): bool
    {
        return $this->foodItems()->exists();
    }

    /**
     * Get available food items count.
     */
    public function getAvailableItemsCountAttribute(): int
    {
        return $this->foodItems()->where('is_available', true)->count();
    }

    /**
     * Get food items by category.
     */
    public function getFoodItemsByCategoryAttribute()
    {
        return $this->foodItems()
            ->orderBy('category')
            ->orderBy('name')
            ->get()
            ->groupBy('category');
    }

    /**
     * Get total stock quantity.
     */
    public function getTotalStockAttribute(): int
    {
        return $this->foodItems()->sum('stock_quantity');
    }

    /**
     * Get low stock food items.
     */
    public function getLowStockItemsAttribute()
    {
        return $this->foodItems()
            ->whereRaw('stock_quantity <= min_stock_level')
            ->get();
    }

    /**
     * Check if menu has low stock items.
     */
    public function hasLowStockItems(): bool
    {
        return $this->foodItems()
            ->whereRaw('stock_quantity <= min_stock_level')
            ->exists();
    }

    /**
     * Get vegetarian food items count.
     */
    public function getVegetarianItemsCountAttribute(): int
    {
        return $this->foodItems()
            ->where('is_vegetarian', true)
            ->count();
    }

    /**
     * Get vegan food items count.
     */
    public function getVeganItemsCountAttribute(): int
    {
        return $this->foodItems()
            ->where('is_vegan', true)
            ->count();
    }

    /**
     * Get gluten-free food items count.
     */
    public function getGlutenFreeItemsCountAttribute(): int
    {
        return $this->foodItems()
            ->where('is_gluten_free', true)
            ->count();
    }
}
