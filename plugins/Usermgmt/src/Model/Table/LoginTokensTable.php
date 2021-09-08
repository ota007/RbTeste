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

class LoginTokensTable extends UsermgmtAppTable {

	public function initialize(array $config): void {
		$this->addBehavior('Timestamp');

		$this->belongsTo('Usermgmt.Users');
	}

	/**
	 * Used to generate cookie token
	 *
	 * @access public
	 * @param integer $userId user id
	 * @param string $duration cookie persist duration
	 * @return string
	 */
	public function saveCookieToken($userId, $duration) {
		$token = md5(uniqid('', true));

		$loginTokenEntity = $this->newEmptyEntity();

		$loginTokenEntity['user_id'] = $userId;
		$loginTokenEntity['token'] = $token;
		$loginTokenEntity['duration'] = $duration;
		$loginTokenEntity['expires'] = date('Y-m-d H:i:s', strtotime($duration));

		$this->deleteAll(['user_id'=>$userId]);

		$this->save($loginTokenEntity);

		return $token.':'.$userId.':'.$duration;
	}

	/**
	 * Used to get user id by cookie token
	 *
	 * @access public
	 * @param array $credentials
	 * @return integer
	 */
	public function getUserIdByCookieToken($credentials) {
		$loginToken = $this->find()
						->where(['LoginTokens.user_id'=>$credentials['user_id'], 'LoginTokens.token'=>$credentials['token'], 'LoginTokens.duration'=>$credentials['duration'], 'LoginTokens.used'=>0, 'LoginTokens.expires >='=>date('Y-m-d H:i:s')])
						->first();

		if(!empty($loginToken)) {
			$this->updateAll(['used'=>1], ['id'=>$loginToken['id']]);

			return $loginToken['user_id'];
		}

		return 0;
	}
}
