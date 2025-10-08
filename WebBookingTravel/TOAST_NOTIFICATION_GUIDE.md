# Hướng dẫn sử dụng Toast Notification Component

## 📍 Component đã được tạo tại:

`resources/views/components/toast-notification.blade.php`

## 🚀 Cách sử dụng:

### 1. Import component vào view:

```blade
{{-- Thêm vào đầu view cần sử dụng --}}
@include('components.toast-notification')
```

### 2. Hiển thị notifications từ Laravel Session:

Component sẽ tự động hiển thị các thông báo từ:

-   `session('success')` - Thông báo thành công
-   `session('error')` - Thông báo lỗi
-   `session('warning')` - Thông báo cảnh báo
-   `session('info')` - Thông báo thông tin
-   `$errors` - Lỗi validation

### 3. Hiển thị thông báo bằng JavaScript:

#### Các function có sẵn:

```javascript
// Thông báo thành công
showSuccessToast("Thành công!", "Dữ liệu đã được lưu");

// Thông báo lỗi
showErrorToast("Lỗi!", "Không thể xóa dữ liệu");

// Thông báo cảnh báo
showWarningToast("Cảnh báo!", "Dữ liệu sẽ bị xóa vĩnh viễn");

// Thông báo thông tin
showInfoToast("Thông tin!", "Có bản cập nhật mới");
```

#### Function tổng quát:

```javascript
showToast(type, title, message, duration);

// Ví dụ:
showToast("success", "Hoàn thành!", "Tour đã được tạo", 3000);
```

### 4. Các loại thông báo:

-   **success**: Màu xanh lá - Thành công
-   **error**: Màu đỏ - Lỗi
-   **warning**: Màu vàng - Cảnh báo
-   **info**: Màu xanh dương - Thông tin

### 5. Tùy chỉnh thời gian hiển thị:

```javascript
// Mặc định: 2000ms (2 giây)
showToast("success", "Thành công!", "Đã lưu", 5000); // 5 giây
```

## 📋 Ví dụ sử dụng trong Controller:

```php
// Trong Controller
public function store(Request $request)
{
    try {
        // Logic xử lý...

        return redirect()->route('admin.tours.index')
            ->with('success', 'Tour đã được tạo thành công!');

    } catch (Exception $e) {
        return redirect()->back()
            ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage())
            ->withInput();
    }
}
```

## 📱 Responsive:

-   Desktop: Hiển thị ở góc trên bên phải
-   Mobile: Hiển thị full width với margin 10px

## 🎨 Đặc điểm:

-   ✅ Tự động ẩn sau 2 giây
-   ✅ Có nút đóng thủ công
-   ✅ Animation mượt mà
-   ✅ Responsive design
-   ✅ Hỗ trợ multiple toasts
-   ✅ Icons tương ứng với từng loại

## 🔧 Cài đặt trong các view khác:

### Ví dụ 1: Admin Booking Index

```blade
@extends('admin.layouts.app')

@section('content')
    {{-- Include toast component --}}
    @include('components.toast-notification')

    {{-- Nội dung page --}}
    <div class="container">
        <!-- Content here -->
    </div>
@endsection
```

### Ví dụ 2: Client views

```blade
@extends('client.layouts.app')

@section('content')
    {{-- Include toast component --}}
    @include('components.toast-notification')

    {{-- Nội dung page --}}
    <div class="container">
        <!-- Content here -->
    </div>
@endsection
```

## 💡 Tips:

1. Chỉ cần include component một lần trong mỗi view
2. Component sẽ tự động xử lý session messages
3. Có thể gọi JavaScript functions từ bất kỳ đâu
4. Responsive tự động
5. Z-index cao để hiển thị trên tất cả elements khác
