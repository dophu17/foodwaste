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
                    <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#aiInsightsModal">
                        <i class="fas fa-robot me-2"></i>AI Insights
                    </button>
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

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- AI Connection Status -->
        <div class="alert alert-info alert-dismissible fade show" role="alert" id="aiStatusAlert" style="display: none;">
            <i class="fas fa-robot me-2"></i>
            <span id="aiStatusMessage">Checking AI connection...</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>

        <!-- Search and Filter Form -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('food-item.index') }}" class="row g-3">
                    <div class="row g-3">
                        <div class="col-md-2">
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
                        <div class="col-md-4">
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
            <div>
                <button type="button" class="btn btn-outline-primary btn-sm" onclick="checkAIConnection()">
                    <i class="fas fa-wifi me-2"></i>Test AI Connection
                </button>
                <button type="button" class="btn btn-outline-success btn-sm" onclick="generateAIInsights()">
                    <i class="fas fa-magic me-2"></i>Generate AI Insights
                </button>
            </div>
        </div>

        <!-- Food Items Grid -->
        <div class="row">
            @forelse($foodItems as $foodItem)
                <div class="col-lg-4 col-md-6 mb-4" data-food-item-id="{{ $foodItem->id }}">
                    <div class="card h-100 food-item-card" style="position: relative;">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <span class="badge bg-{{ $foodItem->is_available ? 'success' : 'secondary' }}">
                                {{ $foodItem->is_available ? __('messages.Available') : __('messages.Unavailable') }}
                            </span>
                            <div class="dropdown" style="position: relative;">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                        type="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end" style="z-index: 99999 !important;">
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
                                        <button type="button" class="dropdown-item" onclick="getAIInsights({{ $foodItem->id }})">
                                            <i class="fas fa-robot me-2"></i>AI Insights
                                        </button>
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
                                        <!-- Dietary badges removed as columns were dropped from database -->
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

                            <!-- AI Waste Prediction -->
                            @if($foodItem->ai_waste_prediction)
                                <div class="mb-3">
                                    <small class="text-muted d-block">{{ __('messages.AI Waste Prediction') }}</small>
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-info me-2">
                                            <i class="fas fa-robot me-1"></i>{{ number_format($foodItem->ai_waste_prediction, 1) }}%
                                        </span>
                                        <small class="text-muted">Predicted waste percentage</small>
                                    </div>
                                </div>
                            @endif
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
        @if($foodItems->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $foodItems->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</div>

<!-- AI Insights Modal -->
<div class="modal fade" id="aiInsightsModal" tabindex="-1" aria-labelledby="aiInsightsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="aiInsightsModalLabel">
                    <i class="fas fa-robot me-2"></i>AI Insights & Recommendations
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="aiInsightsContent">
                    <div class="text-center py-4">
                        <i class="fas fa-robot fa-3x text-muted mb-3"></i>
                        <h6 class="text-muted">Click "Generate AI Insights" to get intelligent recommendations</h6>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="generateAIInsights()">
                    <i class="fas fa-magic me-2"></i>Generate Insights
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Individual Food Item AI Insights Modal -->
<div class="modal fade" id="foodItemAIInsightsModal" tabindex="-1" aria-labelledby="foodItemAIInsightsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="foodItemAIInsightsModalLabel">
                    <i class="fas fa-robot me-2"></i>AI Insights for <span id="foodItemName"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="foodItemAIInsightsContent">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Analyzing food item data...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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
    position: relative;
}

