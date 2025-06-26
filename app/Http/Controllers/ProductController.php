<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    private ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function getFeaturedProducts(): JsonResponse
    {
        $products = $this->productService->getBestSellerProducts();

        return response()->json($products);
    }

    public function getProductBySlug($slug)
    {
        $product = $this->productService->getProductBySlug($slug);

        return response()->json($product);
    }

    public function show(Product $product)
    {
        $product->loadMissing(['category', 'images', 'reviews.user', 'tags']);

        $reviews = $product->reviews()->where('is_approved', true)->latest()->get();
        $reviewsCount = $reviews->count();
        $avgRating = $reviews->avg('rating');

        $ratingDistribution = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
        if ($reviewsCount > 0) {
            $ratingGroups = $reviews->groupBy('rating');
            foreach ($ratingGroups as $rating => $group) {
                $ratingDistribution[$rating] = round(($group->count() / $reviewsCount) * 100);
            }
        }
        // Получаем похожие товары (например, из той же категории)
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id) // Исключаем текущий товар
            ->where('is_active', true)
            ->with('primaryImage', 'category')
            ->limit(5) // Ограничиваем количество похожих товаров
            ->get();

        // Передаем продукт и похожие товары в представление
        return view('components.product-show', [
            'product' => $product,
            'relatedProducts' => $relatedProducts,
            'reviews' => $reviews, // Передаем отфильтрованные и отсортированные отзывы
            'reviewsCount' => $reviewsCount,
            'avgRating' => $avgRating,
            'ratingDistribution' => $ratingDistribution
        ]);
    }
}
