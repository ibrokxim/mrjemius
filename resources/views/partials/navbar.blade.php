<!-- navbar -->
<div class="border-bottom">
    {{-- Верхняя полоса (Top bar) --}}
    <div class="bg-light py-2">
        <div class="container">
            <div class="d-flex justify-content-center align-items-center">

                <!-- 1. ССЫЛКА НА TELEGRAM С ТЕКСТОМ ДЛЯ ДЕСКТОПА -->
                <a href="https://t.me/mrdjemiuszerouz"
                   class="btn btn-sm d-flex align-items-center text-decoration-none me-3"
                   style="border-color: #FF569F; color: #FF569F;"
                   target="_blank">

                    <!-- Иконка, видна всегда -->
                    <i class="bi bi-telegram"></i>

                    <!-- Текст, виден только на экранах md и больше -->
                    <span class="d-none d-md-inline ms-2">Написать в Telegram</span>
                </a>

                <!-- 2. Номер телефона -->
                <div class="me-4">
                    <a href="tel:+998771327700" class="text-dark fw-semibold text-decoration-none">
                        <i class="bi bi-telephone-fill me-1" style="color: #FF569F"></i>
                        +998 77 132 77 00
                    </a>
                </div>

                <!-- 3. Кнопка "Заказать звонок" -->
                <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#contactModal">
                    {{__('form')}}
                </button>

            </div>
        </div>
    </div>

    {{-- Основной блок хедера --}}
    <div class="py-4">
        <div class="container">
            <div class="row w-100 align-items-center gx-lg-2 gx-0">

                {{-- Логотип --}}
                <div class="col-xxl-3 col-lg-3 col-md-6 col-5">
                    <a class="navbar-brand d-none d-lg-block" href="{{ route('welcome') }}">
                        <img style="width: 170px; height: auto;" src="{{ asset('assets/images/logo/logo.png') }}" alt="Mr. Djemius Zero" />
                    </a>
                    <div class="d-flex justify-content-between w-100 d-lg-none">
                        <a class="navbar-brand" href="{{ route('welcome') }}">
                            <img style="width: 170px; height: auto;" src="{{ asset('assets/images/logo/logo.png') }}" alt="Mr. Djemius Zero" />
                        </a>
                    </div>
                </div>

                {{-- Поиск (только на десктопе) --}}
                <div class="col-xxl-5 col-lg-5 d-none d-lg-block">
                    <form action="{{ route('search.products') }}" method="GET">
                        <div class="input-group">
                            <input class="form-control rounded" name="query" type="search" placeholder="{{__('Search')}}" value="{{ request('query') }}" required />
                            <button class="btn bg-white border border-start-0 ms-n10 rounded-0 rounded-end" type="submit" aria-label="Поиск">
                                <i class="feather-icon icon-search"></i>
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Правая часть: Языки и иконки --}}
                <div class="col-xxl-4 col-lg-4 text-end col-md-6 col-7">
                    <div class="list-inline d-flex justify-content-end align-items-center">

                        {{-- Переключатель языков (только на десктопе) --}}
                        <div class="list-inline-item me-4 d-none d-lg-block">
                            @php
                                $available_locales = [
                                    'ru' => [
                                        'name' => 'Русский',
                                        'flag' => '<svg width="16" height="13" viewBox="0 0 16 13" fill="none" xmlns="http://www.w3.org/2000/svg"><g clip-path="url(#clip_ru)"><path d="M0 0.5H16V12.5H0V0.5Z" fill="white"></path><path d="M0 4.5H16V12.5H0V4.5Z" fill="#0039A6"></path><path d="M0 8.5H16V12.5H0V8.5Z" fill="#D52B1E"></path></g><defs><clipPath id="clip_ru"><rect width="16" height="12" fill="white" transform="translate(0 0.5)"></rect></clipPath></defs></svg>'
                                    ],
                                    'uz' => [
                                        'name' => 'Oʻzbekcha',
                                        'flag' => '<svg width="16" height="13" viewBox="0 0 16 13" fill="none" xmlns="http://www.w3.org/2000/svg"><g clip-path="url(#clip0_1_2)"><path d="M0 0.5H16V12.5H0V0.5Z" fill="#1EB53A"/><path d="M0 0.5H16V8.5H0V0.5Z" fill="white"/><path d="M0 0.5H16V4.5H0V0.5Z" fill="#0099B5"/><circle cx="2.5" cy="2.5" r="1.5" fill="white"/></g><defs><clipPath id="clip0_1_2"><rect width="16" height="12" fill="white" transform="translate(0 0.5)"/></clipPath></defs></svg>'
                                    ]
                                ];
                                $current_locale_code = app()->getLocale();
                                $current_locale_data = $available_locales[$current_locale_code] ?? $available_locales['ru'];
                            @endphp
                            <div class="dropdown selectBox">
                                <a class="dropdown-toggle selectValue text-reset" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                                    <span class="me-2">{!! $current_locale_data['flag'] !!}</span>
                                    {{ $current_locale_data['name'] }}
                                </a>
                                <ul class="dropdown-menu">
                                    @foreach ($available_locales as $locale_code => $locale_data)
                                        @if ($locale_code !== $current_locale_code)
                                            <li>
                                                <a class="dropdown-item" href="{{ route('language.switch', $locale_code) }}">
                                                    <span class="me-2">{!! $locale_data['flag'] !!}</span>
                                                    {{ $locale_data['name'] }}
                                                </a>
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>
                        </div>

                        {{-- Иконка "Войти" --}}
                        <div class="list-inline-item me-3">
                            <a href="#!" class="text-muted d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#userModal" title="Войти">
                                <i class="feather-icon icon-user fs-3 me-1" style="font-size: 20px;"></i>
                            </a>
                        </div>

                        {{-- Иконка "Избранное" --}}
                        <div class="list-inline-item me-3">
                            <a href="{{ auth()->check() ? route('wishlist.index') : '#' }}"
                               class="text-muted d-flex align-items-center position-relative"
                               @guest data-bs-toggle="modal" data-bs-target="#userModal" @endguest
                               title="Избранное">
                                <i class="feather-icon icon-heart fs-3 me-1" style="font-size: 20px;"></i>
                                @auth
                                    @if(Auth::user()->wishlistProducts()->count() > 0)
                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-success" id="wishlist-counter">
                                            {{ Auth::user()->wishlistProducts()->count() }}
                                        </span>
                                    @endif
                                @endauth
                            </a>
                        </div>

                        {{-- Иконка "Корзина" --}}
                        <div class="list-inline-item me-3 me-lg-0">
                            <a href="{{ route('cart.index') }}" class="text-muted d-flex align-items-center position-relative" title="Корзина">
                                <i class="feather-icon icon-shopping-bag fs-3 me-1"></i>
                                @auth
                                    @php
                                        $cartItemCount = Auth::user()->cartItems()->sum('quantity');
                                    @endphp
                                    <span id="cart-count"
                                          class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-success"
                                          style="{{ $cartItemCount > 0 ? '' : 'display: none;' }}">
                                             {{ $cartItemCount }}
                                      </span>
                                @else
                                    <span id="cart-count" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-success" style="display: none;">0</span>
                                @endauth
                            </a>
                        </div>

                        {{-- Бургер-меню (только на мобильных) --}}
                        <div class="list-inline-item d-inline-block d-lg-none">
                            <button class="navbar-toggler collapsed p-0" type="button" data-bs-toggle="offcanvas" data-bs-target="#navbar-default" aria-controls="navbar-default" aria-label="Toggle navigation">
                                <i class="bi bi-list " style="font-size: 2rem;"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Нижняя навигация --}}
    <nav class="navbar navbar-expand-lg navbar-light navbar-default py-0 pb-lg-4" aria-label="Offcanvas navbar large">
        <div class="container">

            {{-- Меню для десктопа --}}
            <div class="d-none d-lg-flex justify-content-between w-100" >
                <style>
                    /* Стиль для скрытия стрелки */
                    .dropdown-toggle.no-arrow::after {
                        display: none !important;
                    }

                    /* Стили для кнопки "Категории" */
                    .btn-categories {
                        background-color: #FF569F !important; /* Основной цвет */
                        border-color: #FF569F !important;     /* Цвет рамки */
                    }

                    /* Стили для состояний наведения, фокуса и клика */
                    .btn-categories:hover,
                    .btn-categories:focus,
                    .btn-categories:active {
                        background-color: #e64d8f !important; /* Делаем цвет чуть темнее при наведении */
                        border-color: #e64d8f !important;
                        box-shadow: 0 0 0 0.2rem rgba(255, 86, 159, 0.5) !important;
                    }
                </style>
                <ul class="navbar-nav align-items-center">
                    <li class="nav-item dropdown">
                        <a href="#" class="btn btn-primary px-6 dropdown-toggle no-arrow"  role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="me-1"
                                width="18"
                                height="18"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="1.5"
                                stroke-linecap="round"
                                stroke-linejoin="round">
                                <rect x="3" y="3" width="7" height="7"></rect>
                                <rect x="14" y="3" width="7" height="7"></rect>
                                <rect x="14" y="14" width="7" height="7"></rect>
                                <rect x="3" y="14" width="7" height="7"></rect>
                            </svg>
                             {{__('Categories')}}
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                            @isset($categories)
                                @foreach($categories as $cat)
                                    <li><a class="dropdown-item"  href="{{ route('category.show', $cat->slug) }}">{{ $cat->name }}</a></li>
                                @endforeach
                            @endisset
                        </ul>
                    </li>
                    <li class="nav-item"> <a class="nav-link ms-2" href="{{ route('welcome') }}#products-section">{{__('Products')}}</a> </li>
                    <li class="nav-item"> <a class="nav-link" href="{{ route('blog.index') }}">{{__('Blog')}}</a> </li>
                    <li class="nav-item"> <a class="nav-link" href="https://www.grechkafood.uz/">{{__('PP')}}</a> </li>
                    <li class="nav-item"> <a class="nav-link" href="{{ route('about') }}">{{__('About')}}</a> </li>
                    <li class="nav-item"> <a class="nav-link" href="{{ route('contacts') }}">{{__('Contacts')}}</a> </li>
                </ul>
            </div>

            {{-- Offcanvas меню для мобильных --}}
            <div class="offcanvas offcanvas-start d-lg-none" tabindex="-1" id="navbar-default" aria-labelledby="navbar-defaultLabel">
                <div class="offcanvas-header pb-1">
                    <a href="{{route('welcome')}}"><img src="{{ asset('assets/images/logo/logo.png')}}" style="height: 40px; width: auto;" alt="Mr. Djemius Zero"/></a>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body">

                    {{-- Поиск (1) --}}
                    <div class="d-block d-lg-none mb-4">
                        <form action="{{ route('search.products') }}" method="GET">
                            <div class="input-group">
                                <input class="form-control rounded" name="query" type="search" placeholder="{{__('Search')}}" value="{{ request('query') }}" required />
                                <span class="input-group-append">
                    <button class="btn bg-white border border-start-0 ms-n10 rounded-0 rounded-end" type="submit">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                             stroke-linejoin="round" class="feather feather-search">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                    </button>
                </span>
                            </div>
                        </form>
                    </div>

                    <div class="d-block d-lg-none mb-4">
                        <div class="dropdown w-100">
                            <button class="btn btn-outline-secondary dropdown-toggle w-100 d-flex justify-content-between align-items-center"
                                    type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <span class="d-flex align-items-center">
                    <span class="me-2">{!! $current_locale_data['flag'] !!}</span>
                    {{ $current_locale_data['name'] }}
                </span>
                            </button>
                            <ul class="dropdown-menu w-100">
                                @foreach ($available_locales as $locale_code => $locale_data)
                                    @if ($locale_code !== $current_locale_code)
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center" href="{{ route('language.switch', $locale_code) }}">
                                                <span class="me-2">{!! $locale_data['flag'] !!}</span>
                                                {{ $locale_data['name'] }}
                                            </a>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    {{-- Категории (3) --}}
                    <div class="d-block d-lg-none mb-4">
                        <a class="btn btn-primary w-100 d-flex justify-content-center align-items-center btn-categories" data-bs-toggle="collapse"
                           href="#collapseCategories" role="button" aria-expanded="false" aria-controls="collapseCategories">
            <span class="me-2">
                <i class="feather-icon icon-grid"></i>
            </span>
                            {{ __('Categories') }}
                        </a>
                        <div class="collapse mt-2" id="collapseCategories">
                            <div class="card card-body p-2">
                                @foreach($categories as $cat)
                                    <a class="dropdown-item" href="{{ route('category.show', $cat->slug) }}">{{ $cat->name }}</a>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Основные ссылки (4) --}}
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('welcome') }}#products-section">{{ __('Products') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('blog.index') }}">{{ __('Blog') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="https://www.grechkafood.uz/">{{ __('PP') }}</a>
                        </li><li class="nav-item">
                            <a class="nav-link" href="{{route('about')}}">{{__('About') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('contacts') }}">{{ __('Contacts') }}</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>
</div>

