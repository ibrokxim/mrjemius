@extends('layouts.app')

{{-- Устанавливаем заголовок страницы, используя SEO-данные или название продукта --}}
@section('title', $product->seo->meta_title ?? $product->name)

@push('head-scripts')
    {{-- Добавляем Schema.org разметку для продукта --}}
{{--    @include('partials.seo.product-schema', ['product' => $product])--}}
@endpush

@section('content')
    <main>
        <div class="mt-4">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <!-- breadcrumb -->
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="{{ route('welcome') }}">Главная</a></li>
                                @if($product->category)
                                    <li class="breadcrumb-item"><a href="{{ route('category.show', $product->category->slug) }}">{{ $product->category->name }}</a></li>
                                @endif
                                <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
        <section class="mt-8">
            <div class="container">
                <div class="row">
                    <div class="col-md-5 col-xl-6">
                        {{-- Слайдер изображений продукта --}}
                        @if($product->images->isNotEmpty())
                            <div class="product" id="product">
                                @foreach($product->images as $image)
                                    <div class="zoom" onmousemove="zoom(event)" style="background-image: url({{ asset('storage/' . $image->image_url) }})">
                                        <img src="{{ asset('storage/' . $image->image_url) }}" alt="{{ $image->alt_text ?? $product->name }}" />
                                    </div>
                                @endforeach
                            </div>
                            <div class="product-tools">
                                <div class="thumbnails row g-3" id="productThumbnails">
                                    @foreach($product->images as $image)
                                        <div class="col-3">
                                            <div class="thumbnails-img">
                                                <img src="{{ asset('storage/' . $image->image_url) }}" alt="{{ $image->alt_text ?? $product->name }}" />
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @elseif($product->primaryImage)
                            <img src="{{ asset('storage/' . $product->primaryImage->image_url) }}" alt="{{ $product->name }}" class="img-fluid rounded" />
                        @else
                            <img src="{{ asset('assets/images/placeholder.png') }}" alt="Нет изображения" class="img-fluid rounded" />
                        @endif
                    </div>
                    <div class="col-md-7 col-xl-6">
                        <div class="ps-lg-10 mt-6 mt-md-0">
                            @if($product->category)
                                <a href="{{ route('category.show', $product->category->slug) }}" class="mb-4 d-block">{{ $product->category->name }}</a>
                            @endif
                            <h1 class="mb-1">{{ $product->name }}</h1>
                            <div class="mb-4">
                                {{-- Рейтинг --}}
                                @if($product->reviews_avg_rating)
                                    <small class="text-warning">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <i class="bi bi-star{{ $i <= round($product->reviews_avg_rating) ? '-fill' : '' }}"></i>
                                        @endfor
                                    </small>
                                    <a href="#reviews-tab" class="ms-2">({{ $product->reviews_count }} отзыва(ов))</a>
                                @else
                                    <small class="text-muted">Нет отзывов</small>
                                @endif

                            </div>
                            <div class="fs-4">
                                {{-- Цена --}}
                                <span class="fw-bold text-dark">{{ number_format($product->sale_price ?? $product->price, 2, '.', ' ') }} ₽</span>
                                @if($product->sale_price && $product->sale_price < $product->price)
                                    <span class="text-decoration-line-through text-muted">{{ number_format($product->price, 2, '.', ' ') }} ₽</span>
                                @endif
                            </div>
                            <hr class="my-6" />
                            <div class="mb-5">

                                {{-- Здесь будет логика для вариантов товара, если они есть --}}
                                {{-- <button type="button" class="btn btn-outline-secondary">250г</button> --}}
                            </div>
                            <div>
                                <div class="input-group input-spinner">
                                    <input type="button" value="-" class="button-minus btn btn-sm" data-field="quantity" />
                                    <input type="number" step="1" max="{{ $product->stock_quantity }}" value="1" name="quantity" class="quantity-field form-control-sm form-input" />
                                    <input type="button" value="+" class="button-plus btn btn-sm" data-field="quantity" />
                                </div>
                            </div>
                            <div class="mt-3 row justify-content-start g-2 align-items-center">
                                <div class="col-xxl-4 col-lg-4 col-md-5 col-5 d-grid">
                                    <button type="button" class="btn btn-primary add-to-cart-btn" data-product-id="{{ $product->id }}">
                                        <i class="feather-icon icon-shopping-bag me-2"></i>В корзину
                                    </button>
                                </div>
                                {{-- ... кнопки сравнения, избранного ... --}}
                            </div>
                            <hr class="my-6" />
                            <div>
                                <table class="table table-borderless mb-0">
                                    <tbody>
                                    <tr>
                                        <td>Код товара:</td>
                                        <td>{{ $product->sku ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <td>Наличие:</td>
                                        <td>{!! $product->stock_quantity > 0 ? '<span class="text-success">В наличии</span>' : '<span class="text-danger">Нет в наличии</span>' !!}</td>
                                    </tr>
                                    @if($product->category)
                                        <tr>
                                            <td>Тип:</td>
                                            <td>{{ $product->category->name }}</td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td>Доставка:</td>
                                        <td><small>1-2 дня. <span class="text-muted">(Бесплатный самовывоз)</span></small></td>
                                    </tr>
                                    </tbody>
                                </table>
                                <div class="mt-4">
                                    <div class="dropdown">
                                        <a class="btn btn-outline-secondary dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="feather-icon icon-share-2 me-2"></i> Поделиться
                                        </a>

                                        <ul class="dropdown-menu">
                                            <li>
                                                <a class="dropdown-item" href="#!" id="web-share-button">
                                                    <i class="bi bi-phone me-2"></i>Через меню телефона
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <a class="dropdown-item" id="telegram-share-link" href="#" target="_blank">
                                                    <i class="bi bi-telegram me-2"></i>Telegram
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" id="whatsapp-share-link" href="#" target="_blank">
                                                    <i class="bi bi-whatsapp me-2"></i>WhatsApp
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" id="vk-share-link" href="#" target="_blank">
                                                    <i class="bi bi-vk me-2"></i>VKontakte
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Секция с вкладками: Описание, Информация, Отзывы --}}
        <section class="mt-lg-14 mt-8">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        {{-- СПИСОК ВКЛАДОК --}}
                        <ul class="nav nav-pills nav-lb-tab" id="myTab" role="tablist">
                            {{-- Вкладка "Состав" (теперь первая и активная по умолчанию) --}}
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="composition-tab" data-bs-toggle="tab" data-bs-target="#composition-tab-pane" type="button" role="tab" aria-controls="composition-tab-pane" aria-selected="true">
                                    Состав
                                </button>
                            </li>
                            {{-- Вкладка "Детали продукта" --}}
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="product-details-tab" data-bs-toggle="tab" data-bs-target="#product-details-tab-pane" type="button" role="tab" aria-controls="product-details-tab-pane" aria-selected="false">
                                    Описание
                                </button>
                            </li>
                            {{-- Вкладка "Доставка" --}}
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="delivery-tab" data-bs-toggle="tab" data-bs-target="#delivery-tab-pane" type="button" role="tab" aria-controls="delivery-tab-pane" aria-selected="false">
                                    Доставка
                                </button>
                            </li>
                            {{-- Вкладка "Оплата" --}}
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="payment-tab" data-bs-toggle="tab" data-bs-target="#payment-tab-pane" type="button" role="tab" aria-controls="payment-tab-pane" aria-selected="false">
                                    Оплата
                                </button>
                            </li>
                            {{-- Вкладка "Отзывы" --}}
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews-tab-pane" type="button" role="tab" aria-controls="reviews-tab-pane" aria-selected="false">
                                    Отзывы({{$product->reviews()->count()}})
                                </button>
                            </li>
                        </ul>

                        {{-- КОНТЕНТ ВКЛАДОК --}}
                        <div class="tab-content" id="myTabContent">

                            {{-- Контент для "Состав" (теперь активен по умолчанию) --}}
                            <div class="tab-pane fade show active" id="composition-tab-pane" role="tabpanel" aria-labelledby="composition-tab" tabindex="0">
                                <div class="my-8">
                                    @if($product->short_description)
                                        {!! $product->short_description !!}
                                    @else
                                        <p class="text-muted">Информация о составе продукта не указана.</p>
                                    @endif
                                </div>
                            </div>

                            {{-- Контент для "Описание" --}}
                            <div class="tab-pane fade" id="product-details-tab-pane" role="tabpanel" aria-labelledby="product-details-tab" tabindex="0">
                                <div class="my-8">
                                    @if($product->description)
                                        {!! $product->description !!}
                                    @else
                                        <p class="text-muted">Описание для этого продукта пока не добавлено.</p>
                                    @endif
                                </div>
                            </div>

                            {{-- Контент для "Доставка" --}}
                            <div class="tab-pane fade" id="delivery-tab-pane" role="tabpanel" aria-labelledby="delivery-tab" tabindex="0">
                                <div class="my-8">
                                    @if($product->delivery_info)
                                        {!! $product->delivery_info !!}
                                    @else
                                        {{-- Можно вывести какой-то текст по умолчанию --}}
                                        <h4>Стандартные условия доставки</h4>
                                        <p>Мы доставляем наши товары по всей стране. Стоимость и сроки доставки будут рассчитаны при оформлении заказа. Для получения более подробной информации, пожалуйста, свяжитесь с нашей службой поддержки.</p>
                                    @endif
                                </div>
                            </div>

                            {{-- Контент для "Оплата" --}}
                            <div class="tab-pane fade" id="payment-tab-pane" role="tabpanel" aria-labelledby="payment-tab" tabindex="0">
                                <div class="my-8">
                                    @if($product->payment_info)
                                        {!! $product->payment_info !!}
                                    @else
                                        {{-- Можно вывести какой-то текст по умолчанию --}}
                                        <h4>Способы оплаты</h4>
                                        <p>Вы можете оплатить ваш заказ онлайн с помощью банковской карты или через другие доступные платежные системы. Все платежи безопасны и защищены.</p>
                                    @endif
                                </div>
                            </div>

                            {{-- Контент для "Отзывы" --}}
                            <div class="tab-pane fade" id="reviews-tab-pane" role="tabpanel" aria-labelledby="reviews-tab" tabindex="0">
                                @include('partials.product-reviews', ['reviews' => $product->reviews])
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Секция похожих товаров -->
        @if(isset($relatedProducts) && $relatedProducts->isNotEmpty())
            <section class="my-lg-14 my-14">
                <div class="container">
                    <div class="row">
                        <div class="col-12">
                            <h3>Похожие товары</h3>
                        </div>
                    </div>
                    <div class="row g-4 row-cols-lg-5 row-cols-2 row-cols-md-2 mt-2">
                        @foreach($relatedProducts as $relatedProduct)
                            <div class="col">
                                @include('components.product-card', ['product' => $relatedProduct])
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif
    </main>
