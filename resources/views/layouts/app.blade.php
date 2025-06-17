<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"> {{-- Используем локаль Laravel --}}

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta content="Ваш Автор" name="author" /> {{-- Измените --}}
    <title>@yield('title', config('app.name', 'Laravel'))</title> {{-- Динамический заголовок --}}

    {{-- Стили библиотек --}}
    <link href="{{ asset('assets/libs/slick-carousel/slick/slick.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/libs/slick-carousel/slick/slick-theme.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/libs/tiny-slider/dist/tiny-slider.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/libs/bootstrap-icons/font/bootstrap-icons.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/libs/feather-webfont/dist/feather-icons.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/libs/simplebar/dist/simplebar.min.css') }}" rel="stylesheet" />

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/images/favicon/favicon.ico') }}" />

    <!-- Theme CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/theme.min.css') }}" />

    {{-- Скрипты аналитики (можно вынести в отдельный компонент или оставить здесь) --}}
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-M8S4MT3EYG"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag() { dataLayer.push(arguments); }
        gtag("js", new Date());
        gtag("config", "G-M8S4MT3EYG");
    </script>
    <script type="text/javascript">
        (function (c, l, a, r, i, t, y) {
            c[a] = c[a] || function () { (c[a].q = c[a].q || []).push(arguments); };
            t = l.createElement(r); t.async = 1; t.src = "https://www.clarity.ms/tag/" + i;
            y = l.getElementsByTagName(r)[0]; y.parentNode.insertBefore(t, y);
        })(window, document, "clarity", "script", "kuc8w5o9nt"); // Замените 'kuc8w5o9nt' на ваш ID
    </script>

    @stack('head-scripts') {{-- Стек для дополнительных скриптов в head --}}
    @stack('styles') {{-- Стек для дополнительных стилей --}}
</head>

<body>
@include('partials.navbar') {{-- Включаем шапку --}}

<main>
    @yield('content') {{-- Основной контент страницы --}}
</main>

@include('partials.footer') {{-- Включаем подвал --}}

{{-- Модальные окна можно вынести в отдельные файлы или оставить здесь, если они общие --}}
@include('partials.modals.user_modal')
@include('partials.modals.location_modal')
@include('partials.modals.quick_view_modal')

{{-- Offcanvas для корзины --}}
@include('partials.shop_cart_offcanvas')


<!-- Libs JS -->
{{-- <script src="{{ asset('assets/js/vendors/jquery.min.js') }}"></script> --}} {{-- jQuery часто не нужен с современным Bootstrap --}}
<script src="{{ asset('assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/libs/simplebar/dist/simplebar.min.js') }}"></script>

<!-- Theme JS -->
<script src="{{ asset('assets/js/theme.min.js') }}"></script>

{{-- Скрипты для слайдеров и других вендоров --}}
<script src="{{ asset('assets/js/vendors/jquery.min.js') }}"></script> {{-- Если слайдеры требуют jQuery --}}
<script src="{{ asset('assets/js/vendors/countdown.js') }}"></script>
<script src="{{ asset('assets/libs/slick-carousel/slick/slick.min.js') }}"></script>
<script src="{{ asset('assets/js/vendors/slick-slider.js') }}"></script>
<script src="{{ asset('assets/libs/tiny-slider/dist/min/tiny-slider.js') }}"></script>
<script src="{{ asset('assets/js/vendors/tns-slider.js') }}"></script>
<script src="{{ asset('assets/js/vendors/zoom.js') }}"></script>
<script src="{{ asset('assets/js/vendors/validation.js') }}"></script> {{-- Если это ваш кастомный скрипт валидации --}}

@stack('scripts') {{-- Стек для дополнительных скриптов в конце body --}}
</body>
</html>
