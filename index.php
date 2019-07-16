<?php
require __DIR__ . '/vendor/autoload.php';
require_once 'GsuiteInterface.php';

error_reporting(-1);
ini_set('display_errors', 1);

$gsuiteInterface = new GsuiteInterface();


echo '<h6>Info Domainr</h6>';
try {
	$domain = $gsuiteInterface->getInfoDomain();
	echo '<pre>';
	var_dump($domain);
	echo '</pre>';
} catch (\CustomException $error) {
	echo 'code : ' . $error->getCode();
	echo '</br>';
	echo 'message : ' . $error->getMessage();
}


echo '<h6>List Domainr</h6>';
try {
	$domain = $gsuiteInterface->getListDomain();
	echo '<pre>';
	var_dump($domain);
	echo '</pre>';
} catch (\CustomException $error) {
	echo 'code : ' . $error->getCode();
	echo '</br>';
	echo 'message : ' . $error->getMessage();
}


echo '<h6>Weebhook user</h6>';
try {
	$settingWebhook = $gsuiteInterface->setWebhookDirectoryUser('add');
	echo '<pre>';
	var_dump($settingWebhook);
	echo '</pre>';
} catch (\CustomException $error) {
	echo 'code : ' . $error->getCode();
	echo '</br>';
	echo 'message : ' . $error->getMessage();
}


echo '<h6>List notifications</h6>';
try {
	$listnotification = $gsuiteInterface->getListNotifications();
	echo '<pre>';
	var_dump($listnotification);
	echo '</pre>';
} catch (\CustomException $error) {
	echo 'code : ' . $error->getCode();
	echo '</br>';
	echo 'message : ' . $error->getMessage();
}


echo '<h6>Info customer</h6>';
try {
	$infoCustomer = $gsuiteInterface->getCustomerInfo();
	echo '<pre>';
	var_dump($infoCustomer);
	echo '</pre>';
} catch (\CustomException $error) {
	echo 'code : ' . $error->getCode();
	echo '</br>';
	echo 'message : ' . $error->getMessage();
}


echo '<h6>Info organisation parent</h6>';
try {
	$infoOrgaParent = $gsuiteInterface->getOrganisationInfo('C0318h5kh', '');
	echo '<pre>';
	var_dump($infoOrgaParent);
	echo '</pre>';
} catch (\CustomException $error) {
	echo 'code : ' . $error->getCode();
	echo '</br>';
	echo 'message : ' . $error->getMessage();
}


echo '<h6>Info organisation specifique</h6>';
try {
	$infoOrga = $gsuiteInterface->getOrganisationInfo('C0318h5kh', 'test');
	echo '<pre>';
	var_dump($infoOrga);
	echo '</pre>';
} catch (\CustomException $error) {
	echo 'code : ' . $error->getCode();
	echo '</br>';
	echo 'message : ' . $error->getMessage();
}


echo '<h6>List organisation</h6>';
try {
	$listOrg = $gsuiteInterface->getListOrganisationInfo();
	echo '<pre>';
	var_dump($listOrg);
	echo '</pre>';
} catch (\CustomException $error) {
	echo 'code : ' . $error->getCode();
	echo '</br>';
	echo 'message : ' . $error->getMessage();
}


echo '<h6>Info user with Id</h6>';
try {
	$userInfoID = $gsuiteInterface->getUserInfoDirectory('114312677302791064394');
	echo '<pre>';
	var_dump($userInfoID);
	echo '</pre>';
} catch (\CustomException $error) {
	echo 'code : ' . $error->getCode();
	echo '</br>';
	echo 'message : ' . $error->getMessage();
}


echo '<h6>Info User with email</h6>';
try {
	$userInfoEmail = $gsuiteInterface->getUserInfoDirectory('antoine@favlink.net');
	echo '<pre>';
	var_dump($userInfoEmail);
	echo '</pre>';
} catch (\CustomException $error) {
	echo 'code : ' . $error->getCode();
	echo '</br>';
	echo 'message : ' . $error->getMessage();
}


