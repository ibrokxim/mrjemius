@extends('layouts.app')

@section('title', 'О компании - Mr. Djemius Zero')

@section('content')
    <main>
        <section class="mt-4 mb-lg-14 mb-8">
            <div class="container">
                {{-- Хлебные крошки и заголовок --}}
                <div class="row">
                    <div class="col-12">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-5">
                                <li class="breadcrumb-item"><a href="{{ route('welcome') }}">{{__('main')}}</a></li>
                                <li class="breadcrumb-item active" aria-current="page">{{__('About')}}</li>
                            </ol>
                        </nav>

                    </div>
                </div>

                <div class="row justify-content-center">
                    <div class="col-lg-10 col-md-12">
                        <div class="mb-5">
                            <h1 class="fw-bold">{{__('About')}}</h1>
                        </div>
                        <div class="mb-5">
                            <img src="{{ asset('assets/images/about/company_banner.jpg') }}" alt="Продукция Mr. Djemius" class="img-fluid rounded-3 w-100">
                        </div>
                        <div>
                            {{-- Первый абзац --}}
                            <p class="mb-4">
                                <strong>{{__('title')}}</strong> {{__('first_abz')}}
                            </p>
                            {{-- Второй абзац --}}
                            <p class="mb-4">
                                {{__('second_abz')}}
                            </p>
                            <p class="mb-4">
                                {{__('third_abz')}}
                            </p>

                            {{-- Третий абзац с подзаголовком --}}
{{--                            <h3 class="mb-3 mt-5">Глобальная миссия</h3>--}}
{{--                            <p class="mb-4">--}}
{{--                                Глобальная миссия компании Mr. Djemius – оздоровление нации. Наши цели: уменьшить процент заболевания ожирением и процент людей с избыточной массой тела, сделать жизнь людей с сахарным диабетом ярче и вкуснее, разнообразить рацион всех, кто следит за своим здоровьем и фигурой.--}}
{{--                            </p>--}}

{{--                            --}}{{-- Четвертый абзац с подзаголовком --}}
{{--                            <h3 class="mb-3 mt-5">Наша команда</h3>--}}
{{--                            <p class="mb-4">--}}
{{--                                Сегодня Mr. Djemius – это команда единомышленников, которые верят в свой продукт и несут здоровье в массы. Присоединяйтесь, станьте частью нашей глобальной миссии и помогите нам сделать нацию здоровой, а людей – стройными, сильными и счастливыми!--}}
{{--                            </p>--}}
                        </div>

                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection
