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

namespace Usermgmt\Model\Table;

use Usermgmt\Model\Table\UsermgmtAppTable;
use Cake\Validation\Validator;
use Cake\Datasource\FactoryLocator;
use Cake\Mailer\Mailer;
use Cake\Core\App;
use Cake\Core\Plugin;
use Cake\Filesystem\Folder;
use Cake\Routing\Router;
use Cake\Utility\Security;
use Cake\Validation\Validation;
use Cake\Utility\Inflector;

class UsersTable extends UsermgmtAppTable {

	public $UserAuth;
	public $ControllerList;
	public $multiUsers = [];

	public function initialize(array $config): void {
		$this->addBehavior('Timestamp');

		$this->hasOne('Usermgmt.UserDetails');
	}

	public function validationForLogin($validator) {
		$validator
			->add('email', [
				'notBlank'=>[
					'rule'=>'notBlank',
					'message'=>__('Please enter email or username'),
					'last'=>true
				]
			])

			->add('password', [
				'notBlank'=>[
					'rule'=>'notBlank',
					'message'=>__('Please enter password'),
					'last'=>true
				]
			])

			->add('captcha', [
				'notBlank'=>[
					'rule'=>'notBlank',
					'message'=>__('Please select I\'m not a robot'),
					'last'=>true
				],
				'mustMatch'=>[
					'rule'=>'recaptchaValidate',
					'provider'=>'table',
					'message'=>__('Prove you are not a robot')
				]
			]);

		return $validator;
	}

	public function validationForRegister($validator) {
		$validator
			->add('user_group_id', [
				'notBlank'=>[
					'rule'=>'notBlank',
					'message'=>__('Please select group'),
					'last'=>true
				]
			])

			->add('username', [
				'notBlank'=>[
					'rule'=>'notBlank',
					'message'=>__('Please enter username'),
					'last'=>true
				],
				'mustBeValid'=>[
					'rule'=>'alphaNumericDashUnderscore',
					'provider'=>'table',
					'message'=>__('Please enter a valid username'),
					'last'=>true
				],
				'mustBeAlpha'=>[
					'rule'=>'alpha',
					'provider'=>'table',
					'message'=>__('Please enter a valid username'),
					'last'=>true
				],
				'unique'=>[
					'rule'=>'validateUnique',
					'provider'=>'table',
					'message'=>__('This username already exist'),
					'last'=>true
				],
				'mustBeLonger'=>[
					'rule'=>['minLength', 4],
					'message'=>__('Username must be greater than 3 characters'),
					'last'=>true
				],
				'mustNotBanned'=>[
					'rule'=>'checkForUsernameNotAvailable',
					'provider'=>'table',
					'message'=>__('This Username is not available'),
					'last'=>true
				]
			])

			->add('first_name', [
				'notBlank'=>[
					'rule'=>'notBlank',
					'message'=>__('Please enter first name'),
					'last'=>true
				],
				'mustBeValid'=>[
					'rule'=>'alphaNumericDashUnderscoreSpace',
					'provider'=>'table',
					'message'=>__('Please enter a valid first name'),
					'last'=>true
				],
				'mustBeAlpha'=>[
					'rule'=>'alpha',
					'provider'=>'table',
					'message'=>__('Please enter a valid first name'),
					'last'=>true
				]
			])

			->allowEmptyString('last_name')
			->add('last_name', [
				'mustBeValid'=>[
					'rule'=>'alphaNumericDashUnderscoreSpace',
					'provider'=>'table',
					'message'=>__('Please enter a valid last name'),
					'last'=>true
				],
				'mustBeAlpha'=>[
					'rule'=>'alpha',
					'provider'=>'table',
					'message'=>__('Please enter a valid last name'),
					'last'=>true
				]
			])

			->add('email', [
				'notBlank'=>[
					'rule'=>'notBlank',
					'message'=>__('Please enter email'),
					'last'=>true
				],
				'validFormat'=>[
					'rule'=>'email',
					'message'=>__('Please enter valid email'),
					'last'=>true
				],
				'unique'=>[
					'rule'=>'validateUnique',
					'provider'=>'table',
					'message'=>__('This email already exist')
				]
			])

			->add('password', [
				'notBlank'=>[
					'rule'=>'notBlank',
					'message'=>__('Please enter password'),
					'last'=>true
				],
				'mustBeLonger'=>[
					'rule'=>['minLength', 6],
					'message'=>__('Password must be greater than 5 characters'),
					'last'=>true
				]
			])

			->add('cpassword', [
				'notBlank'=>[
					'rule'=>'notBlank',
					'message'=>__('Please enter password'),
					'last'=>true
				],
				'mustMatch'=>[
					'rule'=>'checkForSamePassword',
					'provider'=>'table',
					'message'=>__('Both password must match')
				]
			])

			->add('captcha', [
				'notBlank'=>[
					'rule'=>'notBlank',
					'message'=>__('Please select I\'m not a robot'),
					'last'=>true
				],
				'mustMatch'=>[
					'rule'=>'recaptchaValidate',
					'provider'=>'table',
					'message'=>__('Prove you are not a robot')
				]
			]);

		return $validator;
	}

