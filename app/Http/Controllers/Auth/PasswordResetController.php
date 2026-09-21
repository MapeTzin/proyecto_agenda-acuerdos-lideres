<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuthCenter\AuthUser;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    /**
     * Muestra la vista para solicitar el enlace de recuperación de contraseña.
     */
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Envía el enlace con el token de recuperación al correo institucional.
     */
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ], [
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Por favor ingrese un formato de correo electrónico válido.',
        ]);

        $email = Str::lower(trim($request->email));

        // Verificar que el usuario exista en Auth Center
        $authUser = AuthUser::where('email', $email)->first();
        if (!$authUser) {
            return back()->withErrors([
                'email' => 'No encontramos ningún usuario registrado con ese correo electrónico.',
            ])->withInput();
        }

        if (!$authUser->is_active) {
            return back()->withErrors([
                'email' => 'La cuenta asociada a este correo se encuentra desactivada. Contacte al administrador.',
            ])->withInput();
        }

        // Generar token y enviar notificación mediante el broker centralizado
        $status = Password::broker()->sendResetLink(['email' => $email]);

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('status', 'Hemos enviado a tu correo el enlace de recuperación de contraseña.');
        }

        return back()->withErrors([
            'email' => __($status),
        ])->withInput();
    }

    /**
     * Muestra el formulario para ingresar y confirmar la nueva contraseña.
     */
    public function showResetForm(Request $request, ?string $token = null)
    {
        return view('auth.reset-password')->with([
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    /**
     * Procesa el cambio y restablecimiento definitivo de la contraseña.
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'min:8', 'confirmed'],
        ], [
            'token.required' => 'El token de seguridad es requerido.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Formato de correo electrónico inválido.',
            'password.required' => 'La nueva contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
        ]);

        $status = Password::broker()->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, string $password) {
                // 1. Actualizar credenciales en Auth Center (fuente de verdad centralizada)
                AuthUser::where('email', $user->email)->update([
                    'password' => Hash::make($password),
                    'password_changed_at' => now(),
                    'failed_login_attempts' => 0,
                    'locked_until' => null,
                    'must_change_password' => 0,
                ]);

                // 2. Actualizar remember token y disparar evento de Laravel
                $user->forceFill([
                    'password' => Hash::make($password),
                    'must_change_password' => 0,
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('status', 'Tu contraseña ha sido restablecida con éxito. Ya puedes iniciar sesión con tu nueva contraseña.');
        }

        return back()->withErrors([
            'email' => __($status),
        ])->withInput($request->only('email'));
    }
}
