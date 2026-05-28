<?php

namespace App\Providers;

use App\Models\SocioCuota;
use App\Observers\SocioCuotaObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        SocioCuota::observe(SocioCuotaObserver::class);
    }
}
