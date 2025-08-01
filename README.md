# 🏦 NevoPay – New Evolution of Payment

**NevoPay** là một dự án mô phỏng ví điện tử cá nhân được xây dựng bằng **Laravel**. Dự án cho phép người dùng:

-   Quản lý ví ảo
-   Nạp tiền / Rút tiền (giả lập)
-   Chuyển tiền giữa tài khoản người dùng
-   Theo dõi lịch sử giao dịch

---

## 🚀 Tính năng chính

-   ✅ Đăng ký / Đăng nhập
-   💼 Tự động tạo ví khi người dùng đăng ký
-   ➕ Nạp tiền (mô phỏng)
-   ➖ Rút tiền (mô phỏng)
-   🔁 Chuyển tiền giữa người dùng (có kiểm tra số dư)
-   📄 Lưu lịch sử giao dịch chi tiết
-   🔒 Xử lý giao dịch bằng `DB::transaction()` để đảm bảo tính toàn vẹn
-   📊 Dashboard hiển thị số dư & thống kê giao dịch

---

## 🧱 Công nghệ sử dụng

-   [Laravel 12+](https://laravel.com)
-   HTML, CSS, Bootstrap
-   MySQL
-   Laravel Breeze (Auth)
-   Laravel Eloquent (ORM)

---

## 🧰 Cài đặt nhanh

```bash
git clone https://github.com/danh126/NevoPay.git

cd NevoPay

composer install
cp .env.example .env
php artisan key:generate

# Tạo database tên 'nevopay', sau đó chạy:
php artisan migrate --seed

php artisan serve
```
