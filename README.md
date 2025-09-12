# WebBookingTravel

## Mục lục
1. [Giới thiệu về dự án](#1-giới-thiệu-về-dự-án)
2. [Công nghệ sử dụng](#2-công-nghệ-sử-dụng)
3. [Phương pháp phát triển](#3-phương-pháp-phát-triển)
4. [Sprint Review](#4-sprint-review)
5. [Chức năng hệ thống](#5-chức-năng-hệ-thống)
6. [Product Backlog](#6-product-backlog)
7. [Sprint Backlog](#7-sprint-backlog)
8. [Thiết kế hệ thống](#8-thiết-kế-hệ-thống)
9. [Hướng dẫn cài đặt & chạy](#9-hướng-dẫn-cài-đặt--chạy)
10. [Demo & Kết quả](#10-demo--kết-quả)


## 1. Giới thiệu về dự án
WebBookingTravel là hệ thống quản lý & đặt tour du lịch (frontend + admin) được xây dựng trên **Laravel**. Ứng dụng cho phép người dùng duyệt tour, lọc (nội địa/quốc tế, danh mục, điểm khởi hành), đặt chỗ và cho admin quản lý toàn bộ thông tin tour, người dùng, khuyến mãi, báo cáo.


## 2. Công nghệ sử dụng
- **Backend**: Laravel 12 (PHP 8.2)
- **Database**: MySQL
- **Kiến trúc**: MVC + Service Layer
- **Xác thực**: Guard Web (User), Guard Admin (Admin)
- **Triển khai**: PHP Artisan Server (local), có thể mở rộng Docker/Cloud


## 3. Phương pháp phát triển
- Mô hình **Agile/Scrum**, chia thành 9 Sprint.
- Mỗi Sprint gồm: Lập kế hoạch, thực hiện, review và retrospective.


## 4. Sprint Review
- [Sprint Meeting 1](./Sprint-Meeting-1:-19‐08‐2025)
- [Sprint Meeting 2](./Sprint-Meeting-2:-27‐08‐2025)
- [Sprint Meeting 3](./Sprint-Meeting-3:-09‐09‐2025)

## 5. Chức năng hệ thống
- **Client**:
  - Xem danh sách tour, chi tiết tour
  - Lọc tour theo loại, danh mục, điểm khởi hành
  - Đặt chỗ, đăng ký/đăng nhập
- **Admin**:
  - Quản lý Tours, Users, Bookings, Promotions
  - Hệ thống Media, Reports (placeholder)
  - Bảo mật bằng middleware, guard riêng


## 6. Product Backlog
- Quản lý Tour (CRUD, lọc, phân trang)
- Quản lý User & Booking
- Đăng ký/Đăng nhập (User & Admin)
- UI/UX tối ưu



## 7. Sprint Backlog



## 8. Thiết kế hệ thống
- **Kiến trúc tổng quan**: MVC + Service
- **Database schema**: MySQL (Tours, Categories, Users, Bookings, Promotions, Media, Reports)
- **Flow**:
  - Người dùng → Client → Laravel Controller → Service → Model → Database
  - Admin → Admin Panel → CRUD/Reports


## 9. Hướng dẫn cài đặt & chạy
1. Clone / copy mã nguồn về máy
2. Tạo file `.env` từ `.env.example` và chỉnh DB_*
3. Cài đặt phụ thuộc: `composer install`
4. Generate key: `php artisan key:generate`
5. Migration: `php artisan migrate`
6. Seed data (nếu có): `php artisan db:seed`
7. Phân quyền thư mục: `storage/`, `bootstrap/cache/`
8. Chạy server: `php artisan serve`
9. Truy cập:
   - Client: `http://127.0.0.1:8000/`
   - Admin: `http://127.0.0.1:8000/admin`


## 10. Demo & Kết quả


