<?php

use Dotenv\Dotenv;

require_once __DIR__ . '/../vendor/autoload.php';

session_start();

$dotenv = new Dotenv(__DIR__ . '/../');
$dotenv->load();

$provider = new WiderFunnel\OAuth2\Client\Provider\Optimizely([
    'clientId'                => getenv('OPTIMIZELY_CLIENT_ID'),    // The client ID assigned to you by the provider
    'clientSecret'            => getenv('OPTIMIZELY_CLIENT_SECRET'),   // The client password assigned to you by the provider
    'redirectUri'             =>  getenv('OPTIMIZELY_CALLBACK_URL')
]);

$token = isset($_SESSION['oauth2token']) ? $_SESSION['oauth2token'] : null;
$refreshed = false;

if (!is_null($token)) {
    $newAccessToken = $provider->getAccessToken('refresh_token', [
        'refresh_token' => $token->getRefreshToken()
    ]);
    $refreshed = true;
    $token = $newAccessToken;
}

include_once __DIR__ . '/templates/refresh.php';