<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\CategoryService;
use App\Services\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected ProductService $productService;
    protected CategoryService $categoryService;
    public function __construct(
        ProductService $productService,
        CategoryService $categoryService
    )
    {
        $this->productService = $productService;
        $this->categoryService = $categoryService;
    }

    public function getAllProducts()
    {
        $products = $this->productService->getAllProducts();
        return view('admin.products.index', compact('products'));
    }



}
