@extends('layouts.app')

@section('title', 'Mr. Djemius Zero - Главная')

@section('content')
    <section class="mt-8">
        <div class="container">
            <div class="hero-slider">
                @foreach($banners as $banner)
                    <a href="{{ $banner->link_url ?? '#' }}" style="display: block;">
                        <div style="
                    background: url({{ asset('storage/' . $banner->banner_image_url) }}) no-repeat;
                    background-size: cover;
                    border-radius: 0.5rem;
                    background-position: center;
                    height: {{ $banner->height ?? 400 }}px;  {{-- Используем высоту из БД или 400px по умолчанию --}}
                ">
                            {{-- Здесь можно разместить текст поверх баннера, если нужно --}}
                        </div>
                    </a>
                @endforeach
            </div>
        </div>

    <section class="my-lg-14 my-8">
        <div class="container">
            {{-- Добавляем классы для выравнивания колонок и отступов между ними --}}
            <div class="row g-4 justify-content-center">

                {{-- Блок 1: Быстрая доставка --}}
                <div class="col-lg-4 col-md-6">
                    {{-- Добавляем классы для фона, отступов и скругления --}}
                    <div class="card bg-light border-0 h-100 p-4 rounded-3">
                        <div class="card-body">
                            {{-- Используем Flexbox для выравнивания иконки и заголовка --}}
                            <div class="d-flex align-items-center mb-3">
                                <div class="me-3">
                                    <img src="{{ asset('assets/images/icons/clock.svg') }}" alt="Быстрая доставка" />
                                </div>
                                <h3 class="h5 mb-0">Быстрая доставка</h3>
                            </div>
                            <p>Получите ваш заказ с доставкой на дом в кратчайшие сроки.</p>
                        </div>
                    </div>
                </div>

                {{-- Блок 2: Широкий ассортимент --}}
                <div class="col-lg-4 col-md-6">
                    <div class="card bg-light border-0 h-100 p-4 rounded-3">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="me-3">
                                    <img src="{{ asset('assets/images/icons/package.svg') }}" alt="Широкий ассортимент" />
                                </div>
                                <h3 class="h5 mb-0">Широкий ассортимент</h3>
                            </div>
                            <p>Выбирайте из 100+ товаров в различных категориях.</p>
                        </div>
                    </div>
                </div>

                {{-- Блок 3: Гарантия качества --}}
                <div class="col-lg-4 col-md-6">
                    <div class="card bg-light border-0 h-100 p-4 rounded-3">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="me-3">
                                    <img src="{{ asset('assets/images/icons/refresh-cw.svg') }}" alt="Гарантия качества" />
                                </div>
                                <h3 class="h5 mb-0">Гарантия качествa</h3>
                            </div>
                            <p>Не довольны продуктом? Верните его при доставке и получите возврат средств.</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
    <!-- Секция Категорий -->
    @if(isset($categories) && $categories->isNotEmpty())
        <section class="mb-lg-10 mt-lg-14 my-8">
            <div class="container">
                <div class="row">
                    <div class="col-12 mb-6">
                        <h3 class="mb-0">Популярные Категории</h3>
                    </div>
                </div>
                <div class="category-slider">
                    @foreach($categories as $category)
                        <div class="item">
                            <a href=" {{ route('category.show', $category->slug) }} " class="text-decoration-none text-inherit">
                                <div class="card card-product mb-lg-4">
                                    <div class="card-body text-center py-8">

                                        <img src="{{ Storage::disk('public')->url($category->image_url) }}"
                                             alt="{{ $category->name }}"
                                             class="mb-3 img-fluid" style="height: 60px; object-fit: contain;" />
                                        <div class="text-truncate">{{ $category->name }}</div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
    <section>

    </section>

    <!-- Секция Популярных Продуктов -->
        @push('styles')
            <style>
                /* Фиксированная высота для карточек продуктов в слайдере */
                .bestseller-slider .card {
                    height: 100%;
                    display: flex;
                    flex-direction: column;
                }

                .bestseller-slider .card-body {
                    flex: 1;
                    display: flex;
                    flex-direction: column;
                    justify-content: space-between;
                }

                /* Ограничиваем название товара */
                .bestseller-slider .card-title,
                .bestseller-slider h5 {
                    display: -webkit-box;
                    -webkit-line-clamp: 2; /* Максимум 2 строки */
                    -webkit-box-orient: vertical;
                    overflow: hidden;
                    line-height: 1.3;
                    height: 2.6em; /* Фиксированная высота для 2 строк */
                    margin-bottom: 1rem;
                }

                /* Выравниваем элементы внутри карточки */
                .bestseller-slider .card-body > *:last-child {
                    margin-top: auto;
                }

                /* Для мобильных устройств */
                @media (max-width: 576px) {
                    .bestseller-slider .slick-track {
                        display: flex;
                        align-items: stretch;
                    }

                    .bestseller-slider .slick-slide {
                        height: auto;
                        display: flex;
                    }

                    .bestseller-slider .slick-slide > div {
                        height: 100%;
                        width: 100%;
                    }
                }
            </style>
        @endpush
        @if(isset($popularProducts) && $popularProducts->isNotEmpty())
            <section class="my-lg-14 my-8" id="products-section">
                <div class="container">
                    <div class="row">
                        <div class="col-12 mb-6">
                            <h3 class="mb-0">Продукты</h3>
                        </div>
                    </div>

                    {{-- Универсальный слайдер для всех устройств --}}
                    <div class="bestseller-slider">
                        @foreach($popularProducts as $product)
                            <div class="item">
                                @include('components.product-card', ['product' => $product])
                            </div>
                        @endforeach
                    </div>

                    <div class="row mt-8">
                        <div class="col-12 text-center">
                            <a href="{{ route('category.show', 'dzemy') }}" class="btn btn-primary">Посмотреть все</a>
                        </div>
                    </div>
                </div>
            </section>
            @push('scripts')
                <script>
                    $(document).ready(function () {
                        $('.bestseller-slider').slick({
                            slidesToShow: 5,
                            slidesToScroll: 1,
                            infinite: true,
                            arrows: true,
                            dots: false,
                            centerMode: false,
                            initialSlide: 0,
                            prevArrow: '<button type="button" class="slick-prev"><i class="feather-icon icon-chevron-left"></i></button>',
                            nextArrow: '<button type="button" class="slick-next"><i class="feather-icon icon-chevron-right"></i></button>',
                            responsive: [
                                {
                                    breakpoint: 1200,
                                    settings: {
                                        slidesToShow: 4,
                                        slidesToScroll: 1
                                    }
                                },
                                {
                                    breakpoint: 992,
                                    settings: {
                                        slidesToShow: 3,
                                        slidesToScroll: 1
                                    }
                                },
                                {
                                    breakpoint: 768,
                                    settings: {
                                        slidesToShow: 2,
                                        slidesToScroll: 1
                                    }
                                },
                                {
                                    breakpoint: 576,
                                    settings: {
                                        slidesToShow: 2,
                                        slidesToScroll: 1,
                                        centerMode: false,
                                        arrows: true,
                                    }
                                }
                            ]
                        });
                    });
                </script>
            @endpush
        @endif


    <!-- Конец Секции Популярных Продуктов -->
{{--        @if(isset($bestsellerProducts) && $bestsellerProducts->isNotEmpty())--}}
{{--            <section class="my-lg-14 my-8">--}}
{{--                <div class="container">--}}
{{--                    <div class="row">--}}
{{--                        <div class="col-md-12 mb-6">--}}
{{--                            <h3 class="mb-0">Лучшие Продажи Дня</h3>--}}
{{--                        </div>--}}
{{--                    </div>--}}

