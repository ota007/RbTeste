<?php
declare(strict_types=1);

/**
 * CakePHP 4.x User Management Plugin
 * Copyright (c) Chetan Varshney (The Director of Ektanjali Softwares Pvt Ltd), Product Copyright No- 11498/2012-CO/L
 *
 * Licensed under The GPL License
 * For full copyright and license information, please see the LICENSE.txt
 *
 * Product From - https://ektanjali.com
 * Product Demo - https://cakephp4-user-management.ektanjali.com
 */

namespace Usermgmt\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\Event\EventInterface;
use Cake\Utility\Inflector;
use Cake\ORM\TableRegistry;
use Cake\Auth\DefaultPasswordHasher;
use Cake\Auth\BasicAuthenticate;
use Cake\Utility\Security;
use Cake\Routing\Router;
use Cake\Event\Event;
use Cake\Http\Cookie\Cookie;
use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Database\Expression\QueryExpression;
use Cake\Datasource\FactoryLocator;
use DateTime;

class UserAuthComponent extends Component {

	public $components = ['Usermgmt.Ssl'];

	public $registry;
	public $controller;
	public $request;
	public $response;
	public $session;

	public function __construct(ComponentRegistry $registry, array $config = []) {
		$this->registry = $registry;

		parent::__construct($registry, $config);
	}

	public function beforeFilter(EventInterface $event) {
		$this->controller = $this->getController();
		$this->request = $this->controller->getRequest();
		$this->response = $this->controller->getResponse();
		$this->session = $this->request->getSession();

		$this->controller->loadComponent('Auth');

		$this->UsermgmtInIt();
	}

	public function beforeRender(EventInterface $event) {
		$this->checkForHttpsRedirect();

		$this->setVarVariableForView();
	}

	/**
	 * Used to init user management plugin
	 *
	 * @access public
	 * @return void
	 */
	public function usermgmtInIt() {
		$this->setConfigureCache();

		$this->setUserSettings();

		$this->setCustomSettings();

		$this->setAuthConfig();

		$this->checkForCookieLogin();

		$this->checkForSocialLoginChangePassword();

		$this->updateActivity();
	}

	/**
	 * Used to configure cache
	 *
	 * @access public
	 * @return void
	 */
	public function setConfigureCache() {
		$configured = Cache::configured();

		if(!in_array('UserMgmtPermissions', $configured)) {
			Cache::setConfig('UserMgmtPermissions', [
				'className'=>'File',
				'duration'=>'+3 months',
				'path'=>CACHE,
				'prefix'=>'UserMgmt_'
			]);
		}

		if(!in_array('UserMgmtSettings', $configured)) {
			Cache::setConfig('UserMgmtSettings', [
				'className'=>'File',
				'duration'=>'+1 day',
				'path'=>CACHE,
				'prefix'=>'UserMgmt_'
			]);
		}
	}

	/**
	 * Used to set user settings
	 *
	 * @access public
	 * @return void
	 */
	public function setUserSettings() {
		$cacheKey = 'all_settings';

		$allSettings = [];

		if(!Configure::read('debug')) {
			$allSettings = Cache::read($cacheKey, 'UserMgmtSettings');
		}

		if(empty($allSettings)) {
			$allSettings = FactoryLocator::get('Table')->get('Usermgmt.UserSettings')->getAllUserSettings();

			Cache::write($cacheKey, $allSettings, 'UserMgmtSettings');
		}

		foreach($allSettings as $key=>$variable) {
			Configure::write("{$key}", $variable['setting_value']);

			if(!defined(strtoupper($key))) {
				define(strtoupper($key), $variable['setting_value']);
			}
		}

		date_default_timezone_set((isset($allSettings['default_time_zone'])) ? $allSettings['default_time_zone']['setting_value'] : 'America/New_York');
	}

