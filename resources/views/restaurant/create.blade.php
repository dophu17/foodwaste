@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
            <div class="page-header">
        <div class="container">
            <h1 class="page-title">🏪 {{ __('messages.Create Restaurant') }}</h1>
            <p class="page-subtitle">{{ __('messages.Set up your restaurant profile to start managing food waste efficiently') }}</p>
        </div>
    </div>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-store me-2"></i>{{ __('messages.Restaurant Information') }}
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <form method="POST" action="{{ route('restaurant.store') }}" enctype="multipart/form-data">
                            @csrf
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">
                                        <i class="fas fa-signature me-2"></i>{{ __('messages.Restaurant Name') }} *
                                    </label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="cuisine_type" class="form-label">
                                        <i class="fas fa-utensils me-2"></i>{{ __('messages.Cuisine Type') }} *
                                    </label>
                                    <select class="form-select @error('cuisine_type') is-invalid @enderror" 
                                            id="cuisine_type" name="cuisine_type" required>
                                        <option value="">{{ __('messages.Select cuisine type') }}</option>
                                        <option value="Japanese" {{ old('cuisine_type') == 'Japanese' ? 'selected' : '' }}>🍣 Japanese</option>
                                        <option value="Italian" {{ old('cuisine_type') == 'Italian' ? 'selected' : '' }}>🍝 Italian</option>
                                        <option value="Chinese" {{ old('cuisine_type') == 'Chinese' ? 'selected' : '' }}>🥢 Chinese</option>
                                        <option value="French" {{ old('cuisine_type') == 'French' ? 'selected' : '' }}>🥖 French</option>
                                        <option value="Thai" {{ old('cuisine_type') == 'Thai' ? 'selected' : '' }}>🌶️ Thai</option>
                                        <option value="Indian" {{ old('cuisine_type') == 'Indian' ? 'selected' : '' }}>🍛 Indian</option>
                                        <option value="Mexican" {{ old('cuisine_type') == 'Mexican' ? 'selected' : '' }}>🌮 Mexican</option>
                                        <option value="American" {{ old('cuisine_type') == 'American' ? 'selected' : '' }}>🍔 American</option>
                                        <option value="Fusion" {{ old('cuisine_type') == 'Fusion' ? 'selected' : '' }}>🌟 Fusion</option>
                                        <option value="Other" {{ old('cuisine_type') == 'Other' ? 'selected' : '' }}>✨ Other</option>
                                    </select>
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
                                           id="phone" name="phone" value="{{ old('phone') }}" required>
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">
                                        <i class="fas fa-envelope me-2"></i>{{ __('messages.Email Address') }} *
                                    </label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           id="email" name="email" value="{{ old('email') }}" required>
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
                                          id="address" name="address" rows="3" required>{{ old('address') }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="capacity" class="form-label">
                                        <i class="fas fa-users me-2"></i>Seating Capacity *
                                    </label>
                                    <input type="number" class="form-control @error('capacity') is-invalid @enderror" 
                                           id="capacity" name="capacity" value="{{ old('capacity') }}" min="1" required>
                                    @error('capacity')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="business_hours" class="form-label">
                                        <i class="fas fa-clock me-2"></i>Business Hours *
                                    </label>
                                    <input type="text" class="form-control @error('business_hours') is-invalid @enderror" 
                                           id="business_hours" name="business_hours" 
                                           value="{{ old('business_hours', '9:00 AM - 10:00 PM') }}" required>
                                    @error('business_hours')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">
                                    <i class="fas fa-align-left me-2"></i>Restaurant Description
                                </label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" name="description" rows="4">{{ old('description') }}</textarea>
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
                                    <i class="fas fa-toggle-on me-2"></i>Status *
                                </label>
                                <select class="form-select @error('status') is-invalid @enderror" 
                                        id="status" name="status" required>
                                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>🟢 Active</option>
                                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>🔴 Inactive</option>
                                    <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>🔧 Under Maintenance</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-between align-items-center">
                                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>{{ __('messages.Back to Dashboard') }}
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>{{ __('messages.Create Restaurant') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Tips Card -->
                <div class="card mt-4">
                    <div class="card-header bg-info text-white">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-lightbulb me-2"></i>💡 Tips for Better Food Waste Management
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <ul class="list-unstyled">
                                    <li class="mb-2">
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        Set realistic portion sizes
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        Track waste patterns daily
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        Use AI predictions for ordering
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <ul class="list-unstyled">
                                    <li class="mb-2">
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        Train staff on waste reduction
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        Monitor seasonal trends
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        Regular menu optimization
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
            localStorage.setItem('restaurantFormData', JSON.stringify(data));
        });
    });

    // Load saved form data
    document.addEventListener('DOMContentLoaded', function() {
        const savedData = localStorage.getItem('restaurantFormData');
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
