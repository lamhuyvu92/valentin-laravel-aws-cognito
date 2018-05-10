<?php
namespace pmill\LaravelAwsCognito;
use pmill\AwsCognito\CognitoClient;

class LaravelCognitoClient extends CognitoClient
{
	public function getUser($access_token)
	{
		$user = $this->client->getUser(['AccessToken' => $access_token]);
		return $user;
	}
}