{{--                    <div class="bestseller-slider ">--}}
{{--                        @foreach($bestsellerProducts as $product)--}}
{{--                            <div class="item">--}}
{{--                                @include('components.product-card', ['product' => $product])--}}
{{--                            </div>--}}
{{--                        @endforeach--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </section>--}}

{{--            @push('scripts')--}}
{{--                <script>--}}
{{--                    $(document).ready(function () {--}}
{{--                        $('.bestseller-slider').slick({--}}
{{--                            slidesToShow: 5,--}}
{{--                            slidesToScroll: 1,--}}
{{--                            infinite: true,--}}
{{--                            arrows: true,--}}
{{--                            dots: false,--}}
{{--                            centerMode: false,--}}
{{--                            initialSlide: 0,--}}
{{--                            prevArrow: '<button type="button" class="slick-prev"><i class="feather-icon icon-chevron-left"></i></button>',--}}
{{--                            nextArrow: '<button type="button" class="slick-next"><i class="feather-icon icon-chevron-right"></i></button>',--}}
{{--                            responsive: [--}}
{{--                                {--}}
{{--                                    breakpoint: 1200,--}}
{{--                                    settings: { slidesToShow: 4 }--}}
{{--                                },--}}
{{--                                {--}}
{{--                                    breakpoint: 992,--}}
{{--                                    settings: { slidesToShow: 3 }--}}
{{--                                },--}}
{{--                                {--}}
{{--                                    breakpoint: 768,--}}
{{--                                    settings: { slidesToShow: 2 }--}}
{{--                                },--}}
{{--                                {--}}
{{--                                    breakpoint: 576,--}}
{{--                                    settings: { slidesToShow: 1 }--}}
{{--                                }--}}
{{--                            ]--}}
{{--                        });--}}
{{--                    });--}}
{{--                </script>--}}
{{--            @endpush--}}
{{--        @endif--}}


        <section class="my-lg-14 my-8">
        <div class="container">
            <div class="row">
                <div class="col-12 col-md-6 mb-3 mb-lg-0">
                    <div>
                        <div class="py-10 px-8 rounded" style="background: url(assets/images/banner/grechka.png)
                        no-repeat;
                        background-size: cover; background-position: center">
                            <div class="text-white" >
                                <h3 class="fw-bold mb-1 text-white">Grechka - правильное питание</h3>
                                <p class="mb-4">
                                    Скидки до
                                    <span class="fw-bold">30%</span>

                                </p>
                                <a href="https://www.grechkafood.uz/" class="btn btn-dark">Перейти </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div>
                        <div class="py-10 px-8 rounded" style="background: url(assets/images/banner/grechka2.png)
                        no-repeat;
                        background-size: cover; background-position: center">
                            <div class="text-white" >
                                <h3 class="fw-bold mb-1 text-white">Grechka - правильное питание</h3>
                                <p class="mb-4">
                                    Скидки до
                                    <span class="fw-bold">30%</span>

                                </p>
                                <a href="https://www.grechkafood.uz/" class="btn btn-dark">Перейти </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
        @push('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    // Получаем CSRF токен один раз
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

                    // =================================================================
                    // ДЕЛЕГИРОВАНИЕ СОБЫТИЙ ДЛЯ ДИНАМИЧЕСКОГО КОНТЕНТА
                    // =================================================================
                    // Вешаем один обработчик на весь `main` тег или на body
                    document.body.addEventListener('click', async function(e) {
                        const target = e.target;

                        // --- Логика для кнопки "В ИЗБРАННОЕ" ---
                        const wishlistBtn = target.closest('.wishlist-toggle-btn');
                        if (wishlistBtn) {
                            e.preventDefault();
                            e.stopPropagation();
                            toggleWishlist(wishlistBtn);
                            return;
                        }

                        // --- Логика для кнопки "В КОРЗИНУ" ---
                        const cartBtn = target.closest('.add-to-cart-btn');
                        if (cartBtn) {
                            e.preventDefault();
                            e.stopPropagation();
                            addToCart(cartBtn);
                            return;
                        }

                        // --- Логика для клика по КАРТОЧКЕ ТОВАРА ---
                        const clickableCard = target.closest('.clickable-card');
                        if (clickableCard) {
                            const url = clickableCard.dataset.url;
                            if (url) {
                                // Не предотвращаем стандартное действие, чтобы средняя кнопка мыши работала
                                // Но если это вызовет проблемы, можно раскомментировать preventDefault и использовать window.location
                                // e.preventDefault();
                                // window.location.href = url;
                            }
                        }
                    });

                    // =================================================================
                    // ФУНКЦИИ-ОБРАБОТЧИКИ
                    // =================================================================

                    async function toggleWishlist(button) {
                        const productId = button.dataset.productId;
                        const icon = button.querySelector('i');
                        if (!productId || !icon || !csrfToken) return;

                        button.disabled = true;
                        try {
                            const urlTemplate = "{{ route('wishlist.toggle', ['product' => ':id']) }}";
                            const finalUrl = urlTemplate.replace(':id', productId);
                            const response = await fetch(finalUrl, {
                                method: 'POST',
                                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
                            });
                            const data = await response.json();
                            if (response.ok && data.success) {
                                if (data.status === 'added') {
                                    button.classList.add('active', 'text-danger');
                                    icon.className = 'bi bi-heart-fill';
                                } else {
                                    button.classList.remove('active', 'text-danger');
                                    icon.className = 'bi bi-heart';
                                }
                            }
                        } catch (error) {
                            console.error('Wishlist toggle error:', error);
                        } finally {
                            button.disabled = false;
                        }
                    }

                    async function addToCart(button) {
                        const productId = button.dataset.productId;
                        const btnText = button.querySelector('.btn-text');
                        const spinner = button.querySelector('.spinner-border');
                        if (!productId || !csrfToken) return;

                        button.disabled = true;
                        if (btnText) btnText.style.display = 'none';
                        if (spinner) spinner.classList.remove('d-none');

                        try {
                            const urlTemplate = "{{ route('cart.add', ['product' => ':id']) }}";
                            const finalUrl = urlTemplate.replace(':id', productId);
                            const response = await fetch(finalUrl, {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                                body: JSON.stringify({ quantity: 1 })
                            });
                            const data = await response.json();
                            if (response.ok && data.success) {
                                button.classList.remove('btn-primary');
                                button.classList.add('btn-success');
                                if (btnText) btnText.textContent = 'В корзине';
                                updateCartCount(data.cart_count);
                            } else {
                                button.disabled = false;
                            }
                        } catch (error) {
                            console.error('Add to cart error:', error);
                            button.disabled = false;
                        } finally {
                            if (spinner) spinner.classList.add('d-none');
                            if (btnText) btnText.style.display = 'inline-block';
                        }
                    }

                    function updateCartCount(count) {
                        const cartCountElement = document.getElementById('cart-count');
                        if (cartCountElement && count !== undefined) {
                            cartCountElement.textContent = count;
                            cartCountElement.style.display = count > 0 ? 'inline-block' : 'none';
                        }
                    }
                });
            </script>
    @endpush


@endsection
