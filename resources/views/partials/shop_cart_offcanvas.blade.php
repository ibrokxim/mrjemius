<!-- Боковая панель: Корзина -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel">
    <div class="offcanvas-header border-bottom">
        <div class="text-start">
            <h5 id="offcanvasRightLabel" class="mb-0 fs-4">Корзина</h5>
{{--            <small>Местоположение: <span id="cartLocation">Ваш город</span></small> --}}{{-- Динамически --}}
        </div>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Закрыть"></button>
    </div>
    <div class="offcanvas-body">
        <div>
            {{-- Это сообщение будет динамическим --}}
            <div class="alert alert-danger p-2" role="alert" id="cartDeliveryMessage" style="display: none;">
                У вас БЕСПЛАТНАЯ доставка. Начните
                <a href="{{-- {{ route('checkout') }} --}}#!" class="alert-link">оформление заказа!</a>
            </div>

            <ul class="list-group list-group-flush" id="cartItemsList">
                {{-- Сюда будут добавляться товары из корзины через JavaScript --}}
                {{-- Пример одного товара (шаблон для JS) --}}
                {{--
                <li class="list-group-item py-3 ps-0 border-top">
                    <div class="row align-items-center">
                        <div class="col-6 col-md-6 col-lg-7">
                            <div class="d-flex">
                                <img src="ПУТЬ_К_ИЗОБРАЖЕНИЮ" alt="Название товара" class="icon-shape icon-xxl" />
                                <div class="ms-3">
                                    <a href="ПУТЬ_К_ТОВАРУ" class="text-inherit">
                                        <h6 class="mb-0">Название товара</h6>
                                    </a>
                                    <span><small class="text-muted">_АТРИБУТЫ_/_ВЕС_</small></span>
                                    <div class="mt-2 small lh-1">
                                        <a href="#!" class="text-decoration-none text-inherit remove-from-cart-btn" data-id="ID_ТОВАРА">
                                            <span class="me-1 align-text-bottom">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2 text-success">
                                                    <polyline points="3 6 5 6 21 6"></polyline>
                                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                    <line x1="10" y1="11" x2="10" y2="17"></line>
                                                    <line x1="14" y1="11" x2="14" y2="17"></line>
                                                </svg>
                                            </span>
                                            <span class="text-muted">Удалить</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-4 col-md-3 col-lg-3">
                            <div class="input-group input-spinner">
                                <input type="button" value="-" class="button-minus btn btn-sm" data-field="quantity" />
                                <input type="number" step="1" max="10" value="1" name="quantity" class="quantity-field form-control-sm form-input" />
                                <input type="button" value="+" class="button-plus btn btn-sm" data-field="quantity" />
                            </div>
                        </div>
                        <div class="col-2 text-lg-end text-start text-md-end col-md-2">
                            <span class="fw-bold">_ЦЕНА_ руб.</span>
                        </div>
                    </div>
                </li>
                --}}
                <li class="list-group-item py-3 ps-0" id="cartEmptyMessage">
                    <p class="text-center text-muted">Ваша корзина пуста.</p>
                </li>
            </ul>
            <!-- Итоги корзины (будут обновляться через JS) -->
            <div class="mt-4 d-flex justify-content-between">
                <div><strong>Промежуточный итог:</strong></div>
                <div><strong id="cartSubtotal">0.00 сумов.</strong></div>
            </div>
            <div class="d-grid mt-4"> {{-- Используем d-grid для кнопок на всю ширину --}}
                <a href="{{-- {{ route('checkout') }} --}}#!" class="btn btn-primary mb-2">Перейти к оформлению</a>
                <a href="{{-- {{ route('cart.view') }} --}}#!" class="btn btn-dark">Просмотреть корзину</a>
            </div>
        </div>
    </div>
</div>
