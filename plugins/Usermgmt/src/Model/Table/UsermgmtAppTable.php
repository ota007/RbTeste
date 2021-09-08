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

use Cake\ORM\Table;
use Cake\Validation\Validation;

class UsermgmtAppTable extends Table {

	/**
	 * Used to validate string with letter, integer, dash, underscore
	 *
	 * @access public
	 * @param mixed $value validation field value
	 * @param array $context the validation context
	 * @return boolean
	 * more info https://www.php.net/manual/en/regexp.reference.unicode.php
	 */
	public function alphaNumericDashUnderscore($value, $context) {
		if(!preg_match('/^[\p{Ll}\p{Lm}\p{Lo}\p{Lt}\p{Lu}\p{Nd}\p{Pd}\p{Pc}]+$/Du', $value)) {
			return false;
		}

		return true;
	}

	/**
	 * Used to validate string with letter, integer, dash, underscore, space
	 *
	 * @access public
	 * @param mixed $value validation field value
	 * @param array $context the validation context
	 * @return boolean
	 * more info https://www.php.net/manual/en/regexp.reference.unicode.php
	 */
	public function alphaNumericDashUnderscoreSpace($value, $context) {
		if(!preg_match('/^[\p{Ll}\p{Lm}\p{Lo}\p{Lt}\p{Lu}\p{Nd}\p{Zs}\p{Pd}\p{Pc}]+$/Du', $value)) {
			return false;
		}

		return true;
	}

	/**
	 * Used to validate string with letter
	 *
	 * @access public
	 * @param mixed $value validation field value
	 * @param array $context the validation context
	 * @return boolean
	 */
	public function alpha($value, $context) {
		if(!preg_match('/[\p{L}]/u', $value)) {
			return false;
		}

		return true;
	}

	/**
	 * Used to validate captcha
	 *
	 * @access public
	 * @param mixed $value validation field value
	 * @param array $context the validation context
	 * @return boolean
	 */
	public function recaptchaValidate($value, $context) {
		require_once(USERMGMT_PATH.DS.'vendor'.DS.'recaptcha'.DS.'src'.DS.'autoload.php');

		$recaptcha = new \ReCaptcha\ReCaptcha(PRIVATE_KEY_FROM_RECAPTCHA);

		$resp = $recaptcha->verify($value);

		if($resp->isSuccess()) {
			return true;
		} else {
			$errors = $resp->getErrorCodes();
			return false;
		}
	}

	/**
	 * Used to check valid emails
	 *
	 * @access public
	 * @param mixed $value validation field value
	 * @param array $context the validation context
	 * @return boolean
	 */
	public function checkValidEmails($value, $context) {
		$emails = explode(',', $value);

		$isValid = true;

		foreach($emails as $email) {
			$email = trim($email);

			if(!empty($email)) {
				if(!Validation::email($email)) {
					$isValid = false;
					break;
				}
			}
		}

		return $isValid;
	}
}
