<?php

use App\Http\Controllers\PaymeController;
use App\Http\Middleware\PaymeMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Khamdullaevuz\Payme\Facades\Payme;
use Khamdullaevuz\Payme\Http\Middleware\PaymeCheck;

Route::any('/payme/callback',[PaymeController::class, 'handle'])->middleware(PaymeMiddleware::class);
//Route::any('/payme/callback', function (Request $request) {
//    return Payme::handle($request);
//})->middleware(PaymeCheck::class);
