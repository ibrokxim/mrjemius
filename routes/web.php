<?php

use App\Http\Controllers\BlogController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\LanguageController;
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
Route::post('/feedback/store', [FeedbackController::class, 'store'])->name('feedback.store');


Route::get('/category/{category:slug}', [CategoryController::class, 'show'])->name('category.show');
Route::get('/products/{product:slug}', [ProductController::class,'show'])->name('product.show');
Route::get('/load-popular-products', [BaseController::class, 'loadMorePopularProducts'])->name('products.load-popular');
Route::get('/auth/telegram/callback', [TelegramAuthController::class, 'handle']);

Route::get('/search', [SearchController::class, 'searchProducts'])->name('search.products');
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{post:slug}', [BlogController::class, 'show'])->name('blog.show');

Route::middleware('auth')->prefix('wishlist')->name('wishlist.')->group(function () {
   // Route::get('/', [WishlistController::class, 'index'])->name('index');
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('index');
    Route::post('/wishlist/toggle/{product}', [WishlistController::class, 'toggle'])->name('toggle');
});


// Маршруты корзины (доступны всем)
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::get('/cart/data', [CartController::class, 'getCartData'])->name('cart.data');

// Маршруты корзины (только для авторизованных)
Route::middleware('auth')->group(function () {
    Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
    Route::patch('/cart/update/{cartItem}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/remove/{cartItem}', [CartController::class, 'remove'])->name('cart.remove');
    Route::delete('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');
    Route::post('/cart/move-from-wishlist', [CartController::class, 'moveFromWishlist'])->name('cart.move.from.wishlist');
});
Route::middleware('auth')->prefix('checkout')->name('checkout.')->group(function () {
    Route::get('/', [CheckoutController::class, 'index'])->name('index'); // Страница оформления
    Route::post('/place-order', [CheckoutController::class, 'store'])->name('store'); // Обработка заказа
});

Route::post('/cart/set-delivery-method', [CartController::class, 'setDeliveryMethod'])->name('cart.setDeliveryMethod')->middleware('auth');

Route::get('/order-success', [App\Http\Controllers\CheckoutController::class,'success'])->name('order.success')->middleware('auth');

Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store')->middleware('auth');
Route::get('/contacts', [PageController::class, 'contacts'])->name('contacts');
Route::get('/terms-and-conditions', function () {
    return view('pages.terms'); // Мы будем использовать шаблон 'pages.terms'
})->name('terms.show');
// routes/web.php

Route::get('language/{locale}', [LanguageController::class, 'switchLanguage'])->name('language.switch')->where('language', 'ru|uz');;


