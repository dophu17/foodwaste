<?php

namespace App\Services;

use App\Models\FoodItem;
use App\Models\WasteRecord;
use App\Models\OrderItem;
use Carbon\Carbon;

class WastePredictionService
{
    /**
     * Check if food item has all required factors for AI prediction
     */
    public function hasRequiredFactors(FoodItem $foodItem): array
    {
        $missingFactors = [];
        
        // Check required factors
        if (empty($foodItem->category)) {
            $missingFactors[] = 'category';
        }
        
        if (empty($foodItem->preparation_time)) {
            $missingFactors[] = 'preparation_time';
        }
        
        if (empty($foodItem->price) || $foodItem->price <= 0) {
            $missingFactors[] = 'price';
        }
        
        if ($foodItem->stock_quantity < 0) {
            $missingFactors[] = 'stock_quantity';
        }
        
        if ($foodItem->min_stock_level < 0) {
            $missingFactors[] = 'min_stock_level';
        }
        
        return [
            'has_all_factors' => empty($missingFactors),
            'missing_factors' => $missingFactors
        ];
    }

    /**
     * Calculate AI waste prediction for a food item
     */
    public function calculateWastePrediction(FoodItem $foodItem): float
    {
        // Check if all required factors are present
        $factorCheck = $this->hasRequiredFactors($foodItem);
        
        if (!$factorCheck['has_all_factors']) {
            // Return null or throw exception to indicate missing factors
            throw new \Exception('Missing required factors: ' . implode(', ', $factorCheck['missing_factors']));
        }
        
        // Base risk percentage
        $baseRisk = 5.0;
        
        // Factor 1: Price Factor (higher price = higher risk)
        $priceFactor = $this->calculatePriceFactor($foodItem->price);
        
        // Factor 2: Preparation Time Factor (longer prep = higher risk)
        $prepTimeFactor = $this->calculatePrepTimeFactor($foodItem->preparation_time);
        
        // Factor 3: Category Factor (different categories have different waste patterns)
        $categoryFactor = $this->calculateCategoryFactor($foodItem->category);
        
        // Factor 4: Stock Level Factor (low stock = higher risk)
        $stockFactor = $this->calculateStockFactor($foodItem->stock_quantity, $foodItem->min_stock_level);
        
        // Factor 5: Historical Waste Factor (based on past waste records)
        $historicalFactor = $this->calculateHistoricalFactor($foodItem);
        
        // Factor 6: Sales Volume Factor (low sales = higher waste risk)
        $salesFactor = $this->calculateSalesFactor($foodItem);
        
        // Factor 7: Seasonal Factor (time-based adjustments)
        $seasonalFactor = $this->calculateSeasonalFactor();
        
        // Calculate final prediction
        $prediction = $baseRisk * $priceFactor * $prepTimeFactor * $categoryFactor * 
                     $stockFactor * $historicalFactor * $salesFactor * $seasonalFactor;
        
        // Apply bounds (minimum 1%, maximum 50%)
        return max(1.0, min($prediction, 50.0));
    }
    
    /**
     * Calculate price factor
     */
    private function calculatePriceFactor(float $price): float
    {
        if ($price >= 500000) return 2.5;      // Very expensive items
        if ($price >= 300000) return 2.0;      // Expensive items
        if ($price >= 150000) return 1.5;      // Moderate price
        if ($price >= 80000) return 1.2;       // Average price
        return 1.0;                            // Low price
    }
    
    /**
     * Calculate preparation time factor
     */
    private function calculatePrepTimeFactor(string $prepTime): float
    {
        // Extract minutes from preparation time string
        preg_match('/(\d+)/', $prepTime, $matches);
        $minutes = isset($matches[1]) ? (int) $matches[1] : 10;
        
        if ($minutes >= 30) return 2.0;        // Very long preparation
        if ($minutes >= 20) return 1.5;        // Long preparation
        if ($minutes >= 15) return 1.2;        // Moderate preparation
        return 1.0;                            // Quick preparation
    }
    
    /**
     * Calculate category factor based on typical waste patterns
     */
    private function calculateCategoryFactor(?string $category): float
    {
        if (empty($category)) {
            return 1.0; // Default factor for null/empty category
        }
        
        return match($category) {
            'Dessert' => 1.8,                  // Desserts often have higher waste
            'Main Course' => 1.3,              // Main courses moderate waste
            'Appetizer' => 1.1,                // Appetizers lower waste
            'Beverage' => 0.7,                 // Beverages lowest waste
            'Soup' => 1.4,                     // Soups moderate-high waste
            'Salad' => 1.6,                    // Salads higher waste (fresh ingredients)
            default => 1.0                     // Default factor
        };
    }
    
    /**
     * Calculate stock level factor
     */
    private function calculateStockFactor(int $currentStock, int $minStock): float
    {
        if ($currentStock == 0) return 1.8;                    // Out of stock
        if ($currentStock <= $minStock) return 1.5;            // Low stock
        if ($currentStock <= $minStock * 1.5) return 1.2;      // Below optimal
        return 1.0;                                            // Good stock level
    }
    
