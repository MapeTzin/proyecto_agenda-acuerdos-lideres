<?php
declare(strict_types=1);
namespace App\Siamt;

use AuthCenter\Satellite\SessionStore;
use AuthCenter\Satellite\SsoClient;
use Illuminate\Support\ServiceProvider;

class Provider extends ServiceProvider
{
    public function register(): void
    {
        config(['services.auth_center' => config('siamt-sso') ?: config('siamt')]);
        foreach (glob(app_path('AuthCenter/Satellite/*.php')) as $file) { require_once $file; }
        foreach (['Access', 'Controller', 'Middleware'] as $name) { require_once __DIR__.'/'.$name.'.php'; }
        $this->app->singleton(SsoClient::class, fn () => new SsoClient(config('services.auth_center')));
        $this->app->singleton(SessionStore::class, fn () => new SessionStore('mysql', 'ac_siamt_'));
        // Do not allow a legacy optional connection to reach the prohibited host.
        foreach ((array) config('database.connections') as $name => $connection) {
            if (in_array($connection['host'] ?? null, ['192.168.100.12'], true)) {
                config(['database.connections.'.$name => null]);
            }
        }
    }

    public function boot(): void
    {
        $this->app->make(\Illuminate\Contracts\Http\Kernel::class)
            ->appendMiddlewareToGroup('web', Middleware::class);
    }
}
