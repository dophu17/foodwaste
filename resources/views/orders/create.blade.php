@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="container">
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="page-title">📋 {{ __('messages.Create New Order') }}</h1>
                    <p class="page-subtitle">{{ __('messages.Add a new order with food items') }}</p>
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
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form action="{{ route('orders.store') }}" method="POST" id="createOrderForm">
            @csrf
            
            <!-- Order Information Card -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>{{ __('messages.Order Information') }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="order_date" class="form-label">{{ __('messages.Order Date') }} *</label>
                                <input type="date" class="form-control @error('order_date') is-invalid @enderror" 
                                       id="order_date" name="order_date" value="{{ $today->format('Y-m-d') }}" required>
                                @error('order_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="customer_count" class="form-label">{{ __('messages.Customer Count') }} *</label>
                                <input type="number" class="form-control @error('customer_count') is-invalid @enderror" 
                                       id="customer_count" name="customer_count" min="1" value="1" required>
                                @error('customer_count')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="day_of_week" class="form-label">{{ __('messages.Day of Week') }} *</label>
                                <select class="form-select @error('day_of_week') is-invalid @enderror" 
                                        id="day_of_week" name="day_of_week" required>
                                    <option value="Monday" {{ $dayOfWeek == 'Monday' ? 'selected' : '' }}>Monday</option>
                                    <option value="Tuesday" {{ $dayOfWeek == 'Tuesday' ? 'selected' : '' }}>Tuesday</option>
                                    <option value="Wednesday" {{ $dayOfWeek == 'Wednesday' ? 'selected' : '' }}>Wednesday</option>
                                    <option value="Thursday" {{ $dayOfWeek == 'Thursday' ? 'selected' : '' }}>Thursday</option>
                                    <option value="Friday" {{ $dayOfWeek == 'Friday' ? 'selected' : '' }}>Friday</option>
                                    <option value="Saturday" {{ $dayOfWeek == 'Saturday' ? 'selected' : '' }}>Saturday</option>
                                    <option value="Sunday" {{ $dayOfWeek == 'Sunday' ? 'selected' : '' }}>Sunday</option>
                                </select>
                                @error('day_of_week')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="weather_condition" class="form-label">{{ __('messages.Weather') }}</label>
                                <select class="form-select" id="weather_condition" name="weather_condition">
                                    <option value="">{{ __('messages.Select Weather') }}</option>
                                    @foreach($weatherConditions as $weather)
                                        <option value="{{ $weather }}">{{ $weather }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_holiday" name="is_holiday" value="1">
                                    <label class="form-check-label" for="is_holiday">
                                        {{ __('messages.Is Holiday') }}
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="special_event" class="form-label">{{ __('messages.Special Event') }}</label>
                                <input type="text" class="form-control" id="special_event" name="special_event" 
                                       placeholder="{{ __('messages.e.g., Birthday Party, Anniversary') }}">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="notes" class="form-label">{{ __('messages.Notes') }}</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3" 
                                  placeholder="{{ __('messages.Any additional notes about this order...') }}"></textarea>
                    </div>
                </div>
            </div>

            <!-- Food Items Selection Card -->
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-utensils me-2"></i>{{ __('messages.Select Food Items') }}
                    </h5>
                </div>
                <div class="card-body">
                    <div id="foodItemsContainer">
                        <div class="food-item-row mb-3">
                            <div class="row align-items-end">
                                <div class="col-md-4">
                                    <label class="form-label">{{ __('messages.Food Item') }} *</label>
                                    <select class="form-select food-item-select" name="food_items[0][food_item_id]" required>
                                        <option value="">{{ __('messages.Select Food Item') }}</option>
                                        @foreach($foodItems as $foodItem)
                                            <option value="{{ $foodItem->id }}" 
                                                    data-price="{{ $foodItem->price }}"
                                                    data-stock="{{ $foodItem->stock_quantity }}">
                                                {{ $foodItem->name }} - ¥{{ number_format($foodItem->price, 0) }}
                                                ({{ __('messages.Stock') }}: {{ $foodItem->stock_quantity }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="col-md-2">
                                    <label class="form-label">{{ __('messages.Quantity') }} *</label>
                                    <input type="number" class="form-control quantity-input" 
                                           name="food_items[0][quantity_sold]" min="1" value="1" required>
                                </div>
                                
                                <div class="col-md-2">
                                    <label class="form-label">{{ __('messages.Unit Price') }} *</label>
                                    <input type="number" class="form-control unit-price-input" 
                                           name="food_items[0][unit_price]" step="0.01" min="0" required>
                                </div>
                                
                                <div class="col-md-2">
                                    <label class="form-label">{{ __('messages.Total') }}</label>
                                    <input type="text" class="form-control total-price-display" readonly>
                                </div>
                                
                                <div class="col-md-2">
                                    <button type="button" class="btn btn-danger remove-food-item" style="display: none;">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-center">
                        <button type="button" class="btn btn-outline-success" id="addFoodItem">
                            <i class="fas fa-plus me-2"></i>{{ __('messages.Add Another Food Item') }}
                        </button>
                    </div>
                    
                    <div class="mt-4">
                        <div class="row">
                            <div class="col-md-6 offset-md-6">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <h6 class="card-title">{{ __('messages.Order Summary') }}</h6>
                                        <div class="d-flex justify-content-between">
                                            <span>{{ __('messages.Total Items') }}:</span>
                                            <span id="totalItems">0</span>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <span>{{ __('messages.Total Amount') }}:</span>
                                            <span id="totalAmount" class="fw-bold">¥0</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="text-center mb-4">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-save me-2"></i>{{ __('messages.Create Order') }}
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let foodItemIndex = 0;
    
    // Add new food item row
    document.getElementById('addFoodItem').addEventListener('click', function() {
        foodItemIndex++;
        const container = document.getElementById('foodItemsContainer');
        const newRow = document.createElement('div');
        newRow.className = 'food-item-row mb-3';
        newRow.innerHTML = `
            <div class="row align-items-end">
                <div class="col-md-4">
                    <select class="form-select food-item-select" name="food_items[${foodItemIndex}][food_item_id]" required>
                        <option value="">{{ __('messages.Select Food Item') }}</option>
                        @foreach($foodItems as $foodItem)
                            <option value="{{ $foodItem->id }}" 
                                    data-price="{{ $foodItem->price }}"
                                    data-stock="{{ $foodItem->stock_quantity }}">
                                {{ $foodItem->name }} - ¥{{ number_format($foodItem->price, 0) }}
                                ({{ __('messages.Stock') }}: {{ $foodItem->stock_quantity }})
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-2">
                    <input type="number" class="form-control quantity-input" 
                           name="food_items[${foodItemIndex}][quantity_sold]" min="1" value="1" required>
                </div>
                
                <div class="col-md-2">
                    <input type="number" class="form-control unit-price-input" 
                           name="food_items[${foodItemIndex}][unit_price]" step="0.01" min="0" required>
                </div>
                
                <div class="col-md-2">
                    <input type="text" class="form-control total-price-display" readonly>
                </div>
                
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger remove-food-item">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `;
        
        container.appendChild(newRow);
        updateRemoveButtons();
        attachEventListeners(newRow);
    });
    
    // Remove food item row
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-food-item')) {
            e.target.closest('.food-item-row').remove();
            updateRemoveButtons();
            calculateTotals();
        }
    });
    
    // Update remove buttons visibility
    function updateRemoveButtons() {
        const rows = document.querySelectorAll('.food-item-row');
        rows.forEach((row, index) => {
            const removeBtn = row.querySelector('.remove-food-item');
            removeBtn.style.display = rows.length > 1 ? 'block' : 'none';
        });
    }
    
    // Attach event listeners to a food item row
    function attachEventListeners(row) {
        const select = row.querySelector('.food-item-select');
        const quantity = row.querySelector('.quantity-input');
        const unitPrice = row.querySelector('.unit-price-input');
        const totalDisplay = row.querySelector('.total-price-display');
        
        select.addEventListener('change', function() {
            const option = this.options[this.selectedIndex];
            if (option.value) {
                const price = parseFloat(option.dataset.price);
                unitPrice.value = price.toFixed(2);
                calculateRowTotal(this);
            }
        });
        
        quantity.addEventListener('input', function() {
            calculateRowTotal(this);
        });
        
        unitPrice.addEventListener('input', function() {
            calculateRowTotal(this);
        });
    }
    
    // Calculate total for a specific row
    function calculateRowTotal(element) {
        const row = element.closest('.food-item-row');
        const quantity = parseFloat(row.querySelector('.quantity-input').value) || 0;
        const unitPrice = parseFloat(row.querySelector('.unit-price-input').value) || 0;
        const total = quantity * unitPrice;
        
        row.querySelector('.total-price-display').value = '¥' + total.toFixed(2);
        calculateTotals();
    }
    
    // Calculate overall totals
    function calculateTotals() {
        let totalItems = 0;
        let totalAmount = 0;
        
        document.querySelectorAll('.food-item-row').forEach(row => {
            const quantity = parseInt(row.querySelector('.quantity-input').value) || 0;
            const unitPrice = parseFloat(row.querySelector('.unit-price-input').value) || 0;
            
            totalItems += quantity;
            totalAmount += quantity * unitPrice;
        });
        
        document.getElementById('totalItems').textContent = totalItems;
        document.getElementById('totalAmount').textContent = '¥' + totalAmount.toFixed(2);
    }
    
    // Initialize first row
    attachEventListeners(document.querySelector('.food-item-row'));
    updateRemoveButtons();
    
    // Auto-calculate when form is submitted
    document.getElementById('createOrderForm').addEventListener('submit', function() {
        calculateTotals();
    });
});
</script>

<style>
.food-item-row {
    padding: 1rem;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    background-color: #f8f9fa;
}

.food-item-row:hover {
    background-color: #e9ecef;
}

.card {
    border: 1px solid #e9ecef;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.card-header {
    border-bottom: none;
}

.btn-lg {
    padding: 1rem 2rem;
    font-size: 1.1rem;
}
</style>
@endsection