	/**
	 * Used to set custom settings
	 *
	 * @access public
	 * @return void
	 */
	public function setCustomSettings() {
		if(!defined("SITE_URL")) {
			if(!defined('CRON_DISPATCHER')) {
				define("SITE_URL", Router::url('/', true));
			}
		}

		if(!defined("SITE_NAME") && defined("SITE_NAME_FULL")) {
			//use SITE_NAME_FULL everywhere
			define("SITE_NAME", SITE_NAME_FULL);
		}

		if(!defined("USERMGMT_PATH")) {
			define("USERMGMT_PATH", dirname(dirname(dirname(__DIR__))));
		}

		if(!defined("DEFAULT_IMAGE_PATH")) {
			define("DEFAULT_IMAGE_PATH", USERMGMT_PATH.DS."webroot".DS."img".DS."default.png");/* setting path for default image */
		}

		if(!defined("DEFAULT_IMAGE_URL")) {
			define("DEFAULT_IMAGE_URL", SITE_URL."usermgmt/img/default.png");
		}

		// define your more constants or write configure elements
	}

	/**
	 * Used to set Auth component config
	 *
	 * @access public
	 * @return void
	 */
	public function setAuthConfig() {
		if(isset($this->controller->Auth)) {
			$this->controller->Auth->setConfig('authenticate', ['Usermgmt.Permission']);

			$this->controller->Auth->setConfig('authorize', ['Usermgmt.Permission']);

			$this->controller->Auth->setConfig('loginAction', ['controller'=>'Users', 'action'=>'login', 'plugin'=>'Usermgmt', 'prefix'=>false]);

			$this->controller->Auth->setConfig('unauthorizedRedirect', ['controller'=>'Users', 'action'=>'accessDenied', 'plugin'=>'Usermgmt', 'prefix'=>false]);

			$this->controller->Auth->setConfig('loginRedirect', LOGIN_REDIRECT_URL);

			$this->controller->Auth->setConfig('logoutRedirect', LOGOUT_REDIRECT_URL);
		}
	}

	/**
	 * Used to check if user is guest and can we auto login via cookie
	 *
	 * @access public
	 * @return void
	 */
	public function checkForCookieLogin() {
		if(!$this->isLogged()) {
			$cookieValue = $this->request->getCookie(LOGIN_COOKIE_NAME);

			if(!empty($cookieValue)) {
				$this->response = $this->response->withExpiredCookie(new Cookie(LOGIN_COOKIE_NAME));
				$this->controller->setResponse($this->response);

				$tokenParts = explode(':',  strval($cookieValue));

				if(count($tokenParts) == 3) {
					$token = $tokenParts[0];
					$user_id = $tokenParts[1];
					$duration = $tokenParts[2];

					$userTable = FactoryLocator::get('Table')->get('Usermgmt.Users');

					$user = $userTable->getUserByCookieToken(compact('token', 'user_id', 'duration'));

					$this->login($user);

					if(!empty($user)) {
						$this->persist($duration);
					}
				}
			}
		}
	}

	/**
	 * Used to redirect user to change password page if they are registered from social plateform and
	 * show change password page setting is ON
	 *
	 * @access public
	 * @return void
	 */
	public function checkForSocialLoginChangePassword() {
		if($this->session->check('Auth.SocialLogin') && empty($this->request->getParam('requested'))) {
			$this->session->delete('Auth.SocialLogin');

			$this->controller->redirect(['plugin'=>'Usermgmt', 'controller'=>'Users', 'action'=>'changePassword']);
		}
	}

	/**
	 * Used to force use of HTTP/HTTPS based on setting
	 *
	 * @access public
	 * @return void
	 */
	public function checkForHttpsRedirect() {
		$skipForHttps = ['login/fb', 'login/twt', 'login/gmail', 'login/ldn', 'login/fs', 'login/yahoo'];

		if(!$this->request->is('ajax')) {
			if(defined('USE_HTTPS') && USE_HTTPS) {
				$this->Ssl->force();
			}
			else {
				if(defined('HTTPS_URLS')) {
					$httpsUrls = HTTPS_URLS;

					if(!empty($httpsUrls)) {
						$httpsUrls = array_map('trim', explode(',', strtolower($httpsUrls)));
						$httpsUrls = array_map(function($v) { return rtrim(ltrim($v, '/'), '/'); }, $httpsUrls);

						$actionUrl1 = strtolower($this->request->getParam('controller').'/'.$this->request->getParam('action'));
						$actionUrl2 = strtolower($this->request->getParam('controller').'/*');

						if(!empty($this->request->getParam('plugin'))) {
							$actionUrl1 = strtolower($this->request->getParam('plugin')).'/'.$actionUrl1;
							$actionUrl2 = strtolower($this->request->getParam('plugin')).'/'.$actionUrl2;
						}

						if(in_array($actionUrl1, $httpsUrls) || in_array($actionUrl2, $httpsUrls)) {
							if(!in_array($this->request->getPath(), $skipForHttps)) {
								$this->Ssl->force();
							}
						} else {
							$this->Ssl->unforce();
						}
					}
				}
			}
		}
	}

