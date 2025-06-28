@extends('layouts.app')
@section('title', 'Моя корзина')
@push('styles')
    <style>
        /* Улучшение кнопок +/- в корзине */
        .input-spinner {
            display: inline-flex;
            align-items: center;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            overflow: hidden;
            background: white;
            max-width: 80px; /* Еще больше уменьшил */
        }

        .input-spinner .btn {
            width: 22px; /* Сильно уменьшил */
            height: 22px; /* Сильно уменьшил */
            display: flex;
            align-items: center;
            justify-content: center;
            border: none !important;
            border-radius: 0;
            background: #f8f9fa;
            color: #495057;
            font-size: 12px; /* Еще меньше */
            font-weight: bold;
            line-height: 1;
            padding: 0;
            transition: all 0.2s ease;
            flex-shrink: 0;
            min-width: 22px;
        }

        .input-spinner .btn:hover {
            background: #e9ecef;
            color: #212529;
        }

        .input-spinner .btn:active {
            background: #dee2e6;
        }

        .input-spinner .form-input {
            border: none !important;
            background: white;
            text-align: center;
            font-weight: 600;
            color: #212529;
            width: 36px; /* Сильно уменьшил */
            height: 22px; /* Сильно уменьшил */
            margin: 0;
            padding: 0 2px;
            outline: none;
            box-shadow: none;
            flex-shrink: 0;
            font-size: 11px; /* Еще меньше */
        }

        .input-spinner .form-input:focus {
            outline: none;
            box-shadow: none;
        }

        /* Стили для кнопки удаления после суммы */
        .delete-item-btn {
            background: none;
            border: none;
            color: #6c757d;
            padding: 4px;
            cursor: pointer;
            transition: color 0.2s ease;
            margin-left: 8px;
        }

        .delete-item-btn:hover {
            color: #dc3545;
        }

        .delete-item-btn svg {
            width: 18px;
            height: 18px;
        }

        /* Мобильная оптимизация */
        @media (max-width: 576px) {
            /* Улучшение отступов в строке товара корзины */
            .list-group-item .row .col-3 {
                padding-left: 8px;
                padding-right: 8px;
            }

            /* Выравнивание цены */
            .item-total-price {
                font-size: 14px;
                font-weight: 700;
            }

            /* Компактный размер для очень маленьких экранов */
            .input-spinner {
                max-width: 70px; /* Еще меньше для мобильных */
            }

            .input-spinner .btn {
                width: 20px; /* Еще меньше для мобильных */
                height: 20px;
                font-size: 10px;
                min-width: 20px;
            }

            .input-spinner .form-input {
                height: 20px;
                width: 30px;
                font-size: 10px;
                padding: 0 1px;
            }

            .delete-item-btn svg {
                width: 16px;
                height: 16px;
            }
        }

        /* Для планшетов */
        @media (min-width: 577px) and (max-width: 991px) {
            .input-spinner .btn {
                width: 21px;
                height: 21px;
                font-size: 11px;
                min-width: 21px;
            }

            .input-spinner .form-input {
                height: 21px;
                width: 34px;
                font-size: 11px;
            }
        }

        /* Дополнительные стили для лучшего вида */
        .cart-item-remove-btn {
            font-size: 13px;
            color: #6c757d;
            transition: color 0.2s ease;
        }

        .cart-item-remove-btn:hover {
            color: #dc3545;
        }

        .cart-item-remove-btn svg {
            width: 12px;
            height: 12px;
        }
    </style>
@endpush

