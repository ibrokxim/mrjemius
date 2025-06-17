<?php

namespace App\Repositories\Contracts;

use App\Models\Product;

interface ProductRepositoryInterface
{
    public function getProductBySlug($slug): ?Product;

    public function getBestSellerProducts($limit = 10);

    public function getAllProducts();

    public function createProduct(array $data);

    public function updateProduct($product);

    public function deleteProduct($product);


}
