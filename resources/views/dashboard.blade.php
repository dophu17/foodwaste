@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
            <div class="page-header">
        <div class="container">
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
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card text-center">
                    <div class="icon">💰</div>
                    <div class="number">{{ number_format($totalRevenue, 0) }}¥</div>
                    <div class="label">{{ __('messages.total_revenue') }}</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card text-center">
                    <div class="icon">🍽️</div>
                    <div class="number">{{ $totalOrders }}</div>
                    <div class="label">{{ __('messages.total_orders') }}</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card text-center">
                    <div class="icon">📊</div>
                    <div class="number">{{ number_format($wastePercentage, 1) }}%</div>
                    <div class="label">{{ __('messages.waste_percentage') }}</div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card text-center">
                    <div class="icon">🤖</div>
                    <div class="number">{{ number_format($aiAccuracy, 1) }}%</div>
                    <div class="label">{{ __('messages.ai_prediction_accuracy') }}</div>
                </div>
            </div>
        </div>

        <!-- AI Insights Section -->
        <div class="row mb-4">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-robot me-2"></i>🤖 {{ __('messages.AI Insights & Recommendations') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        @if(!empty($aiInsights))
                            <div class="row">
                                @foreach($aiInsights as $insight)
                                    <div class="col-md-6 mb-3">
                                        <div class="d-flex align-items-start">
                                            <div class="flex-shrink-0">
                                                <i class="fas fa-lightbulb text-warning fs-4 me-3"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">{{ $insight['title'] }}</h6>
                                                <p class="text-muted mb-0">{{ $insight['description'] }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-robot fs-1 mb-3"></i>
                                <p>{{ __('messages.No AI insights available yet. Continue using the system to generate personalized recommendations!') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-line me-2"></i>📈 {{ __('messages.Quick Actions') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('restaurant.create') }}" class="btn btn-outline-primary">
                                <i class="fas fa-plus me-2"></i>Add Restaurant
                            </a>
                            <a href="{{ route('menu.create') }}" class="btn btn-outline-success">
                                <i class="fas fa-utensils me-2"></i>Create Menu
                            </a>
                            <a href="{{ route('food-item.create') }}" class="btn btn-outline-info">
                                <i class="fas fa-hamburger me-2"></i>Add Food Item
                            </a>
                            <a href="{{ route('waste.analytics') }}" class="btn btn-outline-warning">
                                <i class="fas fa-chart-pie me-2"></i>{{ __('messages.View Analytics') }}
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
                                                            <i class="fas fa-exclamation-triangle me-2"></i>⚠️ {{ __('messages.low_stock_alerts') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($lowStockItems->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Item</th>
                                            <th>Current Stock</th>
                                            <th>Min Level</th>
                                            <th>Status</th>
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
                                                        <span class="badge bg-danger">Out of Stock</span>
                                                    @else
                                                        <span class="badge bg-warning">Low Stock</span>
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
                                <p>All items are well stocked! 🎉</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-history me-2"></i>📝 {{ __('messages.Recent Waste Records') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($recentWasteRecords->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Item</th>
                                            <th>Waste Amount</th>
                                            <th>Cost</th>
                                            <th>Date</th>
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
                                <p>No waste records found. Great job! 🌱</p>
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
                            <i class="fas fa-chart-bar me-2"></i>📊 {{ __('messages.Waste Analysis by Category') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($wasteByCategory->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Category</th>
                                            <th>Total Waste</th>
                                            <th>Total Cost</th>
                                            <th>Percentage</th>
                                            <th>Trend</th>
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
                                                        {{ number_format($category->total_waste, 1) }} {{ $category->waste_unit }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="text-danger fw-bold">
                                                        ¥{{ number_format($category->total_cost, 0) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="progress" style="height: 20px;">
                                                        @php
                                                            $percentage = ($category->total_cost / $totalRevenue) * 100;
                                                        @endphp
                                                        <div class="progress-bar bg-danger" style="width: {{ min($percentage, 100) }}%">
                                                            {{ number_format($percentage, 1) }}%
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    @if($category->ai_predicted_waste > $category->actual_waste_percentage)
                                                        <i class="fas fa-arrow-down text-success" title="Better than predicted"></i>
                                                    @else
                                                        <i class="fas fa-arrow-up text-danger" title="Worse than predicted"></i>
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
                                <p>No waste data available for analysis yet.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
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
    });
</script>
@endpush