	public function validationForAddUser($validator) {
		$validator
			->add('user_group_id', [
				'notBlank'=>[
					'rule'=>'notBlank',
					'message'=>__('Please select group'),
					'last'=>true
				]
			])

			->add('username', [
				'notBlank'=>[
					'rule'=>'notBlank',
					'message'=>__('Please enter username'),
					'last'=>true
				],
				'mustBeValid'=>[
					'rule'=>'alphaNumericDashUnderscore',
					'provider'=>'table',
					'message'=>__('Please enter a valid username'),
					'last'=>true
				],
				'mustBeAlpha'=>[
					'rule'=>'alpha',
					'provider'=>'table',
					'message'=>__('Please enter a valid username'),
					'last'=>true
				],
				'unique'=>[
					'rule'=>'validateUnique',
					'provider'=>'table',
					'message'=>__('This username already exist'),
					'last'=>true
				],
				'mustBeLonger'=>[
					'rule'=>['minLength', 4],
					'message'=>__('Username must be greater than 3 characters'),
					'last'=>true
				],
				'mustNotBanned'=>[
					'rule'=>'checkForUsernameNotAvailable',
					'provider'=>'table',
					'message'=>__('This Username is not available'),
					'last'=>true
				]
			])

			->add('first_name', [
				'notBlank'=>[
					'rule'=>'notBlank',
					'message'=>__('Please enter first name'),
					'last'=>true
				],
				'mustBeValid'=>[
					'rule'=>'alphaNumericDashUnderscoreSpace',
					'provider'=>'table',
					'message'=>__('Please enter a valid first name'),
					'last'=>true
				],
				'mustBeAlpha'=>[
					'rule'=>'alpha',
					'provider'=>'table',
					'message'=>__('Please enter a valid first name'),
					'last'=>true
				]
			])

			->allowEmptyString('last_name')
			->add('last_name', [
				'mustBeValid'=>[
					'rule'=>'alphaNumericDashUnderscoreSpace',
					'provider'=>'table',
					'message'=>__('Please enter a valid last name'),
					'last'=>true
				],
				'mustBeAlpha'=>[
					'rule'=>'alpha',
					'provider'=>'table',
					'message'=>__('Please enter a valid last name'),
					'last'=>true
				]
			])

			->add('email', [
				'notBlank'=>[
					'rule'=>'notBlank',
					'message'=>__('Please enter email'),
					'last'=>true
				],
				'validFormat'=>[
					'rule'=>'email',
					'message'=>__('Please enter valid email'),
					'last'=>true
				],
				'unique'=>[
					'rule'=>'validateUnique',
					'provider'=>'table',
					'message'=>__('This email already exist')
				]
			])

			->add('password', [
				'notBlank'=>[
					'rule'=>'notBlank',
					'message'=>__('Please enter password'),
					'last'=>true
				],
				'mustBeLonger'=>[
					'rule'=>['minLength', 6],
					'message'=>__('Password must be greater than 5 characters'),
					'last'=>true
				]
			])

			->add('cpassword', [
				'notBlank'=>[
					'rule'=>'notBlank',
					'message'=>__('Please enter password'),
					'last'=>true
				],
				'mustMatch'=>[
					'rule'=>'checkForSamePassword',
					'provider'=>'table',
					'message'=>__('Both password must match')
				]
			]);

		return $validator;
	}

