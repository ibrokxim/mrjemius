<?php

namespace App\Http\Controllers;

use App\Services\ProductService;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __construct(protected ProductService $productService)
    {
    }

    public function searchProducts(Request $request)
    {
        $searchQuery = $request->input('query');
        $products = $this->productService->searchProducts(['search_term' => $searchQuery]);


        return view('search-results', [
            'products' => $products,
            'searchQuery' => $searchQuery,
        ]);
    }
}
