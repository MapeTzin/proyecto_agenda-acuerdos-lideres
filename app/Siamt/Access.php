<?php
declare(strict_types=1);
namespace App\Siamt;

class Access
{
    public static function allowed($user): bool
    {
        if (!$user || !$user->is_active) { return false; }
        if ((int) $user->id === 1) { return true; }
        return \Illuminate\Support\Facades\DB::connection(config('services.auth_center.identity_connection'))
            ->table('user_system_access')->where('user_id', $user->id)
            ->where('system_id', config('services.auth_center.system_id'))->where('is_active', 1)->exists();
    }
}
