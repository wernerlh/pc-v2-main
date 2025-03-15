<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\TransaccionesFinanciera; // Asegúrate que esta importación sea exactamente así
use App\Observers\TransaccionesFinancieraObserver;

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
        TransaccionesFinanciera::observe(TransaccionesFinancieraObserver::class);
    }
}