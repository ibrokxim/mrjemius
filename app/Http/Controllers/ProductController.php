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
        // --- НОВАЯ ЛОГИКА ДЛЯ АТРИБУТОВ ---
        $allAttributes = (array)$product->attributes;

        // Ключевые слова для summary-блока
        $keySpecKeywords = ['ккал', 'белки', 'жиры', 'углеводы'];

        $keySpecs = [];
        $otherSpecs = [];

        foreach ($allAttributes as $name => $value) {
            $found = false;
            // Проверяем, содержит ли название атрибута одно из ключевых слов
            foreach ($keySpecKeywords as $keyword) {
                if (mb_stripos($name, $keyword) !== false) {
                    // Если нашли, добавляем в ключевые и переходим к следующему атрибуту
                    $keySpecs[$name] = $value;
                    $found = true;
                    break;
                }
            }
            // Если ключевое слово не найдено, добавляем в "остальные"
            if (!$found) {
                $otherSpecs[$name] = $value;
            }
        }

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
            'ratingDistribution' => $ratingDistribution,
            'allAttributes' => $allAttributes,
        ]);
    }

    public function loadMorePopularProducts(Request $request)
    {
        $page = $request->input('page', 2);
        $perPage = 5;

        $products = $this->productService->getPaginatedPopularProducts($perPage, $page);

        // Проверяем, есть ли вообще товары для загрузки
        if ($products->isEmpty()) {
            return response()->json(['html' => '', 'hasMore' => false]);
        }

        // Рендерим HTML только для новых карточек
        $html = view('partials.product_cards_grid', ['products' => $products])->render();

        // Возвращаем HTML и флаг, есть ли еще страницы
        return response()->json([
            'html' => $html,
            'hasMore' => $products->hasMorePages()
        ]);
    }
}
