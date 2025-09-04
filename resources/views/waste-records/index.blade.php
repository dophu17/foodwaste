@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="container">
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="page-title">🗑️ {{ __('messages.Waste Records') }}</h1>
                    <p class="page-subtitle">{{ __('messages.Manage and track food waste records') }}</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('waste-records.create') }}" class="btn btn-success">
                        <i class="fas fa-plus me-2"></i>{{ __('messages.Add New Waste Record') }}
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

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="card-title">{{ $wasteRecords->total() }}</h4>
                                <p class="card-text">{{ __('messages.Total Waste Records') }}</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-list fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card bg-danger text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="card-title">{{ \App\Helpers\CurrencyHelper::format($wasteRecords->sum('cost_wasted')) }}</h4>
                                <p class="card-text">{{ __('messages.Total Cost') }}</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-{{ app()->getLocale() === 'ja' ? 'yen-sign' : (app()->getLocale() === 'vi' ? 'dong-sign' : 'dollar-sign') }} fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="card-title">{{ $wasteRecords->count() > 0 ? \App\Helpers\CurrencyHelper::format($wasteRecords->sum('cost_wasted') / $wasteRecords->count()) : \App\Helpers\CurrencyHelper::format(0) }}</h4>
                                <p class="card-text">{{ __('messages.Average Cost per Record') }}</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-chart-line fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="card-title">{{ $wasteRecords->groupBy('food_item_id')->count() }}</h4>
                                <p class="card-text">{{ __('messages.Items with Waste') }}</p>
                            </div>
                            <div class="align-self-center">
                                <i class="fas fa-hamburger fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search and Filter Bar -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('waste-records.index') }}" class="row g-3">
                    <div class="col-md-3">
                        <input type="date" class="form-control" name="start_date" 
                               value="{{ request('start_date') }}" placeholder="{{ __('messages.From Date') }}">
                    </div>
                    <div class="col-md-3">
                        <input type="date" class="form-control" name="end_date" 
                               value="{{ request('end_date') }}" placeholder="{{ __('messages.To Date') }}">
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" name="food_item_id">
                            <option value="">{{ __('messages.All Food Items') }}</option>
                            @foreach($foodItems as $foodItem)
                                <option value="{{ $foodItem->id }}" 
                                        {{ request('food_item_id') == $foodItem->id ? 'selected' : '' }}>
                                    {{ $foodItem->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <input type="text" class="form-control" name="waste_reason" 
                               placeholder="{{ __('messages.Search by reason') }}" 
                               value="{{ request('waste_reason') }}">
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-search me-1"></i>{{ __('messages.Search') }}
                        </button>
                        <a href="{{ route('waste-records.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-1"></i>{{ __('messages.Clear Filters') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Waste Records Table -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-list me-2"></i>{{ __('messages.Waste Records List') }}
                </h5>
            </div>
            <div class="card-body">
                @if($wasteRecords->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>{{ __('messages.Food Item') }}</th>
                                    <th>{{ __('messages.Waste Date') }}</th>
                                    <th>{{ __('messages.Quantity Wasted') }}</th>
                                    <th>{{ __('messages.Cost Wasted') }}</th>
                                    <th>{{ __('messages.Waste Reason') }}</th>
                                    <th>{{ __('messages.AI Analysis') }}</th>
                                    <th>{{ __('messages.Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($wasteRecords as $record)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-hamburger text-primary me-2"></i>
                                                <div>
                                                    <strong>{{ $record->foodItem->name }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $record->foodItem->menu->name }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">
                                                {{ $record->waste_date->format('M d, Y') }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="fw-bold">{{ number_format($record->quantity_wasted, 2) }}</span>
                                            <small class="text-muted">{{ $record->waste_unit }}</small>
                                        </td>
                                        <td>
                                            <span class="text-danger fw-bold">
                                                {{ \App\Helpers\CurrencyHelper::format($record->cost_wasted) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-warning text-dark">
                                                {{ $record->waste_reason }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($record->ai_predicted_waste || $record->prediction_accuracy)
                                                <div class="small">
                                                    @if($record->ai_predicted_waste)
                                                        <div class="text-info">
                                                            <i class="fas fa-robot me-1"></i>
                                                            {{ __('messages.AI Predicted') }}: {{ number_format($record->ai_predicted_waste, 1) }}
                                                        </div>
                                                    @endif
                                                    @if($record->prediction_accuracy)
                                                        <div class="text-success">
                                                            <i class="fas fa-bullseye me-1"></i>
                                                            {{ number_format($record->prediction_accuracy, 1) }}% {{ __('messages.accuracy') }}
                                                        </div>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-muted small">{{ __('messages.No AI data') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <a href="{{ route('waste-records.show', $record) }}" 
                                                   class="btn-action btn-view" title="{{ __('messages.View') }}">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('waste-records.edit', $record) }}" 
                                                   class="btn-action btn-edit" title="{{ __('messages.Edit') }}">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('waste-records.destroy', $record) }}" 
                                                      method="POST" class="d-inline"
                                                      onsubmit="return confirm('{{ __('messages.Are you sure you want to delete this waste record?') }}')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn-action btn-delete" 
                                                            title="{{ __('messages.Delete') }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $wasteRecords->appends(request()->query())->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-leaf text-success fa-4x mb-3"></i>
                        <h4 class="text-muted">{{ __('messages.No waste records found') }}</h4>
                        <p class="text-muted">{{ __('messages.Create your first waste record to get started') }}</p>
                        <a href="{{ route('waste-records.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>{{ __('messages.Add New Waste Record') }}
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
.action-buttons {
    display: flex;
    gap: 4px;
    align-items: center;
}

.btn-action {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border: 1px solid transparent;
    border-radius: 6px;
    text-decoration: none;
    font-size: 12px;
    transition: all 0.2s ease;
    cursor: pointer;
    background: #fff;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.btn-action:hover {
    transform: translateY(-1px);
    box-shadow: 0 3px 8px rgba(0,0,0,0.15);
    text-decoration: none;
}

.btn-view {
    color: #17a2b8;
    border-color: #17a2b8;
}

.btn-view:hover {
    background-color: #17a2b8;
    color: white;
}

.btn-edit {
    color: #ffc107;
    border-color: #ffc107;
}

.btn-edit:hover {
    background-color: #ffc107;
    color: white;
}

.btn-delete {
    color: #dc3545;
    border-color: #dc3545;
    background: #fff;
}

.btn-delete:hover {
    background-color: #dc3545;
    color: white;
}

.btn-action i {
    font-size: 11px;
}
</style>
@endsection