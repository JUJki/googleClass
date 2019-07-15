<?php
require __DIR__ . '/vendor/autoload.php';
require_once 'CustomException.php';


class GsuiteInterface
{

	private $client;
	private $pathJsonWebAuth = 'client_secret_aouth.json';
	private $pathJsonAccountService = 'favlink-gsuite-204de3d79173.json';
	private $nameApp = 'FavLink-GSuite';
	private $domainGsuite = 'favlink.net';
	private $emailDelegate = 'julien@favlink.net';
	private $urlDomain = 'https://440891e6.ngrok.io';
	private $endPointNotifications = 'notifications.php';
	private $personFields = [
		'addresses',
		'ageRanges',
		'biographies',
		'birthdays',
		'braggingRights',
		'coverPhotos',
		'emailAddresses',
		'events',
		'genders',
		'imClients',
		'interests',
		'locales',
		'memberships',
		'metadata',
		'names',
		'nicknames',
		'occupations',
		'organizations',
		'phoneNumbers',
		'photos',
		'relations',
		'relationshipInterests',
		'relationshipStatuses',
		'residences',
		'sipAddresses',
		'skills',
		'taglines',
		'urls',
		'userDefined'
	];

	/**
	 * Construct
	 */
	public function __construct()
	{
		$this->client = new Google_Client();
		$this->client->setApplicationName($this->nameApp);
	}

	/**
	 * Permet de configurer le client google avec le web Oauth
	 */
	public function setOauthClient()
	{
		$this->setWebAuthentification();
		$this->setScopeUserOauth();
	}

	/**
	 * Permet de set le client avec le fichier json web Oauth
	 * @throws Google_Exception
	 */
	private function setWebAuthentification()
	{
		$this->client->setAuthConfig($this->pathJsonWebAuth);
	}

	/**
	 * Permet de set le client google avec le dichier json account service
	 * @throws Google_Exception
	 */
	private function setServiceAuthentification()
	{
		$this->client->setAuthConfig($this->pathJsonAccountService);
		$this->client->useApplicationDefaultCredentials();
		$this->client->setSubject($this->emailDelegate);
	}

	/**
	 * Scopes pour les permissions demandant les infos user avec une authentification de type Oauth
	 */
	private function setScopeUserOauth()
	{
		$this->client->setScopes(
			[
				Google_Service_Oauth2::PLUS_ME,
				Google_Service_Oauth2::USERINFO_PROFILE,
				Google_Service_Oauth2::USERINFO_EMAIL
			]
		);
	}

	/**
	 * Scopes pour les permissions demandant les infos user
	 */
	private function setScopeUser()
	{
		$this->client->setScopes(
			[
				Google_Service_Plus::PLUS_ME,
				Google_Service_Plus::PLUS_LOGIN,
				Google_Service_Plus::USERINFO_EMAIL,
				Google_Service_Plus::USERINFO_PROFILE
			]
		);
	}

	/**
	 * Scopes pour les permissions demandant les infos sur un calendar et ces evenements
	 */
	private function setScopeCalendar()
	{
		$this->client->setScopes(
			[
				Google_Service_Calendar::CALENDAR_READONLY,
				Google_Service_Calendar::CALENDAR,
				Google_Service_Calendar::CALENDAR_EVENTS,
				Google_Service_Calendar::CALENDAR_EVENTS_READONLY,
			]
		);
	}

	/**
	 * Scopes pour les permissions demandant les infos sur un user et ces contacts
	 */
	private function setScopePeople()
	{
		$this->client->setScopes(
			[
				Google_Service_PeopleService::CONTACTS,
				Google_Service_PeopleService::CONTACTS_READONLY,
				Google_Service_PeopleService::USERINFO_PROFILE,
				Google_Service_PeopleService::USERINFO_EMAIL,
				Google_Service_PeopleService::USER_EMAILS_READ,
				Google_Service_PeopleService::USER_ADDRESSES_READ

			]
		);
	}

