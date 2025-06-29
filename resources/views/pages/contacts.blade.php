@extends('layouts.app')

@section('title', 'Контакты - Mr. Djemius Zero')

@section('content')
    <main>
        <div class="mt-4">
            <div class="container">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('welcome') }}">Главная</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Контакты</li>
                    </ol>
                </nav>
            </div>
        </div>

        <section class="my-lg-14 my-8">
            <div class="container">
                <div class="row">
                    <div class="col-12 mb-6">
                        <h1 class="mb-0">Контакты</h1>
                    </div>
                </div>

                <div class="row g-5">
                    {{-- Левая колонка с картой --}}
                    <div class="col-lg-7 col-12">
                        {{-- Элемент, в котором будет отображаться карта --}}
                        {{-- Задаем высоту, чтобы он не был "схлопнутым" --}}
                        <div id="yandex-map" style="width: 100%; height: 550px;" class="rounded-3"></div>
                    </div>

                    {{-- Правая колонка с информацией --}}
                    <div class="col-lg-5 col-12">
                        <div class="card border-0 bg-dark text-white p-4 p-lg-5 h-100 rounded-3">
                            <div class="card-body">
                                <h6 class="text-white-50">Центральный офис</h6>
                                <h4 class="mb-4">Ташкент, Мирабадский район, ул. Шахрисабз, 23
                                </h4>

                                <h6 class="text-white-50 mt-4">Режим работы</h6>
                                <p class="mb-1">Интернет-магазин: ежедневно с 9:00 до 21:00</p>
                                <p>Склад (самовывоз): ежедневно с 8:00 до 19:00</p>

                                <h6 class="text-white-50 mt-4">Телефон</h6>
                                <p class="mb-1"><a href="tel:+998771327700" class="text-white text-decoration-none"> +998 77 132 77 00</a></p>

                                <h6 class="text-white-50 mt-4">E-mail</h6>
                                <p><a href="mailto:mrdjemiuszero.uz@gmail.com" class="text-white text-decoration-none">mrdjemiuszero.uz@gmail.com</a></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection

@push('scripts')
    {{-- Подключаем API Яндекс.Карт. Замените YOUR_API_KEY на ваш ключ --}}
    <script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU&apikey=3f4fb90d-20b2-47b6-a147-6e1b90ae3e8b" type="text/javascript"></script>

    <script type="text/javascript">
        // Функция ymaps.ready() будет вызвана, когда загрузятся все компоненты API
        ymaps.ready(init);

        function init(){
            // Создание карты.
            var myMap = new ymaps.Map("yandex-map", {
                // Координаты центра карты.
                // Порядок: [широта, долгота].
                center: [59.924734, 30.360155], // Координаты для Лиговского пр., 50
                // Уровень масштабирования. Допустимые значения: от 0 (весь мир) до 19.
                zoom: 16,
                // Убираем лишние кнопки управления
                controls: ['zoomControl', 'fullscreenControl']
            });

            // Создаем метку для офиса
            var officePlacemark = new ymaps.Placemark([59.924734, 30.360155], {
                // balloonContent: '<strong>Mr. Djemius Zero</strong><br>Центральный офис'
            }, {
                // Опции.
                // Необходимо указать данный тип макета.
                iconLayout: 'default#image',
                // Своё изображение иконки метки.
                iconImageHref: '{{ asset('assets/images/map-pin.svg') }}', // Путь к вашей иконке метки
                // Размеры метки.
                iconImageSize: [30, 42],
                // Смещение левого верхнего угла иконки относительно её "ножки" (точки привязки).
                iconImageOffset: [-15, -42]
            });

            // Добавляем метку на карту
            myMap.geoObjects.add(officePlacemark);

            // Убираем возможность скролла карты колесиком мыши (улучшает UX)
            myMap.behaviors.disable('scrollZoom');
        }
    </script>
@endpush
