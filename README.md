# Next Engine Provider for OAuth 2.0 Client
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/w-takumi/oauth2-next-engine/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/w-takumi/oauth2-next-engine/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/w-takumi/oauth2-next-engine/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/w-takumi/oauth2-next-engine/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/w-takumi/oauth2-next-engine/badges/build.png?b=master)](https://scrutinizer-ci.com/g/w-takumi/oauth2-next-engine/build-status/master)
[![Build Status](https://travis-ci.org/w-takumi/oauth2-next-engine.svg?branch=master)](https://travis-ci.org/w-takumi/oauth2-next-engine)

This package provides Resource [Next Engine](http://next-engine.net/) OAuth 2.0 support for the PHP League’s [OAuth 2.0 Client](https://github.com/thephpleague/oauth2-client).

## Installation

To install, use composer:

```
composer require w-takumi/oauth2-next-engine
```

## Usage

Usage is the same as The League’s OAuth client, using `\Shippinno\NextEngine\OAuth2\Client\Provider\NextEngineProvider` as the provider.

### Authorization Code Flow

```php
<?php

$provider = new \Shippinno\NextEngine\OAuth2\Client\Provider\NextEngineProvider([
    'clientId'          => '{next-engine-client-id}',
    'clientSecret'      => '{next-engine-lient-secret}',
    'redirectUri'       => 'https://example.com/callback',
]);

if (!isset($_GET['uid']) || !isset($_GET['state'])) {
    $authUrl = $provider->getAuthorizationUrl();
    header('Location: '.$authUrl);
    exit;
} else {
    $token = $nextEngineProvider->getAccessToken('client_credentials', [
        'uid' => $_GET['uid'],
        'state' => $_GET['state']
    ]);

    try {
        $user = $provider->getResourceOwner($token);
        printf('Hello %s!', $user->getCompanyName());
    } catch (Exception $e) {
        exit('Oh dear...');
    }
    
    echo $token->getToken();
}

```
