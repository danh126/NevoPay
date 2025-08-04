@extends('shared.dashboard')
@section('title', 'Quản lý người dùng | NevoPay')
@section('content')
        <h2 class="dashboard-title">Quản Lý Người Dùng</h2>
    <div class="filter-section mb-4">
        <div class="row g-3 align-items-center">
            <div class="col-md-4 col-sm-12">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control filter-search" placeholder="Tìm tên hoặc email">
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-filter"></i></span>
                    <select class="form-select filter-status">
                        <option value="all">Tất cả trạng thái</option>
                        <option value="active">Hoạt động</option>
                        <option value="locked">Bị khóa</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                    <input type="date" class="form-control filter-date" placeholder="Chọn ngày đăng ký">
                </div>
            </div>
            <div class="col-md-2 col-sm-12">
                <button class="btn btn-primary btn-filter w-100"><i class="fas fa-search"></i> Tìm kiếm</button>
            </div>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover user-table">
            <thead>
                <tr>
                    <th class="table-header">Tên</th>
                    <th class="table-header">Email</th>
                    <th class="table-header">Ngày đăng ký</th>
                    <th class="table-header">Số dư</th>
                    <th class="table-header">Trạng thái</th>
                    <th class="table-header">Hành động</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="table-cell">Nguyễn Văn A</td>
                    <td class="table-cell">nguyenvana@example.com</td>
                    <td class="table-cell">04-08-2025</td>
                    <td class="table-cell">1,234,560 VNĐ</td>
                    <td class="table-cell"><i class="fas fa-check-circle status-active"></i> Hoạt động</td>
                    <td class="table-cell">
                        <a href="#" class="btn btn-primary btn-sm"><i class="fas fa-eye"></i> Xem</a>
                        <a href="#" class="btn btn-warning btn-sm"><i class="fas fa-lock"></i> Khóa</a>
                    </td>
                </tr>
                <tr>
                    <td class="table-cell">Trần Thị B</td>
                    <td class="table-cell">tranthib@example.com</td>
                    <td class="table-cell">03-08-2025</td>
                    <td class="table-cell">2,345,678 VNĐ</td>
                    <td class="table-cell"><i class="fas fa-check-circle status-active"></i> Hoạt động</td>
                    <td class="table-cell">
                        <a href="#" class="btn btn-primary btn-sm"><i class="fas fa-eye"></i> Xem</a>
                        <a href="#" class="btn btn-warning btn-sm"><i class="fas fa-lock"></i> Khóa</a>
                    </td>
                </tr>
                <tr>
                    <td class="table-cell">Lê Văn C</td>
                    <td class="table-cell">levanc@example.com</td>
                    <td class="table-cell">02-08-2025</td>
                    <td class="table-cell">500,000 VNĐ</td>
                    <td class="table-cell"><i class="fas fa-ban status-locked"></i> Bị khóa</td>
                    <td class="table-cell">
                        <a href="#" class="btn btn-primary btn-sm"><i class="fas fa-eye"></i> Xem</a>
                        <a href="#" class="btn btn-success btn-sm"><i class="fas fa-unlock"></i> Mở khóa</a>
                    </td>
                </tr>
                <tr>
                    <td class="table-cell">Phạm Thị D</td>
                    <td class="table-cell">phamthid@example.com</td>
                    <td class="table-cell">01-08-2025</td>
                    <td class="table-cell">3,456,789 VNĐ</td>
                    <td class="table-cell"><i class="fas fa-check-circle status-active"></i> Hoạt động</td>
                    <td class="table-cell">
                        <a href="#" class="btn btn-primary btn-sm"><i class="fas fa-eye"></i> Xem</a>
                        <a href="#" class="btn btn-warning btn-sm"><i class="fas fa-lock"></i> Khóa</a>
                    </td>
                </tr>
                <tr>
                    <td class="table-cell">Hoàng Văn E</td>
                    <td class="table-cell">hoangvane@example.com</td>
                    <td class="table-cell">31-07-2025</td>
                    <td class="table-cell">1,000,000 VNĐ</td>
                    <td class="table-cell"><i class="fas fa-check-circle status-active"></i> Hoạt động</td>
                    <td class="table-cell">
                        <a href="#" class="btn btn-primary btn-sm"><i class="fas fa-eye"></i> Xem</a>
                        <a href="#" class="btn btn-warning btn-sm"><i class="fas fa-lock"></i> Khóa</a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <nav aria-label="User pagination" class="mt-4">
        <ul class="pagination justify-content-center">
            <li class="page-item disabled"><a class="page-link" href="#" data-page="prev"><i class="fas fa-angle-left"></i> Trước</a></li>
            <li class="page-item"><a class="page-link active" href="#" data-page="1">1</a></li>
            <li class="page-item"><a class="page-link" href="#" data-page="2">2</a></li>
            <li class="page-item"><a class="page-link" href="#" data-page="next">Sau <i class="fas fa-angle-right"></i></a></li>
        </ul>
    </nav>
@endsection