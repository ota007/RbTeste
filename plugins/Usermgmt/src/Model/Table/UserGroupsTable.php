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
use Cake\Cache\Cache;
use Cake\Utility\Inflector;

class UserGroupsTable extends UsermgmtAppTable {

	public function initialize(array $config): void {
		$this->addBehavior('Timestamp');

		$this->hasMany('Usermgmt.UserGroupPermissions');
	}

	public function validationForAdd($validator) {
		$validator
			->add('name', [
				'notBlank'=>[
					'rule'=>'notBlank',
					'message'=>__('Please enter user group name'),
					'last'=>true
				],
				'mustBeValid'=>[
					'rule'=>'alphaNumericDashUnderscoreSpace',
					'provider'=>'table',
					'message'=>__('Please enter a valid name'),
					'last'=>true
				],
				'mustBeAlpha'=>[
					'rule'=>'alpha',
					'provider'=>'table',
					'message'=>__('Please enter a valid name'),
					'last'=>true
				],
				'unique'=>[
					'rule'=>'validateUnique',
					'provider'=>'table',
					'message'=>__('This name already exist'),
					'last'=>true
				]
			]);

		return $validator;
	}

	/**
	 * Used to check permissions of group
	 *
	 * @access public
	 * @param string $controller controller name
	 * @param string $action action name
	 * @param string $plugin plugin name
	 * @param integer $userGroupId user group id
	 * @return boolean
	 */
	public function isUserGroupAccess($controller, $action, $plugin, $prefix, $userGroupId) {
		if(!CHECK_USER_PERMISSIONS) {
			return true;
		}

		$userGroupIds = array_map('trim', explode(',',  strval($userGroupId)));

		if(in_array(ADMIN_GROUP_ID, $userGroupIds) && !CHECK_ADMIN_PERMISSIONS) {
			return true;
		}

		$access = Inflector::camelize($controller).'/'.$action;

		if(!empty($plugin)) {
			$access = $plugin.'/'.$access;
		}
		if(!empty($prefix)) {
			$access = $prefix.'/'.$access;
		}

		$permissions = $this->getPermissions($userGroupId);

		if(!empty($permissions) && in_array(strtolower($access), $permissions)) {
			return true;
		}

		return false;
	}

	/**
	 * Used to check permissions of guest group
	 *
	 * @access public
	 * @param string $controller controller name
	 * @param string $action action name
	 * @param string $plugin plugin name
	 * @param string $prefix prefix name
	 * @return boolean
	 */
	public function isGuestAccess($controller, $action, $plugin=null, $prefix=null) {
		if(CHECK_USER_PERMISSIONS) {
			return $this->isUserGroupAccess($controller, $action, $plugin, $prefix, GUEST_GROUP_ID);
		} else {
			return true;
		}
	}

	/**
	 * Used to get permissions from cache or database of a group
	 *
	 * @access public
	 * @param integer $userGroupId group id
	 * @return array
	 */
	public function getPermissions($userGroupId) {
		// using the cake cache to store rules
		$cacheKey = 'rules_for_group_'.str_replace(',', '-', $userGroupId);

		$permissions = Cache::read($cacheKey, 'UserMgmtPermissions');

		if(empty($permissions)) {
			$permissions = $this->generatePermissions($userGroupId);

			Cache::write($cacheKey, $permissions, 'UserMgmtPermissions');
		}

		return $permissions;
	}

