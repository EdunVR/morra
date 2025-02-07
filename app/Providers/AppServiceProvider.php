<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Setting;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        view()->composer('app', function ($view) {
            $view->with('setting', Setting::first());
        });
        view()->composer('auth', function ($view) {
            $view->with('setting', Setting::first());
        });
        view()->composer('auth.login', function ($view) {
            $view->with('setting', Setting::first());
        });
        view()->composer('member.cetak', function ($view) {
            $view->with('setting', Setting::first());
        });
        view()->composer('header', function ($view) {
            $view->with('setting', Setting::first());
        });
        view()->composer('sidebar', function ($view) {
            $view->with('setting', Setting::first());
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