	public function validationForEditUser($validator) {
		$validator
			->add('user_group_id', [
				'notBlank'=>[
					'rule'=>'notBlank',
					'message'=>__('Please select group'),
					'last'=>true
				]
			])

			->add('username', [
				'notBlank'=>[
					'rule'=>'notBlank',
					'message'=>__('Please enter username'),
					'last'=>true
				],
				'mustBeValid'=>[
					'rule'=>'alphaNumericDashUnderscore',
					'provider'=>'table',
					'message'=>__('Please enter a valid username'),
					'last'=>true
				],
				'mustBeAlpha'=>[
					'rule'=>'alpha',
					'provider'=>'table',
					'message'=>__('Please enter a valid username'),
					'last'=>true
				],
				'unique'=>[
					'rule'=>'validateUnique',
					'provider'=>'table',
					'message'=>__('This username already exist'),
					'last'=>true
				],
				'mustBeLonger'=>[
					'rule'=>['minLength', 4],
					'message'=>__('Username must be greater than 3 characters'),
					'last'=>true
				],
				'mustNotBanned'=>[
					'rule'=>'checkForUsernameNotAvailable',
					'provider'=>'table',
					'message'=>__('This Username is not available'),
					'last'=>true
				]
			])

			->add('first_name', [
				'notBlank'=>[
					'rule'=>'notBlank',
					'message'=>__('Please enter first name'),
					'last'=>true
				],
				'mustBeValid'=>[
					'rule'=>'alphaNumericDashUnderscoreSpace',
					'provider'=>'table',
					'message'=>__('Please enter a valid first name'),
					'last'=>true
				],
				'mustBeAlpha'=>[
					'rule'=>'alpha',
					'provider'=>'table',
					'message'=>__('Please enter a valid first name'),
					'last'=>true
				]
			])

			->allowEmptyString('last_name')
			->add('last_name', [
				'mustBeValid'=>[
					'rule'=>'alphaNumericDashUnderscoreSpace',
					'provider'=>'table',
					'message'=>__('Please enter a valid last name'),
					'last'=>true
				],
				'mustBeAlpha'=>[
					'rule'=>'alpha',
					'provider'=>'table',
					'message'=>__('Please enter a valid last name'),
					'last'=>true
				]
			])

			->add('email', [
				'notBlank'=>[
					'rule'=>'notBlank',
					'message'=>__('Please enter email'),
					'last'=>true
				],
				'validFormat'=>[
					'rule'=>'email',
					'message'=>__('Please enter valid email'),
					'last'=>true
				],
				'unique'=>[
					'rule'=>'validateUnique',
					'provider'=>'table',
					'message'=>__('This email already exist')
				]
			])

			->allowEmptyFile('photo_file')
			->add('photo_file', [
				'validType'=>[
					'rule'=>'checkForPhotoFile',
					'provider'=>'table',
					'message'=>__('Please upload a valid image')
				]
			])

			->allowEmptyDate('bday')
			->add('bday', [
				'validDate'=>[
					'rule'=>['date', 'ymd'],
					'message'=>__('Please select valid date')
				]
			]);

		return $validator;
	}

