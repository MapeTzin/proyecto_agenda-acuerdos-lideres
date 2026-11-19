<?php

declare(strict_types=1);

namespace AuthCenter\Satellite;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SessionStore
{
    public function __construct(private string $connection, private string $prefix = 'ac_sat_')
    {
        if (!preg_match('/^[a-z][a-z0-9_]{0,39}$/D', $prefix)) {
            throw new \RuntimeException('Invalid satellite session table prefix');
        }
        if ($connection === 'mysql_auth' || config('database.connections.'.$connection.'.database') === 'auth_center') {
            throw new \RuntimeException('Satellite session state must not write central identity tables');
        }
    }
    private function db() { return DB::connection($this->connection); }
    private function table(string $suffix): string { return $this->prefix.$suffix; }

    public static function instant(string $value): Carbon
    {
        abort_unless(preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}\.\d{6}Z$/D', $value), 401);
        $date = Carbon::parse($value)->utc();
        abort_unless($date->format('Y-m-d\TH:i:s.u\Z') === $value, 401);
        return $date;
    }

    public function register(array $grant): string
    {
        $userId = (int) $grant['user']['id']; $started = self::instant($grant['authenticated_at']); $expires = self::instant($grant['expires_at']);
        return $this->db()->transaction(function () use ($userId, $started, $expires): string {
            $subject = $this->subject($userId);
            abort_if($subject->revoked_before && $started->lessThanOrEqualTo(Carbon::parse($subject->revoked_before, 'UTC')), 401);
            abort_if($expires <= now()->utc(), 401);
            $handle = Str::random(64);
            $this->db()->table($this->table('sessions'))->insert(['id' => hash('sha256', $handle), 'user_id' => $userId,
                'authenticated_at' => $started->format('Y-m-d H:i:s.u'), 'expires_at' => $expires->format('Y-m-d H:i:s.u')]);
            return $handle;
        });
    }

    public function active(string $handle, int $userId): bool
    {
        return $this->db()->table($this->table('sessions'))->where('id', hash('sha256', $handle))->where('user_id', $userId)
            ->whereNull('revoked_at')->where('expires_at', '>', now()->utc()->format('Y-m-d H:i:s.u'))->exists();
    }

    private function subject(int $userId): object
    {
        $this->db()->table($this->table('subjects'))->insertOrIgnore(['user_id' => $userId]);
        return $this->db()->table($this->table('subjects'))->where('user_id', $userId)->lockForUpdate()->first();
    }

    public function logout(array $event): void
    {
        $cutoff = self::instant($event['revoked_before']);
        $semantic = hash('sha256', json_encode([$event['aud'], $event['user_id'], $event['revoked_before'], $event['action']], JSON_THROW_ON_ERROR));
        $this->db()->transaction(function () use ($event, $cutoff, $semantic): void {
            $subject = $this->subject($event['user_id']);
            $existing = $this->db()->table($this->table('events'))->where('id', $event['event_id'])->first();
            if ($existing) { abort_unless(hash_equals($existing->digest, $semantic), 409); return; }
            if (!$subject->revoked_before || $cutoff > Carbon::parse($subject->revoked_before, 'UTC')) {
                $this->db()->table($this->table('subjects'))->where('user_id', $event['user_id'])->update(['revoked_before' => $cutoff->format('Y-m-d H:i:s.u')]);
            }
            $this->db()->table($this->table('sessions'))->where('user_id', $event['user_id'])->whereNull('revoked_at')
                ->where('authenticated_at', '<=', $cutoff->format('Y-m-d H:i:s.u'))->update(['revoked_at' => now()->utc()->format('Y-m-d H:i:s.u')]);
            $this->db()->table($this->table('events'))->insert(['id' => $event['event_id'], 'digest' => $semantic, 'processed_at' => now()->utc()]);
        });
    }
}
