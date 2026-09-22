<?php

namespace App\Services;

use App\Models\AuthCenter\AuthUser;
use App\Models\AuthCenter\UserSystemAccess;
use App\Models\AuthCenter\LoginLog;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class AuthCenterService
{
    public function findUserByEmail(string $email): ?AuthUser
    {
        try {
            return AuthUser::where('email', $email)->first();
        } catch (\Exception $e) {
            Log::error('AuthCenterService::findUserByEmail: ' . $e->getMessage());
            return null;
        }
    }

    public function validateCredentials(string $email, string $password): ?AuthUser
    {
        try {
            $user = $this->findUserByEmail($email);
            if ($user && $user->is_active && Hash::check($password, $user->password)) {
                return $user;
            }
            return null;
        } catch (\Exception $e) {
            Log::error('AuthCenterService::validateCredentials: ' . $e->getMessage());
            return null;
        }
    }

    public function userHasSystemAccess(int $authUserId, string $systemCode = 'agenda_acuerdos'): bool
    {
        try {
            return UserSystemAccess::join('systems', 'user_system_access.system_id', '=', 'systems.id')
                ->where('user_system_access.user_id', $authUserId)
                ->where('systems.code', $systemCode)
                ->where('systems.is_active', true)
                ->where('user_system_access.is_active', true)
                ->exists();
        } catch (\Exception $e) {
            Log::error('AuthCenterService::userHasSystemAccess: ' . $e->getMessage());
            return false;
        }
    }

    public function getSystemId(string $systemCode = 'agenda_acuerdos'): ?int
    {
        try {
            return \Illuminate\Support\Facades\DB::connection('mysql_auth')
                ->table('systems')
                ->where('code', $systemCode)
                ->value('id');
        } catch (\Exception $e) {
            Log::error('AuthCenterService::getSystemId: ' . $e->getMessage());
            return null;
        }
    }

    public function grantSystemAccess(int $authUserId, ?int $assignedBy = null, string $systemCode = 'agenda_acuerdos'): bool
    {
        try {
            $systemId = $this->getSystemId($systemCode);
            if (!$systemId) {
                Log::error("AuthCenterService::grantSystemAccess: Sistema {$systemCode} no encontrado.");
                return false;
            }

            UserSystemAccess::updateOrCreate(
                [
                    'user_id' => $authUserId,
                    'system_id' => $systemId,
                ],
                [
                    'is_active' => true,
                    'assigned_by' => $assignedBy,
                    'assigned_at' => now(),
                    'revoked_at' => null,
                ]
            );

            return true;
        } catch (\Exception $e) {
            Log::error('AuthCenterService::grantSystemAccess: ' . $e->getMessage());
            return false;
        }
    }

    public function revokeSystemAccess(int $authUserId, string $systemCode = 'agenda_acuerdos'): bool
    {
        try {
            $systemId = $this->getSystemId($systemCode);
            if (!$systemId) {
                return false;
            }

            UserSystemAccess::where('user_id', $authUserId)
                ->where('system_id', $systemId)
                ->update([
                    'is_active' => false,
                    'revoked_at' => now(),
                ]);

            return true;
        } catch (\Exception $e) {
            Log::error('AuthCenterService::revokeSystemAccess: ' . $e->getMessage());
            return false;
        }
    }

    public function logLoginAttempt(?int $userId, ?string $email, string $status, ?string $message, Request $request, string $systemCode = 'agenda_acuerdos'): void
    {
        try {
            $log               = new LoginLog();
            $log->user_id      = $userId;
            $log->email        = $email;
            $log->system_code  = $systemCode;
            $log->ip_address   = $request->ip();
            $log->user_agent   = substr($request->userAgent() ?? '', 0, 500);
            $log->status       = $status;
            $log->message      = $message;
            $log->created_at   = now();
            $log->save();
        } catch (\Exception $e) {
            Log::error('AuthCenterService::logLoginAttempt: ' . $e->getMessage());
        }
    }
}