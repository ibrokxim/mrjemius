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

                <form action="{{ route('checkout.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-lg-7 col-md-12">
                            <div class="card card-body p-6">
                                <h2 class="h5 mb-4">Контактная информация</h2>
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label for="fullName" class="form-label">Полное имя<span class="text-danger">*</span></label>
                                        <input type="text" id="fullName" class="form-control" name="full_name" value="{{ old('full_name', auth()->user()->name) }}" required>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label for="phone" class="form-label">Телефон<span class="text-danger">*</span></label>
                                        <input type="text" id="phone" class="form-control" name="phone_number" value="{{ old('phone_number') }}" required>
                                    </div>
                                </div>

                                <h2 class="h5 mb-4 mt-4">Адрес доставки</h2>
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label for="address" class="form-label">Адрес (улица, дом, квартира)<span class="text-danger">*</span></label>
                                        <input type="text" id="address" class="form-control" name="address_line_1" value="{{ old('address_line_1') }}" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="city" class="form-label">Город<span class="text-danger">*</span></label>
                                        <input type="text" id="city" class="form-control" name="city" value="{{ old('city') }}" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="postal_code" class="form-label">Почтовый индекс<span class="text-danger">*</span></label>
                                        <input type="text" id="postal_code" class="form-control" name="postal_code" value="{{ old('postal_code') }}" required>
                                    </div>
                                </div>

                                <h2 class="h5 mb-4 mt-4">Способ оплаты</h2>
                                <div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_method" id="paymentCash" value="cash" checked>
                                        <label class="form-check-label" for="paymentCash">Наличными при получении</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="payment_method" id="paymentCard" value="card_online">
                                        <label class="form-check-label" for="paymentCard">Картой онлайн (Payme, Click)</label>
                                    </div>
                                </div>

                                <h2 class="h5 mb-4 mt-4">Примечание к заказу (необязательно)</h2>
                                <textarea class="form-control" name="customer_notes" rows="3" placeholder="Оставьте комментарий к вашему заказу..."></textarea>
                            </div>
                        </div>

                        <div class="col-lg-5 col-md-12">
                            <div class="card mt-4 mt-lg-0">
                                <div class="card-body p-6">
                                    <h2 class="h5 mb-4">Ваш заказ</h2>

                                    @foreach($cartItems as $item)
                                        <div class="d-flex mb-3">
                                            <div class="flex-shrink-0">
                                                <img src="{{ $item->product->primaryImage ? asset('storage/' . $item->product->primaryImage->image_url) : '' }}" class="icon-shape icon-lg" alt="">
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h5 class="fs-6">{{ $item->product->name }}</h5>
                                                <p class="mb-0">Кол-во: {{ $item->quantity }}</p>
                                            </div>
                                            <div>
                                                <span>{{ number_format(($item->product->sell_price ?? $item->product->price) * $item->quantity, 0,'.', ' ') }} сум</span>
                                            </div>
                                        </div>
                                    @endforeach

                                    <div class="mt-4 pt-4 border-top">
                                        <div class="d-flex justify-content-between">
                                            <span>Промежуточный итог:</span>
                                            <span>{{ number_format($cartSummary['subtotal'], 0,'.', ' ') }} сум</span>
                                        </div>
                                        <div class="d-flex justify-content-between fw-bold mt-2">
                                            <span>Итого:</span>
                                            <span>{{ number_format($cartSummary['total'], 0,'.', ' ') }} сум</span>
                                        </div>
                                    </div>

                                    <div class="d-grid mt-4">
                                        <button type="submit" class="btn btn-primary btn-lg">Оформить заказ</button>
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
