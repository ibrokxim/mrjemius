<?php

use App\Http\Controllers\PaymeController;
use Illuminate\Support\Facades\Route;



Route::post('/payme/callback', [PaymeController::class, 'webhook']);
