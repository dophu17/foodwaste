<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\FoodItemController;

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
    Route::resource('restaurant', RestaurantController::class);
    
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
    
    // Additional routes for waste management
    Route::get('/waste-analytics', [DashboardController::class, 'wasteAnalytics'])->name('waste.analytics');
    Route::get('/ai-insights', [DashboardController::class, 'aiInsights'])->name('ai.insights');
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
