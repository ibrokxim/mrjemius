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
                {{-- Товары корзины будут загружаться через JavaScript --}}
            </ul>
            
            <!-- Сообщение о пустой корзине -->
            <div id="cartEmptyMessage" class="text-center py-4" style="display: none;">
                <p class="text-muted mb-0">Ваша корзина пуста</p>
            </div>
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
