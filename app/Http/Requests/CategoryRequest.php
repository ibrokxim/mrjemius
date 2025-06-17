<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:categories,slug|max:255',
            'parent_id' => 'nullable|integer|exists:categories,id',
            'sort_order' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
            'image_url' => 'nullable|string',
            
            // SEO fields
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:1000',
            'meta_keywords' => 'nullable|string|max:255',
            'og_title' => 'nullable|string|max:255',
            'og_description' => 'nullable|string|max:1000',
            'og_image_url' => 'nullable|string|url|max:255',
            'canonical_url' => 'nullable|string|url|max:255',
            'robots_tags' => 'nullable|string|max:255',
            'custom_html_head_start' => 'nullable|string',
            'custom_html_head_end' => 'nullable|string',
            'custom_html_body_start' => 'nullable|string',
            'custom_html_body_end' => 'nullable|string',
        ];
    }
}