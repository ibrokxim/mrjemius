<?php

namespace App\Http\Controllers;

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
}
