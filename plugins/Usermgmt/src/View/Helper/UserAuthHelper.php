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

namespace Usermgmt\View\Helper;

use Cake\View\Helper;
use Cake\View\View;
use Cake\Utility\Inflector;
use Cake\Routing\Router;

class UserAuthHelper extends Helper {
	public $helpers = ['Html', 'Form'];
	public $permissions = [];
	public $viewobj;
	public $recaptcha_script = false;
	public $request;

	public function __construct(View $View, array $config = []) {
		$this->viewobj = $View;
		$this->request = $View->getRequest();

		if(!defined("QRDN")) {
			define("QRDN", "12345678");
		}
		if(!defined("SITE_URL")) {
			define("SITE_URL", Router::url('/', true));
		}

		parent::__construct($View, $config);
	}

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
		return $this->request->getSession()->read('Auth');
	}

	/**
	 * Used to get user id from session
	 *
	 * @access public
	 * @return integer
	 */
	public function getUserId() {
		return $this->request->getSession()->read('Auth.User.id');
	}

	/**
	 * Used to get group id from session
	 *
	 * @access public
	 * @return integer
	 */
	public function getGroupId() {
		return $this->request->getSession()->read('Auth.User.user_group_id');
	}

	/**
	 * Used to get group name from session
	 *
	 * @access public
	 * @return string
	 */
	public function getGroupName() {
		return $this->request->getSession()->read('Auth.User.user_group.name');
	}

	/**
	 * Used to check is admin logged in
	 *
	 * @access public
	 * @return boolean
	 */
	public function isAdmin() {
		$idName = $this->request->getSession()->read('Auth.User.user_group.id_name');

		if(isset($idName[ADMIN_GROUP_ID])) {
			return true;
		}

		return false;
	}

	/**
	 * Used to check is guest logged in
	 *
	 * @access public
	 * @return boolean
	 */
	public function isGuest() {
		$idName = $this->request->getSession()->read('Auth.User.user_group.id_name');

		if(empty($idName)) {
			return true;
		}

		return false;
	}

	/**
	 * Used to get last login Time
	 *
	 * @access public
	 * @return string
	 */
	public function getLastLoginTime() {
		$last_login = $this->request->getSession()->read('Auth.User.last_login');

		if(!empty($last_login)) {
			return $this->getFormatDatetime($last_login);
		}

		return '';
	}

	/**
	 * Used to show show captcha
	 *
	 * @access public
	 * @param string $error error message
	 * @return string
	 */
	public function showCaptcha($error=null) {
		$this->Form->unlockField('g-recaptcha-response');

		if(!$this->recaptcha_script) {
			$this->recaptcha_script = true;

			$this->Html->script('https://www.google.com/recaptcha/api.js', array('block'=>true));
		}

		$code = '<div class="g-recaptcha" data-sitekey="'.PUBLIC_KEY_FROM_RECAPTCHA.'"></div>';

		$errorMsg = '';

		if(!empty($error)) {
			$errorMsg = "<div class='error-message'>".$error."</div>";
		}

		return $code.$errorMsg;
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
					if($this->request->getSession()->check('Auth.badLoginCount') && $this->request->getSession()->read('Auth.badLoginCount') > BAD_LOGIN_ALLOW_COUNT) {
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
	 * Used to check if permisson check is needed
	 *
	 * @access public
	 * @return boolean
	 */
	public function needToCheckPermission() {
		if(!CHECK_USER_PERMISSIONS) {
			return false;
		}

		$userGroupId = $this->getGroupId();
		$userGroupIds = array_map('trim', explode(',',  strval($userGroupId)));

		if(in_array(ADMIN_GROUP_ID, $userGroupIds) && !CHECK_ADMIN_PERMISSIONS) {
			return false;
		}

		return true;
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
	 * Used to check permission of controller's action
	 *
	 * @access public
	 * @param string $controller controller name
	 * @param string $action action name
	 * @param string $plugin plugin name
	 * @param string $prefix prefix name
	 * @return boolean
	 */
	public function HP($controller=null, $action='index', $plugin=null, $prefix=null) {
		if($this->needToCheckPermission()) {
			if(empty($this->permissions)) {
				$permissionCell = $this->viewobj->cell('Usermgmt.Permission');

				$this->permissions = $permissionCell->getPermissions();

				if(!is_array($this->permissions)) {
					$this->permissions = json_decode($this->permissions, true);
				}
			}

			if(!empty($controller)) {
				$access = Inflector::camelize($controller).'/'.$action;

				if(!empty($plugin)) {
					$access = $plugin.'/'.$access;
				}

				if(!empty($prefix)) {
					$access = strtolower(Inflector::camelize($prefix)).'/'.$access;
				}

				if(is_array($this->permissions) && in_array(strtolower($access), $this->permissions)) {
					return true;
				}

				return false;
			} else {
				echo "Missing Argument 1";
				return false;
			}
		} else {
			return true;
		}
	}

	/**
	 * Used to get pagination start index
	 *
	 * @access public
	 * @return integer
	 */
	public function getPageStart() {
		$start = 0;
		$pagingAttributes = $this->request->getAttribute('paging');

		if(!empty($pagingAttributes)) {
			foreach($pagingAttributes as $attr) {
				$start = ($attr['page']-1) * $attr['perPage'];
			}
		}

		return $start;
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
