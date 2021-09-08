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

class UserContactsTable extends UsermgmtAppTable {

	public function initialize(array $config): void {
		$this->addBehavior('Timestamp');
	}

	public function validationForContact($validator) {
		$validator
			->add('name', [
				'notBlank'=>[
					'rule'=>'notBlank',
					'message'=>__('Please enter your name'),
					'last'=>true
				]
			])

			->add('email', [
				'notBlank'=>[
					'rule'=>'notBlank',
					'message'=>__('Please enter your email'),
					'last'=>true
				],
				'validFormat'=>[
					'rule'=>'email',
					'message'=>__('Please enter valid email'),
					'last'=>true
				]
			])

			->add('requirement', [
				'notBlank'=>[
					'rule'=>'notBlank',
					'message'=>__('Please enter requirement'),
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
}
