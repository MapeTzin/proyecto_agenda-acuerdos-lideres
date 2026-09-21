<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\AuthCenterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        ]);

        $email = $credentials['email'];
        $password = $credentials['password'];
        $remember = $request->boolean('remember');

        $authCenterService = new AuthCenterService();
        $authUser = $authCenterService->validateCredentials($email, $password);

        if (!$authUser) {
            $authCenterService->logLoginAttempt(null, $email, 'failed', 'Credenciales inválidas o cuenta desactivada', $request);

            return back()->withErrors([
                'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros o la cuenta está desactivada.',
            ])->onlyInput('email');
        }

        // Verificar acceso en user_system_access para agenda_acuerdos
        if (!$authCenterService->userHasSystemAccess($authUser->id, 'agenda_acuerdos')) {
            $authCenterService->logLoginAttempt($authUser->id, $email, 'blocked', 'Sin acceso asignado al sistema de Agenda de Acuerdos', $request);

            return back()->withErrors([
                'email' => 'Su cuenta no tiene acceso asignado al sistema de Agenda de Acuerdos.',
            ])->onlyInput('email');
        }

        // Obtener usuario del modelo User (que ahora conecta a mysql_auth.users)
        $user = User::where('id', $authUser->id)
            ->orWhere('email', $email)
            ->first();

        if (!$user) {
            $authCenterService->logLoginAttempt($authUser->id, $email, 'failed', 'Usuario no encontrado en modelo User', $request);

            return back()->withErrors([
                'email' => 'Usuario no encontrado en el sistema.',
            ])->onlyInput('email');
        }

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
        return redirect()->route('dashboard')->with('info', 'La administración de contraseñas está centralizada en Auth Center.');
    }

    public function updatePassword(Request $request)
    {
        return redirect()->route('dashboard')->with('info', 'La administración de contraseñas está centralizada en Auth Center.');
    }
}