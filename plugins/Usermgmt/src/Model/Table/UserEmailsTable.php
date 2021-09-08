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
use Cake\Validation\Validation;

class UserEmailsTable extends UsermgmtAppTable {

	public function initialize(array $config): void {
		$this->addBehavior('Timestamp');

		$this->belongsTo('Usermgmt.Users', ['foreignKey'=>'sent_by']);
	}

	public function validationForSend($validator) {
		$validator
			->add('to', [
				'notBlank'=>[
					'rule'=>'notBlank',
					'message'=>__('Please enter to email'),
					'last'=>true
				],
				'validFormat'=>[
					'rule'=>'email',
					'message'=>__('Please enter valid email'),
					'last'=>true
				]
			])

			->add('from_name', [
				'notBlank'=>[
					'rule'=>'notBlank',
					'message'=>__('Please enter from name'),
					'last'=>true
				]
			])

			->add('from_email', [
				'notBlank'=>[
					'rule'=>'notBlank',
					'message'=>__('Please enter from email'),
					'last'=>true
				],
				'validFormat'=>[
					'rule'=>'email',
					'message'=>__('Please enter valid email'),
					'last'=>true
				]
			])

			->add('subject', [
				'notBlank'=>[
					'rule'=>'notBlank',
					'message'=>__('Please enter email subject'),
					'last'=>true
				]
			])

			->add('message', [
				'notBlank'=>[
					'rule'=>'notBlank',
					'message'=>__('Please enter email message'),
					'last'=>true
				]
			])

			->add('user_id', [
				'multiple'=>[
					'rule'=>'multiple',
					'message'=>__('Please select user(s)'),
					'last'=>true
				]
			])

			->add('user_group_id', [
				'notBlank'=>[
					'rule'=>'notBlank',
					'message'=>__('Please select group(s)'),
					'last'=>true
				]
			])

			->add('to_email', [
				'notBlank'=>[
					'rule'=>'notBlank',
					'message'=>__('Please enter email(s)'),
					'last'=>true
				],
				'validFormat'=>[
					'rule'=>'checkValidEmails',
					'provider'=>'table',
					'message'=>__('Please enter valid email(s)'),
					'last'=>true
				]
			])

			->allowEmptyString('schedule_date')
			->add('schedule_date', [
				'mustBeFutureDate'=>[
					'rule'=>'checkForScheduledDate',
					'provider'=>'table',
					'message'=>__('Please enter future date'),
					'last'=>true
				]
			])

			->allowEmptyString('cc_to')
			->add('cc_to', [
				'validFormat'=>[
					'rule'=>'checkValidEmails',
					'provider'=>'table',
					'message'=>__('Please enter valid email(s)'),
					'last'=>true
				]
			]);

		return $validator;
	}

	/**
	 * Used to check scheduled date
	 *
	 * @access public
	 * @param mixed $value validation field value
	 * @param array $context the validation context
	 * @return boolean
	 */
	public function checkForScheduledDate($value, $context) {
		if(is_object($value)) {
			$value = $value->format('Y-m-d H:i:s');
		}

		if(!empty($value) && strtotime($value) < time()) {
			return false;
		}

		return true;
	}
}
