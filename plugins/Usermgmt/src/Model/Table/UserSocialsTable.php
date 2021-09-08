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

class UserSocialsTable extends UsermgmtAppTable {

	public function initialize(array $config): void {
		$this->addBehavior('Timestamp');

		$this->belongsTo('Usermgmt.Users');
	}
	public function add_social_account($socialData, $userId) {
		$userSocialEntity = $this->newEmptyEntity();

		$userSocialEntity['user_id'] = $userId;
		$userSocialEntity['type'] = $socialData['type'];
		$userSocialEntity['socialid'] = $socialData['id'];

		if(!empty($socialData['access_token'])) {
			$userSocialEntity['access_token'] = $socialData['access_token'];
		}

		if(!empty($socialData['access_secret'])) {
			$userSocialEntity['access_secret'] = $socialData['access_secret'];
		}

		$this->save($userSocialEntity, ['validate'=>false]);
	}
}
