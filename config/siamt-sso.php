<?php
return [
    'enabled' => filter_var(env('SIAMT_SSO_ENABLED', false), FILTER_VALIDATE_BOOLEAN),
    'base_url' => 'https://siamt.mt.local',
    'api_base_url' => 'https://authcenter.mt.local',
    'client_id' => env('AUTH_CENTER_CLIENT_ID'),
    'system_code' => env('AUTH_SYSTEM_CODE'),
    'system_id' => (int) env('AUTH_SYSTEM_ID'),
    'identity_connection' => env('AUTH_IDENTITY_CONNECTION'),
    'client_secret' => env('AUTH_CENTER_CLIENT_SECRET'),
    'redirect_uri' => env('AUTH_CENTER_REDIRECT_URI'),
    'ca' => '/etc/authcenter-public/hub-web-ca.pem',
];
