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
        $authCenterService = new AuthCenterService();
        $maxAttempts = 5;
        $lockoutSeconds = 60;

        // 1. Verificar si está bloqueado en Auth Center
        $userInAuth = $authCenterService->findUserByEmail($email);
        if ($userInAuth && $userInAuth->locked_until && now()->lt($userInAuth->locked_until)) {
            $seconds = now()->diffInSeconds($userInAuth->locked_until);
            if ($seconds > 0) {
                return back()
                    ->with('lockout_seconds', $seconds)
                    ->with('attempts', $userInAuth->failed_login_attempts ?? $maxAttempts)
                    ->with('max_attempts', $maxAttempts)
                    ->withErrors([
                        'email' => "La cuenta está bloqueada temporalmente por seguridad. Por favor espere a que termine el temporizador.",
                    ])
                    ->onlyInput('email');
            }
        }

        // 2. Verificar RateLimiter (máximo 5 intentos por minuto)
        if (RateLimiter::tooManyAttempts($throttleKey, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()
                ->with('lockout_seconds', $seconds)
                ->with('attempts', RateLimiter::attempts($throttleKey))
                ->with('max_attempts', $maxAttempts)
                ->withErrors([
                    'email' => "Demasiados intentos fallidos. Su acceso está bloqueado temporalmente.",
                ])
                ->onlyInput('email');
        }

        $authUser = $authCenterService->validateCredentials($email, $password);

        if (!$authUser) {
            RateLimiter::hit($throttleKey, $lockoutSeconds);
            $currentAttempts = RateLimiter::attempts($throttleKey);
            $retriesLeft = max(0, $maxAttempts - $currentAttempts);

            $authCenterService->logLoginAttempt(null, $email, 'failed', 'Credenciales inválidas o cuenta desactivada', $request);

            if ($currentAttempts >= $maxAttempts) {
                $seconds = RateLimiter::availableIn($throttleKey);
                return back()
                    ->with('lockout_seconds', $seconds)
                    ->with('attempts', $currentAttempts)
                    ->with('max_attempts', $maxAttempts)
                    ->with('retries_left', 0)
                    ->withErrors([
                        'email' => "Ha superado el número máximo de intentos ({$maxAttempts} de {$maxAttempts}). Su acceso ha sido bloqueado temporalmente.",
                    ])
                    ->onlyInput('email');
            }

            return back()
                ->with('attempts', $currentAttempts)
                ->with('max_attempts', $maxAttempts)
                ->with('retries_left', $retriesLeft)
                ->withErrors([
                    'email' => "Las credenciales no coinciden con nuestros registros. Intento {$currentAttempts} de {$maxAttempts} (te quedan {$retriesLeft} intento" . ($retriesLeft === 1 ? '' : 's') . ").",
                ])
                ->onlyInput('email');
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