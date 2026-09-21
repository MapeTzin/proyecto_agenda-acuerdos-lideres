<?php

namespace App\Models\AuthCenter;

use Illuminate\Database\Eloquent\Model;

class LoginLog extends Model
{
    protected $connection = 'mysql_auth';
    protected $table = 'login_logs';

    protected $guarded = [];

    public $timestamps = false;
}