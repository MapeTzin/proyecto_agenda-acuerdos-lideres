<?php

namespace App\Models\AuthCenter;

use Illuminate\Database\Eloquent\Model;

class UserSystemAccess extends Model
{
    protected $connection = 'mysql_auth';
    protected $table = 'user_system_access';

    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
        'assigned_at' => 'datetime',
        'revoked_at' => 'datetime',
    ];
}