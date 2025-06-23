<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Traits\HasUniqueSlug;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProductService
{
    use HasUniqueSlug;

    protected ProductRepositoryInterface $productRepository;

    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function getBestSellerProducts(): ?Collection
    {
        $products = $this->productRepository->getBestSellerProducts();
//        if ($products->isEmpty()) {
//            throw new NotFoundHttpException('Products not found');
//        }
        return $products;
    }

    public function getProductBySlug(string $slug): ?Product
    {
        $product = $this->productRepository->getProductBySlug($slug);
        if (!$product) {
            throw new NotFoundHttpException('Product not found');
        }
        return $product;
    }

    public function getAllProducts()
    {
        return $this->productRepository->getAllProducts();
    }

    public function getProductsForCategoryPage(Category $category, array $requestData): LengthAwarePaginator
    {

        // 1. Извлекаем и подготавливаем данные для репозитория
        $filters = [
            'price_from' => $requestData['price_from'] ?? null,
            'price_to' => $requestData['price_to'] ?? null,
            'rating' => $requestData['rating'] ?? null,
        ];

        $sortBy = match ($requestData['sort'] ?? '') {
            'price-asc' => ['price' => 'asc'],
            'price-desc' => ['price' => 'desc'],
            'rating-desc' => ['rating' => 'desc'],
            default => ['created_at' => 'desc'],
        };

        $perPage = $requestData['per_page'] ?? 12;

        // 2. Вызываем метод репозитория с подготовленными данными
        return $this->productRepository->getForCategory(
            $category->id,
            (int) $perPage,
            array_filter($filters), // Передаем только не-пустые фильтры
            $sortBy
        );
    }

    public function searchProducts(array $searchData, int $perPage = 12): LengthAwarePaginator
    {
        return $this->productRepository->search($searchData, $perPage);
    }

}
