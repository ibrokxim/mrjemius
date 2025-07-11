<?php

namespace App\Providers;

use App\Models\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrap();
        View::composer('partials.navbar', function ($view) {
            $categories = Cache::remember('navbar_categories', 3600, function () {
                return Category::where('is_active', true)
                    ->orderBy('sort_order', 'asc')
                    ->limit(10)
                    ->get();
            });

            $view->with('categories', $categories);
        });
        Model::unguard();

//        if (config('app.env') === 'production') {
//            URL::forceScheme('https');
//        }
        if (app()->environment('production')) {
            URL::forceRootUrl(config('app.url'));
            URL::forceScheme('https');
        }
    }
}