	public function validationForEditProfile($validator) {
		$validator
			->add('username', [
				'notBlank'=>[
					'rule'=>'notBlank',
					'message'=>__('Please enter username'),
					'last'=>true
				],
				'mustBeValid'=>[
					'rule'=>'alphaNumericDashUnderscore',
					'provider'=>'table',
					'message'=>__('Please enter a valid username'),
					'last'=>true
				],
				'mustBeAlpha'=>[
					'rule'=>'alpha',
					'provider'=>'table',
					'message'=>__('Please enter a valid username'),
					'last'=>true
				],
				'unique'=>[
					'rule'=>'validateUnique',
					'provider'=>'table',
					'message'=>__('This username already exist'),
					'last'=>true
				],
				'mustBeLonger'=>[
					'rule'=>['minLength', 4],
					'message'=>__('Username must be greater than 3 characters'),
					'last'=>true
				],
				'mustNotBanned'=>[
					'rule'=>'checkForUsernameNotAvailable',
					'provider'=>'table',
					'message'=>__('This Username is not available'),
					'last'=>true
				]
			])

			->add('first_name', [
				'notBlank'=>[
					'rule'=>'notBlank',
					'message'=>__('Please enter first name'),
					'last'=>true
				],
				'mustBeValid'=>[
					'rule'=>'alphaNumericDashUnderscoreSpace',
					'provider'=>'table',
					'message'=>__('Please enter a valid first name'),
					'last'=>true
				],
				'mustBeAlpha'=>[
					'rule'=>'alpha',
					'provider'=>'table',
					'message'=>__('Please enter a valid first name'),
					'last'=>true
				]
			])

			->allowEmptyString('last_name')
			->add('last_name', [
				'mustBeValid'=>[
					'rule'=>'alphaNumericDashUnderscoreSpace',
					'provider'=>'table',
					'message'=>__('Please enter a valid last name'),
					'last'=>true
				],
				'mustBeAlpha'=>[
					'rule'=>'alpha',
					'provider'=>'table',
					'message'=>__('Please enter a valid last name'),
					'last'=>true
				]
			])

			->add('email', [
				'notBlank'=>[
					'rule'=>'notBlank',
					'message'=>__('Please enter email'),
					'last'=>true
				],
				'validFormat'=>[
					'rule'=>'email',
					'message'=>__('Please enter valid email'),
					'last'=>true
				],
				'unique'=>[
					'rule'=>'validateUnique',
					'provider'=>'table',
					'message'=>__('This email already exist')
				]
			])

			->add('gender', [
				'notBlank'=>[
					'rule'=>'notBlank',
					'message'=>__('Please select gender'),
					'last'=>true
				]
			])

			->allowEmptyFile('photo_file')
			->add('photo_file', [
				'validType'=>[
					'rule'=>'checkForPhotoFile',
					'provider'=>'table',
					'message'=>__('Please upload a valid image')
				]
			])

			->allowEmptyDate('bday')
			->add('bday', [
				'validDate'=>[
					'rule'=>['date', 'ymd'],
					'message'=>__('Please select valid date')
				]
			]);

		return $validator;
	}

	public function validationForChangePassword($validator) {
		$validator
			->add('oldpassword', [
				'notBlank'=>[
					'rule'=>'notBlank',
					'message'=>__('Please enter old password'),
					'last'=>true
				],
				'mustMatch'=>[
					'rule'=>'verifyOldPass',
					'provider'=>'table',
					'message'=>__('Please enter correct old password'),
					'last'=>true
				]
			])

			->add('password', [
				'notBlank'=>[
					'rule'=>'notBlank',
					'message'=>__('Please enter password'),
					'last'=>true
				],
				'mustBeLonger'=>[
					'rule'=>['minLength', 6],
					'message'=>__('Password must be greater than 5 characters'),
					'last'=>true
				]
			])

			->add('cpassword', [
				'notBlank'=>[
					'rule'=>'notBlank',
					'message'=>__('Please enter password'),
					'last'=>true
				],
				'mustMatch'=>[
					'rule'=>'checkForSamePassword',
					'provider'=>'table',
					'message'=>__('Both password must match')
				]
			]);

		return $validator;
	}

	public function validationForChangeUserPassword($validator) {
		$validator
			->add('password', [
				'notBlank'=>[
					'rule'=>'notBlank',
					'message'=>__('Please enter password'),
					'last'=>true
				],
				'mustBeLonger'=>[
					'rule'=>['minLength', 6],
					'message'=>__('Password must be greater than 5 characters'),
					'last'=>true
				]
			])

			->add('cpassword', [
				'notBlank'=>[
					'rule'=>'notBlank',
					'message'=>__('Please enter password'),
					'last'=>true
				],
				'mustMatch'=>[
					'rule'=>'checkForSamePassword',
					'provider'=>'table',
					'message'=>__('Both password must match')
				]
			]);

		return $validator;
	}

	public function validationForForgotPassword($validator) {
		$validator
			->add('email', [
				'notBlank'=>[
					'rule'=>'notBlank',
					'message'=>__('Please enter email or username'),
					'last'=>true
				]
			])

			->add('captcha', [
				'notBlank'=>[
					'rule'=>'notBlank',
					'message'=>__('Please select I\'m not a robot'),
					'last'=>true
				],
				'mustMatch'=>[
					'rule'=>'recaptchaValidate',
					'provider'=>'table',
					'message'=>__('Prove you are not a robot')
				]
			]);

		return $validator;
	}