	/**
	 * Used to set $var variable with user details, which is available in view templates
	 *
	 * @access public
	 * @return void
	 */
	public function setVarVariableForView() {
		$userId = $this->getUserId();
		$user = [];

		if($userId) {
			$userTable = FactoryLocator::get('Table')->get('Usermgmt.Users');

			$user = $userTable->getUserById($userId);

			if(empty($user['id'])) {
				$this->controller->redirect(['plugin'=>'Usermgmt', 'controller'=>'Users', 'action'=>'logout']);
			}
		}

		$this->controller->set('var', $user);
	}

	/**
	 * Used to maintain login session of user
	 *
	 * @access public
	 * @param array $user user information for session
	 * @return void
	 */
	public function login($user) {
		if(isset($user['user_group_id'])) {
			$userGroupTable = FactoryLocator::get('Table')->get('Usermgmt.UserGroups');
			$groups = $userGroupTable->getGroupsByIds($user['user_group_id'], true);

			$user['user_group']['id_name'] = $groups;
			$user['user_group']['name'] = implode(', ', $groups);
		}

		$loginAllowed = $this->checkForLoginAllowed($user);

		if($loginAllowed) {
			$this->updateLastLoginTime($user);

			unset($user['password']);

			$this->controller->Auth->setUser($user);

			$this->session->delete('Auth.badLoginCount');
		} else {
			$this->controller->Flash->info(__('Your account is currently logged in on another computer.'));
			$this->controller->redirect(['plugin'=>'Usermgmt', 'controller'=>'Users', 'action'=>'login']);
		}
	}

	public function checkForLoginAllowed($user) {
		$loginAllowed = true;

		if(isset($user['id'])) {
			$gids = [];

			if(isset($user['user_group']['id_name'])) {
				$gids = $user['user_group']['id_name'];
			}

			$loginAllowed = $this->isAllowedLogin($user['id'], $gids);
		}

		return $loginAllowed;
	}

	public function isAllowedLogin($userId, $groupIds) {
		$allowMultipleLogin = ALLOW_USER_MULTIPLE_LOGIN;

		if(isset($groupIds[ADMIN_GROUP_ID])) {
			$allowMultipleLogin = ALLOW_ADMIN_MULTIPLE_LOGIN;
		}

		if(!$allowMultipleLogin) {
			$useragent = $this->getUserActivityCookie();
			$last_action = time() - (abs(LOGIN_IDLE_TIME) * 60);

			$activityTable = FactoryLocator::get('Table')->get('Usermgmt.UserActivities');

			$res = $activityTable->find()
						->where(['UserActivities.user_id'=>$userId, 'UserActivities.last_action >'=>$last_action, 'UserActivities.useragent !='=>$useragent])
						->first();

			if(!empty($res)) {
				if($res['is_logout'] || $res['is_deleted']) {
					return true;
				}
				return false;
			}
			else {
				$activityTable->updateAll(['is_logout'=>1], ['user_id'=>$userId, 'useragent !='=>$useragent]);
			}
		}
		return true;
	}

	public function updateLastLoginTime($user) {
		if(isset($user['id'])) {
			$userTable = FactoryLocator::get('Table')->get('Usermgmt.Users');

			$userEntity = $userTable->newEmptyEntity();

			$userEntity['id'] = $user['id'];
			$userEntity['last_login'] = date('Y-m-d H:i:s');

			$userTable->save($userEntity, ['validate'=>false]);
		}
	}

	/**
	 * Used to maintain login cookie on remember me option, it is used to auto login user
	 *
	 * @access public
	 * @param string $duration duration time
	 * @return void
	 */
	public function persist($duration = '2 weeks') {
		$userId = $this->getUserId();

		if(!empty($userId)) {
			$loginTokenTable = FactoryLocator::get('Table')->get('Usermgmt.LoginTokens');

			$token = $loginTokenTable->saveCookieToken($userId, $duration);

			// Add a cookie
			$cookie = new Cookie(
				LOGIN_COOKIE_NAME,
				$token,
				new DateTime('+'.$duration),
				'/',
				'',
				false,
				true
			);

			$this->response = $this->response->withCookie($cookie);
			$this->controller->setResponse($this->response);
		}
	}

