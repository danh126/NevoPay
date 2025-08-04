@extends('shared.dashboard')
@section('title','Tổng quan | NevoPay')
@section('content')
    <h2 class="dashboard-title">Tổng Quan Admin</h2>
    <div class="row">
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="stats-card">
                <i class="fas fa-users"></i>
                <h3>Tổng số người dùng</h3>
                <p>1,234</p>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="stats-card">
                <i class="fas fa-exchange-alt"></i>
                <h3>Tổng giao dịch</h3>
                <p>5,678</p>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="stats-card">
                <i class="fas fa-money-bill-wave"></i>
                <h3>Doanh thu phí</h3>
                <p>12,345,678 VNĐ</p>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="stats-card">
                <i class="fas fa-wallet"></i>
                <h3>Số dư hệ thống</h3>
                <p>123,456,789 VNĐ</p>
            </div>
        </div>
    </div>
    <h3 class="mt-4" style="color: #243f65;">Người Dùng Gần Đây</h3>
    <div class="table-responsive">
        <table class="table table-hover user-table">
            <thead>
                <tr>
                    <th class="table-header">Tên</th>
                    <th class="table-header">Email</th>
                    <th class="table-header">Ngày đăng ký</th>
                    <th class="table-header">Số dư</th>
                    <th class="table-header">Hành động</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="table-cell">Nguyễn Văn A</td>
                    <td class="table-cell">nguyenvana@example.com</td>
                    <td class="table-cell">01-08-2025</td>
                    <td class="table-cell">1,234,560 VNĐ</td>
                    <td class="table-cell">
                        <a href="#" class="btn btn-primary"><i class="fas fa-eye"></i> Xem chi tiết</a>
                    </td>
                </tr>
                <tr>
                    <td class="table-cell">Trần Thị B</td>
                    <td class="table-cell">tranthib@example.com</td>
                    <td class="table-cell">31-07-2025</td>
                    <td class="table-cell">2,345,678 VNĐ</td>
                    <td class="table-cell">
                        <a href="#" class="btn btn-primary"><i class="fas fa-eye"></i> Xem chi tiết</a>
                    </td>
                </tr>
                <tr>
                    <td class="table-cell">Lê Văn C</td>
                    <td class="table-cell">levanc@example.com</td>
                    <td class="table-cell">30-07-2025</td>
                    <td class="table-cell">500,000 VNĐ</td>
                    <td class="table-cell">
                        <a href="#" class="btn btn-primary"><i class="fas fa-eye"></i> Xem chi tiết</a>
                    </td>
                </tr>
                <tr>
                    <td class="table-cell">Phạm Thị D</td>
                    <td class="table-cell">phamthid@example.com</td>
                    <td class="table-cell">29-07-2025</td>
                    <td class="table-cell">3,456,789 VNĐ</td>
                    <td class="table-cell">
                        <a href="#" class="btn btn-primary"><i class="fas fa-eye"></i> Xem chi tiết</a>
                    </td>
                </tr>
                <tr>
                    <td class="table-cell">Hoàng Văn E</td>
                    <td class="table-cell">hoangvane@example.com</td>
                    <td class="table-cell">28-07-2025</td>
                    <td class="table-cell">1,000,000 VNĐ</td>
                    <td class="table-cell">
                        <a href="#" class="btn btn-primary"><i class="fas fa-eye"></i> Xem chi tiết</a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <h3 class="mt-4" style="color: #243f65;">Giao Dịch Gần Đây</h3>
    <div class="table-responsive">
        <table class="table table-hover transaction-table">
            <thead>
                <tr>
                    <th class="table-header">Mã GD</th>
                    <th class="table-header">Người gửi</th>
                    <th class="table-header">Người nhận</th>
                    <th class="table-header">Số tiền</th>
                    <th class="table-header">Trạng thái</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="table-cell">TX123456</td>
                    <td class="table-cell">Nguyễn Văn A</td>
                    <td class="table-cell">Trần Thị B</td>
                    <td class="table-cell">-1,200,000 VNĐ</td>
                    <td class="table-cell"><i class="fas fa-check-circle status-success"></i> Thành công</td>
                </tr>
                <tr>
                    <td class="table-cell">TX123457</td>
                    <td class="table-cell">Trần Thị B</td>
                    <td class="table-cell">Nạp từ thẻ</td>
                    <td class="table-cell">+5,000,000 VNĐ</td>
                    <td class="table-cell"><i class="fas fa-check-circle status-success"></i> Thành công</td>
                </tr>
                <tr>
                    <td class="table-cell">TX123458</td>
                    <td class="table-cell">Nguyễn Văn A</td>
                    <td class="table-cell">Trần Thị C</td>
                    <td class="table-cell">-500,000 VNĐ</td>
                    <td class="table-cell"><i class="fas fa-times-circle status-failed"></i> Thất bại</td>
                </tr>
                <tr>
                    <td class="table-cell">TX123459</td>
                    <td class="table-cell">Lê Văn C</td>
                    <td class="table-cell">Nạp từ thẻ</td>
                    <td class="table-cell">+2,000,000 VNĐ</td>
                    <td class="table-cell"><i class="fas fa-check-circle status-success"></i> Thành công</td>
                </tr>
                <tr>
                    <td class="table-cell">TX123460</td>
                    <td class="table-cell">Phạm Thị D</td>
                    <td class="table-cell">Hoàng Văn E</td>
                    <td class="table-cell">-800,000 VNĐ</td>
                    <td class="table-cell"><i class="fas fa-check-circle status-success"></i> Thành công</td>
                </tr>
            </tbody>
        </table>
    </div>
@endsection