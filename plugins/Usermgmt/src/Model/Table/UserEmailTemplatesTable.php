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

class UserEmailTemplatesTable extends UsermgmtAppTable {

	public function initialize(array $config): void {
		$this->addBehavior('Timestamp');
	}

	public function validationForAdd($validator) {
		$validator
			->add('template_name', [
				'notBlank'=>[
					'rule'=>'notBlank',
					'message'=>__('Please enter template name'),
					'last'=>true
				],
				'unique'=>[
					'rule'=>'validateUniqueTemplate',
					'provider'=>'table',
					'message'=>__('This template name already exist')
				]
			]);

		return $validator;
	}

	/**
	 * Used to check for unique email template user wise
	 *
	 * @access public
	 * @param mixed $value validation field value
	 * @param array $context the validation context
	 * @return boolean
	 */
	public function validateUniqueTemplate($value, $context) {
		if(!empty($value)) {
			if(empty($context['data']['id'])) {
				$result = $this->find()
							->where(['UserEmailTemplates.user_id'=>$context['data']['user_id'], 'UserEmailTemplates.template_name'=>$context['data']['template_name']])
							->first();

				if(!empty($result)) {
					return false;
				}
			}
			else {
				$result = $this->find()
							->where(['UserEmailTemplates.id !='=>$context['data']['id'], 'UserEmailTemplates.user_id'=>$context['data']['user_id'], 'UserEmailTemplates.template_name'=>$context['data']['template_name']])
							->first();

				if(!empty($result)) {
					return false;
				}
			}
		}

		return true;
	}
	/**
	 * Used to get all email templates
	 *
	 * @access public
	 * @param integer $userId user id
	 * @param bool $sel true/false
	 * @return array
	 */
	public function getEmailTemplates($userId, $sel=true) {
		$templates = [];

		if($sel) {
			$templates[''] = __('No Template');
		}

		$result = $this->find()
					->select(['UserEmailTemplates.id', 'UserEmailTemplates.template_name'])
					->where(['UserEmailTemplates.user_id'=>$userId])
					->order(['UserEmailTemplates.template_name'=>'ASC'])
					->enableHydration(false)
					->toArray();

		foreach($result as $row) {
			$templates[$row['id']] = $row['template_name'];
		}

		return $templates;
	}

	/**
	 * Used to get email template by id
	 *
	 * @access public
	 * @param integer $emailTemplateId email template id
	 * @return array
	 */
	public function getEmailTemplateById($emailTemplateId) {
		if(!empty($emailTemplateId)) {
			$result = $this->find()->where(['UserEmailTemplates.id'=>$emailTemplateId])->first();

			return $result;
		}

		return [];
	}
}
