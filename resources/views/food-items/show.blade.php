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
                                        <p>Không có ảnh</p>
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
                                        Thông tin cơ bản
                                    </h5>
                                    
                                    <table class="table table-borderless">
                                        <tr>
                                            <td class="fw-bold">Tên món:</td>
                                            <td>{{ $foodItem->name }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Menu:</td>
                                            <td>
                                                <a href="{{ route('menus.show', $foodItem->menu_id) }}" 
                                                   class="text-decoration-none">
                                                    {{ $foodItem->menu->name }}
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Danh mục:</td>
                                            <td>
                                                <span class="badge bg-secondary">{{ $foodItem->category }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Phong cách:</td>
                                            <td>{{ $foodItem->cuisine_style }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Giá:</td>
                                            <td>
                                                <span class="h5 text-primary mb-0">{{ $foodItem->formatted_price }}</span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>

                                <div class="col-md-6">
                                    <h5 class="text-success mb-3">
                                        <i class="fas fa-list-alt me-2"></i>
                                        Thông tin bổ sung
                                    </h5>
                                    
                                    <table class="table table-borderless">
                                        <tr>
                                            <td class="fw-bold">Thời gian chuẩn bị:</td>
                                            <td>{{ $foodItem->preparation_time }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Tồn kho:</td>
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
                                            <td class="fw-bold">Trạng thái:</td>
                                            <td>
                                                @if($foodItem->is_available)
                                                    <span class="badge bg-success">Có sẵn</span>
                                                @else
                                                    <span class="badge bg-danger">Không có sẵn</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Ngày tạo:</td>
                                            <td>{{ $foodItem->created_at->format('d/m/Y H:i') }}</td>
                                        </tr>
                                        <tr>
                                            <td class="fw-bold">Cập nhật lần cuối:</td>
                                            <td>{{ $foodItem->updated_at->format('d/m/Y H:i') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>

                            <!-- Dietary Information -->
                            <div class="row mt-4">
                                <div class="col-12">
                                    <h5 class="text-info mb-3">
                                        <i class="fas fa-leaf me-2"></i>
                                        Thông tin ăn kiêng
                                    </h5>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="d-flex align-items-center">
                                                @if($foodItem->is_vegetarian)
                                                    <i class="fas fa-seedling text-success me-2"></i>
                                                    <span class="badge bg-success">Chay</span>
                                                @else
                                                    <i class="fas fa-times text-muted me-2"></i>
                                                    <span class="badge bg-secondary">Không chay</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="d-flex align-items-center">
                                                @if($foodItem->is_vegan)
                                                    <i class="fas fa-leaf text-info me-2"></i>
                                                    <span class="badge bg-info">Thuần chay</span>
                                                @else
                                                    <i class="fas fa-times text-muted me-2"></i>
                                                    <span class="badge bg-secondary">Không thuần chay</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="d-flex align-items-center">
                                                @if($foodItem->is_gluten_free)
                                                    <i class="fas fa-wheat-awn text-warning me-2"></i>
                                                    <span class="badge bg-warning">Không gluten</span>
                                                @else
                                                    <i class="fas fa-times text-muted me-2"></i>
                                                    <span class="badge bg-secondary">Có gluten</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Description -->
                            @if($foodItem->description)
                                <div class="row mt-4">
                                    <div class="col-12">
                                        <h5 class="text-dark mb-3">
                                            <i class="fas fa-align-left me-2"></i>
                                            Mô tả
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
                                            Nguyên liệu
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
                                            Thông tin dị ứng
                                        </h5>
                                        <p class="text-muted">{{ $foodItem->allergens }}</p>
                                    </div>
                                </div>
                            @endif

                            <!-- AI Waste Prediction -->
                            @if($foodItem->ai_waste_prediction)
                                <div class="row mt-4">
                                    <div class="col-12">
                                        <h5 class="text-purple mb-3">
                                            <i class="fas fa-robot me-2"></i>
                                            Dự đoán thất thoát AI
                                        </h5>
                                        <div class="alert alert-info">
                                            <strong>Tỷ lệ dự đoán:</strong> {{ $foodItem->ai_waste_prediction }}%
                                            <br>
                                            <strong>Phân tích:</strong> {{ $foodItem->getWasteInsights() }}
                                        </div>
                                    </div>
                                </div>
                            @endif
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
                        Thông tin nhà hàng
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
                        Thông tin menu
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
                        Thao tác nhanh
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <form action="{{ route('food-item.toggle-availability', $foodItem->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-outline-secondary w-100">
                                <i class="fas fa-{{ $foodItem->is_available ? 'eye-slash' : 'eye' }} me-1"></i>
                                {{ $foodItem->is_available ? 'Ẩn món ăn' : 'Hiện món ăn' }}
                            </button>
                        </form>
                        
                        <button type="button" class="btn btn-outline-primary w-100" 
                                data-bs-toggle="modal" data-bs-target="#updateStockModal">
                            <i class="fas fa-boxes me-1"></i>
                            Cập nhật tồn kho
                        </button>
                        
                        <form action="{{ route('food-item.destroy', $foodItem->id) }}" method="POST" 
                              onsubmit="return confirm('Bạn có chắc chắn muốn xóa món ăn này?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger w-100">
                                <i class="fas fa-trash me-1"></i>
                                Xóa món ăn
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
                    Cập nhật tồn kho
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('food-item.update-stock', $foodItem->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="stock_quantity" class="form-label">Số lượng tồn kho</label>
                        <input type="number" name="stock_quantity" id="stock_quantity" 
                               class="form-control" value="{{ $foodItem->stock_quantity }}" 
                               min="0" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Cập nhật</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
