<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Restaurant;

class AIAnalysisController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display AI analysis dashboard
     */
    public function index()
    {
        $user = Auth::user();
        $restaurant = $user->restaurant;

        if (!$restaurant) {
            return redirect()->route('restaurant.create')
                ->with('warning', 'Please create restaurant information before viewing AI analysis.');
        }

        // Get AI analysis data
        $aiAnalysis = app('ai.analysis')->getRestaurantAIAnalysis($restaurant->id, 14);

        return view('ai.analysis', compact('aiAnalysis', 'restaurant'));
    }

    /**
     * Get AI analysis data as JSON for API calls
     */
    public function getAnalysisData(Request $request)
    {
        $user = Auth::user();
        $restaurant = $user->restaurant;

        if (!$restaurant) {
            return response()->json(['error' => 'Restaurant not found'], 404);
        }

        $days = $request->get('days', 14);
        $aiAnalysis = app('ai.analysis')->getRestaurantAIAnalysis($restaurant->id, $days);

        return response()->json($aiAnalysis);
    }

    /**
     * Get forecasting data for next day
     */
    public function getForecastingData()
    {
        $user = Auth::user();
        $restaurant = $user->restaurant;

        if (!$restaurant) {
            return response()->json(['error' => 'Restaurant not found'], 404);
        }

        $forecastingData = app('ai.analysis')->getRestaurantAIAnalysis($restaurant->id, 14);
        
        return response()->json([
            'forecasting' => $forecastingData['ai_forecasting_data'],
            'time_series' => $forecastingData['time_series_data'],
            'sales_patterns' => $forecastingData['sales_patterns']
        ]);
    }

    /**
     * Get time series analysis data
     */
    public function getTimeSeriesData(Request $request)
    {
        $user = Auth::user();
        $restaurant = $user->restaurant;

        if (!$restaurant) {
            return response()->json(['error' => 'Restaurant not found'], 404);
        }

        $days = $request->get('days', 14);
        $aiAnalysis = app('ai.analysis')->getRestaurantAIAnalysis($restaurant->id, $days);

        return response()->json([
            'time_series' => $aiAnalysis['time_series_data'],
            'environmental_factors' => $aiAnalysis['environmental_factors']
        ]);
    }

    /**
     * Get sales patterns analysis
     */
    public function getSalesPatterns(Request $request)
    {
        $user = Auth::user();
        $restaurant = $user->restaurant;

        if (!$restaurant) {
            return response()->json(['error' => 'Restaurant not found'], 404);
        }

        $days = $request->get('days', 14);
        $aiAnalysis = app('ai.analysis')->getRestaurantAIAnalysis($restaurant->id, $days);

        return response()->json([
            'sales_patterns' => $aiAnalysis['sales_patterns'],
            'top_selling_items' => $aiAnalysis['sales_patterns']['top_selling_items'],
            'category_performance' => $aiAnalysis['sales_patterns']['category_performance']
        ]);
    }
}
