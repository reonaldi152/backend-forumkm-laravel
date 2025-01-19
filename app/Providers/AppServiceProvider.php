<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\EnsureSellerIsActive;
use App\Http\Middleware\EnsureSellerSessionIsValid;

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
        Route::aliasMiddleware('seller.active', EnsureSellerIsActive::class);
        Route::aliasMiddleware('seller.session.valid', EnsureSellerSessionIsValid::class);
    }
}
