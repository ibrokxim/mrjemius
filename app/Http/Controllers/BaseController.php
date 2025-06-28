<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Services\ProductService;
use App\Services\CategoryService;

class BaseController extends Controller
{
    public function __construct(
        protected CategoryService $categoryService,
        protected ProductService $productService
    ) {}
    public function returnWelcomePage()
    {
        $categories = $this->categoryService->getCategoriesForMainPage(10);
        $popularProducts = $this->productService->getAllProducts();
        $bestsellerProducts = $this->productService->getBestSellerProducts(8);
        $banners = Banner::where('is_active', 1)->get();
        return view('welcome', compact('categories','banners', 'popularProducts', 'bestsellerProducts'));
    }

    public function loadMorePopularProducts(Request $request)
    {
        $perPage = $request->input('per_page', 5);
        $products = $this->productService->getPopularProductsWithPagination($perPage);
        return response()->json([
            'html' => view('partials.product_cards_grid', ['products' => $products])->render(),
            'hasMorePages' => $products->hasMorePages(),
            'currentPage' => $products->currentPage(),
            'totalPages' => $products->lastPage()
        ]);
    }
    public function index()
    {
        return view('welcome');
    }

//    public function loadMorePopularProducts(Request $request)
//    {
//        $perPage = 5; // Сколько товаров загружать за один раз по клику на кнопку
//
//        $products = $this->productService->getPopularProductsWithPagination($perPage);
//
//        if ($products->isEmpty()) {
//            return response()->json(['html' => '', 'hasMore' => false]);
//        }
//
//        $html = view('partials.product_cards_grid', ['products' => $products])->render();
//        return response()->json([
//            'html' => $html,
//            'hasMore' => $products->hasMorePages()
//        ]);
//    }

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
