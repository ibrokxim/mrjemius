<?php

namespace App\Http\Controllers;

use App\Services\TelegramService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class FeedbackController extends Controller
{
    protected $telegramService;

    public function __construct(TelegramService $telegramService)
    {
        $this->telegramService = $telegramService;
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Ошибка валидации.', 'errors' => $validator->errors()], 422);
        }

        // 2. Если валидация прошла, вызываем метод сервиса для отправки
        try {
            $this->telegramService->sendFeedbackNotification($request->input('name'), $request->input('phone'));
            return response()->json(['success' => true, 'message' => 'Ваша заявка успешно отправлена!']);

        } catch (\Exception $e) {
            Log::error('Ошибка отправки заявки обратной связи: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Не удалось отправить заявку. Попробуйте позже.'], 500);
        }
    }

}
