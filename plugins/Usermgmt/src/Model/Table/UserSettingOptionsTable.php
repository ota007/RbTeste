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

class UserSettingOptionsTable extends UsermgmtAppTable {

	public function initialize(array $config): void {
		$this->addBehavior('Timestamp');

		$this->belongsTo('Usermgmt.SettingOptions');

		$this->belongsTo('Usermgmt.UserSettings');
	}

	/**
	 * Used to get all setting options
	 *
	 * @access public
	 * @param integer $userSettingId user setting id
	 * @param bool $sel true/false
	 * @return array
	 */
	public function getUserSettingOptions($userSettingId, $sel=true) {
		$userSettingOptions = [];

		if($sel) {
			$userSettingOptions[''] = __('Select');
		}

		$result = $this->find()
					->select(['UserSettingOptions.id', 'SettingOptions.id', 'SettingOptions.title'])
					->where(['UserSettingOptions.user_setting_id'=>$userSettingId])
					->order(['SettingOptions.title'=>'ASC'])
					->contain(['SettingOptions'])
					->enableHydration(false)
					->toArray();

		foreach($result as $row) {
			$userSettingOptions[$row['setting_option']['id']] = $row['setting_option']['title'];
		}

		return $userSettingOptions;
	}
}
