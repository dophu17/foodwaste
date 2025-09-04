@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="container">
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="page-title">🍽️ {{ $menu->name }}</h1>
                    <p class="page-subtitle">
                        {{ $menu->category }} • 
                        {{ $menu->restaurant->name }}
                    </p>
                </div>
                <div class="d-flex gap-2">
                    @if($menu->canEditByUser(auth()->user()))
                        <a href="{{ route('menus.edit', $menu) }}" class="btn btn-primary">
                            <i class="fas fa-edit me-2"></i>{{ __('messages.Edit Menu') }}
                        </a>
                        <form action="{{ route('menus.toggle-status', $menu) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-{{ $menu->is_active ? 'warning' : 'success' }}">
                                <i class="fas fa-{{ $menu->is_active ? 'pause' : 'play' }} me-2"></i>
                                {{ $menu->is_active ? __('messages.Deactivate') : __('messages.Activate') }}
                            </button>
                        </form>
                    @endif
                    <a href="{{ route('menus.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>{{ __('messages.Back to Menus') }}
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
            <!-- Menu Details -->
            <div class="col-lg-8">
                <!-- Menu Info Card -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-info-circle me-2"></i>{{ __('messages.Menu Information') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <small class="text-muted">{{ __('messages.Menu Name') }}</small>
                                <div class="fw-bold">{{ $menu->name }}</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <small class="text-muted">{{ __('messages.Category') }}</small>
                                <div class="fw-bold">
                                    <span class="badge bg-primary">
                                        {{ $menu->category }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <small class="text-muted">{{ __('messages.Status') }}</small>
                                <div class="fw-bold">
                                    <span class="badge bg-{{ $menu->is_active ? 'success' : 'secondary' }}">
                                        {{ $menu->status_text }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <small class="text-muted">{{ __('messages.Restaurant') }}</small>
                                <div class="fw-bold">
                                    <a href="{{ route('restaurant.show', $menu->restaurant) }}" class="text-decoration-none">
                                        🏪 {{ $menu->restaurant->name }}
                                    </a>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <small class="text-muted">{{ __('messages.Valid From') }}</small>
                                <div class="fw-bold">
                                    @if($menu->valid_from)
                                        {{ $menu->valid_from->format('d/m/Y') }}
                                    @else
                                        {{ __('messages.Unlimited') }}
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <small class="text-muted">{{ __('messages.Valid Until') }}</small>
                                <div class="fw-bold">
                                    @if($menu->valid_until)
                                        {{ $menu->valid_until->format('d/m/Y') }}
                                    @else
                                        {{ __('messages.Unlimited') }}
                                    @endif
                                </div>
                            </div>
                            @if($menu->description)
                                <div class="col-12 mb-3">
                                    <small class="text-muted">{{ __('messages.Description') }}</small>
                                    <div class="fw-bold">{{ $menu->description }}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                                <!-- Food Items Section -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-utensils me-2"></i>{{ __('messages.Food Items') }}
                            <span class="badge bg-secondary ms-2">{{ $menu->foodItems->count() }}</span>
                            @if(!$menu->is_active)
                                <span class="badge bg-warning ms-2">Đã vô hiệu hóa</span>
                            @endif
                        </h5>
                        @if($menu->canEditByUser(auth()->user()))
                            @if($menu->is_active)
                                <a href="{{ route('food-item.create', ['menu_id' => $menu->id]) }}" class="btn btn-sm btn-success">
                                    <i class="fas fa-plus me-2"></i>Thêm món ăn
                                </a>
                            @else
                                <button class="btn btn-sm btn-secondary" disabled title="Menu đã bị vô hiệu hóa">
                                    <i class="fas fa-plus me-2"></i>Thêm món ăn
                                </button>
                            @endif
                        @endif
                    </div>
                    <div class="card-body">
                        @if($menu->hasFoodItems())
                            <div class="row">
                                @foreach($menu->foodItemsByCategory as $category => $foodItems)
                                    <div class="col-12 mb-4">
                                        <div class="category-header mb-3">
                                            <h6 class="text-primary fw-bold mb-2">
                                                <i class="fas fa-tag me-2"></i>{{ $category }}
                                                <span class="badge bg-light text-dark ms-2">{{ count($foodItems) }}</span>
                                            </h6>
                                            <hr class="my-2">
                                        </div>
                                        <div class="row">
                                            @foreach($foodItems as $foodItem)
                                                <div class="col-lg-6 col-xl-4 mb-3">
                                                    <div class="food-item-card h-100">
                                                        <div class="card-body p-3">
                                                            <div class="d-flex align-items-start mb-3">
                                                                @if($foodItem->image_path)
                                                                    <img src="{{ Storage::url($foodItem->image_path) }}" 
                                                                         alt="{{ $foodItem->name }}" 
                                                                         class="img-thumbnail me-3" 
                                                                         style="width: 60px; height: 60px; object-fit: cover;">
                                                                @else
                                                                    <div class="bg-light d-flex align-items-center justify-content-center me-3" 
                                                                         style="width: 60px; height: 60px; border-radius: 8px;">
                                                                        <i class="fas fa-utensils text-muted fa-lg"></i>
                                                                    </div>
                                                                @endif
                                                                <div class="flex-grow-1">
                                                                    <h6 class="mb-1 fw-bold text-dark">{{ $foodItem->name }}</h6>

                                                                </div>
                                                            </div>
                                                            
                                                            @if($foodItem->description)
                                                                <p class="text-muted small mb-3">{{ Str::limit($foodItem->description, 100) }}</p>
                                                            @endif
                                                            
                                                            <div class="row mb-3">
                                                                <div class="col-6">
                                                                    <small class="text-muted d-block">Danh mục</small>
                                                                    <span class="badge bg-primary">{{ $foodItem->category }}</span>
                                                                </div>
                                                                <div class="col-6">
                                                                    <small class="text-muted d-block">Giá</small>
                                                                    <span class="fw-bold text-success fs-6">{{ $foodItem->formatted_price }}</span>
                                                                </div>
                                                            </div>
                                                            
                                                            <div class="row mb-3">
                                                                <div class="col-6">
                                                                    <small class="text-muted d-block">Tồn kho</small>
                                                                    <span class="badge bg-{{ $foodItem->stock_status_class }}">{{ $foodItem->stock_status }}</span>
                                                                </div>
                                                                <div class="col-6">
                                                                    <small class="text-muted d-block">Trạng thái</small>
                                                                    <span class="badge bg-{{ $foodItem->is_available ? 'success' : 'secondary' }}">
                                                                        {{ $foodItem->is_available ? 'Có sẵn' : 'Hết hàng' }}
                                                                    </span>
                                                                </div>
                                                            </div>
                                                            
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div class="action-buttons">
                                                                    <a href="{{ route('food-item.show', $foodItem->id) }}" 
                                                                       class="btn-action btn-view" title="Xem chi tiết">
                                                                        <i class="fas fa-eye"></i>
                                                                    </a>
                                                                    @if($menu->canEditByUser(auth()->user()))
                                                                        <a href="{{ route('food-item.edit', $foodItem->id) }}" 
                                                                           class="btn-action btn-edit" title="Chỉnh sửa">
                                                                            <i class="fas fa-edit"></i>
                                                                        </a>
                                                                        <form action="{{ route('food-item.destroy', $foodItem->id) }}" method="POST" class="d-inline">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                            <button type="submit" class="btn-action btn-delete" 
                                                                                    onclick="return confirm('Bạn có chắc muốn xóa món ăn này?')" title="Xóa">
                                                                                <i class="fas fa-trash"></i>
                                                                            </button>
                                                                        </form>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-5">
                                <div class="empty-state">
                                    <i class="fas fa-utensils fa-3x text-muted mb-3"></i>
                                    <h6 class="text-muted mb-2">Chưa có món ăn nào</h6>
                                    <p class="text-muted mb-3">Thêm món ăn để làm cho thực đơn này hoàn chỉnh</p>
                                    @if($menu->canEditByUser(auth()->user()))
                                        @if($menu->is_active)
                                            <a href="{{ route('food-item.create', ['menu_id' => $menu->id]) }}" class="btn btn-primary">
                                                <i class="fas fa-plus me-2"></i>Thêm món ăn đầu tiên
                                            </a>
                                        @else
                                            <button class="btn btn-secondary" disabled title="Menu đã bị vô hiệu hóa">
                                                <i class="fas fa-plus me-2"></i>Thêm món ăn đầu tiên
                                            </button>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Quick Stats -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-chart-bar me-2"></i>{{ __('messages.Quick Stats') }}
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6 mb-3">
                                <div class="stat-item">
                                    <div class="stat-number text-primary">{{ $menu->total_items }}</div>
                                    <div class="stat-label text-muted">{{ __('messages.Food Items') }}</div>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="stat-item">
                                    <div class="stat-number text-success">{{ $menu->price_range }}</div>
                                    <div class="stat-label text-muted">{{ __('messages.Price Range') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Menu Status -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-info-circle me-2"></i>{{ __('messages.Menu Status') }}
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="status-item mb-3">
                            <small class="text-muted">{{ __('messages.Current Status') }}</small>
                            <div class="fw-bold">
                                <span class="badge bg-{{ $menu->is_active ? 'success' : 'secondary' }}">
                                    {{ $menu->status_text }}
                                </span>
                            </div>
                        </div>
                        
                        @if($menu->days_remaining !== null)
                            <div class="status-item mb-3">
                                <small class="text-muted">{{ __('messages.Days Remaining') }}</small>
                                <div class="fw-bold text-{{ $menu->days_remaining > 7 ? 'success' : ($menu->days_remaining > 3 ? 'warning' : 'danger') }}">
                                    {{ $menu->days_remaining }} {{ __('messages.days') }}
                                </div>
                            </div>
                        @endif

                        <div class="status-item">
                            <small class="text-muted">{{ __('messages.Created') }}</small>
                            <div class="fw-bold">{{ $menu->created_at->format('d/m/Y H:i') }}</div>
                        </div>
                    </div>
                </div>

                <!-- Food Items Summary -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-utensils me-2"></i>Tổng quan món ăn
                        </h6>
                    </div>
                    <div class="card-body">
                        @if($menu->hasFoodItems())
                            <div class="food-summary">
                                @foreach($menu->foodItemsByCategory as $category => $foodItems)
                                    <div class="category-summary mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="fw-bold text-primary">{{ $category }}</span>
                                            <span class="badge bg-secondary">{{ count($foodItems) }}</span>
                                        </div>
                                        <div class="category-items">
                                            @foreach($foodItems->take(3) as $foodItem)
                                                <div class="food-item-summary d-flex align-items-center mb-2">
                                                    <div class="food-icon me-2">
                                                        @if($foodItem->image_path)
                                                            <img src="{{ Storage::url($foodItem->image_path) }}" 
                                                                 alt="{{ $foodItem->name }}" 
                                                                 class="rounded-circle" 
                                                                 style="width: 24px; height: 24px; object-fit: cover;">
                                                        @else
                                                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" 
                                                                 style="width: 24px; height: 24px;">
                                                                <i class="fas fa-utensils text-muted fa-xs"></i>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <div class="food-name small fw-medium">{{ Str::limit($foodItem->name, 25) }}</div>
                                                        <div class="food-price text-muted small">{{ $foodItem->formatted_price }}</div>
                                                    </div>
                                                    <div class="food-status">
                                                        @if($foodItem->is_available)
                                                            <span class="badge bg-success badge-sm">✓</span>
                                                        @else
                                                            <span class="badge bg-secondary badge-sm">✗</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                            @if(count($foodItems) > 3)
                                                <div class="text-center text-muted small">
                                                    <i class="fas fa-ellipsis-h"></i> và {{ count($foodItems) - 3 }} món khác
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center text-muted py-3">
                                <i class="fas fa-utensils fa-2x mb-2"></i>
                                <p class="small mb-2">Chưa có món ăn nào</p>
                                @if($menu->is_active)
                                    <a href="{{ route('food-item.create', ['menu_id' => $menu->id]) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-plus me-1"></i>Thêm món ăn
                                    </a>
                                @else
                                    <button class="btn btn-sm btn-secondary" disabled title="Menu đã bị vô hiệu hóa">
                                        <i class="fas fa-plus me-1"></i>Thêm món ăn
                                    </button>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Actions -->
                @if($menu->canEditByUser(auth()->user()))
                    <div class="card">
                        <div class="card-header">
                            <h6 class="card-title mb-0">
                                <i class="fas fa-cogs me-2"></i>{{ __('messages.Actions') }}
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="{{ route('menus.edit', $menu) }}" class="btn btn-primary">
                                    <i class="fas fa-edit me-2"></i>{{ __('messages.Edit Menu') }}
                                </a>
                                <form action="{{ route('menus.destroy', $menu) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger w-100" onclick="return confirm('{{ __('messages.Are you sure you want to delete this menu?') }}')">
                                        <i class="fas fa-trash me-2"></i>{{ __('messages.Delete Menu') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
.food-item-card {
    transition: all 0.3s ease;
    background-color: #ffffff;
    border: 1px solid #e9ecef;
    border-radius: 12px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.food-item-card:hover {
    background-color: #f8f9fa;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    border-color: #dee2e6;
}

.category-header {
    border-left: 4px solid #007bff;
    padding-left: 1rem;
}

.category-header h6 {
    margin-bottom: 0.5rem;
}

.food-item-summary {
    padding: 0.5rem;
    border-radius: 8px;
    background-color: #f8f9fa;
    transition: background-color 0.2s ease;
}

.food-item-summary:hover {
    background-color: #e9ecef;
}

.food-icon img,
.food-icon div {
    border: 2px solid #e9ecef;
}

.food-name {
    color: #495057;
    font-weight: 500;
}

.food-price {
    color: #6c757d;
}

.badge-sm {
    font-size: 0.75em;
    padding: 0.25em 0.5em;
}

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

.empty-state {
    padding: 2rem 1rem;
}

.empty-state i {
    opacity: 0.6;
}

.stat-item {
    padding: 0.75rem;
    text-align: center;
}

.stat-number {
    font-size: 1.75rem;
    font-weight: bold;
    margin-bottom: 0.25rem;
}

.stat-label {
    font-size: 0.875rem;
    color: #6c757d;
}

.status-item {
    padding: 0.75rem 0;
    border-bottom: 1px solid #e9ecef;
}

.status-item:last-child {
    border-bottom: none;
}

.status-item small {
    color: #6c757d;
    font-size: 0.875rem;
}

.status-item .fw-bold {
    color: #495057;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .food-item-card {
        margin-bottom: 1rem;
    }
    
    .category-header {
        padding-left: 0.75rem;
    }
    
    .food-item-summary {
        padding: 0.75rem;
    }
}

/* Animation for new items */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.food-item-card {
    animation: fadeInUp 0.5s ease-out;
}

/* Hover effects for buttons */
.btn-group .btn {
    transition: all 0.2s ease;
}

.btn-group .btn:hover {
    transform: translateY(-1px);
}

/* Category badge styling */
.badge.bg-light {
    color: #495057 !important;
    background-color: #f8f9fa !important;
    border: 1px solid #dee2e6;
}
</style>
@endsection
