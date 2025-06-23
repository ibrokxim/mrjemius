<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FilterProductsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'price_from' => 'nullable|numeric|min:0',
            'price_to' => 'nullable|numeric|min:0|gte:price_from',
            'rating' => 'nullable|integer|between:1,5',
            'sort' => 'nullable|string|in:price-asc,price-desc,rating-desc,newest',
            'per_page' => 'nullable|integer|in:12,24,36',
        ];
    }
}
