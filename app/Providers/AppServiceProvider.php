<?php

namespace App\Providers;

use App\Models\DataInventory;
use App\Observers\DataInventoryObserver;
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
        // Register Observer untuk Auto Register Aset
        DataInventory::observe(DataInventoryObserver::class);
    }
}
