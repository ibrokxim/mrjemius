<?php

namespace App\Http\Controllers;

use App\Services\TelegramBotService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TelegramController extends Controller
{
    protected $telegramBotService;

    public function __construct(TelegramBotService $telegramBotService)
    {
        $this->telegramBotService = $telegramBotService;
    }

    public function handle(Request $request)
    {
        try {
            $updateData = $request->all();
            Log::info('Telegram Webhook:', $updateData);

            // Передаем все данные в сервис для обработки
            $this->telegramBotService->handleUpdate($updateData);

        } catch (\Exception $e) {
            Log::error('Telegram handle error: ' . $e->getMessage());
        } finally {
            // Всегда возвращаем успешный ответ, чтобы Telegram не пытался отправить обновление снова
            return response()->json(['status' => 'ok']);
        }
    }
}
