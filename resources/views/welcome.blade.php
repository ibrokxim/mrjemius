@extends('layouts.app') {{-- Наследуем базовый layout --}}

@section('title', 'Главная страница - Наш Магазин') {{-- Устанавливаем заголовок для этой страницы --}}

@section('content')
    <section class="mt-8">
        <div class="container">
            <div class="hero-slider">
                <div style="background: url({{ asset('assets/images/slider/slide-1.jpg') }}) no-repeat; background-size: cover; border-radius: 0.5rem; background-position: center">
                    <div class="ps-lg-12 py-lg-16 col-xxl-5 col-md-7 py-14 px-8 text-xs-center">
                        <span class="badge text-bg-warning">Распродажа! Скидка 50%</span>
                        <h2 class="text-dark display-5 fw-bold mt-4">Супермаркет Свежих Продуктов</h2>
                        <p class="lead">Представляем новую модель онлайн-покупок продуктов с удобной доставкой на дом.</p>
                        <a href="{{-- {{ route('catalog') }} --}}" class="btn btn-dark mt-3">
                            В магазин <i class="feather-icon icon-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
                <div style="background: url({{ asset('assets/images/slider/slider-2.jpg') }}) no-repeat; background-size: cover; border-radius: 0.5rem; background-position: center">
                    <div class="ps-lg-12 py-lg-16 col-xxl-5 col-md-7 py-14 px-8 text-xs-center">
                        <span class="badge text-bg-warning">Бесплатная доставка - при заказе от $100</span> {{-- Переведите или используйте динамическую сумму --}}
                        <h2 class="text-dark display-5 fw-bold mt-4">
                            Бесплатная доставка
                            <br />
                            при заказе от
                            <span class="text-primary">$100</span>
                        </h2>
                        <p class="lead">Бесплатная доставка только для первых клиентов, после применения акций и скидок.</p>
                        <a href="{{-- {{ route('catalog') }} --}}" class="btn btn-dark mt-3">
                            В магазин <i class="feather-icon icon-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Секция Категорий -->
    <section class="mb-lg-10 mt-lg-14 my-8">
        <div class="container">
            <div class="row">
                <div class="col-12 mb-6">
                    <h3 class="mb-0">Популярные Категории</h3>
                </div>
            </div>
            <div class="category-slider">
                {{-- Здесь вы будете выводить категории из вашей базы данных циклом --}}
                {{-- Пример одной карточки категории (повторите в цикле @foreach) --}}
                <div class="item">
                    <a href="{{-- {{ route('category.show', $category->slug) }} --}}" class="text-decoration-none text-inherit">
                        <div class="card card-product mb-lg-4">
                            <div class="card-body text-center py-8">
                                <img src="{{ asset('assets/images/category/category-dairy-bread-eggs.jpg') }}" {{-- {{ asset($category->image_url) }} --}} alt="Молоко, Хлеб и Яйца" class="mb-3 img-fluid" />
                                <div class="text-truncate">Молоко, Хлеб и Яйца</div> {{-- {{ $category->name }} --}}
                            </div>
                        </div>
                    </a>
                </div>
                {{-- ... другие категории ... --}}
            </div>
        </div>
    </section>
    <!-- Конец Секции Категорий -->

    {{-- ... Остальной уникальный контент для главной страницы (баннеры, популярные товары и т.д.) с переведенным текстом и динамическими данными ... --}}

    <section class="my-lg-14 my-8">
        <div class="container">
            <div class="row">
                <div class="col-md-6 col-lg-3">
                    <div class="mb-8 mb-xl-0">
                        <div class="mb-6"><img src="{{ asset('assets/images/icons/clock.svg') }}" alt="" /></div>
                        <h3 class="h5 mb-3">Доставка продуктов за 10 минут</h3>
                        <p>Получите ваш заказ с доставкой на дом в кратчайшие сроки из ближайших магазинов FreshCart.</p>
                    </div>
                </div>
                {{-- ... другие блоки преимуществ ... --}}
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    {{-- Специфичные скрипты для этой страницы, если нужны --}}
    <script>
        // Инициализация слайдеров и т.д., если они не инициализируются глобально в theme.min.js
        $(document).ready(function(){
          $('.hero-slider').slick(); // Пример для slick
          $('.category-slider').slick(); // Пример для slick
        });
    </script>
@endpush
