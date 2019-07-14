<?php
require __DIR__ . '/vendor/autoload.php';
require_once 'GsuiteInterface.php';

error_reporting(-1);
ini_set('display_errors', 1);

$gsuiteInterface = new GsuiteInterface();
try {
	//$events = $gsuiteInterface->getOrganisationInfo('C0318h5kh', '');
//114312677302791064394
	//$events = $gsuiteInterface->getUserInfo('114312677302791064394');
	//$events = $gsuiteInterface->getUserInfoDirectory('antoine@favlink.net');
	$events = $gsuiteInterface->getInfoPeople();
	echo '<pre>';
	var_dump($events);
	echo '</pre>';
} catch (\CustomException $error) {
	echo $error->getMessage();
}