<?php
require __DIR__ . '/vendor/autoload.php';
require_once 'CustomException.php';


class GsuiteInterface
{

	private $client;
	private $domaineGsuite = 'favlink.net';
	private $emailDelegate = 'julien@favlink.net';
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
		$this->client->setApplicationName("FavLink-GSuite");
		/*$this->client->setScopes(
			[

				Google_Service_CloudResourceManager::CLOUD_PLATFORM,
				Google_Service_CloudResourceManager::CLOUD_PLATFORM_READ_ONLY,

				Google_Service_Sheets::DRIVE,
				Google_Service_Sheets::DRIVE_FILE,
				Google_Service_Sheets::DRIVE_READONLY,

				Google_Service_Sheets::SPREADSHEETS,
				Google_Service_Sheets::SPREADSHEETS_READONLY,


			]
		);*/

	}

	public function setOauthClient()
	{
		$this->setWebAuthentification();
		$this->setScopeUserOauth();
	}

	private function setWebAuthentification()
	{
		$this->client->setAuthConfig('client_secret_aouth.json');
	}

	private function setServiceAuthentification()
	{
		$this->client->setAuthConfig('favlink-gsuite-204de3d79173.json');
		$this->client->useApplicationDefaultCredentials();
		$this->client->setAccessType('offline');
		$this->client->setPrompt('select_account consent');
		$this->client->setSubject($this->emailDelegate);
	}

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

	/**
	 * @return Google_Client
	 */
	public function getClient()
	{
		return $this->client;
	}

//ok
	public function createAuthUrl()
	{
		return $this->client->createAuthUrl();
	}

//ok
	public function getUserInfoOAuth()
	{
		$service = new Google_Service_Oauth2($this->getClient());
		return $service->userinfo->get()->toSimpleObject();
	}

	public function getUserInfo($userId)
	{
		$this->setServiceAuthentification();
		$this->setScopeUser();
		$service = new Google_Service_Plus($this->getClient());
		return $service->people->get($userId)->toSimpleObject();
	}
//ok

	/**
	 * Permet de recuperer les informations d'unutilisateur à partir du userKey => email
	 * @param $userKey email de l'utilisateur
	 * @return stdClass
	 */
	public function getUserInfoDirectory($userKey)
	{
		$this->setServiceAuthentification();
		$this->setScopeDirectory();
		$service = new Google_Service_Directory($this->getClient());
		return $service->users->get($userKey)->toSimpleObject();
	}

//ok
	public function getCustomerInfo($customerKey = 'my_customer')
	{
		$this->setServiceAuthentification();
		$this->setScopeDirectory();

		$service = new Google_Service_Directory($this->getClient());
		$customer = $service->customers->get($customerKey);
		return $customer->toSimpleObject();
	}

	//OK
	public function getOrganisationInfo($customerId = 'my_customer', $path = "")
	{
		$this->setServiceAuthentification();
		$this->setScopeDirectory();
		$service = new Google_Service_Directory($this->getClient());
		$organisationInfo = $service->orgunits->get($customerId, $path);
		return $organisationInfo->toSimpleObject();
	}

	//OK
	public function getListOrganisationInfo($customerId = 'my_customer')
	{
		$this->setServiceAuthentification();
		$this->setScopeDirectory();

		$service = new Google_Service_Directory($this->getClient());
		$organisationInfo = $service->orgunits->listOrgunits($customerId)->toSimpleObject();
		return $organisationInfo;
	}
//OK

	/**
	 * Permet de retourner la liste des users
	 * @return Google_Service_Directory_User
	 * @throws CustomException
	 */
	public function getListMembers()
	{
		$this->setServiceAuthentification();
		$this->setScopeDirectory();
		$service = new Google_Service_Directory($this->getClient());
		$optParams = array(
			'customer' => 'my_customer',
			'domain' => $this->domaineGsuite,
			'maxResults' => 10,
			'orderBy' => 'email',
		);
		try {
			$listMembers = $service->users->listUsers($optParams);
		} catch (\Google_Exception $error) {
			throw new CustomException('GsuiteInterface/exampleMethod:: Explication text' . $error->getMessage(), GS_ERROR1);
		}
		$users = [];
		foreach ($listMembers->getUsers() as $user) {
			array_push($users, $user->toSimpleObject());
		}
		return $users;
	}

//OK
	public function getPeopleConnection()
	{
		$this->setServiceAuthentification();
		$this->setScopePeople();
		$service = new Google_Service_PeopleService($this->getClient());
		$optParams = array(
			'pageSize' => 10,
			'personFields' => 'names,emailAddresses',
		);
		try {
			$peoples = $service->people_connections->listPeopleConnections('people/me', $optParams);
		} catch (\Google_Exception $error) {
			throw new CustomException('GsuiteInterface/getPeopleConnection:: Explication text' . $error->getMessage(), GS_ERROR1);
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
//OK

	/**
	 * Permet de recuperer les informations d'un utilisateur à partir de son id
	 * @param $id string
	 * @return Google_Service_PeopleService_Person
	 * @throws CustomException
	 */
	public function getInfoPeople()
	{
		$this->setServiceAuthentification();
		$this->setScopeUser();
		$service = new Google_Service_PeopleService($this->getClient());
		$optParams = [
			'personFields' => implode(",", $this->personFields)
		];
		try {
			$people = $service->people->get('people/me', $optParams);
		} catch (\Google_Exception $error) {
			throw new CustomException('GsuiteInterface/getPeopleConnection:: Explication text' . $error->getMessage(), GS_ERROR1);
		}
		return $people->toSimpleObject();
	}

	public function getContactGroup()
	{
		$this->setServiceAuthentification();
		$this->setScopeUser();
		$service = new Google_Service_PeopleService($this->getClient());
		$optParams = [
			'personFields' => implode(",", $this->personFields)
		];
		try {
			$contactGroup = $service->contactGroups->get('people/me', $optParams);
		} catch (\Google_Exception $error) {
			throw new CustomException('GsuiteInterface/getPeopleConnection:: Explication text' . $error->getMessage(), GS_ERROR1);
		}
		return $contactGroup->toSimpleObject();
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
			throw new CustomException('GsuiteInterface/getListEventsCalendar:: Explication text' . $error->getMessage(), GS_ERROR1);
		}
		$litEvents = [];
		while (true) {
			foreach ($events->getItems() as $event) {
				array_push($litEvents, $event->getSummary());
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

	public function getCalendar($idCalendar)
	{
		$this->setServiceAuthentification();
		$this->setScopeCalendar();
		$service = new Google_Service_Calendar($this->getClient());
		$calendar = $service->calendars->get($idCalendar);
		return $calendar->getSummary();
	}
}