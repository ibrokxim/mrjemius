<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => 'nullable|integer|exists:categories,id',
            'name' => 'nullable|string|max:255',
            'slug' => 'nullable|string|unique:products,slug|max:255',
            'sku' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string',
            'price' => 'nullable|numeric',
            'sale_price' => 'nullable|numeric',
            'stock_quantity' => 'nullable|numeric',
            'is_active' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'weight_kg' => 'nullable|numeric',
            'attributes' => 'nullable|array',

            'images' => 'nullable|array',
            'images.*.image_url' => 'nullable|string',
            'images.*.alt_text' => 'nullable|string',
            'images.*.is_primary' => 'nullable|boolean',
            'images.*.sort_order' => 'nullable|integer|min:0',

            'tags' => 'nullable|array',
            'tags.*' => 'integer|exists:tags,id',

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
            'seo.meta_keywords' => 'nullable|string|max:255',
            'seo.og_title' => 'nullable|string|max:255',
            'seo.og_description' => 'nullable|string|max:1000',
            'seo.og_image_url' => 'nullable|string|url|max:255', // URL для OG Image
            'seo.canonical_url' => 'nullable|string|url|max:255',
            'seo.robots_tags' => 'nullable|string|max:255',
            'seo.custom_html_head_start' => 'nullable|string',
            'seo.custom_html_head_end' => 'nullable|string',
            'seo.custom_html_body_start' => 'nullable|string',
            'seo.custom_html_body_end' => 'nullable|string',
        ];
    }
}