	/**
	 * Used to generate permissions from database
	 *
	 * @access public
	 * @param string $userGroupId group ids comma separated
	 * @return array
	 */
	public function generatePermissions($userGroupId) {
		$userGroupIds = array_map('trim', explode(',',  strval($userGroupId)));

		$permissions = [];

		$result = $this->UserGroupPermissions->find()
					->select(['UserGroupPermissions.controller', 'UserGroupPermissions.action', 'UserGroupPermissions.plugin', 'UserGroupPermissions.prefix'])
					->where(['UserGroupPermissions.user_group_id IN'=>$userGroupIds, 'UserGroupPermissions.is_allowed'=>1])
					->enableHydration(false)
					->toArray();

		foreach($result as $row) {
			$permissions[] = $this->generateActionUrl($row);
		}

		$result = $this->find()
					->select(['UserGroups.id', 'UserGroups.parent_id'])
					->where(['UserGroups.id IN'=>$userGroupIds, 'UserGroups.parent_id >'=>0])
					->enableHydration(false)
					->toArray();

		foreach($result as $row) {
			if(!in_array($row['parent_id'], $userGroupIds)) {
				$permissionsTmp1 = $permissionsTmp2 = [];

				$parentPermissions = $this->UserGroupPermissions->find()
										->select(['UserGroupPermissions.controller', 'UserGroupPermissions.action', 'UserGroupPermissions.plugin', 'UserGroupPermissions.prefix'])
										->where(['UserGroupPermissions.user_group_id'=>$row['parent_id'], 'UserGroupPermissions.is_allowed'=>1])
										->enableHydration(false)
										->toArray();

				foreach($parentPermissions as $parentPermission) {
					$permissionsTmp1[] = $this->generateActionUrl($parentPermission);
				}

				$childNotPermissions = $this->UserGroupPermissions->find()
										->select(['UserGroupPermissions.controller', 'UserGroupPermissions.action', 'UserGroupPermissions.plugin', 'UserGroupPermissions.prefix'])
										->where(['UserGroupPermissions.user_group_id'=>$row['id'], 'UserGroupPermissions.is_allowed'=>0])
										->enableHydration(false)
										->toArray();

				foreach($childNotPermissions as $childNotPermission) {
					$permissionsTmp2[] = $this->generateActionUrl($childNotPermission);
				}

				$permissionsTmp1 = array_unique($permissionsTmp1);
				$permissionsTmp2 = array_unique($permissionsTmp2);

				$permissions = array_merge($permissions, array_diff($permissionsTmp1, $permissionsTmp2));
			}
		}

		if(!empty($permissions)) {
			$permissions = array_unique($permissions);
		}

		return $permissions;
	}

	private function generateActionUrl($data) {
		$actionUrl = $data['controller'].'/'.$data['action'];

		if(!empty($data['plugin'])) {
			$actionUrl = $data['plugin'].'/'.$actionUrl;
		}
		if(!empty($data['prefix'])) {
			$actionUrl = str_replace('.', '/', $data['prefix']).'/'.$actionUrl;
		}

		return strtolower($actionUrl);
	}

	/**
	 * Used to get group names with ids without guest group
	 *
	 * @access public
	 * @param bool $sel true/false
	 * @return array
	 */
	public function getUserGroups($sel=true) {
		$result = $this->find()
					->select(['UserGroups.id', 'UserGroups.name', 'UserGroups.parent_id'])
					->where(['UserGroups.name !='=>'Guest'])
					->order(['UserGroups.parent_id', 'UserGroups.name'])
					->enableHydration(false)
					->toArray();

		$userGroups = [];

		if($sel) {
			$userGroups[''] = __('Select');
		}

		foreach($result as $row) {
			if($row['parent_id'] == 0) {
				$userGroups[$row['id']] = $row['name'];

				foreach($result as $row1) {
					if($row1['parent_id'] == $row['id']) {
						$userGroups[$row1['id']] = '.....'.$row1['name'];
					}
				}
			}
		}
		return $userGroups;
	}

	/**
	 * Used to get group names with ids for registration
	 *
	 * @access public
	 * @param bool $sel true/false
	 * @return array
	 */
	public function getGroupsForRegistration($sel=true) {
		$userGroups = [];

		$result = $this->find()
					->select(['UserGroups.id', 'UserGroups.name'])
					->where(['UserGroups.is_registration_allowed'=>1])
					->order(['UserGroups.name'])
					->enableHydration(false)
					->toArray();

		if($sel) {
			$userGroups[''] = __('Select');
		}

		foreach($result as $row) {
			$userGroups[$row['id']] = $row['name'];
		}

		return $userGroups;
	}

