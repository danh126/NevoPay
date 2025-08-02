@extends('shared.auth')
@section('title','Đăng ký tài khoản | NevoPay')
@section('content')
    <form>
        <div class="mb-3">
            <label for="fullName" class="form-label">Họ và Tên</label>
            <input type="text" class="form-control" id="fullName" placeholder="Nhập họ và tên" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" placeholder="Nhập email" required>
        </div>
        <div class="mb-3 password-field">
            <label for="password" class="form-label">Mật Khẩu</label>
            <input type="password" class="form-control" id="password" placeholder="Nhập mật khẩu" required>
            <button type="button" class="password-toggle" onclick="togglePassword('password', this)">
                <i class="fas fa-eye"></i>
            </button>
        </div>
        <div class="mb-3 password-field">
            <label for="confirmPassword" class="form-label">Xác Nhận Mật Khẩu</label>
            <input type="password" class="form-control" id="confirmPassword" placeholder="Xác nhận mật khẩu" required>
            <button type="button" class="password-toggle" onclick="togglePassword('confirmPassword', this)">
                <i class="fas fa-eye"></i>
            </button>
        </div>
        <button type="submit" class="btn btn-primary-form">Đăng Ký</button>
        <p class="text-center mt-3">
            Đã có tài khoản? <a href="/login" class="text-link">Đăng nhập</a>
        </p>
    </form>
@endsection