	/**
	 * Used to delete user session and cookie
	 *
	 * @access public
	 * @return void
	 */
	public function logout() {
		$this->deleteActivity($this->getUserId());

		$this->clearSessionAndCookie();

		$this->controller->redirect($this->controller->Auth->logout());
	}

	public function clearSessionAndCookie() {
		$this->response = $this->response->withExpiredCookie(new Cookie(LOGIN_COOKIE_NAME));
		$this->controller->setResponse($this->response);

		if(defined('FB_APP_ID')) {
			$this->session->delete("fb_".FB_APP_ID."_code");
			$this->session->delete("fb_".FB_APP_ID."_access_token");
			$this->session->delete("fb_".FB_APP_ID."_user_id");
		}

		$this->session->delete("ot");
		$this->session->delete("ots");
		$this->session->delete("oauth.linkedin");
		$this->session->delete("fs_access_token");
		$this->session->delete("G_token");
	}

	public function getUserActivityCookie() {
		$useragent = $this->request->getCookie('usermgmt_user_activity');

		if(empty($useragent)) {
			$user_browser = $this->request->getHeader('User-Agent');

			if(!empty($user_browser[0])) {
				$user_browser = $user_browser[0];
			} else {
				$user_browser = '';
			}

			$useragent = md5($user_browser).session_id();

			// Add a cookie
			$cookie = new Cookie(
				'usermgmt_user_activity',
				$useragent,
				new DateTime('+ 2 months'),
				'/',
				'',
				false,
				true
			);

			$this->response = $this->response->withCookie($cookie);
			$this->controller->setResponse($this->response);
		}

		return $useragent;
	}

	/**
	 * Used to update update activities of user or a guest
	 *
	 * @access public
	 * @return void
	 */
	public function updateActivity() {
		if(!defined('CRON_DISPATCHER')) {
			$actionUrl = Inflector::camelize($this->request->getParam('controller')).'/'.$this->request->getParam('action');

			if(!in_array($actionUrl, ['Users/login', 'Users/logout']) && empty($this->request->getParam('requested'))) {
				$useragent = $this->getUserActivityCookie();
				$userId = $this->getUserId();

				$activityTable = FactoryLocator::get('Table')->get('Usermgmt.UserActivities');

				$userActivityEntity = $activityTable->findByUseragent($useragent)->first();

				if(empty($userActivityEntity)) {
					$userActivityEntity = $activityTable->newEmptyEntity();
				}

				if(!empty($userActivityEntity['is_logout']) && !empty($userActivityEntity['user_id']) && $userActivityEntity['user_id'] == $userId) {
					return $this->controller->redirect(['plugin'=>'Usermgmt', 'controller'=>'Users', 'action'=>'logout']);
				}
				if(!empty($userActivityEntity['is_deleted']) && !empty($userActivityEntity['user_id']) && $userActivityEntity['user_id'] == $userId) {
					return $this->controller->redirect(['plugin'=>'Usermgmt', 'controller'=>'Users', 'action'=>'logout']);
				}

				$user_browser = $this->request->getHeader('User-Agent');

				if(!empty($user_browser[0])) {
					$userActivityEntity['user_browser'] = $user_browser[0];
				}

				$userActivityEntity['useragent'] = $useragent;
				$userActivityEntity['user_id'] = $userId;
				$userActivityEntity['last_action'] = time();
				$userActivityEntity['last_url'] = $this->request->getRequestTarget();
				$userActivityEntity['ip_address'] = $this->request->clientIp();

				unset($userActivityEntity['modified']);

				$activityTable->save($userActivityEntity, ['validate'=>false]);
			}
		}
	}

	public function deleteActivity($userId) {
		if(!empty($userId)) {
			$activityTable = FactoryLocator::get('Table')->get('Usermgmt.UserActivities');

			$useragent = $this->getUserActivityCookie();

			$activityTable->deleteAll(['user_id'=>$userId, 'useragent'=>$useragent]);
		}
	}

