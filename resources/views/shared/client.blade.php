<!DOCTYPE html>
<html lang="vi">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>@yield('title', 'Ví điện tử | NevoPay')</title>
        
        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        
        <!-- Google Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Roboto:wght@300;400&display=swap" rel="stylesheet">
        
        <!-- Font Awesome -->
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">

        <!-- CSS -->
        <link rel="stylesheet" href="{{asset('css/client/main.css')}}">

        <!-- Favicon -->
        <link rel="shortcut icon" href="{{asset('images/logo/nevopay-icon.png')}}">
    </head>
    <body class="app-body">
        <!-- Thanh đầu trang -->
        <header class="app-header">
            <!-- Logo NevoPay -->
            <a href="/" class="text-decoration-none"><img src="{{asset('images/logo/nevopay-v2.png')}}" alt="NevoPay" srcset="" width="90"></a>
            <button class="menu-btn d-md-none"><i class="fas fa-bars"></i></button>
            <nav class="header-nav d-none d-md-flex">
                <a href="#" class="nav-link active"><i class="fas fa-user"></i> Hồ sơ</a>
                <a href="#" class="nav-link"><i class="fas fa-history"></i> Giao dịch</a>
                <a href="#" class="nav-link logout-btn"><i class="fas fa-sign-out-alt"></i> Đăng Xuất</a>
            </nav>
        </header>

        <!-- Sidebar cho mobile -->
        <nav class="app-sidebar">
            <a href="#" class="nav-link active"><i class="fas fa-user"></i> Hồ sơ</a>
            <a href="#" class="nav-link"><i class="fas fa-history"></i> Giao dịch</a>
            <a href="#" class="nav-link"><i class="fas fa-sign-out-alt"></i> Đăng Xuất</a>
        </nav>

        <!-- Nội dung chính -->
        <div class="main-content container-fluid">
            @yield('content')
        </div>

        <!-- Chân trang -->
        <footer class="app-footer">
            <div class="container text-center">
                <p class="footer-text">© {{ now()->format('Y')}} - DEV NTD</p>
            </div>
        </footer>

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    
         <script>
            // Toggle sidebar trên mobile
            function toggleSidebar() {
                document.querySelector('.app-sidebar').classList.toggle('active');
            }
            document.querySelector('.menu-btn').addEventListener('click', toggleSidebar);
        </script>
    </body>
</html>