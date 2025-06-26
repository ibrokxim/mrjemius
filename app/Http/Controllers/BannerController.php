<?php

namespace App\Http\Controllers;

use App\Models\Banner;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::where('is_active', 1)->get();
        return view('welcome', compact('banners'));
    }

    public function getBannerBySlug($slug)
    {
        // TODO
    }
}
