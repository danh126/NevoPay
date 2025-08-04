@extends('shared.client')
@section('title', 'Giao dịch | NevoPay')
@section('content')
    <h2 class="transaction-title">Lịch Sử Giao Dịch</h2>
    <div class="filter-form">
        <form id="filterForm">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="transactionType" class="form-label"><i class="fas fa-filter"></i> Loại giao dịch</label>
                    <select class="form-select" id="transactionType">
                        <option value="all">Tất cả</option>
                        <option value="transfer">Chuyển Tiền</option>
                        <option value="deposit">Nạp Tiền</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="startDate" class="form-label"><i class="fas fa-calendar"></i> Từ ngày</label>
                    <input type="date" class="form-control" id="startDate">
                </div>
                <div class="col-md-4 mb-3">
                    <label for="endDate" class="form-label"><i class="fas fa-calendar"></i> Đến ngày</label>
                    <input type="date" class="form-control" id="endDate">
                </div>
            </div>
            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Lọc</button>
            </div>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table table-hover transaction-table">
            <thead>
                <tr>
                    <th class="table-header">Ngày</th>
                    <th class="table-header">Loại</th>
                    <th class="table-header">Số Tiền</th>
                    <th class="table-header">Mô tả</th>
                    <th class="table-header">Trạng thái</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="table-cell">01-08-2025</td>
                    <td class="table-cell">Chuyển Tiền</td>
                    <td class="table-cell">-1.200.000 VNĐ</td>
                    <td class="table-cell">Chuyển cho Nguyễn Văn B</td>
                    <td class="table-cell"><i class="fas fa-check-circle status-success"></i> Thành công</td>
                </tr>
                <tr>
                    <td class="table-cell">30-07-2025</td>
                    <td class="table-cell">Nạp Tiền</td>
                    <td class="table-cell">+5.000.000 VNĐ</td>
                    <td class="table-cell">Nạp qua ngân hàng Vietcombank</td>
                    <td class="table-cell"><i class="fas fa-check-circle status-success"></i> Thành công</td>
                </tr>
                <tr>
                    <td class="table-cell">28-07-2025</td>
                    <td class="table-cell">Chuyển Tiền</td>
                    <td class="table-cell">-500.000 VNĐ</td>
                    <td class="table-cell">Chuyển cho Trần Thị C</td>
                    <td class="table-cell"><i class="fas fa-times-circle status-failed"></i> Thất bại</td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="pagination-nav">
        <a href="#" class="pagination-btn"><i class="fas fa-chevron-left"></i> Trang trước</a>
        <a href="#" class="pagination-btn"><i class="fas fa-chevron-right"></i> Trang sau</a>
    </div>
    <p class="text-center mt-4">
        <a href="#" class="text-link"><i class="fas fa-arrow-left"></i> Quay lại Dashboard</a> | 
        <a href="#" class="text-link"><i class="fas fa-user"></i> Hồ sơ</a>
    </p> 
@endsection