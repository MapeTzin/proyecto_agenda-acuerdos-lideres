<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AuthCenter\AuthUser;
use App\Models\AuthCenter\UserSystemAccess;
use App\Services\AuthCenterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionController extends Controller
{
    protected AuthCenterService $authCenterService;

    public function __construct(AuthCenterService $authCenterService)
    {
        $this->middleware('arochi-only');
        $this->authCenterService = $authCenterService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $systemId = $this->authCenterService->getSystemId('agenda_acuerdos') ?? 8;

        // 1. Obtener estados de acceso en Auth Center para el sistema actual
        $authCenterAccess = DB::connection('mysql_auth')
            ->table('user_system_access')
            ->where('system_id', $systemId)
            ->get()
            ->keyBy('user_id');

        // 2. Consulta de usuarios con acceso (únicamente usuarios con roles asignados)
        $query = User::with(['roles', 'permissions'])
            ->where('is_active', 1)
            ->has('roles');

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $roleName = $request->role;
            $query->whereHas('roles', function ($q) use ($roleName) {
                $q->where('name', $roleName);
            });
        }

        $users = $query->orderBy('name')->paginate(20)->withQueryString();

        // Anexar estado de auth center a cada usuario paginado
        foreach ($users as $user) {
            $access = $authCenterAccess->get($user->id);
            $user->auth_center_active = $access ? (bool) $access->is_active : false;
            $user->auth_center_assigned_at = $access ? $access->assigned_at : null;
        }

        // 3. Roles con conteo de usuarios y permisos vinculados
        $roles = Role::with(['permissions'])
            ->withCount('users')
            ->orderBy('name')
            ->get();

        // 4. Catálogo de permisos agrupados por categoría
        $permissions = Permission::orderBy('name')->get();
        $permissionsGrouped = $this->groupPermissions($permissions);

        // 5. Usuarios disponibles de Auth Center que aún no tienen roles asignados
        $usersWithRolesIds = DB::table('model_has_roles')
            ->where('model_type', User::class)
            ->pluck('model_id')
            ->toArray();

        $departments = DB::connection('mysql_auth')->table('departments')->pluck('name', 'id');

        $availableAuthUsers = AuthUser::where('is_active', 1)
            ->whereNotIn('id', $usersWithRolesIds)
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'position', 'department_id'])
            ->map(function ($u) use ($departments) {
                $u->department_name = $departments[$u->department_id] ?? 'Sin Departamento';
                return $u;
            });

        $totalUsersWithAccess = User::where('is_active', 1)->has('roles')->count();

        return view('roles-permisos.index', compact(
            'users',
            'totalUsersWithAccess',
            'roles',
            'permissions',
            'permissionsGrouped',
            'availableAuthUsers'
        ));
    }

    /**
     * Otorga acceso a un usuario de Auth Center (Asigna rol y sincroniza user_system_access).
     */
    public function grantAccess(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer',
            'role' => 'required|string|exists:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|exists:permissions,name',
        ], [
            'user_id.required' => 'Debe seleccionar un usuario.',
            'role.required' => 'Debe seleccionar un rol.',
            'role.exists' => 'El rol seleccionado no es válido.',
        ]);

        $user = User::findOrFail($request->user_id);

        // 1. Asignar rol en Spatie
        $user->syncRoles([$request->role]);

        // 2. Asignar permisos directos opcionales
        if ($request->has('permissions')) {
            $user->syncPermissions($request->permissions);
        }

        // 3. Sincronizar en Auth Center (user_system_access)
        $synced = $this->authCenterService->grantSystemAccess($user->id, Auth::id(), 'agenda_acuerdos');

        // Limpiar caché de permisos
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        return response()->json([
            'success' => true,
            'message' => "Acceso otorgado exitosamente a {$user->name} con el rol {$request->role}." . ($synced ? ' Sincronizado con Auth Center.' : ''),
        ]);
    }

    /**
     * Actualiza los roles y permisos de un usuario existente.
     */
    public function updateUserAccess(Request $request, User $user)
    {
        if ($user->email === 'soporte@mapetzin.com') {
            return response()->json([
                'success' => false,
                'message' => 'No es posible modificar los roles o permisos de la cuenta de Super Administrador.',
            ], 422);
        }

        $request->validate([
            'role' => 'required|string|exists:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|exists:permissions,name',
        ], [
            'role.required' => 'El rol es obligatorio.',
            'role.exists' => 'El rol seleccionado no es válido.',
        ]);

        // 1. Actualizar roles
        $user->syncRoles([$request->role]);

        // 2. Actualizar permisos directos
        $user->syncPermissions($request->permissions ?? []);

        // 3. Asegurar estado activo en Auth Center
        $this->authCenterService->grantSystemAccess($user->id, Auth::id(), 'agenda_acuerdos');

        // Limpiar caché
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        return response()->json([
            'success' => true,
            'message' => "Roles y permisos actualizados correctamente para {$user->name}.",
        ]);
    }

    /**
     * Revoca el acceso de un usuario (remueve roles y desactiva en Auth Center).
     */
    public function revokeAccess(User $user)
    {
        if ($user->email === 'soporte@mapetzin.com') {
            return response()->json([
                'success' => false,
                'message' => 'No es posible revocar el acceso a la cuenta de Super Administrador.',
            ], 422);
        }

        // 1. Remover roles y permisos locales
        $user->syncRoles([]);
        $user->syncPermissions([]);

        // 2. Desactivar en Auth Center
        $this->authCenterService->revokeSystemAccess($user->id, 'agenda_acuerdos');

        // Limpiar caché
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        return response()->json([
            'success' => true,
            'message' => "Acceso revocado exitosamente para {$user->name}. Se ha sincronizado con Auth Center.",
        ]);
    }

    /**
     * Crea un nuevo Rol en el sistema con sus permisos asignados.
     */
    public function storeRole(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50|unique:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|exists:permissions,name',
        ], [
            'name.required' => 'El nombre del rol es obligatorio.',
            'name.unique' => 'Ya existe un rol con este nombre.',
        ]);

        $role = Role::create([
            'name' => trim($request->name),
            'guard_name' => 'web',
        ]);

        if (!empty($request->permissions)) {
            $role->syncPermissions($request->permissions);
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        return response()->json([
            'success' => true,
            'message' => "Rol '{$role->name}' creado correctamente.",
            'role' => $role,
        ]);
    }

    /**
     * Actualiza un rol existente y sus permisos asociados.
     */
    public function updateRole(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|max:50|unique:roles,name,' . $role->id,
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|exists:permissions,name',
        ], [
            'name.required' => 'El nombre del rol es obligatorio.',
            'name.unique' => 'Ya existe un rol con este nombre.',
        ]);

        $role->name = trim($request->name);
        $role->save();

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions ?? []);
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        return response()->json([
            'success' => true,
            'message' => "Rol '{$role->name}' actualizado correctamente.",
        ]);
    }

    /**
     * Elimina un rol personalizado (protege roles del sistema y roles en uso).
     */
    public function destroyRole(Role $role)
    {
        $systemRoles = ['Administrador', 'Director General', 'Usuario'];
        if (in_array($role->name, $systemRoles)) {
            return response()->json([
                'success' => false,
                'message' => "El rol '{$role->name}' es un rol predeterminado del sistema y no puede eliminarse.",
            ], 422);
        }

        if ($role->users()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => "No se puede eliminar el rol '{$role->name}' porque tiene {$role->users()->count()} usuario(s) asignado(s).",
            ], 422);
        }

        $roleName = $role->name;
        $role->delete();

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        return response()->json([
            'success' => true,
            'message' => "Rol '{$roleName}' eliminado correctamente.",
        ]);
    }

    /**
     * Crea un permiso nuevo individual en el sistema.
     */
    public function storePermission(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:60|unique:permissions,name',
        ], [
            'name.required' => 'El identificador del permiso es obligatorio.',
            'name.unique' => 'Ya existe un permiso con este identificador.',
        ]);

        $permission = Permission::create([
            'name' => trim($request->name),
            'guard_name' => 'web',
        ]);

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        return response()->json([
            'success' => true,
            'message' => "Permiso '{$permission->name}' registrado con éxito.",
        ]);
    }

    /**
     * Sincroniza los permisos base del sistema ejecutando el seeder de permisos.
     */
    public function syncSystemPermissions()
    {
        try {
            $seeder = new \Database\Seeders\RolesAndPermissionsSeeder();
            $seeder->run();

            return response()->json([
                'success' => true,
                'message' => 'Catálogo de permisos y roles del sistema sincronizados con éxito.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al sincronizar permisos: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Helper para clasificar permisos por módulos.
     */
    protected function groupPermissions($permissions)
    {
        $groups = [
            'Dashboard y Seguimiento' => [],
            'KPIs de Áreas' => [],
            'Gestión de Acuerdos' => [],
            'Planeador' => [],
            'Seguridad y Accesos' => [],
            'Otros' => [],
        ];

        foreach ($permissions as $perm) {
            if (str_starts_with($perm->name, 'dashboard') || str_starts_with($perm->name, 'seguimiento')) {
                $groups['Dashboard y Seguimiento'][] = $perm;
            } elseif (str_starts_with($perm->name, 'kpi')) {
                $groups['KPIs de Áreas'][] = $perm;
            } elseif (str_starts_with($perm->name, 'acuerdo')) {
                $groups['Gestión de Acuerdos'][] = $perm;
            } elseif (str_starts_with($perm->name, 'planeador') || str_starts_with($perm->name, 'event')) {
                $groups['Planeador'][] = $perm;
            } elseif (str_starts_with($perm->name, 'user') || str_starts_with($perm->name, 'role') || str_starts_with($perm->name, 'permission')) {
                $groups['Seguridad y Accesos'][] = $perm;
            } else {
                $groups['Otros'][] = $perm;
            }
        }

        return array_filter($groups, fn($list) => count($list) > 0);
    }
}
