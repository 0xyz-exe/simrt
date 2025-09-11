<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

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
        Gate::define('isAdmin', fn($user) => $user->is_admin);
        Gate::define('admin', fn($user) => $user->is_admin);
        Gate::define('resident', fn($user) => !$user->is_admin);
    }
}
