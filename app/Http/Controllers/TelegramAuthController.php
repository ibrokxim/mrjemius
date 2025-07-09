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

    // Для обычной авторизации через Telegram Login Widget
    public function handle(Request $request)
    {
        $data = $request->all();
        Log::info('Telegram Auth Data Received:', $data);

        if (!$this->checkTelegramAuth($data)) {
            Log::error('Telegram Auth Check FAILED.');
            abort(403, 'Данные не прошли проверку');
        }

        $user = $this->findOrCreateUser($data);
        Auth::login($user, true);

        // Мигрируем корзину гостя к авторизованному пользователю
        $cartController = app(CartController::class);
        $cartController->migrateGuestCart();

        return redirect('/');
    }

    // Для авторизации через Web App (AJAX)
    public function authenticate(Request $request)
    {
        $initData = $request->input('initData');

        if (!$initData) {
            return response()->json(['success' => false, 'error' => 'No initData provided'], 400);
        }

        Log::info('Web App Auth Data Received:', ['initData' => $initData]);

        if (!$this->validateTelegramWebAppData($initData)) {
            Log::error('Telegram Web App Auth Check FAILED.');
            return response()->json(['success' => false, 'error' => 'Invalid data'], 403);
        }

        // Парсим данные из initData
        parse_str($initData, $parsedData);
        $userData = json_decode($parsedData['user'], true);

        $user = $this->findOrCreateUser($userData);
        Auth::login($user, true);

        // Мигрируем корзину
        if (class_exists('App\Http\Controllers\CartController')) {
            $cartController = app(CartController::class);
            $cartController->migrateGuestCart();
        }

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'telegram_id' => $user->telegram_id
            ]
        ]);
    }

    // Универсальный метод для создания/поиска пользователя
    private function findOrCreateUser(array $data): User
    {
        return User::updateOrCreate(
            ['telegram_id' => $data['id']],
            [
                'telegram_username' => $data['username'] ?? null,
                'telegram_first_name' => $data['first_name'] ?? null,
                'telegram_last_name' => $data['last_name'] ?? null,
                'telegram_photo_url' => $data['photo_url'] ?? null,
                'name' => $data['first_name'] ?? 'Без имени',
            ]
        );
    }

    // Проверка для обычной авторизации через Login Widget
    private function checkTelegramAuth(array $auth_data): bool
    {
        if (!isset($auth_data['hash'])) {
            Log::error('Telegram Auth: Hash not found in received data.');
            return false;
        }

        $check_hash = $auth_data['hash'];
        $data_check_arr = [];

        foreach ($auth_data as $key => $value) {
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
            Log::error('Telegram Auth Hash Mismatch.', [
                'expected_hash' => $hash,
                'received_hash' => $check_hash,
                'check_string' => $data_check_string,
            ]);
        }

        return $is_valid;
    }

    // Проверка для Web App initData
    private function validateTelegramWebAppData(string $initData): bool
    {
        parse_str($initData, $data);

        if (!isset($data['hash'])) {
            Log::error('Telegram Web App: Hash not found in initData.');
            return false;
        }

        $checkHash = $data['hash'];
        unset($data['hash']);

        // Проверяем время (данные не должны быть старше 1 часа)
        if (isset($data['auth_date'])) {
            $authDate = (int)$data['auth_date'];
            $currentTime = time();
            if ($currentTime - $authDate > 3600) { // 1 час
                Log::error('Telegram Web App: Data too old.', [
                    'auth_date' => $authDate,
                    'current_time' => $currentTime,
                    'diff' => $currentTime - $authDate
                ]);
                return false;
            }
        }

        // Сортируем ключи и создаем строку для проверки
        ksort($data);
        $dataCheckString = implode("\n", array_map(
            fn($k, $v) => "$k=$v",
            array_keys($data),
            array_values($data)
        ));

        $secretKey = hash('sha256', env('TELEGRAM_BOT_TOKEN'), true);
        $hash = hash_hmac('sha256', $dataCheckString, $secretKey);

        $is_valid = hash_equals($hash, $checkHash);

        if (!$is_valid) {
            Log::error('Telegram Web App Hash Mismatch.', [
                'expected_hash' => $hash,
                'received_hash' => $checkHash,
                'check_string' => $dataCheckString,
            ]);
        }

        return $is_valid;
    }
}
