<?php
declare(strict_types=1);
namespace App\Siamt;

use AuthCenter\Satellite\LogoutReceiver;
use AuthCenter\Satellite\SessionStore;
use AuthCenter\Satellite\SsoClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Controller extends \App\Http\Controllers\Controller
{
    public function start(Request $request, SsoClient $client)
    {
        if (Auth::check()) { return redirect()->route('dashboard'); }
        return redirect()->away($client->begin($request->session()));
    }

    public function callback(Request $request, SsoClient $client, SessionStore $sessions)
    {
        $input = $request->validate(['sso_token' => 'required|string|max:100', 'state' => 'required|string|max:128']);
        $grant = $client->exchange($request->session(), $input['sso_token'], $input['state']);
        $user = \App\Models\User::find($grant['user']['id']);
        abort_unless(Access::allowed($user), 403);
        $handle = $sessions->register($grant);
        Auth::login($user, false);
        $request->session()->regenerate();
        $request->session()->put([
            'sso_grant_token' => $grant['session_token'],
            'sso_local_handle' => $handle,
            'auth_center_user_id' => $user->id,
            'user_name' => $user->name ?? '',
            'user_email' => $user->email ?? '',
            'user_position' => $user->position ?? null,
            'logged_in' => true,
        ]);
        if (class_exists(\App\Models\AccessUser::class)) {
            $accessUser = \App\Models\AccessUser::where('external_user_id', $user->id)->first();
            $permissions = $accessUser ? $accessUser->getEffectivePermissions()->pluck('code')->toArray() : [];
            $request->session()->put([
                'external_user_id' => $user->id,
                'user_department' => $user->department_id ?? null,
                'role_id' => $accessUser?->role_id,
                'role_name' => $accessUser?->role?->name,
                'role_code' => $accessUser?->role?->code,
                'user_permissions' => $permissions,
            ]);
        }
        return redirect()->route('dashboard');
    }

    public function destroy(Request $request, SsoClient $client)
    {
        $confirmed = false;
        try { $confirmed = $client->logout((string) $request->session()->get('sso_grant_token')); }
        catch (\Throwable $error) { report(new \RuntimeException('SIAMT global logout could not be confirmed')); }
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        if ($confirmed) { return redirect()->away($client->loginUrl().'?logout=1'); }
        return response('La sesión local se cerró. Revisa el cierre global en SIAMT.', 503)->header('Cache-Control', 'no-store');
    }

    public function receive(Request $request, SessionStore $sessions)
    {
        $config = config('services.auth_center');
        (new LogoutReceiver($sessions, $config['client_id'], $config['client_secret']))->receive($request);
        return response()->json(['accepted' => true]);
    }
}
