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
        $query = User::with('roles')->has('roles')->where('is_active', 1);

        if ($request->has('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->search . '%')
                  ->orWhere('email', 'LIKE', '%' . $request->search . '%');
            });
        }

        $users = $query->orderBy('name')->paginate(10);
        $roles = Role::all();
        
        // Users from ticket system without access to agenda and are active
        $availableUsers = User::where('is_active', 1)->doesntHave('roles')->orderBy('name')->get();

        return view('users.index', compact('users', 'roles', 'availableUsers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'role' => 'required|string'
        ]);

        $user = User::findOrFail($request->user_id);
        $user->assignRole($request->role);

        return response()->json(['success' => true]);
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|string'
        ]);

        $user->syncRoles([$request->role]);

        return response()->json(['success' => true]);
    }

    public function toggleStatus(User $user)
    {
        $user->syncRoles([]);
        return response()->json(['success' => true, 'is_active' => false]);
    }

    public function destroy(User $user)
    {
        $user->syncRoles([]);
        return response()->json(['success' => true]);
    }
}
