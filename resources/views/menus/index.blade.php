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

        <!-- Menus Grid -->
        <div class="row">
            @forelse($menus as $menu)
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100 menu-card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <span class="badge bg-{{ $menu->is_active ? 'success' : 'secondary' }}">
                                {{ $menu->status_text }}
                            </span>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                        type="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu">
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
                        
                        <div class="card-body">
                            <h5 class="card-title">{{ $menu->name }}</h5>
                            <p class="card-text text-muted">{{ Str::limit($menu->description, 100) }}</p>
                            
                            <div class="menu-info">
                                <div class="row text-center">
                                    <div class="col-4">
                                        <small class="text-muted">{{ __('messages.Category') }}</small>
                                        <div class="fw-bold">{{ $menu->category }}</div>
                                    </div>
                                    <div class="col-4">
                                        <small class="text-muted">{{ __('messages.Items') }}</small>
                                        <div class="fw-bold">{{ $menu->total_items }}</div>
                                    </div>
                                    <div class="col-4">
                                        <small class="text-muted">{{ __('messages.Price Range') }}</small>
                                        <div class="fw-bold">{{ $menu->price_range }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card-footer">
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <i class="fas fa-store me-1"></i>{{ $menu->restaurant->name }}
                                </small>
                                <small class="text-muted">
                                    <i class="fas fa-calendar me-1"></i>
                                                                                @if($menu->valid_from && $menu->valid_until)
                                                {{ $menu->valid_from->format('d/m/Y') }} - {{ $menu->valid_until->format('d/m/Y') }}
                                            @else
                                                {{ __('messages.Unlimited validity') }}
                                            @endif
                                </small>
                            </div>
                            @if($menu->days_remaining !== null && $menu->days_remaining > 0)
                                <div class="mt-2">
                                    <small class="text-info">
                                        <i class="fas fa-clock me-1"></i>
                                        {{ __('messages.Days remaining') }}: {{ $menu->days_remaining }}
                                    </small>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="text-center py-5">
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
            <div class="d-flex justify-content-center">
                {{ $menus->links() }}
            </div>
        @endif
    </div>
</div>

<style>
.menu-card {
    transition: transform 0.2s, box-shadow 0.2s;
    border: 1px solid #e9ecef;
}

.menu-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.menu-info {
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid #e9ecef;
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #e9ecef;
}
</style>
@endsection