	public function validationForActivatePassword($validator) {
		$validator
			->add('password', [
				'notBlank'=>[
					'rule'=>'notBlank',
					'message'=>__('Please enter password'),
					'last'=>true
				],
				'mustBeLonger'=>[
					'rule'=>['minLength', 6],
					'message'=>__('Password must be greater than 5 characters'),
					'last'=>true
				]
			])

			->add('cpassword', [
				'notBlank'=>[
					'rule'=>'notBlank',
					'message'=>__('Please enter password'),
					'last'=>true
				],
				'mustMatch'=>[
					'rule'=>'checkForSamePassword',
					'provider'=>'table',
					'message'=>__('Both password must match')
				]
			]);

		return $validator;
	}

	public function validationForEmailVerification($validator) {
		$validator
			->add('email', [
				'notBlank'=>[
					'rule'=>'notBlank',
					'message'=>__('Please enter email or username'),
					'last'=>true
				]
			])

			->add('captcha', [
				'notBlank'=>[
					'rule'=>'notBlank',
					'message'=>__('Please select I\'m not a robot'),
					'last'=>true
				],
				'mustMatch'=>[
					'rule'=>'recaptchaValidate',
					'provider'=>'table',
					'message'=>__('Prove you are not a robot')
				]
			]);

		return $validator;
	}

	public function validationForMultipleUsers($validator) {
		$validator
			->add('user_group_id', [
				'notBlank'=>[
					'rule'=>'notBlank',
					'message'=>__('Please select group'),
					'last'=>true
				]
			])

			->add('username', [
				'notBlank'=>[
					'rule'=>'notBlank',
					'message'=>__('Please enter username'),
					'last'=>true
				],
				'mustBeValid'=>[
					'rule'=>'alphaNumericDashUnderscore',
					'provider'=>'table',
					'message'=>__('Please enter a valid username'),
					'last'=>true
				],
				'mustBeAlpha'=>[
					'rule'=>'alpha',
					'provider'=>'table',
					'message'=>__('Please enter a valid username'),
					'last'=>true
				],
				'unique'=>[
					'rule'=>'validateUnique',
					'provider'=>'table',
					'message'=>__('This username already exist'),
					'last'=>true
				],
				'mustBeLonger'=>[
					'rule'=>['minLength', 4],
					'message'=>__('Username must be greater than 3 characters'),
					'last'=>true
				],
				'mustNotBanned'=>[
					'rule'=>'checkForUsernameNotAvailable',
					'provider'=>'table',
					'message'=>__('This Username is not available'),
					'last'=>true
				]
			])

			->add('first_name', [
				'notBlank'=>[
					'rule'=>'notBlank',
					'message'=>__('Please enter first name'),
					'last'=>true
				],
				'mustBeValid'=>[
					'rule'=>'alphaNumericDashUnderscoreSpace',
					'provider'=>'table',
					'message'=>__('Please enter a valid first name'),
					'last'=>true
				],
				'mustBeAlpha'=>[
					'rule'=>'alpha',
					'provider'=>'table',
					'message'=>__('Please enter a valid first name'),
					'last'=>true
				]
			])

			->allowEmptyString('last_name')
			->add('last_name', [
				'mustBeValid'=>[
					'rule'=>'alphaNumericDashUnderscoreSpace',
					'provider'=>'table',
					'message'=>__('Please enter a valid last name'),
					'last'=>true
				],
				'mustBeAlpha'=>[
					'rule'=>'alpha',
					'provider'=>'table',
					'message'=>__('Please enter a valid last name'),
					'last'=>true
				]
			])

			->add('email', [
				'notBlank'=>[
					'rule'=>'notBlank',
					'message'=>__('Please enter email'),
					'last'=>true
				],
				'validFormat'=>[
					'rule'=>'email',
					'message'=>__('Please enter valid email'),
					'last'=>true
				],
				'unique'=>[
					'rule'=>'validateUnique',
					'provider'=>'table',
					'message'=>__('This email already exist')
				]
			])

			->add('password', [
				'notBlank'=>[
					'rule'=>'notBlank',
					'message'=>__('Please enter password'),
					'last'=>true
				],
				'mustBeLonger'=>[
					'rule'=>['minLength', 6],
					'message'=>__('Password must be greater than 5 characters'),
					'last'=>true
				]
			]);

		return $validator;
	}

