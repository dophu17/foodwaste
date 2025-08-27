@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="container">
            <h1 class="page-title">📊 Waste Analytics</h1>
            <p class="page-subtitle">Comprehensive analysis of your restaurant's food waste patterns</p>
        </div>
    </div>

    <div class="container">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Statistics Overview -->
        <div class="row mb-4">
            <div class="col-lg-4 col-md-6 mb-3">
                <div class="stats-card text-center">
                    <div class="icon">💰</div>
                    <div class="number">¥{{ number_format($totalWasteCost, 0) }}</div>
                    <div class="label">Total Waste Cost</div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-3">
                <div class="stats-card text-center">
                    <div class="icon">🍽️</div>
                    <div class="number">{{ number_format($totalWasteQuantity, 1) }}g</div>
                    <div class="label">Total Waste Quantity</div>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 mb-3">
                <div class="stats-card text-center">
                    <div class="icon">📝</div>
                    <div class="number">{{ $wasteCount }}</div>
                    <div class="label">Waste Records</div>
                </div>
            </div>
        </div>

        <!-- Waste by Category -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-pie me-2"></i>📊 Waste Analysis by Category
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
                                            <th>Record Count</th>
                                            <th>Average Cost</th>
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
                                                    <span class="badge bg-info">{{ $category->count }}</span>
                                                </td>
                                                <td>
                                                    <span class="text-muted">
                                                        ¥{{ number_format($category->total_cost / $category->count, 0) }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-chart-pie fs-1 mb-3"></i>
                                <p>No waste data available for analysis yet.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Daily Waste Trend -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-line me-2"></i>📈 Daily Waste Trend
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($dailyWaste->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Daily Waste Cost</th>
                                            <th>Trend</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($dailyWaste as $daily)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-calendar text-primary me-2"></i>
                                                        <strong>{{ \Carbon\Carbon::parse($daily->date)->format('M d, Y') }}</strong>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="text-danger fw-bold">
                                                        ¥{{ number_format($daily->daily_cost, 0) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($loop->index > 0)
                                                        @php
                                                            $previousCost = $dailyWaste[$loop->index - 1]->daily_cost;
                                                            $currentCost = $daily->daily_cost;
                                                            $change = $currentCost - $previousCost;
                                                        @endphp
                                                        @if($change > 0)
                                                            <span class="text-danger">
                                                                <i class="fas fa-arrow-up"></i> +¥{{ number_format($change, 0) }}
                                                            </span>
                                                        @elseif($change < 0)
                                                            <span class="text-success">
                                                                <i class="fas fa-arrow-down"></i> ¥{{ number_format(abs($change), 0) }}
                                                            </span>
                                                        @else
                                                            <span class="text-muted">
                                                                <i class="fas fa-minus"></i> No change
                                                            </span>
                                                        @endif
                                                    @else
                                                        <span class="text-muted">First day</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-chart-line fs-1 mb-3"></i>
                                <p>No daily waste data available yet.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Recommendations -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-lightbulb me-2"></i>💡 Waste Reduction Recommendations
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-start">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-utensils text-primary fs-4 me-3"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">Portion Control</h6>
                                        <p class="text-muted mb-0">Analyze customer feedback and adjust portion sizes to reduce waste.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-start">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-chart-line text-success fs-4 me-3"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">Demand Forecasting</h6>
                                        <p class="text-muted mb-0">Use historical data to better predict customer demand and reduce over-preparation.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-start">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-recycle text-warning fs-4 me-3"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">Leftover Management</h6>
                                        <p class="text-muted mb-0">Implement creative ways to use leftover ingredients in daily specials.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-start">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-users text-info fs-4 me-3"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">Staff Training</h6>
                                        <p class="text-muted mb-0">Train kitchen staff on proper food handling and waste prevention techniques.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="row mb-4">
            <div class="col-12 text-center">
                <div class="d-flex justify-content-center gap-3 flex-wrap">
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                                                        <i class="fas fa-arrow-left me-2"></i>{{ __('messages.Back to Dashboard') }}
                    </a>
                    <a href="{{ route('ai.insights') }}" class="btn btn-primary">
                        <i class="fas fa-robot me-2"></i>View AI Insights
                    </a>
                    <a href="{{ route('restaurant.show', $restaurant->id) }}" class="btn btn-success">
                        <i class="fas fa-store me-2"></i>Restaurant Details
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Add interactive features
    document.addEventListener('DOMContentLoaded', function() {
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

        // Add click effects to action buttons
        document.querySelectorAll('.btn').forEach(btn => {
            btn.addEventListener('click', function() {
                this.style.transform = 'scale(0.95)';
                setTimeout(() => {
                    this.style.transform = 'scale(1)';
                }, 150);
            });
        });
    });
</script>
@endpush
