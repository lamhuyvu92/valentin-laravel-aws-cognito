<?php
namespace pmill\LaravelAwsCognito\Controllers;

use Illuminate\Http\JsonResponse;
use pmill\AwsCognito\CognitoClient;

class ResetPasswordController
{
    /**
     * @var CognitoClient
     */
    protected $cognitoClient;

    /**
     * ResetPasswordController constructor.
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
    public function resetPassword()
    {
        $confirmationCode = request()->json('confirmation_code');
        $username = request()->json('username');
        $proposedPassword = request()->json('proposed_password');

        $this->cognitoClient->resetPassword($confirmationCode, $username, $proposedPassword);
        return response()->json(['success' => true]);
    }
}