	/**
	 * Scopes pour les permissions demandant les infos pour consulter un organisation
	 */
	private function setScopeDirectory()
	{
		$this->client->setScopes(
			[
				Google_Service_Directory::ADMIN_DIRECTORY_USER,
				Google_Service_Directory::ADMIN_DIRECTORY_USER_READONLY,
				Google_Service_Directory::ADMIN_DIRECTORY_CUSTOMER,
				Google_Service_Directory::ADMIN_DIRECTORY_CUSTOMER_READONLY,
				Google_Service_Directory::ADMIN_DIRECTORY_DOMAIN,
				Google_Service_Directory::ADMIN_DIRECTORY_DOMAIN_READONLY,
				Google_Service_Directory::ADMIN_DIRECTORY_USER_ALIAS,
				Google_Service_Directory::ADMIN_DIRECTORY_USER_ALIAS_READONLY,
				Google_Service_Directory::ADMIN_DIRECTORY_CUSTOMER,
				Google_Service_Directory::ADMIN_DIRECTORY_CUSTOMER_READONLY,
				Google_Service_Directory::ADMIN_DIRECTORY_ORGUNIT,
				Google_Service_Directory::ADMIN_DIRECTORY_ORGUNIT_READONLY
			]
		);
	}

	public function getUrlNotification()
	{
		return $this->urlDomain . DIRECTORY_SEPARATOR . $this->endPointNotifications;
	}

	/**
	 * Retourne le client Google
	 * @return Google_Client
	 */
	public function getClient()
	{
		return $this->client;
	}

	/**
	 * Retourne l'url de redirection apres autorisation google
	 * @return string
	 */
	public function createAuthUrl()
	{
		return $this->client->createAuthUrl();
	}


	/**
	 * Retourne les infos de l'utilisateur donnant son accord à la connection Oauth
	 * @return stdClass
	 * @throws CustomException
	 */
	public function getUserInfoOAuth()
	{
		$service = new Google_Service_Oauth2($this->getClient());
		try {
			$user = $service->userinfo->get();
		} catch (\Google_Exception $error) {
			$this->interpretationException($error, 'getUserInfoOAuth');
		}
		return $user->toSimpleObject();
	}

	/**
	 * Permet de set un webhook
	 * @param $event
	 * @return Google_Service_Directory_Channel
	 * @throws CustomException
	 * @throws Google_Exception
	 */
	public function setWebhookDirectoryUser($event)
	{
		$eventAvailable = [
			'add',
			'delete',
			'makeAdmin',
			'undelete',
			'update',
		];
		if (!in_array($event, $eventAvailable)) {
			throw new InvalidArgumentException(
				'Invalid event type'
			);
		}
		$this->setServiceAuthentification();
		$this->setScopeDirectory();
		$service = new Google_Service_Directory($this->getClient());
		$directoryChannel = new Google_Service_Directory_Channel();
		$directoryChannel->setId(uniqid());
		$directoryChannel->setType('web_hook');
		$directoryChannel->setAddress($this->getUrlNotification());
		$directoryChannel->setToken("target=MyNotification");
		$directoryChannel->setParams([
			"ttl" => 3600
		]);
		$optParams = [
			'event' => $event,
			'domain' => $this->domainGsuite
		];
		try {
			$response = $service->users->watch($directoryChannel, $optParams);
		} catch (\Google_Exception $error) {
			$this->interpretationException($error, 'getCustomerInfo');
		}
		return $response;
	}

	/**
	 * Retourne les informations d'un customer de l'organistion, par defaut : my_customer
	 * @param string $customerKey
	 * @return stdClass
	 * @throws CustomException
	 */
	public function getCustomerInfo($customerKey = 'my_customer')
	{
		$this->setServiceAuthentification();
		$this->setScopeDirectory();

		$service = new Google_Service_Directory($this->getClient());
		try {
			$customer = $service->customers->get($customerKey);
		} catch (\Google_Exception $error) {
			$this->interpretationException($error, 'getCustomerInfo');
		}
		return $customer->toSimpleObject();
	}

