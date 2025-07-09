<?php
// app/Http/Controllers/Auth/TelegramLoginController.php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TelegramLoginController extends Controller
{
    public function loginFromToken(Request $request, string $token)
    {
        $user = User::where('login_token', $token)
            ->where('login_token_expires_at', '>', now())
            ->first();

        // Если токен неверный или устарел
        if (!$user) {
            return redirect('/')->with('error', 'Ссылка для входа недействительна. Пожалуйста, попробуйте снова из Telegram.');
        }

        // 2. Сбрасываем токен, чтобы его нельзя было использовать повторно
        $user->update([
            'login_token' => null,
            'login_token_expires_at' => null,
        ]);

        // 3. Авторизуем пользователя в сессии
        Auth::login($user, true);

        // 4. Перенаправляем на страницу оформления заказа
        return redirect()->route('checkout.index');
    }
}
