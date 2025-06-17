<?php

namespace App\Http\Controllers;

use App\Services\CategoryService;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    private CategoryService $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    public function getAllCategories(): JsonResponse
    {
        $categories = $this->categoryService->getCategoriesForMainPage();

        return response()->json($categories);
    }

    public function getCategoryBySlug(string $slug): JsonResponse
    {
        $category = $this->categoryService->getCategoryBySlug($slug);

        return response()->json($category);
    }
}
