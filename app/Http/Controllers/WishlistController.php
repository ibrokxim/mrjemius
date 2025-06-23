<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function index()
    {
        $wishlistProducts = Auth::user()->wishlistProducts()
            ->with('primaryImage')
            ->paginate(5); // По 5 на странице для примера

        return view('wishlist', ['products' => $wishlistProducts]);
    }

    public function toggle(Product $product, Request $request)
    {
        $user = $request->user();

        // toggle() - удобный метод для many-to-many. Он добавляет запись, если ее нет, и удаляет, если есть.
        $result = $user->wishlistProducts()->toggle($product->id);

        $isAdded = !empty($result['attached']);

        return response()->json([
            'success' => true,
            'status' => $isAdded ? 'added' : 'removed',
            'message' => $isAdded ? 'Товар добавлен в избранное!' : 'Товар удален из избранного.',

            'wishlistCount' => $user->wishlistProducts()->count()
        ]);
    }
}
