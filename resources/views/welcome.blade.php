<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>@yield('title','Trang chủ | NevoPay')</title>
        
        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        
        <!-- Google Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Roboto:wght@300;400&display=swap" rel="stylesheet">

        <!-- CSS -->
        <link rel="stylesheet" href="{{asset('css/client/main.css')}}">

        <!-- Favicon -->
        <link rel="shortcut icon" href="{{asset('images/logo/nevopay-icon.png')}}">
    </head>
    <body>
        <!-- Header -->
        <header class="text-white py-3">
            <!-- Logo NevoPay -->
            <div class="container">
                <a href="/" class="text-decoration-none"><img src="{{asset('images/logo/nevopay-v2.png')}}" alt="NevoPay" srcset="" width="90"></a>
            </div>
        </header>

        <!-- Main Content -->
        <main class="hero-section py-5">
            <div class="container text-center">
                <h2 class="mb-4">NevoPay</h2>
                <p class="lead mb-4">
                    Quản lý ví ảo, chuyển tiền, xem giao dịch
                </p>
                <div class="d-flex flex-column flex-sm-row justify-content-center gap-3">
                    <a href="/register" class="btn btn-primary btn-lg">Đăng ký</a>
                    <a href="/login" class="btn btn-outline-primary btn-lg">Đăng nhập</a>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="py-3">
            <div class="container text-center">
                <p class="mb-0">
                    © {{ now()->format('Y')}} - DEV NTD
                </p>
            </div>
        </footer>

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    </body>
</html>