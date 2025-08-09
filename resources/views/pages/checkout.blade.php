@extends('layouts.app')

@section('title', 'Оформление заказа')
@push('style')
    <style>
        .date-select-btn.active {
            background-color: #FF569F; /* Ваш розовый цвет */
            color: white;
            border-color: #FF569F;
        }

        /* Дополнительно, чтобы текст даты тоже стал белым */
        .date-select-btn.active small {
            color: white !important;
        }

        .btn-group .btn small {
            display: block;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
    </style>
@endpush

@section('content')
    <main>
        <div class="mt-4">
            <div class="container">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('welcome') }}">{{__('main')}}</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('cart.index') }}">{{__('cart')}}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{__('order')}}</li>
                    </ol>
                </nav>
            </div>
        </div>

        <section class="mb-lg-14 mb-8 mt-8">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div>
                            <h1 class="fw-bold">{{__('order')}}</h1>
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
                                <h2 class="h5 mb-4">{{__('contact_info')}}</h2>
                                <div class="row">
                                    <input type="hidden" name="delivery_method" value="{{ $deliveryMethod }}">
                                    <div class="col-md-12 mb-3">
                                        <label for="fullName" class="form-label">{{__('full_name')}}<span class="text-danger">*</span></label>
                                        <input type="text" id="fullName" class="form-control" name="full_name" value="{{ old('full_name', auth()->user()->name) }}" required>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label for="phone" class="form-label">{{__('phone')}}<span class="text-danger">*</span></label>
                                        <input type="text" id="phone" class="form-control" name="phone_number" value="{{ old('phone_number', $latestAddress->phone_number ?? '') }}" required>
                                    </div>
                                </div>

                                @if($deliveryMethod === 'delivery')
                                    <div id="deliveryAddressSection">
                                        <h2 class="h5 mb-4 mt-4">{{__('address')}}</h2>

                                        {{-- Выбор существующего адреса --}}
                                        @if($addresses->isNotEmpty())
                                            <div class="mb-4">
                                                <h5>{{__('save_address')}}</h5>
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
                                                    <label class="form-check-label" for="address_new">{{__('new_address')}}</label>
                                                </div></div></div>

                                        {{-- Форма нового адреса --}}
                                        <div id="newAddressForm" class="{{ $addresses->isNotEmpty() ? 'd-none' : '' }}">
                                            <div class="row g-3">
                                                <div class="col-12"><label for="address_line_1_new" class="form-label">{{__('address_line')}}<span class="text-danger">*</span></label><input type="text" class="form-control" name="address_line_1" id="address_line_1_new"></div>
                                                <div class="col-12"><label for="city_new" class="form-label">{{__('city')}}<span class="text-danger">*</span></label><input type="text" class="form-control" name="city" id="city_new" value="Ташкент"></div>
                                            </div>
                                        </div>
                                    </div>

                                @endif
                                <div class="mb-4">
                                    <h2 class="h5 mb-3 mt-4">{{__('delivery_date')}}</h2>

                                    {{-- Группа кнопок, которая будет вести себя как радио-кнопки --}}
                                    <div class="d-flex flex-wrap gap-2">
                                        <input type="hidden" name="delivered_at" id="delivery_date_input" value="{{ $deliveryDates['today'] }}">
                                        {{-- Кнопка 1: Сегодня --}}
                                        <div class="flex-fill">
                                            <button type="button" class="btn btn-outline-primary w-100 date-select-btn active" data-date="{{ $deliveryDates['today'] }}">
                                                <div>{{__('today')}}</div>
                                                <small>{{ now()->locale(app()->getLocale())->isoFormat('D MMMM') }}</small>
                                            </button>
                                        </div>

                                        <div class="flex-fill">
                                            <button type="button" class="btn btn-outline-primary w-100 date-select-btn" data-date="{{ $deliveryDates['tomorrow'] }}">
                                                <div>{{__('tomorrow')}}</div>
                                                <small>{{ now()->addDay()->locale(app()->getLocale())->isoFormat('D MMMM') }}</small>
                                            </button>
                                        </div>

                                        <div class="flex-fill">
                                            <button type="button" class="btn btn-outline-primary w-100 date-select-btn" data-date="{{ $deliveryDates['day_after'] }}">
                                                <div>{{__('after_tommorrow')}}</div>
                                                <small>{{ now()->addDays(2)->locale(app()->getLocale())->isoFormat('D MMMM') }}</small>
                                            </button>
                                        </div>

                                    </div>

                                    {{-- Текст с временем доставки под кнопками --}}
                                    <div class="text-muted mt-2">
                                        <label class="form-check-label">{{__('delivery_time')}} 18:00-22:00</label>
                                    </div>
                                </div>

                                {{-- 4. Способ оплаты --}}
                                <h2 class="h5 mb-4 mt-4">{{__('payment_methods')}}</h2>
                                <div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_method" id="paymentCash" value="cash" @checked(old('payment_method', 'cash') == 'cash')>
                                        <label class="form-check-label" for="paymentCash">{{__('cash')}}</label>
                                    </div>

                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_method" id="paymentCard" value="card_online" @checked(old('payment_method') == 'card_online')>
                                        <label class="form-check-label" for="paymentCard">{{__('payme')}}</label>
                                    </div>
                                </div>

                                {{-- 5. Примечание --}}
                                <h2 class="h5 mb-4 mt-4">{{__('note')}}</h2>
                                <textarea class="form-control" name="customer_notes" rows="3" placeholder="{{__('komment')}}">{{ old('customer_notes') }}</textarea>
                            </div>
                        </div>

                        <div class="col-lg-5 col-md-12">
                            <div class="card mt-4 mt-lg-0">
                                <div class="card-body p-6">
                                    <h2 class="h5 mb-4">{{__('your_order')}}</h2>

                                    {{-- БЛОК С ТОВАРАМИ --}}
                                    <ul class="list-group list-group-flush">
                                        @foreach ($cartItems as $item)
                                            <li class="list-group-item py-3">
                                                <div class="d-flex align-items-center">
                                                    <img src="{{ $item->product->primaryImage ? asset('storage/' . $item->product->primaryImage->image_url) : asset('assets/images/placeholder.png') }}" alt="{{ $item->product->name }}" class="rounded" style="width: 60px;">
                                                    <div class="ms-3 flex-grow-1">
                                                        <h6 class="mb-0">{{ $item->product->name }}</h6>
                                                        <span><small>{{ $item->quantity }} x {{ number_format($item->product->sell_price ?? $item->product->price, 0, '.', ' ') }} {{__('sum')}}</small></span>
                                                    </div>
                                                    <span class="fw-bold">{{ number_format(($item->product->sell_price ?? $item->product->price) * $item->quantity, 0, '.', ' ') }} {{__('sum')}}</span>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>

                                    <hr class="my-4">

                                    {{-- БЛОК С ИТОГАМИ --}}
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>{{__('order_sum')}}</span>
                                        <span class="fw-bold">{{ number_format($cartSummary['subtotal'], 0, '.', ' ') }} {{__('sum')}}</span>
                                    </div>
                                    {{-- Этот блок вставляется в pages/checkout.blade.php --}}
                                    <div class="d-flex flex-column mb-3">
                                        {{-- Верхняя строка с основной информацией --}}
                                        <div class="d-flex justify-content-between w-100">
                                            <span>{{__('Delivery in city')}}</span>
                                            <span class="fw-bold">
                                                 @if($cartSummary['shipping'] > 0)
                                                    {{ number_format($cartSummary['shipping'], 0, '.', ' ') }} {{__('sum')}}
                                                @else
                                                    <span class="text-success">{{__('free')}}</span>
                                                @endif
                                            </span>
                                        </div>

                                        {{-- Нижняя строка с примечанием (теперь без выравнивания по правому краю) --}}
                                        <small class="text-muted mt-1">{{__('delivery_sum')}}</small>
                                    </div>
                                    <div class="d-flex justify-content-between fw-bold fs-5">
                                        <span>{{__('final_price')}} </span>
                                        <span>{{ number_format($cartSummary['total'], 0, '.', ' ') }} {{__('sum')}}</span>
                                    </div>

                                    <div class="d-grid mt-4">
                                        <button type="submit" id="place-order-btn" class="btn btn-primary btn-lg ">
                                            {{__('final')}}</button>
                                    </div>
                                    <div class="mt-3 text-center">
                                        <small class="text-muted">
                                            {{__('checkout agreement')}}<br>
                                            <a href="{{ route('terms.show') }}" target="_blank">
                                                {{ __('terms_title') }}
                                            </a>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </section>
    </main>
@endsection

@push('scripts')
    <script>
        document.querySelectorAll('.date-select-btn').forEach(button => {
            button.addEventListener('click', function() {
                // Убираем класс 'active' у всех кнопок
                document.querySelectorAll('.date-select-btn').forEach(btn => btn.classList.remove('active'));
                // Добавляем класс 'active' нажатой кнопке
                this.classList.add('active');
                // Устанавливаем значение скрытого инпута
                document.getElementById('delivery_date_input').value = this.dataset.date;
            });
        });
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
                                // Вместо window.location.href = redirectUrl;
                                window.location.href = `{{ route('payme.redirect') }}?url=${encodeURIComponent(redirectUrl)}`;


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
