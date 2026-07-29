<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasAccess
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user) {
            // Superadmin always has access
            if ($user->email === 'soporte@mapetzin.com') {
                return $next($request);
            }

            // Check if user is active in ticket system AND has roles in agenda
            if ($user->is_active != 1 || !$user->roles()->exists()) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                $message = $user->is_active != 1 
                    ? 'Su cuenta ha sido desactivada en el sistema central.' 
                    : 'Su cuenta no tiene acceso a esta aplicación.';

                return redirect()->route('login')->withErrors([
                    'email' => $message . ' Por favor, contacte al administrador.',
                ]);
            }
        }

        return $next($request);
    }
}
