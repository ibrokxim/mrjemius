document.addEventListener('DOMContentLoaded', function() {
    const addToCartButtons = document.querySelectorAll('.add-to-cart-btn');
    const moveToCartButton = document.getElementById('moveAllToCartBtn');
    const offcanvasCart = document.getElementById('offcanvasRight'); // Получаем элемент offcanvas
    const cartItemsList = document.getElementById('cartItemsList');
    const cartSubtotalEl = document.getElementById('cartSubtotal');
    const cartCountNavEl = document.getElementById('cart-count-nav'); // Предполагаем, что есть элемент для счетчика в навигации
    const cartEmptyMessage = document.getElementById('cartEmptyMessage');
    const cartDeliveryMessage = document.getElementById('cartDeliveryMessage'); // Для сообщения о доставке
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // --- Вспомогательные функции ---
    function showAlert(type, message) {
        // ... ваш код showAlert ... (оставляем без изменений)
        const alertContainer = document.createElement('div');
        alertContainer.className = `alert alert-${type} alert-dismissible fade show m-3`; // Добавил отступы
        alertContainer.style.position = 'fixed'; // Чтобы было поверх всего
        alertContainer.style.top = '20px';
        alertContainer.style.right = '20px';
        alertContainer.style.zIndex = '1055'; // Больше чем у offcanvas
        alertContainer.role = "alert";
        alertContainer.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Закрыть"></button>
        `;
        document.body.appendChild(alertContainer); // Добавляем в body

        setTimeout(() => {
            bootstrap.Alert.getOrCreateInstance(alertContainer).close(); // Используем Bootstrap для закрытия
        }, 5000);
    }

    // --- Функции для работы с корзиной ---

    // Загрузка и отрисовка всей корзины
    async function loadAndRenderCart() {
        if (!cartItemsList) return; // Если элемента списка нет на странице

        try {
            const response = await fetch("{{ route('cart.data') }}", { // Используем именованный роут Laravel
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const data = await response.json();

            renderCart(data);

        } catch (error) {
            console.error('Ошибка при загрузке корзины:', error);
            showAlert('danger', 'Не удалось загрузить данные корзины.');
        }
    }

    // Отрисовка данных корзины
    function renderCart(cartData) {
        if (!cartItemsList || !cartSubtotalEl || !cartCountNavEl || !cartEmptyMessage) return;

        cartItemsList.innerHTML = ''; // Очищаем текущий список

        if (cartData.items && cartData.items.length > 0) {
            cartEmptyMessage.style.display = 'none';
            cartData.items.forEach(item => {
                const li = document.createElement('li');
                li.className = 'list-group-item py-3 ps-0';
                // Используем более детальный HTML для каждого элемента
                li.innerHTML = `
                    <div class="row align-items-center">
                        <div class="col-3 col-md-2">
                            <img src="${item.image}" alt="${item.name}" class="img-fluid">
                        </div>
                        <div class="col-4 col-md-5">
                            <a href="/product/${item.slug}" class="text-inherit">
                                <h6 class="mb-0 fs-sm">${item.name}</h6>
                            </a>
                            <span><small class="text-muted">${item.price} x ${item.quantity}</small></span>
                        </div>
                        <div class="col-3 col-md-3 text-end text-md-start">
                            <input type="number" class="form-control form-control-sm cart-item-quantity"
                                   value="${item.quantity}" min="1" max="100"
                                   data-item-id="${item.id}" style="width: 60px; display: inline-block;">
                        </div>
                        <div class="col-2 text-end">
                             <button class="btn btn-link text-danger p-0 cart-item-remove" data-item-id="${item.id}">
                                <i class="bi bi-trash"></i>
                             </button>
                        </div>
                    </div>`;
                cartItemsList.appendChild(li);
            });
        } else {
            cartEmptyMessage.style.display = 'block';
        }

        cartSubtotalEl.textContent = `${cartData.formatted_total}`;
        cartCountNavEl.textContent = cartData.count || 0; // Обновляем счетчик в навигации

        // Логика для сообщения о доставке (пример)
        if (cartData.total > 0 && cartData.total < 500000) { // Пример порога для бесплатной доставки
            cartDeliveryMessage.innerHTML = `До бесплатной доставки осталось ${500000 - cartData.total} сумов.`;
            cartDeliveryMessage.style.display = 'block';
            cartDeliveryMessage.classList.remove('alert-success');
            cartDeliveryMessage.classList.add('alert-warning');
        } else if (cartData.total >= 500000) {
            cartDeliveryMessage.innerHTML = `У вас БЕСПЛАТНАЯ доставка! <a href="{{ route('checkout') }}" class="alert-link">Оформить заказ</a>`;
            cartDeliveryMessage.style.display = 'block';
            cartDeliveryMessage.classList.remove('alert-warning');
            cartDeliveryMessage.classList.add('alert-success');
        } else {
            cartDeliveryMessage.style.display = 'none';
        }

        // Повторно навешиваем обработчики на новые элементы (обновление, удаление)
        attachCartItemEventListeners();
    }

    // Добавление товара
    async function addToCart(productId, quantity = 1) {
        try {
            const response = await fetch(`{{ route('cart.add', ['product' => '__PRODUCT_ID__']) }}`.replace('__PRODUCT_ID__', productId), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ quantity: quantity })
            });
            const data = await response.json();
            if (response.ok && data.success) {
                showAlert('success', data.message);
                await loadAndRenderCart(); // Перезагружаем и отрисовываем всю корзину
                // Открыть offcanvas корзины, если он есть
                if (offcanvasCart && bootstrap.Offcanvas.getInstance(offcanvasCart)) {
                    bootstrap.Offcanvas.getInstance(offcanvasCart).show();
                } else if (offcanvasCart) {
                    new bootstrap.Offcanvas(offcanvasCart).show();
                }

            } else {
                showAlert('danger', data.message || 'Не удалось добавить товар.');
            }
        } catch (error) {
            console.error('Ошибка при добавлении в корзину:', error);
            showAlert('danger', 'Ошибка при добавлении в корзину.');
        }
    }

    // Обновление количества
    async function updateCartItem(itemId, quantity) {
        try {
            const response = await fetch(`{{ route('cart.update', ['cartItem' => '__ITEM_ID__']) }}`.replace('__ITEM_ID__', itemId), {
                method: 'PATCH', // или PUT
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ quantity: quantity })
            });
            const data = await response.json();
            if (response.ok && data.success) {
                showAlert('success', data.message);
                await loadAndRenderCart(); // Перезагружаем всю корзину
            } else {
                showAlert('danger', data.message || 'Не удалось обновить количество.');
                await loadAndRenderCart(); // Перезагружаем, чтобы вернуть старое значение если сервер отклонил
            }
        } catch (error) {
            console.error('Ошибка при обновлении корзины:', error);
            showAlert('danger', 'Ошибка при обновлении корзины.');
        }
    }

    // Удаление товара
    async function removeCartItem(itemId) {
        try {
            const response = await fetch(`{{ route('cart.remove', ['cartItem' => '__ITEM_ID__']) }}`.replace('__ITEM_ID__', itemId), {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });
            const data = await response.json();
            if (response.ok && data.success) {
                showAlert('success', data.message);
                await loadAndRenderCart(); // Перезагружаем всю корзину
            } else {
                showAlert('danger', data.message || 'Не удалось удалить товар.');
            }
        } catch (error) {
            console.error('Ошибка при удалении из корзины:', error);
            showAlert('danger', 'Ошибка при удалении из корзины.');
        }
    }

    // Перенос из избранного
    async function moveWishlistToCart() {
        try {
            const response = await fetch("{{ route('cart.moveFromWishlist') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            });
            const data = await response.json();
            if (response.ok && data.success) {
                showAlert('success', data.message);
                await loadAndRenderCart();
                // Обновляем страницу списка желаний, чтобы показать изменения
                if (window.location.pathname.includes('/wishlist')) {
                    window.location.reload();
                }
            } else {
                showAlert('danger', data.message || 'Не удалось переместить товары.');
            }
        } catch (error) {
            console.error('Ошибка при переносе из избранного в корзину:', error);
            showAlert('danger', 'Ошибка при переносе из избранного.');
        }
    }

    // Навешивание обработчиков на элементы корзины (вызывать после каждой перерисовки)
    function attachCartItemEventListeners() {
        document.querySelectorAll('.cart-item-quantity').forEach(input => {
            input.addEventListener('change', function() {
                const itemId = this.dataset.itemId;
                const quantity = parseInt(this.value, 10);
                if (quantity >= 1) {
                    updateCartItem(itemId, quantity);
                } else {
                    this.value = 1; // Возвращаем к минимуму, если введено некорректное значение
                }
            });
        });

        document.querySelectorAll('.cart-item-remove').forEach(button => {
            button.addEventListener('click', function() {
                const itemId = this.dataset.itemId;
                if (confirm('Удалить этот товар из корзины?')) {
                    removeCartItem(itemId);
                }
            });
        });
    }


    // --- Инициализация и основные обработчики ---

    // Кнопки "Добавить в корзину" на страницах
    addToCartButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const productId = this.dataset.productId;
            // Для карточек товара может понадобиться получить количество из input рядом
            const quantityInput = this.closest('.card-product-action') ? null : this.closest('form')?.querySelector('input[name="quantity"]');
            const quantity = quantityInput ? parseInt(quantityInput.value) : 1;
            addToCart(productId, quantity);
        });
    });

    // Кнопка "Все в корзину" из избранного
    if (moveToCartButton) {
        moveToCartButton.addEventListener('click', moveWishlistToCart);
    }

    // Загрузка корзины при открытии offcanvas
    if (offcanvasCart) {
        offcanvasCart.addEventListener('show.bs.offcanvas', async function () {
            await loadAndRenderCart();
        });
    }

    // Начальная загрузка данных корзины (например, для счетчика в шапке)
    // Эту функцию можно вызвать один раз при загрузке страницы, если счетчик и сумма видны сразу
    async function initialCartLoad() {
        // Только если есть элементы для обновления на странице, кроме offcanvas
        if (cartCountNavEl || cartSubtotalEl /* ... другие элементы ... */) {
            await loadAndRenderCart(); // или более легковесный запрос только на count/total
        }
    }
    initialCartLoad(); // Вызываем при загрузке страницы

});
