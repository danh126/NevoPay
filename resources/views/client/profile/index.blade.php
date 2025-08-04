@extends('shared.client')
@section('title','Hồ sơ cá nhân | NevoPay')
@section('content')
    <div class="profile-header">
    <i class="fas fa-user-circle profile-avatar"></i>
    <h2 class="profile-name">Nguyễn Văn A</h2>
    <p class="profile-email">nguyenvana@example.com</p>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="profile-card">
                <h3>Thông Tin Cá Nhân</h3>
                <p class="profile-info"><i class="fas fa-user"></i> Họ tên: Nguyễn Văn A</p>
                <p class="profile-info"><i class="fas fa-envelope"></i> Email: nguyenvana@example.com</p>
                <p class="profile-info"><i class="fas fa-phone"></i> Số điện thoại: 0123 456 789</p>
                <p class="profile-info"><i class="fas fa-calendar"></i> Ngày sinh: 01-01-1990</p>
                <p class="profile-info"><i class="fas fa-map-marker-alt"></i> Địa chỉ: 123 Đường Láng, Hà Nội</p>
            </div>
        </div>
        <div class="col-md-6">
            <div class="edit-profile-form">
                <h3>Chỉnh Sửa Hồ Sơ</h3>
                <form id="editProfileForm">
                    <div class="mb-3">
                        <label for="fullName" class="form-label"><i class="fas fa-user"></i> Họ tên</label>
                        <input type="text" class="form-control" id="fullName" value="Nguyễn Văn A" required>
                        <div class="error-message" id="fullNameError">Họ tên không được để trống!</div>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label"><i class="fas fa-phone"></i> Số điện thoại</label>
                        <input type="tel" class="form-control" id="phone" value="0123 456 789" pattern="[0-9]{10}" required>
                        <div class="error-message" id="phoneError">Số điện thoại không hợp lệ!</div>
                    </div>
                    <div class="mb-3">
                        <label for="dob" class="form-label"><i class="fas fa-calendar"></i> Ngày sinh</label>
                        <input type="date" class="form-control" id="dob" value="1990-01-01">
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label"><i class="fas fa-map-marker-alt"></i> Địa chỉ</label>
                        <input type="text" class="form-control" id="address" value="123 Đường Láng, Hà Nội">
                    </div>
                    <div class="d-flex justify-content-between gap-2 flex-wrap">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Lưu thay đổi</button>
                        <a href="#" class="btn btn-outline-primary"><i class="fas fa-lock"></i> Đổi mật khẩu</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <p class="text-center mt-4">
        <a href="#" class="text-link"><i class="fas fa-arrow-left"></i> Quay lại Dashboard</a>
    </p>
@endsection