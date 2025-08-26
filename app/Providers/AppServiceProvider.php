<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Rekening;
use App\Models\Utang;
use App\Models\Piutang;
use App\Observers\RekeningObserver;
use App\Observers\UtangObserver;
use App\Observers\PiutangObserver;

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
        Utang::observe(UtangObserver::class);
        Piutang::observe(PiutangObserver::class);
    }
}
