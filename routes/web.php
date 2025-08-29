<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\FoodItemController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\AIAnalysisController;
use App\Http\Controllers\GeminiAIController;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Test language route
Route::get('/test-lang', function () {
    return view('test-lang');
})->name('test-lang');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Restaurant management (requires authentication)
Route::middleware(['auth'])->group(function () {
    Route::get('/restaurant', [RestaurantController::class, 'index'])->name('restaurant.index');
    Route::get('/restaurant/create', [RestaurantController::class, 'create'])->name('restaurant.create');
    Route::post('/restaurant', [RestaurantController::class, 'store'])->name('restaurant.store');
    Route::get('/restaurant/{restaurant}', [RestaurantController::class, 'show'])->name('restaurant.show');
    Route::get('/restaurant/{restaurant}/edit', [RestaurantController::class, 'edit'])->name('restaurant.edit');
    Route::put('/restaurant/{restaurant}', [RestaurantController::class, 'update'])->name('restaurant.update');
    
    // Menu management routes
    Route::resource('menus', MenuController::class);
    Route::post('menus/{menu}/toggle-status', [MenuController::class, 'toggleStatus'])->name('menus.toggle-status');
    Route::get('restaurants/{restaurant}/menus', [MenuController::class, 'getByRestaurant'])->name('menus.by-restaurant');
    Route::get('menus/category/{category}', [MenuController::class, 'getByCategory'])->name('menus.by-category');
    
    // Food Item management routes
    Route::resource('food-item', FoodItemController::class);
    Route::post('food-item/{food_item}/toggle-availability', [FoodItemController::class, 'toggleAvailability'])->name('food-item.toggle-availability');
    Route::get('menus/{menu}/food-items', [FoodItemController::class, 'getByMenu'])->name('food-item.by-menu');
    Route::get('food-item/category/{category}', [FoodItemController::class, 'getByCategory'])->name('food-item.by-category');
    Route::post('food-item/{food_item}/update-stock', [FoodItemController::class, 'updateStock'])->name('food-item.update-stock');
    
    // Order management routes
    Route::resource('orders', OrderController::class)->only(['index', 'show', 'create', 'store']);
    Route::get('/orders/statistics', [OrderController::class, 'statistics'])->name('orders.statistics');
    
    // Additional routes for waste management
    Route::get('/ai-insights', [DashboardController::class, 'aiInsights'])->name('ai.insights');
    
    // AI Analysis routes
    Route::get('/ai-analysis', [AIAnalysisController::class, 'index'])->name('ai.analysis');
    Route::get('/ai-analysis/data', [AIAnalysisController::class, 'getAnalysisData'])->name('ai.analysis.data');
    Route::get('/ai-analysis/forecasting', [AIAnalysisController::class, 'getForecastingData'])->name('ai.analysis.forecasting');
    Route::get('/ai-analysis/time-series', [AIAnalysisController::class, 'getTimeSeriesData'])->name('ai.analysis.time-series');
    Route::get('/ai-analysis/sales-patterns', [AIAnalysisController::class, 'getSalesPatterns'])->name('ai.analysis.sales-patterns');

// Gemini AI Routes
Route::prefix('gemini-ai')->name('gemini.')->group(function () {
    Route::get('/test-connection', [GeminiAIController::class, 'testConnection'])->name('test-connection');
    Route::post('/demand-forecast', [GeminiAIController::class, 'generateDemandForecast'])->name('demand-forecast');
    Route::post('/waste-insights', [GeminiAIController::class, 'generateWasteInsights'])->name('waste-insights');
    Route::post('/menu-optimization', [GeminiAIController::class, 'generateMenuOptimization'])->name('menu-optimization');
    Route::get('/comprehensive-analysis', [GeminiAIController::class, 'getComprehensiveAnalysis'])->name('comprehensive-analysis');
});
});

// Redirect authenticated users to dashboard
Route::middleware(['auth'])->group(function () {
    Route::get('/home', function () {
        return redirect()->route('dashboard');
    });
});

// Language switcher route
Route::get('/language/{locale}', function ($locale) {
    if (in_array($locale, ['ja', 'vi'])) {
        session()->put('locale', $locale);
        app()->setLocale($locale);
    }
    return redirect()->back();
})->name('language.switch');
