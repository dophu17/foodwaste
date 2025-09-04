@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="container">
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="page-title">📋 {{ __('messages.Order Details') }} #{{ $order->id }}</h1>
                    <p class="page-subtitle">{{ __('messages.View order information and items') }}</p>
                </div>
                <div>
                    <a href="{{ route('orders.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>{{ __('messages.Back to Orders') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Order Summary Card -->
        <div class="row mb-4">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-info-circle me-2"></i>{{ __('messages.Order Information') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="fw-bold text-muted">{{ __('messages.Order ID') }}:</label>
                                    <div class="fs-5 fw-bold">#{{ $order->id }}</div>
                                </div>
                                <div class="mb-3">
                                    <label class="fw-bold text-muted">{{ __('messages.Order Date') }}:</label>
                                    <div>{{ $order->order_date->format('l, F d, Y') }}</div>
                                </div>
                                <div class="mb-3">
                                    <label class="fw-bold text-muted">{{ __('messages.Status') }}:</label>
                                    <span class="badge bg-success">{{ ucfirst($order->status) }}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="fw-bold text-muted">{{ __('messages.Total Amount') }}:</label>
                                    <div class="fs-4 fw-bold text-success">{{ \App\Helpers\CurrencyHelper::format($order->total_amount) }}</div>
                                </div>
                                <div class="mb-3">
                                    <label class="fw-bold text-muted">{{ __('messages.Customer Count') }}:</label>
                                    <div class="fs-5">{{ $order->customer_count }} {{ __('messages.customers') }}</div>
                                </div>
                                <div class="mb-3">
                                    <label class="fw-bold text-muted">{{ __('messages.Average Order Value') }}:</label>
                                    <div class="fs-6">{{ \App\Helpers\CurrencyHelper::format($order->average_order_value) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-chart-line me-2"></i>{{ __('messages.Environmental Factors') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="fw-bold text-muted">{{ __('messages.Day of Week') }}:</label>
                            <div class="badge bg-info bg-opacity-10 text-info">
                                <i class="fas fa-calendar me-1"></i>{{ $order->day_of_week }}
                            </div>
                        </div>
                        @if($order->weather_condition)
                            <div class="mb-3">
                                <label class="fw-bold text-muted">{{ __('messages.Weather') }}:</label>
                                <div class="badge bg-secondary bg-opacity-10 text-secondary">
                                    <i class="fas fa-cloud me-1"></i>{{ $order->weather_condition }}
                                </div>
                            </div>
                        @endif
                        @if($order->is_holiday)
                            <div class="mb-3">
                                <label class="fw-bold text-muted">{{ __('messages.Holiday') }}:</label>
                                <div class="badge bg-warning bg-opacity-10 text-warning">
                                    <i class="fas fa-star me-1"></i>{{ __('messages.Yes') }}
                                </div>
                            </div>
                        @endif
                        @if($order->special_event)
                            <div class="mb-3">
                                <label class="fw-bold text-muted">{{ __('messages.Special Event') }}:</label>
                                <div class="badge bg-primary bg-opacity-10 text-primary">
                                    <i class="fas fa-calendar-star me-1"></i>{{ $order->special_event }}
                                </div>
                            </div>
                        @endif
                        @if($order->notes)
                            <div class="mb-3">
                                <label class="fw-bold text-muted">{{ __('messages.Notes') }}:</label>
                                <div class="small text-muted">{{ $order->notes }}</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Items -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-list me-2"></i>{{ __('messages.Order Items') }} ({{ $order->orderItems->count() }})
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($order->orderItems->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>{{ __('messages.Item') }}</th>
                                            <th>{{ __('messages.Category') }}</th>
                                            <th>{{ __('messages.Unit Price') }}</th>
                                            <th>{{ __('messages.Quantity') }}</th>
                                            <th>{{ __('messages.Total Price') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($order->orderItems as $item)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-utensils text-success me-2"></i>
                                                        <strong>{{ $item->foodItem->name }}</strong>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-primary">{{ $item->foodItem->category }}</span>
                                                </td>
                                                <td>
                                                    <span class="fw-bold">{{ \App\Helpers\CurrencyHelper::format($item->unit_price) }}</span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-info">{{ $item->quantity_sold }}</span>
                                                </td>
                                                <td>
                                                    <span class="fw-bold text-success">{{ \App\Helpers\CurrencyHelper::format($item->total_price) }}</span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-active">
                                            <td colspan="4" class="text-end fw-bold">{{ __('messages.Total') }}:</td>
                                            <td class="fw-bold text-success fs-5">{{ \App\Helpers\CurrencyHelper::format($order->total_amount) }}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        @else
                            <div class="text-center text-muted py-4">
                                <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
                                <p>{{ __('messages.No order items found') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
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

.table th {
    border-top: none;
    font-weight: 600;
    color: #495057;
}

.table td {
    vertical-align: middle;
}

.fs-4 {
    font-size: 1.5rem !important;
}

.fs-5 {
    font-size: 1.25rem !important;
}

.fs-6 {
    font-size: 1rem !important;
}
</style>
@endsection
