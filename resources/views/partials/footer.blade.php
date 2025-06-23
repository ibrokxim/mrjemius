<!-- footer -->
<footer class="footer">
    <div class="container">
        {{-- Основной блок с колонками --}}
        <div class="row g-4 py-4">

            {{-- КОЛОНКА 1: Компания --}}
            <div class="col-12 col-md-6 col-lg-2">
                <h6 class="mb-4">Компания</h6>
                <ul class="nav flex-column">
                    <li class="nav-item mb-2"><a href="{{-- route('about') --}}#!" class="nav-link">О компании</a></li>
                    <li class="nav-item mb-2"><a href="{{-- route('stores') --}}#!" class="nav-link">Точки продаж</a></li>
                    <li class="nav-item mb-2"><a href="{{-- route('promotions') --}}#!" class="nav-link">Акции</a></li>
                    <li class="nav-item mb-2"><a href="{{-- route('contact') --}}#!" class="nav-link">Контакты</a></li>
                </ul>
            </div>

            {{-- КОЛОНКА 2: Каталог --}}
            <div class="col-12 col-md-6 col-lg-2">
                <h6 class="mb-4">Каталог</h6>
                <ul class="nav flex-column">
                    {{-- Динамический вывод категорий --}}
                    @if(isset($categories) && $categories->isNotEmpty())
                        @foreach($categories as $category)
                            <li class="nav-item mb-2"><a href=" {{ route('category.show', $category->slug)}} " class="nav-link">{{ $category->name }}</a></li>
                        @endforeach
                    @else
                        {{-- Заглушки, если категории не переданы --}}
                        <li class="nav-item mb-2"><a href="#!" class="nav-link">Джемы</a></li>
                        <li class="nav-item mb-2"><a href="#!" class="nav-link">Соусы</a></li>
                        <li class="nav-item mb-2"><a href="#!" class="nav-link">Сиропы</a></li>
                        <li class="nav-item mb-2"><a href="#!" class="nav-link">Наборы</a></li>
                        <li class="nav-item mb-2"><a href="#!" class="nav-link">Распродажа</a></li>
                    @endif
                </ul>
            </div>

            {{-- КОЛОНКА 3: Информация --}}
            <div class="col-12 col-md-6 col-lg-2">
                <h6 class="mb-4">Информация</h6>
                <ul class="nav flex-column">
                    <li class="nav-item mb-2"><a href="{{-- route('page', 'how-to-order') --}}#!" class="nav-link">Как сделать заказ</a></li>
                    <li class="nav-item mb-2"><a href="{{-- route('page', 'payment') --}}#!" class="nav-link">Оплата</a></li>
                    <li class="nav-item mb-2"><a href="{{-- route('page', 'delivery') --}}#!" class="nav-link">Доставка</a></li>
                    <li class="nav-item mb-2"><a href="{{-- route('page', 'bonus-system') --}}#!" class="nav-link">Бонусная система</a></li>
                    <li class="nav-item mb-2"><a href="{{-- route('page', 'refund-policy') --}}#!" class="nav-link">Условия возврата</a></li>
                    <li class="nav-item mb-2"><a href="{{-- route('faq') --}}#!" class="nav-link">Вопросы и ответы</a></li>
                    <li class="nav-item mb-2"><a href="{{-- route('reviews') --}}#!" class="nav-link">Отзывы</a></li>
                    <li class="nav-item mb-2"><a href="{{-- route('wholesale') --}}#!" class="nav-link">Оптовикам</a></li>
                </ul>
            </div>

            {{-- КОЛОНКА 4: Цели --}}
            <div class="col-12 col-md-6 col-lg-2">
                <h6 class="mb-4">Цели</h6>
                <ul class="nav flex-column">
                    <li class="nav-item mb-2"><a href="#!" class="nav-link">Диетическое питание</a></li>
                    <li class="nav-item mb-2"><a href="#!" class="nav-link">Кето диета</a></li>
                    <li class="nav-item mb-2"><a href="#!" class="nav-link">Диета Дюкана</a></li>
                    <li class="nav-item mb-2"><a href="#!" class="nav-link">Продукты без глютена</a></li>
                    <li class="nav-item mb-2"><a href="#!" class="nav-link">ПП завтрак</a></li>
                    <li class="nav-item mb-2"><a href="#!" class="nav-link">Полезный перекус</a></li>
                    <li class="nav-item mb-2"><a href="#!" class="nav-link">Диабетическое питание</a></li>
                    <li class="nav-item mb-2"><a href="#!" class="nav-link">Веганские продукты</a></li>
                </ul>
            </div>

            {{-- КОЛОНКА 5: Контакты --}}
            <div class="col-12 col-md-12 col-lg-4">
                <h6 class="mb-4">Контакты</h6>
                <ul class="nav flex-column">
                    <li class="nav-item mb-3">
                        <a href="tel:+78005001743" class="nav-link p-0 fs-5 fw-bold text-dark">+7 (800) 500-17-43</a>
                    </li>
                    <li class="nav-item mb-3">
                        <a href="mailto:hello@mrdjemiuszero.com" class="nav-link p-0 text-muted">hello@mrdjemiuszero.com</a>
                    </li>
                    <li class="nav-item mb-3">
                        <span class="nav-link p-0 text-muted">г. Санкт-Петербург, Лиговский пр., д. 50, лит. О, корп. 1, офис 4</span>
                    </li>
                </ul>
                {{-- Иконки соцсетей --}}
                <ul class="list-inline">
                    <li class="list-inline-item me-1">
                        <a href="#!" class="btn btn-xs btn-social btn-icon">
                            {{-- SVG вашей иконки --}}
                            <i class="bi bi-facebook"></i>
                        </a>
                    </li>
                    <li class="list-inline-item me-1">
                        <a href="#!" class="btn btn-xs btn-social btn-icon">
                            <i class="bi bi-telegram"></i>
                        </a>
                    </li>
                    <li class="list-inline-item">
                        <a href="#!" class="btn btn-xs btn-social btn-icon">
                            <i class="bi bi-youtube"></i>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        {{-- Нижняя часть футера --}}
        <div class="border-top py-4">
            <div class="row align-items-center">
                <div class="col-md-6">
                     <span class="small text-muted">
                        ©
                        <span id="copyright">
                           <script>document.getElementById("copyright").appendChild(document.createTextNode(new Date().getFullYear()));</script>
                        </span>
                        Mr. Djemius. Все права защищены.
                     </span>
                </div>
                <div class="col-md-6">
                    <ul class="list-inline text-md-end mb-0 small mt-3 mt-md-0">
                        {{-- Ссылки на партнеров по оплате, если нужны --}}
                        <li class="list-inline-item text-dark">Способы оплаты</li>
                        <li class="list-inline-item">
                            <a href="#!"><img src="{{ asset('assets/images/payment/visa.svg') }}" alt="Visa" /></a>
                        </li>
                        <li class="list-inline-item">
                            <a href="#!"><img src="{{ asset('assets/images/payment/mastercard.svg') }}" alt="Mastercard" /></a>
                        </li>
                        <li class="list-inline-item">
                            <a href="#!"><img src="{{ asset('assets/images/payment/paypal.svg') }}" alt="PayPal" /></a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</footer>
