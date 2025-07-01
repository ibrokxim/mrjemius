<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        // Проверяем, есть ли в сессии переменная 'locale'
        $locale = $request->segment(1);

        // Если локаль не в URL, берем из сессии или устанавливаем по умолчанию
        if (!in_array($locale, ['uz', 'ru'])) {
            $locale = Session::get('locale', config('app.locale', 'ru'));
        }

        // Устанавливаем локаль
        if (in_array($locale, ['uz', 'ru'])) {
            App::setLocale($locale);
            Session::put('locale', $locale);
        }

        return $next($request);
    }
}
