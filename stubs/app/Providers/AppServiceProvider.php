<?php

namespace App\Providers;

use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
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
        Authenticate::redirectUsing(function (Request $request) {
            if (Route::is('admin.*')) {
                return route('admin.login.create');
            }

            return route('login');
        });

        RedirectIfAuthenticated::redirectUsing(function (Request $request) {
            if (Route::is('admin.*')) {
                return route('admin.dashboard');
            }

            return '/';
        });
    }
}
