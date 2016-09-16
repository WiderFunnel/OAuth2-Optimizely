# Optimizely Provider for OAuth 2.0 Client
[![Packagist](https://img.shields.io/packagist/v/widerfunnel/OAuth2-Optimizely.svg?maxAge=2592000?style=flat-square)](https://packagist.org/packages/widerfunnel/oauth2-optimizely)
[![Travis](https://img.shields.io/travis/WiderFunnel-Labs/OAuth2-Optimizely.svg?maxAge=2592000?style=flat-square)](https://travis-ci.org/WiderFunnel-Labs/OAuth2-Optimizely)

This package provides Optimizely OAuth 2.0 support for the PHP League's [OAuth 2.0 Client](https://github.com/thephpleague/oauth2-client).

## Installation

To install, use composer:

```
composer require widerfunnel/oauth2-optimizely
```

## Usage

Usage is the same as The League's OAuth client, using `\WiderFunnel\OAuth2\Client\Provider\Optimizely` as the provider.

### Authorization Code Flow

**Note:** Optimizely does not allow access to the resource owner profile via their API currently. This will be updated 
accordingly, should the Optimizely API be updated.

```php
$provider = new WiderFunnel\OAuth2\Client\Provider\Optimizely([
    'clientId'          => '{optimizely-client-id}',
    'clientSecret'      => '{optimizely-client-secret}',
    'redirectUri'       => 'https://example.com/callback-url',
]);

if (!isset($_GET['code'])) {

    // If we don't have an authorization code then get one
    $authUrl = $provider->getAuthorizationUrl();
    $_SESSION['oauth2state'] = $provider->getState();
    header('Location: '.$authUrl);
    exit;

// Check given state against previously stored one to mitigate CSRF attack
} elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {

    unset($_SESSION['oauth2state']);
    exit('Invalid state');

} else {

    // Try to get an access token (using the authorization code grant)
    $token = $provider->getAccessToken('authorization_code', [
        'code' => $_GET['code']
    ]);

    // Optional: Now you have a token you can look up a users profile data
    try {

        // We got an access token, let's now get the projects details
        $project = $provider->getResourceOwner($token);        

    } catch (Exception $e) {

        // Failed to get user details
        exit('Oh dear...');
    }

    // Use this to interact with an API on the users behalf
    echo $token->getToken();
}
```

### Refresh token 

```
$provider = new WiderFunnel\OAuth2\Client\Provider\Optimizely([
    'clientId'          => '{optimizely-client-id}',
    'clientSecret'      => '{optimizely-client-secret}',
    'redirectUri'       => 'https://example.com/callback-url',
]);

$existingAccessToken = getAccessTokenFromYourDataStore();

if ($existingAccessToken->hasExpired()) {
    $newAccessToken = $provider->getAccessToken('refresh_token', [
        'refresh_token' => $existingAccessToken->getRefreshToken()
    ]);

    // Purge old access token and store new access token to your data store.
}
```

## Testing

``` bash
$ ./vendor/bin/phpunit
```

## Contributing

Please see [CONTRIBUTING](https://github.com/widerfunnel/oauth2-optimizely/blob/master/CONTRIBUTING.md) for details.


## Credits

- [WiderFunnel](https://github.com/widerfunnel)
- [All Contributors](https://github.com/widerfunnel/oauth2-optimizely/contributors)


## License

The MIT License (MIT). Please see [License File](https://github.com/widerfunnel/oauth2-optimizely/blob/master/LICENSE) for more information.
