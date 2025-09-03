@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="container">
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="page-title">🤖 {{ __('messages.AI Analysis') }}</h1>
                    <p class="page-subtitle">{{ __('messages.Comprehensive AI-powered analysis powered by Gemini AI') }}</p>
                </div>
                <div>
                    <button id="testConnectionBtn" class="btn btn-info me-2">
                        <i class="fas fa-wifi me-2"></i>{{ __('messages.Test AI Connection') }}
                    </button>
                    <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>{{ __('messages.Back to Dashboard') }}
                    </a>
                </div>
            </div>
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

        <!-- AI Connection Status -->
        <div id="connectionStatus" class="alert alert-info d-none" role="alert">
            <i class="fas fa-info-circle me-2"></i>
            <span id="connectionMessage"></span>
        </div>

        <!-- AI Analysis Controls -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-cogs me-2"></i>{{ __('messages.AI Analysis Controls') }}
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
                                <button id="generateAnalysis" class="btn btn-success me-2">
                                    <i class="fas fa-magic me-2"></i>{{ __('messages.Generate AI Analysis') }}
                                </button>
                                <button id="refreshData" class="btn btn-outline-primary">
                                    <i class="fas fa-sync-alt me-2"></i>{{ __('messages.Refresh Data') }}
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

        <!-- No Data State -->
        <div id="noDataState" class="text-center py-5">
            <i class="fas fa-robot fs-1 text-muted mb-3"></i>
            <h4 class="text-muted">{{ __('messages.Welcome to AI Analysis') }}</h4>
            <p class="text-muted">{{ __('messages.Click "Generate AI Analysis" to get started with intelligent insights for your restaurant.') }}</p>
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

.ai-insight-card h6 {
    margin-bottom: 0.5rem;
    color: #495057;
}

.ai-insight-card p {
    margin-bottom: 0.5rem;
    color: #6c757d;
}
</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const testConnectionBtn = document.getElementById('testConnectionBtn');
    const generateAnalysisBtn = document.getElementById('generateAnalysis');
    const connectionStatus = document.getElementById('connectionStatus');
    const connectionMessage = document.getElementById('connectionMessage');
    const analysisResults = document.getElementById('analysisResults');
    const loadingState = document.getElementById('loadingState');
    const noDataState = document.getElementById('noDataState');

    // Test AI Connection
    testConnectionBtn.addEventListener('click', function() {
        testConnectionBtn.disabled = true;
        testConnectionBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>{{ __("messages.Testing...") }}';
        
        fetch('{{ route("ai.analysis.test-connection") }}')
            .then(response => response.json())
            .then(data => {
                connectionStatus.classList.remove('d-none', 'alert-danger', 'alert-success');
                connectionStatus.classList.add('alert-success');
                connectionMessage.textContent = data.message;
                if (data.response) {
                    connectionMessage.textContent += ' Response: ' + data.response.substring(0, 100) + '...';
                }
            })
            .catch(error => {
                connectionStatus.classList.remove('d-none', 'alert-success');
                connectionStatus.classList.add('alert-danger');
                connectionMessage.textContent = '{{ __("messages.Connection failed") }}: ' + error.message;
            })
            .finally(() => {
                testConnectionBtn.disabled = false;
                testConnectionBtn.innerHTML = '<i class="fas fa-wifi me-2"></i>{{ __("messages.Test AI Connection") }}';
            });
    });

    // Generate AI Analysis
    generateAnalysisBtn.addEventListener('click', function() {
        const days = document.getElementById('analysisDays').value;
        const forecastPeriod = document.getElementById('forecastPeriod').value;
        
        // Show loading state
        loadingState.classList.remove('d-none');
        analysisResults.classList.add('d-none');
        noDataState.classList.add('d-none');
        
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
            noDataState.classList.remove('d-none');
            alert('{{ __("messages.Error generating AI analysis") }}: ' + error.message);
        }).finally(() => {
            generateAnalysisBtn.disabled = false;
            generateAnalysisBtn.innerHTML = '<i class="fas fa-magic me-2"></i>{{ __("messages.Generate AI Analysis") }}';
        });
    });

    // Generate Forecast
    function generateForecast(days, forecastPeriod) {
        return fetch(`{{ route('ai.analysis.forecasting') }}?days=${days}&forecast_period=${forecastPeriod}`)
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
        return fetch(`{{ route('ai.analysis.waste-insights') }}?days=${days}`)
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
        return fetch(`{{ route('ai.analysis.menu-optimization') }}?days=${days}`)
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
@endsection
