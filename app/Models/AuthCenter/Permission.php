<?php

namespace App\Models\AuthCenter;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $connection = 'mysql_auth';
    protected $table = 'permissions';

    protected $fillable = [
        'system_id',
        'module',
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

        static::creating(function (Permission $perm) {
            $perm->system_id = $perm->system_id ?: 8;
            $perm->code = $perm->code ?: $perm->name;
            $perm->module = $perm->module ?: explode('.', (string) $perm->name)[0];
            $perm->is_active = $perm->is_active ?? 1;
        });
    }
}