echo '<h6>Create user</h6>';
try {
	$user = [
		"firstname" => "julien1",
		"lastname" => "jannneau1",
		"email" => "juljanneau1@favlink.net",
		"password" => "8888",
	];
	$userCreated = $gsuiteInterface->newUserDirectory($user);
	echo '<pre>';
	var_dump($userCreated);
	echo '</pre>';
} catch (\CustomException $error) {
	echo 'code : ' . $error->getCode();
	echo '</br>';
	echo 'message : ' . $error->getMessage();
}


echo '<h6>Suspension user</h6>';
try {
	$userUpdated = $gsuiteInterface->suspendedUserDirectory('117182665353540868093');
	echo '<pre>';
	var_dump($userUpdated);
	echo '</pre>';
} catch (\CustomException $error) {
	echo 'code : ' . $error->getCode();
	echo '</br>';
	echo 'message : ' . $error->getMessage();
}


echo '<h6>List Members</h6>';
try {
	$listMembersOrga = $gsuiteInterface->getListMembers();
	echo '<pre>';
	var_dump($listMembersOrga);
	echo '</pre>';
} catch (\CustomException $error) {
	echo 'code : ' . $error->getCode();
	echo '</br>';
	echo 'message : ' . $error->getMessage();
}


echo '<h6>User Photo</h6>';
try {
	$photoUser = $gsuiteInterface->getUserPhoto('114312677302791064394');
	echo '<pre>';
	var_dump($photoUser);
	echo '</pre>';
} catch (\CustomException $error) {
	echo 'code : ' . $error->getCode();
	echo '</br>';
	echo 'message : ' . $error->getMessage();
}


echo '<h6>User alias</h6>';
try {
	$aliasUser = $gsuiteInterface->getUserAlias('114312677302791064394');
	echo '<pre>';
	var_dump($aliasUser);
	echo '</pre>';
} catch (\CustomException $error) {
	echo 'code : ' . $error->getCode();
	echo '</br>';
	echo 'message : ' . $error->getMessage();
}


echo '<h6>People Connection</h6>';
try {
	$peopleConnection = $gsuiteInterface->getPeopleConnection();
	echo '<pre>';
	var_dump($peopleConnection);
	echo '</pre>';
} catch (\CustomException $error) {
	echo 'code : ' . $error->getCode();
	echo '</br>';
	echo 'message : ' . $error->getMessage();
}


echo '<h6>Info People</h6>';
try {
	$infoPeople = $gsuiteInterface->getInfoPeople();
	echo '<pre>';
	var_dump($infoPeople);
	echo '</pre>';
} catch (\CustomException $error) {
	echo 'code : ' . $error->getCode();
	echo '</br>';
	echo 'message : ' . $error->getMessage();
}


echo '<h6>Groups</h6>';
try {
	$contacts = $gsuiteInterface->getContactGroups();
	echo '<pre>';
	var_dump($contacts);
	echo '</pre>';
} catch (\CustomException $error) {
	echo 'code : ' . $error->getCode();
	echo '</br>';
	echo 'message : ' . $error->getMessage();
}


echo '<h6>Info User</h6>';
try {
	$userInfo = $gsuiteInterface->getUserInfo();
	echo '<pre>';
	var_dump($userInfo);
	echo '</pre>';
} catch (\CustomException $error) {
	echo 'code : ' . $error->getCode();
	echo '</br>';
	echo 'message : ' . $error->getMessage();
}


echo '<h6>List Events Calendar</h6>';
try {
	$listCalendar = $gsuiteInterface->getListEventsCalendar();
	echo '<pre>';
	var_dump($listCalendar);
	echo '</pre>';
} catch (\CustomException $error) {
	echo 'code : ' . $error->getCode();
	echo '</br>';
	echo 'message : ' . $error->getMessage();
}


echo '<h6>Calendar</h6>';
try {
	$calendar = $gsuiteInterface->getCalendar();
	echo '<pre>';
	var_dump($calendar);
	echo '</pre>';
} catch (\CustomException $error) {
	echo 'code : ' . $error->getCode();
	echo '</br>';
	echo 'message : ' . $error->getMessage();
}