@section('content')
    <main>
        <div class="mt-4">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="{{ route('welcome') }}">Главная</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Моя корзина</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <section class="mb-lg-14 mb-8 mt-8">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="card py-1 border-0 mb-8">
                            <div>
                                <h1 class="fw-bold">Моя корзина</h1>
                                <p class="mb-0">В вашей корзине {{ count($cartItems) }} {{ \Str::plural('товар', count($cartItems), ['товар', 'товара', 'товаров']) }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                @if($cartItems->isNotEmpty())
                    <div class="row">
                        <div class="col-lg-8 col-md-7">
                            <div class="py-3">
                                @if($subtotal >= $freeShippingThreshold)
                                    <div class="alert alert-success p-2" role="alert">
                                        У вас БЕСПЛАТНАЯ доставка!
                                        <a href="{{ route('checkout.index') }}" class="alert-link">Перейти к оформлению</a>
                                    </div>
                                @elseif($subtotal > 0)
                                    <div class="alert alert-info p-2" role="alert">
                                        До бесплатной доставки осталось: {{ number_format($needsForFreeShipping, 0, '.', ' ') }} сумов.
                                    </div>
                                @endif

                                <ul class="list-group list-group-flush">
                                    @foreach ($cartItems as $item)
                                        <li class="list-group-item py-3 ps-0 @if(!$loop->last) border-bottom @endif" id="cart-item-row-{{ $item->id }}">
                                            <!-- row -->
                                            <div class="row align-items-center">
                                                <div class="col-6 col-md-6 col-lg-7">
                                                    <div class="d-flex">
                                                        <img src="{{ $item->product->primaryImage ? asset('storage/' . $item->product->primaryImage->image_url) : asset('assets/images/placeholder.png') }}"
                                                             alt="{{ $item->product->name }}" class="icon-shape icon-xxl" />
                                                        <div class="ms-3">
                                                            <a href="{{ route('product.show', $item->product->slug) }}" class="text-inherit">
                                                                <h6 class="mb-0">{{ $item->product->name }}</h6>
                                                            </a>
                                                            <span>
<small class="text-muted">
Цена: {{ number_format($item->product->sell_price ?? $item->product->price, 0, '.', ' ') }} сум.
</small>
</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- input group -->
                                                <div class="col-3 col-md-3 col-lg-3">
                                                    <div class="input-group input-spinner cart-item-quantity-control" data-item-id="{{ $item->id }}">
                                                        <button type="button" class="button-minus btn btn-sm border" data-field="quantity">-</button>
                                                        <input type="number" step="1" max="{{ $item->product->stock_quantity }}" value="{{ $item->quantity }}" name="quantity"
                                                               class="quantity-field form-control-sm form-input text-center border-start-0 border-end-0 cart-item-quantity-input"
                                                               readonly>
                                                        <button type="button" class="button-plus btn btn-sm border" data-field="quantity">+</button>
                                                    </div>
                                                </div>
                                                <!-- price -->
                                                <div class="col-3 col-md-3 col-lg-2 text-lg-end text-start text-md-end d-flex align-items-center justify-content-end">
                                                    <div class="text-end">
<span class="fw-bold text-dark item-total-price" data-item-id="{{ $item->id }}">
{{ number_format(($item->product->sell_price ?? $item->product->price) * $item->quantity, 0, '.', ' ') }} сум
</span>
                                                        @if($item->product->sell_price && $item->product->sell_price < $item->product->price)
                                                            <div class="text-decoration-line-through text-muted small">
                                                                {{ number_format($item->product->price * $item->quantity, 0, '.', ' ') }} сум
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <button class="delete-item-btn cart-item-remove-btn" data-item-id="{{ $item->id }}" title="Удалить товар">
                                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                            <polyline points="3 6 5 6 21 6"></polyline>
                                                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                            <line x1="10" y1="11" x2="10" y2="17"></line>
                                                            <line x1="14" y1="11" x2="14" y2="17"></line>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>

                                <div class="d-flex justify-content-between mt-4">
                                    <a href="{{ route('welcome') }}" class="btn btn-primary">Продолжить покупки</a>
                                    <form action="{{ route('cart.clear') }}" method="POST" id="clearCartForm" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-danger">Очистить корзину</button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- sidebar -->
                        <div class="col-12 col-lg-4 col-md-5">
                            <div class="mb-5 card mt-6">
                                <div class="card-body p-6">
                                    <h2 class="h5 mb-4">Сумма заказа</h2>
                                    <div class="card mb-2">
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                                <div class="me-auto">
                                                    <div>Сумма товаров</div>
                                                </div>
                                                <span id="summary-subtotal">{{ number_format($subtotal, 0, '.', ' ') }} сум</span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                                <div class="me-auto">
                                                    <div>Доставка</div>
                                                </div>
                                                <span id="summary-shipping">
@if($subtotal >= $freeShippingThreshold)
                                                        <span class="text-success">Бесплатно</span>
                                                    @else
                                                        {{ number_format(config('cart.shipping_cost', 15000), 0, '.', ' ') }} сум
                                                    @endif
</span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                                <div class="me-auto">
                                                    <div class="fw-bold">Итого</div>
                                                </div>
                                                <span class="fw-bold" id="summary-total">{{ number_format($total, 0, '.', ' ') }} сум</span>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="d-grid mb-1 mt-4">
                                        <button class="btn btn-primary btn-lg d-flex justify-content-between align-items-center"
                                                onclick="window.location.href='{{ route('checkout.index') }}'">
                                            <span>Перейти к оформлению</span>
                                            <span class="fw-bold" id="checkout-total-btn">{{ number_format($total, 0, '.', ' ') }} сум</span>
                                        </button>
                                    </div>
                                    <p><small>Размещая заказ, вы соглашаетесь с <a href="#!">Условиями обслуживания</a> и <a href="#!">Политикой конфиденциальности</a>.</small></p>

                                    {{-- Промокод --}}
                                    <div class="mt-8">
                                        <h2 class="h5 mb-3">Промокод или подарочная карта</h2>
                                        <form>
                                            <div class="mb-2">
                                                <label for="giftcard" class="form-label sr-only">Промокод</label>
                                                <input type="text" class="form-control" id="giftcard" placeholder="Промокод или подарочная карта" />
                                            </div>
                                            <div class="d-grid"><button type="submit" class="btn btn-outline-dark mb-1">Применить</button></div>
                                            <p class="text-muted mb-0"><small>Действуют Условия и Положения</small></p>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="row">
                        <div class="col-12 text-center">
                            <div class="py-8">
                                <h2>Ваша корзина пуста</h2>
                                <p class="mb-4">Похоже, вы еще ничего не добавили в корзину.<br>Начните покупки, чтобы наполнить ее!</p>
                                <a href="{{ route('welcome') }}" class="btn btn-primary">К покупкам</a>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </section>
    </main>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            function showAlert(type, message) {
                const alertContainer = document.createElement('div');
                alertContainer.className = `alert alert-${type} alert-dismissible fade show m-3`;
                alertContainer.style.position = 'fixed';
                alertContainer.style.top = '20px';
                alertContainer.style.right = '20px';
                alertContainer.style.zIndex = '1055';
                alertContainer.role = "alert";
                alertContainer.innerHTML = `${message}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Закрыть"></button>`;
                document.body.appendChild(alertContainer);
                setTimeout(() => { bootstrap.Alert.getOrCreateInstance(alertContainer).close(); }, 5000);
            }

// AJAX запрос для обновления/удаления
            async function updateCartAjax(url, method, body = null) {
                try {
                    const headers = {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    };
                    if (body && method !== 'GET') {
                        headers['Content-Type'] = 'application/json';
                    }

                    const response = await fetch(url, {
                        method: method,
                        headers: headers,
                        body: body ? JSON.stringify(body) : null
                    });

                    const data = await response.json();

                    if (response.ok && data.success) {
                        showAlert('success', data.message);
                        // Обновляем страницу для отображения изменений с сервера
                        window.location.reload();
                    } else {
                        showAlert('danger', data.message || 'Произошла ошибка');
                    }
                } catch (error) {
                    console.error('Ошибка AJAX запроса:', error);
                    showAlert('danger', 'Сетевая ошибка или ошибка сервера.');
                }
            }

// Обработчики для изменения количества
            document.querySelectorAll('.cart-item-quantity-control').forEach(control => {
                const itemId = control.dataset.itemId;
                const input = control.querySelector('.quantity-field');
                const minusBtn = control.querySelector('.button-minus');
                const plusBtn = control.querySelector('.button-plus');
                const stockMax = parseInt(input.getAttribute('max'));

                async function sendUpdate(quantity) {
                    const url = `{{ url('cart/update') }}/${itemId}`;
                    await updateCartAjax(url, 'PATCH', { quantity: quantity });
                }

                minusBtn.addEventListener('click', function() {
                    let currentValue = parseInt(input.value);
                    if (currentValue > 1) {
                        input.value = currentValue - 1;
                        sendUpdate(input.value);
                    }
                });

                plusBtn.addEventListener('click', function() {
                    let currentValue = parseInt(input.value);
                    if (currentValue < stockMax) {
                        input.value = currentValue + 1;
                        sendUpdate(input.value);
                    } else {
                        showAlert('warning', 'Достигнуто максимальное количество на складе.');
                    }
                });
            });

// Обработчики для удаления товара
            document.querySelectorAll('.cart-item-remove-btn').forEach(button => {
                button.addEventListener('click', async function() {
                    if (confirm('Вы уверены, что хотите удалить этот товар из корзины?')) {
                        const itemId = this.dataset.itemId;
                        const url = `{{ url('cart/remove') }}/${itemId}`;
                        await updateCartAjax(url, 'DELETE');
                    }
                });
            });

// Очистка корзины
            const clearCartForm = document.getElementById('clearCartForm');
            if(clearCartForm) {
                clearCartForm.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    if (confirm('Вы уверены, что хотите очистить корзину?')) {
                        const url = this.action;
                        await updateCartAjax(url, 'POST');
                    }
                });
            }

