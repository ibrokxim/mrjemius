<!-- footer -->
<footer class="footer">
    <div class="container">
        <div class="row g-4 py-4">

            {{-- 1. КОЛОНКА КОНТАКТОВ (видна всегда) --}}
            <div class="col-12 col-lg-3">
                <h6 class="mb-4">Контакты</h6>
                <ul class="nav flex-column">
                    <li class="nav-item mb-3">
                        <a href="tel:+998901884748" class="nav-link p-0 fs-5 fw-bold text-dark">+998 90 188 47 48</a>
                    </li>
                    <li class="nav-item mb-3">
                        <a href="mailto:mrdjemiuszero.uz@gmail.com" class="nav-link p-0 text-muted">mrdjemiuszero.uz@gmail.com</a>
                    </li>
                </ul>
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
                        <a href="#!" class="btn btn-xs btn-social btn-icon" aria-label="Instagram">
                            {{-- Замените SVG ниже на свой кастомный --}}
                            <i class="bi-instagram"></i>
                        </a>
                    </li>
                </ul>
            </div>

            {{-- Обертка для остальных колонок --}}
            <div class="col-12 col-lg-9">
                <div class="row">
                    {{-- КОЛОНКА "Продукты" --}}
                    <div class="col-12 col-md-4 col-lg-3  mb-4 mb-md-0">
                        {{-- Заголовок для десктопа --}}
                        <h6 class="mb-4 d-none d-md-block">Продукты</h6>
                        {{-- Заголовок-кнопка для мобильных --}}
                        <a class="h6 text-decoration-none d-md-none" data-bs-toggle="collapse" href="#footerProducts" role="button" aria-expanded="false" aria-controls="footerProducts">
                            Продукты <i class="bi bi-chevron-down"></i>
                        </a>
                        {{-- Сворачивающийся список --}}
                        <div class="collapse d-md-block" id="footerProducts">
                            <ul class="nav flex-column mt-md-0 mt-3">
                                <li class="nav-item mb-2"><a href="#!" class="nav-link">О компании</a></li>
                                <li class="nav-item mb-2"><a href="#!" class="nav-link">Точки продаж</a></li>
                                <li class="nav-item mb-2"><a href="#!" class="nav-link">Акции</a></li>
                                <li class="nav-item mb-2"><a href="#!" class="nav-link">Контакты</a></li>
                            </ul>
                        </div>
                    </div>

                    {{-- КОЛОНКА "Каталог" --}}
                    <div class="col-12 col-md-4 col-lg-3  mb-4 mb-md-0">
                        <h6 class="mb-4 d-none d-md-block">Каталог</h6>
                        <a class="h6 text-decoration-none d-md-none" data-bs-toggle="collapse" href="#footerCatalog" role="button" aria-expanded="false" aria-controls="footerCatalog">
                            Каталог <i class="bi bi-chevron-down"></i>
                        </a>
                        <div class="collapse d-md-block" id="footerCatalog">
                            <ul class="nav flex-column mt-md-0 mt-3">
                                @if(isset($categories) && $categories->isNotEmpty())
                                    @foreach($categories as $category)
                                        <li class="nav-item mb-2"><a href="{{ route('category.show', $category->slug)}}" class="nav-link">{{ $category->name }}</a></li>
                                    @endforeach
                                @endif
                            </ul>
                        </div>
                    </div>

                    {{-- КОЛОНКА "Полезные советы" --}}
                    <div class="col-12 col-md-4 col-lg-3  mb-4 mb-md-0">
                        <h6 class="mb-4 d-none d-md-block">Полезные советы</h6>
                        <a class="h6 text-decoration-none d-md-none" data-bs-toggle="collapse" href="#footerHelp" role="button" aria-expanded="false" aria-controls="footerHelp">
                            Полезные советы <i class="bi bi-chevron-down"></i>
                        </a>
                        <div class="collapse d-md-block" id="footerHelp">
                            <ul class="nav flex-column mt-md-0 mt-3">
                                <li class="nav-item mb-2"><a href="#!" class="nav-link">Как сделать заказ</a></li>
                                <li class="nav-item mb-2"><a href="#!" class="nav-link">Оплата</a></li>
                                <li class="nav-item mb-2"><a href="#!" class="nav-link">Доставка</a></li>
                                <li class="nav-item mb-2"><a href="#!" class="nav-link">Бонусная система</a></li>
                                <li class="nav-item mb-2"><a href="#!" class="nav-link">Условия возврата</a></li>
                                <li class="nav-item mb-2"><a href="#!" class="nav-link">Вопросы и ответы</a></li>
                                <li class="nav-item mb-2"><a href="#!" class="nav-link">Отзывы</a></li>
                                <li class="nav-item mb-2"><a href="#!" class="nav-link">Оптовикам</a></li>
                            </ul>
                        </div>
                    </div>

                    {{-- КОЛОНКА "Правильное питание" --}}
                    <div class="col-12 col-md-4 col-lg-3  mb-4 mb-md-0">
                        <h6 class="mb-4 d-none d-md-block">Правильное питание</h6>
                        <a class="h6 text-decoration-none d-md-none" data-bs-toggle="collapse" href="#footerNutrition" role="button" aria-expanded="false" aria-controls="footerNutrition">
                            Правильное питание <i class="bi bi-chevron-down"></i>
                        </a>
                        <div class="collapse d-md-block" id="footerNutrition">
                            <ul class="nav flex-column mt-md-0 mt-3">
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
                    </div>
                </div>
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
                            <a href="#!"><img src="{{ asset('assets/images/payment/uzcardd.png') }}" alt="Uzcard" width="20" height="20" /></a>
                        </li>
                        <li class="list-inline-item">
                            <a href="#!"><img src="{{ asset('assets/images/payment/payme.svg') }}" alt="Payme" width="50" height="30" /></a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</footer>
