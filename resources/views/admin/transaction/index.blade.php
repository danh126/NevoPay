@extends('shared.dashboard')
@section('title', 'Quản lý giao dịch | NevoPay')
@section('content')
    <h2 class="dashboard-title">Quản Lý Giao Dịch</h2>
    <div class="filter-section mb-4">
        <div class="row g-3 align-items-center">
            <div class="col-md-4 col-sm-12">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control filter-search" placeholder="Tìm mã GD, email người gửi/nhận">
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-filter"></i></span>
                    <select class="form-select filter-status">
                        <option value="all">Tất cả trạng thái</option>
                        <option value="success">Thành công</option>
                        <option value="failed">Thất bại</option>
                        <option value="pending">Đang chờ</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                    <input type="date" class="form-control filter-date" placeholder="Chọn ngày">
                </div>
            </div>
            <div class="col-md-2 col-sm-12">
                <button class="btn btn-primary btn-filter w-100"><i class="fas fa-search"></i> Tìm kiếm</button>
            </div>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover transaction-table">
            <thead>
                <tr>
                    <th class="table-header">Mã GD</th>
                    <th class="table-header">Người gửi</th>
                    <th class="table-header">Người nhận</th>
                    <th class="table-header">Số tiền</th>
                    <th class="table-header">Ngày</th>
                    <th class="table-header">Trạng thái</th>
                    <th class="table-header">Hành động</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="table-cell">TX123456</td>
                    <td class="table-cell">Nguyễn Văn A</td>
                    <td class="table-cell">Trần Thị B</td>
                    <td class="table-cell">-1,200,000 VNĐ</td>
                    <td class="table-cell">04-08-2025</td>
                    <td class="table-cell"><i class="fas fa-check-circle status-success"></i> Thành công</td>
                    <td class="table-cell">
                        <a href="#" class="btn btn-primary btn-sm"><i class="fas fa-eye"></i> Xem</a>
                    </td>
                </tr>
                <tr>
                    <td class="table-cell">TX123457</td>
                    <td class="table-cell">Trần Thị B</td>
                    <td class="table-cell">Nạp từ thẻ</td>
                    <td class="table-cell">+5,000,000 VNĐ</td>
                    <td class="table-cell">04-08-2025</td>
                    <td class="table-cell"><i class="fas fa-check-circle status-success"></i> Thành công</td>
                    <td class="table-cell">
                        <a href="#" class="btn btn-primary btn-sm"><i class="fas fa-eye"></i> Xem</a>
                    </td>
                </tr>
                <tr>
                    <td class="table-cell">TX123458</td>
                    <td class="table-cell">Nguyễn Văn A</td>
                    <td class="table-cell">Trần Thị C</td>
                    <td class="table-cell">-500,000 VNĐ</td>
                    <td class="table-cell">03-08-2025</td>
                    <td class="table-cell"><i class="fas fa-times-circle status-failed"></i> Thất bại</td>
                    <td class="table-cell">
                        <a href="#" class="btn btn-primary btn-sm"><i class="fas fa-eye"></i> Xem</a>
                    </td>
                </tr>
                <tr>
                    <td class="table-cell">TX123459</td>
                    <td class="table-cell">Lê Văn C</td>
                    <td class="table-cell">Nạp từ thẻ</td>
                    <td class="table-cell">+2,000,000 VNĐ</td>
                    <td class="table-cell">03-08-2025</td>
                    <td class="table-cell"><i class="fas fa-hourglass-half status-pending"></i> Đang chờ</td>
                    <td class="table-cell">
                        <a href="#" class="btn btn-primary btn-sm"><i class="fas fa-eye"></i> Xem</a>
                        <a href="#" class="btn btn-success btn-sm"><i class="fas fa-check"></i> Duyệt</a>
                        <a href="#" class="btn btn-danger btn-sm"><i class="fas fa-ban"></i> Hủy</a>
                    </td>
                </tr>
                <tr>
                    <td class="table-cell">TX123460</td>
                    <td class="table-cell">Phạm Thị D</td>
                    <td class="table-cell">Hoàng Văn E</td>
                    <td class="table-cell">-800,000 VNĐ</td>
                    <td class="table-cell">02-08-2025</td>
                    <td class="table-cell"><i class="fas fa-hourglass-half status-pending"></i> Đang chờ</td>
                    <td class="table-cell">
                        <a href="#" class="btn btn-primary btn-sm"><i class="fas fa-eye"></i> Xem</a>
                        <a href="#" class="btn btn-success btn-sm"><i class="fas fa-check"></i> Duyệt</a>
                        <a href="#" class="btn btn-danger btn-sm"><i class="fas fa-ban"></i> Hủy</a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <nav aria-label="Transaction pagination" class="mt-4">
        <ul class="pagination justify-content-center">
            <li class="page-item"><a class="page-link" href="#" data-page="prev"><i class="fas fa-angle-left"></i> Trước</a></li>
            <li class="page-item"><a class="page-link active" href="#" data-page="1">1</a></li>
            <li class="page-item"><a class="page-link" href="#" data-page="2">2</a></li>
            <li class="page-item"><a class="page-link" href="#" data-page="next">Sau <i class="fas fa-angle-right"></i></a></li>
        </ul>
    </nav>
@endsection