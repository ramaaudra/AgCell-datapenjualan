<?php

namespace App\Providers;

use App\Models\ProdukPenjualan;
use App\Models\LanggananWifi;
use App\Observers\ProdukPenjualanObserver;
use App\Observers\LanggananWifiObserver;
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
    public function boot()
    {
        ProdukPenjualan::observe(ProdukPenjualanObserver::class);
        LanggananWifi::observe(LanggananWifiObserver::class);
    }
}
