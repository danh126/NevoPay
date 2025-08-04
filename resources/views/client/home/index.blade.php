@extends('shared.client')
@section('content')
    <p class="welcome-text">Chào mừng, Nguyễn Văn A!</p>
    <div class="row">
        <div class="col-md-6">
            <div class="balance-card">
                <h3 class="balance-amount">1.234.560 VNĐ</h3>
                <p class="balance-label">Số dư hiện có</p>
                <div class="d-flex justify-content-center gap-2 flex-wrap">
                    <a href="#" class="btn btn-primary"><i class="fas fa-exchange-alt"></i> Chuyển Tiền</a>
                    <a href="#" class="btn btn-outline-primary"><i class="fas fa-plus"></i> Nạp Tiền</a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="transfer-card">
                <h4 class="transfer-title">Chuyển Tiền Nhanh</h4>
                <form id="transferForm">
                    <div class="mb-3">
                        <label for="transferAmount" class="form-label">Số Tiền (VNĐ)</label>
                        <input type="number" class="form-control" id="transferAmount" placeholder="Nhập số tiền" min="1" required>
                        <div class="error-message" id="amountError">Số tiền phải lớn hơn 0!</div>
                    </div>
                    <div class="mb-3">
                        <label for="recipient" class="form-label">Người Nhận (Email hoặc ID)</label>
                        <input type="text" class="form-control" id="recipient" placeholder="Nhập email hoặc ID" required>
                        <div class="error-message" id="recipientError">Email hoặc ID không hợp lệ!</div>
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-exchange-alt"></i> Chuyển Tiền</button>
                </form>
            </div>
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
        <a href="#" class="text-link"><i class="fas fa-history"></i> Xem tất cả giao dịch</a> | 
        <a href="#" class="text-link"><i class="fas fa-user"></i> Hồ sơ</a> | 
        <a href="#" class="text-link"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
    </p>
@endsection