@endsection

@push('scripts')
    {{-- Скрипты для слайдера изображений, зума и т.д. --}}
    <script src="{{ asset('assets/libs/tiny-slider/dist/min/tiny-slider.js') }}"></script>
    <script src="{{ asset('assets/js/vendors/tns-slider.js') }}"></script>
    <script src="{{ asset('assets/js/vendors/zoom.js') }}"></script>
@endpush
@push('scripts')
    {{-- ... ваши другие скрипты для этой страницы (слайдеры и т.д.) ... --}}

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- Данные для "Поделиться" ---
            const shareTitle = "{{ e($product->name) }}"; // Название продукта
            const shareUrl = "{{ url()->current() }}"; // URL текущей страницы
            const shareText = "Посмотрите, какой отличный продукт я нашел: {{ e($product->name) }}";

            // --- Настройка ссылок для соцсетей ---
            const telegramLink = document.getElementById('telegram-share-link');
            if (telegramLink) {
                telegramLink.href = `https://t.me/share/url?url=${encodeURIComponent(shareUrl)}&text=${encodeURIComponent(shareText)}`;
            }

            const whatsappLink = document.getElementById('whatsapp-share-link');
            if (whatsappLink) {
                whatsappLink.href = `https://api.whatsapp.com/send?text=${encodeURIComponent(shareText + ' ' + shareUrl)}`;
            }

            const vkLink = document.getElementById('vk-share-link');
            if(vkLink) {
                vkLink.href = `http://vk.com/share.php?url=${encodeURIComponent(shareUrl)}&title=${encodeURIComponent(shareTitle)}`;
            }

            // --- Логика для Web Share API ---
            const webShareButton = document.getElementById('web-share-button');
            // Проверяем, поддерживает ли браузер Web Share API
            if (navigator.share) {
                webShareButton.style.display = 'block'; // Показываем кнопку
                webShareButton.addEventListener('click', async () => {
                    try {
                        await navigator.share({
                            title: shareTitle,
                            text: shareText,
                            url: shareUrl,
                        });
                        console.log('Контент успешно отправлен');
                    } catch (err) {
                        console.error('Ошибка при отправке:', err);
                    }
                });
            } else {
                // Если API не поддерживается, скрываем кнопку
                webShareButton.style.display = 'none';
                // Также можно скрыть разделитель
                const divider = webShareButton.nextElementSibling;
                if (divider && divider.tagName === 'HR') {
                    divider.style.display = 'none';
                }
            }
        });
    </script>
    @push('scripts')
        {{-- ... ваши другие скрипты ... --}}
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const writeReviewBtn = document.getElementById('write-review-btn');
                const reviewFormContainer = document.getElementById('review-form-container');

                if (writeReviewBtn && reviewFormContainer) {
                    writeReviewBtn.addEventListener('click', function() {
                        // Плавно показываем/скрываем форму
                        if (reviewFormContainer.style.display === 'none') {
                            reviewFormContainer.style.display = 'block';
                            this.innerText = 'Скрыть форму';
                        } else {
                            reviewFormContainer.style.display = 'none';
                            this.innerText = 'Написать отзыв';
                        }
                    });
                }

                // Если были ошибки валидации при отправке формы,
                // то после перезагрузки страницы форма должна быть сразу видна.
                @if($errors->any())
                if (reviewFormContainer) {
                    reviewFormContainer.style.display = 'block';
                    if (writeReviewBtn) {
                        writeReviewBtn.innerText = 'Скрыть форму';
                    }
                }
                @endif

            });
        </script>
    @endpush
@endpush
