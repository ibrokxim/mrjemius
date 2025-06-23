<?php

namespace App\Http\Controllers;

use App\Services\CategoryService;
use App\Services\ProductService;

class BaseController extends Controller
{
    public function __construct(
        protected CategoryService $categoryService,
        protected ProductService $productService
    ) {}
    public function returnWelcomePage()
    {
        $categories = $this->categoryService->getCategoriesForMainPage(10); // Пример метода
        $popularProducts = $this->productService->getAllProducts(); // Пример метода
        $bestsellerProducts = $this->productService->getBestSellerProducts(8);

        return view('welcome', compact('categories', 'popularProducts', 'bestsellerProducts'));
    }
    public function index()
    {
        return view('welcome');
    }

    public function footer()
    {
        $categories = $this->categoryService->getAllCategories();
        return view('partials.footer', compact('categories'));
    }

    public function navbar()
    {
        $categories = $this->categoryService->getAllCategories();
        return view('partials.navbar', compact('categories'));
    }
}
