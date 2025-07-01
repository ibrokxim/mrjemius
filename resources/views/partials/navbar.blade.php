<!-- navbar -->
<div class="border-bottom">
    <div class="bg-light py-1">
        <div class="container">
            <div class="row">
                <div class="col-md-6 col-12 text-center text-md-start"><span>Супер скидки - Экономьте больше с купонами</span></div>
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
                    $current_locale_data = $available_locales[$current_locale_code];
                @endphp
                <div class="col-6 text-end d-none d-md-block">
                    <div class="dropdown selectBox">
                        <a class="dropdown-toggle selectValue text-reset" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                       <span class="me-2">
                            {!! $current_locale_data['flag'] !!}
                       </span>
                            {{ $current_locale_data['name'] }}
                        </a>

                        <ul class="dropdown-menu">
                            @foreach ($available_locales as $locale_code => $locale_data)
                                @if ($locale_code !== $current_locale_code)
                                    <li>
                                        <a class="dropdown-item" href="{{ route('language.switch', $locale_code) }}">
                                         <span class="me-2">
                                            {!! $locale_data['flag'] !!}
                                         </span>
                                            {{ $locale_data['name'] }}
                                        </a>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="py-5">
        <div class="container">
            <div class="row w-100 align-items-center gx-lg-2 gx-0">
                <div class="col-xxl-2 col-lg-3 col-md-6 col-5">
                    <a class="navbar-brand d-none d-lg-block" href="{{route('welcome')}}">
                        <img style="width: 170px; height: 40px;" src="{{ asset('assets/images/logo/logo.png')}}" alt="eCommerce HTML Template" />
                    </a>
                    <div class="d-flex justify-content-between w-100 d-lg-none">
                        <a class="navbar-brand" href="{{route('welcome')}}">
                            <img  style="width: 170px; height: 40px;" src="{{ asset('assets/images/logo/logo.png')}}" alt="eCommerce HTML Template" />
                        </a>
                    </div>
                </div>
                <div class="col-xxl-5 col-lg-5 d-none d-lg-block">
                    <form action="{{ route('search.products') }}" method="GET">
                        <div class="input-group">
                            <input class="form-control rounded" name="query" type="search" placeholder="Поиск по продуктам"  value="{{ request('query') }}" required/>
                            <span class="input-group-append">
                              <button class="btn bg-white border border-start-0 ms-n10 rounded-0 rounded-end" type="submit">
                                 <svg
                                     xmlns="http://www.w3.org/2000/svg"
                                     width="16"
                                     height="16"
                                     viewBox="0 0 24 24"
                                     fill="none"
                                     stroke="currentColor"
                                     stroke-width="2"
                                     stroke-linecap="round"
                                     stroke-linejoin="round"
                                     class="feather feather-search">
                                    <circle cx="11" cy="11" r="8"></circle>
                                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                 </svg>
                              </button>
                           </span>
                        </div>
                    </form>
                </div>
                <div class="col-md-2 col-xxl-3 d-none d-lg-block">
                    <!-- Button trigger modal -->
                </div>
                <div class="col-lg-2 col-xxl-2 text-end col-md-6 col-7">
                    <div class="list-inline">

                        <div class="list-inline-item me-5">
                            <a href="#!" class="text-muted" data-bs-toggle="modal" data-bs-target="#userModal">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="20"
                                    height="20"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="feather feather-user">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                    <circle cx="12" cy="7" r="4"></circle>
                                </svg>
                            </a>
                        </div>
                        <div class="list-inline-item me-5">
                            @auth
                                <a href="{{ route('wishlist.index') }}" class="text-muted position-relative" title="Ваши избранные">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        width="20"
                                        height="20"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        class="feather feather-heart">
                                        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                                    </svg>
                                    @if(Auth::user()->wishlistProducts()->count() > 0)
                                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-success" id="wishlist-counter">
                                             {{ Auth::user()->wishlistProducts()->count() }}
                                        </span>
                                    @endif
                                </a>
                            @else
                                <button type="button" class="btn btn-link text-muted p-0" data-bs-toggle="modal" data-bs-target="#userModal" title="Войдите через Telegram, чтобы использовать избранное">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        width="20"
                                        height="20"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        class="feather feather-heart">
                                        <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                                    </svg>
                                </button>
                            @endauth
                        </div>
                        <div class="list-inline-item me-5 me-lg-0">
                            <a class="text-muted position-relative"  data-bs-target="#offcanvasRight" href="{{ route('cart.index') }}"  aria-controls="offcanvasRight">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="20"
                                    height="20"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="feather feather-shopping-bag">
                                    <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                                    <line x1="3" y1="6" x2="21" y2="6"></line>
                                    <path d="M16 10a4 4 0 0 1-8 0"></path>
                                </svg>
                                @if(auth()->check() && auth()->user()->cartProducts()->count() > 0)
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-success" id="wishlist-counter">
                                        {{ auth()->user()->cartProducts()->count() }}
                                    </span>
                                @endif
                            </a>
                        </div>
                        <div class="list-inline-item d-inline-block d-lg-none">
                            <button
                                class="navbar-toggler collapsed"
                                type="button"
                                data-bs-toggle="offcanvas"
                                data-bs-target="#navbar-default"
                                aria-controls="navbar-default"
                                aria-label="Toggle navigation">
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-text-indent-left text-primary" viewBox="0 0 16 16">
                                    <path
                                        d="M2 3.5a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5zm.646 2.146a.5.5 0 0 1 .708 0l2 2a.5.5 0 0 1 0 .708l-2 2a.5.5 0 0 1-.708-.708L4.293 8 2.646 6.354a.5.5 0 0 1 0-.708zM7 6.5a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1h-6a.5.5 0 0 1-.5-.5zm0 3a.5.5 0 0 1 .5-.5h6a.5.5 0 0 1 0 1h-6a.5.5 0 0 1-.5-.5zm-5 3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <nav class="navbar navbar-expand-lg navbar-light navbar-default py-0 pb-lg-4" aria-label="Offcanvas navbar large">
        <div class="container">
            <div class="offcanvas offcanvas-start" tabindex="-1" id="navbar-default" aria-labelledby="navbar-defaultLabel">
                <div class="offcanvas-header pb-1">
                    <a href="{{route('welcome')}}"><img src="{{ asset('assets/images/logo/logo.png')}}" style="height: 40px; width: auto;" alt=""/> </a>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body">
                    <!-- Добавляем переключатель языка для мобилки -->
                    <div class="d-block d-lg-none mb-4">
                        <div class="dropdown w-100">
                            <button class="btn btn-outline-secondary dropdown-toggle w-100 d-flex justify-content-between align-items-center" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <span class="d-flex align-items-center">
                                    <span class="me-2">
                                        {!! $current_locale_data['flag'] !!}
                                    </span>
                                    {{ $current_locale_data['name'] }}
                                </span>
                            </button>
                            <ul class="dropdown-menu w-100">
                                @foreach ($available_locales as $locale_code => $locale_data)
                                    @if ($locale_code !== $current_locale_code)
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center" href="{{ route('language.switch', $locale_code) }}">
                                                <span class="me-2">
                                                    {!! $locale_data['flag'] !!}
                                                </span>
                                                {{ $locale_data['name'] }}
                                            </a>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    <div class="d-block d-lg-none mb-4">
                        <form action="{{ route('search.products') }}" method="GET">
                            <div class="input-group">
                                <input class="form-control rounded" name="query" type="search" placeholder="Поиск по продуктам" value="{{ request('query') }}" required/>
                                <span class="input-group-append">
                                 <button class="btn bg-white border border-start-0 ms-n10 rounded-0 rounded-end" type="submit">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        width="16"
                                        height="16"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        class="feather feather-search">
                                       <circle cx="11" cy="11" r="8"></circle>
                                       <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                    </svg>
                                 </button>
                              </span>
                            </div>
                        </form>
                    </div>
                    <div class="d-block d-lg-none mb-4">
                        <a
                            class="btn btn-primary w-100 d-flex justify-content-center align-items-center"
                            data-bs-toggle="collapse"
                            href="#collapseExample"
                            role="button"
                            aria-expanded="false"
                            aria-controls="collapseExample">
                           <span class="me-2">
                              <svg
                                  xmlns="http://www.w3.org/2000/svg"
                                  width="16"
                                  height="16"
                                  viewBox="0 0 24 24"
                                  fill="none"
                                  stroke="currentColor"
                                  stroke-width="1.5"
                                  stroke-linecap="round"
                                  stroke-linejoin="round"
                                  class="feather feather-grid">
                                 <rect x="3" y="3" width="7" height="7"></rect>
                                 <rect x="14" y="3" width="7" height="7"></rect>
                                 <rect x="14" y="14" width="7" height="7"></rect>
                                 <rect x="3" y="14" width="7" height="7"></rect>
                              </svg>
                           </span>
                            {{__('Categories')}}
                        </a>
                        <div class="collapse mt-2" id="collapseExample">
                            <div class="card card-body">
                                @foreach($categories as $cat)
                                    <li><a class="dropdown-item" href="{{route('category.show', $cat->slug)}}">{{$cat->name}}</a></li>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="dropdown me-3 d-none d-lg-block"  >
                        <button class="btn btn-primary px-6" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                           <span class="me-1">
                              <svg
                                  xmlns="http://www.w3.org/2000/svg"
                                  width="16"
                                  height="16"
                                  viewBox="0 0 24 24"
                                  fill="none"
                                  stroke="currentColor"
                                  stroke-width="1.2"
                                  stroke-linecap="round"
                                  stroke-linejoin="round"
                                  class="feather feather-grid">
                                 <rect x="3" y="3" width="7" height="7"></rect>
                                 <rect x="14" y="3" width="7" height="7"></rect>
                                 <rect x="14" y="14" width="7" height="7"></rect>
                                 <rect x="3" y="14" width="7" height="7"></rect>
                              </svg>
                           </span>
                            {{__('Categories')}}
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                            @foreach($categories as $cat)
                                <li><a class="dropdown-item" href="{{route('category.show', $cat->slug)}}">{{$cat->name}}</a></li>
                            @endforeach
                        </ul>
                    </div>
                    <div>
                        <ul class="navbar-nav align-items-center">
                            <li class="nav-item dropdown w-100 w-lg-auto">
                                <a class="nav-link " href="{{ route('welcome') }}#products-section" role="button"  aria-expanded="false">{{__('Products')}}</a>
                            </li>
                            <li class="nav-item dropdown w-100 w-lg-auto">
                                <a class="nav-link " href="{{ route('blog.index') }}" role="button"  aria-expanded="false">{{__('Blog')}}</a>
                            </li>
                            <li class="nav-item dropdown w-100 w-lg-auto">
                                <a class="nav-link " href="https://www.grechkafood.uz/" role="button"  aria-expanded="false">{{__('PP')}}</a>
                            </li>
                            <li class="nav-item dropdown w-100 w-lg-auto">
                                <a class="nav-link " href="{{route('contacts')}}" role="button"  aria-expanded="false">{{__('Contacts')}}</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</div>
