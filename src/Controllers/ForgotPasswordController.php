<?php
namespace pmill\LaravelAwsCognito\Controllers;

use Illuminate\Http\JsonResponse;
use pmill\AwsCognito\CognitoClient;

class ForgotPasswordController
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
    public function requestForgottenPassword()
    {
        $username = request()->json('username');
        $this->cognitoClient->sendForgottenPasswordRequest($username);
        return response()->json(['success' => true]);
    }
}
