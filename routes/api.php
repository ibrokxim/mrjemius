<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;

// Products
Route::group(['prefix' => 'products'], function () {
    Route::get('/{slug}', [ProductController::class, 'getProductBySlug']);
    Route::get('/featured', [ProductController::class, 'getFeaturedProducts']);
});

// Categories
Route::group(['prefix' => 'categories'], function () {
    Route::get('/{slug}', [CategoryController::class, 'getCategoryBySlug']);
    Route::get('/all-categories', [CategoryController::class, 'getAllCategories']);
});


Route::group(['prefix' => 'cart'], function () {
//   Route::get();
});

