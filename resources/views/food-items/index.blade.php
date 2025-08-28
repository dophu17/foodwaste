@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="container">
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="page-title">🍽️ {{ __('messages.Food Items') }}</h1>
                    <p class="page-subtitle">
                        {{ __('messages.Manage food items and dishes') }}
                        @if(isset($menu))
                            • Menu: {{ $menu->name }}
                        @endif
                        @if(isset($category))
                            • Danh mục: {{ $category }}
                        @endif
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('food-item.create') }}" class="btn btn-success">
                        <i class="fas fa-plus me-2"></i>{{ __('messages.Create Food Item') }}
                    </a>
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

        <!-- Search and Filter Form -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('food-item.index') }}" class="row g-3">
                    <!-- Search and Filter Form -->
                                        <div class="row g-3">
                        <div class="col-md-3">
                            <input type="text" name="search" class="form-control" 
                                   placeholder="{{ __('messages.Search food items...') }}" 
                                   value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2">
                            <select name="category" class="form-select">
                                <option value="">{{ __('messages.All Categories') }}</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>
                                        {{ $cat }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="availability" class="form-select">
                                <option value="">{{ __('messages.All Status') }}</option>
                                <option value="available" {{ request('availability') == 'available' ? 'selected' : '' }}>
                                    {{ __('messages.Available') }}
                                </option>
                                <option value="unavailable" {{ request('availability') == 'unavailable' ? 'selected' : '' }}>
                                    {{ __('messages.Unavailable') }}
                                </option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="menu_id" class="form-select">
                                <option value="">{{ __('messages.All Menus') }}</option>
                                @foreach($menus as $menu)
                                    <option value="{{ $menu->id }}" {{ request('menu_id') == $menu->id ? 'selected' : '' }}>
                                        {{ $menu->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-search me-2"></i>{{ __('messages.Search') }}
                            </button>
                            <a href="{{ route('food-item.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>{{ __('messages.Clear Search') }}
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h5 class="mb-0">{{ __('messages.Total Food Items') }}: {{ $foodItems->total() }}</h5>
            </div>
        </div>

                    <!-- Food Items Grid -->
                    <div class="row">
                        @forelse($foodItems as $foodItem)
                            <div class="col-lg-4 col-md-6 mb-4">
                                <div class="card h-100 food-item-card">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <span class="badge bg-{{ $foodItem->is_available ? 'success' : 'secondary' }}">
                                            {{ $foodItem->is_available ? __('messages.Available') : __('messages.Unavailable') }}
                                        </span>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                                    type="button" data-bs-toggle="dropdown">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('food-item.show', $foodItem->id) }}">
                                                        <i class="fas fa-eye me-2"></i>{{ __('messages.View') }}
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('food-item.edit', $foodItem->id) }}">
                                                        <i class="fas fa-edit me-2"></i>{{ __('messages.Edit') }}
                                                    </a>
                                                </li>
                                                <li>
                                                    <form action="{{ route('food-item.toggle-availability', $foodItem->id) }}" 
                                                          method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="dropdown-item">
                                                            <i class="fas fa-{{ $foodItem->is_available ? 'eye-slash' : 'eye' }} me-2"></i>
                                                            {{ $foodItem->is_available ? __('messages.Hide') : __('messages.Show') }}
                                                        </button>
                                                    </form>
                                                </li>
                                                <li>
                                                    <form action="{{ route('food-item.destroy', $foodItem->id) }}" 
                                                          method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger" 
                                                                onclick="return confirm('{{ __('messages.Are you sure you want to delete this food item?') }}')">
                                                            <i class="fas fa-trash me-2"></i>{{ __('messages.Delete') }}
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    
                                    <div class="card-body">
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
                                                <div class="mb-2">
                                                    @if($foodItem->is_vegetarian)
                                                        <span class="badge bg-success btn-sm me-1">Chay</span>
                                                    @endif
                                                    @if($foodItem->is_vegan)
                                                        <span class="badge bg-info btn-sm me-1">Thuần chay</span>
                                                    @endif
                                                    @if($foodItem->is_gluten_free)
                                                        <span class="badge bg-warning btn-sm">Không gluten</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        
                                        @if($foodItem->description)
                                            <p class="text-muted small mb-3">{{ Str::limit($foodItem->description, 100) }}</p>
                                        @endif
                                        
                                        <div class="row mb-3">
                                            <div class="col-6">
                                                <small class="text-muted d-block">{{ __('messages.Category') }}</small>
                                                <span class="badge bg-primary">{{ $foodItem->category }}</span>
                                            </div>
                                            <div class="col-6">
                                                <small class="text-muted d-block">{{ __('messages.Price') }}</small>
                                                <span class="fw-bold text-success fs-6">{{ $foodItem->formatted_price }}</span>
                                            </div>
                                        </div>
                                        
                                        <div class="row mb-3">
                                            <div class="col-6">
                                                <small class="text-muted d-block">{{ __('messages.Stock') }}</small>
                                                <span class="badge bg-{{ $foodItem->stock_status_class }}">{{ $foodItem->stock_status }}</span>
                                            </div>
                                            <div class="col-6">
                                                <small class="text-muted d-block">{{ __('messages.Menu') }}</small>
                                                <a href="{{ route('menus.show', $foodItem->menu) }}" class="text-decoration-none">
                                                    <span class="text-primary">{{ Str::limit($foodItem->menu->name, 20) }}</span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="text-center py-5">
                                    <div class="empty-state">
                                        <i class="fas fa-utensils fa-3x text-muted mb-3"></i>
                                        <h6 class="text-muted mb-2">{{ __('messages.No food items yet') }}</h6>
                                        <p class="text-muted mb-3">{{ __('messages.Create your first food item to get started') }}</p>
                                        <a href="{{ route('food-item.create') }}" class="btn btn-primary">
                                            <i class="fas fa-plus me-2"></i>{{ __('messages.Create First Food Item') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforelse
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $foodItems->links() }}
                    </div>
                </div>
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

.empty-state {
    padding: 2rem 1rem;
}

.empty-state i {
    opacity: 0.6;
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
</style>

@endsection

@push('scripts')
<script>
    // Auto-submit form when filters change
    document.querySelectorAll('select[name="category"], select[name="availability"], select[name="menu_id"]').forEach(select => {
        select.addEventListener('change', function() {
            this.closest('form').submit();
        });
    });
</script>
@endpush
