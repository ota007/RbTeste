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

class UserSettingsTable extends UsermgmtAppTable {

	public function initialize(array $config): void {
		$this->addBehavior('Timestamp');

		$this->hasMany('Usermgmt.UserSettingOptions');

		$this->belongsTo('Usermgmt.SettingOptions', ['foreignKey'=>'setting_value']);
	}

	public function validationForAdd($validator) {
		$validator
			->add('category_type', [
				'notBlank'=>[
					'rule'=>'notBlank',
					'message'=>__('Please select category type'),
					'last'=>true
				]
			])

			->add('setting_category', [
				'notBlank'=>[
					'rule'=>'notBlank',
					'message'=>__('Please select category'),
					'last'=>true
				]
			])

			->add('new_category', [
				'notBlank'=>[
					'rule'=>'notBlank',
					'message'=>__('Please enter new category'),
					'last'=>true
				]
			])

			->add('setting_type', [
				'notBlank'=>[
					'rule'=>'notBlank',
					'message'=>__('Please select input type'),
					'last'=>true
				]
			])

			->add('setting_key', [
				'notBlank'=>[
					'rule'=>'notBlank',
					'message'=>__('Please enter setting key'),
					'last'=>true
				],
				'mustBeValid'=>[
					'rule'=>'alphaNumericDashUnderscore',
					'provider'=>'table',
					'message'=>__('Please enter a valid key'),
					'last'=>true
				],
				'unique'=>[
					'rule'=>'validateUnique',
					'provider'=>'table',
					'message'=>__('This key already exist')
				]
			])

			->add('setting_description', [
				'notBlank'=>[
					'rule'=>'notBlank',
					'message'=>__('Please enter setting description'),
					'last'=>true
				]
			]);

		return $validator;
	}

	/**
	 * Used to get all settings
	 *
	 * @access public
	 * @return array
	 */
	public function getAllUserSettings() {
		$settings = [];

		$result = $this->find()
					->select(['UserSettings.setting_key', 'UserSettings.setting_value', 'UserSettings.setting_type'])
					->enableHydration(false)
					->toArray();

		foreach($result as $row) {
			if($row['setting_type'] == 'dropdown' || $row['setting_type'] == 'radio') {
				$row['setting_value'] = $this->SettingOptions->getTitleById($row['setting_value']);
			}

			$settings[$row['setting_key']]['setting_value'] = trim(strval($row['setting_value']));
		}

		return $settings;
	}
	/**
	 * Used to get settings categories
	 *
	 * @access public
	 * @param bool $sel true/false
	 * @return array
	 */
	public function getSettingCategories($sel=true) {
		$settingCategories = [];

		$result = $this->find()
					->select(['UserSettings.setting_category'])
					->where(['UserSettings.setting_category IS NOT NULL', 'UserSettings.setting_category !='=>''])
					->group(['UserSettings.setting_category'])
					->order(['UserSettings.setting_category'=>'ASC'])
					->enableHydration(false)
					->toArray();

		if($sel) {
			$settingCategories[''] = __('Select Category');
		}

		foreach($result as $row) {
			$settingCategories[$row['setting_category']] = ucwords(strtolower($row['setting_category']));
		}

		return $settingCategories;
	}
	/**
	 * Used to get settings input types
	 *
	 * @access public
	 * @param bool $sel true/false
	 * @return array
	 */
	public function getSettingInputTypes($sel=true) {
		$inputTypes = [''=>__('Select Input Type'), 'input'=>__('Text Input'), 'checkbox'=>__('Checkbox Input'), 'dropdown'=>__('Dropdown Input'), 'radio'=>__('Radio Input'), 'textarea'=>__('Textarea Input'), 'tinymce'=>__('Tinymce Editor'), 'ckeditor'=>__('CK Editor')];

		if(!$sel) {
			unset($inputTypes['']);
		}

		return $inputTypes;
	}
}
