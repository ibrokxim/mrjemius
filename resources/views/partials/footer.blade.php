<!-- footer -->
<footer class="footer bg-dark text-white">
    <div class="container">
        <div class="row g-4 py-5">
            {{-- Логотип и описание --}}
            <div class="col-12 col-lg-4">
                <div class="mb-4">
                    <h4 class="text-white mb-3">Mr. Djemius Zero</h4>
                    <p class="text-white-50 mb-4">{{__('health')}}</p>
                </div>

                {{-- Социальные сети --}}
                <div class="mb-4">
                    <h6 class="text-white mb-3">{{__('social_links')}}</h6>
                    <div class="d-flex gap-3">
                        <a href="https://t.me/mrdjemiuszerouz" class="btn btn-outline-light btn-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="bi bi-telegram"></i>
                        </a>
                        <a href="https://www.instagram.com/mr.djemiuszero.uz" class="btn btn-outline-light btn-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="bi bi-instagram"></i>
                        </a>
                        <a href="" class="btn btn-outline-light btn-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="bi bi-facebook"></i>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Пустая колонка для выравнивания --}}
            <div class="col-12 col-lg-2 d-none d-lg-block"></div>

            {{-- Быстрые ссылки --}}
            <div class="col-12 col-md-4 col-lg-2">
                <h6 class="text-white mb-4">{{__('menu')}}</h6>
                <ul class="nav flex-column">
                    <li class="nav-item mb-2">
                        <a href="{{ route('welcome') }}#products-section" class="nav-link p-0 text-white-50 hover-text-white">{{__('Products')}}</a>
                    </li>
                    <li class="nav-item mb-2">
                        <a href="{{ route('blog.index') }}" class="nav-link p-0 text-white-50 hover-text-white">{{__('Blog')}}</a>
                    </li>
                    <li class="nav-item mb-2">
                        <a href="https://www.grechkafood.uz/" class="nav-link p-0 text-white-50 hover-text-white">{{__('PP')}}</a>
                    </li>
                    <li class="nav-item mb-2">
                        <a href="{{ route('about') }}" class="nav-link p-0 text-white-50 hover-text-white">{{__('About')}}</a>
                    </li>
                    <li class="nav-item mb-2">
                        <a href="{{ route('contacts') }}" class="nav-link p-0 text-white-50 hover-text-white">{{__('Contacts')}}</a>
                    </li>
                </ul>
            </div>

            {{-- Каталог --}}
            <div class="col-12 col-md-4 col-lg-2">
                <h6 class="text-white mb-4">{{__('catalog')}}</h6>
                <ul class="nav flex-column">
                    @foreach($categories as $cat)
                        <li class="nav-item mb-2">
                            <a class="nav-link p-0 text-white-50 hover-text-white" href="{{ route('category.show', $cat->slug) }}">{{ $cat->name }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- Контакты --}}
            <div class="col-12 col-md-4 col-lg-2">
                <h6 class="text-white mb-4">{{__('Contacts')}}</h6>
                <div class="mb-3">
                    <div class="d-flex align-items-center mb-2">
                        <i class="bi bi-telephone-fill text-white-50 me-2"></i>
                        <a href="tel:+998771327700" class="text-white text-decoration-none fw-bold">+998 77 132 77 00</a>
                    </div>
                    <div class="d-flex align-items-center mb-2">
                        <i class="bi bi-envelope-fill text-white-50 me-2"></i>
                        <a href="mailto:mrdjemiuszero.uz@gmail.com" class="text-white-50 text-decoration-none">mrdjemiuszero.uz@gmail.com</a>
                    </div>
                    {{--                    <div class="d-flex align-items-start">--}}
                    {{--                        <i class="bi bi-geo-alt-fill text-white-50 me-2 mt-1"></i>--}}
                    {{--                        <span class="text-white-50">Узбекистан, г. Ташкент</span>--}}
                    {{--                    </div>--}}
                </div>

                <div class="text-white-50 small">
                    <strong>{{__('rejim')}}</strong><br>
                    {{__('time')}}
                </div>
            </div>
        </div>

        {{-- Нижняя часть футера --}}
        <div class="border-top border-secondary py-4">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <span class="text-white-50 small">
                        ©
                        <span id="copyright">
                           <script>document.getElementById("copyright").appendChild(document.createTextNode(new Date().getFullYear()));</script>
                        </span>
                        Mr. Djemius Zero. {{__('rights')}}
                    </span>
                </div>
                <div class="col-md-6">
                    <div class="d-flex justify-content-md-end align-items-center mt-3 mt-md-0">
                        <span class="text-white-50 small me-3">{{__('payment_methods')}}</span>
                        <div class="d-flex gap-2">
                            <li class="list-inline-item">
                                <a href="#!"><img src="{{ asset('assets/images/payment/uzcardd.png') }}" alt="Uzcard" width="25" height="25" /></a>
                            </li>
                            <li class="list-inline-item">
                                <a href="#!"><img src="{{ asset('assets/images/payment/payme.svg') }}" alt="Payme" width="50" height="30" /></a>
                            </li>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>

<style>
    .hover-text-white:hover {
        color: white !important;
        transition: color 0.3s ease;
    }

    .footer .nav-link:hover {
        transform: translateX(5px);
        transition: transform 0.3s ease;
    }

    .footer .btn-outline-light:hover {
        background-color: rgba(255, 255, 255, 0.1);
        border-color: white;
        transform: translateY(-2px);
        transition: all 0.3s ease;
    }
</style>
