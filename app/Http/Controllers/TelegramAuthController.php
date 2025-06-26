<?php

namespace App\Http\Controllers;


use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TelegramAuthController extends Controller
{
    public function handle(Request $request)
    {
        $data = $request->all();
        if (!$this->checkTelegramAuth($data)) {
            abort(403, 'Данные не прошли проверку');
        }

        // Найти или создать пользователя
        $user = User::updateOrCreate(
            ['telegram_id' => $data['id']],
            [
                'telegram_username' => $data['username'] ?? null,
                'telegram_first_name' => $data['first_name'] ?? null,
                'telegram_last_name' => $data['last_name'] ?? null,
                'telegram_photo_url' => $data['photo_url'] ?? null,
                'name' => $data['first_name'] ?? 'Без имени',
            ]
        );

        Auth::login($user, true); // Авторизация
        
        // Мигрируем корзину гостя к авторизованному пользователю
        $cartController = new CartController();
        $cartController->migrateGuestCart();
        
        return redirect('/'); // Куда перенаправить
    }

    private function checkTelegramAuth(array $data): bool
    {
        $check_hash = $data['hash'];
        unset($data['hash']);
        ksort($data);

        $data_check_arr = [];
        foreach ($data as $key => $value) {
            $data_check_arr[] = "$key=$value";
        }

        $data_check_string = implode("\n", $data_check_arr);
        $secret_key = hash('sha256', env('TELEGRAM_BOT_TOKEN'), true);
        $hash = hash_hmac('sha256', $data_check_string, $secret_key);

        return hash_equals($hash, $check_hash);
    }

}
