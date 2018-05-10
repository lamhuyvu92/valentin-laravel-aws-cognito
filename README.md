pmill/laravel-aws-cognito
=================

Introduction
------------

This library contains a Laravel guard and authentication implementation for AWS Cognito user pools.

Requirements
------------

This library package requires PHP 7.0 or later

Installation
------------

### Installing via Composer

The recommended way to install is through
[Composer](http://getcomposer.org).

```bash
# Install Composer
curl -sS https://getcomposer.org/installer | php
```

Next, run the Composer command to install the latest version:

```bash
composer.phar require lamhuyvu92/laravel-aws-cognito
```

Usage
-----

Add the service provider to the providers array in `config/app.php`.

```php
'providers' => [
    ...
    lamhuyvu92\LaravelAwsCognito\ServiceProvider::class,
    ...
]
```

Add the middleware to either the middleware groups or the middleware array in `app/Http/Kernel.php`.
 
```php
protected $middlewareGroups = [
    'api' => [
        ...
        lamhuyvu92\LaravelAwsCognito\Middleware\CognitoAuthenticationMiddleware,
        ...
    ],
];
```

```php
protected $routeMiddleware = [
    ...
    'aws-cognito' => lamhuyvu92\LaravelAwsCognito\Middleware\CognitoAuthenticationMiddleware,
    ...
];
```

Publish then edit the config file.

```
php artisan vendor:publish --provider="lamhuyvu92\LaravelAwsCognito\ServiceProvider"
```

Edit the `config/auth.php` file:

```php
'guards' => [
    'aws-cognito' => [
        'driver' => 'aws-cognito',
        'provider' => 'eloquent',
    ],
],
```

Version History
---------------

0.2.0 (12/11/2017)

*   Upgraded pmill/aws-cognito dependency
*   Updated login to fetch user by cognito username

0.1.0 (30/04/2017)

*   First public release of laravel-aws-cognito


Copyright
---------

pmill/laravel-aws-cognito
Copyright (c) 2017 pmill (dev.pmill@gmail.com) 
All rights reserved.
