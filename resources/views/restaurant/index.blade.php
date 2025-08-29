@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="container">
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="page-title">🏪 {{ __('messages.restaurant') }}</h1>
                    <p class="page-subtitle">{{ __('messages.Manage your restaurant information and settings') }}</p>
                </div>
                <div>
                    @if($restaurant)
                        <a href="{{ route('restaurant.edit', $restaurant) }}" class="btn btn-primary">
                            <i class="fas fa-edit me-2"></i>{{ __('messages.Edit Restaurant') }}
                        </a>
                    @else
                        <a href="{{ route('restaurant.create') }}" class="btn btn-success">
                            <i class="fas fa-plus me-2"></i>{{ __('messages.Create Restaurant') }}
                        </a>
                    @endif
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

        @if($restaurant)
            <!-- Restaurant Overview -->
            <div class="row mb-4">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-store me-2"></i>{{ __('messages.Restaurant Information') }}
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="fw-bold text-muted">{{ __('messages.Name') }}:</label>
                                        <div class="fs-5">{{ $restaurant->name }}</div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="fw-bold text-muted">{{ __('messages.Cuisine Type') }}:</label>
                                        <div>
                                            @if($restaurant->cuisine_type)
                                                <span class="badge bg-info">{{ $restaurant->cuisine_type }}</span>
                                            @else
                                                <span class="text-muted">{{ __('messages.Not specified') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="fw-bold text-muted">{{ __('messages.Status') }}:</label>
                                        <div>
                                            <span class="badge bg-success">{{ ucfirst($restaurant->status) }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="fw-bold text-muted">{{ __('messages.Address') }}:</label>
                                        <div>{{ $restaurant->address ?: __('messages.Not specified') }}</div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="fw-bold text-muted">{{ __('messages.Phone') }}:</label>
                                        <div>{{ $restaurant->phone ?: __('messages.Not specified') }}</div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="fw-bold text-muted">{{ __('messages.Email') }}:</label>
                                        <div>{{ $restaurant->email ?: __('messages.Not specified') }}</div>
                                    </div>
                                </div>
                            </div>
                            
                            @if($restaurant->description)
                                <div class="mb-3">
                                    <label class="fw-bold text-muted">{{ __('messages.Description') }}:</label>
                                    <div class="text-muted">{{ $restaurant->description }}</div>
                                </div>
                            @endif

                            @if($restaurant->business_hours)
                                <div class="mb-3">
                                    <label class="fw-bold text-muted">{{ __('messages.Business Hours') }}:</label>
                                    <div class="text-muted">{{ $restaurant->business_hours }}</div>
                                </div>
                            @endif

                            <div class="mb-3">
                                <label class="fw-bold text-muted">{{ __('messages.Capacity') }}:</label>
                                <div>{{ $restaurant->capacity ?: __('messages.Not specified') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-chart-line me-2"></i>{{ __('messages.Restaurant Statistics') }}
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span>{{ __('messages.Total Menus') }}:</span>
                                    <span class="badge bg-primary">{{ $restaurant->menus->count() }}</span>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span>{{ __('messages.Total Food Items') }}:</span>
                                    <span class="badge bg-info">{{ $restaurant->foodItems->count() }}</span>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span>{{ __('messages.Total Orders') }}:</span>
                                    <span class="badge bg-success">{{ $restaurant->orders->count() }}</span>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span>{{ __('messages.Total Revenue') }}:</span>
                                    <span class="badge bg-warning">¥{{ number_format($restaurant->getTotalRevenue(), 0) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- AI Insights for Restaurant -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-robot me-2"></i>{{ __('messages.AI Restaurant Insights') }}
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="text-primary">{{ __('messages.Performance Analysis') }}</h6>
                                    <ul class="list-unstyled">
                                        <li class="mb-2">
                                            <i class="fas fa-chart-line text-success me-2"></i>
                                            <strong>{{ __('messages.Revenue Trend') }}:</strong>
                                            @php
                                                $currentMonth = \Carbon\Carbon::now()->startOfMonth();
                                                $lastMonth = \Carbon\Carbon::now()->subMonth()->startOfMonth();
                                                $currentRevenue = $restaurant->getTotalRevenue($currentMonth);
                                                $lastRevenue = $restaurant->getTotalRevenue($lastMonth);
                                                $growth = $lastRevenue > 0 ? (($currentRevenue - $lastRevenue) / $lastRevenue) * 100 : 0;
                                            @endphp
                                            @if($growth > 0)
                                                <span class="text-success">+{{ number_format($growth, 1) }}%</span>
                                            @else
                                                <span class="text-danger">{{ number_format($growth, 1) }}%</span>
                                            @endif
                                        </li>
                                        <li class="mb-2">
                                            <i class="fas fa-users text-info me-2"></i>
                                            <strong>{{ __('messages.Customer Growth') }}:</strong>
                                            @php
                                                $currentCustomers = $restaurant->getTotalCustomers($currentMonth);
                                                $lastCustomers = $restaurant->getTotalCustomers($lastMonth);
                                                $customerGrowth = $lastCustomers > 0 ? (($currentCustomers - $lastCustomers) / $lastCustomers) * 100 : 0;
                                            @endphp
                                            @if($customerGrowth > 0)
                                                <span class="text-success">+{{ number_format($customerGrowth, 1) }}%</span>
                                            @else
                                                <span class="text-danger">{{ number_format($customerGrowth, 1) }}%</span>
                                            @endif
                                        </li>
                                        <li class="mb-2">
                                            <i class="fas fa-utensils text-warning me-2"></i>
                                            <strong>{{ __('messages.Menu Performance') }}:</strong>
                                            @php
                                                $activeMenus = $restaurant->menus()->where('is_active', true)->count();
                                                $totalMenus = $restaurant->menus()->count();
                                                $menuUtilization = $totalMenus > 0 ? ($activeMenus / $totalMenus) * 100 : 0;
                                            @endphp
                                            <span class="text-info">{{ number_format($menuUtilization, 1) }}% {{ __('messages.active') }}</span>
                                        </li>
                                    </ul>
                                </div>
                                
                                <div class="col-md-6">
                                    <h6 class="text-success">{{ __('messages.AI Recommendations') }}</h6>
                                    <ul class="list-unstyled">
                                        @if($restaurant->menus()->count() == 0)
                                            <li class="mb-2">
                                                <i class="fas fa-lightbulb text-warning me-2"></i>
                                                <strong>{{ __('messages.Create Your First Menu') }}</strong>
                                                <br><small class="text-muted">{{ __('messages.Start by creating a menu to track food items and sales') }}</small>
                                            </li>
                                        @endif
                                        
                                        @if($restaurant->foodItems()->count() < 10)
                                            <li class="mb-2">
                                                <i class="fas fa-lightbulb text-info me-2"></i>
                                                <strong>{{ __('messages.Expand Food Items') }}</strong>
                                                <br><small class="text-muted">{{ __('messages.Add more food items to increase menu variety and sales opportunities') }}</small>
                                            </li>
                                        @endif
                                        
                                        @if($restaurant->orders()->count() < 5)
                                            <li class="mb-2">
                                                <i class="fas fa-lightbulb text-success me-2"></i>
                                                <strong>{{ __('messages.Start Recording Orders') }}</strong>
                                                <br><small class="text-muted">{{ __('messages.Begin recording orders to generate AI insights and improve forecasting') }}</small>
                                            </li>
                                        @endif
                                        
                                        @if($restaurant->orders()->count() >= 10)
                                            <li class="mb-2">
                                                <i class="fas fa-lightbulb text-primary me-2"></i>
                                                <strong>{{ __('messages.Ready for AI Analysis') }}</strong>
                                                <br><small class="text-muted">{{ __('messages.You have enough data for AI-powered insights and demand forecasting') }}</small>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
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
                            <h5 class="card-title mb-0">
                                <i class="fas fa-bolt me-2"></i>{{ __('messages.Quick Actions') }}
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <a href="{{ route('menus.create') }}" class="btn btn-outline-success w-100">
                                        <i class="fas fa-plus me-2"></i>{{ __('messages.Create Menu') }}
                                    </a>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <a href="{{ route('food-item.create') }}" class="btn btn-outline-primary w-100">
                                        <i class="fas fa-hamburger me-2"></i>{{ __('messages.Add Food Item') }}
                                    </a>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <a href="{{ route('orders.create') }}" class="btn btn-outline-warning w-100">
                                        <i class="fas fa-receipt me-2"></i>{{ __('messages.Create Order') }}
                                    </a>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <a href="{{ route('ai.analysis') }}" class="btn btn-outline-danger w-100">
                                        <i class="fas fa-robot me-2"></i>{{ __('messages.AI Analysis') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        @else
            <!-- No Restaurant Created -->
            <div class="text-center py-5">
                <div class="empty-state">
                    <i class="fas fa-store fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">{{ __('messages.No Restaurant Created Yet') }}</h5>
                    <p class="text-muted">{{ __('messages.Create your restaurant profile to get started with food waste management and AI insights') }}</p>
                    <a href="{{ route('restaurant.create') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-plus me-2"></i>{{ __('messages.Create Restaurant') }}
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>

<style>
.empty-state {
    padding: 2rem;
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

.list-unstyled li {
    padding: 0.5rem 0;
    border-bottom: 1px solid #f8f9fa;
}

.list-unstyled li:last-child {
    border-bottom: none;
}
</style>
@endsection