// Обработчики для кнопок "Добавить в корзину" со страниц каталога/товара
            document.querySelectorAll('.add-to-cart-btn').forEach(button => {
                if (!button.closest('.cart-item-quantity-control')) {
                    button.addEventListener('click', async function(e) {
                        e.preventDefault();
                        const productId = this.dataset.productId;
                        const form = this.closest('form');
                        let quantity = 1;
                        if (form) {
                            const quantityInput = form.querySelector('input[name="quantity"]');
                            if (quantityInput) quantity = parseInt(quantityInput.value) || 1;
                        }

                        try {
                            const addUrlTemplate = "{{ route('cart.add', ['product' => ':productIdPlaceholder']) }}";
                            const finalAddUrl = addUrlTemplate.replace(':productIdPlaceholder', productId);

                            const response = await fetch(finalAddUrl, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': csrfToken
                                },
                                body: JSON.stringify({ quantity: quantity })
                            });
                            const data = await response.json();

                            if (response.ok && data.success) {
                                showAlert('success', data.message);
                                // Обновляем счетчик в шапке, если он есть
                                const cartCountNav = document.getElementById('cart-count-nav');
                                if (cartCountNav && typeof data.cart_count !== 'undefined') {
                                    cartCountNav.textContent = data.cart_count;
                                }
                                // Можно добавить открытие offcanvas корзины
                                const offcanvasCartEl = document.getElementById('offcanvasRight');
                                if (offcanvasCartEl) {
                                    const offcanvasInstance = bootstrap.Offcanvas.getInstance(offcanvasCartEl) || new bootstrap.Offcanvas(offcanvasCartEl);
                                    offcanvasInstance.show();
                                }
                            } else {
                                showAlert('danger', data.message || 'Не удалось добавить товар.');
                            }
                        } catch (error) {
                            console.error('Ошибка при добавлении в корзину:', error);
                            showAlert('danger', 'Ошибка при добавлении в корзину.');
                        }
                    });
                }
            });
        });
    </script>
@endpush
