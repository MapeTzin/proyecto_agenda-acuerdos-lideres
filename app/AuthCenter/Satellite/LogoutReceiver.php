<?php

declare(strict_types=1);

namespace AuthCenter\Satellite;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LogoutReceiver
{
    public function __construct(private SessionStore $sessions, private string $clientId, #[\SensitiveParameter] private string $secret) {}

    public function receive(Request $request): void
    {
        $body = $request->getContent();
        abort_unless(strlen($this->secret) >= 32 && strlen($body) <= 4096
            && hash_equals($this->secret, (string) $request->bearerToken())
            && hash_equals(hash_hmac('sha256', $body, $this->secret), (string) $request->header('X-AuthCenter-Signature')), 401);
        try { $event = json_decode($body, true, 16, JSON_THROW_ON_ERROR); }
        catch (\JsonException) { abort(400); }
        abort_unless(is_array($event) && Str::isUuid($event['event_id'] ?? '') && ($event['aud'] ?? null) === $this->clientId
            && ($event['action'] ?? '') === 'slo_logout' && is_int($event['user_id'] ?? null) && $event['user_id'] > 0
            && is_int($event['timestamp'] ?? null) && is_int($event['expires_at'] ?? null)
            && $event['timestamp'] <= now()->timestamp + 30 && $event['timestamp'] >= now()->timestamp - 120
            && $event['expires_at'] > now()->timestamp && $event['expires_at'] <= $event['timestamp'] + 120
            && is_string($event['revoked_before'] ?? null), 401);
        abort_unless(SessionStore::instant($event['revoked_before']) <= now()->utc()->addSeconds(30), 401);
        $this->sessions->logout($event);
    }
}
