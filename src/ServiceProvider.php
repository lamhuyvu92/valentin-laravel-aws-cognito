<?php
namespace pmill\LaravelAwsCognito;

use Illuminate\Contracts\Cache\Repository;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider;
use Madewithlove\IlluminatePsrCacheBridge\Laravel\CacheItemPool;
use pmill\AwsCognito\CognitoClient;
use Psr\Cache\CacheItemPoolInterface;

class ServiceProvider extends AuthServiceProvider
{
    /**
     * Boot any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/aws-cognito-auth.php' => config_path('aws-cognito-auth.php'),
        ], 'config');

        $this->app->singleton('aws-cognito-sdk', function (Application $app) {
            return new \Aws\Sdk(config('aws-cognito-auth'));
        });

        $this->app->singleton(CognitoClient::class, function (Application $app) {
            $awsCognitoIdentityProvider = $app->make('aws-cognito-sdk')->createCognitoIdentityProvider();

            $cognitoClient = new CognitoClient($awsCognitoIdentityProvider);
            $cognitoClient->setAppClientId(config('aws-cognito-auth.app_client_id'));
            $cognitoClient->setAppClientSecret(config('aws-cognito-auth.app_client_secret'));
            $cognitoClient->setRegion(config('aws-cognito-auth.region'));
            $cognitoClient->setUserPoolId(config('aws-cognito-auth.user_pool_id'));

            return $cognitoClient;
        });

        $this->app['auth']->extend('aws-cognito', function (Application $app, $name, array $config) {
            $client = $app->make(CognitoClient::class);
            $provider = $app['auth']->createUserProvider($config['provider']);

            return new ApiGuard($provider, $client);
        });
    }
}
