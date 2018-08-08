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
    
    /**
     * @param string $username
     * @param string $refreshToken
     * @return string
     * @throws Exception
     */
    public function refreshAuthentication($username, $refreshToken)
    {
        try {
            $response = $this->client->adminInitiateAuth([
                'AuthFlow' => 'REFRESH_TOKEN_AUTH',
                'AuthParameters' => [
                    // 'USERNAME' => $username,
                    'REFRESH_TOKEN' => $refreshToken,
                    // 'SECRET_HASH' => $this->cognitoSecretHash($username),
                    'SECRET_HASH' => $this->appClientSecret,
                ],
                'ClientId' => $this->appClientId,
                'UserPoolId' => $this->userPoolId,
            ])->toArray();
            
            return $response['AuthenticationResult'];
        } catch (CognitoIdentityProviderException $e) {
            throw CognitoResponseException::createFromCognitoException($e);
        }
    }
    
    public function AdminCreateUser($username, $password, array $attributes = [])
    {
        $userAttributes = $this->buildAttributesArray($attributes);

        try {
            $response = $this->client->AdminCreateUser([
                'DesiredDeliveryMediums' => ['EMAIL'],
                'TemporaryPassword' => $password,
                // 'SecretHash' => $this->cognitoSecretHash($username),
                'UserAttributes' => $userAttributes,
                'Username' => $username,
                'UserPoolId' => $this->userPoolId,
            ]);

            return $response['UserSub'];
        } catch (CognitoIdentityProviderException $e) {
            throw CognitoResponseException::createFromCognitoException($e);
        }
    }
    
    /**
     * @param array $attributes
     * @return array
     */
    private function buildAttributesArray(array $attributes): array
    {
        $userAttributes = [];
        foreach ($attributes as $key => $value) {
            $userAttributes[] = [
                'Name' => (string)$key,
                'Value' => (string)$value,
            ];
        }
        return $userAttributes;
    }
    
    public function test(){
        $result = $this->client->AdminGetUser([
                   "Username"=> "valentin2",
                   "UserPoolId"=> "ap-northeast-1_yJg3FpzaK"
            ]);
        dd($result);
    }
}