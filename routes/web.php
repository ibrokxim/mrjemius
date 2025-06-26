<?php

use App\Http\Controllers\BlogController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BaseController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\TelegramAuthController;


Route::get('/', [BaseController::class, 'returnWelcomePage'])->name('welcome');
Route::get('/about', [PageController::class, 'about'])->name('about');

Route::get('/category/{category:slug}', [CategoryController::class, 'show'])->name('category.show');
Route::get('/products/{product:slug}', [ProductController::class,'show'])->name('product.show');

Route::get('/auth/telegram/callback', [TelegramAuthController::class, 'handle']);

Route::get('/search', [SearchController::class, 'searchProducts'])->name('search.products');
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{post:slug}', [BlogController::class, 'show'])->name('blog.show');

Route::middleware('auth')->prefix('wishlist')->name('wishlist.')->group(function () {
    Route::get('/', [WishlistController::class, 'index'])->name('index');
});

Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index')->middleware('auth');
Route::post('/wishlist/toggle/{product}', [WishlistController::class, 'toggle'])->name('wishlist.toggle')->middleware('auth');

// Маршруты корзины (доступны всем)
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::get('/cart/data', [CartController::class, 'getCartData'])->name('cart.data');

// Маршруты корзины (только для авторизованных)
Route::middleware('auth')->group(function () {
    Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/update/{cartItem}', [CartController::class, 'update'])->name('cart.update');
    Route::post('/cart/remove/{cartItem}', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
    Route::post('/cart/move-from-wishlist', [CartController::class, 'moveFromWishlist'])->name('cart.move.from.wishlist');
});


Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store')->middleware('auth');
Route::get('/contacts', [PageController::class, 'contacts'])->name('contacts');
