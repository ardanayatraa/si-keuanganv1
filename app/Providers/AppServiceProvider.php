<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Rekening;
use App\Observers\RekeningObserver;

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
            Rekening::observe(RekeningObserver::class);
    }
}
