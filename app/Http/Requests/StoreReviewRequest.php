<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StoreReviewRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check(); // Только для авторизованных
    }

    public function rules(): array
    {
        return [
            'product_id' => [
                'required',
                'integer',
                'exists:products,id',
                // Проверка, что пользователь еще не оставлял отзыв на этот товар
                Rule::unique('reviews')->where(function ($query) {
                    return $query->where('user_id', Auth::id());
                }),
            ],
            'rating' => 'required|integer|between:1,5',
            'comment' => 'required|string|min:10|max:2000',
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.unique' => 'Вы уже оставляли отзыв на этот товар.',
            'rating.required' => 'Пожалуйста, поставьте оценку.',
            'comment.required' => 'Пожалуйста, напишите текст отзыва.',
            'comment.min' => 'Отзыв должен содержать не менее 10 символов.',
        ];
    }
}
