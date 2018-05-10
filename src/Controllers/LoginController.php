<?php
namespace pmill\LaravelAwsCognito\Controllers;

use pmill\LaravelAwsCognito\ApiGuard;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use pmill\AwsCognito\CognitoClient;

class LoginController
{
    /**
     * @var CognitoClient
     */
    protected $cognitoClient;

    /**
     * ForgotPasswordController constructor.
     *
     * @param CognitoClient $cognitoClient
     */
    public function __construct(CognitoClient $cognitoClient)
    {
        $this->cognitoClient = $cognitoClient;
    }

    /**
     * @return JsonResponse
     */
    public function login()
    {
        $credentials = array_only(response()->json(), ['username', 'password']);
        /** @var ApiGuard $guard */
        $guard = Auth::guard('aws-cognito');
        $guard->validate($credentials);

        return response()->json([
            'accessToken' => $guard->accessToken(),
        ]);
    }
}