.food-item-card:hover {
    background-color: #f8f9fa;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    border-color: #dee2e6;
    z-index: 1000;
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

/* Dropdown menu z-index */
.dropdown-menu {
    z-index: 99999 !important;
    position: absolute !important;
}

.dropdown-menu-end {
    right: 0;
    left: auto;
}

/* Bootstrap Pagination Styling */
.pagination {
    margin-bottom: 0;
}

.page-link {
    color: #6c757d;
    background-color: #fff;
    border: 1px solid #dee2e6;
    padding: 0.5rem 0.75rem;
    margin-left: -1px;
    transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out;
}

.page-link:hover {
    color: #495057;
    background-color: #e9ecef;
    border-color: #dee2e6;
}

.page-link:focus {
    color: #495057;
    background-color: #e9ecef;
    outline: 0;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.page-item.active .page-link {
    background-color: #0d6efd;
    border-color: #0d6efd;
    color: #fff;
}

.page-item.disabled .page-link {
    color: #6c757d;
    pointer-events: none;
    background-color: #fff;
    border-color: #dee2e6;
}

.page-item:first-child .page-link {
    margin-left: 0;
    border-top-left-radius: 0.375rem;
    border-bottom-left-radius: 0.375rem;
}

.page-item:last-child .page-link {
    border-top-right-radius: 0.375rem;
    border-bottom-right-radius: 0.375rem;
}

/* AI Insights Styling */
.ai-insight-card {
    border-left: 4px solid #0d6efd;
    background-color: #f8f9fa;
    padding: 1rem;
    margin-bottom: 1rem;
    border-radius: 0.375rem;
}

.ai-insight-card.warning {
    border-left-color: #ffc107;
    background-color: #fff3cd;
}

.ai-insight-card.success {
    border-left-color: #198754;
    background-color: #d1e7dd;
}

.ai-insight-card.danger {
    border-left-color: #dc3545;
    background-color: #f8d7da;
}
</style>

@endsection

@push('scripts')
<script>
    // Check AI Connection
    function checkAIConnection() {
        const alert = document.getElementById('aiStatusAlert');
        const message = document.getElementById('aiStatusMessage');
        
        alert.style.display = 'block';
        message.innerHTML = 'Testing AI connection...';
        
        fetch('/gemini-ai/test-connection', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert.className = 'alert alert-success alert-dismissible fade show';
                message.innerHTML = `<i class="fas fa-check-circle me-2"></i>${data.message}`;
            } else {
                alert.className = 'alert alert-danger alert-dismissible fade show';
                message.innerHTML = `<i class="fas fa-exclamation-triangle me-2"></i>${data.message}`;
            }
        })
        .catch(error => {
            alert.className = 'alert alert-danger alert-dismissible fade show';
            message.innerHTML = `<i class="fas fa-exclamation-triangle me-2"></i>Connection error: ${error.message}`;
        });
    }

    // Generate AI Insights for all food items
    function generateAIInsights() {
        const content = document.getElementById('aiInsightsContent');
        content.innerHTML = `
            <div class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2">Generating AI insights...</p>
            </div>
        `;

        fetch('/gemini-ai/comprehensive-analysis', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayAIInsights(data.data);
            } else {
                content.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>${data.message}
                    </div>
                `;
            }
        })
        .catch(error => {
            content.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>Error: ${error.message}
                </div>
            `;
        });
    }

    // Display AI Insights
    function displayAIInsights(data) {
        const content = document.getElementById('aiInsightsContent');
        let html = '<div class="row">';

        if (data.ai_insights) {
            if (data.ai_insights.demand_forecast) {
                html += `
                    <div class="col-md-6 mb-3">
                        <div class="ai-insight-card success">
                            <h6><i class="fas fa-chart-line me-2"></i>Demand Forecast</h6>
                            <p class="mb-2">${data.ai_insights.demand_forecast.analysis || 'AI analysis available'}</p>
                            <small class="text-muted">Confidence: ${data.ai_insights.demand_forecast.confidence_level || 'Medium'}</small>
                        </div>
                    </div>
                `;
            }

            if (data.ai_insights.waste_insights) {
                html += `
                    <div class="col-md-6 mb-3">
                        <div class="ai-insight-card warning">
                            <h6><i class="fas fa-recycle me-2"></i>Waste Insights</h6>
                            <p class="mb-2">${data.ai_insights.waste_insights.waste_analysis || 'AI waste analysis available'}</p>
                            <small class="text-muted">Cost savings: ${data.ai_insights.waste_insights.cost_savings || 'Calculating...'}</small>
                        </div>
                    </div>
                `;
            }

            if (data.ai_insights.menu_optimization) {
                html += `
                    <div class="col-md-6 mb-3">
                        <div class="ai-insight-card info">
                            <h6><i class="fas fa-utensils me-2"></i>Menu Optimization</h6>
                            <p class="mb-2">${data.ai_insights.menu_optimization.menu_performance || 'AI menu analysis available'}</p>
                            <small class="text-muted">Profit improvement: ${data.ai_insights.menu_optimization.profit_improvement || 'Calculating...'}</small>
                        </div>
                    </div>
                `;
            }
        }

        if (html === '<div class="row">') {
            html += `
                <div class="col-12">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>No AI insights available yet. Try generating insights for specific food items.
                    </div>
                </div>
            `;
        }

        html += '</div>';
        content.innerHTML = html;
    }

    // Get AI Insights for specific food item
    function getAIInsights(foodItemId) {
        const modal = new bootstrap.Modal(document.getElementById('foodItemAIInsightsModal'));
        modal.show();

        // Get food item name
        const foodItemName = document.querySelector(`[data-food-item-id="${foodItemId}"] .card-body h6`)?.textContent || 'Food Item';
        document.getElementById('foodItemName').textContent = foodItemName;

        // Generate insights for this specific item
        fetch(`/gemini-ai/menu-optimization?food_item_id=${foodItemId}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            const content = document.getElementById('foodItemAIInsightsContent');
            if (data.success) {
                content.innerHTML = `
                    <div class="ai-insight-card success">
                        <h6><i class="fas fa-robot me-2"></i>AI Analysis Results</h6>
                        <p class="mb-2">${data.data.menu_performance || 'AI analysis completed successfully'}</p>
                        <div class="mt-3">
                            <h6>Recommendations:</h6>
                            <ul class="mb-0">
                                ${(data.data.menu_changes || []).map(change => `<li>${change}</li>`).join('')}
                            </ul>
                        </div>
                    </div>
                `;
            } else {
                content.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>${data.message}
                    </div>
                `;
            }
        })
        .catch(error => {
            const content = document.getElementById('foodItemAIInsightsContent');
            content.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>Error: ${error.message}
                </div>
            `;
        });
    }

    // Auto-check AI connection on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Check AI connection status after a short delay
        setTimeout(checkAIConnection, 1000);
    });

    // Form will only submit when search button is clicked
    // No auto-submit on filter changes
</script>
@endpush
