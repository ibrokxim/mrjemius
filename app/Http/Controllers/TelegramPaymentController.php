<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TelegramPaymentController extends Controller
{
    public function show(Request $request)
    {
        // Получаем данные из GET-параметров
        $order_id = $request->query('order_id');
        $amount = $request->query('amount');
        $user_id = $request->query('user_id');

        // Проверяем, что все данные есть
        if (!$order_id || !$amount || !$user_id) {
            return response('Отсутствуют необходимые параметры.', 400);
        }

        // Передаем данные в наш HTML-шаблон
        return view('telegram.pay', [
            'order_id' => $order_id,
            'amount' => $amount,
            'user_id' => $user_id,
            'merchant_id' => config('payme.merchant_id'),
            'success_url' => 'https://t.me/' . config('telegram.bots.my-bot.username'),
        ]);
    }
}
