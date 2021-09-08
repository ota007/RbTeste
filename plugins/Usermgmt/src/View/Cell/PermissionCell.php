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

namespace Usermgmt\View\Cell;

use Cake\View\Cell;

class PermissionCell extends Cell {
	public $helpers = ['Usermgmt.UserAuth'];
	public $viewobj = null;

	public function initialize(): void {
		$this->viewobj = $this->createView();
    }

	public function getPermissions() {
		$this->loadModel('Usermgmt.UserGroups');

		$permissions = [];

		if($this->viewobj->UserAuth->isLogged()) {
			$permissions = $this->UserGroups->getPermissions($this->viewobj->UserAuth->getGroupId());
		} else {
			$permissions = $this->UserGroups->getPermissions(GUEST_GROUP_ID);
		}

		return $permissions;
	}
}
