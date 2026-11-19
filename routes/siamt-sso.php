<?php
use App\Siamt\Controller as SiamtController;
use Illuminate\Support\Facades\Route;

// Replace legacy authentication routes, including GET|POST multi-method logout.
// Merely overriding individual verbs leaves old named routes in route caches.
$collection = new \Illuminate\Routing\RouteCollection();
$catchAll = [];
foreach (Route::getRoutes() as $route) {
    $uri = trim($route->uri(), '/');
    if ($uri === '{any}') {
        $catchAll[] = $route;
    } elseif (!in_array($uri, ['', 'login', 'logout', 'api/login', 'api/logout', 'auth/sso/callback', 'api/sso/slo-logout'], true)) {
        $collection->add($route);
    }
}
Route::setRoutes($collection);

if (!Route::has('dashboard')) {
    if (Route::has('reportes.index')) {
        Route::get('/dashboard', fn () => redirect()->route('reportes.index'))->middleware('auth')->name('dashboard');
    } elseif (class_exists(\App\Http\Controllers\CuentasController::class)) {
        Route::get('/dashboard', [\App\Http\Controllers\CuentasController::class, 'index'])
            ->middleware(['auth', 'auth.cxc', 'permission:cxc.ver'])
            ->name('dashboard');
        Route::get('/cxc', fn () => redirect()->route('dashboard'))
            ->middleware('auth')
            ->name('cxc.index');
    } else {
        Route::get('/dashboard', fn () => view('welcome'))->middleware('auth')->name('dashboard');
    }
}

Route::get('/', [SiamtController::class, 'start']);
Route::get('/login', [SiamtController::class, 'start'])->name('login');
Route::post('/login', fn () => abort(405, 'Inicia sesión desde SIAMT.'));
Route::post('/api/login', fn () => abort(405, 'Inicia sesión desde SIAMT.'));
Route::get('/auth/sso/callback', [SiamtController::class, 'callback'])->name('auth.sso.callback');
Route::post('/logout', [SiamtController::class, 'destroy'])->middleware('auth')->name('logout');
Route::post('/api/logout', [SiamtController::class, 'destroy'])->middleware('auth');
Route::get('/logout', fn () => redirect()->away('https://siamt.mt.local/'));
Route::post('/api/sso/slo-logout', [SiamtController::class, 'receive'])->withoutMiddleware([
    \App\Siamt\Middleware::class,
    \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
    \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
    \Illuminate\Foundation\Http\Middleware\PreventRequestForgery::class,
    \App\Http\Middleware\VerifyCsrfToken::class,
    \Illuminate\Session\Middleware\StartSession::class,
    \Illuminate\View\Middleware\ShareErrorsFromSession::class,
    \App\Http\Middleware\HandleInertiaRequests::class,
    \Inertia\Middleware::class,
]);

if ($catchAll !== []) {
    $finalCollection = Route::getRoutes();
    foreach ($catchAll as $route) {
        $finalCollection->add($route);
    }
    Route::setRoutes($finalCollection);
}
