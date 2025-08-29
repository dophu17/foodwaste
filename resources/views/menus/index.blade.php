@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="container">
        <div class="page-header">
            <h1 class="page-title">🍽️ {{ __('messages.Menus') }}</h1>
            <p class="page-subtitle">{{ __('messages.Manage restaurant menus') }}</p>
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
                <form action="{{ route('menus.index') }}" method="GET" class="row g-3">
                    <div class="col-md-4">
                        <input type="text" class="form-control" name="search" 
                               placeholder="{{ __('messages.Search menus...') }}" 
                               value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" name="status">
                            <option value="">{{ __('messages.All Status') }}</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>
                                {{ __('messages.Active') }}
                            </option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>
                                {{ __('messages.Inactive') }}
                            </option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search me-2"></i>{{ __('messages.Search') }}
                        </button>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('menus.index') }}" class="btn btn-secondary w-100">
                            <i class="fas fa-times me-2"></i>{{ __('messages.Clear Search') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h5 class="mb-0">{{ __('messages.Total Menus') }}: {{ $menus->total() }}</h5>
            </div>
            <div>
                <a href="{{ route('menus.create') }}" class="btn btn-success">
                    <i class="fas fa-plus me-2"></i>{{ __('messages.Create Menu') }}
                </a>
            </div>
        </div>

        <!-- Menus List -->
        <div class="menus-list">
            @forelse($menus as $menu)
                <div class="menu-item mb-3" style="position: relative;">
                    <div class="card">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <!-- Menu Icon and Name -->
                                <div class="col-lg-3 col-md-4">
                                    <div class="d-flex align-items-center">
                                        <div class="menu-icon me-3">
                                            <i class="fas fa-utensils text-primary"></i>
                                        </div>
                                        <div>
                                            <h5 class="mb-1 fw-bold">{{ $menu->name }}</h5>
                                            @if($menu->description)
                                                <small class="text-muted">{{ Str::limit($menu->description, 60) }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Restaurant and Category -->
                                <div class="col-lg-2 col-md-3">
                                    <div class="mb-2">
                                        <span class="badge bg-info bg-opacity-10 text-info">
                                            <i class="fas fa-store me-1"></i>{{ $menu->restaurant->name }}
                                        </span>
                                    </div>
                                    @if($menu->category)
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary">
                                            {{ $menu->category }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </div>
                                
                                <!-- Items and Price Range -->
                                <div class="col-lg-2 col-md-3">
                                    <div class="mb-2">
                                        <span class="badge bg-primary bg-opacity-10 text-primary">
                                            <i class="fas fa-list me-1"></i>{{ $menu->total_items }} {{ __('messages.Items') }}
                                        </span>
                                    </div>
                                    @if($menu->price_range)
                                        <div class="fw-medium">{{ $menu->price_range }}</div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </div>
                                
                                <!-- Status and Validity -->
                                <div class="col-lg-2 col-md-3">
                                    <div class="mb-2">
                                        <span class="badge bg-{{ $menu->is_active ? 'success' : 'secondary' }}">
                                            {{ $menu->status_text }}
                                        </span>
                                    </div>
                                    @if($menu->valid_from && $menu->valid_until)
                                        <div class="small">
                                            <div class="fw-medium">{{ $menu->valid_from->format('d/m/Y') }}</div>
                                            <div class="text-muted">to {{ $menu->valid_until->format('d/m/Y') }}</div>
                                        </div>
                                    @else
                                        <span class="badge bg-warning bg-opacity-10 text-warning">
                                            {{ __('messages.Unlimited validity') }}
                                        </span>
                                    @endif
                                </div>
                                
                                <!-- Days Remaining -->
                                <div class="col-lg-1 col-md-2">
                                    @if($menu->days_remaining !== null && $menu->days_remaining > 0)
                                        <span class="badge bg-info bg-opacity-10 text-info">
                                            <i class="fas fa-clock me-1"></i>{{ $menu->days_remaining }}
                                        </span>
                                    @elseif($menu->days_remaining === 0)
                                        <span class="badge bg-danger bg-opacity-10 text-danger">
                                            {{ __('messages.Expired') }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </div>
                                
                                <!-- Actions -->
                                <div class="col-lg-2 col-md-3 text-end">
                                    <div class="dropdown" style="position: relative;">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                                type="button" data-bs-toggle="dropdown">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end" style="z-index: 99999 !important;">
                                            <li>
                                                <a class="dropdown-item" href="{{ route('menus.show', $menu) }}">
                                                    <i class="fas fa-eye me-2"></i>{{ __('messages.View') }}
                                                </a>
                                            </li>
                                            @if($menu->canEditByUser(auth()->user()))
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('menus.edit', $menu) }}">
                                                        <i class="fas fa-edit me-2"></i>{{ __('messages.Edit') }}
                                                    </a>
                                                </li>
                                                <li>
                                                    <form action="{{ route('menus.toggle-status', $menu) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="dropdown-item">
                                                            <i class="fas fa-{{ $menu->is_active ? 'pause' : 'play' }} me-2"></i>
                                                            {{ $menu->is_active ? __('messages.Deactivate') : __('messages.Activate') }}
                                                        </button>
                                                    </form>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <form action="{{ route('menus.destroy', $menu) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger" 
                                                                onclick="return confirm('{{ __('messages.Are you sure you want to delete this menu?') }}')">
                                                            <i class="fas fa-trash me-2"></i>{{ __('messages.Delete') }}
                                                        </button>
                                                    </form>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-5">
                    <div class="empty-state">
                        <i class="fas fa-utensils fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">{{ __('messages.No menus found') }}</h5>
                        <p class="text-muted">{{ __('messages.Create your first menu to get started') }}</p>
                        <a href="{{ route('menus.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>{{ __('messages.Create Menu') }}
                        </a>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($menus->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $menus->links() }}
            </div>
        @endif
    </div>
</div>

<style>
.menu-item {
    transition: all 0.2s ease;
    position: relative;
}

.menu-item:hover {
    z-index: 1000;
}

.menu-item:hover .card {
    background-color: #f8f9fa;
}

.menu-icon {
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

.dropdown-menu {
    z-index: 99999 !important;
    position: absolute !important;
}

.dropdown-menu-end {
    right: 0;
    left: auto;
}

.card {
    border: 1px solid #e9ecef;
    border-radius: 8px;
}

.card-body {
    padding: 1.25rem;
}

@media (max-width: 768px) {
    .menu-item .row > div {
        margin-bottom: 1rem;
    }
    
    .menu-item .row > div:last-child {
        margin-bottom: 0;
    }
}
</style>
@endsection
