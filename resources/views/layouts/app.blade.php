<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <!-- Google Tag Manager -->
    <script>
        (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
            j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
            'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-TKQ5TLZW');
    </script>
    <!-- End Google Tag Manager -->

    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta content="Codescandy" name="author" />
    <title>@yield('title', 'Mr. Djemius Zero')</title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css" />
    <link href="{{ asset('assets/libs/slick-carousel/slick/slick.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/libs/slick-carousel/slick/slick-theme.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/libs/tiny-slider/dist/tiny-slider.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/libs/bootstrap-icons/font/bootstrap-icons.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/libs/feather-webfont/dist/feather-icons.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/libs/simplebar/dist/simplebar.min.css') }}" rel="stylesheet" />

    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/images/favicon/favicon.ico') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/theme.min.css') }}" />
    <script src="//code.jivo.ru/widget/XAsl12rHFk" async></script>

    @stack('head-scripts')
    @stack('styles')
</head>

<body>
<!-- Google Tag Manager (noscript) -->
<noscript>
    <iframe src="https://www.googletagmanager.com/ns.html?id=GTM-TKQ5TLZW"
            height="0" width="0" style="display:none;visibility:hidden"></iframe>
</noscript>
<!-- End Google Tag Manager (noscript) -->

@include('partials.navbar')

<main>
    @yield('content')
</main>

@include('partials.footer')
@include('partials.modals.user_modal')
@include('partials.modals.location_modal')
@include('partials.modals.quick_view_modal')
@include('partials.shop_cart_offcanvas')
@include('partials.modals.contact_modal')

<script src="https://unpkg.com/imask"></script>
<script src="{{ asset('assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/libs/simplebar/dist/simplebar.min.js') }}"></script>
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sendContactBtn = document.getElementById('sendContactFormBtn');
        const contactForm = document.getElementById('contact-form');
        const contactModalEl = document.getElementById('contactModal');

        if (sendContactBtn && contactForm && contactModalEl) {
            sendContactBtn.addEventListener('click', async function() {
                const formData = new FormData(contactForm);
                const button = this;

                button.disabled = true;
                button.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Отправка...';

                try {
                    const response = await fetch("{{ route('feedback.store') }}", {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                    });

                    const data = await response.json();

                    if (response.ok && data.success) {
                        const modal = bootstrap.Modal.getInstance(contactModalEl);
                        if (modal) modal.hide();
                        contactForm.reset();
                        alert('Спасибо! Мы скоро с вами свяжемся.');
                    } else {
                        let errorMessage = data.message || 'Произошла ошибка.';
                        if (data.errors) {
                            errorMessage = Object.values(data.errors).flat().join('\n');
                        }
                        alert(errorMessage);
                    }
                } catch (error) {
                    console.error('Ошибка отправки формы:', error);
                    alert('Не удалось отправить форму. Попробуйте позже.');
                } finally {
                    button.disabled = false;
                    button.textContent = 'Отправить';
                }
            });
        }
    });
</script>
</body>
</html>
