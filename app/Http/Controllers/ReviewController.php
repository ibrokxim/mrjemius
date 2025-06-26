<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReviewRequest;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(StoreReviewRequest $request)
    {
        // Проверяем, покупал ли пользователь этот товар (опционально, но хорошая практика)
        // $user = Auth::user();
        // if (!$user->orders()->whereHas('items', fn($q) => $q->where('product_id', $request->product_id))->exists()) {
        //     return back()->with('error', 'Вы можете оставлять отзывы только на купленные товары.');
        // }

        Auth::user()->reviews()->create($request->validated());

        return back()->with('success', 'Спасибо за ваш отзыв! Он будет опубликован после модерации.');
    }
}