	/**
	 * Retourne les informations d'une organisation, par défaut my_customer et path vide pour le premier
	 * @param string $customerId
	 * @param string $path
	 * @return stdClass
	 * @throws CustomException
	 */
	public function getOrganisationInfo($customerId = 'my_customer', $path = "")
	{
		$this->setServiceAuthentification();
		$this->setScopeDirectory();
		$service = new Google_Service_Directory($this->getClient());
		try {
			$organisationInfo = $service->orgunits->get($customerId, $path);
		} catch (\Google_Exception $error) {
			$this->interpretationException($error, 'getOrganisationInfo');
		}
		return $organisationInfo->toSimpleObject();
	}

	/**
	 * Permet de récupérer les informations de toutes les organistions d'un customer
	 * @param string $customerId
	 * @return stdClass
	 * @throws CustomException
	 */
	public function getListOrganisationInfo($customerId = 'my_customer')
	{
		$this->setServiceAuthentification();
		$this->setScopeDirectory();

		$service = new Google_Service_Directory($this->getClient());
		try {
			$organisationInfo = $service->orgunits->listOrgunits($customerId);
		} catch (\Google_Exception $error) {
			$this->interpretationException($error, 'getListOrganisationInfo');
		}
		return $organisationInfo->toSimpleObject();
	}

	/**
	 * Permet de recuperer les informations d'un utilisateur à partir du userKey => email ou id
	 * @param $userKey is id ou user email
	 * @return stdClass
	 * @throws CustomException
	 */
	public function getUserInfoDirectory($userKey)
	{
		$this->setServiceAuthentification();
		$this->setScopeDirectory();
		$service = new Google_Service_Directory($this->getClient());
		try {
			$user = $service->users->get($userKey);
		} catch (\Google_Exception $error) {
			$this->interpretationException($error, 'getUserInfoDirectory');
		}
		return $user->toSimpleObject();
	}


	/**
	 * Permet de creer un nouvel utilisateur
	 * @param $infoUser
	 * @return stdClass
	 * @throws CustomException
	 * @throws Google_Exception
	 */
	public function newUserDirectory($infoUser)
	{
		if (!isset($infoUser['firstname']) || empty($infoUser['firstname'])) {
			throw new InvalidArgumentException(
				'Invalid firstname key - make sure firstname is defined and not empt'
			);
		}
		if (!isset($infoUser['lastname']) || empty($infoUser['lastname'])) {
			throw new InvalidArgumentException(
				'Invalid lastname key - make sure lastname is defined and not empt'
			);
		}
		if (!isset($infoUser['password']) || empty($infoUser['password'])) {
			throw new InvalidArgumentException(
				'Invalid password key - make sure password is defined and not empt'
			);
		}
		if (!isset($infoUser['email']) || empty($infoUser['email'])) {
			throw new InvalidArgumentException(
				'Invalid email key - make sure email is defined and not empty'
			);
		}
		$this->setServiceAuthentification();
		$this->setScopeDirectory();
		$service = new Google_Service_Directory($this->getClient());
		try {
			$nameInstance = new Google_Service_Directory_UserName();
			$nameInstance->setGivenName($infoUser['firstname']);
			$nameInstance->setFamilyName($infoUser['lastname']);
			$nameInstance->setFullName($infoUser['firstname'] . ' ' . $infoUser['lastname']);

			$userInstance = new Google_Service_Directory_User();
			$userInstance->setName($nameInstance);
			$userInstance->setHashFunction("MD5");
			$userInstance->setPrimaryEmail($infoUser['email']);
			$userInstance->setPassword(hash("md5", $infoUser['password']));

			$newUser = $service->users->insert($userInstance);
		} catch (\Google_Exception $error) {
			$this->interpretationException($error, 'newUserDirectory');
		}
		return $newUser->toSimpleObject();
	}

	/**
	 * Permet de suspendre un user
	 * @param $userKey is id ou user email
	 * @return stdClass
	 * @throws CustomException
	 * @throws Google_Exception
	 */
	public function suspendedUserDirectory($userKey)
	{
		$this->setServiceAuthentification();
		$this->setScopeDirectory();
		$service = new Google_Service_Directory($this->getClient());
		try {
			$userInstance = new Google_Service_Directory_User();
			$userInstance->setSuspended(true);
			$newUpdated = $service->users->update($userKey, $userInstance);
		} catch (\Google_Exception $error) {
			$this->interpretationException($error, 'suspendedUserDirectory');
		}
		return $newUpdated->toSimpleObject();
	}