    /**
     * Calculate historical waste factor based on past waste records
     */
    private function calculateHistoricalFactor(FoodItem $foodItem): float
    {
        $last30Days = Carbon::now()->subDays(30);
        
        // Get waste records for this food item in last 30 days
        $wasteRecords = WasteRecord::where('food_item_id', $foodItem->id)
            ->where('waste_date', '>=', $last30Days)
            ->get();
        
        if ($wasteRecords->isEmpty()) {
            return 1.0; // No historical data, use neutral factor
        }
        
        // Calculate average waste percentage
        $totalWasteCost = $wasteRecords->sum('cost_wasted');
        $totalRevenue = $foodItem->getTotalQuantitySold($last30Days) * $foodItem->price;
        
        if ($totalRevenue == 0) {
            return 1.2; // No sales but has waste = high risk
        }
        
        $wastePercentage = ($totalWasteCost / $totalRevenue) * 100;
        
        // Convert waste percentage to factor
        if ($wastePercentage >= 20) return 1.8;      // Very high waste
        if ($wastePercentage >= 15) return 1.5;      // High waste
        if ($wastePercentage >= 10) return 1.2;      // Moderate waste
        if ($wastePercentage >= 5) return 1.0;       // Normal waste
        return 0.8;                                   // Low waste (good management)
    }
    
    /**
     * Calculate sales volume factor
     */
    private function calculateSalesFactor(FoodItem $foodItem): float
    {
        $last14Days = Carbon::now()->subDays(14);
        $totalSold = $foodItem->getTotalQuantitySold($last14Days);
        
        // Calculate average daily sales
        $avgDailySales = $totalSold / 14;
        
        if ($avgDailySales >= 10) return 0.8;        // High sales = lower waste risk
        if ($avgDailySales >= 5) return 1.0;         // Good sales
        if ($avgDailySales >= 2) return 1.2;         // Low sales
        if ($avgDailySales >= 1) return 1.4;         // Very low sales
        return 1.6;                                   // Almost no sales = high waste risk
    }
    
    /**
     * Calculate seasonal factor based on current time
     */
    private function calculateSeasonalFactor(): float
    {
        $month = Carbon::now()->month;
        
        // Seasonal adjustments based on typical restaurant patterns
        return match($month) {
            1, 2 => 1.1,    // January-February: Post-holiday, slightly higher waste
            3, 4 => 1.0,    // March-April: Normal period
            5, 6 => 0.9,    // May-June: Good season, lower waste
            7, 8 => 1.0,    // July-August: Summer, normal
            9, 10 => 1.1,   // September-October: Back to school, slightly higher
            11, 12 => 1.2,  // November-December: Holiday season, higher waste
            default => 1.0
        };
    }
    
    /**
     * Update waste prediction for a specific food item
     */
    public function updateWastePrediction(FoodItem $foodItem): bool
    {
        try {
            $newPrediction = $this->calculateWastePrediction($foodItem);
            
            $foodItem->update([
                'ai_waste_prediction' => round($newPrediction, 2)
            ]);
            
            return true;
        } catch (\Exception $e) {
            \Log::error("Failed to update waste prediction for food item {$foodItem->id}: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update waste predictions for all food items
     */
    public function updateAllWastePredictions(): array
    {
        $results = [
            'success' => 0,
            'failed' => 0,
            'total' => 0
        ];
        
        $foodItems = FoodItem::all();
        $results['total'] = $foodItems->count();
        
        foreach ($foodItems as $foodItem) {
            if ($this->updateWastePrediction($foodItem)) {
                $results['success']++;
            } else {
                $results['failed']++;
            }
        }
        
        return $results;
    }
    
    /**
     * Get detailed breakdown of waste prediction calculation
     */
    public function getPredictionBreakdown(FoodItem $foodItem): array
    {
        $baseRisk = 5.0;
        $priceFactor = $this->calculatePriceFactor($foodItem->price);
        $prepTimeFactor = $this->calculatePrepTimeFactor($foodItem->preparation_time);
        $categoryFactor = $this->calculateCategoryFactor($foodItem->category);
        $stockFactor = $this->calculateStockFactor($foodItem->stock_quantity, $foodItem->min_stock_level);
        $historicalFactor = $this->calculateHistoricalFactor($foodItem);
        $salesFactor = $this->calculateSalesFactor($foodItem);
        $seasonalFactor = $this->calculateSeasonalFactor();
        
        $finalPrediction = $baseRisk * $priceFactor * $prepTimeFactor * $categoryFactor * 
                          $stockFactor * $historicalFactor * $salesFactor * $seasonalFactor;
        
        return [
            'base_risk' => $baseRisk,
            'price_factor' => $priceFactor,
            'prep_time_factor' => $prepTimeFactor,
            'category_factor' => $categoryFactor,
            'stock_factor' => $stockFactor,
            'historical_factor' => $historicalFactor,
            'sales_factor' => $salesFactor,
            'seasonal_factor' => $seasonalFactor,
            'final_prediction' => round(max(1.0, min($finalPrediction, 50.0)), 2),
            'calculation' => "{$baseRisk} × {$priceFactor} × {$prepTimeFactor} × {$categoryFactor} × {$stockFactor} × {$historicalFactor} × {$salesFactor} × {$seasonalFactor} = " . round($finalPrediction, 2)
        ];
    }
}
