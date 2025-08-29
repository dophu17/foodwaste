@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="container">
        <div class="page-header">
            <h1 class="page-title">🏪 {{ __('messages.Edit Restaurant') }}</h1>
            <p class="page-subtitle">{{ __('messages.Update your restaurant profile information') }}</p>
        </div>
    </div>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-store me-2"></i>{{ __('messages.Restaurant Information') }}
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <form method="POST" action="{{ route('restaurant.update', $restaurant->id) }}" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">
                                        <i class="fas fa-signature me-2"></i>{{ __('messages.Restaurant Name') }} *
                                    </label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name', $restaurant->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="cuisine_type" class="form-label">
                                        <i class="fas fa-utensils me-2"></i>{{ __('messages.Cuisine Type') }}
                                    </label>
                                    <input type="text" class="form-control @error('cuisine_type') is-invalid @enderror" 
                                           id="cuisine_type" name="cuisine_type" 
                                           placeholder="{{ __('messages.Enter cuisine type (optional)') }}"
                                           value="{{ old('cuisine_type', $restaurant->cuisine_type) }}">
                                    @error('cuisine_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">
                                        <i class="fas fa-phone me-2"></i>{{ __('messages.Phone Number') }} *
                                    </label>
                                    <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                           id="phone" name="phone" value="{{ old('phone', $restaurant->phone) }}" required>
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">
                                        <i class="fas fa-envelope me-2"></i>{{ __('messages.Email Address') }} *
                                    </label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           id="email" name="email" value="{{ old('email', $restaurant->email) }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="address" class="form-label">
                                    <i class="fas fa-map-marker-alt me-2"></i>{{ __('messages.Address') }} *
                                </label>
                                <textarea class="form-control @error('address') is-invalid @enderror" 
                                          id="address" name="address" rows="3" required>{{ old('address', $restaurant->address) }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="capacity" class="form-label">
                                        <i class="fas fa-users me-2"></i>{{ __('messages.Seating Capacity') }} *
                                    </label>
                                    <input type="number" class="form-control @error('capacity') is-invalid @enderror" 
                                           id="capacity" name="capacity" min="1" max="1000" 
                                           value="{{ old('capacity', $restaurant->capacity) }}" required>
                                    @error('capacity')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="business_hours" class="form-label">
                                        <i class="fas fa-clock me-2"></i>{{ __('messages.Business Hours') }} *
                                    </label>
                                    <input type="text" class="form-control @error('business_hours') is-invalid @enderror" 
                                           id="business_hours" name="business_hours" 
                                           placeholder="e.g. 9:00 AM - 10:00 PM" 
                                           value="{{ old('business_hours', $restaurant->business_hours) }}" required>
                                    @error('business_hours')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">
                                    <i class="fas fa-align-left me-2"></i>{{ __('messages.Description') }}
                                </label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" name="description" rows="4" 
                                          placeholder="{{ __('messages.Enter restaurant description (optional)') }}">{{ old('description', $restaurant->description) }}</textarea>
                                <div class="form-text">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Tell customers about your restaurant's unique features, specialties, and atmosphere.
                                </div>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="status" class="form-label">
                                    <i class="fas fa-toggle-on me-2"></i>{{ __('messages.Status') }}
                                </label>
                                <select class="form-select @error('status') is-invalid @enderror" 
                                        id="status" name="status">
                                    <option value="active" {{ old('status', $restaurant->status) == 'active' ? 'selected' : '' }}>
                                        🟢 {{ __('messages.Active') }}
                                    </option>
                                    <option value="inactive" {{ old('status', $restaurant->status) == 'inactive' ? 'selected' : '' }}>
                                        🔴 {{ __('messages.Inactive') }}
                                    </option>
                                    <option value="maintenance" {{ old('status', $restaurant->status) == 'maintenance' ? 'selected' : '' }}>
                                        🔧 {{ __('messages.Maintenance') }}
                                    </option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-between align-items-center">
                                <a href="{{ route('restaurant.show', $restaurant->id) }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>{{ __('messages.Back to Restaurant') }}
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>{{ __('messages.Update Restaurant') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Tips Card -->
                <div class="card mt-4">
                    <div class="card-header bg-info text-white">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-lightbulb me-2"></i>💡 {{ __('messages.Tips for Better Food Waste Management') }}
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <ul class="list-unstyled">
                                    <li class="mb-2">
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        {{ __('messages.Set realistic portion sizes') }}
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        {{ __('messages.Track waste patterns daily') }}
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        {{ __('messages.Use AI predictions for ordering') }}
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul class="list-unstyled">
                                    <li class="mb-2">
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        {{ __('messages.Train staff on waste reduction') }}
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        {{ __('messages.Monitor seasonal trends') }}
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        {{ __('messages.Regular menu optimization') }}
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Phone number formatting
    document.getElementById('phone').addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length > 0) {
            if (value.length <= 3) {
                value = value;
            } else if (value.length <= 7) {
                value = value.slice(0, 3) + '-' + value.slice(3);
            } else {
                value = value.slice(0, 3) + '-' + value.slice(3, 7) + '-' + value.slice(7, 11);
            }
        }
        e.target.value = value;
    });

    // Form validation enhancement
    document.querySelector('form').addEventListener('submit', function(e) {
        const requiredFields = this.querySelectorAll('[required]');
        let isValid = true;

        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                field.classList.add('is-invalid');
                isValid = false;
            } else {
                field.classList.remove('is-invalid');
            }
        });

        if (!isValid) {
            e.preventDefault();
            // Show error message
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-danger alert-dismissible fade show';
            alertDiv.innerHTML = `
                <i class="fas fa-exclamation-triangle me-2"></i>
                Please fill in all required fields.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            this.insertBefore(alertDiv, this.firstChild);
        }
    });

    // Auto-save form data to localStorage
    const form = document.querySelector('form');
    const formData = new FormData(form);

    // Save form data on input
    form.querySelectorAll('input, textarea, select').forEach(field => {
        field.addEventListener('input', function() {
            const formData = new FormData(form);
            const data = {};
            for (let [key, value] of formData.entries()) {
                data[key] = value;
            }
            localStorage.setItem('restaurantEditFormData', JSON.stringify(data));
        });
    });

    // Load saved form data
    document.addEventListener('DOMContentLoaded', function() {
        const savedData = localStorage.getItem('restaurantEditFormData');
        if (savedData) {
            const data = JSON.parse(savedData);
            Object.keys(data).forEach(key => {
                const field = form.querySelector(`[name="${key}"]`);
                if (field && !field.value) {
                    field.value = data[key];
                }
            });
        }
    });
</script>
@endpush
