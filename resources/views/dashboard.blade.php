@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="container">
        <div class="page-header">
            <h1 class="page-title">🎯 {{ __('messages.dashboard') }}</h1>
            <p class="page-subtitle">{{ __('messages.Welcome back') }}, {{ Auth::user()->name }}! {{ __('messages.Here\'s your restaurant overview.') }}</p>
        </div>
    </div>

    <div class="container">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Statistics Cards -->
        <div class="row">
            <!-- First Row - 3 cards -->
            <div class="col-lg-4 col-md-6 mb-1">
                <div class="stats-card stats-card-revenue">
                    <div class="stats-icon">💰</div>
                    <div class="stats-content">
                        <div class="stats-number">{{ number_format($totalRevenue ?? 0, 0) }}¥</div>
                        <div class="stats-label">{{ __('messages.total_revenue') }}</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-1">
                <div class="stats-card stats-card-orders">
                    <div class="stats-icon">🍽️</div>
                    <div class="stats-content">
                        <div class="stats-number">{{ $totalOrders }}</div>
                        <div class="stats-label">{{ __('messages.total_orders') }}</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-1">
                <div class="stats-card stats-card-waste">
                    <div class="stats-icon">📊</div>
                    <div class="stats-content">
                        <div class="stats-number">{{ number_format($wastePercentage ?? 0, 1) }}%</div>
                        <div class="stats-label">{{ __('messages.waste_percentage') }}</div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mb-4">
            <!-- Second Row - 3 cards -->
            <div class="col-lg-4 col-md-6 mb-1">
                <div class="stats-card stats-card-ai">
                    <div class="stats-icon">🤖</div>
                    <div class="stats-content">
                        <div class="stats-number">{{ number_format($aiAccuracy ?? 0, 1) }}%</div>
                        <div class="stats-label">{{ __('messages.ai_prediction_accuracy') }}</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-1">
                <div class="stats-card stats-card-menus">
                    <div class="stats-icon">📋</div>
                    <div class="stats-content">
                        <div class="stats-number">{{ $totalMenus ?? 0 }}</div>
                        <div class="stats-label">{{ __('messages.Total Menus') }}</div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-1">
                <div class="stats-card stats-card-items">
                    <div class="stats-icon">🍜</div>
                    <div class="stats-content">
                        <div class="stats-number">{{ $totalFoodItems ?? 0 }}</div>
                        <div class="stats-label">{{ __('messages.Total Food Items') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- AI Analysis Controls -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-robot me-2"></i>{{ __('messages.AI Analysis') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label for="analysisDays" class="form-label">{{ __('messages.Analysis Period (Days)') }}</label>
                                <select id="analysisDays" class="form-select">
                                    <option value="7">7 {{ __('messages.days') }}</option>
                                    <option value="14" selected>14 {{ __('messages.days') }}</option>
                                    <option value="30">30 {{ __('messages.days') }}</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="forecastPeriod" class="form-label">{{ __('messages.Forecast Period (Days)') }}</label>
                                <select id="forecastPeriod" class="form-select">
                                    <option value="3">3 {{ __('messages.days') }}</option>
                                    <option value="7" selected>7 {{ __('messages.days') }}</option>
                                    <option value="14">14 {{ __('messages.days') }}</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3 d-flex align-items-end">
                                <button id="generateAnalysis" class="btn btn-success">
                                    <i class="fas fa-magic me-2"></i>{{ __('messages.Generate AI Analysis') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- AI Analysis Results -->
        <div id="analysisResults" class="d-none">
            <!-- Demand Forecasting -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-chart-line me-2"></i>{{ __('messages.AI Demand Forecasting') }}
                            </h5>
                        </div>
                        <div class="card-body">
                            <div id="forecastContent">
                                <!-- Forecast content will be loaded here -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Waste Insights -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-recycle me-2"></i>{{ __('messages.AI Waste Insights') }}
                            </h5>
                        </div>
                        <div class="card-body">
                            <div id="wasteInsightsContent">
                                <!-- Waste insights content will be loaded here -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Menu Optimization -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-info text-white">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-utensils me-2"></i>{{ __('messages.AI Menu Optimization') }}
                            </h5>
                        </div>
                        <div class="card-body">
                            <div id="menuOptimizationContent">
                                <!-- Menu optimization content will be loaded here -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Loading State -->
        <div id="loadingState" class="text-center py-5 d-none">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">{{ __('messages.Loading...') }}</span>
            </div>
            <p class="mt-3 text-muted">{{ __('messages.Generating AI insights...') }}</p>
        </div>

        <!-- Quick Actions -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-line me-2"></i> {{ __('messages.Quick Actions') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2 d-md-flex">
                            <a href="{{ route('restaurant.create') }}" class="btn btn-outline-primary">
                                <i class="fas fa-plus me-2"></i>{{ __('messages.Add Restaurant') }}
                            </a>
                            <a href="{{ route('menus.create') }}" class="btn btn-outline-success">
                                <i class="fas fa-utensils me-2"></i>{{ __('messages.Create Menu') }}
                            </a>
                            <a href="{{ route('food-item.create') }}" class="btn btn-outline-info">
                                <i class="fas fa-hamburger me-2"></i>{{ __('messages.Add Food Item') }}
                            </a>
                            <a href="{{ route('orders.create') }}" class="btn btn-outline-warning">
                                <i class="fas fa-shopping-cart me-2"></i>{{ __('messages.Create Order') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity & Low Stock -->
        <div class="row mb-4">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-exclamation-triangle me-2"></i> {{ __('messages.low_stock_alerts') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($lowStockItems->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>{{ __('messages.Item') }}</th>
                                            <th>{{ __('messages.Current Stock') }}</th>
                                            <th>{{ __('messages.Min Level') }}</th>
                                            <th>{{ __('messages.Status') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($lowStockItems as $item)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-hamburger text-warning me-2"></i>
                                                        <strong>{{ $item->name }}</strong>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-danger">{{ $item->stock_quantity }}</span>
                                                </td>
                                                <td>{{ $item->min_stock_level }}</td>
                                                <td>
                                                    @if($item->stock_quantity == 0)
                                                        <span class="badge bg-danger">{{ __('messages.Out of Stock') }}</span>
                                                    @else
                                                        <span class="badge bg-warning">{{ __('messages.Low Stock') }}</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-check-circle text-success fs-1 mb-3"></i>
                                <p>{{ __('messages.All items are well stocked!') }} 🎉</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-history me-2"></i> {{ __('messages.Recent Waste Records') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($recentWasteRecords->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>{{ __('messages.Item') }}</th>
                                            <th>{{ __('messages.Waste Amount') }}</th>
                                            <th>{{ __('messages.Cost') }}</th>
                                            <th>{{ __('messages.Date') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recentWasteRecords as $record)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-trash text-danger me-2"></i>
                                                        <strong>{{ $record->foodItem->name }}</strong>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-secondary">
                                                        {{ $record->formatted_quantity }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="text-danger fw-bold">
                                                        {{ $record->formatted_waste_cost }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <small class="text-muted">
                                                        {{ $record->waste_date->format('M d, Y') }}
                                                    </small>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-leaf text-success fs-1 mb-3"></i>
                                <p>{{ __('messages.No waste records found. Great job!') }} 🌱</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Waste by Category Chart -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-dark text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-bar me-2"></i> {{ __('messages.Waste Analysis by Category') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($wasteByCategory->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>{{ __('messages.Category') }}</th>
                                            <th>{{ __('messages.Total Waste') }}</th>
                                            <th>{{ __('messages.Total Cost') }}</th>
                                            <th>{{ __('messages.Percentage') }}</th>
                                            <th>{{ __('messages.Trend') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($wasteByCategory as $category)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        @switch($category->category)
                                                            @case('Main Course')
                                                                <i class="fas fa-utensils text-primary me-2"></i>
                                                                @break
                                                            @case('Appetizer')
                                                                <i class="fas fa-carrot text-success me-2"></i>
                                                                @break
                                                            @case('Dessert')
                                                                <i class="fas fa-ice-cream text-warning me-2"></i>
                                                                @break
                                                            @case('Beverage')
                                                                <i class="fas fa-coffee text-info me-2"></i>
                                                                @break
                                                            @default
                                                                <i class="fas fa-circle text-secondary me-2"></i>
                                                        @endswitch
                                                        <strong>{{ $category->category }}</strong>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-secondary">
                                                        {{ number_format($category->total_waste ?? 0, 1) }} {{ $category->waste_unit }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="text-danger fw-bold">
                                                        ¥{{ number_format($category->total_cost ?? 0, 0) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="progress" style="height: 20px;">
                                                        @php
                                                            $percentage = ($totalRevenue > 0 && $category->total_cost) ? ($category->total_cost / $totalRevenue) * 100 : 0;
                                                        @endphp
                                                        <div class="progress-bar bg-danger" style="width: {{ min($percentage, 100) }}%">
                                                            {{ number_format($percentage, 1) }}%
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    @if($category->ai_predicted_waste > $category->actual_waste_percentage)
                                                        <i class="fas fa-arrow-down text-success" title="{{ __('messages.Better than predicted') }}"></i>
                                                    @else
                                                        <i class="fas fa-arrow-up text-danger" title="{{ __('messages.Worse than predicted') }}"></i>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-chart-bar fs-1 mb-3"></i>
                                <p>{{ __('messages.No waste data available for analysis yet.') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.ai-insight-card {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 1rem;
    border-left: 4px solid #007bff;
}

.ai-insight-card.success {
    border-left-color: #28a745;
}

.ai-insight-card.warning {
    border-left-color: #ffc107;
}

.ai-insight-card.danger {
    border-left-color: #dc3545;
}

.ai-insight-card.info {
    border-left-color: #17a2b8;
}

.ai-insight-card h6 {
    margin-bottom: 0.5rem;
    color: #495057;
}

.ai-insight-card p {
    margin-bottom: 0.5rem;
    color: #6c757d;
}
</style>
@endsection

@push('scripts')
<script>
    // Add some interactive features
    document.addEventListener('DOMContentLoaded', function() {
        // Animate stats cards on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        document.querySelectorAll('.stats-card').forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'all 0.6s ease';
            observer.observe(card);
        });

        // Add hover effects to table rows
        document.querySelectorAll('.table tbody tr').forEach(row => {
            row.addEventListener('mouseenter', function() {
                this.style.backgroundColor = '#f8f9fa';
                this.style.transform = 'scale(1.01)';
                this.style.transition = 'all 0.2s ease';
            });

            row.addEventListener('mouseleave', function() {
                this.style.backgroundColor = '';
                this.style.transform = 'scale(1)';
            });
        });

        // AI Analysis functionality
        const generateAnalysisBtn = document.getElementById('generateAnalysis');
        const analysisResults = document.getElementById('analysisResults');
        const loadingState = document.getElementById('loadingState');

        // Generate AI Analysis
        if (generateAnalysisBtn) {
            generateAnalysisBtn.addEventListener('click', function() {
                const days = document.getElementById('analysisDays').value;
                const forecastPeriod = document.getElementById('forecastPeriod').value;
                
                // Show loading state
                loadingState.classList.remove('d-none');
                analysisResults.classList.add('d-none');
                
                generateAnalysisBtn.disabled = true;
                generateAnalysisBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>{{ __("messages.Generating...") }}';

                // Generate all AI insights in parallel
                Promise.all([
                    generateForecast(days, forecastPeriod),
                    generateWasteInsights(days),
                    generateMenuOptimization(days)
                ]).then(() => {
                    // Show results
                    loadingState.classList.add('d-none');
                    analysisResults.classList.remove('d-none');
                }).catch(error => {
                    console.error('Error generating analysis:', error);
                    loadingState.classList.add('d-none');
                    alert('{{ __("messages.Error generating AI analysis") }}: ' + error.message);
                }).finally(() => {
                    generateAnalysisBtn.disabled = false;
                    generateAnalysisBtn.innerHTML = '<i class="fas fa-magic me-2"></i>{{ __("messages.Generate AI Analysis") }}';
                });
            });
        }

        // Generate Forecast
        function generateForecast(days, forecastPeriod) {
            return fetch(`{{ route('dashboard.ai.forecasting') }}?days=${days}&forecast_period=${forecastPeriod}`)
                .then(response => response.json())
                .then(data => {
                    const content = document.getElementById('forecastContent');
                    if (data.forecast) {
                        content.innerHTML = formatForecastResponse(data.forecast);
                    } else {
                        content.innerHTML = '<p class="text-muted">{{ __("messages.No forecast data available") }}</p>';
                    }
                });
        }

        // Generate Waste Insights
        function generateWasteInsights(days) {
            return fetch(`{{ route('dashboard.ai.waste-insights') }}?days=${days}`)
                .then(response => response.json())
                .then(data => {
                    const content = document.getElementById('wasteInsightsContent');
                    if (data.waste_insights) {
                        content.innerHTML = formatWasteInsightsResponse(data.waste_insights);
                    } else {
                        content.innerHTML = '<p class="text-muted">{{ __("messages.No waste insights available") }}</p>';
                    }
                });
        }

        // Generate Menu Optimization
        function generateMenuOptimization(days) {
            return fetch(`{{ route('dashboard.ai.menu-optimization') }}?days=${days}`)
                .then(response => response.json())
                .then(data => {
                    const content = document.getElementById('menuOptimizationContent');
                    if (data.menu_optimization) {
                        content.innerHTML = formatMenuOptimizationResponse(data.menu_optimization);
                    } else {
                        content.innerHTML = '<p class="text-muted">{{ __("messages.No menu optimization available") }}</p>';
                    }
                });
        }

        // Format Forecast Response
        function formatForecastResponse(forecast) {
            let html = '';
            
            if (forecast.analysis) {
                html += `<div class="ai-insight-card success">
                    <h6><i class="fas fa-chart-line me-2"></i>{{ __("messages.Analysis") }}</h6>
                    <p>${forecast.analysis}</p>
                </div>`;
            }
            
            if (forecast.customer_forecast && Array.isArray(forecast.customer_forecast)) {
                html += `<div class="ai-insight-card info">
                    <h6><i class="fas fa-users me-2"></i>{{ __("messages.Customer Forecast") }}</h6>
                    <p>${forecast.customer_forecast.join(', ')}</p>
                </div>`;
            }
            
            if (forecast.food_forecast && Array.isArray(forecast.food_forecast)) {
                html += `<div class="ai-insight-card warning">
                    <h6><i class="fas fa-utensils me-2"></i>{{ __("messages.Food Forecast") }}</h6>
                    <p>${forecast.food_forecast.join(', ')}</p>
                </div>`;
            }
            
            if (forecast.ingredient_recommendations && Array.isArray(forecast.ingredient_recommendations)) {
                html += `<div class="ai-insight-card success">
                    <h6><i class="fas fa-shopping-cart me-2"></i>{{ __("messages.Ingredient Recommendations") }}</h6>
                    <p>${forecast.ingredient_recommendations.join(', ')}</p>
                </div>`;
            }
            
            if (forecast.confidence_level) {
                html += `<div class="mt-3">
                    <span class="badge bg-${forecast.confidence_level === 'High' ? 'success' : forecast.confidence_level === 'Medium' ? 'warning' : 'danger'}">
                        {{ __("messages.Confidence Level") }}: ${forecast.confidence_level}
                    </span>
                </div>`;
            }
            
            return html || '<p class="text-muted">{{ __("messages.No forecast data available") }}</p>';
        }

        // Format Waste Insights Response
        function formatWasteInsightsResponse(insights) {
            let html = '';
            
            if (insights.waste_analysis) {
                html += `<div class="ai-insight-card warning">
                    <h6><i class="fas fa-recycle me-2"></i>{{ __("messages.Waste Analysis") }}</h6>
                    <p>${insights.waste_analysis}</p>
                </div>`;
            }
            
            if (insights.recommendations && Array.isArray(insights.recommendations)) {
                html += `<div class="ai-insight-card success">
                    <h6><i class="fas fa-lightbulb me-2"></i>{{ __("messages.Recommendations") }}</h6>
                    <ul class="mb-0">${insights.recommendations.map(rec => `<li>${rec}</li>`).join('')}</ul>
                </div>`;
            }
            
            if (insights.process_optimization && Array.isArray(insights.process_optimization)) {
                html += `<div class="ai-insight-card info">
                    <h6><i class="fas fa-cogs me-2"></i>{{ __("messages.Process Optimization") }}</h6>
                    <ul class="mb-0">${insights.process_optimization.map(opt => `<li>${opt}</li>`).join('')}</ul>
                </div>`;
            }
            
            if (insights.cost_savings) {
                html += `<div class="ai-insight-card success">
                    <h6><i class="fas fa-yen-sign me-2"></i>{{ __("messages.Cost Savings") }}</h6>
                    <p>${insights.cost_savings}</p>
                </div>`;
            }
            
            return html || '<p class="text-muted">{{ __("messages.No waste insights available") }}</p>';
        }

        // Format Menu Optimization Response
        function formatMenuOptimizationResponse(optimization) {
            let html = '';
            
            if (optimization.menu_performance) {
                html += `<div class="ai-insight-card info">
                    <h6><i class="fas fa-chart-bar me-2"></i>{{ __("messages.Menu Performance") }}</h6>
                    <p>${optimization.menu_performance}</p>
                </div>`;
            }
            
            if (optimization.menu_changes && Array.isArray(optimization.menu_changes)) {
                html += `<div class="ai-insight-card warning">
                    <h6><i class="fas fa-edit me-2"></i>{{ __("messages.Menu Changes") }}</h6>
                    <ul class="mb-0">${optimization.menu_changes.map(change => `<li>${change}</li>`).join('')}</ul>
                </div>`;
            }
            
            if (optimization.pricing_optimization && Array.isArray(optimization.pricing_optimization)) {
                html += `<div class="ai-insight-card success">
                    <h6><i class="fas fa-tags me-2"></i>{{ __("messages.Pricing Optimization") }}</h6>
                    <ul class="mb-0">${optimization.pricing_optimization.map(price => `<li>${price}</li>`).join('')}</ul>
                </div>`;
            }
            
            if (optimization.waste_reduction && Array.isArray(optimization.waste_reduction)) {
                html += `<div class="ai-insight-card warning">
                    <h6><i class="fas fa-recycle me-2"></i>{{ __("messages.Waste Reduction") }}</h6>
                    <ul class="mb-0">${optimization.waste_reduction.map(waste => `<li>${waste}</li>`).join('')}</ul>
                </div>`;
            }
            
            if (optimization.profit_improvement) {
                html += `<div class="ai-insight-card success">
                    <h6><i class="fas fa-chart-line me-2"></i>{{ __("messages.Profit Improvement") }}</h6>
                    <p>${optimization.profit_improvement}</p>
                </div>`;
            }
            
            return html || '<p class="text-muted">{{ __("messages.No menu optimization available") }}</p>';
        }
    });
</script>
@endpush
