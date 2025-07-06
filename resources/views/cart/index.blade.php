@extends('layouts.app')
@section('title', 'Моя корзина')

@push('styles')
    <style>
        .input-spinner { display: inline-flex; align-items: center; border: 1px solid #e9ecef; border-radius: 0.375rem; }
        .input-spinner .btn { width: 28px; height: 28px; font-size: 1rem; color: #6c757d; background-color: #f8f9fa; border: none; padding: 0; line-height: 1; }
        .input-spinner .btn:first-child { border-right: 1px solid #e9ecef; }
        .input-spinner .btn:last-child { border-left: 1px solid #e9ecef; }
        .input-spinner .form-input { width: 40px; height: 28px; text-align: center; border: none; background-color: white; font-weight: 500; font-size: 0.9rem; }
        .input-spinner .form-input:focus { outline: none; box-shadow: none; }
        .delete-item-btn { background: none; border: none; color: #6c757d; padding: 0.25rem 0.5rem; margin-left: 0.5rem; cursor: pointer; line-height: 1; }
        .delete-item-btn:hover { color: #dc3545; }
        .delete-item-btn svg { width: 18px; height: 18px; }
        .checkout-button-final { background-color: #FF569F; border-color: #FF569F; font-size: 1rem; font-weight: 500; }
        .checkout-button-final:hover { background-color: #8a0c6a; border-color: #8a0c6a; }
        .checkout-button-final .fw-bold { font-size: 1.1rem; }
    </style>
@endpush

@section('content')
    <main>
        <div class="mt-4">
            <div class="container"></div>
        </div>

        <section class="mb-lg-14 mb-8 mt-8">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="card py-1 border-0 mb-8">
                            <h1 class="fw-bold">{{__('My cart')}}</h1>
                            {{-- Общее количество товаров --}}
                            <p class="mb-0" id="cart-summary-text">{{__('in your cart')}} <span id="cart-item-count-text">{{ $cartItems->sum('quantity') }}</span>
                                {{__('tovars')}}</p>
                        </div>
                    </div>
                </div>

                @if($cartItems->isNotEmpty())
                    <div class="row" id="cart-container">
                        <div class="col-lg-8 col-md-7">
                            <div class="py-3">
                                {{-- Блок с информацией о бесплатной доставке --}}
                                <div id="free-shipping-alert">
                                    @if($subtotal >= $freeShippingThreshold)
                                        <div class="alert alert-success p-2">{{__('You have free sheeping')}}</div>
                                    @elseif($subtotal > 0)
                                        <div class="alert alert-info p-2">{{__('To free delivery')}} <b>{{ number_format($needsForFreeShipping, 0, '.', ' ') }} {{__('sum')}}</b>.</div>
                                    @endif
                                </div>

                                <ul class="list-group list-group-flush">
                                    @foreach ($cartItems as $item)
                                        <li class="list-group-item py-3 ps-0 @if(!$loop->last) border-bottom @endif" id="cart-item-row-{{ $item->id }}">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-grow-1 d-flex align-items-center">
                                                    <img src="{{ $item->product->primaryImage ? asset('storage/' . $item->product->primaryImage->image_url) : asset('assets/images/placeholder.png') }}" alt="{{ $item->product->name }}" class="icon-shape icon-xxl" style="height: 65px; width: 60px;" />
                                                    <div class="ms-3">
                                                        <a href="{{ route('product.show', $item->product->slug) }}" class="text-inherit"><h6 class="mb-0">{{ $item->product->name }}</h6></a>
                                                    </div>
                                                </div>
                                                <div class="d-flex align-items-center">
                                                    <div class="input-spinner cart-item-quantity-control" data-item-id="{{ $item->id }}">
                                                        <button type="button" class="button-minus btn">-</button>
                                                        <input type="number" step="1" max="{{ $item->product->stock_quantity }}" value="{{ $item->quantity }}" class="quantity-field form-input text-center" readonly>
                                                        <button type="button" class="button-plus btn">+</button>
                                                    </div>
                                                    <div class="text-end ms-3" style="min-width: 90px;">
                                                        <span class="fw-bold text-dark item-total-price" data-item-id="{{ $item->id }}">{{ number_format(($item->product->sell_price ?? $item->product->price) * $item->quantity, 0, '.', ' ') }} {{__('sum')}}</span>
                                                    </div>
                                                    <button class="delete-item-btn cart-item-remove-btn" data-item-id="{{ $item->id }}" title="Удалить товар">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                                                    </button>
                                                </div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                                <div class="d-flex justify-content-between mt-4">
                                    <a href="{{ route('welcome') }}" class="btn btn-primary">{{__('Continue shopping')}}</a>
                                    <form action="{{ route('cart.clear') }}" method="POST" onsubmit="return confirm('Вы уверены, что хотите очистить корзину?');">
                                        @csrf
                                        @method('DELETE') {{-- Используем DELETE метод для очистки --}}
                                        <button type="submit" class="btn btn-outline-danger">{{__('Clear cart')}}</button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        {{-- СУММА ЗАКАЗА --}}
                        <div class="col-12 col-lg-4 col-md-5">
                            <div class="mb-5 card mt-6">
                                <div class="card-body p-6">
                                    <h2 class="h5 mb-4">{{__('Order sum')}}</h2>
                                    <div class="card mb-2">
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item d-flex justify-content-between">
                                                <span>{{__('Products sum')}}</span>
                                                <span id="summary-subtotal">{{ number_format($subtotal, 0, '.', ' ') }} {{__('sum')}}</span>
                                            </li>
                                            <li class="list-group-item d-flex flex-column py-3">
                                                {{-- Верхняя строка с основной информацией --}}
                                                <div class="d-flex justify-content-between w-100">
                                                    <span>{{__('Delivery in city')}}</span>
                                                    <span id="summary-shipping">
                                                        @if($shippingCost > 0)
                                                            {{ number_format($shippingCost, 0, '.', ' ') }} {{__('sum')}}
                                                        @else
                                                            <span class="text-success">{{__('free')}}</span>
                                                        @endif
                                                     </span>
                                                </div>

                                                {{-- Нижняя строка с примечанием --}}
                                                <small class="text-muted mt-1">{{__('delivery_sum')}}</small>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between fw-bold fs-5">
                                                <span>{{__('final_price')}}</span>
                                                <span id="summary-total">{{ number_format($total, 0, '.', ' ') }} {{__('sum')}}</span>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="d-grid mb-1 mt-4">
                                        <a href="{{ route('checkout.index') }}" class="btn btn-primary btn-lg d-flex justify-content-center align-items-center checkout-button-final">
                                            <span class="bg-center">{{ __('Go to order') }}</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="row">
                        <div class="col-12 text-center py-8">
                            <h2>{{__('Your cart is free')}}</h2>
                            <a href="{{ route('welcome') }}" class="btn btn-primary mt-2">{{__("Go to buy")}}</a>
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
            // --- ОБЩИЕ ПЕРЕМЕННЫЕ И ФУНКЦИИ ---
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const formatNumber = (num) => new Intl.NumberFormat('ru-RU').format(Math.round(num));

            // --- ИСХОДНЫЕ ДАННЫЕ ИЗ BLADE ДЛЯ ЛОГИКИ О ДОСТАВКЕ ---
            const freeShippingThreshold = {{ $freeShippingThreshold }};

            // --- ФУНКЦИЯ ДЛЯ ОБНОВЛЕНИЯ ВСЕХ ДАННЫХ НА СТРАНИЦЕ ---
            function updateCartUI(summary) {
                // Обновляем текст с количеством товаров
                const countTextEl = document.getElementById('cart-item-count-text');
                if (countTextEl) {
                    countTextEl.textContent = `${summary.count} {{__('tovars')}}`;
                }

                // Обновляем блок с итогами
                document.getElementById('summary-subtotal').textContent = `${formatNumber(summary.subtotal)} {{__('sum')}}`;
                document.getElementById('summary-total').textContent = `${formatNumber(summary.total)} {{__('sum')}}`;
               // document.getElementById('checkout-total-btn').textContent = `${formatNumber(summary.total)} {{__('sum')}}`;

                // Обновляем стоимость доставки с логикой
                const shippingEl = document.getElementById('summary-shipping');
                if (summary.shipping > 0) {
                    shippingEl.innerHTML = `${formatNumber(summary.shipping)} {{__('sum')}}`;
                    shippingEl.classList.remove('text-success');
                } else {
                    shippingEl.innerHTML = `<span class="text-success">Бесплатно</span>`;
                }

                // Обновляем блок с оповещением о бесплатной доставке
                const alertEl = document.getElementById('free-shipping-alert');
                if (summary.subtotal >= freeShippingThreshold) {
                    alertEl.innerHTML = `<div class="alert alert-success p-2">У вас БЕСПЛАТНАЯ доставка!</div>`;
                } else if (summary.subtotal > 0) {
                    alertEl.innerHTML = `<div class="alert alert-info p-2">До бесплатной доставки осталось: <b>${formatNumber(summary.needsForFreeShipping)} {{__('sum')}}</b>.</div>`;
                } else {
                    alertEl.innerHTML = ''; // Скрываем блок, если корзина пуста
                }

                // Если в корзине 0 товаров, показываем сообщение об этом
                if (summary.count === 0) {
                    const cartContainer = document.getElementById('cart-container');
                    if(cartContainer) {
                        cartContainer.innerHTML = `<div class="col-12 text-center py-8"><h2>Ваша корзина пуста</h2><a href="{{ route('welcome') }}" class="btn btn-primary mt-2">Начать покупки</a></div>`;
                    }
                }
            }


            // --- УНИВЕРСАЛЬНАЯ ФУНКЦИЯ ДЛЯ ОТПРАВКИ ЗАПРОСОВ ---
            async function sendCartUpdateRequest(itemId, quantity) {
                // Если количество 0, это удаление
                const isRemoving = quantity === 0;
                const url = isRemoving ? `{{ url('cart/remove') }}/${itemId}` : `{{ url('cart/update') }}/${itemId}`;
                const method = isRemoving ? 'DELETE' : 'PATCH';

                // Блокируем кнопки на время запроса, чтобы избежать двойных кликов
                document.querySelectorAll('.cart-item-quantity-control button, .delete-item-btn').forEach(btn => btn.disabled = true);

                try {
                    const response = await fetch(url, {
                        method: method,
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({ quantity: quantity })
                    });

                    if (!response.ok) {
                        const errorData = await response.json();
                        throw new Error(errorData.message || 'Ошибка сервера.');
                    }

                    const data = await response.json();

                    // Обновляем сумму конкретного товара
                    if (data.item_total_formatted) {
                        const itemTotalEl = document.querySelector(`.item-total-price[data-item-id="${itemId}"]`);
                        if (itemTotalEl) {
                            itemTotalEl.textContent = `${data.item_total_formatted} {{__('sum')}}`;
                        }
                    }

                    // Если удаляем товар, убираем его строку из HTML
                    if (isRemoving) {
                        const itemRow = document.getElementById(`cart-item-row-${itemId}`);
                        if(itemRow) itemRow.remove();
                    }

                    // Обновляем все итоговые суммы и сообщения
                    updateCartUI(data.summary);

                } catch (error) {
                    console.error('Ошибка при обновлении корзины:', error);
                    alert(`Произошла ошибка: ${error.message}`);
                    // Если произошла ошибка, лучше перезагрузить страницу, чтобы показать актуальное состояние
                    window.location.reload();
                } finally {
                    // Разблокируем кнопки после завершения запроса
                    document.querySelectorAll('.cart-item-quantity-control button, .delete-item-btn').forEach(btn => btn.disabled = false);
                }
            }

            // --- ДЕЛЕГИРОВАНИЕ СОБЫТИЙ ДЛЯ КНОПОК +/- И УДАЛЕНИЯ ---
            document.querySelector('.list-group-flush')?.addEventListener('click', function(e) {
                const quantityControl = e.target.closest('.cart-item-quantity-control');
                const removeBtn = e.target.closest('.cart-item-remove-btn');

                if (quantityControl) {
                    const button = e.target.closest('button');
                    if (!button) return;

                    const input = quantityControl.querySelector('.quantity-field');
                    const itemId = quantityControl.dataset.itemId;
                    let currentValue = parseInt(input.value);
                    let newValue = currentValue;

                    if (button.classList.contains('button-plus')) {
                        const max = parseInt(input.getAttribute('max'));
                        if (currentValue < max) {
                            newValue = currentValue + 1;
                            input.value = newValue; // Оптимистичное обновление
                        }
                    } else if (button.classList.contains('button-minus')) {
                        if (currentValue > 1) {
                            newValue = currentValue - 1;
                            input.value = newValue; // Оптимистичное обновление
                        }
                    }

                    if (newValue !== currentValue) {
                        sendCartUpdateRequest(itemId, newValue);
                    }
                }

                if (removeBtn) {
                    const itemId = removeBtn.dataset.itemId;
                    if (confirm('Вы уверены, что хотите удалить этот товар?')) {
                        sendCartUpdateRequest(itemId, 0); // Отправляем 0 для удаления
                    }
                }
            });
        });
    </script>
@endpush
