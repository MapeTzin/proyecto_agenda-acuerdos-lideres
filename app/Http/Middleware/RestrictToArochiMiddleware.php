<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RestrictToArochiMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (!$user) {
            abort(403, 'Acceso no autorizado.');
        }

        if (
            $user->email === 'soporte@mapetzin.com' ||
            $user->hasRole('Administrador') ||
            $user->can('roles.manage') ||
            $user->can('users.manage')
        ) {
            return $next($request);
        }

        abort(403, 'No tiene permisos suficientes para gestionar roles y accesos.');
    }
}
