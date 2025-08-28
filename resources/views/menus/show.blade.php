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
                        </h5>
                        @if($menu->canEditByUser(auth()->user()))
                            <a href="#" class="btn btn-sm btn-success">
                                <i class="fas fa-plus me-2"></i>{{ __('messages.Add Food Item') }}
                            </a>
                        @endif
                    </div>
                    <div class="card-body">
                        @if($menu->hasFoodItems())
                            <div class="row">
                                @foreach($menu->foodItems as $foodItem)
                                    <div class="col-md-6 mb-3">
                                        <div class="food-item-card p-3 border rounded">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1">{{ $foodItem->name }}</h6>
                                                    <p class="text-muted small mb-2">{{ Str::limit($foodItem->description, 80) }}</p>
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <span class="badge bg-info">{{ $foodItem->category }}</span>
                                                        <span class="fw-bold text-success">{{ number_format($foodItem->price) }} {{ __('messages.currency') }}</span>
                                                    </div>
                                                </div>
                                                @if($menu->canEditByUser(auth()->user()))
                                                    <div class="dropdown ms-2">
                                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                                                type="button" data-bs-toggle="dropdown">
                                                            <i class="fas fa-ellipsis-v"></i>
                                                        </button>
                                                        <ul class="dropdown-menu">
                                                            <li>
                                                                <a class="dropdown-item" href="#">
                                                                    <i class="fas fa-edit me-2"></i>{{ __('messages.Edit') }}
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <form action="#" method="POST" class="d-inline">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="dropdown-item text-danger" onclick="return confirm('{{ __('messages.Are you sure you want to delete this food item?') }}')">
                                                                        <i class="fas fa-trash me-2"></i>{{ __('messages.Delete') }}
                                                                    </button>
                                                                </form>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <i class="fas fa-utensils fa-2x text-muted mb-3"></i>
                                <h6 class="text-muted">{{ __('messages.No food items yet') }}</h6>
                                <p class="text-muted">{{ __('messages.Add food items to make this menu complete') }}</p>
                                @if($menu->canEditByUser(auth()->user()))
                                    <a href="#" class="btn btn-primary">
                                        <i class="fas fa-plus me-2"></i>{{ __('messages.Add First Food Item') }}
                                    </a>
                                @endif
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
    transition: all 0.2s;
    background-color: #f8f9fa;
}

.food-item-card:hover {
    background-color: #e9ecef;
    transform: translateY(-1px);
}

.stat-item {
    padding: 0.5rem;
}

.stat-number {
    font-size: 1.5rem;
    font-weight: bold;
}

.stat-label {
    font-size: 0.875rem;
}

.status-item {
    padding: 0.5rem 0;
    border-bottom: 1px solid #e9ecef;
}

.status-item:last-child {
    border-bottom: none;
}
</style>
@endsection
