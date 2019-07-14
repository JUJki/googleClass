<?php

require 'GsuiteInterface.php';

session_start();
/*error_reporting(-1);
ini_set('display_errors', 1);*/
function getUserInfo()
{
	$googleInterface = new GsuiteInterface();
	$googleInterface->setOauthClient();
	$clientGoogle = $googleInterface->getClient();
	// If Param code exist
	if (isset($_GET['code'])) {
		// Fetch token with param code google
		$token = $clientGoogle->fetchAccessTokenWithAuthCode($_GET['code']);
		if (isset($token['access_token'])) {
			// Set access_token in clinet google
			$clientGoogle->setAccessToken($token);
			// Set in fo token in session
			$_SESSION['upload_token'] = $token;
			$_SESSION['access_token'] = $clientGoogle->getAccessToken();
		}
	}
	if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
		$clientGoogle->setAccessToken($_SESSION['upload_token']);
	}

	// If there is no previous token or it's expired.
	if ($clientGoogle->isAccessTokenExpired()) {
		// Refresh the token if possible, else fetch a new one.
		if ($clientGoogle->getRefreshToken()) {
			$clientGoogle->fetchAccessTokenWithRefreshToken($clientGoogle->getRefreshToken());
		} else {
			// Request authorization from the user.
			$urlCallBack = $googleInterface->createAuthUrl();
			header('Location:'.$urlCallBack);
		}
	}
	return $googleInterface->getUserInfoOAuth();
}

try {
	echo json_encode(getUserInfo());
} catch (\CustomException $error) {
	echo $error->getCode();
	echo $error->getMessage();
}

