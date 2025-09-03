<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\FoodItemController;
use App\Http\Controllers\OrderController;

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
    Route::post('food-item/{food_item}/calculate-ai', [FoodItemController::class, 'calculateAI'])->name('food-item.calculate-ai');
    
    // Order management routes
    Route::resource('orders', OrderController::class)->only(['index', 'show', 'create', 'store']);
    Route::get('/orders/statistics', [OrderController::class, 'statistics'])->name('orders.statistics');
    
    // Additional routes for waste management
    
    // AI Analysis routes (moved to dashboard)
    Route::get('/dashboard/ai/data', [DashboardController::class, 'getAnalysisData'])->name('dashboard.ai.data');
    Route::get('/dashboard/ai/forecasting', [DashboardController::class, 'getForecastingData'])->name('dashboard.ai.forecasting');
    Route::get('/dashboard/ai/waste-insights', [DashboardController::class, 'getWasteInsights'])->name('dashboard.ai.waste-insights');
    Route::get('/dashboard/ai/menu-optimization', [DashboardController::class, 'getMenuOptimization'])->name('dashboard.ai.menu-optimization');
    Route::get('/dashboard/ai/test-connection', [DashboardController::class, 'testConnection'])->name('dashboard.ai.test-connection');
});



// Gemini AI Routes
Route::prefix('gemini-ai')->name('gemini.')->group(function () {
    Route::get('/test-connection', [GeminiAIController::class, 'testConnection'])->name('test-connection');
    Route::post('/demand-forecast', [GeminiAIController::class, 'generateDemandForecast'])->name('demand-forecast');
    Route::post('/waste-insights', [GeminiAIController::class, 'generateWasteInsights'])->name('waste-insights');
    Route::post('/menu-optimization', [GeminiAIController::class, 'generateMenuOptimization'])->name('menu-optimization');
    Route::get('/comprehensive-analysis', [GeminiAIController::class, 'getComprehensiveAnalysis'])->name('comprehensive-analysis');
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
