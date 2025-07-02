<?php

namespace App\Http\Controllers;


use App\Models\User;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TelegramAuthController extends Controller
{
    protected $cartService;
    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }
    public function handle(Request $request)
    {
        $data = $request->all();
        Log::info('Telegram Auth Data Received:', $data);
        if (!$this->checkTelegramAuth($data)) {
            Log::error('Telegram Auth Check FAILED.');
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
        $cartController = app(CartController::class);
        $cartController->migrateGuestCart();

        return redirect('/'); // Куда перенаправить
    }

    private function checkTelegramAuth(array $auth_data): bool
    {
        // 1. Проверяем, что хэш вообще пришел
        if (!isset($auth_data['hash'])) {
            \Log::error('Telegram Auth: Hash not found in received data.');
            return false;
        }

        $check_hash = $auth_data['hash'];

        // 2. Создаем массив данных для проверки, включая только нужные поля
        $data_check_arr = [];
        foreach ($auth_data as $key => $value) {
            // Исключаем hash из массива для проверки
            if ($key !== 'hash') {
                $data_check_arr[] = $key . '=' . $value;
            }
        }

        sort($data_check_arr);

        $data_check_string = implode("\n", $data_check_arr);

        $secret_key = hash('sha256', env('TELEGRAM_BOT_TOKEN'), true);
        $hash = hash_hmac('sha256', $data_check_string, $secret_key);

        $is_valid = hash_equals($hash, $check_hash);
        if (!$is_valid) {
            \Log::error('Telegram Auth Hash Mismatch.', [
                'expected_hash' => $hash,
                'received_hash' => $check_hash,
                'check_string' => $data_check_string,
            ]);
        }

        return $is_valid;
    }

}
