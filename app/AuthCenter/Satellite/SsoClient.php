<?php

declare(strict_types=1);

namespace AuthCenter\Satellite;

use Illuminate\Session\Store;
use Illuminate\Support\Facades\Http;

class SsoClient
{
    public function __construct(private array $config)
    {
        foreach (['base_url', 'redirect_uri', 'api_base_url'] as $key) {
            if ($key === 'api_base_url' && empty($config[$key])) { continue; }
            $url = parse_url((string) ($config[$key] ?? ''));
            if (!$url || ($url['scheme'] ?? '') !== 'https' || empty($url['host']) || isset($url['user']) || isset($url['pass']) || isset($url['fragment'])) {
                throw new \RuntimeException('Explicit HTTPS SSO configuration required');
            }
        }
        if (empty($config['client_id']) || strlen((string) ($config['client_secret'] ?? '')) < 32) {
            throw new \RuntimeException('Independent client identity and secret required');
        }
    }

    public function begin(Store $session): string
    {
        $verifier = rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
        $state = bin2hex(random_bytes(32));
        $session->put('authcenter_attempt', ['state' => $state, 'verifier' => $verifier, 'expires' => now()->addMinutes(5)->timestamp]);
        return rtrim($this->config['base_url'], '/').'/login?'.http_build_query([
            'app_client_id' => $this->config['client_id'], 'return_to' => $this->config['redirect_uri'], 'state' => $state,
            'code_challenge' => rtrim(strtr(base64_encode(hash('sha256', $verifier, true)), '+/', '-_'), '='),
            'code_challenge_method' => 'S256',
        ]);
    }

    public function exchange(Store $session, string $ticket, string $state): array
    {
        $attempt = $session->pull('authcenter_attempt');
        abort_unless(is_array($attempt) && $attempt['expires'] > now()->timestamp
            && hash_equals($attempt['state'], $state) && preg_match('/^sso_[A-Za-z0-9]{64}$/D', $ticket), 401);
        $response = $this->request()->post($this->endpoint('exchange-token'), [
            'app_client_id' => $this->config['client_id'], 'token' => $ticket,
            'code_verifier' => $attempt['verifier'], 'redirect_uri' => $this->config['redirect_uri'],
        ]);
        abort_unless($response->successful() && $response->json('success') === true, 401);
        $data = $response->json('data');
        abort_unless(is_array($data) && ($data['app_client_id'] ?? null) === $this->config['client_id']
            && ($data['system_code'] ?? null) === $this->config['system_code']
            && is_int($data['user']['id'] ?? null) && $data['user']['id'] > 0
            && is_string($data['session_token'] ?? null) && preg_match('/^[A-Za-z0-9]{64}$/D', $data['session_token'])
            && is_string($data['authenticated_at'] ?? null) && is_string($data['expires_at'] ?? null), 401);
        $authenticated = SessionStore::instant($data['authenticated_at'] ?? '');
        $expires = SessionStore::instant($data['expires_at'] ?? '');
        abort_unless($authenticated <= now()->utc()->addSeconds(30) && $expires > now()->utc() && $expires > $authenticated, 401);
        return $data;
    }

    public function active(#[\SensitiveParameter] string $grant, bool $interaction = false): bool
    {
        if (!preg_match('/^[A-Za-z0-9]{64}$/D', $grant)) { return false; }
        $response = $this->request()->post($this->endpoint($interaction ? 'session-activity' : 'check-session'), ['app_client_id' => $this->config['client_id'], 'session_token' => $grant]);
        if ($response->status() === 401 || $response->status() === 422) { return false; }
        if (!$response->successful() || !is_bool($response->json('active'))) {
            throw new \RuntimeException('Central authentication unavailable');
        }
        return $response->json('active') === true;
    }

    public function loginUrl(): string { return rtrim($this->config['base_url'], '/').'/login'; }
    public function logoutUrl(): string { return rtrim($this->config['base_url'], '/').'/logout'; }

    public function logout(#[\SensitiveParameter] string $grant): bool
    {
        $response = $this->request()->post($this->endpoint('revoke-session'), ['app_client_id' => $this->config['client_id'], 'session_token' => $grant]);
        return $response->successful() && $response->json('revoked') === true;
    }

    private function endpoint(string $operation): string { return rtrim($this->config['api_base_url'] ?? $this->config['base_url'], '/').'/api/v1/auth/'.$operation; }

    private function request()
    {
        $request = Http::connectTimeout(2)->timeout(5)->withoutRedirecting()->acceptJson()->asJson()->withToken($this->config['client_secret']);
        if (!empty($this->config['ca'])) { $request = $request->withOptions(['verify' => $this->config['ca']]); }
        return $request;
    }
}
