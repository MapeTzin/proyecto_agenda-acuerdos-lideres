<?php

namespace App\Models\AuthCenter;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Role extends Model
{
    protected $connection = 'mysql_auth';
    protected $table = 'roles';

    protected $fillable = [
        'system_id',
        'code',
        'name',
        'description',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope('agenda_system', function (Builder $builder) {
            $builder->where($builder->getModel()->qualifyColumn('system_id'), 8);
        });

        static::creating(function (Role $role) {
            $role->system_id = $role->system_id ?: 8;
            $role->code = $role->code ?: Str::slug((string) $role->name, '_');
            $role->is_active = $role->is_active ?? 1;
        });
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permissions', 'role_id', 'permission_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_system_access', 'role_id', 'user_id')
            ->wherePivot('system_id', 8)
            ->wherePivot('is_active', 1);
    }

    public function syncPermissions(array $permNames): void
    {
        $permIds = Permission::query()
            ->where(function ($q) use ($permNames) {
                $q->whereIn('name', $permNames)->orWhereIn('code', $permNames);
            })
            ->pluck('id')
            ->all();

        DB::connection('mysql_auth')->table('role_permissions')->where('role_id', $this->id)->delete();
        $now = now();
        foreach ($permIds as $pid) {
            DB::connection('mysql_auth')->table('role_permissions')->insert([
                'role_id' => $this->id,
                'permission_id' => $pid,
                'created_by' => auth()->id() ?? 1,
                'updated_by' => auth()->id() ?? 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}