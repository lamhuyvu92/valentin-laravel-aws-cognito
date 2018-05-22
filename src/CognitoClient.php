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
	
	public function UpdateUserAttributes($access_token, $attributes)
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
	
	public function test(){
		$result = $this->client->AdminGetUser([
   				"Username"=> "valentin2",
   				"UserPoolId"=> "ap-northeast-1_yJg3FpzaK"
			]);
		dd($result);
	}
}