<?php

use Dotenv\Dotenv;

require_once __DIR__ . '/../vendor/autoload.php';

session_start();

$dotenv = new Dotenv(__DIR__ . '/../');
$dotenv->load();

$provider = new WiderFunnel\OAuth2\Client\Provider\Optimizely([
    'clientId' => getenv('OPTIMIZELY_CLIENT_ID'),
    'clientSecret' => getenv('OPTIMIZELY_CLIENT_SECRET'),
    'redirectUri' => getenv('OPTIMIZELY_CALLBACK_URL'),
]);

$token = isset($_SESSION['oauth2token']) ? $_SESSION['oauth2token'] : null;

if (!$token) {
    if (!isset($_GET['code'])) {

        // If we don't have an authorization code then get one
        $authUrl = $provider->getAuthorizationUrl();
        $_SESSION['oauth2state'] = $provider->getState();
        include_once __DIR__ . '/templates/index.php';

// Check given state against previously stored one to mitigate CSRF attack
    } elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {

        unset($_SESSION['oauth2state']);
        exit('Invalid state!');

    } else {

        // Try to get an access token (using the authorization code grant)
        $token = $provider->getAccessToken('authorization_code', [
            'code' => $_GET['code'],
        ]);

        $_SESSION['oauth2token'] = $token;

        // Optional: Now you have a token you can look up a users profile data
        try {

            // We got an access token, let's now get the projects details
            $projects = $provider->getResourceOwner($token)->toArray();

            include_once __DIR__ . '/templates/project.php';

        } catch (Exception $e) {

            // Failed to get user details
            exit('Oh dear...');
        }
    }
} else {
    $projects = $provider->getResourceOwner($token)->toArray();
    include_once __DIR__ . '/templates/project.php';
}