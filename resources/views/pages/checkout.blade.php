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
                                    <div class="col-md-12 mb-3">
                                        <label for="fullName" class="form-label">Полное имя<span class="text-danger">*</span></label>
                                        <input type="text" id="fullName" class="form-control" name="full_name" value="{{ old('full_name', auth()->user()->name) }}" required>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label for="phone" class="form-label">Телефон<span class="text-danger">*</span></label>
                                        <input type="text" id="phone" class="form-control" name="phone_number" value="{{ old('phone_number') }}" required placeholder="+998 XX XXX-XX-XX">
                                    </div>
                                </div>

                                {{-- 2. Способ получения --}}
                                <h2 class="h5 mb-4 mt-4">Способ получения</h2>
                                <div class="mb-3">
                                    <div class="form-check d-inline-block me-3">
                                        <input class="form-check-input" type="radio" name="delivery_method" id="deliveryMethod" value="delivery" @checked(old('delivery_method', 'delivery') == 'delivery')>
                                        <label class="form-check-label" for="deliveryMethod">Доставка курьером</label>
                                    </div>
                                    <div class="form-check d-inline-block">
                                        <input class="form-check-input" type="radio" name="delivery_method" id="pickupMethod" value="pickup" @checked(old('delivery_method') == 'pickup')>
                                        <label class="form-check-label" for="pickupMethod">Самовывоз</label>
                                    </div>
                                </div>
                                <div id="deliveryAddressSection">
                                {{-- 3. Адрес доставки (скрываемый блок) --}}
                                <h4 class="mb-3">Адрес доставки</h4>

                                {{-- 1. Блок с выбором существующих адресов --}}
                                @if($addresses->isNotEmpty())
                                    <div class="mb-4">
                                        <h5>Выберите сохраненный адрес:</h5>
                                        @foreach($addresses as $address)
                                            <div class="card mb-2">
                                                <div class="card-body p-3">
                                                    <div class="form-check">
                                                        {{-- Радиокнопка для выбора адреса --}}
                                                        <input class="form-check-input" type="radio" name="address_option"
                                                               id="address_{{ $address->id }}" value="{{ $address->id }}"
                                                            {{ $loop->first ? 'checked' : '' }}>

                                                        <label class="form-check-label w-100" for="address_{{ $address->id }}">
                                                            <span class="fw-bold">{{ $address->full_name }}, {{ $address->phone_number }}</span><br>
                                                            <span class="text-muted">{{ $address->address_line_1 }}, {{ $address->city }}</span>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                {{-- 2. Опция для добавления нового адреса --}}
                                <div class="card mb-4">
                                    <div class="card-body p-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="address_option" id="address_new" value="new"
                                                {{-- Если сохраненных адресов нет, эта опция будет выбрана по умолчанию --}}
                                                {{ $addresses->isEmpty() ? 'checked' : '' }}>
                                            <label class="form-check-label" for="address_new">
                                                Добавить новый адрес
                                            </label>
                                        </div>
                                    </div>
                                </div>


                                {{-- 3. Форма для ввода нового адреса (изначально может быть скрыта) --}}
                                {{-- Добавляем ID и класс, чтобы управлять видимостью через JS --}}
                                <div id="newAddressForm" class="{{ $addresses->isNotEmpty() ? 'd-none' : '' }}">
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label for="address_line_1_new" class="form-label">Адрес (улица, дом, квартира)</label>
                                            <input type="text" class="form-control" name="address_line_1" id="address_line_1_new" placeholder="ул. Амира Темура, 1">
                                        </div>

                                        <div class="col-12">
                                            <label for="city_new" class="form-label">Город</label>
                                            <input type="text" class="form-control" name="city" id="city_new" placeholder="Ташкент">
                                        </div>
                                    </div>
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
                                    <div class="d-grid mt-4">
                                        <button type="submit" id="place-order-btn" class="btn btn-primary btn-lg">Оформить заказ</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                {{-- Скрытая форма для перенаправления на Payme --}}
                <form id="payme-form" action="https://checkout.paycom.uz" method="POST" style="display: none;">
                    <input type="hidden" name="merchant" value="{{config('payme.merchant_id') }}">
                    <input type="hidden" name="amount" id="payme-amount">
                    <input type="hidden" name="account[order_id]" id="payme-order-id">
                    <input type="hidden" name="description" value="Оплата заказа">
                    <input type="hidden" name="lang" value="ru">
                </form>
            </div>
        </section>
    </main>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // ===================================================================
            // ОБРАБОТЧИКИ ДЛЯ ИНТЕРФЕЙСА ФОРМЫ
            // ===================================================================

            // --- Логика для скрытия/показа ВСЕГО блока адреса при смене способа получения ---
            const deliveryMethodOptions = document.querySelectorAll('input[name="delivery_method"]');
            const deliveryAddressSection = document.getElementById('deliveryAddressSection');

            function toggleDeliverySection() {
                const selectedMethod = document.querySelector('input[name="delivery_method"]:checked').value;
                if (selectedMethod === 'delivery') {
                    deliveryAddressSection.classList.remove('d-none');
                } else {
                    deliveryAddressSection.classList.add('d-none');
                }
            }

            deliveryMethodOptions.forEach(radio => {
                radio.addEventListener('change', toggleDeliverySection);
            });

            // --- Логика для скрытия/показа формы НОВОГО адреса ---
            const addressOptions = document.querySelectorAll('input[name="address_option"]');
            const newAddressForm = document.getElementById('newAddressForm');

            function toggleNewAddressForm() {
                const newAddressRadio = document.getElementById('address_new');
                if (newAddressRadio && newAddressRadio.checked) {
                    newAddressForm.classList.remove('d-none');
                } else {
                    newAddressForm.classList.add('d-none');
                }
            }

            addressOptions.forEach(radio => {
                radio.addEventListener('change', toggleNewAddressForm);
            });

            // --- Вызываем обе функции при загрузке страницы, чтобы установить правильное начальное состояние ---
            if (deliveryAddressSection) toggleDeliverySection();
            if (newAddressForm) toggleNewAddressForm();


            // ===================================================================
            // ОБРАБОТКА ОТПРАВКИ ФОРМЫ И ПЕРЕНАПРАВЛЕНИЯ НА PAYME
            // ===================================================================

            const checkoutForm = document.getElementById('checkout-form');
            const placeOrderBtn = document.getElementById('place-order-btn');

            if(checkoutForm && placeOrderBtn) {
                checkoutForm.addEventListener('submit', function(event) {
                    event.preventDefault(); // Всегда останавливаем стандартную отправку

                    const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;

                    // Блокируем кнопку, чтобы избежать двойных нажатий
                    placeOrderBtn.disabled = true;
                    placeOrderBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Обработка...';

                    if (paymentMethod === 'card_online') {
                        // --- ОПЛАТА КАРТОЙ (Payme) ---
                        const formData = new FormData(checkoutForm);

                        fetch('{{ route('checkout.store') }}', {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json', // Говорим серверу, что мы ожидаем JSON
                                // CSRF токен не нужен при использовании FormData в Laravel, но оставим для совместимости
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: formData
                        })
                            .then(response => {
                                // Важно: сначала проверяем, был ли ответ успешным
                                if (!response.ok) {
                                    // Если сервер вернул ошибку (например, 422 Unprocessable Entity из-за валидации)
                                    // то пытаемся прочитать JSON с ошибками
                                    return response.json().then(errorData => Promise.reject(errorData));
                                }
                                return response.json(); // Если все хорошо, читаем успешный JSON
                            })
                            .then(data => {
                                // Этот блок выполнится ТОЛЬКО если ответ был успешным (статус 2xx)
                                if (data.success) {
                                    // Заполняем скрытую форму Payme
                                    const paymeForm = document.getElementById('payme-form');
                                    document.getElementById('payme-amount').value = data.amount;
                                    document.getElementById('payme-order-id').value = data.order_id;

                                    // Отправляем пользователя на сайт Payme
                                    paymeForm.submit();
                                } else {
                                    // Это случай, когда сервер вернул 200 OK, но в JSON есть `success: false`
                                    throw new Error(data.message || 'Произошла неизвестная ошибка.');
                                }
                            })
                            .catch(error => {
                                // Этот блок ловит ВСЕ ошибки: и сетевые, и ошибки сервера (4xx, 5xx)
                                console.error('Ошибка при оформлении заказа:', error);
                                // Выводим сообщение об ошибке
                                let errorMessage = 'Не удалось создать заказ.';
                                if (error.message) {
                                    errorMessage = error.message;
                                }
                                if (error.errors) { // Если это ошибка валидации от Laravel
                                    errorMessage = Object.values(error.errors).join('\n');
                                }
                                alert(errorMessage);

                                // Разблокируем кнопку
                                placeOrderBtn.disabled = false;
                                placeOrderBtn.innerHTML = 'Оформить заказ';
                            });

                    } else {
                        // --- ОПЛАТА НАЛИЧНЫМИ ---
                        // Просто отправляем форму как обычно, сервер обработает и сделает редирект
                        checkoutForm.submit();
                    }
                });
            }
        });
    </script>
@endpush
