<?php

namespace App\Providers;

use App\Auth\MultiRoleUserProvider;
use Illuminate\Support\Facades\Auth;
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
        Auth::provider('multi_role', function ($app) {
            return new MultiRoleUserProvider($app['hash']);
        });
    }
}
