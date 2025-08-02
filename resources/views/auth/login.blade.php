@extends('shared.auth')
@section('title','Đăng nhập tài khoản | NevoPay')
@section('content')
    <form id="loginForm">
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
        <button type="submit" class="btn btn-primary-form">Đăng Nhập</button>
        <button type="button" class="btn btn-google mt-2">
            <i class="fab fa-google"></i> Đăng Nhập bằng Google
        </button>
        <p class="text-center mt-3">
            Chưa có tài khoản? <a href="/register" class="text-link">Đăng ký</a>
        </p>
    </form>
@endsection