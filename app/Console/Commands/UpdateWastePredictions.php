<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\WastePredictionService;
use App\Models\FoodItem;

class UpdateWastePredictions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'waste:update-predictions 
                            {--item= : Update prediction for specific food item ID}
                            {--all : Update predictions for all food items}
                            {--show-breakdown : Show detailed calculation breakdown}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update AI waste predictions for food items using advanced calculation formula';

    protected $wastePredictionService;

    public function __construct(WastePredictionService $wastePredictionService)
    {
        parent::__construct();
        $this->wastePredictionService = $wastePredictionService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🤖 AI Waste Prediction Calculator');
        $this->info('=====================================');

        $itemId = $this->option('item');
        $updateAll = $this->option('all');
        $showBreakdown = $this->option('show-breakdown');

        if ($itemId) {
            $this->updateSingleItem($itemId, $showBreakdown);
        } elseif ($updateAll) {
            $this->updateAllItems();
        } else {
            $this->showUsage();
        }
    }

    /**
     * Update prediction for a single food item
     */
    private function updateSingleItem($itemId, $showBreakdown = false)
    {
        $foodItem = FoodItem::find($itemId);
        
        if (!$foodItem) {
            $this->error("❌ Food item with ID {$itemId} not found!");
            return;
        }

        $this->info("📊 Updating waste prediction for: {$foodItem->name}");
        $this->info("Current prediction: " . ($foodItem->ai_waste_prediction ?? 'Not set') . "%");

        if ($showBreakdown) {
            $this->showCalculationBreakdown($foodItem);
        }

        $this->info("🔄 Calculating new prediction...");
        
        if ($this->wastePredictionService->updateWastePrediction($foodItem)) {
            $foodItem->refresh();
            $this->info("✅ Successfully updated!");
            $this->info("New prediction: {$foodItem->ai_waste_prediction}%");
            $this->info("Risk level: " . $foodItem->getWasteInsights());
        } else {
            $this->error("❌ Failed to update prediction!");
        }
    }

    /**
     * Update predictions for all food items
     */
    private function updateAllItems()
    {
        $this->info("🔄 Updating waste predictions for all food items...");
        
        $results = $this->wastePredictionService->updateAllWastePredictions();
        
        $this->info("📈 Results:");
        $this->info("✅ Successfully updated: {$results['success']} items");
        $this->info("❌ Failed to update: {$results['failed']} items");
        $this->info("📊 Total processed: {$results['total']} items");

        if ($results['success'] > 0) {
            $this->info("\n🎯 Top 5 highest risk items:");
            $highRiskItems = FoodItem::whereNotNull('ai_waste_prediction')
                ->orderBy('ai_waste_prediction', 'desc')
                ->limit(5)
                ->get();

            foreach ($highRiskItems as $item) {
                $riskLevel = $item->ai_waste_prediction > 20 ? '🔴 HIGH' : 
                           ($item->ai_waste_prediction > 10 ? '🟡 MODERATE' : '🟢 LOW');
                $this->info("- {$item->name}: {$item->ai_waste_prediction}% ({$riskLevel})");
            }
        }
    }

    /**
     * Show detailed calculation breakdown
     */
    private function showCalculationBreakdown(FoodItem $foodItem)
    {
        $breakdown = $this->wastePredictionService->getPredictionBreakdown($foodItem);
        
        $this->info("\n📋 Calculation Breakdown:");
        $this->info("Base Risk: {$breakdown['base_risk']}%");
        $this->info("Price Factor: {$breakdown['price_factor']}x (Price: " . number_format($foodItem->price) . " VNĐ)");
        $this->info("Prep Time Factor: {$breakdown['prep_time_factor']}x ({$foodItem->preparation_time})");
        $this->info("Category Factor: {$breakdown['category_factor']}x ({$foodItem->category})");
        $this->info("Stock Factor: {$breakdown['stock_factor']}x (Current: {$foodItem->stock_quantity}/{$foodItem->min_stock_level})");
        $this->info("Historical Factor: {$breakdown['historical_factor']}x (Based on 30-day waste data)");
        $this->info("Sales Factor: {$breakdown['sales_factor']}x (Based on 14-day sales)");
        $this->info("Seasonal Factor: {$breakdown['seasonal_factor']}x (Month: " . date('n') . ")");
        $this->info("\nFormula: {$breakdown['calculation']}");
        $this->info("Final Prediction: {$breakdown['final_prediction']}%");
    }

    /**
     * Show command usage
     */
    private function showUsage()
    {
        $this->info("Usage examples:");
        $this->info("  php artisan waste:update-predictions --item=20");
        $this->info("  php artisan waste:update-predictions --item=20 --show-breakdown");
        $this->info("  php artisan waste:update-predictions --all");
        $this->info("");
        $this->info("Options:");
        $this->info("  --item=ID        Update prediction for specific food item");
        $this->info("  --all            Update predictions for all food items");
        $this->info("  --show-breakdown Show detailed calculation breakdown");
    }
}
