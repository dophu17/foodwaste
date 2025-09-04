@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="container">
        <div class="page-header">
            <h1 class="page-title">🏪 {{ $restaurant->name }}</h1>
            <p class="page-subtitle">
                @if($restaurant->cuisine_type)
                    {{ $restaurant->cuisine_type }} {{ __('messages.Cuisine Type') }} • 
                @endif
                {{ $restaurant->address }}
            </p>
        </div>
    </div>

    <div class="container">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Restaurant Info Card -->
        <div class="row mb-4">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-info-circle me-2"></i>{{ __('messages.Restaurant Details') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-utensils text-primary me-3 fs-4"></i>
                                    <div>
                                        <small class="text-muted">{{ __('messages.Cuisine Type') }}</small>
                                        <div class="fw-bold">
                                            @if($restaurant->cuisine_type)
                                                {{ $restaurant->cuisine_type }}
                                            @else
                                                <span class="text-muted">{{ __('messages.Not specified') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-users text-success me-3 fs-4"></i>
                                    <div>
                                        <small class="text-muted">{{ __('messages.Capacity') }}</small>
                                        <div class="fw-bold">{{ $restaurant->capacity }} {{ __('messages.seats') }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-clock text-warning me-3 fs-4"></i>
                                    <div>
                                        <small class="text-muted">{{ __('messages.Business Hours') }}</small>
                                        <div class="fw-bold">{{ $restaurant->business_hours }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-toggle-on text-info me-3 fs-4"></i>
                                    <div>
                                        <small class="text-muted">{{ __('messages.Status') }}</small>
                                        <div>
                                            @if($restaurant->status == 'active')
                                                <span class="badge bg-success">🟢 {{ __('messages.Active') }}</span>
                                            @elseif($restaurant->status == 'inactive')
                                                <span class="badge bg-danger">🔴 {{ __('messages.Inactive') }}</span>
                                            @else
                                                <span class="badge bg-warning">🔧 {{ __('messages.Maintenance') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        @if($restaurant->description)
                            <hr>
                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-align-left me-2"></i>{{ __('messages.Description') }}
                                </label>
                                <p class="text-muted mb-0">{{ $restaurant->description }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-chart-line me-2"></i> {{ __('messages.Quick Stats') }}
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <div class="display-6 text-success mb-2">{{ $restaurant->menus->count() }}</div>
                            <div class="text-muted">{{ __('messages.Active Menus') }}</div>
                        </div>
                        <div class="text-center mb-3">
                            <div class="display-6 text-info mb-2">{{ $restaurant->foodItems->count() }}</div>
                            <div class="text-muted">{{ __('messages.Food Items') }}</div>
                        </div>
                        <div class="text-center">
                            <div class="display-6 text-warning mb-2">{{ $restaurant->wasteRecords->count() }}</div>
                            <div class="text-muted">{{ __('messages.Waste Records') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-bolt me-2"></i> {{ __('messages.Quick Actions') }}
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-2">
                                <a href="{{ route('menus.create') }}" class="btn btn-outline-primary w-100">
                                    <i class="fas fa-plus me-2"></i>{{ __('messages.Create Menu') }}
                                </a>
                            </div>
                            <div class="col-md-3 mb-2">
                                <a href="{{ route('food-item.create') }}" class="btn btn-outline-success w-100">
                                    <i class="fas fa-hamburger me-2"></i>{{ __('messages.Add Food Item') }}
                                </a>
                            </div>
                            <div class="col-md-3 mb-2">
                                <a href="{{ route('restaurant.edit', $restaurant->id) }}" class="btn btn-outline-warning w-100">
                                    <i class="fas fa-edit me-2"></i>{{ __('messages.Edit Restaurant') }}
                                </a>
                            </div>
                            <div class="col-md-3 mb-2">
                                <a href="{{ route('dashboard') }}" class="btn btn-outline-danger w-100">
                                    <i class="fas fa-chart-pie me-2"></i>{{ __('messages.View Analytics') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Menus Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-utensils me-2"></i> {{ __('messages.Restaurant Menus') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($restaurant->menus->count() > 0)
                            <div class="row">
                                @foreach($restaurant->menus as $menu)
                                    <div class="col-lg-4 col-md-6 mb-3">
                                        <div class="card h-100 border-0 shadow-sm">
                                            <div class="card-body text-center">
                                                <div class="mb-3">
                                                    <i class="fas fa-utensils text-warning fs-1"></i>
                                                </div>
                                                <h6 class="card-title">{{ $menu->name }}</h6>
                                                @if($menu->description)
                                                    <p class="card-text text-muted small">{{ $menu->description }}</p>
                                                @else
                                                    <p class="card-text text-muted small">&nbsp;</p>
                                                @endif
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="badge bg-secondary">{{ $menu->category }}</span>
                                                    <span class="badge {{ $menu->is_active ? 'bg-success' : 'bg-danger' }}">
                                                        {{ $menu->is_active ? '🟢 ' . __('messages.Active') : '🔴 ' . __('messages.Inactive') }}
                                                    </span>
                                                </div>
                                                <div class="mt-3">
                                                    <small class="text-muted">
                                                        <i class="fas fa-calendar me-1"></i>
                                                        @if($menu->valid_from && $menu->valid_until)
                                    {{ $menu->valid_from->format('M d') }} - {{ $menu->valid_until->format('M d, Y') }}
                                @else
                                    {{ __('messages.Unlimited validity') }}
                                @endif
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-utensils fs-1 mb-3"></i>
                                <p>{{ __('messages.No menus created yet. Create your first menu to get started!') }}</p>
                                <a href="{{ route('menus.create') }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>{{ __('messages.Create First Menu') }}
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="row mb-4">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-star me-2"></i> {{ __('messages.Popular Food Items') }}
                        </h6>
                    </div>
                    <div class="card-body">
                        @if($restaurant->foodItems->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>{{ __('messages.Item') }}</th>
                                            <th>{{ __('messages.Price') }}</th>
                                            <th>{{ __('messages.Stock') }}</th>
                                            <th>{{ __('messages.Status') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($restaurant->foodItems->take(5) as $item)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-hamburger text-success me-2"></i>
                                                        <strong>{{ $item->name }}</strong>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="fw-bold text-primary">{{ \App\Helpers\CurrencyHelper::format($item->price) }}</span>
                                                </td>
                                                <td>
                                                    @if($item->stock_quantity <= $item->min_stock_level)
                                                        <span class="badge bg-danger">{{ $item->stock_quantity }}</span>
                                                    @else
                                                        <span class="badge bg-success">{{ $item->stock_quantity }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($item->is_available)
                                                        <span class="badge bg-success">🟢 {{ __('messages.Available') }}</span>
                                                    @else
                                                        <span class="badge bg-danger">🔴 {{ __('messages.Unavailable') }}</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-hamburger fs-1 mb-3"></i>
                                <p>{{ __('messages.No food items added yet.') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header bg-danger text-white">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-chart-line me-2"></i> {{ __('messages.AI Recommendations') }}
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-lightbulb text-warning fs-4 me-3"></i>
                                <div>
                                    <h6 class="mb-1">{{ __('messages.Optimize Portion Sizes') }}</h6>
                                    <p class="text-muted mb-0 small">{{ __('messages.Based on waste analysis, consider reducing portion sizes for popular items.') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-lightbulb text-warning fs-4 me-3"></i>
                                <div>
                                    <h6 class="mb-1">{{ __('messages.Stock Management') }}</h6>
                                    <p class="text-muted mb-0 small">{{ __('messages.Monitor low stock items and set up automatic reorder notifications.') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex align-items-start">
                                <i class="fas fa-lightbulb text-warning fs-4 me-3"></i>
                                <div>
                                    <h6 class="mb-1">{{ __('messages.Menu Optimization') }}</h6>
                                    <p class="text-muted mb-0 small">{{ __('messages.Analyze customer preferences to optimize your menu offerings.') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
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
        // Add hover effects to menu cards
        document.querySelectorAll('.card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-5px)';
                this.style.transition = 'all 0.3s ease';
            });

            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
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

        // Initialize tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endpush
