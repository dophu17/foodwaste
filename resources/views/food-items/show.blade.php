@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="container">
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="page-title">🍽️ {{ $foodItem->name }}</h1>
                    <p class="page-subtitle">
                        {{ $foodItem->category }} • 
                        {{ $foodItem->menu->name }} • 
                        {{ $foodItem->restaurant->name }}
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('food-item.edit', $foodItem->id) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-2"></i>{{ __('messages.Edit Food Item') }}
                    </a>
                    <a href="{{ route('food-item.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>{{ __('messages.Back to Food Items') }}
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

        <div class="row">
            <div class="col-lg-8">
                <div class="card">

                <div class="card-body">
                    <div class="row">
                        <!-- Food Item Image -->
                        <div class="col-md-4">
                            @if($foodItem->image_path)
                                <img src="{{ Storage::url($foodItem->image_path) }}" 
                                     alt="{{ $foodItem->name }}" 
                                     class="img-fluid rounded shadow-sm">
                            @else
                                <div class="bg-light d-flex align-items-center justify-content-center rounded shadow-sm" 
                                     style="height: 300px;">
                                    <div class="text-center text-muted">
                                        <i class="fas fa-image fa-4x mb-3"></i>
                                        <p>{{ __('messages.No image') }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Food Item Details -->
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-6">
                                    <h5 class="text-primary mb-3">
                                        <i class="fas fa-info-circle me-2"></i>
                                        {{ __('messages.Basic Information') }}
                                    </h5>
                                    
                                    <table class="table table-borderless">
                                        <tr>
                                            <td class="fw-bold">{{ __('messages.Food Name') }}:</td>
                                            <td>{{ $foodItem->name }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">{{ __('messages.Menu') }}:</td>
                                            <td>
                                                <a href="{{ route('menus.show', $foodItem->menu_id) }}" 
                                                   class="text-decoration-none">
                                                    {{ $foodItem->menu->name }}
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">{{ __('messages.Category') }}:</td>
                                            <td>
                                                <span class="badge bg-secondary">{{ $foodItem->category }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">{{ __('messages.Style') }}:</td>
                                            <td>{{ $foodItem->cuisine_style }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">{{ __('messages.Price') }}:</td>
                                            <td>
                                                <span class="h5 text-primary mb-0">{{ $foodItem->formatted_price }}</span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>

                                <div class="col-md-6">
                                    <h5 class="text-success mb-3">
                                        <i class="fas fa-list-alt me-2"></i>
                                        {{ __('messages.Additional Information') }}
                                    </h5>
                                    
                                    <table class="table table-borderless">
                                        <tr>
                                            <td class="fw-bold">{{ __('messages.Preparation Time') }}:</td>
                                            <td>{{ $foodItem->preparation_time }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">{{ __('messages.Stock') }}:</td>
                                            <td>
                                                <span class="badge {{ $foodItem->stock_status_class }}">
                                                    {{ $foodItem->stock_status }}
                                                </span>
                                                <br>
                                                <small class="text-muted">
                                                    {{ $foodItem->stock_quantity }}/{{ $foodItem->min_stock_level }}
                                                </small>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">{{ __('messages.Status') }}:</td>
                                            <td>
                                                @if($foodItem->is_available)
                                                    <span class="badge bg-success">{{ __('messages.Available') }}</span>
                                                @else
                                                    <span class="badge bg-danger">{{ __('messages.Unavailable') }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">{{ __('messages.Created Date') }}:</td>
                                            <td>{{ $foodItem->created_at->format('d/m/Y H:i') }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">{{ __('messages.Last Updated') }}:</td>
                                            <td>{{ $foodItem->updated_at->format('d/m/Y H:i') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>



                            <!-- Description -->
                            @if($foodItem->description)
                                <div class="row mt-4">
                                    <div class="col-12">
                                        <h5 class="text-dark mb-3">
                                            <i class="fas fa-align-left me-2"></i>
                                            {{ __('messages.Description') }}
                                        </h5>
                                        <p class="text-muted">{{ $foodItem->description }}</p>
                                    </div>
                                </div>
                            @endif

                            <!-- Ingredients -->
                            @if($foodItem->ingredients)
                                <div class="row mt-4">
                                    <div class="col-12">
                                        <h5 class="text-dark mb-3">
                                            <i class="fas fa-list me-2"></i>
                                            {{ __('messages.Ingredients') }}
                                        </h5>
                                        <p class="text-muted">{{ $foodItem->ingredients }}</p>
                                    </div>
                                </div>
                            @endif

                            <!-- Allergens -->
                            @if($foodItem->allergens)
                                <div class="row mt-4">
                                    <div class="col-12">
                                        <h5 class="text-warning mb-3">
                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                            {{ __('messages.Allergen Information') }}
                                        </h5>
                                        <p class="text-muted">{{ $foodItem->allergens }}</p>
                                    </div>
                                </div>
                            @endif

                            <!-- AI Waste Prediction -->
                            <!-- AI Waste Prediction Section -->
                            <div class="row mt-4">
                                <div class="col-12">
                                    <h5 class="text-purple mb-3">
                                        <i class="fas fa-robot me-2"></i>
                                        {{ __('messages.AI Waste Prediction') }}
                                    </h5>
                                    
                                    @if($foodItem->ai_waste_prediction)
                                        <!-- Hiển thị AI prediction khi có dữ liệu -->
                                        <div class="alert alert-info">
                                            <strong>{{ __('messages.Prediction Rate') }}:</strong> {{ $foodItem->ai_waste_prediction }}%
                                            <br>
                                            <strong>{{ __('messages.Analysis') }}:</strong> {{ $foodItem->getWasteInsights() }}
                                        </div>
                                    @else
                                        <!-- Hiển thị thông báo nhắc nhở khi không có dữ liệu -->
                                        <div class="alert alert-warning">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-exclamation-triangle me-3 fs-4"></i>
                                                <div>
                                                    <strong>{{ __('messages.No AI prediction yet') }}</strong>
                                                    <br>
                                                    <small class="text-muted">
                                                        {{ __('messages.To calculate AI waste prediction, please ensure all required factors') }}:
                                                    </small>
                                                    <ul class="mb-0 mt-2">
                                                        @if(empty($foodItem->category))
                                                            <li><i class="fas fa-times text-danger me-2"></i><strong>{{ __('messages.Category (Category) not set') }}</strong></li>
                                                        @else
                                                            <li><i class="fas fa-check text-success me-2"></i>{{ __('messages.Category') }}: {{ $foodItem->category }}</li>
                                                        @endif
                                                        
                                                        @if(empty($foodItem->preparation_time))
                                                            <li><i class="fas fa-times text-danger me-2"></i><strong>{{ __('messages.Preparation time not set') }}</strong></li>
                                                        @else
                                                            <li><i class="fas fa-check text-success me-2"></i>{{ __('messages.Preparation Time') }}: {{ $foodItem->preparation_time }}</li>
                                                        @endif
                                                        
                                                        @if(empty($foodItem->price) || $foodItem->price <= 0)
                                                            <li><i class="fas fa-times text-danger me-2"></i><strong>{{ __('messages.Price not set or invalid') }}</strong></li>
                                                        @else
                                                            <li><i class="fas fa-check text-success me-2"></i>{{ __('messages.Price') }}: {{ \App\Helpers\CurrencyHelper::format($foodItem->price) }}</li>
                                                        @endif
                                                        
                                                        @if($foodItem->stock_quantity < 0)
                                                            <li><i class="fas fa-times text-danger me-2"></i><strong>{{ __('messages.Stock quantity invalid') }}</strong></li>
                                                        @else
                                                            <li><i class="fas fa-check text-success me-2"></i>{{ __('messages.Stock') }}: {{ $foodItem->stock_quantity }}</li>
                                                        @endif
                                                        
                                                        @if($foodItem->min_stock_level < 0)
                                                            <li><i class="fas fa-times text-danger me-2"></i><strong>{{ __('messages.Minimum stock level invalid') }}</strong></li>
                                                        @else
                                                            <li><i class="fas fa-check text-success me-2"></i>{{ __('messages.Minimum stock level') }}: {{ $foodItem->min_stock_level }}</li>
                                                        @endif
                                                    </ul>
                                                    
                                                    @php
                                                        $missingFactors = [];
                                                        if (empty($foodItem->category)) $missingFactors[] = 'category';
                                                        if (empty($foodItem->preparation_time)) $missingFactors[] = 'preparation_time';
                                                        if (empty($foodItem->price) || $foodItem->price <= 0) $missingFactors[] = 'price';
                                                        if ($foodItem->stock_quantity < 0) $missingFactors[] = 'stock_quantity';
                                                        if ($foodItem->min_stock_level < 0) $missingFactors[] = 'min_stock_level';
                                                    @endphp
                                                    
                                                    @if(!empty($missingFactors))
                                                        <div class="mt-3">
                                                            <a href="{{ route('food-item.edit', $foodItem->id) }}" class="btn btn-warning btn-sm">
                                                                <i class="fas fa-edit me-2"></i>{{ __('messages.Update missing information') }}
                                                            </a>
                                                        </div>
                                                    @else
                                                        <div class="mt-3">
                                                            <form method="POST" action="{{ route('food-item.calculate-ai', $foodItem->id) }}" class="d-inline">
                                                                @csrf
                                                                <button type="submit" class="btn btn-primary btn-sm">
                                                                    <i class="fas fa-calculator me-2"></i>{{ __('messages.Calculate AI Prediction') }}
                                                                </button>
                                                            </form>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Restaurant Information -->
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-store me-2"></i>
                        {{ __('messages.Restaurant Information') }}
                    </h6>
                </div>
                <div class="card-body">
                    <h6>{{ $foodItem->menu->restaurant->name }}</h6>
                    <p class="text-muted mb-2">
                        <i class="fas fa-map-marker-alt me-1"></i>
                        {{ $foodItem->menu->restaurant->address }}
                    </p>
                    <p class="text-muted mb-0">
                        <i class="fas fa-phone me-1"></i>
                        {{ $foodItem->menu->restaurant->phone }}
                    </p>
                </div>
            </div>

            <!-- Menu Information -->
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-clipboard-list me-2"></i>
                        {{ __('messages.Menu Information') }}
                    </h6>
                </div>
                <div class="card-body">
                    <h6>{{ $foodItem->menu->name }}</h6>
                    <p class="text-muted mb-2">
                        <i class="fas fa-tag me-1"></i>
                        {{ $foodItem->menu->category ?? 'Không phân loại' }}
                    </p>
                    <p class="text-muted mb-0">
                        <i class="fas fa-clock me-1"></i>
                        {{ $foodItem->menu->status_text }}
                    </p>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">
                        <i class="fas fa-tools me-2"></i>
                        {{ __('messages.Quick Actions') }}
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <form action="{{ route('food-item.toggle-availability', $foodItem->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline-secondary w-100">
                                <i class="fas fa-{{ $foodItem->is_available ? 'eye-slash' : 'eye' }} me-1"></i>
                                {{ $foodItem->is_available ? __('messages.Hide Food Item') : __('messages.Show Food Item') }}
                            </button>
                        </form>
                        
                        <button type="button" class="btn btn-outline-primary w-100" 
                                data-bs-toggle="modal" data-bs-target="#updateStockModal">
                            <i class="fas fa-boxes me-1"></i>
                            {{ __('messages.Update Stock') }}
                        </button>
                        
                        <form action="{{ route('food-item.destroy', $foodItem->id) }}" method="POST" 
                              onsubmit="return confirm('{{ __('messages.Are you sure you want to delete this food item?') }}')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger w-100">
                                <i class="fas fa-trash me-1"></i>
                                {{ __('messages.Delete Food Item') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Update Stock Modal -->
<div class="modal fade" id="updateStockModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-boxes me-2"></i>
                    {{ __('messages.Update Stock') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('food-item.update-stock', $foodItem->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="stock_quantity" class="form-label">{{ __('messages.Stock Quantity') }}</label>
                        <input type="number" name="stock_quantity" id="stock_quantity" 
                               class="form-control" value="{{ $foodItem->stock_quantity }}" 
                               min="0" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('messages.Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('messages.Update') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
