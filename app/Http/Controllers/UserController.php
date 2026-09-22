<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Area;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('arochi-only');
    }

    public function index(Request $request)
    {
        return redirect()->route('roles-permisos.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'role' => 'required|string'
        ]);

        $user = User::findOrFail($request->user_id);
        $user->syncRoles([$request->role]);

        (new \App\Services\AuthCenterService())->grantSystemAccess($user->id, Auth::id(), 'agenda_acuerdos');

        return response()->json(['success' => true]);
    }

    public function update(Request $request, User $user)
    {
        if ($user->email === 'soporte@mapetzin.com') {
            return response()->json(['success' => false, 'message' => 'No es posible modificar al Super Administrador.'], 422);
        }

        $request->validate([
            'role' => 'required|string'
        ]);

        $user->syncRoles([$request->role]);

        (new \App\Services\AuthCenterService())->grantSystemAccess($user->id, Auth::id(), 'agenda_acuerdos');

        return response()->json(['success' => true]);
    }

    public function toggleStatus(User $user)
    {
        if ($user->email === 'soporte@mapetzin.com') {
            return response()->json(['success' => false, 'message' => 'No es posible desactivar al Super Administrador.'], 422);
        }

        $user->syncRoles([]);
        (new \App\Services\AuthCenterService())->revokeSystemAccess($user->id, 'agenda_acuerdos');

        return response()->json(['success' => true, 'is_active' => false]);
    }

    public function destroy(User $user)
    {
        if ($user->email === 'soporte@mapetzin.com') {
            return response()->json(['success' => false, 'message' => 'No es posible eliminar al Super Administrador.'], 422);
        }

        $user->syncRoles([]);
        (new \App\Services\AuthCenterService())->revokeSystemAccess($user->id, 'agenda_acuerdos');

        return response()->json(['success' => true]);
    }
}
