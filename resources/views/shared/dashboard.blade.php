<!DOCTYPE html>
<html lang="vi">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>@yield('title','Trang quản trị | NevoPay')</title>
        
        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        
        <!-- Google Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Roboto:wght@300;400&display=swap" rel="stylesheet">
        
        <!-- Font Awesome -->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
        
        <!-- CSS -->
        <link rel="stylesheet" href="{{asset('css/dashboard/main.css')}}">

        <!-- Favicon -->
        <link rel="shortcut icon" href="{{asset('images/logo/nevopay-icon.png')}}">
    </head>
    <body>
        <div class="container-dashboard">
            <!-- Thanh bên -->
            <div class="sidebar" id="sidebar">
                <!-- Logo NevoPay -->
                <div class="logo">
                    <img src="{{asset('images/logo/nevopay-v2.png')}}" alt="NevoPay" srcset="" width="150px">
                </div>
                <a href="#" class="active">Tổng quan</a>
                <a href="#">Giao Dịch</a>
                <a href="#">Chuyển Tiền</a>
                <a href="#">Hồ Sơ</a>
                <a href="#">Đăng Xuất</a>
            </div>

            <!-- Nội dung chính -->
            <div class="content">
                <!-- Thanh đầu trang -->
                <header>
                    <button class="sidebar-toggle" id="toggleSidebar"><i class="fas fa-times"></i></button>
                    <h1>NevoPay - New Evolution of Payment</h1>
                    <div class="dropdown">
                        <button class="btn btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            Tên Người Dùng
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">Hồ Sơ</a></li>
                            <li><a class="dropdown-item" href="#">Đăng Xuất</a></li>
                        </ul>
                    </div>
                </header>

                <!-- Nội dung Bảng Điều Khiển -->
                <div class="container-fluid py-3">
                @yield('content')
                </div>
            </div>

            <!-- Chân trang -->
            <footer>
                <div class="container text-center">
                    <p>© {{ now()->format('Y')}} - DEV NTD</p>
                </div>
            </footer>
        </div>

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

        <!-- JS Handle -->
        <script src="{{asset('js/dashboard/toggleSidebar.js')}}"></script>
    </body>
</html>