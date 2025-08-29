<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Order;
use App\Models\FoodItem;
use App\Models\Restaurant;
use Carbon\Carbon;

class AIServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton('ai.analysis', function ($app) {
            return new class {
                /**
                 * Get comprehensive AI analysis data for a restaurant
                 */
                public function getRestaurantAIAnalysis($restaurantId, $days = 14)
                {
                    $restaurant = Restaurant::find($restaurantId);
                    if (!$restaurant) {
                        return null;
                    }

                    return [
                        'restaurant_info' => [
                            'id' => $restaurant->id,
                            'name' => $restaurant->name,
                            'cuisine_type' => $restaurant->cuisine_type,
                            'analysis_period' => $days . ' days'
                        ],
                        'time_series_data' => $this->getTimeSeriesData($restaurantId, $days),
                        'sales_patterns' => $this->getSalesPatterns($restaurantId, $days),
                        'environmental_factors' => $this->getEnvironmentalFactors($restaurantId, $days),
                        'ai_forecasting_data' => $this->getAIForecastingData($restaurantId, $days)
                    ];
                }

                /**
                 * Get time series data for AI analysis
                 */
                private function getTimeSeriesData($restaurantId, $days)
                {
                    return [
                        'daily_sales_trend' => Order::getDailySalesTrend($restaurantId, $days),
                        'weekly_patterns' => Order::getWeeklySalesPattern($restaurantId, 4),
                        'monthly_comparison' => $this->getMonthlyComparison($restaurantId)
                    ];
                }

                /**
                 * Get sales patterns for AI analysis
                 */
                private function getSalesPatterns($restaurantId, $days)
                {
                    $startDate = Carbon::now()->subDays($days);
                    
                    // Get top selling food items
                    $topSellingItems = FoodItem::whereHas('menu', function($query) use ($restaurantId) {
                        $query->where('restaurant_id', $restaurantId);
                    })
                    ->with(['orderItems' => function($query) use ($startDate) {
                        $query->whereHas('order', function($q) use ($startDate) {
                            $q->where('order_date', '>=', $startDate);
                        });
                    }])
                    ->get()
                    ->map(function($item) {
                        $totalSold = $item->orderItems->sum('quantity_sold');
                        return [
                            'id' => $item->id,
                            'name' => $item->name,
                            'category' => $item->category,
                            'total_quantity_sold' => $totalSold,
                            'avg_daily_sales' => $item->getAverageDailySales($startDate ?? null),
                            'recent_sales_data' => $item->getRecentSalesData(7)
                        ];
                    })
                    ->sortByDesc('total_quantity_sold')
                    ->take(10);

                    return [
                        'top_selling_items' => $topSellingItems,
                        'category_performance' => $this->getCategoryPerformance($restaurantId, $days),
                        'peak_hours_analysis' => $this->getPeakHoursAnalysis($restaurantId, $days)
                    ];
                }

                /**
                 * Get environmental factors for AI analysis
                 */
                private function getEnvironmentalFactors($restaurantId, $days)
                {
                    return [
                        'weather_impact' => Order::getWeatherImpactAnalysis($restaurantId, $days),
                        'holiday_impact' => Order::getEventImpactAnalysis($restaurantId, $days * 2),
                        'seasonal_trends' => $this->getSeasonalTrends($restaurantId)
                    ];
                }

                /**
                 * Get AI forecasting data for next day prediction
                 */
                private function getAIForecastingData($restaurantId, $days)
                {
                    $restaurant = Restaurant::find($restaurantId);
                    $foodItems = $restaurant->foodItems()->with('menu')->get();

                    $forecastingData = [];
                    foreach ($foodItems as $item) {
                        $forecastingData[] = [
                            'food_item_id' => $item->id,
                            'name' => $item->name,
                            'category' => $item->category,
                            'current_stock' => $item->stock_quantity,
                            'min_stock_level' => $item->min_stock_level,
                            'forecast_data' => $item->getAIForecastingData($days),
                            'recommended_preparation' => $this->calculateRecommendedPreparation($item, $days)
                        ];
                    }

                    return [
                        'tomorrow_date' => Carbon::tomorrow()->format('Y-m-d'),
                        'forecast_period' => $days . ' days',
                        'food_items_forecast' => $forecastingData,
                        'overall_demand_prediction' => $this->calculateOverallDemandPrediction($restaurantId, $days)
                    ];
                }

                /**
                 * Calculate recommended preparation quantity for next day
                 */
                private function calculateRecommendedPreparation($foodItem, $days)
                {
                    $forecastData = $foodItem->getAIForecastingData($days);
                    $baseDemand = $forecastData['trend_data']['avg_daily_quantity'] ?? 0;
                    
                    // Apply day-of-week multiplier
                    $dayMultiplier = $forecastData['forecasting_factors']['day_of_week_multiplier'] ?? 1.0;
                    
                    // Apply weather impact (assuming tomorrow's weather - could be enhanced with weather API)
                    $weatherMultiplier = 1.0; // Default, could be enhanced
                    
                    // Apply holiday impact
                    $holidayMultiplier = $forecastData['forecasting_factors']['holiday_impact']['normal'];
                    
                    $recommendedQuantity = round($baseDemand * $dayMultiplier * $weatherMultiplier * $holidayMultiplier);
                    
                    // Add safety buffer (20%)
                    $recommendedQuantity = round($recommendedQuantity * 1.2);
                    
                    return [
                        'base_demand' => $baseDemand,
                        'day_multiplier' => $dayMultiplier,
                        'weather_multiplier' => $weatherMultiplier,
                        'holiday_multiplier' => $holidayMultiplier,
                        'recommended_quantity' => $recommendedQuantity,
                        'safety_buffer' => '20%',
                        'notes' => $this->getPreparationNotes($foodItem, $recommendedQuantity)
                    ];
                }

                /**
                 * Get preparation notes based on forecast
                 */
                private function getPreparationNotes($foodItem, $recommendedQuantity)
                {
                    $currentStock = $foodItem->stock_quantity;
                    $minStock = $foodItem->min_stock_level;
                    
                    if ($recommendedQuantity <= $currentStock) {
                        return "Sufficient stock available. No additional preparation needed.";
                    }
                    
                    $additionalNeeded = $recommendedQuantity - $currentStock;
                    
                    if ($additionalNeeded <= $minStock) {
                        return "Prepare {$additionalNeeded} additional units to meet demand.";
                    }
                    
                    return "High demand expected. Prepare {$additionalNeeded} additional units. Consider increasing stock levels.";
                }

                /**
                 * Calculate overall demand prediction for restaurant
                 */
                private function calculateOverallDemandPrediction($restaurantId, $days)
                {
                    $startDate = Carbon::now()->subDays($days);
                    
                    $recentOrders = Order::where('restaurant_id', $restaurantId)
                        ->where('order_date', '>=', $startDate)
                        ->with('orderItems')
                        ->get();

                    $totalCustomers = $recentOrders->sum('customer_count');
                    $totalOrders = $recentOrders->count();
                    $avgCustomersPerDay = $totalOrders > 0 ? $totalCustomers / $totalOrders : 0;
                    
                    // Get tomorrow's day of week
                    $tomorrow = Carbon::tomorrow();
                    $tomorrowDayOfWeek = $tomorrow->format('l');
                    
                    // Calculate day-of-week multiplier
                    $sameDayOrders = $recentOrders->where('day_of_week', $tomorrowDayOfWeek);
                    $sameDayAvgCustomers = $sameDayOrders->count() > 0 ? $sameDayOrders->avg('customer_count') : $avgCustomersPerDay;
                    $dayMultiplier = $avgCustomersPerDay > 0 ? $sameDayAvgCustomers / $avgCustomersPerDay : 1.0;

                    $predictedCustomers = round($avgCustomersPerDay * $dayMultiplier);
                    
                    return [
                        'avg_customers_per_day' => round($avgCustomersPerDay, 1),
                        'tomorrow_day_of_week' => $tomorrowDayOfWeek,
                        'day_multiplier' => round($dayMultiplier, 2),
                        'predicted_customers_tomorrow' => $predictedCustomers,
                        'confidence_level' => $this->calculateConfidenceLevel($recentOrders->count(), $days)
                    ];
                }

                /**
                 * Calculate confidence level for predictions
                 */
                private function calculateConfidenceLevel($dataPoints, $days)
                {
                    $coverage = $dataPoints / $days;
                    
                    if ($coverage >= 0.8) return 'High';
                    if ($coverage >= 0.5) return 'Medium';
                    return 'Low';
                }

                /**
                 * Get monthly comparison data
                 */
                private function getMonthlyComparison($restaurantId)
                {
                    $currentMonth = Carbon::now()->startOfMonth();
                    $lastMonth = Carbon::now()->subMonth()->startOfMonth();
                    
                    $currentMonthData = Order::where('restaurant_id', $restaurantId)
                        ->whereBetween('order_date', [$currentMonth, Carbon::now()])
                        ->selectRaw('
                            COUNT(*) as total_orders,
                            SUM(total_amount) as total_revenue,
                            SUM(customer_count) as total_customers
                        ')
                        ->first();

                    $lastMonthData = Order::where('restaurant_id', $restaurantId)
                        ->whereBetween('order_date', [$lastMonth, $currentMonth])
                        ->selectRaw('
                            COUNT(*) as total_orders,
                            SUM(total_amount) as total_revenue,
                            SUM(customer_count) as total_customers
                        ')
                        ->first();

                    return [
                        'current_month' => $currentMonthData,
                        'last_month' => $lastMonthData,
                        'growth_rate' => $this->calculateGrowthRate($currentMonthData, $lastMonthData)
                    ];
                }

                /**
                 * Calculate growth rate between two periods
                 */
                private function calculateGrowthRate($current, $previous)
                {
                    if (!$previous || $previous->total_revenue == 0) {
                        return null;
                    }

                    $revenueGrowth = (($current->total_revenue - $previous->total_revenue) / $previous->total_revenue) * 100;
                    $orderGrowth = (($current->total_orders - $previous->total_orders) / $previous->total_orders) * 100;
                    $customerGrowth = (($current->total_customers - $previous->total_customers) / $previous->total_customers) * 100;

                    return [
                        'revenue_growth' => round($revenueGrowth, 2),
                        'order_growth' => round($orderGrowth, 2),
                        'customer_growth' => round($customerGrowth, 2)
                    ];
                }

                /**
                 * Get category performance analysis
                 */
                private function getCategoryPerformance($restaurantId, $days)
                {
                    $startDate = Carbon::now()->subDays($days);
                    
                    return FoodItem::whereHas('menu', function($query) use ($restaurantId) {
                        $query->where('restaurant_id', $restaurantId);
                    })
                    ->with(['orderItems' => function($query) use ($startDate) {
                        $query->whereHas('order', function($q) use ($startDate) {
                            $q->where('order_date', '>=', $startDate);
                        });
                    }])
                    ->get()
                    ->groupBy('category')
                    ->map(function($items, $category) {
                        $totalSold = $items->sum(function($item) {
                            return $item->orderItems->sum('quantity_sold');
                        });
                        
                        $totalRevenue = $items->sum(function($item) {
                            return $item->orderItems->sum(function($orderItem) {
                                return $orderItem->quantity_sold * $orderItem->unit_price;
                            });
                        });

                        return [
                            'category' => $category,
                            'total_quantity_sold' => $totalSold,
                            'total_revenue' => $totalRevenue,
                            'avg_price' => $totalSold > 0 ? $totalRevenue / $totalSold : 0,
                            'item_count' => $items->count()
                        ];
                    })
                    ->sortByDesc('total_revenue');
                }

                /**
                 * Get peak hours analysis (simplified - could be enhanced with actual time data)
                 */
                private function getPeakHoursAnalysis($restaurantId, $days)
                {
                    // This is a simplified analysis - in a real system, you'd have actual time data
                    return [
                        'note' => 'Peak hours analysis requires actual order time data. Currently using day-of-week patterns.',
                        'day_of_week_patterns' => Order::getWeeklySalesPattern($restaurantId, 4)
                    ];
                }

                /**
                 * Get seasonal trends
                 */
                private function getSeasonalTrends($restaurantId)
                {
                    $currentMonth = Carbon::now()->month;
                    $season = $this->getSeason($currentMonth);
                    
                    return [
                        'current_season' => $season,
                        'seasonal_factors' => $this->getSeasonalFactors($season),
                        'note' => 'Seasonal analysis based on month. Could be enhanced with actual seasonal data.'
                    ];
                }

                /**
                 * Get season based on month
                 */
                private function getSeason($month)
                {
                    if (in_array($month, [12, 1, 2])) return 'Winter';
                    if (in_array($month, [3, 4, 5])) return 'Spring';
                    if (in_array($month, [6, 7, 8])) return 'Summer';
                    return 'Fall';
                }

                /**
                 * Get seasonal factors
                 */
                private function getSeasonalFactors($season)
                {
                    $factors = [
                        'Winter' => ['hot_soups' => 1.3, 'cold_drinks' => 0.7, 'comfort_food' => 1.2],
                        'Spring' => ['fresh_vegetables' => 1.2, 'light_meals' => 1.1, 'seasonal_dishes' => 1.3],
                        'Summer' => ['cold_drinks' => 1.4, 'ice_cream' => 1.5, 'light_meals' => 1.2],
                        'Fall' => ['warm_dishes' => 1.2, 'seasonal_ingredients' => 1.3, 'comfort_food' => 1.1]
                    ];

                    return $factors[$season] ?? [];
                }
            };
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
