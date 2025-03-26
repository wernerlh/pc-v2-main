<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <title>casino - @yield('title')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('assets/images/favicon.png') }}">

    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/animate.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/icofont.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/swiper.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/lightcase.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/odometer.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.min.css') }}">

    <style>
        .banner-carousel .carousel-item img {
            height: 500px;
            /* Ajusta la altura según necesites */
            object-fit: cover;
            /* Para que las imágenes cubran todo el espacio */
        }

        .banner-carousel .carousel-indicators {
            bottom: 20px;
        }

        .banner-carousel .carousel-indicators button {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.7);
            margin: 0 5px;
        }

        .banner-carousel .carousel-indicators button.active {
            background-color: #ffffff;
        }

        .carousel-control-prev-icon,
        .carousel-control-next-icon {
            background-color: rgba(0, 0, 0, 0.3);
            border-radius: 50%;
            padding: 10px;
        }

        .benefits-list,
        .benefits-list ul,
        .benefits-list ol,
        .benefits-list li,
        .benefits-list p {
            color: black !important;
        }
    </style>

    @stack('styles')
</head>

<body>
    <!-- preloader -->
    <div class="preloader">
        <div class="preloader-inner">
            <div class="preloader-icon">
                <span></span>
                <span></span>
            </div>
        </div>
    </div>

    @include('layouts.partials.header')

    <main>
        @yield('content')
    </main>

    @include('layouts.partials.footer')

    @include('layouts.partials.scripts')

    @stack('scripts')
</body>

</html>
