@extends('dashboard')
@section('content')
<div class="row g-3">
    <!-- Thẻ Chào Mừng -->
    <div class="col-12">
        <div class="card">
            <div class="card-header">Chào Mừng, Tên Người Dùng!</div>
            <div class="card-body">
                <p class="card-text">Quản lý ví điện tử của bạn một cách dễ dàng và an toàn.</p>
            </div>
        </div>
    </div>

    <!-- Tổng Quan Số Dư -->
    <div class="col-md-6 col-12">
        <div class="card">
            <div class="card-header">Tổng Quan Số Dư</div>
            <div class="card-body">
                <h3 class="card-title">1.234.560 VNĐ</h3>
                <p class="card-text">Số Dư Hiện Có</p>
            </div>
        </div>
    </div>

    <!-- Chuyển Tiền Nhanh -->
    <div class="col-md-6 col-12">
        <div class="card">
            <div class="card-header">Chuyển Tiền Nhanh</div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="recipient" class="form-label">Người Nhận</label>
                    <input type="text" class="form-control" id="recipient" placeholder="Nhập tên người nhận">
                </div>
                <div class="mb-3">
                    <label for="amount" class="form-label">Số Tiền</label>
                    <input type="number" class="form-control" id="amount" placeholder="Nhập số tiền">
                </div>
                <button class="btn btn-primary">Chuyển Tiền</button>
            </div>
        </div>
    </div>

    <!-- Giao Dịch Gần Đây -->
    <div class="col-12">
        <div class="card">
            <div class="card-header">Giao Dịch Gần Đây</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Ngày</th>
                                <th>Loại</th>
                                <th>Số Tiền</th>
                                <th>Người Nhận/Người Gửi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>01-08-2025</td>
                                <td>Chuyển Tiền</td>
                                <td>-1.200.000 VNĐ</td>
                                <td>Nguyễn Văn A</td>
                            </tr>
                            <tr>
                                <td>30-07-2025</td>
                                <td>Nạp Tiền</td>
                                <td>+5.000.000 VNĐ</td>
                                <td>Ngân Hàng</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection