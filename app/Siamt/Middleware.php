<?php
declare(strict_types=1);
namespace App\Siamt;

use AuthCenter\Satellite\SessionStore;
use AuthCenter\Satellite\SsoClient;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Middleware
{
    public function handle(Request $request, Closure $next)
    {
        $credentials = $request->is('register', 'forgot-password', 'reset-password*', 'password/reset*', 'password/email', 'change-password');
        if ($credentials) {
            abort_unless($request->isMethod('GET'), 405, 'La autenticación se administra en SIAMT.');
            return redirect()->away(app(SsoClient::class)->loginUrl());
        }
        if ($request->user()) {
            $handle = $request->session()->get('sso_local_handle');
            $grant = $request->session()->get('sso_grant_token');
            try {
                $interaction = $request->header('Sec-Fetch-Mode') === 'navigate'
                    || $request->header('X-SPA-Navigation') === '1' || !$request->isMethodSafe();
                $valid = is_string($handle) && is_string($grant)
                    && app(SessionStore::class)->active($handle, $request->user()->id)
                    && Access::allowed($request->user()) && app(SsoClient::class)->active($grant, $interaction);
            } catch (\Throwable $error) {
                return response('SIAMT temporalmente no disponible. Intenta nuevamente.', 503)->header('Cache-Control', 'no-store');
            }
            if (!$valid) {
                Auth::logout(); $request->session()->invalidate(); $request->session()->regenerateToken();
                return $request->expectsJson() ? response()->json(['message' => 'Sesión expirada o revocada.'], 401)
                    : redirect()->away(app(SsoClient::class)->loginUrl());
            }
        }
        $response = $next($request);
        $response->headers->set('Cache-Control', 'no-store, private');
        $response->headers->set('Referrer-Policy', 'no-referrer');
        return $response;
    }
}