	/**
	 * Used to get group names by groupd ids
	 *
	 * @access public
	 * @param mixed $userGroupIds user group ids
	 * @param bool $returnArray true/false
	 * @return array
	 */
	public function getGroupsByIds($userGroupIds, $returnArray=false) {
		$userGroups = [];

		if(!is_array($userGroupIds)) {
			$userGroupIds = explode(',',  strval($userGroupIds));
		}

		$result = $this->find()
					->select(['UserGroups.id', 'UserGroups.name'])
					->where(['UserGroups.id IN'=>$userGroupIds])
					->order(['UserGroups.name'])
					->enableHydration(false)
					->toArray();

		foreach($result as $row) {
			$userGroups[$row['id']] = $row['name'];
		}

		if(!$returnArray) {
			$userGroups = implode(', ', $userGroups);
		}

		return $userGroups;
	}

	/**
	 * Used to get all groups
	 *
	 * @access public
	 * @param bool $sel true/false
	 * @return array
	 */
	public function getAllGroups($sel=true) {
		$userGroups = [];

		if($sel) {
			$userGroups[''] = __('Select');
		}

		$result = $this->find()
					->select(['UserGroups.id', 'UserGroups.name'])
					->order(['UserGroups.name'=>'ASC'])
					->enableHydration(false)
					->toArray();

		foreach($result as $row) {
			$userGroups[$row['id']] = $row['name'];
		}

		return $userGroups;
	}

	/**
	 * Used to get parent group names with ids without guest group
	 *
	 * @access public
	 * @param integer $skipUserGroupId user group id which need to skip
	 * @return array
	 */
	public function getParentGroups($skipUserGroupId=0) {
		$userGroups = [];

		$result = $this->find()
					->select(['UserGroups.id', 'UserGroups.name'])
					->where(['UserGroups.name !='=>'Guest', 'UserGroups.parent_id'=>0])
					->order(['UserGroups.id'=>'ASC'])
					->enableHydration(false)
					->toArray();

		$userGroups[0] = __('No Group');

		foreach($result as $row) {
			if(!($skipUserGroupId && $row['id'] == $skipUserGroupId)) {
				$userGroups[$row['id']] = $row['name'];
			}
		}

		return $userGroups;
	}

	/**
	 * Used to get parent group details with ids
	 *
	 * @access public
	 * @return array
	 */
	public function getParentGroupDetails() {
		$userGroups = $this->find()
						->select(['UserGroups.id', 'UserGroups.name', 'UserGroups.parent_id'])
						->where(['UserGroups.parent_id'=>0])
						->order(['UserGroups.id'])
						->enableHydration(false)
						->toArray();

		return $userGroups;
	}

	/**
	 * Used to get sub group details with ids
	 *
	 * @access public
	 * @return array
	 */
	public function getSubGroupDetails() {
		$userGroups = $this->find()
						->select(['UserGroups.id', 'UserGroups.name', 'UserGroups.parent_id'])
						->where(['UserGroups.parent_id >'=>0])
						->order(['UserGroups.id'])
						->enableHydration(false)
						->toArray();

		return $userGroups;
	}

	/**
	 * Used to get parent group ids
	 *
	 * @access public
	 * @param array $subGroupIds user sub group id
	 * @return array
	 */
	public function getParentGroupIds($subGroupIds) {
		$userGroups = [];

		$result = $this->find()
					->select(['UserGroups.id', 'UserGroups.parent_id'])
					->where(['UserGroups.id IN'=>$subGroupIds])
					->order(['UserGroups.id'])
					->enableHydration(false)
					->toArray();

		foreach($result as $row) {
			$userGroups[$row['id']] = $row['parent_id'];
		}

		return $userGroups;
	}
}
