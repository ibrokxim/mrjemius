@extends('layouts.app')
@push('styles')
    <style>

        /* Стиль для GLightbox, чтобы стрелки были видны */
        .product-specs-table {
            width: 100%;
            border-collapse: collapse;
        }

        .product-specs-table td {
            vertical-align: middle;
            padding: 0.3rem 0;
        }

        .product-specs-table .spec-name {
            white-space: nowrap;
            padding-right: 0.5rem;
        }

        .product-specs-table .spec-value {
            white-space: nowrap;
            padding-left: 0.5rem;
            text-align: right;
            font-weight: 500;
        }

        .product-specs-table .spec-dots {
            width: 100%;
            position: relative;
        }

        .product-specs-table .spec-dots::before {
            content: '';
            display: block;
            border-bottom: 1px dotted #ccc;
            width: 100%;
            height: 1px;
            position: relative;
            top: 0.5em;
        }

    </style>
@endpush

@section('title', $product->seo->meta_title ?? $product->name)
@section('content')
    <main>
        <div class="mt-4">
            <div class="container">
                <div class="row">
                    <div class="col-12">
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
                        @if($product->images->isNotEmpty())
                            <div class="product" id="product">
                                @foreach($product->images as $image)
                                    <div>
                                        {{-- Оборачиваем изображение в ссылку для GLightbox --}}
                                        <a href="{{ asset('storage/' . $image->image_url) }}" class="glightbox" data-gallery="product-gallery">
                                            <img src="{{ asset('storage/' . $image->image_url) }}"
                                                 alt="{{ $image->alt_text ?? $product->name }}"
                                                 style="width: 100%; height: auto; object-fit: contain; max-height: 500px;"
                                            />
                                        </a>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Миниатюры для навигации слайдера (горизонтальные) -->
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
                            {{-- Случай с одним изображением --}}
                            <a href="{{ asset('storage/' . $product->primaryImage->image_url) }}" class="glightbox" data-gallery="product-gallery">
                                <img src="{{ asset('storage/' . $product->primaryImage->image_url) }}" alt="{{ $product->name }}" class="img-fluid rounded" />
                            </a>
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
                            <div class="mb-4 d-flex align-items-center">
                                {!! $product->stock_quantity > 0 ? '<span class="text-success me-3">В наличии</span>' : '<span class="text-danger me-3">Нет в наличии</span>' !!}
                                @if($reviewsCount > 0)
                                    <small class="text-warning">
                                        @for ($i = 1; $i <= 5; $i++)<i class="bi bi-star{{ $i <= round($avgRating) ? '-fill' : '' }}"></i>@endfor
                                    </small>
                                    <a href="#reviews-tab-pane" class="ms-2 text-muted">({{ $reviewsCount }} {{ trans_choice('отзыв|отзыва|отзывов', $reviewsCount) }})</a>
                                @else
                                    <small class="text-muted">Нет отзывов</small>
                                @endif
                            </div>
                            <div class="fs-4">
                                <span class="fw-bold text-dark">{{ number_format($product->sale_price ?? $product->price,  0, '', ' ') }} сум</span>
                                @if($product->sale_price && $product->sale_price < $product->price)
                                    <span class="text-decoration-line-through text-muted ms-1">{{ number_format($product->price,  0, '', ' ') }} сум</span>
                                @endif
                            </div>

                            <hr class="my-6" />

                            {{-- БЛОК С КОРЗИНОЙ И КОЛИЧЕСТВОМ (теперь здесь) --}}
                            <div class="d-flex align-items-stretch gap-2 mb-4">
                                <div class="input-group input-spinner" style="width: auto;">
                                    <button type="button" class="button-minus btn btn-outline-secondary h-100" data-field="quantity">-</button>
                                    <input type="number" step="1" max="{{ $product->stock_quantity }}" value="1" name="quantity" class="quantity-field form-control-sm form-input text-center" style="width: 60px;">
                                    <button type="button" class="button-plus btn btn-outline-secondary h-100" data-field="quantity">+</button>
                                </div>
                                <div class="flex-grow-1">
                                    @auth
                                        {{-- ЕСЛИ ПОЛЬЗОВАТЕЛЬ АВТОРИЗОВАН --}}
                                        {{-- Проверяем, есть ли товар уже в корзине --}}
                                        @php
                                            $productInCart = auth()->user()->cartItems()->where('product_id', $product->id)->exists();
                                        @endphp
                                        <button type="button" class="btn {{ $productInCart ? 'btn-success' : 'btn-primary' }} w-100 h-100 add-to-cart-btn"
                                                data-product-id="{{ $product->id }}"
                                            {{ $productInCart ? 'disabled' : '' }}>
                                            <i class="feather-icon icon-shopping-bag me-2"></i>
                                            <span class="btn-text">{{ $productInCart ? 'В корзине' : 'В корзину' }}</span>
                                            <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                        </button>
                                    @else
                                        {{-- ЕСЛИ ПОЛЬЗОВАТЕЛЬ ГОСТЬ --}}
                                        <button type="button" class="btn btn-primary w-100 h-100" data-bs-toggle="modal" data-bs-target="#userModal">
                                            <i class="feather-icon icon-shopping-bag me-2"></i>В корзину
                                        </button>
                                    @endauth
                                </div>

                                <div class="dropdown">
                                    <a class="btn btn-outline-secondary h-100 d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" title="Поделиться">
                                        <i class="feather-icon icon-share-2"></i>
                                    </a>
                                    <ul class="dropdown-menu">

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
                                    </ul>
                                </div>
                            </div>

                            <hr class="my-6" />

                            {{-- БЛОК С ХАРАКТЕРИСТИКАМИ (теперь здесь) --}}
                            @if(!empty($allAttributes))
                                @php
                                    // Ключевые слова для summary-блока
                                    $keySpecKeywords = ['ккал', 'белки', 'жиры', 'углеводы'];
                                    $keySpecs = [];
                                    // Находим ключевые характеристики
                                    foreach ($allAttributes as $name => $value) {
                                        foreach ($keySpecKeywords as $keyword) {
                                            if (mb_stripos($name, $keyword) !== false) {
                                                $keySpecs[strtolower($keyword)] = ['name' => $name, 'value' => $value];
                                                break;
                                            }
                                        }
                                    }
                                @endphp

                                <h4 class="mb-3">Пищевая ценность на 100г продукта</h4>

                                {{-- Краткий summary-блок ("квадрат") --}}
                                <div class="p-3 border rounded-3 bg-light d-flex justify-content-around text-center flex-wrap gap-3">
                                    @foreach (['белки', 'жиры', 'углеводы', 'ккал'] as $keyword)
                                        @if(isset($keySpecs[$keyword]))
                                            <div>
                                                <div class="fw-bold fs-4">{{ $keySpecs[$keyword]['value'] }}</div>
                                                <div class="small text-muted">{{ $keyword }}</div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>

                                {{-- Детальная таблица (выводим первые 5 из ВСЕХ атрибутов) --}}
                                <div class="mt-4">
                                    <h4 class="mb-3">Характеристики</h4>
                                    <table class="product-specs-table">
                                        <tbody>
                                        @foreach(array_slice($allAttributes, 0, 5, true) as $name => $value)
                                            <tr>
                                                <td class="spec-name">{{ $name }}</td>
                                                <td class="spec-dots"></td>
                                                <td class="spec-value">{{ $value }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                    {{-- Ссылка для показа всех характеристик (если их больше 5) --}}
                                    @if(count($allAttributes) > 5)
                                        <a href="#all-specs-tab-pane" class="text-decoration-none" id="show-all-specs-link">Все характеристики</a>
                                    @endif
                                </div>
                            @endif
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

                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="all-specs-tab" data-bs-toggle="tab" data-bs-target="#all-specs-tab-pane" type="button" role="tab" aria-controls="all-specs-tab-pane" aria-selected="false">
                                        Все характеристики
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

                            {{-- НОВЫЙ КОД --}}
                            <div class="tab-pane fade" id="all-specs-tab-pane" role="tabpanel" aria-labelledby="all-specs-tab" tabindex="0">
                                <div class="my-8">
                                    @php
                                        // 1. Определяем желаемый порядок ключей
                                        $displayOrder = [
                                            'Вес, г',
                                            'Белки',
                                            'Жиры',
                                            'Углеводы',
                                            'Энергетическая ценность',
                                            'Пищевые волокна',
                                            'Ккал'
                                        ];
                                        // 2. Создаем копию всех атрибутов, чтобы из нее удалять уже выведенные
                                        $otherAttributes = $allAttributes;
                                    @endphp

                                    <table class="table table-bordered table-striped">
                                        <tbody>
                                        {{-- 3. Сначала выводим атрибуты в заданном порядке --}}
                                        @foreach ($displayOrder as $specName)
                                            @if (isset($allAttributes[$specName]))
                                                <tr>
                                                    <td class="fw-medium">{{ $specName }}</td>
                                                    <td class="text-end">{{ $allAttributes[$specName] }}</td>
                                                </tr>
                                                @php
                                                    // Удаляем выведенный атрибут из копии, чтобы не дублировать
                                                    unset($otherAttributes[$specName]);
                                                @endphp
                                            @endif
                                        @endforeach

                                        {{-- 4. Теперь выводим все остальные атрибуты, которые не вошли в основной список --}}
                                        @foreach ($otherAttributes as $name => $value)
                                            <tr>
                                                <td class="fw-medium">{{ $name }}</td>
                                                <td class="text-end">{{ $value }}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
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
    <script src="{{ asset('assets/libs/tiny-slider/dist/min/tiny-slider.js') }}"></script>
    <script src="{{ asset('assets/js/vendors/tns-slider.js') }}"></script>
    <script src="{{ asset('assets/js/vendors/zoom.js') }}"></script>
@endpush
@push('scripts')
    {{-- Подключаем GLightbox, так как он нужен для этой страницы --}}
    <script src="https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // =================================================================
            // ИНИЦИАЛИЗАЦИЯ ПЛАГИНОВ И СТАТИЧЕСКИХ ЭЛЕМЕНТОВ
            // =================================================================

            // 1. Инициализация Tiny Slider (для галереи)
            if (document.querySelector('#product') && document.querySelector('#productThumbnails')) {
                try {
                    var slider = tns({
                        container: '#product',
                        items: 1,
                        autoplay: false,
                        controls: false,
                        navContainer: "#productThumbnails",
                        navAsThumbnails: true,
                    });
                } catch (error) { console.error('Ошибка Tiny Slider:', error); }
            }

            // 2. Инициализация GLightbox (для попапа)
            if (typeof GLightbox !== 'undefined') {
                try {
                    const lightbox = GLightbox({ selector: '.glightbox', loop: true });
                } catch (error) { console.error('Ошибка GLightbox:', error); }
            }

            // 3. Обработчик для ссылки "Все характеристики"
            const showAllSpecsLink = document.getElementById('show-all-specs-link');
            if (showAllSpecsLink) {
                showAllSpecsLink.addEventListener('click', function(e) {
                    e.preventDefault();
                    const tabTrigger = document.getElementById('all-specs-tab');
                    if (tabTrigger) {
                        new bootstrap.Tab(tabTrigger).show();
                        document.getElementById('myTab').scrollIntoView({ behavior: 'smooth' });
                    }
                });
            }

            // 4. Обработчик для формы отзыва
            const writeReviewBtn = document.getElementById('write-review-btn');
            const reviewFormContainer = document.getElementById('review-form-container');
            if (writeReviewBtn && reviewFormContainer) {
                writeReviewBtn.addEventListener('click', function() {
                    if (reviewFormContainer.style.display === 'none') {
                        reviewFormContainer.style.display = 'block';
                        this.innerText = 'Скрыть форму';
                    } else {
                        reviewFormContainer.style.display = 'none';
                        this.innerText = 'Написать отзыв';
                    }
                });
                // Показываем форму, если были ошибки валидации
                @if($errors->any())
                    reviewFormContainer.style.display = 'block';
                writeReviewBtn.innerText = 'Скрыть форму';
                @endif
            }

            // 5. Обработчик для кнопок +/- счетчика основного товара
            const mainQuantitySpinner = document.querySelector('.col-md-7 .input-spinner');
            if(mainQuantitySpinner) {
                const minusBtn = mainQuantitySpinner.querySelector('.button-minus');
                const plusBtn = mainQuantitySpinner.querySelector('.button-plus');
                const quantityInput = mainQuantitySpinner.querySelector('.quantity-field');
                if (minusBtn && plusBtn && quantityInput) {
                    minusBtn.addEventListener('click', () => {
                        let val = parseInt(quantityInput.value);
                        if (val > 1) quantityInput.value = val - 1;
                    });
                    plusBtn.addEventListener('click', () => {
                        let val = parseInt(quantityInput.value);
                        let max = parseInt(quantityInput.max);
                        if (val < max) quantityInput.value = val + 1;
                    });
                }
            }


            // =================================================================
            // ДЕЛЕГИРОВАНИЕ СОБЫТИЙ ДЛЯ ДИНАМИЧЕСКИХ ЭЛЕМЕНТОВ
            // =================================================================
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (!csrfToken) {
                console.error('CSRF-токен не найден!');
                return;
            }

            // Вешаем один "умный" обработчик на весь `main` тег
            document.querySelector('main').addEventListener('click', async function(event) {
                const target = event.target;

                // --- Логика для кнопки "В ИЗБРАННОЕ" (работает везде) ---
                const wishlistBtn = target.closest('.wishlist-toggle-btn');
                if (wishlistBtn) {
                    event.preventDefault();
                    event.stopPropagation();
                    toggleWishlist(wishlistBtn);
                    return;
                }

                // --- Логика для кнопки "В КОРЗИНУ" (работает везде) ---
                const cartBtn = target.closest('.add-to-cart-btn');
                if (cartBtn) {
                    event.preventDefault();
                    event.stopPropagation();
                    addToCart(cartBtn);
                    return;
                }
            });


            // =================================================================
            // ФУНКЦИИ-ОБРАБОТЧИКИ
            // =================================================================

            async function toggleWishlist(button) {
                const productId = button.dataset.productId;
                const icon = button.querySelector('i');
                if (!productId || !icon) return;

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
                if (!productId) return;

                let quantity = 1; // По умолчанию 1 товар (для карточек в "похожих")
                // Если это основная кнопка, берем количество из ее счетчика
                if (button.closest('.col-md-7')) {
                    const quantityInput = document.querySelector('input[name="quantity"]');
                    if (quantityInput) quantity = quantityInput.value;
                }

                button.disabled = true;
                if (btnText) btnText.style.display = 'none';
                if (spinner) spinner.classList.remove('d-none');

                try {
                    const urlTemplate = "{{ route('cart.add', ['product' => ':id']) }}";
                    const finalUrl = urlTemplate.replace(':id', productId);
                    const response = await fetch(finalUrl, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                        body: JSON.stringify({ quantity: quantity })
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

            // =================================================================
            // ЛОГИКА ДЛЯ КНОПКИ "ПОДЕЛИТЬСЯ"
            // =================================================================
            const shareTitle = "{{ e($product->name) }}";
            const shareUrl = "{{ url()->current() }}";
            const shareText = "Посмотрите, какой отличный продукт я нашел: {{ e($product->name) }}";
            const telegramLink = document.getElementById('telegram-share-link');
            if (telegramLink) {
                telegramLink.href = `https://t.me/share/url?url=${encodeURIComponent(shareUrl)}&text=${encodeURIComponent(shareText)}`;
            }
            const whatsappLink = document.getElementById('whatsapp-share-link');
            if (whatsappLink) {
                whatsappLink.href = `https://api.whatsapp.com/send?text=${encodeURIComponent(shareText + ' ' + shareUrl)}`;
            }
            // ... и так далее для других соцсетей ...
        });
    </script>
@endpush
