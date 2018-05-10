lamhuyvu92/valentin-laravel-aws-cognito
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
composer.phar require lamhuyvu92/valentin-laravel-aws-cognito
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

Copyright
---------

Reference from pmill/laravel-aws-cognito
Copyright (c) 2018 valentin (lamhuyvu92@gmail.com) 