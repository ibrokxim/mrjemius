<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TelegramWebAppAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            return $next($request);
        }

        if ($request->has('telegram_id')) {
            $telegramId = $request->get('telegram_id');
            $user = User::where('telegram_id', $telegramId)->first();

            if ($user) {
                Auth::login($user);
            }
        }

        return $next($request);
    }

}