	/**
	 * Permet de retourner la liste des users présents dans l'organisation appartenant au domain et un customer
	 * @param string $customerId
	 * @return array
	 * @throws CustomException
	 */
	public function getListMembers($customerId = 'my_customer')
	{
		$this->setServiceAuthentification();
		$this->setScopeDirectory();
		$service = new Google_Service_Directory($this->getClient());
		$optParams = array(
			'customer' => $customerId,
			'domain' => $this->domainGsuite,
			'maxResults' => 10,
			'orderBy' => 'email',
		);
		try {
			$listMembers = $service->users->listUsers($optParams);
		} catch (\Google_Exception $error) {
			$this->interpretationException($error, 'getListMembers');
		}
		$users = [];
		foreach ($listMembers->getUsers() as $user) {
			array_push($users, $user->toSimpleObject());
		}
		return $users;
	}

	/**
	 * Permet de recuperer les photos d'un user à partir de son userkey
	 * @param $userKey is id ou user email
	 * @return stdClass
	 * @throws CustomException
	 * @throws Google_Exception
	 */
	public function getUserPhoto($userKey)
	{
		$this->setServiceAuthentification();
		$this->setScopeDirectory();
		$service = new Google_Service_Directory($this->getClient());

		try {
			$userPhoto = $service->users_photos->get($userKey);
		} catch (\Google_Exception $error) {
			$this->interpretationException($error, 'getUserPhoto');
		}

		return $userPhoto->toSimpleObject();
	}

	/**
	 * Permet de recuperer les alias d'un user
	 * @param $userKey is id ou user email
	 * @return mixed
	 * @throws CustomException
	 * @throws Google_Exception
	 */
	public function getUserAlias($userKey)
	{
		$this->setServiceAuthentification();
		$this->setScopeDirectory();
		$service = new Google_Service_Directory($this->getClient());
		try {
			$userAlias = $service->users_aliases->listUsersAliases($userKey);
		} catch (\Google_Exception $error) {
			$this->interpretationException($error, 'getUserAlias');
		}

		return $userAlias->getAliases();
	}

	/**
	 * Permet de récuperer les connections d'un contact, par défaut c'est le compte principal
	 * @param string $accountId
	 * @return array
	 * @throws CustomException
	 */
	public function getPeopleConnection($accountId = 'me')
	{
		$this->setServiceAuthentification();
		$this->setScopePeople();
		$service = new Google_Service_PeopleService($this->getClient());
		$optParams = array(
			'pageSize' => 10,
			'personFields' => implode(",", $this->personFields)
		);
		try {
			$peoples = $service->people_connections->listPeopleConnections('people/' . $accountId, $optParams);
		} catch (\Google_Exception $error) {
			$this->interpretationException($error, 'getPeopleConnection');
		}
		$listPeople = [];
		while (true) {
			foreach ($peoples->getConnections() as $people) {
				array_push($listPeople, $people->toSimpleObject());
			}
			$pageToken = $peoples->getNextPageToken();
			if ($pageToken) {
				$optParams['pageToken'] = $pageToken;
				$peoples = $service->people_connections->listPeopleConnections('people/me', $optParams);
			} else {
				break;
			}
		}
		return $listPeople;
	}


	/**
	 * Permet de recuperer les informations d'un utilisateur à partir de son accountId
	 * @param string $accountId
	 * @return stdClass
	 * @throws CustomException
	 */
	public function getInfoPeople($accountId = 'me')
	{
		$this->setServiceAuthentification();
		$this->setScopePeople();
		$service = new Google_Service_PeopleService($this->getClient());
		$optParams = [
			'personFields' => implode(",", $this->personFields)
		];
		try {
			$people = $service->people->get('people/' . $accountId, $optParams);
		} catch (\Google_Exception $error) {
			$this->interpretationException($error, 'getInfoPeople');
		}
		return $people->toSimpleObject();
	}


