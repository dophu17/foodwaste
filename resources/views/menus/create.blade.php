@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="container">
        <div class="page-header">
            <h1 class="page-title">🍽️ {{ __('messages.Create Menu') }}</h1>
            <p class="page-subtitle">{{ __('messages.Add a new menu for your restaurant') }}</p>
        </div>
    </div>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-plus me-2"></i>{{ __('messages.Menu Information') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('menus.store') }}" method="POST">
                            @csrf
                            
                            <div class="row">
                                <!-- Menu Name -->
                                <div class="col-md-8 mb-3">
                                    <label for="name" class="form-label">
                                        <i class="fas fa-utensils me-2"></i>{{ __('messages.Menu Name') }} *
                                    </label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name') }}" 
                                           placeholder="{{ __('messages.Enter menu name') }}">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Category -->
                                <div class="col-md-4 mb-3">
                                    <label for="category" class="form-label">
                                        <i class="fas fa-tags me-2"></i>{{ __('messages.Category') }}
                                    </label>
                                    <input type="text" class="form-control @error('category') is-invalid @enderror" 
                                           id="category" name="category" value="{{ old('category') }}" 
                                           placeholder="{{ __('messages.Enter category (e.g. Bữa sáng, Lunch, Special)') }}">
                                    <small class="form-text text-muted">
                                        {{ __('messages.Optional - helps organize your menus') }}
                                    </small>
                                    @error('category')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Description -->
                                <div class="col-12 mb-3">
                                    <label for="description" class="form-label">
                                        <i class="fas fa-align-left me-2"></i>{{ __('messages.Description') }}
                                    </label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" name="description" rows="3" 
                                              placeholder="{{ __('messages.Enter menu description (optional)') }}">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Valid From Date -->
                                <div class="col-md-6 mb-3">
                                    <label for="valid_from" class="form-label">
                                        <i class="fas fa-calendar-plus me-2"></i>{{ __('messages.Valid From') }}
                                    </label>
                                    <input type="date" class="form-control @error('valid_from') is-invalid @enderror" 
                                           id="valid_from" name="valid_from" value="{{ old('valid_from') }}" 
                                           placeholder="{{ __('messages.Leave empty for unlimited validity') }}">
                                    <small class="form-text text-muted">
                                        {{ __('messages.Leave empty if menu has unlimited validity') }}
                                    </small>
                                    @error('valid_from')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Valid Until Date -->
                                <div class="col-md-6 mb-3">
                                    <label for="valid_until" class="form-label">
                                        <i class="fas fa-calendar-minus me-2"></i>{{ __('messages.Valid Until') }}
                                    </label>
                                    <input type="date" class="form-control @error('valid_until') is-invalid @enderror" 
                                           id="valid_until" name="valid_until" value="{{ old('valid_until') }}" 
                                           placeholder="{{ __('messages.Leave empty for unlimited validity') }}">
                                    <small class="form-text text-muted">
                                        {{ __('messages.Leave empty if menu has unlimited validity') }}
                                    </small>
                                    @error('valid_until')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Active Status -->
                                <div class="col-12 mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="is_active" 
                                               name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_active">
                                            <i class="fas fa-toggle-on me-2"></i>{{ __('messages.Active Menu') }}
                                        </label>
                                        <small class="form-text text-muted">
                                            {{ __('messages.Uncheck to deactivate this menu') }}
                                        </small>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('menus.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>{{ __('messages.Back to Menus') }}
                                </a>
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save me-2"></i>{{ __('messages.Create Menu') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Help Card -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-info-circle me-2"></i>{{ __('messages.Tips for Creating Menus') }}
                        </h6>
                    </div>
                    <div class="card-body">
                        <ul class="mb-0">
                            <li>{{ __('messages.Choose a descriptive name that customers will easily recognize') }}</li>
                            <li>{{ __('messages.Select the appropriate category to help customers find your menu') }}</li>
                            <li>{{ __('messages.Set realistic validity dates to keep your menus current') }}</li>
                            <li>{{ __('messages.You can add food items to this menu after creation') }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Update valid_until minimum when valid_from changes
    document.getElementById('valid_from').addEventListener('change', function() {
        const validFrom = this.value;
        if (validFrom) {
            document.getElementById('valid_until').min = validFrom;
            
            // If valid_until is before valid_from, clear it
            if (document.getElementById('valid_until').value && 
                document.getElementById('valid_until').value < validFrom) {
                document.getElementById('valid_until').value = '';
            }
        } else {
            // If valid_from is empty, remove min restriction from valid_until
            document.getElementById('valid_until').removeAttribute('min');
        }
    });
});
</script>
@endsection
