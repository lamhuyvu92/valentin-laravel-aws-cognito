<?php
namespace pmill\LaravelAwsCognito;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
use pmill\LaravelAwsCognito\LaravelCognitoClient;
use pmill\AwsCognito\Exception\TokenExpiryException;
use pmill\AwsCognito\Exception\TokenVerificationException;
use pmill\LaravelAwsCognito\Exceptions\CognitoUserNotFoundException;

class ApiGuard implements Guard
{
    use GuardHelpers;

    /**
     * @var CognitoClient
     */
    protected $cognitoClient;

    /**
     * @var string
     */
    protected $usernameField;

    /**
     * @var AuthenticationResponse
     */
    protected $authenticationResponse;
    
    /**
     * @var cognitoAuthenticationResponse
     */
    protected $cognitoAuthenticationResponse;

    /**
     * ApiGuard constructor.
     *
     * @param UserProvider $userProvider
     * @param CognitoClient $cognitoClient
     */
    public function __construct(UserProvider $userProvider, LaravelCognitoClient $cognitoClient)
    {
        $this->provider = $userProvider;
        $this->cognitoClient = $cognitoClient;
        $this->usernameField = config('aws-cognito-auth.username_field');
    }

    /**
     * @return Authenticatable
     */
    public function user()
    {
        return $this->user;
    }

    public function logout()
    {
        $this->user = null;
    }

    /**
     * @param array $credentials
     *
     * @return bool
     */
    public function validate(array $credentials = [])
    {
        $username = array_get($credentials, $this->usernameField);
        $password = array_get($credentials, 'password');
        $this->attempt($username, $password);
        return $this->check();
    }

    /**
     * @return bool
     */
    public function validateToken($accessToken = null)
    {
        if(!$accessToken){
            $authorizationHeader = request()->header('Authorization');
            $accessToken = trim(str_replace('Bearer', '', $authorizationHeader));
        }
        return $this->attemptWithToken($accessToken);
    }

    /**
     * @param string $username
     * @param string $refreshToken
     *
     * @return AuthenticationResponse
     */
    public function refreshAccessToken($username, $refreshToken)
    {
        $cognitoAuthenticationResponse = $this->cognitoClient->refreshAuthentication($username, $refreshToken);

        $authenticationResponse = new AuthenticationResponse;
        $authenticationResponse->setAccessToken(array_get($cognitoAuthenticationResponse, 'AccessToken'));
        $authenticationResponse->setExpiresIn(array_get($cognitoAuthenticationResponse, 'ExpiresIn'));
        $authenticationResponse->setIdToken(array_get($cognitoAuthenticationResponse, 'IdToken'));
        $authenticationResponse->setRefreshToken(array_get($cognitoAuthenticationResponse, 'RefreshToken'));
        $authenticationResponse->setTokenType(array_get($cognitoAuthenticationResponse, 'TokenType'));
        
        $this->cognitoAuthenticationResponse = $cognitoAuthenticationResponse;
        return $this->authenticationResponse = $authenticationResponse;
    }

    /**
     * @param string $accessToken
     * @throws AuthenticationException
     * @throws TokenExpiryException
     * @throws TokenVerificationException
     */
    public function attemptWithToken($accessToken)
    {
        if (!$accessToken) {
            throw new AuthenticationException();
        }

        $result = $this->cognitoClient->verifyAccessToken($accessToken);
        if($result['status'] == "valid"){
            $this->user = $this->provider->retrieveByCredentials([
                'username' => $result['username'],
            ]);
            return true;
        }
        return false;
    }

    /**
     * @param string $username
     * @param string $password
     *
     * @return AuthenticationResponse
     * @throws CognitoUserNotFoundException
     */
    public function attempt($username, $password)
    {
        $cognitoAuthenticationResponse = $this->cognitoClient->authenticate($username, $password);

        $authenticationResponse = new AuthenticationResponse;
        $authenticationResponse->setAccessToken(array_get($cognitoAuthenticationResponse, 'AccessToken'));
        $authenticationResponse->setExpiresIn(array_get($cognitoAuthenticationResponse, 'ExpiresIn'));
        $authenticationResponse->setIdToken(array_get($cognitoAuthenticationResponse, 'IdToken'));
        $authenticationResponse->setRefreshToken(array_get($cognitoAuthenticationResponse, 'RefreshToken'));
        $authenticationResponse->setTokenType(array_get($cognitoAuthenticationResponse, 'TokenType'));

        $result = $this->cognitoClient->verifyAccessToken($authenticationResponse->getAccessToken());
        if($result['status'] == "valid"){
            $this->user = $this->provider->retrieveByCredentials([
                'username' => $result['username'],
            ]);
        }
        
        $this->cognitoAuthenticationResponse = $cognitoAuthenticationResponse;
        return $this->authenticationResponse = $authenticationResponse;
    }

    /**
     * @return AuthenticationResponse
     */
    public function authenticationResponse()
    {
        return $this->authenticationResponse;
    }
    
    /**
     * @return AuthenticationResponse
     */
    public function cognitoAuthenticationResponse()
    {
        return $this->cognitoAuthenticationResponse;
    }
    
    /**
     * @return aws user
     */
    public function getUser($accessToken = null){
        if($accessToken && $this->validateToken($accessToken)){
            return $this->cognitoClient->getUser($accessToken);
        }else{
            return null;
        }
    }
    
    /**
     * @param string $username
     * @param string $password
     * @param array $attributes
     * @return string
     * @throws Exception
     */
    public function registerUser($request = null)
    {
        $username = $request['username'];
        $password = $request['password'];
        unset($request['username']);
        unset($request['password']);
        unset($request['password_confirmation']);
        return $this->cognitoClient->registerUser($username, $password, $request);
    }
    
    public function confirmUserRegistration($confirmationCode, $username)
    {
        $this->cognitoClient->confirmUserRegistration($confirmationCode, $username);
    }
    
    public function UpdateUserAttributes($access_token, $attributes)
    {
        $this->cognitoClient->UpdateUserAttributes($access_token, $attributes);
    }
    
    public function changePassword($access_token, $current_password, $new_password){
        $this->cognitoClient->changePassword($access_token, $current_password, $new_password);
    }
    
    public function sendForgottenPasswordRequest($username){
        $this->cognitoClient->sendForgottenPasswordRequest($username);
    }
    
    public function resetPassword($confirmationCode, $username, $proposedPassword){
        $this->cognitoClient->resetPassword($confirmationCode, $username, $proposedPassword);
    }
    
    public function test(){
        $this->cognitoClient->test();
    }
}
