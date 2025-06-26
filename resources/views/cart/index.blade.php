@extends('layouts.app')

@section('title', 'Моя корзина')

@section('content')
    <main>
        <!-- section-->
        <div class="mt-4">
            <div class="container">
                <!-- row -->
                <div class="row">
                    <!-- col -->
                    <div class="col-12">
                        <!-- breadcrumb -->
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
        <!-- section -->
        <section class="mb-lg-14 mb-8 mt-8">
            <div class="container">
                <!-- row -->
                <div class="row">
                    <div class="col-12">
                        <!-- card -->
                        <div class="card py-1 border-0 mb-8">
                            <div>
                                <h1 class="fw-bold">Моя корзина</h1>
                                {{-- Динамическое количество товаров --}}
                                <p class="mb-0">В вашей корзине {{ $cartItems->sum('quantity') }} товар(ов).</p>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- row -->
                @if($cartItems->isNotEmpty())
                    <div class="row">
                        <div class="col-lg-8 col-md-7">
                            <div class="py-3">
                                <!-- alert -->
                                @if($subtotal >= $freeShippingThreshold)
                                    <div class="alert alert-success p-2" role="alert">
                                        У вас БЕСПЛАТНАЯ доставка!
                                        <a href="{{ route('checkout') }}" class="alert-link">Перейти к оформлению!</a>
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
                                                            <!-- remove -->
                                                            <div class="mt-2 small lh-1">
                                                                <button class="btn btn-link text-decoration-none text-inherit p-0 border-0 cart-item-remove-btn"
                                                                        data-item-id="{{ $item->id }}">
                                                                    <span class="me-1 align-text-bottom">
                                                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2 text-success">
                                                                            <polyline points="3 6 5 6 21 6"></polyline>
                                                                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                                            <line x1="10" y1="11" x2="10" y2="17"></line>
                                                                            <line x1="14" y1="11" x2="14" y2="17"></line>
                                                                        </svg>
                                                                    </span>
                                                                    <span class="text-muted">Удалить</span>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- input group -->
                                                <div class="col-3 col-md-3 col-lg-3"> <!-- Изменил col-4 на col-3 для лучшего выравнивания -->
                                                    <div class="input-group input-spinner cart-item-quantity-control" data-item-id="{{ $item->id }}">
                                                        <button type="button" class="button-minus btn btn-sm border" data-field="quantity">-</button>
                                                        <input type="number" step="1" max="{{ $item->product->stock_quantity }}" value="{{ $item->quantity }}" name="quantity"
                                                               class="quantity-field form-control-sm form-input w-50 text-center border-start-0 border-end-0 cart-item-quantity-input"
                                                               readonly> {{-- readonly, чтобы менять только кнопками или JS --}}
                                                        <button type="button" class="button-plus btn btn-sm border" data-field="quantity">+</button>
                                                    </div>
                                                </div>
                                                <!-- price -->
                                                <div class="col-3 col-md-3 col-lg-2 text-lg-end text-start text-md-end"> <!-- Изменил col-2 на col-3 -->
                                                    <span class="fw-bold text-dark item-total-price" data-item-id="{{ $item->id }}">
                                                        {{ number_format(($item->product->sell_price ?? $item->product->price) * $item->quantity, 0, '.', ' ') }} сум
                                                    </span>
                                                    @if($item->product->sell_price && $item->product->sell_price < $item->product->price)
                                                        <div class="text-decoration-line-through text-muted small">
                                                            {{ number_format($item->product->price * $item->quantity, 0, '.', ' ') }} сум
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                                <!-- btn -->
                                <div class="d-flex justify-content-between mt-4">
                                    <a href="{{ route('welcome') }}" class="btn btn-primary">Продолжить покупки</a>
                                    {{-- <a href="#!" class="btn btn-dark">Update Cart</a> --}} {{-- Кнопка Update Cart обычно нужна, если нет AJAX --}}
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
                                                    <div>Промежуточный итог</div>
                                                </div>
                                                <span id="summary-subtotal">{{ number_format($subtotal, 0, '.', ' ') }} сум</span>
                                            </li>
                                            {{-- <li class="list-group-item d-flex justify-content-between align-items-start">
                                                <div class="me-auto">
                                                    <div>Service Fee</div>
                                                </div>
                                                <span id="summary-service-fee">{{ number_format($serviceFee, 0, '.', ' ') }} сум</span>
                                            </li> --}}
                                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                                <div class="me-auto">
                                                    <div class="fw-bold">Итого</div>
                                                </div>
                                                <span class="fw-bold" id="summary-total">{{ number_format($total, 0, '.', ' ') }} сум</span>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="d-grid mb-1 mt-4">
                                        <a href="#" class="btn btn-primary btn-lg d-flex justify-content-between align-items-center">
                                            Перейти к оформлению
                                            <span class="fw-bold" id="checkout-total-btn">{{ number_format($total, 0, '.', ' ') }} сум</span>
                                        </a>
                                    </div>
                                    <p><small>Размещая заказ, вы соглашаетесь с <a href="#!">Условиями обслуживания</a> и <a href="#!">Политикой конфиденциальности</a>.</small></p>
                                    {{-- Промокод можно оставить как есть, его логика будет отдельной --}}
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
                // ... ваш код showAlert ...
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
                        // Это самый простой способ после AJAX действия на странице корзины
                        window.location.reload();
                    } else {
                        showAlert('danger', data.message || 'Произошла ошибка');
                        // Если была ошибка валидации количества, сервер может вернуть 400/422
                        if(response.status === 400 || response.status === 422) {
                            // Можно попытаться восстановить предыдущее значение инпута, но проще перезагрузить
                            // window.location.reload();
                        }
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
                    const url = `{{ url('cart/update') }}/${itemId}`; // Используем url() для простоты, замените на route() если нужно
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

                // Можно добавить обработчик на input.addEventListener('change', ...)
                // но с кнопками +/- UX часто лучше
            });

            // Обработчики для удаления товара
            document.querySelectorAll('.cart-item-remove-btn').forEach(button => {
                button.addEventListener('click', async function() {
                    if (confirm('Вы уверены, что хотите удалить этот товар из корзины?')) {
                        const itemId = this.dataset.itemId;
                        const url = `{{ url('cart/remove') }}/${itemId}`; // Используем url()
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
            // Код из вашего предыдущего JS для addToCartButtons
            document.querySelectorAll('.add-to-cart-btn').forEach(button => {
                // Убедимся, что это не кнопки ВНУТРИ страницы корзины (они уже обработаны)
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
                                    // При открытии offcanvas данные там должны быть свежими (серверный рендеринг)
                                    // или вы можете вызвать здесь функцию для AJAX обновления содержимого offcanvas, если хотите
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
