@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="container">
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="page-title">🍽️ {{ __('messages.Create Food Item') }}</h1>
                    <p class="page-subtitle">{{ __('messages.Add a new food item to your menu') }}</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('food-item.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>{{ __('messages.Back to Food Items') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="card">

                <form action="{{ route('food-item.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <!-- Basic Information -->
                            <div class="col-md-6">
                                <h5 class="mb-3">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Thông tin cơ bản
                                </h5>

                                <div class="mb-3">
                                    <label for="menu_id" class="form-label">Menu <span class="text-danger">*</span></label>
                                    <select name="menu_id" id="menu_id" class="form-select @error('menu_id') is-invalid @enderror" required>
                                        <option value="">Chọn menu</option>
                                        @foreach($menus as $menu)
                                            <option value="{{ $menu->id }}" {{ old('menu_id', $selectedMenuId) == $menu->id ? 'selected' : '' }}>
                                                {{ $menu->name }} 
                                                @if($menu->category)
                                                    ({{ $menu->category }})
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('menu_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="name" class="form-label">Tên món ăn <span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" 
                                           value="{{ old('name') }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="description" class="form-label">Mô tả</label>
                                    <textarea name="description" id="description" rows="3" class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="price" class="form-label">Giá (VNĐ) <span class="text-danger">*</span></label>
                                    <input type="number" name="price" id="price" class="form-control @error('price') is-invalid @enderror" 
                                           value="{{ old('price') }}" min="0" step="1000" required>
                                    @error('price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="category" class="form-label">Danh mục</label>
                                            <input type="text" name="category" id="category" class="form-control @error('category') is-invalid @enderror" 
                                                   value="{{ old('category') }}" placeholder="VD: Món chính, Khai vị, Tráng miệng...">
                                            @error('category')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="cuisine_style" class="form-label">Phong cách ẩm thực</label>
                                            <input type="text" name="cuisine_style" id="cuisine_style" class="form-control @error('cuisine_style') is-invalid @enderror" 
                                                   value="{{ old('cuisine_style') }}" placeholder="VD: Truyền thống Nhật, Hiện đại, Fusion...">
                                            @error('cuisine_style')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Additional Information -->
                            <div class="col-md-6">
                                <h5 class="mb-3">
                                    <i class="fas fa-list-alt me-2"></i>
                                    Thông tin bổ sung
                                </h5>

                                <div class="mb-3">
                                    <label for="ingredients" class="form-label">Nguyên liệu</label>
                                    <textarea name="ingredients" id="ingredients" rows="3" class="form-control @error('ingredients') is-invalid @enderror" 
                                              placeholder="Liệt kê các nguyên liệu chính...">{{ old('ingredients') }}</textarea>
                                    @error('ingredients')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="allergens" class="form-label">Dị ứng</label>
                                    <textarea name="allergens" id="allergens" rows="2" class="form-control @error('allergens') is-invalid @enderror" 
                                              placeholder="Các thành phần có thể gây dị ứng...">{{ old('allergens') }}</textarea>
                                    @error('allergens')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="preparation_time" class="form-label">Thời gian chuẩn bị <span class="text-danger">*</span></label>
                                    <input type="text" name="preparation_time" id="preparation_time" class="form-control @error('preparation_time') is-invalid @enderror" 
                                           value="{{ old('preparation_time') }}" placeholder="VD: 15 phút" required>
                                    @error('preparation_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="stock_quantity" class="form-label">Số lượng tồn kho <span class="text-danger">*</span></label>
                                            <input type="number" name="stock_quantity" id="stock_quantity" class="form-control @error('stock_quantity') is-invalid @enderror" 
                                                   value="{{ old('stock_quantity', 0) }}" min="0" required>
                                            @error('stock_quantity')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="min_stock_level" class="form-label">Mức tồn kho tối thiểu <span class="text-danger">*</span></label>
                                            <input type="number" name="min_stock_level" id="min_stock_level" class="form-control @error('min_stock_level') is-invalid @enderror" 
                                                   value="{{ old('min_stock_level', 5) }}" min="0" required>
                                            @error('min_stock_level')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="image" class="form-label">Ảnh món ăn</label>
                                    <input type="file" name="image" id="image" class="form-control @error('image') is-invalid @enderror" 
                                           accept="image/*">
                                    <small class="form-text text-muted">Định dạng: JPEG, PNG, JPG, GIF. Kích thước tối đa: 2MB</small>
                                    @error('image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>



                        <!-- Availability -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="form-check">
                                    <input type="checkbox" name="is_available" id="is_available" class="form-check-input" 
                                           value="1" {{ old('is_available', true) ? 'checked' : '' }}>
                                    <label for="is_available" class="form-check-label">
                                        <i class="fas fa-check-circle me-1 text-success"></i>
                                        Món ăn có sẵn
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>
                            Lưu món ăn
                        </button>
                        <a href="{{ route('food-item.index') }}" class="btn btn-secondary ms-2">
                            <i class="fas fa-times me-1"></i>
                            Hủy
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Preview image before upload
    document.getElementById('image').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                // You can add image preview functionality here if needed
                console.log('Image selected:', file.name);
            };
            reader.readAsDataURL(file);
        }
    });
</script>
@endpush
