<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

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
        if (isset($_SERVER['VERCEL_URL']) || config('app.env') === 'production') {
            URL::forceScheme('https');
            
            // If VERCEL_URL is set, we can use it to force the APP_URL if needed
            if (isset($_SERVER['VERCEL_URL']) && !str_contains(config('app.url'), 'vercel.app')) {
                config(['app.url' => 'https://' . $_SERVER['VERCEL_URL']]);
            }
        }
    }
}
