<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AuthCenterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.register');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Ingrese un formato de correo válido.',
            'password.required' => 'La contraseña es obligatoria.',
        ]);

        $email = Str::lower(trim($credentials['email']));
        $password = $credentials['password'];
        $remember = $request->boolean('remember');
        $throttleKey = Str::transliterate($email . '|' . $request->ip());

        // 1. Verificar RateLimiter (máximo 5 intentos por minuto)
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withErrors([
                'email' => "Demasiados intentos fallidos. Por favor espere {$seconds} segundos antes de intentar nuevamente.",
            ])->onlyInput('email');
        }

        $authCenterService = new AuthCenterService();
        $authUser = $authCenterService->validateCredentials($email, $password);

        if (!$authUser) {
            RateLimiter::hit($throttleKey, 60);
            $authCenterService->logLoginAttempt(null, $email, 'failed', 'Credenciales inválidas o cuenta desactivada', $request);

            return back()->withErrors([
                'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros o la cuenta está desactivada.',
            ])->onlyInput('email');
        }

        // 2. Verificar acceso en user_system_access para agenda_acuerdos
        if (!$authCenterService->userHasSystemAccess($authUser->id, 'agenda_acuerdos')) {
            RateLimiter::hit($throttleKey, 60);
            $authCenterService->logLoginAttempt($authUser->id, $email, 'blocked', 'Sin acceso asignado al sistema de Agenda de Acuerdos', $request);

            return back()->withErrors([
                'email' => 'Su cuenta no tiene acceso asignado al sistema de Agenda de Acuerdos. Contacte al administrador.',
            ])->onlyInput('email');
        }

        // 3. Obtener usuario del modelo User
        $user = User::where('id', $authUser->id)
            ->orWhere('email', $email)
            ->first();

        if (!$user) {
            RateLimiter::hit($throttleKey, 60);
            $authCenterService->logLoginAttempt($authUser->id, $email, 'failed', 'Usuario no encontrado en modelo User', $request);

            return back()->withErrors([
                'email' => 'Usuario no encontrado en el sistema.',
            ])->onlyInput('email');
        }

        // 4. Limpiar contador de intentos fallidos al tener éxito
        RateLimiter::clear($throttleKey);

        Auth::login($user, $remember);
        $request->session()->regenerate();

        $authCenterService->logLoginAttempt($authUser->id, $email, 'success', 'Inicio de sesión exitoso', $request);

        return redirect()->intended('dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    public function showChangePassword()
    {
        return view('auth.change-password');
    }

    public function updateChangePassword(Request $request)
    {
        $request->validate([
            'password' => ['required', 'min:8', 'confirmed'],
        ], [
            'password.required' => 'La nueva contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
        ]);

        $user = Auth::user();

        // 1. Actualizar en Auth Center
        \App\Models\AuthCenter\AuthUser::where('email', $user->email)->update([
            'password' => \Illuminate\Support\Facades\Hash::make($request->password),
            'password_changed_at' => now(),
            'failed_login_attempts' => 0,
            'locked_until' => null,
            'must_change_password' => 0,
        ]);

        // 2. Actualizar sesión local
        $user->must_change_password = 0;
        $user->save();

        return redirect()->route('dashboard')->with('success', 'Contraseña actualizada correctamente.');
    }
}