@extends('layouts.app')

@section('title', 'Mr. Djemius Zero - Главная')

@section('content')
    <section class="mt-8">
        <div class="container">
            <div class="hero-slider">
                <div style="background: url({{ asset('assets/images/slider/slide-1.jpg') }}) no-repeat; background-size: cover; border-radius: 0.5rem; background-position: center">
                    <div class="ps-lg-12 py-lg-16 col-xxl-5 col-md-7 py-14 px-8 text-xs-center">
                        <span class="badge text-bg-warning">Распродажа! Скидка 50%</span>
                        <h2 class="text-dark display-5 fw-bold mt-4">Mr.Jemius</h2>
                        <p class="lead">Представляем новую модель онлайн-покупок продуктов с удобной доставкой на дом.</p>
                        <a href="#" class="btn btn-dark mt-3">
                            В магазин
                            <i class="feather-icon icon-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
                <div style="background: url({{ asset('assets/images/slider/slider-2.jpg') }}) no-repeat; background-size: cover; border-radius: 0.5rem; background-position: center">
                    <div class="ps-lg-12 py-lg-16 col-xxl-5 col-md-7 py-14 px-8 text-xs-center">
                        <span class="badge text-bg-warning">Распродажа! Скидка 50%</span>
                        <h2 class="text-dark display-5 fw-bold mt-4">Супермаркет Свежих Продуктов</h2>
                        <p class="lead">Представляем новую модель онлайн-покупок продуктов с удобной доставкой на дом.</p>
                        <a href="#" class="btn btn-dark mt-3">
                            В магазин
                            <i class="feather-icon icon-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

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
                                <h3 class="h5 mb-0">Гарантия качества</h3>
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
    <!-- Конец Секции Категорий -->

    <!-- Секция баннеров -->
    <section>

    </section>

    <!-- Секция Популярных Продуктов -->
    @if(isset($popularProducts) && $popularProducts->isNotEmpty())
        <section class="my-lg-14 my-8">
            <div class="container">
                <div class="row">
                    <div class="col-12 mb-6">
                        <h3 class="mb-0">Популярные Продукты</h3>
                    </div>
                </div>
                <div class="row g-4 row-cols-lg-5 row-cols-2 row-cols-md-3">
                    @foreach($popularProducts as $product)
                        <div class="col">
                            @include('components.product-card', ['product' => $product])
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
    <!-- Конец Секции Популярных Продуктов -->

    @if(isset($bestsellerProducts) && $bestsellerProducts->isNotEmpty())
        <section class="my-lg-14 my-8">
            <div class="container">
                <div class="row">
                    <div class="col-md-12 mb-6">
                        <h3 class="mb-0">Лучшие Продажи Дня</h3>
                    </div>
                </div>
                <div class="table-responsive-lg pb-6">
                    {{-- flex-nowrap заставляет элементы выстраиваться в одну линию, что идеально для слайдера --}}
                    {{-- Если у вас не слайдер, а сетка, уберите 'flex-nowrap' --}}
                    <div class="row row-cols-lg-4 row-cols-1 row-cols-md-2 g-4 flex-nowrap">
                        <div class="col">
                            {{-- Это статический баннер. Вы можете его оставить или сделать динамическим. --}}
                            <div class="pt-8 px-6 px-xl-8 rounded" style="background: url({{ asset('assets/images/banner/banner-deal.jpg') }}) no-repeat; background-size: cover; height: 470px">
                                <div>
                                    <h3 class="fw-bold text-white">100% Органические Кофейные Зерна.</h3>
                                    <p class="text-white">Получите лучшее предложение.</p>
                                    <a href="#!" class="btn btn-primary">
                                        В магазин
                                        <i class="feather-icon icon-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                        {{-- Здесь мы выводим продукты-бестселлеры --}}
                        @foreach($bestsellerProducts as $product)
                            <div class="col">
                                @include('components.product-card', ['product' => $product])
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    @endif
    <section class="my-lg-14 my-8">
        <div class="container">
            <div class="row">
                <div class="col-12 col-md-6 mb-3 mb-lg-0">
                    <div>
                        <div class="py-10 px-8 rounded" style="background: url(assets/images/banner/grocery-banner.png) no-repeat; background-size: cover; background-position: center">
                            <div>
                                <h3 class="fw-bold mb-1">Fruits &amp; Vegetables</h3>
                                <p class="mb-4">
                                    Get Upto
                                    <span class="fw-bold">30%</span>
                                    Off
                                </p>
                                <a href="#!" class="btn btn-dark">Shop Now</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div>
                        <div class="py-10 px-8 rounded" style="background: url(assets/images/banner/grocery-banner-2.jpg) no-repeat; background-size: cover; background-position: center">
                            <div>
                                <h3 class="fw-bold mb-1">Freshly Baked Buns</h3>
                                <p class="mb-4">
                                    Get Upto
                                    <span class="fw-bold">25%</span>
                                    Off
                                </p>
                                <a href="#!" class="btn btn-dark">Shop Now</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection
