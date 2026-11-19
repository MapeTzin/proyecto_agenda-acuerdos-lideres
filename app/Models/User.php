<?php

namespace App\Models;

use App\Models\AuthCenter\Permission;
use App\Models\AuthCenter\Role;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $connection = 'mysql';
    protected $table = 'auth_center.users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'department_id',
        'puesto_id',
        'must_change_password',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $appends = ['has_access', 'area', 'additional_area_ids'];

    protected static $departmentCache = [];
    protected static $puestoCache = [];

    public static function mapDepartmentToArea(?string $rawDepartment, ?string $email = null): string
    {
        if (!$rawDepartment) {
            return 'SIN ÁREA';
        }

        $deptUpper = mb_strtoupper(trim($rawDepartment), 'UTF-8');

        $map = [
            'SISTEMAS Y TECNOLOGÍA' => 'SISTEMAS Y TI',
            'SISTEMAS Y TECNOLOGIA' => 'SISTEMAS Y TI',
            'SISTEMAS Y TI' => 'SISTEMAS Y TI',
            'CAPITAL HUMANO' => 'CAPITAL HUMANO',
            'RECURSOS HUMANOS' => 'CAPITAL HUMANO',
            'JEFATURA DE STAFF' => 'JEFATURA DE STAFF',
            'ASIS ADMIN' => 'ASIS ADMIN',
            'ASIS ADM' => 'ASIS ADMIN',
            'ADMINISTRACIÓN' => 'ASIS ADMIN',
            'ADMINISTRACION' => 'ASIS ADMIN',
            'DOCUMENTAL' => 'ASIS ADMIN',
            'COMERCIAL' => 'COMERCIAL',
            'VENTAS' => 'VENTAS',
            'LICITACIONES' => 'VENTAS',
            'ALMACEN' => 'ALMACEN',
            'ALMACÉN' => 'ALMACEN',
            'EMBARQUES' => 'ALMACEN',
            'EMBARQUES2' => 'ALMACEN',
            'CONTRA-ETIQUETADO' => 'ALMACEN',
            'CONTABILIDAD' => 'CONTABILIDAD',
            'CUENTAS POR COBRAR' => 'CUENTAS POR COBRAR',
            'DIRECCIÓN' => 'DIRECCION GENERAL',
            'DIRECCION' => 'DIRECCION GENERAL',
            'DIRECCION GENERAL' => 'DIRECCION GENERAL',
            'CALIDAD' => 'ASEGURAMIENTO DE CALIDAD',
            'ASEGURAMIENTO DE CALIDAD' => 'ASEGURAMIENTO DE CALIDAD',
            'ADQUISICIONES Y SERVICIOS' => 'ADQUISICIONES',
            'ADQUISICIONES' => 'ADQUISICIONES',
            'COMPRAS' => 'ADQUISICIONES',
            'PROYECTO ISSSTE' => 'JEFATURA DE STAFF',
        ];

        return $map[$deptUpper] ?? $deptUpper;
    }

    public function getAreaAttribute()
    {
        $rawArea = $this->attributes['department'] ?? $this->attributes['departamento'] ?? null;

        if (!$rawArea && !empty($this->department_id)) {
            if (!isset(self::$departmentCache[$this->department_id])) {
                self::$departmentCache[$this->department_id] = DB::connection('mysql_auth')
                    ->table('departments')
                    ->whereNull('deleted_at')
                    ->where('id', $this->department_id)
                    ->value('name');
            }
            $rawArea = self::$departmentCache[$this->department_id];
        }

        return self::mapDepartmentToArea($rawArea, $this->email);
    }

    public function additionalAreas(): Collection
    {
        return collect();
    }

    public function getAdditionalAreasAttribute(): Collection
    {
        return collect();
    }

    public function getAdditionalAreaIdsAttribute(): array
    {
        return [];
    }

    public function getAreasAttribute()
    {
        $areas = [];
        $primary = $this->area;
        if ($primary && $primary !== 'SIN ÁREA' && $primary !== 'Sin Área') {
            $areas[] = $primary;
        }

        if (in_array('CAPITAL HUMANO', $areas, true)) {
            $areas[] = 'CULTURA ORGANIZACIONAL';
        }

        if (in_array('CONTABILIDAD', $areas, true)) {
            $areas[] = 'CUENTAS POR COBRAR';
            $areas[] = 'CONTABILIDAD Y CXC';
        } elseif (in_array('CUENTAS POR COBRAR', $areas, true)) {
            $areas[] = 'CONTABILIDAD Y CXC';
        }

        return !empty($areas) ? array_values(array_unique($areas)) : [$primary];
    }

    public function getPositionAttribute()
    {
        if (!empty($this->attributes['position'])) {
            return $this->attributes['position'];
        }
        if (!empty($this->puesto_id)) {
            if (!array_key_exists($this->puesto_id, self::$puestoCache)) {
                self::$puestoCache[$this->puesto_id] = DB::connection('mysql_auth')
                    ->table('puestos')
                    ->where('id', $this->puesto_id)
                    ->value('nombre');
            }
            return self::$puestoCache[$this->puesto_id] ?: 'Sin Puesto';
        }
        return 'Sin Puesto';
    }

    public function department()
    {
        return $this->belongsTo(\App\Models\AuthCenter\Department::class, 'department_id');
    }

    public function getAreaColorAttribute()
    {
        $areaName = $this->area;
        $area = \App\Models\Area::where('name', $areaName)->first();
        return $area->color ?? '#64748b';
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'auth_center.user_system_access', 'user_id', 'role_id')
            ->wherePivot('system_id', 8)
            ->wherePivot('is_active', 1);
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'auth_center.user_permissions', 'user_id', 'permission_id')
            ->wherePivot('system_id', 8)
            ->wherePivot('is_allowed', 1);
    }

    public function getHasAccessAttribute(): bool
    {
        return $this->roles()->exists();
    }

    public function events()
    {
        return $this->hasMany(Event::class, 'created_by');
    }

    public function isSuperAdmin(): bool
    {
        if ($this->email === 'soporte@mapetzin.com') {
            return true;
        }

        $rolesCollection = $this->relationLoaded('roles')
            ? $this->roles
            : $this->roles()->get();

        foreach ($rolesCollection as $role) {
            $nameLower = mb_strtolower(trim((string) ($role->name ?? '')), 'UTF-8');
            $codeLower = mb_strtolower(trim((string) ($role->code ?? '')), 'UTF-8');
            if ($nameLower === 'super administrador' || $codeLower === 'superadministrador') {
                return true;
            }
        }

        return false;
    }

    public function hasRole($roles, ?string $guard = null): bool
    {
        if ($this->email === 'soporte@mapetzin.com') {
            return true;
        }

        $roleList = is_array($roles) ? $roles : [$roles];
        $requested = array_map(function ($r) {
            if (is_object($r)) {
                return [
                    mb_strtolower(trim((string) ($r->name ?? '')), 'UTF-8'),
                    mb_strtolower(trim((string) ($r->code ?? '')), 'UTF-8'),
                ];
            }
            $val = mb_strtolower(trim((string) $r), 'UTF-8');
            return [$val, $val];
        }, $roleList);

        if ($this->isSuperAdmin()) {
            foreach ($requested as [$rName, $rCode]) {
                if (in_array($rName, ['administrador', 'super administrador', 'superadministrador'], true)
                    || in_array($rCode, ['administrador', 'super administrador', 'superadministrador'], true)) {
                    return true;
                }
            }
        }

        $rolesCollection = $this->relationLoaded('roles')
            ? $this->roles
            : $this->roles()->get();

        $userIdentifiers = [];
        foreach ($rolesCollection as $ur) {
            if (!empty($ur->name)) {
                $userIdentifiers[] = mb_strtolower(trim((string) $ur->name), 'UTF-8');
            }
            if (!empty($ur->code)) {
                $userIdentifiers[] = mb_strtolower(trim((string) $ur->code), 'UTF-8');
            }
        }

        foreach ($requested as [$rName, $rCode]) {
            if (($rName !== '' && in_array($rName, $userIdentifiers, true))
                || ($rCode !== '' && in_array($rCode, $userIdentifiers, true))) {
                return true;
            }
        }

        return false;
    }

    public function hasAnyRole(...$roles): bool
    {
        $flat = [];
        foreach ($roles as $r) {
            if (is_array($r)) {
                $flat = array_merge($flat, $r);
            } else {
                $flat[] = $r;
            }
        }
        return $this->hasRole($flat);
    }

    public function hasPermissionTo($permission, ?string $guardName = null): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        $permName = is_object($permission) ? ($permission->name ?? $permission->code ?? '') : (string) $permission;
        if ($permName === '') {
            return false;
        }

        $direct = DB::connection('mysql_auth')->table('user_permissions as up')
            ->join('permissions as p', 'p.id', '=', 'up.permission_id')
            ->where('up.user_id', $this->id)
            ->where('up.system_id', 8)
            ->where(function ($q) use ($permName) {
                $q->where('p.name', $permName)->orWhere('p.code', $permName);
            })
            ->value('up.is_allowed');

        if ($direct !== null) {
            return (bool) $direct;
        }

        return DB::connection('mysql_auth')->table('user_system_access as usa')
            ->join('role_permissions as rp', 'rp.role_id', '=', 'usa.role_id')
            ->join('permissions as p', 'p.id', '=', 'rp.permission_id')
            ->where('usa.user_id', $this->id)
            ->where('usa.system_id', 8)
            ->where('usa.is_active', 1)
            ->where(function ($q) use ($permName) {
                $q->where('p.name', $permName)->orWhere('p.code', $permName);
            })
            ->exists();
    }

    public function syncRoles(array $roleNames): void
    {
        if (empty($roleNames)) {
            DB::connection('mysql_auth')->table('user_system_access')
                ->where('user_id', $this->id)
                ->where('system_id', 8)
                ->update(['is_active' => 0, 'revoked_at' => now(), 'updated_at' => now()]);
            return;
        }

        $first = reset($roleNames);
        $roleId = is_object($first)
            ? $first->id
            : Role::query()->where('name', (string) $first)->value('id');

        if ($roleId) {
            DB::connection('mysql_auth')->table('user_system_access')->updateOrInsert(
                ['user_id' => $this->id, 'system_id' => 8],
                [
                    'role_id' => $roleId,
                    'is_active' => 1,
                    'assigned_by' => auth()->id() ?? 1,
                    'assigned_at' => now(),
                    'revoked_at' => null,
                    'updated_at' => now(),
                ]
            );
        }
    }

    public function syncPermissions(array $permNames): void
    {
        DB::connection('mysql_auth')->table('user_permissions')
            ->where('user_id', $this->id)
            ->where('system_id', 8)
            ->delete();

        if (empty($permNames)) {
            return;
        }

        $permIds = Permission::query()
            ->where(function ($q) use ($permNames) {
                $q->whereIn('name', $permNames)->orWhereIn('code', $permNames);
            })
            ->pluck('id')
            ->all();

        $now = now();
        foreach ($permIds as $pid) {
            DB::connection('mysql_auth')->table('user_permissions')->insert([
                'user_id' => $this->id,
                'system_id' => 8,
                'permission_id' => $pid,
                'is_allowed' => 1,
                'created_by' => auth()->id() ?? 1,
                'updated_by' => auth()->id() ?? 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}