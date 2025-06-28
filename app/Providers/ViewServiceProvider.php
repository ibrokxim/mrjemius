<?php

namespace App\Providers;

use App\Services\CategoryService;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(CategoryService $categoryService): void
    {
        View::composer('partials.footer', function ($view) use ($categoryService) {
            // Получаем все категории
            $categories = $categoryService->getAllCategories();

            // Передаем переменную $categories в шаблон
            $view->with('categories', $categories);
        });

    }
}