	/**
	 * Used to check for password and confirm password
	 *
	 * @access public
	 * @param mixed $value validation field value
	 * @param array $context the validation context
	 * @return boolean
	 */
	public function checkForSamePassword($value, $context) {
		if(!empty($value) && $value != $context['data']['password']) {
			return false;
		}

		return true;
	}

	/**
	 * Used to match old password
	 *
	 * @access public
	 * @param mixed $value validation field value
	 * @param array $context the validation context
	 * @return boolean
	 */
	public function verifyOldPass($value, $context) {
		$userId = $this->UserAuth->getUserId();

		if(!empty($userId)) {
			$user = $this->find()->where(['Users.id'=>$userId])->first();

			if(!empty($user)) {
				if($this->UserAuth->checkPassword($value, $user->password)) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Used to check for photo file attachment
	 *
	 * @access public
	 * @param mixed $value validation field value
	 * @param array $context the validation context
	 * @return boolean
	 */
	public function checkForPhotoFile($value, $context) {
		if(is_object($value) || (isset($value['name']) && !empty($value['name']))) {
			return Validation::extension($value, ['gif', 'jpeg', 'png', 'jpg']);
		}

		return true;
	}

	/**
	 * Used to check duplicate username in list
	 *
	 * @access public
	 * @param mixed $value validation field value
	 * @param array $context the validation context
	 * @return boolean
	 */
	public function checkExistUsernameInList($value, $context) {
		$found = 0;

		foreach($this->multiUsers['Users'] as $row) {
			if(isset($row['usercheck']) && $row['usercheck']) {
				if(strtolower(trim($row['username'])) == strtolower(trim($value))) {
					$found++;
				}
			}
		}

		if($found > 1) {
			return false;
		}

		return true;
	}

	/**
	 * Used to check duplicate email in list
	 *
	 * @access public
	 * @param mixed $value validation field value
	 * @param array $context the validation context
	 * @return boolean
	 */
	public function checkExistEmailInList($value, $context) {
		$found = 0;

		foreach($this->multiUsers['Users'] as $row) {
			if(isset($row['usercheck']) && $row['usercheck']) {
				if(strtolower(trim($row['email'])) == strtolower(trim($value))) {
					$found++;
				}
			}
		}

		if($found > 1) {
			return false;
		}

		return true;
	}

	/**
	 * Used to check username is available
	 *
	 * @access public
	 * @param mixed $value validation field value
	 * @param array $context the validation context
	 * @return boolean
	 */
	public function checkForUsernameNotAvailable($value, $context=null) {
		if(strtolower($value) != 'admin') {
			return $this->isUsernameAvailable($value, $context);
		}

		return true;
	}

	/**
	 * Used tocheck username is available
	 *
	 * @access public
	 * @param mixed $value validation field value
	 * @param array $context the validation context
	 * @return boolean
	 */
	public function isUsernameAvailable($value=null, $context=null) {
		$bannedUsernames = array_map('trim', explode(',', strtolower(BANNED_USERNAMES)));

		if(!empty($bannedUsernames)) {
			$oldUsername = '';

			if(isset($context['data']['id'])) {
				$oldUsername = $this->getUsernameById($context['data']['id']);
			}

			if(empty($oldUsername) || $oldUsername != $value) {
				if(in_array(strtolower($value), $bannedUsernames)) {
					return false;
				}
			}
		}

		$controller_or_plugin = strtolower(str_replace(' ', '', ucwords(str_replace('-', ' ', str_replace('_', ' ', $value)))));

		$controllerAndPluginNames = $this->ControllerList->getAllControllerAndPluginNames();

		if(isset($controllerAndPluginNames[$controller_or_plugin])) {
			return false;
		}

		$customRoutes = Router::routes();

		$url = '/'.strtolower($value);

		$found = false;

		foreach($customRoutes as $customRoute) {
			if(strpos(strtolower($customRoute->template), $url) !== false) {
				$found = true;
				break;
			}
		}

		return !$found;
	}

	/**
	 * Used to get user by cookie token
	 *
	 * @access public
	 * @param array $credentials
	 * @return array
	 */
	public function getUserByCookieToken($credentials=array()) {
		$loginTokenTable = FactoryLocator::get('Table')->get('Usermgmt.LoginTokens');

		$userId = $loginTokenTable->getUserIdByCookieToken($credentials);

		if($userId) {
			$user = $this->getUserById($userId);

			if(!empty($user) && $user['is_active'] && $user['is_email_verified']) {
				return $user->toArray();
			}
		}

		return [];
	}

	/**
	 * Used to get name by user id
	 *
	 * @access public
	 * @param integer $userId user id
	 * @return string
	 */
	public function getNameById($userId) {
		if($userId) {
			$user = $this->find()->select(['Users.first_name', 'Users.last_name'])->where(['Users.id'=>$userId])->first();

			if(!empty($user)) {
				return $user['first_name'].' '.$user['last_name'];
			}
		}

		return '';
	}

	/**
	 * Used to get username by user id
	 *
	 * @access public
	 * @param integer $userId user id
	 * @return string
	 */
	public function getUsernameById($userId) {
		if($userId) {
			$user = $this->find()->select(['Users.username'])->where(['Users.id'=>$userId])->first();

			if(!empty($user)) {
				return $user['username'];
			}
		}

		return '';
	}

	/**
	 * Used to get email by user id
	 *
	 * @access public
	 * @param integer $userId user id
	 * @return string
	 */
	public function getEmailById($userId) {
		if($userId) {
			$user = $this->find()->select(['Users.email'])->where(['Users.id'=>$userId])->first();

			if(!empty($user)) {
				return $user['email'];
			}
		}

		return '';
	}

	/**
	 * Used to get user by user id
	 *
	 * @access public
	 * @param integer $userId user id
	 * @return array
	 */
	public function getUserById($userId) {
		if($userId) {
			$user = $this->find()->where(['Users.id'=>$userId])->contain(['UserDetails'])->first();

			if(!empty($user)) {
				return $user;
			}
		}

		return [];
	}

	/**
	 * Used to get user by user email
	 *
	 * @access public
	 * @param string $email user email
	 * @return string
	 */
	public function getUserByEmail($email) {
		if($email) {
			$user = $this->find()->where(['Users.email'=>$email])->contain(['UserDetails'])->first();

			if(!empty($user)) {
				return $user;
			}
		}

		return [];
	}

	/**
	 * Used to get gender array
	 *
	 * @access public
	 * @param bool $sel true|false
	 * @return array
	 */
	public function getGenders($sel=true) {
		$genders = [];

		if($sel) {
			$genders[''] = __('Select Gender');
		}

		$genders['male'] = __('Male');
		$genders['female'] = __('Female');

		return $genders;
	}

	/**
	 * Used to check users by user group id
	 *
	 * @access public
	 * @param integer $userGroupId user group id
	 * @return boolean
	 */
	public function isUserAssociatedWithGroup($userGroupId) {
		$conditions = ['OR'=>[['Users.user_group_id'=>$userGroupId], ['Users.user_group_id like'=>$userGroupId.',%'], ['Users.user_group_id like'=>'%,'.$userGroupId.',%'], ['Users.user_group_id like'=>'%,'.$userGroupId]]];

		if($this->exists($conditions)) {
			return true;
		}

		return false;
	}

	/**
	 * Used to get all users with user ids
	 *
	 * @access public
	 * @param array $userIds user ids
	 * @return array
	 */
	public function getAllUsersWithUserIds($userIds=array()) {
		$users = $cond = [];

		$cond['Users.is_active'] = 1;
		$cond['Users.id IN'] = $userIds;

		$result = $this->find()
					->select(['Users.id', 'Users.email', 'Users.first_name', 'Users.last_name'])
					->where($cond)
					->enableHydration(false)
					->toArray();

		foreach($result as $row) {
			$users[$row['id']] = $row['first_name'].' '.$row['last_name'].' ('.$row['email'].')';
		}

		return $users;
	}
}
