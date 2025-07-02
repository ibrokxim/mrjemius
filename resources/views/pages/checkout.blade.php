@extends('layouts.app')

@section('title', 'Оформление заказа')

@section('content')
    <main>
        <div class="mt-4">
            <div class="container">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('welcome') }}">Главная</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('cart.index') }}">Корзина</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Оформление заказа</li>
                    </ol>
                </nav>
            </div>
        </div>

        <section class="mb-lg-14 mb-8 mt-8">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div>
                            <h1 class="fw-bold">Оформление заказа</h1>
                        </div>
                    </div>
                </div>

                @if (session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                {{-- Выводим ошибки валидации, если они есть --}}
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form id="checkout-form" action="{{ route('checkout.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-lg-7 col-md-12">
                            <div class="card card-body p-6">
                                {{-- 1. Контактная информация --}}
                                <h2 class="h5 mb-4">Контактная информация</h2>
                                <div class="row">
                                    <input type="hidden" name="delivery_method" value="{{ $deliveryMethod }}">
                                    <div class="col-md-12 mb-3">
                                        <label for="fullName" class="form-label">Полное имя<span class="text-danger">*</span></label>
                                        <input type="text" id="fullName" class="form-control" name="full_name" value="{{ old('full_name', auth()->user()->name) }}" required>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label for="phone" class="form-label">Телефон<span class="text-danger">*</span></label>
                                        <input type="text" id="phone" class="form-control" name="phone_number" value="{{ old('phone_number') }}" required placeholder="+998 XX XXX-XX-XX">
                                    </div>
                                </div>

                                @if($deliveryMethod === 'delivery')
                                    <div id="deliveryAddressSection">
                                        <h2 class="h5 mb-4 mt-4">Адрес доставки</h2>

                                        {{-- Выбор существующего адреса --}}
                                        @if($addresses->isNotEmpty())
                                            <div class="mb-4">
                                                <h5>Выберите сохраненный адрес:</h5>
                                                @foreach($addresses as $address)
                                                    <div class="card mb-2"><div class="card-body p-3"><div class="form-check">
                                                                <input class="form-check-input" type="radio" name="address_option" id="address_{{ $address->id }}" value="{{ $address->id }}" {{ $loop->first ? 'checked' : '' }}>
                                                                <label class="form-check-label w-100" for="address_{{ $address->id }}">
                                                                    <span class="fw-bold">{{ $address->full_name }}, {{ $address->phone_number }}</span><br>
                                                                    <span class="text-muted">{{ $address->address_line_1 }}, {{ $address->city }}</span>
                                                                </label>
                                                            </div></div></div>
                                                @endforeach
                                            </div>
                                        @endif

                                        {{-- Опция "Новый адрес" --}}
                                        <div class="card mb-4"><div class="card-body p-3"><div class="form-check">
                                                    <input class="form-check-input" type="radio" name="address_option" id="address_new" value="new" {{ $addresses->isEmpty() ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="address_new">Добавить новый адрес</label>
                                                </div></div></div>

                                        {{-- Форма нового адреса --}}
                                        <div id="newAddressForm" class="{{ $addresses->isNotEmpty() ? 'd-none' : '' }}">
                                            <div class="row g-3">
                                                <div class="col-12"><label for="address_line_1_new" class="form-label">Адрес(улица,дом,квартира)<span class="text-danger">*</span></label><input type="text" class="form-control" name="address_line_1" id="address_line_1_new"></div>
                                                <div class="col-12"><label for="city_new" class="form-label">Город<span class="text-danger">*</span></label><input type="text" class="form-control" name="city" id="city_new" value="Ташкент"></div>
                                            </div>
                                        </div>
                                    </div>

                                @endif
                                <!-- Вставьте этот блок в нужное место вашей формы -->
                                <div class="mb-4">
                                    <h2 class="h5 mb-3 mt-4">Дата доставки</h2>

                                    {{-- Группа кнопок, которая будет вести себя как радио-кнопки --}}
                                    <div class="btn-group w-100" role="group" aria-label="Выберите дату доставки">

                                        {{-- Кнопка 1: Сегодня --}}
                                        <input type="radio" class="btn-check" name="delivery_date" id="date_today" value="today" autocomplete="off" checked>
                                        <label class="btn btn-outline-primary" for="date_today">Сегодня</label>

                                        {{-- Кнопка 2: Завтра --}}
                                        <input type="radio" class="btn-check" name="delivery_date" id="date_tomorrow" value="tomorrow" autocomplete="off">
                                        <label class="btn btn-outline-primary" for="date_tomorrow">Завтра</label>

                                        {{-- Кнопка 3: Послезавтра --}}
                                        <input type="radio" class="btn-check" name="delivery_date" id="date_day_after" value="day_after" autocomplete="off">
                                        <label class="btn btn-outline-primary" for="date_day_after">Послезавтра</label>

                                    </div>

                                    {{-- Текст с временем доставки под кнопками --}}
                                    <div class="text-muted mt-2">
                                        <label class="form-check-label">Время доставки: 19:00-22:00</label>
                                    </div>
                                </div>

                                {{-- 4. Способ оплаты --}}
                                <h2 class="h5 mb-4 mt-4">Способ оплаты</h2>
                                <div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_method" id="paymentCash" value="cash" @checked(old('payment_method', 'cash') == 'cash')>
                                        <label class="form-check-label" for="paymentCash">Наличными при получении</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_method" id="paymentTerminal" value="card_terminal" @checked(old('payment_method') == 'card_terminal')>
                                        <label class="form-check-label" for="paymentTerminal">Терминалом при получении</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_method" id="paymentCard" value="card_online" @checked(old('payment_method') == 'card_online')>
                                        <label class="form-check-label" for="paymentCard">Картой онлайн (Payme, Uzcard)</label>
                                    </div>
                                </div>

                                {{-- 5. Примечание --}}
                                <h2 class="h5 mb-4 mt-4">Примечание к заказу (необязательно)</h2>
                                <textarea class="form-control" name="customer_notes" rows="3" placeholder="Оставьте комментарий к вашему заказу...">{{ old('customer_notes') }}</textarea>
                            </div>
                        </div>

                        <div class="col-lg-5 col-md-12">
                            <div class="card mt-4 mt-lg-0">
                                <div class="card-body p-6">
                                    <h2 class="h5 mb-4">Ваш заказ</h2>

                                    {{-- БЛОК С ТОВАРАМИ --}}
                                    <ul class="list-group list-group-flush">
                                        @foreach ($cartItems as $item)
                                            <li class="list-group-item py-3">
                                                <div class="d-flex align-items-center">
                                                    <img src="{{ $item->product->primaryImage ? asset('storage/' . $item->product->primaryImage->image_url) : asset('assets/images/placeholder.png') }}" alt="{{ $item->product->name }}" class="rounded" style="width: 60px;">
                                                    <div class="ms-3 flex-grow-1">
                                                        <h6 class="mb-0">{{ $item->product->name }}</h6>
                                                        <span><small>{{ $item->quantity }} x {{ number_format($item->product->sell_price ?? $item->product->price, 0, '.', ' ') }} сум</small></span>
                                                    </div>
                                                    <span class="fw-bold">{{ number_format(($item->product->sell_price ?? $item->product->price) * $item->quantity, 0, '.', ' ') }} сум</span>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>

                                    <hr class="my-4">

                                    {{-- БЛОК С ИТОГАМИ --}}
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Сумма товаров</span>
                                        <span class="fw-bold">{{ number_format($cartSummary['subtotal'], 0, '.', ' ') }} сум</span>
                                    </div>
                                    {{-- Этот блок вставляется в pages/checkout.blade.php --}}
                                    <div class="d-flex flex-column mb-3">
                                        {{-- Верхняя строка с основной информацией --}}
                                        <div class="d-flex justify-content-between w-100">
                                            <span>Доставка</span>
                                            <span class="fw-bold">
            @if($cartSummary['shipping'] > 0)
                                                    {{ number_format($cartSummary['shipping'], 0, '.', ' ') }} сум
                                                @else
                                                    <span class="text-success">Бесплатно</span>
                                                @endif
        </span>
                                        </div>

                                        {{-- Нижняя строка с примечанием (теперь без выравнивания по правому краю) --}}
                                        <small class="text-muted mt-1">Выезд за город: +1000 сум/км</small>
                                    </div>
                                    <div class="d-flex justify-content-between fw-bold fs-5">
                                        <span>Итого к оплате: </span>
                                        <span>{{ number_format($cartSummary['total'], 0, '.', ' ') }} сум</span>
                                    </div>

                                    <div class="d-grid mt-4">
                                        <button type="submit" id="place-order-btn" class="btn btn-primary btn-lg">Оформить заказ</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                {{-- Скрытая форма для перенаправления на Payme --}}
{{--                <form id="payme-form" action="https://checkout.paycom.uz/api" method="POST" style="display: none;">--}}
{{--                    <input type="hidden" name="merchant" value="{{config('payme.merchant_id') }}">--}}
{{--                    <input type="hidden" name="amount" id="payme-amount">--}}
{{--                    <input type="hidden" name="account[order_id]" id="payme-order-id">--}}
{{--                    <input type="hidden" name="account[user_id]" id="payme-user-id"> --}}{{-- <-- НОВОЕ ПОЛЕ --}}
{{--                    <input type="hidden" name="description" value="Оплата заказа">--}}
{{--                    <input type="hidden" name="lang" value="ru">--}}
{{--                </form>--}}
            </div>
        </section>
    </main>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ===================================================================
            // ЛОГИКА ДЛЯ ПЕРЕКЛЮЧЕНИЯ АДРЕСА (остается без изменений)
            // ===================================================================
            const addressOptions = document.querySelectorAll('input[name="address_option"]');
            const newAddressForm = document.getElementById('newAddressForm');
            const newAddressInputs = newAddressForm ? newAddressForm.querySelectorAll('input') : [];

            function toggleNewAddressForm() {
                if (!newAddressForm) return;
                const newAddressRadio = document.getElementById('address_new');
                if (newAddressRadio && newAddressRadio.checked) {
                    newAddressForm.classList.remove('d-none');
                    newAddressInputs.forEach(input => input.required = true);
                } else {
                    newAddressForm.classList.add('d-none');
                    newAddressInputs.forEach(input => input.required = false);
                }
            }
            addressOptions.forEach(radio => radio.addEventListener('change', toggleNewAddressForm));
            toggleNewAddressForm();

            // ===================================================================
            // ИСПРАВЛЕННАЯ ОБРАБОТКА ОТПРАВКИ ФОРМЫ
            // ===================================================================
            const checkoutForm = document.getElementById('checkout-form');
            const placeOrderBtn = document.getElementById('place-order-btn');

            if (checkoutForm && placeOrderBtn) {
                checkoutForm.addEventListener('submit', function(event) {
                    const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;

                    placeOrderBtn.disabled = true;
                    placeOrderBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Обработка...';

                    if (paymentMethod !== 'card_online') {
                        return; // Разрешаем обычную отправку для наличных/терминала
                    }

                    // Логика только для онлайн-оплаты
                    event.preventDefault();

                    const formData = new FormData(checkoutForm);

                    fetch('{{ route('checkout.store') }}', {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: formData
                    })
                        .then(response => response.ok ? response.json() : response.json().then(e => Promise.reject(e)))
                        .then(data => {
                            if (data.success) {
                                // --- НОВАЯ ЛОГИКА ФОРМИРОВАНИЯ URL ---

                                // 1. Собираем параметры в массив
                                const params = {
                                    'm': '{{ config('payme.merchant_id') }}',
                                    'ac.order_id': data.order_id,
                                    'ac.user_id': data.user_id, // Добавляем user_id
                                    'a': data.amount,
                                    'l': 'ru', // Язык
                                    'c': '{{ route('order.success') }}' // URL для возврата после успешной оплаты
                                };

                                // 2. Превращаем объект в строку "key=value;key=value"
                                const paramString = Object.entries(params)
                                    .map(([key, value]) => `${key}=${value}`)
                                    .join(';');

                                // 3. Кодируем строку в base64
                                // Функция btoa() кодирует в base64
                                const base64Params = btoa(paramString);

                                // 4. Формируем финальный URL
                                const redirectUrl = `https://checkout.paycom.uz/${base64Params}`;

                                console.log('Сгенерированная строка:', paramString);
                                console.log('Закодированная строка:', base64Params);
                                console.log('Финальный URL для редиректа:', redirectUrl);

                                // 5. Перенаправляем пользователя
                                window.location.href = redirectUrl;

                            } else {
                                throw new Error(data.message || 'Произошла ошибка на сервере.');
                            }
                        })
                        .catch(error => {
                            // ... (код обработки ошибок остается таким же)
                            console.error('Произошла ошибка:', error);
                            let errorMessage = 'Не удалось создать заказ.';
                            if (error && error.errors) {
                                errorMessage = Object.values(error.errors).flat().join('\n');
                            } else if (error && error.message) {
                                errorMessage = error.message;
                            }
                            alert(errorMessage);
                            placeOrderBtn.disabled = false;
                            placeOrderBtn.innerHTML = 'Оформить заказ';
                        });
                });
            }
        });
    </script>
@endpush
