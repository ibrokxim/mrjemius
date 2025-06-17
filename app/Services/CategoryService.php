<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CategoryService
{
    protected CategoryRepositoryInterface $categoryRepository;

    public function __construct(CategoryRepositoryInterface $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }
    public function getCategoryBySlug($slug): ?Category
    {
        $category = $this->categoryRepository->findBySlug($slug);
        if (!$category) {
            throw new NotFoundHttpException('Category not found');
        }
        return $category;
    }

    public function getCategoriesForMainPage(): Collection
    {
        $categories = $this->categoryRepository->getAll();
        if ($categories->isEmpty()) {
            throw new NotFoundHttpException('Category not found');
        }
        return $categories;
    }
}
