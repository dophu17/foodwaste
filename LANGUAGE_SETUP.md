# Cài đặt Đa ngôn ngữ - Food Waste Management System

## Tổng quan
Hệ thống này hỗ trợ 2 ngôn ngữ:
- 🇯🇵 **Tiếng Nhật** (mặc định)
- 🇻🇳 **Tiếng Việt**

## Cấu hình

### 1. File cấu hình ngôn ngữ
- **Tiếng Việt**: `lang/vi/messages.php`
- **Tiếng Nhật**: `lang/ja/messages.php`

### 2. Cấu hình Laravel
Trong `config/app.php`:
```php
'locale' => env('APP_LOCALE', 'ja'),
'fallback_locale' => env('APP_FALLBACK_LOCALE', 'ja'),
'faker_locale' => env('APP_FAKER_LOCALE', 'ja_JP'),
```

### 3. Middleware
`app/Http/Middleware/SetLocale.php` tự động đặt ngôn ngữ mặc định là tiếng Nhật nếu không có ngôn ngữ nào được chọn.

## Sử dụng

### 1. Chuyển đổi ngôn ngữ
- Sử dụng dropdown language switcher trong navigation bar
- Hoặc truy cập trực tiếp: `/language/vi` hoặc `/language/ja`

### 2. Trong Views
```php
{{ __('messages.dashboard') }}
{{ __('messages.restaurant') }}
```

### 3. Trong Controllers
```php
App::setLocale('ja'); // Đặt ngôn ngữ tiếng Nhật
App::setLocale('vi'); // Đặt ngôn ngữ tiếng Việt
```

## Thêm ngôn ngữ mới

### 1. Tạo thư mục ngôn ngữ
```bash
mkdir lang/[locale_code]
```

### 2. Tạo file messages.php
```php
<?php

return [
    'dashboard' => 'Translation here',
    // ... các key khác
];
```

### 3. Cập nhật routes
Thêm locale mới vào `routes/web.php`:
```php
if (in_array($locale, ['ja', 'vi', 'new_locale'])) {
    session()->put('locale', $locale);
}
```

### 4. Cập nhật language switcher
Thêm option mới vào các view có language switcher.

## Lưu ý
- Ngôn ngữ mặc định là tiếng Nhật
- Tất cả các key ngôn ngữ phải có trong cả hai file ngôn ngữ
- Sử dụng `__()` helper function để hiển thị text đa ngôn ngữ
- Ngôn ngữ được lưu trong session và sẽ được nhớ cho đến khi thay đổi