	/********************************************** USEFUL FUNCTIONS ****************************************/

	/**
	 * Used to check whether user is logged in or not
	 *
	 * @access public
	 * @return boolean
	 */
	public function isLogged() {
		if($this->getUserId()) {
			return true;
		}
		return false;
	}

	/**
	 * Used to get user from session
	 *
	 * @access public
	 * @return array
	 */
	public function getUser() {
		return $this->session->read('Auth');
	}

	/**
	 * Used to get user id from session
	 *
	 * @access public
	 * @return integer
	 */
	public function getUserId() {
		return $this->session->read('Auth.User.id');
	}

	/**
	 * Used to get group id from session
	 *
	 * @access public
	 * @return integer
	 */
	public function getGroupId() {
		return $this->session->read('Auth.User.user_group_id');
	}

	/**
	 * Used to get group name from session
	 *
	 * @access public
	 * @return string
	 */
	public function getGroupName() {
		return $this->session->read('Auth.User.user_group.name');
	}

	/**
	 * Used to check is admin logged in
	 *
	 * @access public
	 * @return string
	 */
	public function isAdmin() {
		$idName = $this->session->read('Auth.User.user_group.id_name');

		if(isset($idName[ADMIN_GROUP_ID])) {
			return true;
		}

		return false;
	}

	/**
	 * Used to check is guest logged in
	 *
	 * @access public
	 * @return string
	 */
	public function isGuest() {
		$idName = $this->session->read('Auth.User.user_group.id_name');

		if(empty($idName)) {
			return true;
		}

		return false;
	}

	/**
	 * Used to make password in hash format
	 *
	 * @access public
	 * @param string $password password of user
	 * @return hash
	 */
	public function makeHashedPassword($password) {
		return (new DefaultPasswordHasher)->hash($password);
	}

	/**
	 * Used to check user password with database password
	 *
	 * @access public
	 * @param string $password password of user
	 * @param string $dbpassword database password of user
	 * @param array $options options array
	 * @return boolean
	 */
	public function checkPassword($password, $dbpassword, $options=[]) {
		if(!isset($options['passwordHasher'])) {
			$options['passwordHasher'] = 'Default';
		}

		$passwordHasher = [];

		if(!empty($options)) {
			if(strtolower($options['passwordHasher']) == 'ump2' && !empty($options['salt'])) {
				//cakephp 2.x old password compatibility
				if(strlen($options['salt']) == 32) {
					//cakephp 2.x user management plugin version upto 2.2.1 version
					return $dbpassword === md5(md5($password).md5($options['salt']));
				}
				else {
					//cakephp 2.x user management plugin version greater than 2.2.1 version
					$options['salt'] = base64_decode($options['salt']).Security::getSalt();
					return $dbpassword === Security::hash($password, 'sha256', $options['salt']);
				}
			}
			else {
				//cakephp 2.x old password compatibility (which are not using our cakephp 2.x user management plugin)
				$passwordHasher['passwordHasher']['className'] = $options['passwordHasher'];
			}

			if(isset($options['hashType'])) {
				$passwordHasher['passwordHasher']['hashType'] = $options['hashType'];
			}
		}

		//cakephp 3.x & 4.x

		$hasher = (new BasicAuthenticate($this->registry, $passwordHasher))->passwordHasher();

		return $hasher->check($password, $dbpassword);
	}

	/**
	 * Used to generate random password
	 *
	 * @access public
	 * @return string
	 */
	public function generatePassword() {
		return substr(md5(mt_rand(0, 32) . time()), 0,7);
	}

	/**
	 * It is used to generate unique username
	 *
	 * @access public
	 * @param string $name user's name to generate username
	 * @return string
	 */
	public function generateUserName($name=null) {
		$name = str_replace(' ', '', strtolower($name));
		$username = 'user_'.time().rand(1000, 9999);

		if(!empty($name)) {
			$userTable = FactoryLocator::get('Table')->get('Usermgmt.Users');

			$username = $name;

			while($userTable->exists(['Users.username'=>$username]) || !$userTable->isUsernameAvailable($username)) {
				$username = $name.'_'.rand(1000, 9999);
			}
		}

		return $username;
	}

