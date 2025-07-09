<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Переход к оплате...</title>
    <script src="https://telegram.org/js/telegram-web-app.js"></script>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; background-color: #f0f2f5; text-align: center; }
        .container { padding: 20px; }
        .spinner { border: 4px solid rgba(0,0,0,.1); width: 36px; height: 36px; border-radius: 50%; border-left-color: #09f; animation: spin 1s ease infinite; margin: 0 auto 20px; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    </style>
</head>
<body>
<div class="container">
    <div class="spinner"></div>
    <p>Перенаправляем вас на страницу оплаты...</p>
</div>

<script>
    // Инициализируем Telegram Web App
    Telegram.WebApp.ready();

    // Данные, которые мы передали из PHP
    const orderId = "{{ $order_id }}";
    const amount = "{{ $amount }}";
    const userId = "{{ $user_id }}";
    const merchantId = "{{ $merchant_id }}";
    const successUrl = "{{ $success_url }}";

    // 1. Собираем параметры в массив
    const params = {
        'm': merchantId,
        'ac.order_id': orderId,
        'ac.user_id': userId,
        'a': amount,
        'l': 'ru',
        'c': successUrl
    };

    // 2. Превращаем объект в строку "key=value;key=value"
    const paramString = Object.entries(params)
        .map(([key, value]) => `${key}=${value}`)
        .join(';');

    // 3. Кодируем строку в base64 (btoa - встроенная функция браузера)
    const base64Params = btoa(paramString);

    // 4. Формируем финальный URL для тестового чекаута
    const redirectUrl = `https://checkout.paycom.uz/${base64Params}`;

    // 5. Перенаправляем пользователя
    window.location.replace(redirectUrl);
</script>
</body>
</html>
