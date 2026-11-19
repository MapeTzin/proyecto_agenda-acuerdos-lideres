<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // SIAMT_SSO_PROVIDER (solo activo en produccion cuando SIAMT_SSO_ENABLED=true)
        if (config('siamt-sso.enabled', false) && is_file(app_path('Siamt/Provider.php'))) {
            require_once app_path('Siamt/Provider.php');
            $this->app->register(\App\Siamt\Provider::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
