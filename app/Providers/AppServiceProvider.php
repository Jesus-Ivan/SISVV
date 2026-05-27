<?php

namespace App\Providers;

use App\Models\SocioCuota;
use App\Observers\SocioCuotaObserver;
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
        // Mantiene sincronizada la fila legacy de socios_membresias cuando se modifica socios_cuotas
        SocioCuota::observe(SocioCuotaObserver::class);
    }
}
