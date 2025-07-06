@extends('layouts.app')

@section('title', 'Спасибо за ваш заказ!')

@section('content')
    <main>
        <section class="my-lg-14 my-8">
            <div class="container">
                <div class="row">
                    <div class="col-12 text-center">

                        {{-- Иконка успеха --}}
                        <div class="mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" width="72" height="72" fill="currentColor" class="bi bi-check-circle-fill text-success" viewBox="0 0 16 16">
                                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                            </svg>
                        </div>

                        {{-- Основной текст --}}
                        <h1 class="display-5 fw-bold">{{__('Thank you')}}</h1>

                        {{-- Показываем сообщение из сессии, если оно есть --}}
                        @if (session('success'))
                            <p class="lead">{{ session('success') }}</p>
                        @endif

                        @if (session('order_number'))
                            <p class="mb-4">
                                {{__('Number of order')}} <strong class="text-dark">#{{ session('order_number') }}</strong>
                            </p>
                        @endif

                        <p class="mb-5">
                            {{__('Order comment')}}
                        </p>

                        {{-- Кнопка для возврата на главную --}}
                        <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                            <a href="{{ route('welcome') }}" class="btn btn-primary btn-lg px-4">{{__('Back to main')}}</a>
                            {{-- Можно добавить ссылку на личный кабинет --}}
                            {{-- <a href="{{ route('account.orders') }}" class="btn btn-outline-secondary btn-lg px-4">Мои заказы</a> --}}
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection
