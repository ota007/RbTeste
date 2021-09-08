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

class UserEmailSignaturesTable extends UsermgmtAppTable {

	public function initialize(array $config): void {
		$this->addBehavior('Timestamp');
	}

	public function validationForAdd($validator) {
		$validator
			->add('signature_name', [
				'notBlank'=>[
					'rule'=>'notBlank',
					'message'=>__('Please enter signature name'),
					'last'=>true
				],
				'unique'=>[
					'rule'=>'validateUniqueSignature',
					'provider'=>'table',
					'message'=>__('This signature name already exist')
				]
			]);

		return $validator;
	}

	/**
	 * Used to check for unique email signature user wise
	 *
	 * @access public
	 * @param mixed $value validation field value
	 * @param array $context the validation context
	 * @return boolean
	 */
	public function validateUniqueSignature($value, $context) {
		if(!empty($value)) {
			if(empty($context['data']['id'])) {
				$result = $this->find()
							->where(['UserEmailSignatures.user_id'=>$context['data']['user_id'], 'UserEmailSignatures.signature_name'=>$context['data']['signature_name']])
							->first();

				if(!empty($result)) {
					return false;
				}
			}
			else {
				$result = $this->find()
							->where(['UserEmailSignatures.id !='=>$context['data']['id'], 'UserEmailSignatures.user_id'=>$context['data']['user_id'], 'UserEmailSignatures.signature_name'=>$context['data']['signature_name']])
							->first();

				if(!empty($result)) {
					return false;
				}
			}
		}

		return true;
	}

	/**
	 * Used to get all email signatures
	 *
	 * @access public
	 * @param integer $userId user id
	 * @param bool $sel true/false
	 * @return array
	 */
	public function getEmailSignatures($userId, $sel=true) {
		$signatures = [];

		if($sel) {
			$signatures[''] = __('No Signature');
		}

		$result = $this->find()
					->select(['UserEmailSignatures.id', 'UserEmailSignatures.signature_name'])
					->where(['UserEmailSignatures.user_id'=>$userId])
					->order(['UserEmailSignatures.signature_name'=>'ASC'])
					->enableHydration(false)
					->toArray();

		foreach($result as $row) {
			$signatures[$row['id']] = $row['signature_name'];
		}

		return $signatures;
	}

	/**
	 * Used to get signature by id
	 *
	 * @access public
	 * @param integer $emailSignatureId email signature id
	 * @return array
	 */
	public function getEmailSignatureById($emailSignatureId) {
		if(!empty($emailSignatureId)) {
			$result = $this->find()->where(['UserEmailSignatures.id'=>$emailSignatureId])->first();

			return $result;
		}

		return [];
	}
}
