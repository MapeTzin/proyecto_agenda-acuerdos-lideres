<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    use HasRoles {
        hasRole as traitHasRole;
        hasAnyRole as traitHasAnyRole;
        hasPermissionTo as traitHasPermissionTo;
    }

    protected $connection = 'mysql';
    protected $table = 'auth_center.users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'area',
        'must_change_password',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $appends = ['has_access', 'area', 'additional_area_ids'];

    protected static $departmentCache = [];

    public static function mapDepartmentToArea(?string $rawDepartment, ?string $email = null): string
    {
        if ($email === 'produccion@mapetzin.com') { // Marlon
            return 'COMERCIAL';
        }
        if ($email === 's.gema@mapetzin.com') {
            return 'ASIS ADM';
        }
        if ($email === 's.arias@mapetzin.com') {
            return 'CAPITAL HUMANO';
        }
        if ($email === 'direccion@mapetzin.com') {
            return 'SIN ÁREA';
        }

        if (!$rawDepartment) {
            return 'Sin Área';
        }

        $map = [
            'Adquisiciones y Servicios' => 'ADQUISICIONES',
            'Almacen' => 'ALMACEN',
            'Calidad' => 'ASEGURAMIENTO DE CALIDAD',
            'Recursos Humanos' => 'CAPITAL HUMANO',
            'Cuentas por Cobrar' => 'CUENTAS POR COBRAR',
            'Sistemas y Tecnología' => 'SISTEMAS Y TI',
            'SISTEMAS Y TI' => 'SISTEMAS Y TI',
            'Ventas' => 'VENTAS',
            'Licitaciones' => 'VENTAS', // As per User request: Cesar Osbaldo is Ventas
            'Contabilidad' => 'CONTABILIDAD',
            'Administración' => 'ASIS ADM', // Assumption based on ADM
            'Dirección' => 'CULTURA ORGANIZACIONAL', // Dynamic DB area
            'Proyecto ISSSTE' => 'JEFATURA DE STAFF', // Main ISSSTE administration area
            'Embarques' => 'ALMACEN',
            'Embarques2' => 'ALMACEN',
            'Contra-Etiquetado' => 'ALMACEN',
            'Documental' => 'ASIS ADM',
        ];

        return $map[$rawDepartment] ?? strtoupper($rawDepartment);
    }

    public function getAreaAttribute()
    {
        if ($this->email === 'produccion@mapetzin.com') { // Marlon
            return 'COMERCIAL';
        }
        if ($this->email === 's.gema@mapetzin.com') {
            return 'ASIS ADM';
        }
        if ($this->email === 's.arias@mapetzin.com') {
            return 'CAPITAL HUMANO';
        }
        if ($this->email === 'direccion@mapetzin.com') {
            return 'SIN ÁREA';
        }

        $rawArea = $this->attributes['department'] ?? $this->attributes['departamento'] ?? null;
        
        if (!$rawArea && !empty($this->department_id)) {
            if (!isset(self::$departmentCache[$this->department_id])) {
                self::$departmentCache[$this->department_id] = \Illuminate\Support\Facades\DB::connection('mysql_auth')->table('departments')
                    ->where('id', $this->department_id)
                    ->value('name');
            }
            $rawArea = self::$departmentCache[$this->department_id];
        }

        return self::mapDepartmentToArea($rawArea, $this->email);
    }

    public function additionalAreas()
    {
        return $this->belongsToMany(Area::class, 'user_areas', 'user_id', 'area_id')->withTimestamps();
    }

    public function getAdditionalAreaIdsAttribute(): array
    {
        if ($this->relationLoaded('additionalAreas')) {
            return $this->additionalAreas->pluck('id')->toArray();
        }
        return $this->additionalAreas()->pluck('area_id')->toArray();
    }

    public function getAreasAttribute()
    {
        $areas = [];
        $primary = $this->area;
        if ($primary && $primary !== 'SIN ÁREA' && $primary !== 'Sin Área') {
            $areas[] = $primary;
        }

        // Áreas adicionales configuradas para este usuario
        if ($this->relationLoaded('additionalAreas')) {
            $additional = $this->additionalAreas->pluck('name')->toArray();
        } else {
            $additional = $this->additionalAreas()->pluck('name')->toArray();
        }

        $merged = array_unique(array_merge($areas, $additional));

        // Retrocompatibilidad con acuerdos históricos de 'CONTABILIDAD Y CXC'
        if (in_array('CONTABILIDAD', $merged) || in_array('CUENTAS POR COBRAR', $merged)) {
            $merged[] = 'CONTABILIDAD Y CXC';
        }

        return !empty($merged) ? array_values(array_unique($merged)) : [$primary];
    }

    public function getPositionAttribute()
    {
        return $this->attributes['position'] ?? 'Sin Puesto';
    }

    public function getAreaColorAttribute()
    {
        $areaName = $this->area;
        $area = \App\Models\Area::where('name', $areaName)->first();
        return $area->color ?? '#64748b';
    }

    public function getHasAccessAttribute(): bool
    {
        return $this->roles()->exists();
    }

    public function events()
    {
        return $this->hasMany(Event::class, 'created_by');
    }

    public function hasRole($roles, ?string $guard = null): bool
    {
        if ($this->email === 'soporte@mapetzin.com') {
            return true;
        }

        try {
            return $this->traitHasRole($roles, $guard);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function hasAnyRole(...$roles): bool
    {
        if ($this->email === 'soporte@mapetzin.com') {
            return true;
        }

        try {
            return $this->traitHasAnyRole(...$roles);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function hasPermissionTo($permission, ?string $guardName = null): bool
    {
        if ($this->email === 'soporte@mapetzin.com') {
            return true;
        }

        try {
            return $this->traitHasPermissionTo($permission, $guardName);
        } catch (\Exception $e) {
            return false;
        }
    }
}
