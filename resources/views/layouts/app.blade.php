<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta content="Codescandy" name="author" />
    <title>@yield('title', 'Mr. Djemius Zero')</title>
    {{-- Внутри <head> --}}

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css" />
    <link href="{{ asset('assets/libs/slick-carousel/slick/slick.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/libs/slick-carousel/slick/slick-theme.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/libs/tiny-slider/dist/tiny-slider.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/libs/bootstrap-icons/font/bootstrap-icons.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/libs/feather-webfont/dist/feather-icons.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/libs/simplebar/dist/simplebar.min.css') }}" rel="stylesheet" />

    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/images/favicon/favicon.ico') }}" />

    <!-- Theme CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/theme.min.css') }}" />
    <script src="//code.jivo.ru/widget/UgOFHdeQxC" async></script>
    {{-- Скрипты аналитики --}}
{{--    @include('partials.analytics')--}}

    @stack('head-scripts')
    @stack('styles')
</head>

<body>
{{-- Включаем шапку (Navbar) --}}
@include('partials.navbar')

<main>
    {{-- Основной контент страницы будет вставлен сюда --}}
    @yield('content')
</main>

{{-- Включаем подвал (Footer) --}}
@include('partials.footer')

{{-- Модальные окна и боковая панель корзины --}}
@include('partials.modals.user_modal')
@include('partials.modals.location_modal')
@include('partials.modals.quick_view_modal')
@include('partials.shop_cart_offcanvas')

<!-- Libs JS -->
<script src="{{ asset('assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/libs/simplebar/dist/simplebar.min.js') }}"></script>
<!-- Theme JS -->

{{-- Скрипты для слайдеров и других вендоров --}}
<script src="{{ asset('assets/js/vendors/jquery.min.js') }}"></script>
<script src="{{ asset('assets/libs/tiny-slider/dist/min/tiny-slider.js') }}"></script>
<script src="{{ asset('assets/js/vendors/tns-slider.js') }}"></script>

<script src="{{ asset('assets/js/vendors/zoom.js') }}"></script>
<script src="{{ asset('assets/js/vendors/countdown.js') }}"></script>
<script src="{{ asset('assets/libs/slick-carousel/slick/slick.min.js') }}"></script>
<script src="{{ asset('assets/js/vendors/slick-slider.js') }}"></script>
<script src="{{ asset('assets/js/vendors/validation.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js"></script>
@stack('scripts')
<script src="{{ asset('assets/js/theme.min.js') }}"></script>
</body>
</html>
