<?php

namespace App\Repositories;

use App\Models\Product as Model;
use App\Repositories\Contracts\ProductRepositoryInterface;

class ProductRepository extends CoreRepository implements ProductRepositoryInterface
{
    protected function getModelClass(): string
    {
        return Model::class;
    }

    public function getProductBySlug($slug): ?Model
    {
        return $this->startConditions()
            ->with(['category', 'images', 'tags', 'reviews'])
            ->where('slug', $slug)
            ->first();
    }

    public function getBestSellerProducts($limit = 10)
    {
        return $this->startConditions()::with(['category', 'primaryImage'])
            ->where('is_active', true)
            ->where('is_featured', true)
            ->orderBy('updated_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getAllProducts()
    {
        return $this->startConditions()::with(['category', 'primaryImage'])->paginate(20);
    }

    public function createProduct(array $data)
    {
        return $this->startConditions()->create($data);
    }

    public function updateProduct($product)
    {
        // TODO: Implement updateProduct() method.
    }

    public function deleteProduct($product)
    {
        // TODO: Implement deleteProduct() method.
    }
}
