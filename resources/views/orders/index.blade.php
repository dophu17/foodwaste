@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="container">
        <div class="page-header">
            <h1 class="page-title">📋 {{ __('messages.Orders') }}</h1>
            <p class="page-subtitle">{{ __('messages.Manage and track restaurant orders') }}</p>
        </div>
    </div>

    <div class="container">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Search and Filter Bar -->
        <div class="card mb-4">
            <div class="card-body">
                <form action="{{ route('orders.index') }}" method="GET" class="row g-3">
                    <div class="col-md-3">
                        <input type="text" class="form-control" name="search" 
                               placeholder="{{ __('messages.Search orders...') }}" 
                               value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <input type="date" class="form-control" name="date_from" 
                               value="{{ request('date_from') }}" placeholder="From Date">
                    </div>
                    <div class="col-md-2">
                        <input type="date" class="form-control" name="date_to" 
                               value="{{ request('date_to') }}" placeholder="To Date">
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" name="day_of_week">
                            <option value="">{{ __('messages.All Days') }}</option>
                            @foreach($dayOfWeeks as $day)
                                <option value="{{ $day }}" {{ request('day_of_week') == $day ? 'selected' : '' }}>
                                    {{ $day }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" name="weather">
                            <option value="">{{ __('messages.All Weather') }}</option>
                            @foreach($weatherConditions as $weather)
                                <option value="{{ $weather }}" {{ request('weather') == $weather ? 'selected' : '' }}>
                                    {{ $weather }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-1">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h5 class="mb-0">{{ __('messages.Total Orders') }}: {{ $orders->total() }}</h5>
            </div>
            <div>
                <a href="{{ route('orders.create') }}" class="btn btn-success me-2">
                    <i class="fas fa-plus me-2"></i>{{ __('messages.Create New Order') }}
                </a>
                <a href="{{ route('orders.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times me-2"></i>{{ __('messages.Clear Filters') }}
                </a>
            </div>
        </div>

        <!-- Orders List -->
        <div class="orders-list">
            @forelse($orders as $order)
                <div class="order-item mb-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <!-- Order ID and Date -->
                                <div class="col-lg-2 col-md-3">
                                    <div class="d-flex align-items-center">
                                        <div class="order-icon me-3">
                                            <i class="fas fa-receipt text-primary"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-1 fw-bold">#{{ $order->id }}</h6>
                                            <small class="text-muted">{{ $order->order_date->format('M d, Y') }}</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Order Details -->
                                <div class="col-lg-2 col-md-3">
                                    <div class="mb-2">
                                        <span class="badge bg-success bg-opacity-10 text-success">
                                            <i class="fas fa-yen-sign me-1"></i>{{ number_format($order->total_amount, 0) }}
                                        </span>
                                    </div>
                                    <div class="small">
                                        <i class="fas fa-users me-1"></i>{{ $order->customer_count }} {{ __('messages.customers') }}
                                    </div>
                                </div>
                                
                                <!-- Day and Weather -->
                                <div class="col-lg-2 col-md-3">
                                    <div class="mb-2">
                                        <span class="badge bg-info bg-opacity-10 text-info">
                                            <i class="fas fa-calendar me-1"></i>{{ $order->day_of_week }}
                                        </span>
                                    </div>
                                    @if($order->weather_condition)
                                        <div class="small">
                                            <i class="fas fa-cloud me-1"></i>{{ $order->weather_condition }}
                                        </div>
                                    @endif
                                </div>
                                
                                <!-- Special Events -->
                                <div class="col-lg-2 col-md-3">
                                    @if($order->is_holiday)
                                        <span class="badge bg-warning bg-opacity-10 text-warning">
                                            <i class="fas fa-star me-1"></i>{{ __('messages.Holiday') }}
                                        </span>
                                    @endif
                                    @if($order->special_event)
                                        <div class="small text-muted">
                                            <i class="fas fa-calendar-star me-1"></i>{{ $order->special_event }}
                                        </div>
                                    @endif
                                </div>
                                
                                <!-- Order Items Count -->
                                <div class="col-lg-2 col-md-3">
                                    <div class="mb-2">
                                        <span class="badge bg-primary bg-opacity-10 text-primary">
                                            <i class="fas fa-list me-1"></i>{{ $order->orderItems->count() }} {{ __('messages.items') }}
                                        </span>
                                    </div>
                                    <div class="small text-muted">
                                        {{ __('messages.AVG') }}: ¥{{ number_format($order->average_order_value, 0) }}
                                    </div>
                                </div>
                                
                                <!-- Actions -->
                                <div class="col-lg-2 col-md-3 text-end">
                                    <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye me-1"></i>{{ __('messages.View') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-5">
                    <div class="empty-state">
                        <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">{{ __('messages.No orders found') }}</h5>
                        <p class="text-muted">{{ __('messages.Orders will appear here when they are created') }}</p>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($orders->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $orders->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>

<style>
.order-item {
    transition: all 0.2s ease;
}

.order-item:hover .card {
    background-color: #f8f9fa;
}

.order-icon {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #e3f2fd;
    border-radius: 8px;
}

.empty-state {
    padding: 2rem;
}

.badge {
    font-size: 0.75rem;
    padding: 0.375rem 0.75rem;
}

.card {
    border: 1px solid #e9ecef;
    border-radius: 8px;
}

.card-body {
    padding: 1.25rem;
}

@media (max-width: 768px) {
    .order-item .row > div {
        margin-bottom: 1rem;
    }
    
    .order-item .row > div:last-child {
        margin-bottom: 0;
    }
}
</style>
@endsection
