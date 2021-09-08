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

class SettingOptionsTable extends UsermgmtAppTable {

	public function initialize(array $config): void {
		$this->addBehavior('Timestamp');
	}

	public function validationForAdd($validator) {
		$validator
			->add('title', [
				'notBlank'=>[
					'rule'=>'notBlank',
					'message'=>__('Please enter title'),
					'last'=>true
				],
				'unique'=>[
					'rule'=>'validateUnique',
					'provider'=>'table',
					'message'=>__('This title already exist')
				]
			]);

		return $validator;
	}

	/**
	 * Used to get all setting options
	 *
	 * @access public
	 * @param bool $sel true/false
	 * @return array
	 */
	public function getSettingOptions($sel=true) {
		$settingOptions = [];

		if($sel) {
			$settingOptions[''] = __('Select');
		}

		$result = $this->find()
					->select(['SettingOptions.id', 'SettingOptions.title'])
					->order(['SettingOptions.title'=>'ASC'])
					->enableHydration(false)
					->toArray();

		foreach($result as $row) {
			$settingOptions[$row['id']] = $row['title'];
		}

		return $settingOptions;
	}

	/**
	 * Used to get title by id
	 *
	 * @access public
	 * @param integer $settingOptionId setting option id
	 * @return string
	 */
	public function getTitleById($settingOptionId) {
		if(!empty($settingOptionId)) {
			$result = $this->find()->where(['SettingOptions.id'=>$settingOptionId])->first();

			if(!empty($result)) {
				return $result['title'];
			}
		}

		return '';
	}
}
