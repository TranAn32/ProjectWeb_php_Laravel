# WebBookingTravel

## Mục lục

1. [Giới thiệu về dự án](#1-giới-thiệu-về-dự-án)
2. [Công nghệ sử dụng](#2-công-nghệ-sử-dụng)
3. [Phương pháp phát triển](#3-phương-pháp-phát-triển)
4. [Sprint Review](#4-sprint-review)
5. [Chức năng hệ thống](#5-chức-năng-hệ-thống)
6. [Product Backlog](#6-product-backlog)
7. [Thiết kế hệ thống](#7-thiết-kế-hệ-thống)
8. [Hướng dẫn cài đặt & chạy](#8-hướng-dẫn-cài-đặt--chạy)
9. [Demo & Kết quả](#9-demo--kết-quả)

## 1. Giới thiệu về dự án

WebBookingTravel là hệ thống quản lý và đặt tour du lịch toàn diện được xây dựng trên nền tảng Laravel. Dự án bao gồm hai phần chính: giao diện người dùng (client) cho việc duyệt và đặt tour, và hệ thống quản trị (admin) để quản lý nội dung. Ứng dụng hỗ trợ lọc tour theo danh mục, điểm khởi hành, đặt chỗ trực tuyến, quản lý slideshow, và nhiều tính năng khác để tối ưu trải nghiệm người dùng.

## 2. Công nghệ sử dụng

-   **Backend**: Laravel 10/11 (PHP 8.1+)
-   **Database**: MySQL (sử dụng Aiven cho production)
-   **Frontend**: Blade Templates, Bootstrap 5, Swiper.js, FontAwesome
-   **Kiến trúc**: MVC + Service Layer Pattern
-   **Xác thực**: Laravel Guards (Web cho client, Admin cho quản trị)
-   **Triển khai**: Railway (production), PHP Artisan Server (local)
-   **Công cụ khác**: Composer, NPM, Git, VS Code

## 3. Phương pháp phát triển

-   Mô hình **Agile/Scrum**, chia thành nhiều Sprint.
-   Mỗi Sprint gồm: Lập kế hoạch, phát triển, testing, review và retrospective.
-   Sử dụng Git cho version control, GitHub cho repository.
-   Testing thủ công và unit tests cơ bản.

## 4. Sprint Review

- [Sprint Meeting 1 - 19-08-2025](#sprint-meeting-1---19-08-2025)
- [Sprint Meeting 2 - 27-08-2025](#sprint-meeting-2---27-08-2025)
- [Sprint Meeting 3 - 09-09-2025](#sprint-meeting-3---09-09-2025)
- [Sprint Meeting 4 - 16-09-2025](#sprint-meeting-4---16-09-2025)
- [Sprint Meeting 5 - 23-09-2025](#sprint-meeting-5---23-09-2025)
- [Sprint Meeting 6 - 30-09-2025](#sprint-meeting-6---30-09-2025)
- [Sprint Meeting 7 - 10-07-2025](#sprint-meeting-7---10-07-2025)


## 5. Chức năng hệ thống

### Client (Người dùng cuối):

-   **Trang chủ**: Slideshow tự động, danh sách tour nổi bật
-   **Danh sách tour**: Phân trang, lọc theo danh mục, điểm khởi hành, loại tour (nội địa/quốc tế)
-   **Chi tiết tour**: Thông tin đầy đủ, hình ảnh, lịch trình, giá cả
-   **Đặt tour**: Form đặt chỗ với thông tin khách hàng, điểm đón, số điện thoại
-   **Quản lý booking**: Xem lịch sử đặt chỗ, hủy booking (trạng thái pending)
-   **Tài khoản**: Đăng ký, đăng nhập, cập nhật thông tin cá nhân
-   **Popup tìm kiếm**: Chọn điểm đến với search real-time

### Admin (Quản trị viên):

-   **Dashboard**: Tổng quan thống kê (tours, bookings, users)
-   **Quản lý Tours**: CRUD tours, upload nhiều ảnh, quản lý lịch trình, giá cả, khách sạn
-   **Quản lý Categories**: CRUD danh mục tour
-   **Quản lý Users**: Xem danh sách users, quản lý roles
-   **Quản lý Bookings**: Xem, cập nhật trạng thái booking, chi tiết booking
-   **Quản lý Slideshow**: Upload, xóa, sắp xếp thứ tự ảnh slideshow
-   **Bảo mật**: Middleware CheckAdmin, guard riêng biệt
-   **Toast Notifications**: Hệ thống thông báo popup cho tất cả actions

### Tính năng chung:

-   **Toast Notifications**: Thông báo popup cho success/error
-   **File Upload**: Quản lý ảnh với validation
-   **Session Management**: Xử lý login/logout với CSRF protection
-   **Database Relationships**: Quan hệ giữa tours, categories, users, bookings

## 6. Product Backlog

-   ✅ Quản lý Tour (CRUD, lọc, phân trang, upload ảnh)
-   ✅ Quản lý User & Booking (xem, cập nhật trạng thái)
-   ✅ Đăng ký/Đăng nhập (User & Admin với guards riêng)
-   ✅ UI/UX tối ưu (Bootstrap, responsive, animations)
-   ✅ Slideshow management (upload, reorder, toggle active)
-   ✅ Toast notification system (reusable component)
-   ✅ Deploy production (Railway + Aiven MySQL)
-   🔄 Reports & Analytics (placeholder for future)
-   🔄 Promotions system (placeholder for future)


## 7. Thiết kế hệ thống

### Kiến trúc tổng quan:

-   **MVC Pattern**: Controllers xử lý logic, Models tương tác DB, Views render UI
-   **Service Layer**: AdminValidationService cho logic chung admin
-   **Middleware**: CheckAdmin cho bảo mật admin routes
-   **Guards**: Web (client), Admin (admin panel)

### Database Schema (MySQL):

-   **users**: userID, userName, email, password, role, status
-   **categories**: categoryID, categoryName, type (domestic/international), slug
-   **tours**: tourID, categoryID, title, description, images (JSON), prices (JSON), itinerary (JSON), departurePoint, pickupPoint, hotels (JSON), status
-   **bookings**: bookingID, tourID, userID, bookingDate, departureDate, numAdults, numChildren, totalPrice, status, paymentStatus, specialRequest, pickup_point, phone_number
-   **slides**: id, image_path, title, link_url, sort_order, is_active (cho slideshow)

### Data Flow:

-   Client: User → Routes → Controllers → Services/Models → Views
-   Admin: Admin → Admin Routes → Admin Controllers → Models → Admin Views
-   File Upload: Storage trong [`storage/app/public`](storage/app/public), symlink đến `public/storage`

### Security:

-   CSRF protection trên tất cả forms
-   Password hashing với bcrypt
-   Session secure cookies trên production
-   Trust proxies middleware cho deploy

## 8. Hướng dẫn cài đặt & chạy

### Yêu cầu hệ thống:

-   PHP 8.1+
-   Composer
-   MySQL 8.0+
-   Node.js & NPM (cho assets nếu cần)

### Cài đặt local:

1. Clone repository:

    ```bash
    git clone <repository-url>
    cd WebBookingTravel
    ```

2. Cài đặt dependencies:

    ```bash
    composer install
    npm install  # nếu có package.json
    ```

3. Tạo file môi trường:

    ```bash
    cp .env.example .env
    ```

4. Cấu hình database trong [`.env`](.env):

    ```env
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=webbookingtravel
    DB_USERNAME=root
    DB_PASSWORD=
    ```

5. Generate application key:

    ```bash
    php artisan key:generate
    ```

6. Chạy migrations:

    ```bash
    php artisan migrate
    ```

7. Seed data (tùy chọn):

    ```bash
    php artisan db:seed
    ```

8. Tạo storage link:

    ```bash
    php artisan storage:link
    ```

9. Chạy server:

    ```bash
    php artisan serve
    ```

10. Truy cập:
    - Client: `http://127.0.0.1:8000/`
    - Admin: `http://127.0.0.1:8000/admin`

### Deploy production (Railway + Aiven):

1. Push code lên GitHub
2. Tạo project trên Railway, connect GitHub repo
3. Cấu hình environment variables:
    ```env
    APP_ENV=production
    APP_DEBUG=false
    APP_URL=https://your-railway-app.up.railway.app
    DB_CONNECTION=mysql
    DB_HOST=<aiven-host>
    DB_PORT=<aiven-port>
    DB_DATABASE=<aiven-db>
    DB_USERNAME=<aiven-user>
    DB_PASSWORD=<aiven-password>
    SESSION_SECURE_COOKIE=true
    ```
4. Railway tự động build và deploy
5. Chạy migrations trên Railway:
    ```bash
    railway run php artisan migrate --force
    ```

## 9. Demo & Kết quả

### Demo Live:

-   **Production URL**: [https://projectwebphplaravel-production.up.railway.app](https://projectwebphplaravel-production.up.railway.app) (Railway)
-   **Admin Panel**: `/admin` (credentials: cần tạo admin user)

### Screenshots:

-   Trang chủ với slideshow
-   Danh sách tour với bộ lọc
-   Form đặt tour
-   Admin dashboard
-   Quản lý tours với upload ảnh

### Kết quả đạt được:

-   ✅ Hệ thống hoạt động ổn định trên production
-   ✅ Bảo mật với authentication
-   ✅ File upload & management
-   ✅ Toast notifications cho UX tốt
-   ✅ Database relationships & queries tối ưu
-   ✅ Deploy tự động với CI/CD cơ bản

### Hướng phát triển tương lai:

-   Thêm hệ thống thanh toán online
-   Reports & analytics dashboard
-   API cho mobile app
-   Multi-language support
-   Email notifications
-   Advanced search & filters