<?php

namespace App\Http\Controllers;

use App\Models\Promotion;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PromotionController extends Controller
{
    /**
     * Проверяет и применяет промокод к сессии корзины.
     */
    public function apply(Request $request, CartService $cartService)
    {
        $request->validate(['promo_code' => 'required|string|max:50']);
        $code = trim($request->input('promo_code'));

        $promotion = Promotion::where('code', $code)->where('is_active', true)->first();

        if (!$promotion) {
            return response()->json(['success' => false, 'message' => 'Промокод не найден или неактивен.']);
        }
        if ($promotion->starts_at && $promotion->starts_at->isFuture()) {
            return response()->json(['success' => false, 'message' => 'Этот промокод еще не начал действовать.']);
        }
        if ($promotion->expires_at && $promotion->expires_at->isPast()) {
            return response()->json(['success' => false, 'message' => 'Срок действия этого промокода истек.']);
        }
        if ($promotion->max_uses && $promotion->uses_count >= $promotion->max_uses) {
            return response()->json(['success' => false, 'message' => 'Лимит использования этого промокода исчерпан.']);
        }

        $summary = $cartService->getSummary(); // Получаем текущую сумму
        if ($promotion->minimum_spend && $summary['subtotal'] < $promotion->minimum_spend) {
            return response()->json(['success' => false, 'message' => "Минимальная сумма заказа для этого промокода: {$promotion->minimum_spend} сум."]);
        }

        if ($promotion->max_uses_user) {
            $userUses = DB::table('orders')
                ->where('user_id', auth()->id())
                ->where('promotion_id', $promotion->id)
                ->count();
            if ($userUses >= $promotion->max_uses_user) {
                return response()->json(['success' => false, 'message' => 'Вы уже использовали этот промокод максимальное количество раз.']);
            }
        }

        session(['applied_promotion_id' => $promotion->id]);

        $newSummary = $cartService->getSummary();

        return response()->json([
            'success' => true,
            'message' => 'Промокод успешно применен!',
            'cart_summary' => $newSummary,
        ]);
    }

    public function remove()
    {
        session()->forget('applied_promotion_id');
        return back()->with('success', 'Промокод удален.');
    }
}
