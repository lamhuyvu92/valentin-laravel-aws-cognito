<?php
namespace pmill\LaravelAwsCognito;
use pmill\AwsCognito\CognitoClient;
use pmill\AwsCognito\Exception\CognitoResponseException;

class LaravelCognitoClient extends CognitoClient
{
    public function getUser($access_token)
    {
        $user = $this->client->getUser(['AccessToken' => $access_token]);
        return $user;
    }
    
    public function ResendConfirmationCode($username)
    {
        try {
            $this->client->ResendConfirmationCode([
                'ClientId' => $this->appClientId,
                'SecretHash' => $this->cognitoSecretHash($username),
                'Username' => $username
            ]);
        } catch (CognitoIdentityProviderException $e) {
            throw CognitoResponseException::createFromCognitoException($e);
        }
    }
    
    public function UpdateUserAttributes($access_token, array $attributes = [])
    {
        try {
            $this->client->UpdateUserAttributes([
                "AccessToken"=> $access_token,
                "UserAttributes"=> $attributes
            ]);
        } catch (CognitoIdentityProviderException $e) {
            throw CognitoResponseException::createFromCognitoException($e);
        }
    }
    
    public function verifyAccessToken($accessToken){
        $jwtPayload = $this->decodeAccessToken($accessToken);

        $expectedIss = sprintf('https://cognito-idp.%s.amazonaws.com/%s', $this->region, $this->userPoolId);
        if ($jwtPayload['iss'] !== $expectedIss) {
            return ['status' => 'invalid', 'msg' => 'invalid iss'];
        }

        if ($jwtPayload['token_use'] !== 'access') {
            return ['status' => 'invalid', 'msg' => 'invalid token_use'];
        }

        if ($jwtPayload['exp'] < time()) {
            return ['status' => 'invalid', 'msg' => 'invalid exp'];
        }
        
        return ['status' => 'valid', 'username' => $jwtPayload['username']];
    }
    
    public function test(){
        $result = $this->client->AdminGetUser([
                   "Username"=> "valentin2",
                   "UserPoolId"=> "ap-northeast-1_yJg3FpzaK"
            ]);
        dd($result);
    }
}