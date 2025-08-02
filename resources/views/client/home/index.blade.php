@extends('shared.client')
@section('content')
    <p class="welcome-text">Chào mừng, Nguyễn Văn A!</p>
    <div class="balance-card">
        <h3 class="balance-amount">1.234.560 VNĐ</h3>
        <p class="balance-label">Số dư hiện có</p>
        <div class="d-flex justify-content-center gap-2 flex-wrap">
            <a href="#" class="btn btn-primary">Chuyển Tiền</a>
            <a href="#" class="btn btn-outline-primary">Nạp Tiền</a>
        </div>
    </div>
    <h4 class="mb-3" style="color: #243f65;">Giao Dịch Gần Đây</h4>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th class="table-header">Ngày</th>
                    <th class="table-header">Loại</th>
                    <th class="table-header">Số Tiền</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="table-cell">01-08-2025</td>
                    <td class="table-cell">Chuyển Tiền</td>
                    <td class="table-cell">-1.200.000 VNĐ</td>
                </tr>
                <tr>
                    <td class="table-cell">30-07-2025</td>
                    <td class="table-cell">Nạp Tiền</td>
                    <td class="table-cell">+5.000.000 VNĐ</td>
                </tr>
                <tr>
                    <td class="table-cell">28-07-2025</td>
                    <td class="table-cell">Chuyển Tiền</td>
                    <td class="table-cell">-500.000 VNĐ</td>
                </tr>
            </tbody>
        </table>
    </div>
    <p class="text-center mt-4">
        <a href="#" class="text-link">Xem tất cả giao dịch</a> | 
        <a href="#" class="text-link">Hồ sơ</a> | 
        <a href="#" class="text-link">Đăng xuất</a>
    </p>
@endsection