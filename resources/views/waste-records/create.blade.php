@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="container">
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="page-title">➕ {{ __('messages.Create Waste Record') }}</h1>
                    <p class="page-subtitle">{{ __('messages.Add a new waste record to track food waste') }}</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('waste-records.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>{{ __('messages.Back to Waste Records') }}
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

        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-info-circle me-2"></i>{{ __('messages.Waste Record Information') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('waste-records.store') }}" method="POST">
                            @csrf
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="food_item_id" class="form-label">
                                        {{ __('messages.Food Item') }} <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('food_item_id') is-invalid @enderror" 
                                            id="food_item_id" name="food_item_id" required>
                                        <option value="">{{ __('messages.Select Food Item') }}</option>
                                        @foreach($foodItems as $foodItem)
                                            <option value="{{ $foodItem->id }}" 
                                                    {{ old('food_item_id') == $foodItem->id ? 'selected' : '' }}
                                                    data-price="{{ $foodItem->price }}">
                                                {{ $foodItem->name }}
                                                ({{ \App\Helpers\CurrencyHelper::format($foodItem->price) }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('food_item_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="waste_date" class="form-label">
                                        {{ __('messages.Waste Date') }} <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" class="form-control @error('waste_date') is-invalid @enderror" 
                                           id="waste_date" name="waste_date" 
                                           value="{{ old('waste_date', date('Y-m-d')) }}" required>
                                    @error('waste_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="quantity_wasted" class="form-label">
                                        {{ __('messages.Quantity Wasted') }} <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" step="0.01" min="0.01" 
                                           class="form-control @error('quantity_wasted') is-invalid @enderror" 
                                           id="quantity_wasted" name="quantity_wasted" 
                                           value="{{ old('quantity_wasted') }}" 
                                           placeholder="0.00" required>
                                    @error('quantity_wasted')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="waste_unit" class="form-label">
                                        {{ __('messages.Waste Unit') }} <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('waste_unit') is-invalid @enderror" 
                                            id="waste_unit" name="waste_unit" required>
                                        <option value="">{{ __('messages.Select Unit') }}</option>
                                        <option value="grams" {{ old('waste_unit') == 'grams' ? 'selected' : '' }}>{{ __('messages.grams') }}</option>
                                        <option value="kg" {{ old('waste_unit') == 'kg' ? 'selected' : '' }}>{{ __('messages.kg') }}</option>
                                        <option value="pieces" {{ old('waste_unit') == 'pieces' ? 'selected' : '' }}>{{ __('messages.pieces') }}</option>
                                        <option value="servings" {{ old('waste_unit') == 'servings' ? 'selected' : '' }}>{{ __('messages.servings') }}</option>
                                        <option value="portions" {{ old('waste_unit') == 'portions' ? 'selected' : '' }}>{{ __('messages.portions') }}</option>
                                    </select>
                                    @error('waste_unit')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="cost_wasted" class="form-label">
                                        {{ __('messages.Cost Wasted') }} <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <input type="number" step="0.01" min="0" 
                                               class="form-control @error('cost_wasted') is-invalid @enderror" 
                                               id="cost_wasted" name="cost_wasted" 
                                               value="{{ old('cost_wasted') }}" 
                                               placeholder="0.00" required>
                                        <span class="input-group-text">{{ app()->getLocale() === 'ja' ? '¥' : (app()->getLocale() === 'vi' ? '₫' : '$') }}</span>
                                    </div>
                                    @error('cost_wasted')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="waste_reason" class="form-label">
                                        {{ __('messages.Waste Reason') }} <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('waste_reason') is-invalid @enderror" 
                                            id="waste_reason" name="waste_reason" required>
                                        <option value="">{{ __('messages.Select Reason') }}</option>
                                        @foreach($wasteReasons as $reason)
                                            <option value="{{ $reason }}" {{ old('waste_reason') == $reason ? 'selected' : '' }}>
                                                {{ __('messages.' . $reason) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('waste_reason')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="notes" class="form-label">{{ __('messages.Notes') }}</label>
                                    <textarea class="form-control @error('notes') is-invalid @enderror" 
                                              id="notes" name="notes" rows="3" 
                                              placeholder="{{ __('messages.Enter additional notes (optional)') }}">{{ old('notes') }}</textarea>
                                    @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-save me-2"></i>{{ __('messages.Save Waste Record') }}
                                    </button>
                                    <a href="{{ route('waste-records.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times me-2"></i>{{ __('messages.Cancel') }}
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-lightbulb me-2"></i>{{ __('messages.Tips') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <h6><i class="fas fa-info-circle me-2"></i>{{ __('messages.Tips for Recording Waste') }}</h6>
                            <ul class="mb-0">
                                <li>{{ __('messages.Record waste immediately when discovered') }}</li>
                                <li>{{ __('messages.Be specific about quantities and units') }}</li>
                                <li>{{ __('messages.Provide detailed reasons for better analysis') }}</li>
                                <li>{{ __('messages.Use consistent units across records') }}</li>
                            </ul>
                        </div>

                        <div class="alert alert-warning">
                            <h6><i class="fas fa-exclamation-triangle me-2"></i>{{ __('messages.Common Waste Reasons') }}</h6>
                            <ul class="mb-0">
                                <li>{{ __('messages.Food left over after meal time') }}</li>
                                <li>{{ __('messages.Expired ingredients') }}</li>
                                <li>{{ __('messages.Customer dissatisfaction') }}</li>
                                <li>{{ __('messages.Over-preparation') }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-calculate cost based on quantity and food item price
    const foodItemSelect = document.getElementById('food_item_id');
    const quantityInput = document.getElementById('quantity_wasted');
    const costInput = document.getElementById('cost_wasted');
    
    function calculateCost() {
        const selectedOption = foodItemSelect.options[foodItemSelect.selectedIndex];
        if (selectedOption && selectedOption.dataset.price && quantityInput.value) {
            const price = parseFloat(selectedOption.dataset.price);
            const quantity = parseFloat(quantityInput.value);
            costInput.value = (price * quantity).toFixed(2);
        }
    }
    
    foodItemSelect.addEventListener('change', calculateCost);
    quantityInput.addEventListener('input', calculateCost);
});
</script>
@endsection