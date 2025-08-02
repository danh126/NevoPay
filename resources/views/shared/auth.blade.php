<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title','Tài khoản | NevoPay')</title>
    
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
    <div class="container-form">
        <!-- Nội dung chính -->
        <div class="register-card">
            <div class="card-header">
                <!-- Logo NevoPay -->
                <div class="logo">
                    <a href="/"><img src="{{asset('images/logo/nevopay-v2.png')}}" alt="NevoPay" srcset="" width="110px"></a>
                </div>
            </div>
            <div class="card-body">
                @yield('content')
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <!-- JS Handle -->
    <script src="{{asset('js/auth/togglePassword.js')}}"></script>
</body>
</html>