	/**
	 * It is used to update profile pic from given url
	 *
	 * @access public
	 * @param url $file_location url of pic
	 * @return string
	 */
	public function updateProfilePic($file_location) {
		$fullpath = WWW_ROOT."library".DS.IMG_DIR;

		if(!is_dir($fullpath)) {
			mkdir($fullpath, 0777, true);
		}

		$imgContent = file_get_contents($file_location);
		$photo = time().mt_rand().".jpg";

		$fp = fopen($fullpath.DS.$photo, "w");
		fwrite($fp, $imgContent);
		fclose($fp);

		return $photo;
	}

	/**
	 * It is used to delete tmp cache
	 * $congig = ['type'=>'all', 'increase_qrdn'=>true, 'truncate_user_activities_table'=>true]
	 * type = 'all', 'models', 'persistent', 'views', 'user_settings', 'permissions'
	 * increase_qrdn = true|false
	 * truncate_user_activities_table = true|false
	 *
	 * @access public
	 * @param array $congig array of features
	 * @return string
	 */
	public function deleteCache($congig) {
		$default = ['type'=>'all', 'increase_qrdn'=>false, 'truncate_user_activities_table'=>false];

		$congig = $default + $congig;

		$success = true;

		$iterator = new \RecursiveDirectoryIterator(CACHE);

		foreach(new \RecursiveIteratorIterator($iterator, \RecursiveIteratorIterator::CHILD_FIRST) as $file) {
			if(!in_array($file->getBasename(), ['.svn', '.', '..'])) {
				$filepath = $file->getPath();
				$filepathname = $file->getPathname();
				$basename = $file->getBasename();

				if($congig['type'] == 'all' || $congig['type'] == 'models') {
					if($filepath == CACHE.'models') {
						if(!@unlink($filepathname)) {
							$success = false;
						}
					}
				}

				if($congig['type'] == 'all' || $congig['type'] == 'persistent') {
					if($filepath == CACHE.'persistent') {
						if(!@unlink($filepathname)) {
							$success = false;
						}
					}
				}

				if($congig['type'] == 'all' || $congig['type'] == 'views') {
					if($filepath == CACHE.'views') {
						if(!@unlink($filepathname)) {
							$success = false;
						}
					}
				}

				if($filepath == TMP.'cache') {
					if($congig['type'] == 'all') {
						if(!is_dir($filepathname) && strpos($basename, 'UserMgmt_') !== false) {
							if(!@unlink($filepathname)) {
								$success = false;
							}
						}
					}
					if($congig['type'] == 'user_settings') {
						if(!is_dir($filepathname) && strpos($basename, 'UserMgmt_all_settings') !== false) {
							if(!@unlink($filepathname)) {
								$success = false;
							}
						}
					}
					if($congig['type'] == 'permissions') {
						if(!is_dir($filepathname) && strpos($basename, 'UserMgmt_rules_for_group') !== false) {
							if(!@unlink($filepathname)) {
								$success = false;
							}
						}
					}
				}
			}
		}

		if($congig['increase_qrdn']) {
			$userSettingTable = FactoryLocator::get('Table')->get('Usermgmt.UserSettings');

			$expression = new QueryExpression('setting_value = setting_value + 1');
			$userSettingTable->updateAll([$expression], ['setting_key'=>'qrdn']);
		}

		if($congig['truncate_user_activities_table']) {
			$UserActivityTable = FactoryLocator::get('Table')->get('Usermgmt.UserActivities');

			$UserActivityTable->deleteAll(['1'=>'1']);
		}

		return $success;
	}

	/**
	 * Used to get last login time
	 *
	 * @access public
	 * @return string
	 */
	public function getLastLoginTime() {
		$last_login = $this->session->read('Auth.User.last_login');

		if(!empty($last_login)) {
			return $this->getFormatDatetime($last_login);
		}
		return '';
	}

	public function setBadLoginCount() {
		$count = 1;

		if($this->session->check('Auth.badLoginCount')) {
			$count += $this->session->read('Auth.badLoginCount');
		}

		$this->session->write('Auth.badLoginCount', $count);
	}

