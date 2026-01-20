<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Helpers\PermissionHelper;

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
        // Share user roles and accessible menus with all views
        View::composer('*', function ($view) {
            if (auth()->check()) {
                $user = auth()->user();
                // Eager load roles to avoid N+1 queries
                if (!$user->relationLoaded('roles')) {
                    $user->load('roles');
                }
                $view->with('currentUser', $user);
                $view->with('accessibleMenus', PermissionHelper::getAccessibleMenus($user));
            }
        });
    }
}
