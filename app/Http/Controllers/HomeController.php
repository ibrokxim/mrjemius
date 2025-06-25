<?php

namespace App\Http\Controllers;

use App\Models\Banner;

class HomeController extends Controller
{
    public function index()
    {
        $activeBanners = Banner::where('is_active', true);
        return view('welcome', compact('activeBanners'));
    }
}
