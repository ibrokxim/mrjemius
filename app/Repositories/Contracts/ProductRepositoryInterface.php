<?php

namespace App\Repositories\Contracts;

use App\Models\Product;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ProductRepositoryInterface
{
    public function getProductBySlug($slug): ?Product;

    public function getBestSellerProducts($limit = 10);

    public function getAllProducts();

    public function getForCategory(int $categoryId, int $perPage = 12, array $filters = [], array $sortBy = []): Paginator;

    public function search(array $searchData, int $perPage = 12): LengthAwarePaginator;
}
