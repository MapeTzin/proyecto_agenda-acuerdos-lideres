<?php

namespace App\Models\AuthCenter;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class AuthUser extends Authenticatable
{
    use Notifiable, SoftDeletes;

    protected $connection = 'mysql_auth';
    protected $table = 'users';

    protected $guarded = [];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected static array $puestosCache = [];

    public function getPositionAttribute(): string
    {
        if (!empty($this->attributes['position'])) {
            return $this->attributes['position'];
        }
        if (!empty($this->puesto_id)) {
            if (!array_key_exists($this->puesto_id, self::$puestosCache)) {
                self::$puestosCache[$this->puesto_id] = \Illuminate\Support\Facades\DB::connection('mysql_auth')
                    ->table('puestos')
                    ->where('id', $this->puesto_id)
                    ->value('nombre');
            }
            return self::$puestosCache[$this->puesto_id] ?: 'Sin Puesto';
        }
        return 'Sin Puesto';
    }
}