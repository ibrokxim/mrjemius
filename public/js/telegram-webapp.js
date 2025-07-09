document.addEventListener('DOMContentLoaded', function() {
    // Проверяем, что мы в Telegram Web App
    if (!window.Telegram || !window.Telegram.WebApp) {
        console.log('Not in Telegram Web App');
        return;
    }

    const tg = window.Telegram.WebApp;

    // Инициализируем Web App
    tg.ready();

    // Получаем данные пользователя
    const user = tg.initDataUnsafe.user;

    console.log('Telegram User:', user);

    // Проверяем авторизацию (эта переменная должна быть передана из blade)
    if (user && !window.isAuthenticated) {
        authenticateUser(tg.initData);
    }

    // Настраиваем UI
    setupTelegramUI();

    function authenticateUser(initData) {
        fetch('/api/telegram-auth', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                initData: initData
            })
        }).then(response => {
            if (response.ok) {
                location.reload();
            }
        }).catch(error => {
            console.error('Auth error:', error);
        });
    }

    function setupTelegramUI() {
        // Настраиваем тему
        document.body.style.backgroundColor = tg.themeParams.bg_color || '#ffffff';
        document.body.style.color = tg.themeParams.text_color || '#000000';

        // Настраиваем главную кнопку
        tg.MainButton.setText('Оформить заказ');
        tg.MainButton.color = tg.themeParams.button_color || '#0088cc';
        tg.MainButton.textColor = tg.themeParams.button_text_color || '#ffffff';
        tg.MainButton.show();

        tg.MainButton.onClick(function() {
            submitOrder();
        });
    }

    function submitOrder() {
        // Показываем прогресс
        tg.MainButton.showProgress();

        // Собираем данные формы
        const formData = new FormData(document.getElementById('checkout-form'));

        fetch('/api/checkout/submit', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData
        }).then(response => {
            tg.MainButton.hideProgress();

            if (response.ok) {
                tg.showPopup({
                    title: 'Успех!',
                    message: 'Заказ успешно оформлен',
                    buttons: [{
                        type: 'ok'
                    }]
                }, function() {
                    tg.close();
                });
            } else {
                tg.showAlert('Ошибка при оформлении заказа');
            }
        }).catch(error => {
            tg.MainButton.hideProgress();
            tg.showAlert('Ошибка сети');
            console.error('Order error:', error);
        });
    }
});