	/**
	 * Permet de récupérer les groups
	 * @return stdClass
	 * @throws CustomException
	 * @throws Google_Exception
	 */
	public function getContactGroups()
	{
		$this->setServiceAuthentification();
		$this->setScopePeople();
		$service = new Google_Service_PeopleService($this->getClient());
		try {
			$contactGroup = $service->contactGroups->get('contactGroups/all');
		} catch (\Google_Exception $error) {
			$this->interpretationException($error, 'getContactGroup');
		}
		return $contactGroup->toSimpleObject();
	}


	/**
	 * Permet de récuperer les informations d'un user à partir de son id
	 * @param string $userId
	 * @return stdClass
	 */
	public function getUserInfo($userId = 'me')
	{
		$this->setServiceAuthentification();
		$this->setScopeUser();
		$service = new Google_Service_Plus($this->getClient());
		try {
			$user = $service->people->get($userId);
		} catch (\Google_Exception $error) {
			$this->interpretationException($error, 'getUserInfo');
		}
		return $user->toSimpleObject();
	}

	/**
	 * Permet de recuperer les evenements d'un calendrier selon son id
	 * @param $calendarId string
	 * @return array
	 */
	public function getListEventsCalendar($calendarId = 'primary')
	{
		$this->setServiceAuthentification();
		$this->setScopeCalendar();
		$service = new Google_Service_Calendar($this->getClient());
		$optParams = array(
			'maxResults' => 10,
			'orderBy' => 'startTime',
			'singleEvents' => true,
			'timeMin' => date('c'), // today
		);
		try {
			$events = $service->events->listEvents($calendarId, $optParams);
		} catch (\Google_Exception $error) {
			$this->interpretationException($error, 'getListEventsCalendar');
		}
		$litEvents = [];
		while (true) {
			foreach ($events->getItems() as $event) {
				array_push($litEvents, $event->toSimpleObject());
			}
			$pageToken = $events->getNextPageToken();
			if ($pageToken) {
				$optParams['pageToken'] = $pageToken;
				$events = $service->events->listEvents($calendarId, $optParams);
			} else {
				break;
			}
		}
		return $litEvents;
	}

	/**
	 * Permet de récupérer les informations d'un calendar
	 * @param string $idCalendar
	 * @return stdClass
	 * @throws CustomException
	 */
	public function getCalendar($idCalendar = 'primary')
	{
		$this->setServiceAuthentification();
		$this->setScopeCalendar();
		$service = new Google_Service_Calendar($this->getClient());
		try {
			$calendar = $service->calendars->get($idCalendar);
		} catch (\Google_Exception $error) {
			$this->interpretationException($error, 'getCalendar');
		}
		return $calendar->toSimpleObject();
	}

	private function interpretationException(Google_Exception $error, $nameFunction)
	{
		if ($error->getCode() === 400) {
			throw new CustomException(
				'GsuiteInterface/' . $nameFunction . '/ [' . $error->getCode() . '] ' . $error->getMessage()->status,
				GS_ERROR_400
			);
		} else if ($error->getCode() === 401) {
			throw new CustomException(
				'GsuiteInterface/' . $nameFunction . '/ [' . $error->getCode() . '] ' . $error->getMessage(),
				GS_ERROR_401
			);
		} else if ($error->getCode() === 403) {
			throw new CustomException(
				'GsuiteInterface/' . $nameFunction . '/ [' . $error->getCode() . '] ' . $error->getMessage(),
				GS_ERROR_403
			);
		} else if ($error->getCode() === 404) {
			throw new CustomException(
				'GsuiteInterface/' . $nameFunction . '/ [' . $error->getCode() . '] ' . $error->getMessage(),
				GS_ERROR_404
			);
		} else {
			throw new CustomException(
				'GsuiteInterface/' . $nameFunction . '/ [' . $error->getCode() . '] ' . $error->getMessage(),
				GS_ERROR
			);
		}
	}
}