	/**
	 * Used to check can we use repatcha on bad login
	 *
	 * @access public
	 * @return boolean
	 */
	public function captchaOnBadLogin() {
		if($this->session->check('Auth.badLoginCount')) {
			if($this->session->read('Auth.badLoginCount') > BAD_LOGIN_ALLOW_COUNT) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Used to check can we use repatcha on given type page
	 *
	 * @access public
	 * @param string $type page types are 'login', 'registration', 'forgotPassword', 'emailVerification', 'contactus'
	 * @return boolean
	 */
	public function canUseRecaptha($type=null) {
		$privatekey = PRIVATE_KEY_FROM_RECAPTCHA;
		$publickey = PUBLIC_KEY_FROM_RECAPTCHA;

		if(!empty($privatekey) && !empty($publickey)) {
			if($type == 'login') {
				if(USE_RECAPTCHA_ON_LOGIN) {
					return true;
				}
				else if(USE_RECAPTCHA_ON_BAD_LOGIN) {
					if($this->session->check('Auth.badLoginCount') && $this->session->read('Auth.badLoginCount') > BAD_LOGIN_ALLOW_COUNT) {
						return true;
					}
				}
			}
			else if($type == 'registration') {
				if(USE_RECAPTCHA_ON_REGISTRATION) {
					return true;
				}
			}
			else if($type == 'forgotPassword') {
				if(USE_RECAPTCHA_ON_FORGOT_PASSWORD) {
					return true;
				}
			}
			else if($type == 'emailVerification') {
				if(USE_RECAPTCHA_ON_EMAIL_VERIFICATION) {
					return true;
				}
			}
			else if($type == 'contactus') {
				if(!$this->isLogged()) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Used to format date
	 *
	 * @access public
	 * @param mixed $dateObj string or date object
	 * @return string
	 */
	public function getFormatDate($dateObj) {
		if(is_object($dateObj)) {
			return $dateObj->i18nFormat('dd-MMM-yyyy', date_default_timezone_get());
		}
		else if(!empty($dateObj)) {
			return date('d-M-Y', strtotime($dateObj));
		}

		return null;
	}

	/**
	 * Used to format datetime
	 *
	 * @access public
	 * @param mixed $dateObj string or date object
	 * @return string
	 */
	public function getFormatDatetime($dateObj) {
		if(is_object($dateObj)) {
			return $dateObj->i18nFormat('dd-MMM-yyyy hh:mm a', date_default_timezone_get());
		}
		else if(!empty($dateObj)) {
			return date('d-M-Y h:i A', strtotime($dateObj));
		}

		return null;
	}

	/**
	 * Used to format time
	 *
	 * @access public
	 * @param mixed $dateObj string or date object
	 * @return string
	 */
	public function getFormatTime($dateObj) {
		if(is_object($dateObj)) {
			return $dateObj->i18nFormat('hh:mm a', date_default_timezone_get());
		}
		else if(!empty($dateObj)) {
			return date('h:i A', strtotime($dateObj));
		}

		return null;
	}

	/**
	 * Used to generate activation key
	 *
	 * @access public
	 * @param string $string string
	 * @return hash
	 */
	public function getActivationKey($string) {
		return md5(md5($string).Security::getSalt());
	}

	/**
	 * Used to encrypt field
	 *
	 * @access public
	 * @param string $string string
	 * @return hash
	 */
	public function encryptField($field) {
		return base64_encode($field.'.'.Security::getSalt());
	}

	/**
	 * Used to decrypt field
	 *
	 * @access public
	 * @param hash $hash hash string
	 * @return string
	 */
	public function decryptField($hash) {
		$fieldDecrypt = base64_decode($hash);

		if(strpos($fieldDecrypt, '.') !== false) {
			$fieldDecrypt = explode('.', $fieldDecrypt);
			$field = array_shift($fieldDecrypt);
			$salt = implode('.', $fieldDecrypt);

			if(Security::getSalt() === $salt) {
				return $field;
			}
		}

		return 'unknown';
	}

	/**
	 * Used to get pagination page number
	 *
	 * @access public
	 * @return integer
	 */
	public function getPageNumber() {
		$page = 1;
		$pagingAttributes = $this->request->getAttribute('paging');
		$pageQuery = $this->request->getQuery('page');

		if(!empty($pagingAttributes)) {
			foreach($pagingAttributes as $attr) {
				$page = $attr['page'];
			}
		}
		else if(!empty($pageQuery)) {
			$page = $pageQuery;
		}

		return $page;
	}
}
