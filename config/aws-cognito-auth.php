<?php

return [
    'credentials' => [
        'key' => env('AWS_KEY'),
        'secret' => env('AWS_SECRET'),
    ],
    'region' => env('AWS_REGION'),
    'version' => env('AWS_VERSION', 'latest'),

    'app_client_id' => env('AWS_COGNITO_CLIENT_ID'),
    'app_client_secret' => env('AWS_COGNITO_CLIENT_SECRET'),
    'user_pool_id' => env('AWS_COGNITO_USER_POOL_ID'),
    'username_field' => 'username',
];
