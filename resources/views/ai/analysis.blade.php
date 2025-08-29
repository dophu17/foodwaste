@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="container">
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="page-title">🤖 {{ __('messages.AI Analysis') }}</h1>
                    <p class="page-subtitle">{{ __('messages.Comprehensive AI-powered analysis for demand forecasting and insights') }}</p>
                </div>
                <div>
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

        <!-- AI Analysis Overview -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card text-center">
                    <div class="icon">📊</div>
                    <div class="number">{{ $aiAnalysis['restaurant_info']['analysis_period'] }}</div>
                    <div class="label">{{ __('messages.Analysis Period') }}</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card text-center">
                    <div class="icon">🍽️</div>
                    <div class="number">{{ count($aiAnalysis['sales_patterns']['top_selling_items']) }}</div>
                    <div class="label">{{ __('messages.Top Selling Items') }}</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card text-center">
                    <div class="icon">📈</div>
                    <div class="number">{{ count($aiAnalysis['time_series_data']['daily_sales_trend']) }}</div>
                    <div class="label">{{ __('messages.Days Analyzed') }}</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card text-center">
                    <div class="icon">🎯</div>
                    <div class="number">{{ $aiAnalysis['ai_forecasting_data']['overall_demand_prediction']['confidence_level'] }}</div>
                    <div class="label">{{ __('messages.Prediction Confidence') }}</div>
                </div>
            </div>
        </div>

        <!-- Time Series Analysis -->
        <div class="row mb-4">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-line me-2"></i>{{ __('messages.Daily Sales Trend Analysis') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        @if(count($aiAnalysis['time_series_data']['daily_sales_trend']) > 0)
                            <div class="chart-container" style="position: relative; height:300px;">
                                <canvas id="dailySalesChart"></canvas>
                            </div>
                        @else
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-chart-line fs-1 mb-3"></i>
                                <p>{{ __('messages.No daily sales data available for analysis') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-calendar-week me-2"></i>{{ __('messages.Weekly Sales Pattern') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        @if(count($aiAnalysis['time_series_data']['weekly_patterns']) > 0)
                            <div class="weekly-patterns">
                                @foreach($aiAnalysis['time_series_data']['weekly_patterns'] as $pattern)
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="fw-medium">{{ $pattern->day_of_week }}</span>
                                        <div class="text-end">
                                            <div class="fw-bold text-success">¥{{ number_format($pattern->total_revenue, 0) }}</div>
                                            <small class="text-muted">{{ $pattern->total_orders }} {{ __('messages.orders') }}</small>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-calendar-week fs-1 mb-3"></i>
                                <p>{{ __('messages.No weekly pattern data available') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Sales Patterns Analysis -->
        <div class="row mb-4">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-trophy me-2"></i>{{ __('messages.Top Selling Items') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        @if(count($aiAnalysis['sales_patterns']['top_selling_items']) > 0)
                            <div class="top-selling-items">
                                @foreach($aiAnalysis['sales_patterns']['top_selling_items']->take(5) as $item)
                                    <div class="d-flex justify-content-between align-items-center mb-3 p-2 border-bottom">
                                        <div>
                                            <div class="fw-bold">{{ $item['name'] }}</div>
                                            <small class="text-muted">{{ $item['category'] }}</small>
                                        </div>
                                        <div class="text-end">
                                            <div class="fw-bold text-success">{{ $item['total_quantity_sold'] }}</div>
                                            <small class="text-muted">{{ __('messages.sold') }}</small>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-trophy fs-1 mb-3"></i>
                                <p>{{ __('messages.No sales data available for analysis') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-pie me-2"></i>{{ __('messages.Category Performance') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        @if(count($aiAnalysis['sales_patterns']['category_performance']) > 0)
                            <div class="category-performance">
                                @foreach($aiAnalysis['sales_patterns']['category_performance']->take(5) as $category)
                                    <div class="d-flex justify-content-between align-items-center mb-3 p-2 border-bottom">
                                        <div>
                                            <div class="fw-bold">{{ $category['category'] }}</div>
                                            <small class="text-muted">{{ $category['item_count'] }} {{ __('messages.items') }}</small>
                                        </div>
                                        <div class="text-end">
                                            <div class="fw-bold text-success">¥{{ number_format($category['total_revenue'], 0) }}</div>
                                            <small class="text-muted">{{ $category['total_quantity_sold'] }} {{ __('messages.sold') }}</small>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-chart-pie fs-1 mb-3"></i>
                                <p>{{ __('messages.No category performance data available') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- AI Forecasting Data -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-danger text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-crystal-ball me-2"></i>{{ __('messages.AI Demand Forecasting') }} - {{ $aiAnalysis['ai_forecasting_data']['tomorrow_date'] }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Overall Demand Prediction -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6 class="text-primary">{{ __('messages.Overall Demand Prediction') }}</h6>
                                <div class="demand-prediction p-3 bg-light rounded">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="mb-2">
                                                <small class="text-muted">{{ __('messages.Average Customers/Day') }}</small>
                                                <div class="fw-bold">{{ $aiAnalysis['ai_forecasting_data']['overall_demand_prediction']['avg_customers_per_day'] }}</div>
                                            </div>
                                            <div class="mb-2">
                                                <small class="text-muted">{{ __('messages.Tomorrow\'s Day') }}</small>
                                                <div class="fw-bold">{{ $aiAnalysis['ai_forecasting_data']['overall_demand_prediction']['tomorrow_day_of_week'] }}</div>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="mb-2">
                                                <small class="text-muted">{{ __('messages.Day Multiplier') }}</small>
                                                <div class="fw-bold">{{ $aiAnalysis['ai_forecasting_data']['overall_demand_prediction']['day_multiplier'] }}</div>
                                            </div>
                                            <div class="mb-2">
                                                <small class="text-muted">{{ __('messages.Predicted Customers') }}</small>
                                                <div class="fw-bold text-success fs-5">{{ $aiAnalysis['ai_forecasting_data']['overall_demand_prediction']['predicted_customers_tomorrow'] }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <small class="text-muted">{{ __('messages.Confidence Level') }}:</small>
                                        <span class="badge bg-{{ $aiAnalysis['ai_forecasting_data']['overall_demand_prediction']['confidence_level'] == 'High' ? 'success' : ($aiAnalysis['ai_forecasting_data']['overall_demand_prediction']['confidence_level'] == 'Medium' ? 'warning' : 'danger') }}">
                                            {{ $aiAnalysis['ai_forecasting_data']['overall_demand_prediction']['confidence_level'] }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <h6 class="text-success">{{ __('messages.Forecasting Factors') }}</h6>
                                <div class="forecasting-factors p-3 bg-light rounded">
                                    <div class="mb-2">
                                        <small class="text-muted">{{ __('messages.Base Demand') }}</small>
                                        <div class="fw-bold">{{ __('messages.Calculated from') }} {{ $aiAnalysis['ai_forecasting_data']['forecast_period }}</div>
                                    </div>
                                    <div class="mb-2">
                                        <small class="text-muted">{{ __('messages.Day of Week Impact') }}</small>
                                        <div class="fw-bold">{{ __('messages.Historical patterns for') }} {{ $aiAnalysis['ai_forecasting_data']['tomorrow_day_of_week'] }}</div>
                                    </div>
                                    <div class="mb-2">
                                        <small class="text-muted">{{ __('messages.Weather Impact') }}</small>
                                        <div class="fw-bold">{{ __('messages.Weather-based demand adjustments') }}</div>
                                    </div>
                                    <div class="mb-2">
                                        <small class="text-muted">{{ __('messages.Holiday Impact') }}</small>
                                        <div class="fw-bold">{{ __('messages.Special day demand patterns') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Food Items Forecast -->
                        <h6 class="text-info mb-3">{{ __('messages.Food Items Forecast') }}</h6>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>{{ __('messages.Food Item') }}</th>
                                        <th>{{ __('messages.Category') }}</th>
                                        <th>{{ __('messages.Current Stock') }}</th>
                                        <th>{{ __('messages.Base Demand') }}</th>
                                        <th>{{ __('messages.Recommended') }}</th>
                                        <th>{{ __('messages.Status') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($aiAnalysis['ai_forecasting_data']['food_items_forecast'] as $forecast)
                                        <tr>
                                            <td>
                                                <div class="fw-bold">{{ $forecast['name'] }}</div>
                                                <small class="text-muted">{{ $forecast['category'] }}</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary">{{ $forecast['category'] }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $forecast['current_stock'] <= $forecast['min_stock_level'] ? 'danger' : 'success' }}">
                                                    {{ $forecast['current_stock'] }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="fw-medium">{{ number_format($forecast['recommended_preparation']['base_demand'], 1) }}</span>
                                            </td>
                                            <td>
                                                <span class="fw-bold text-success">{{ $forecast['recommended_preparation']['recommended_quantity'] }}</span>
                                                <small class="text-muted d-block">{{ __('messages.Including') }} {{ $forecast['recommended_preparation']['safety_buffer'] }}</small>
                                            </td>
                                            <td>
                                                @if($forecast['recommended_preparation']['recommended_quantity'] <= $forecast['current_stock'])
                                                    <span class="badge bg-success">{{ __('messages.Sufficient') }}</span>
                                                @else
                                                    <span class="badge bg-warning">{{ __('messages.Prepare') }} {{ $forecast['recommended_preparation']['recommended_quantity'] - $forecast['current_stock'] }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Environmental Factors -->
        <div class="row mb-4">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-cloud me-2"></i>{{ __('messages.Weather Impact Analysis') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        @if(count($aiAnalysis['environmental_factors']['weather_impact']) > 0)
                            <div class="weather-impact">
                                @foreach($aiAnalysis['environmental_factors']['weather_impact'] as $weather)
                                    <div class="d-flex justify-content-between align-items-center mb-3 p-2 border-bottom">
                                        <div>
                                            <div class="fw-bold">{{ $weather->weather_condition }}</div>
                                            <small class="text-muted">{{ $weather->total_orders }} {{ __('messages.orders') }}</small>
                                        </div>
                                        <div class="text-end">
                                            <div class="fw-bold text-success">¥{{ number_format($weather->total_revenue, 0) }}</div>
                                            <small class="text-muted">{{ __('messages.AVG') }}: ¥{{ number_format($weather->avg_order_value, 0) }}</small>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-cloud fs-1 mb-3"></i>
                                <p>{{ __('messages.No weather impact data available') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-star me-2"></i>{{ __('messages.Holiday & Event Impact') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        @if(count($aiAnalysis['environmental_factors']['holiday_impact']) > 0)
                            <div class="holiday-impact">
                                @foreach($aiAnalysis['environmental_factors']['holiday_impact']->take(5) as $event)
                                    <div class="d-flex justify-content-between align-items-center mb-3 p-2 border-bottom">
                                        <div>
                                            <div class="fw-bold">{{ $event->date }}</div>
                                            <small class="text-muted">
                                                @if($event->is_holiday)
                                                    <span class="badge bg-warning">{{ __('messages.Holiday') }}</span>
                                                @endif
                                                @if($event->special_event)
                                                    {{ $event->special_event }}
                                                @endif
                                            </small>
                                        </div>
                                        <div class="text-end">
                                            <div class="fw-bold text-success">¥{{ number_format($event->total_revenue, 0) }}</div>
                                            <small class="text-muted">{{ $event->total_customers }} {{ __('messages.customers') }}</small>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-star fs-1 mb-3"></i>
                                <p>{{ __('messages.No holiday/event impact data available') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Comparison -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-bar me-2"></i>{{ __('messages.Monthly Performance Comparison') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($aiAnalysis['time_series_data']['monthly_comparison']['current_month'] && $aiAnalysis['time_series_data']['monthly_comparison']['last_month'])
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="text-center p-3">
                                        <h6 class="text-muted">{{ __('messages.Current Month') }}</h6>
                                        <div class="fw-bold text-success fs-4">¥{{ number_format($aiAnalysis['time_series_data']['monthly_comparison']['current_month']->total_revenue, 0) }}</div>
                                        <small class="text-muted">{{ $aiAnalysis['time_series_data']['monthly_comparison']['current_month']->total_orders }} {{ __('messages.orders') }}</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-center p-3">
                                        <h6 class="text-muted">{{ __('messages.Last Month') }}</h6>
                                        <div class="fw-bold text-info fs-4">¥{{ number_format($aiAnalysis['time_series_data']['monthly_comparison']['last_month']->total_revenue, 0) }}</div>
                                        <small class="text-muted">{{ $aiAnalysis['time_series_data']['monthly_comparison']['last_month']->total_orders }} {{ __('messages.orders') }}</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-center p-3">
                                        <h6 class="text-muted">{{ __('messages.Growth Rate') }}</h6>
                                        @if($aiAnalysis['time_series_data']['monthly_comparison']['growth_rate'])
                                            <div class="fw-bold fs-4 {{ $aiAnalysis['time_series_data']['monthly_comparison']['growth_rate']['revenue_growth'] > 0 ? 'text-success' : 'text-danger' }}">
                                                {{ $aiAnalysis['time_series_data']['monthly_comparison']['growth_rate']['revenue_growth'] > 0 ? '+' : '' }}{{ number_format($aiAnalysis['time_series_data']['monthly_comparison']['growth_rate']['revenue_growth'], 1) }}%
                                            </div>
                                            <small class="text-muted">{{ __('messages.Revenue') }}</small>
                                        @else
                                            <div class="fw-bold text-muted fs-4">-</div>
                                            <small class="text-muted">{{ __('messages.No comparison data') }}</small>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-chart-bar fs-1 mb-3"></i>
                                <p>{{ __('messages.No monthly comparison data available') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.stats-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 1.5rem;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    transition: transform 0.3s ease;
}

.stats-card:hover {
    transform: translateY(-5px);
}

.stats-card .icon {
    font-size: 2rem;
    margin-bottom: 1rem;
}

.stats-card .number {
    font-size: 2rem;
    font-weight: bold;
    margin-bottom: 0.5rem;
}

.stats-card .label {
    font-size: 0.9rem;
    opacity: 0.9;
}

.card {
    border: 1px solid #e9ecef;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.card-header {
    border-bottom: none;
}

.badge {
    font-size: 0.75rem;
    padding: 0.375rem 0.75rem;
}

.weekly-patterns .d-flex {
    padding: 0.5rem 0;
    border-bottom: 1px solid #f8f9fa;
}

.weekly-patterns .d-flex:last-child {
    border-bottom: none;
}

.top-selling-items .d-flex,
.category-performance .d-flex,
.weather-impact .d-flex,
.holiday-impact .d-flex {
    padding: 0.5rem 0;
    border-bottom: 1px solid #f8f9fa;
}

.top-selling-items .d-flex:last-child,
.category-performance .d-flex:last-child,
.weather-impact .d-flex:last-child,
.holiday-impact .d-flex:last-child {
    border-bottom: none;
}

.demand-prediction,
.forecasting-factors {
    background-color: #f8f9fa;
    border-radius: 8px;
}

.table th {
    background-color: #f8f9fa;
    border-top: none;
}

.chart-container {
    position: relative;
    height: 300px;
}
</style>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Daily Sales Chart
    @if(count($aiAnalysis['time_series_data']['daily_sales_trend']) > 0)
        const dailySalesCtx = document.getElementById('dailySalesChart').getContext('2d');
        const dailySalesChart = new Chart(dailySalesCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($aiAnalysis['time_series_data']['daily_sales_trend']->pluck('date')) !!},
                datasets: [{
                    label: '{{ __("messages.Daily Revenue") }}',
                    data: {!! json_encode($aiAnalysis['time_series_data']['daily_sales_trend']->pluck('daily_revenue')) !!},
                    borderColor: 'rgb(75, 192, 192)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    tension: 0.1
                }, {
                    label: '{{ __("messages.Orders") }}',
                    data: {!! json_encode($aiAnalysis['time_series_data']['daily_sales_trend']->pluck('total_orders')) !!},
                    borderColor: 'rgb(255, 99, 132)',
                    backgroundColor: 'rgba(255, 99, 132, 0.2)',
                    tension: 0.1,
                    yAxisID: 'y1'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: '{{ __("messages.Revenue (¥)") }}'
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: '{{ __("messages.Orders") }}'
                        },
                        grid: {
                            drawOnChartArea: false
                        }
                    }
                }
            }
        });
    @endif
});
</script>
@endpush
@endsection
