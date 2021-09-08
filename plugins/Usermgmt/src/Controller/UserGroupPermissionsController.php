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

namespace Usermgmt\Controller;

use Usermgmt\Controller\UsermgmtAppController;
use Cake\Event\EventInterface;
use Cake\Core\Configure;
use Cake\Core\Plugin;

class UserGroupPermissionsController extends UsermgmtAppController {

	/**
	 * Initialization hook method.
	 *
	 * Use this method to add common initialization code like loading components.
	 *
	 * @return void
	 */
	public function initialize(): void {
		parent::initialize();

		$this->loadComponent('Usermgmt.ControllerList');
		$this->loadComponent('Usermgmt.FunctionBlock');
	}

	/**
	 * Called before the controller action. You can use this method to configure and customize components
	 * or perform logic that needs to happen before each controller action.
	 *
	 * @return void
	 */
	public function beforeFilter(EventInterface $event) {
		parent::beforeFilter($event);

		$this->loadModel('Usermgmt.UserGroupPermissions');

		$action = $this->getRequest()->getParam('action');

		if(isset($this->FormProtection)) {
			$this->FormProtection->setConfig('unlockedActions', [$action]);
		}
	}

	/**
	 * It displays all parent group permissions with controller and action names in matrix view
	 *
	 * @access public
	 * @return void
	 */
	public function groups() {
		$this->loadModel('Usermgmt.UserGroups');

		$userGroupPermissionEntity = $this->UserGroupPermissions->newEmptyEntity();

		$allControllerClasses = $this->ControllerList->getControllerClasses();
		$userGroups = $this->UserGroups->getParentGroupDetails();

		$selectedControllers = $selectedUserGroups = $dbPermissions = $selectedUserGroupIds = $newControllerList = [];

		if($this->getRequest()->is('post')) {
			$userGroupPermissionEntity = $this->UserGroupPermissions->patchEntity($userGroupPermissionEntity, $this->getRequest()->getData());

			if(!empty($userGroupPermissionEntity['ControllerList'])) {
				$controllerList = $this->ControllerList->getControllerAndActions();

				foreach($controllerList as $data) {
					$newControllerList[$data['prefix'].':'.$data['plugin'].':'.$data['controller']] = $data;
				}

				if(!empty($userGroupPermissionEntity['GroupList'])) {
					foreach($userGroupPermissionEntity['GroupList'] as $key=>$val) {
						if($val['grpcheck']) {
							$selectedUserGroupIds[$key] = $key;
						}
					}

					foreach($userGroups as $userGroup) {
						if(in_array($userGroup['id'], $selectedUserGroupIds)) {
							$selectedUserGroups[] = $userGroup;
						}
					}
				} else {
					foreach($userGroups as $userGroup) {
						$selectedUserGroupIds[$userGroup['id']] = $userGroup['id'];
						$selectedUserGroups[] = $userGroup;
					}
				}

				foreach($userGroupPermissionEntity['ControllerList'] as $row) {
					$selectedControllers[$row['name']] = (isset($newControllerList[$row['name']])) ? $newControllerList[$row['name']] : [];
					$val = explode(':', $row['name']);
					$cond = [];

					if(empty($val[0])) {
						$cond[] = 'UserGroupPermissions.prefix IS NULL';
					} else {
						$cond['UserGroupPermissions.prefix'] = $val[0];
					}

					if(empty($val[1])) {
						$cond[] = 'UserGroupPermissions.plugin IS NULL';
					} else {
						$cond['UserGroupPermissions.plugin'] = $val[1];
					}

					$cond['UserGroupPermissions.controller'] = $val[2];
					$cond['UserGroupPermissions.user_group_id IN'] = $selectedUserGroupIds;

					$userGroupPermissions = $this->UserGroupPermissions->find()->where($cond)->enableHydration(false)->toArray();

					foreach($userGroupPermissions as $ugp) {
						$dbPermissions[$ugp['prefix'].':'.$ugp['plugin'].':'.$ugp['controller']][$ugp['action']][$ugp['user_group_id']] = $ugp['is_allowed'];
					}
				}
			}
		}

		$funcDesc = $this->getFunctionDesc($selectedControllers);

		$this->set(compact('userGroupPermissionEntity', 'allControllerClasses', 'userGroups', 'selectedControllers', 'selectedUserGroupIds', 'selectedUserGroups', 'funcDesc', 'dbPermissions'));
	}

	/**
	 * It displays all sub group permissions with controller and action names in matrix view
	 *
	 * @access public
	 * @return void
	 */
	public function subgroups() {
		$this->loadModel('Usermgmt.UserGroups');

		$userGroupPermissionEntity = $this->UserGroupPermissions->newEmptyEntity();

		$allControllerClasses = $this->ControllerList->getControllerClasses();
		$userGroups = $this->UserGroups->getSubGroupDetails();

		$selectedControllers = $selectedUserGroups = $dbPermissions = $selectedUserGroupIds = $parentUserGroupIds = $parentPermissions = $newControllerList = [];

		if($this->getRequest()->is('post')) {
			$userGroupPermissionEntity = $this->UserGroupPermissions->patchEntity($userGroupPermissionEntity, $this->getRequest()->getData());

			if(!empty($userGroupPermissionEntity['ControllerList'])) {
				$controllerList = $this->ControllerList->getControllerAndActions();

				foreach($controllerList as $data) {
					$newControllerList[$data['prefix'].':'.$data['plugin'].':'.$data['controller']] = $data;
				}

				if(!empty($userGroupPermissionEntity['GroupList'])) {
					foreach($userGroupPermissionEntity['GroupList'] as $key=>$val) {
						if($val['grpcheck']) {
							$selectedUserGroupIds[$key] = $key;
						}
					}

					foreach($userGroups as $userGroup) {
						if(in_array($userGroup['id'], $selectedUserGroupIds)) {
							$selectedUserGroups[] = $userGroup;
						}
					}
				} else {
					foreach($userGroups as $userGroup) {
						$selectedUserGroupIds[$userGroup['id']] = $userGroup['id'];
						$selectedUserGroups[] = $userGroup;
					}
				}

				foreach($userGroupPermissionEntity['ControllerList'] as $row) {
					$selectedControllers[$row['name']] = (isset($newControllerList[$row['name']])) ? $newControllerList[$row['name']] : [];
					$val = explode(':', $row['name']);
					$cond = $parentCond = [];

					if(empty($val[0])) {
						$cond[] = 'UserGroupPermissions.prefix IS NULL';
					} else {
						$cond['UserGroupPermissions.prefix'] = $val[0];
					}

					if(empty($val[1])) {
						$cond[] = 'UserGroupPermissions.plugin IS NULL';
					} else {
						$cond['UserGroupPermissions.plugin'] = $val[1];
					}
					$cond['UserGroupPermissions.controller'] = $val[2];

					$parentCond = $cond;

					$cond['UserGroupPermissions.user_group_id IN'] = $selectedUserGroupIds;

					$userGroupPermissions = $this->UserGroupPermissions->find()->where($cond)->enableHydration(false)->toArray();

					foreach($userGroupPermissions as $ugp) {
						$dbPermissions[$ugp['prefix'].':'.$ugp['plugin'].':'.$ugp['controller']][$ugp['action']][$ugp['user_group_id']] = $ugp['is_allowed'];
					}

					$parentUserGroupIds = $this->UserGroups->getParentGroupIds($selectedUserGroupIds);

					$parentCond['UserGroupPermissions.user_group_id IN'] = $parentUserGroupIds;
					$parentCond['UserGroupPermissions.is_allowed'] = 1;

					$parentResult = $this->UserGroupPermissions->find()->where($parentCond)->enableHydration(false)->toArray();

					foreach($parentResult as $pr) {
						$parentPermissions[$pr['prefix'].':'.$pr['plugin'].':'.$pr['controller']][$pr['action']][$pr['user_group_id']] = $pr['is_allowed'];
					}
				}
			}
		}

		$funcDesc = $this->getFunctionDesc($selectedControllers);

		$this->set(compact('userGroupPermissionEntity', 'allControllerClasses', 'userGroups', 'selectedControllers', 'selectedUserGroupIds', 'selectedUserGroups', 'funcDesc', 'dbPermissions', 'parentPermissions'));
	}

	/**
	 * It is used to view user's permissions
	 *
	 * @access public
	 * @param integer $userId user id of user
	 * @return void
	 */
	public function user($userId=null) {
		if($userId) {
			$this->loadModel('Usermgmt.Users');

			$user = $this->Users->find()->where(['Users.id'=>$userId])->first();

			if(!empty($user)) {
				$user_group_ids = array_map('trim', explode(',',  strval($user['user_group_id'])));

				$result = $this->UserGroupPermissions->find()->where(['UserGroupPermissions.user_group_id IN'=>$user_group_ids, 'UserGroupPermissions.is_allowed'=>1])->order(['UserGroupPermissions.plugin'=>'ASC', 'UserGroupPermissions.controller'=>'ASC'])->contain(['UserGroups'])->toArray();

				$permissions = [];

				foreach($result as $row) {
					$conAct = $row['controller'].'/'.$row['action'];

					if(!empty($row['plugin'])) {
						$conAct = $row['plugin'].'/'.$conAct;
					}

					if(isset($permissions[$conAct])) {
						$permissions[$conAct]['group'] .= ', '.$row['user_group']['name'];
					} else {
						$permissions[$conAct]['plugin'] = $row['plugin'];
						$permissions[$conAct]['controller'] = $row['controller'];
						$permissions[$conAct]['action'] = $row['action'];
						$permissions[$conAct]['group'] = $row['user_group']['name'];
					}
				}

				$this->set(compact('user', 'permissions'));
			} else {
				$this->Flash->error(__('Invalid user id'));
				$this->redirect(['controller'=>'Users', 'action'=>'index']);
			}
		} else {
			$this->Flash->error(__('Missing user id'));
			$this->redirect(['controller'=>'Users', 'action'=>'index']);
		}
	}

	/**
	 * It is used to change group permission from matrix chart by ajax
	 *
	 * @access public
	 * @param string $controller controller name
	 * @param string $action action name
	 * @param integer $userGroupId user group id
	 * @param string $plugin plugin name
	 * @param string $prefix prefix name
	 * @return void
	 */
	public function changeGroupPermission($controller=null, $action=null, $userGroupId=null, $plugin=null, $prefix=null) {
		if($plugin == 'false') {
			$plugin = null;
		}
		if($prefix == 'false') {
			$prefix = null;
		}

		if($controller && $action && $userGroupId) {
			if($this->getRequest()->is('post')) {
				$this->loadModel('Usermgmt.UserGroups');

				$userGroup = $this->UserGroups->find()->where(['UserGroups.id'=>$userGroupId])->enableHydration(false)->first();

				if(!empty($userGroup)) {
					$mainCond = [];

					if(empty($plugin)) {
						$mainCond[] = 'UserGroupPermissions.plugin IS NULL';
					} else {
						$mainCond['UserGroupPermissions.plugin'] = $plugin;
					}

					if(empty($prefix)) {
						$mainCond[] = 'UserGroupPermissions.prefix IS NULL';
					} else {
						$mainCond['UserGroupPermissions.prefix'] = $prefix;
					}

					$mainCond['UserGroupPermissions.controller'] = $controller;
					$mainCond['UserGroupPermissions.action'] = $action;

					$cond = $mainCond;
					$cond['UserGroupPermissions.user_group_id'] = $userGroupId;

					$userGroupPermission = $this->UserGroupPermissions->find()->where($cond)->first();

					$parentUserGroupPermission = [];

					if($userGroup['parent_id']) {
						$cond = $mainCond;
						$cond['UserGroupPermissions.user_group_id'] = $userGroup['parent_id'];

						$parentUserGroupPermission = $this->UserGroupPermissions->find()->where($cond)->enableHydration(false)->first();
					}

					$is_allowed = 1;
					$save = false;

					if(!empty($userGroupPermission)) {
						if($userGroupPermission['is_allowed']) {
							$is_allowed = 0;
						}

						if($userGroup['parent_id']) {
							//this is sub group so check for parent permission

							if(!empty($parentUserGroupPermission)) {
								if($parentUserGroupPermission['is_allowed'] == $is_allowed) {
									$this->UserGroupPermissions->delete($userGroupPermission);
								}
								else {
									$save = true;
								}
							}
							else {
								$save = true;
							}
						}
						else {
							//this is main group
							$save = true;
						}
					}
					else {
						$save = true;

						if($userGroup['parent_id']) {
							//this is sub group so check for parent permission

							if(!empty($parentUserGroupPermission)) {
								if($parentUserGroupPermission['is_allowed']) {
									$is_allowed = 0;
								}
							}
						}
					}

					if($save) {
						$userGroupPermissionEntity = $this->UserGroupPermissions->newEmptyEntity();

						if(!empty($userGroupPermission)) {
							$userGroupPermissionEntity['id'] = $userGroupPermission['id'];
						}

						$userGroupPermissionEntity['user_group_id'] = $userGroupId;
						$userGroupPermissionEntity['plugin'] = $plugin;
						$userGroupPermissionEntity['prefix'] = $prefix;
						$userGroupPermissionEntity['controller'] = $controller;
						$userGroupPermissionEntity['action'] = $action;
						$userGroupPermissionEntity['is_allowed'] = $is_allowed;

						$this->UserGroupPermissions->save($userGroupPermissionEntity, ['validate'=>false]);
					}

					$this->deleteCache();

					if($this->getRequest()->is('ajax')) {
						if($is_allowed) {
							echo '1';
						} else {
							echo '0';
						}
					}
				}
			}
		}
		exit;
	}

	/**
	 * It is used to delete cache of permissions and used when any permission gets changed by Admin
	 *
	 * @access private
	 * @return void
	 */
	private function deleteCache() {
		$this->UserAuth->deleteCache(['type'=>'permissions']);
	}

	/**
	 * It is used to get controller's action comment
	 *
	 * @access private
	 * @param array $controllerList controller list array
	 * @return array
	 */
	private function getFunctionDesc($controllerList) {
		$funcDesc = [];

		if(!empty($controllerList)) {
			foreach($controllerList as $row) {
				if(!empty($row['actions'])) {
					if(!empty($row['prefix'])) {
						$row['prefix'] = str_replace('.', '\\', $row['prefix']).'\\';
					}

					if(empty($row['plugin'])) {
						$base = Configure::read('App.namespace');

						$controllerClass = $base.'\Controller\\'.$row['prefix'].$row['controller'].'Controller';
					}
					else {
						$controllerClass = $row['plugin'].'\Controller\\'.$row['prefix'].$row['controller'].'Controller';
					}

					foreach($row['actions'] as $action) {
						$comment = $this->FunctionBlock->getComment($controllerClass, $action);

						$funcDesc[$row['prefix'].':'.$row['plugin'].':'.$row['controller']][$action] = $comment;
					}
				}
			}
		}

		return $funcDesc;
	}

	/**
	 * It is used to print permission changes
	 *
	 * @access public
	 * @param string $prefix prefix name
	 * @param string $plugin plugin name
	 * @param string $controller controller name
	 * @param string $action action name
	 * @return array
	 */
	public function printPermissionChanges($prefix=null, $plugin=null, $controller=null, $action=null) {
		if($prefix == 'false') {
			$prefix = null;
		}
		if($plugin == 'false') {
			$plugin = null;
		}

		if($this->getRequest()->is('post')) {
			$cond = [];

			if(empty($prefix)) {
				$cond[] = 'prefix IS NULL';
			} else {
				$cond['prefix'] = $prefix;
			}

			if(empty($plugin)) {
				$cond[] = 'plugin IS NULL';
			} else {
				$cond['plugin'] = $plugin;
			}

			$cond['controller'] = $controller;

			if(!empty($action)) {
				$cond['action'] = $action;
			}

			$this->UserGroupPermissions->deleteAll($cond);

			$this->Flash->success(__('Permissions deleted successfully'));

			$this->redirect(['action'=>'printPermissionChanges']);
		}

		$filesControllerActions = $dbControllerActions = [];

		$controllerList = $this->ControllerList->getControllerAndActions();

		foreach($controllerList as $data) {
			foreach($data['actions'] as $act) {
				$filesControllerActions[$data['prefix'].':'.$data['plugin'].':'.$data['controller']][$act] = $act;
			}
		}

		$userGroupPermissions = $this->UserGroupPermissions->find()->order(['UserGroupPermissions.plugin'=>'ASC', 'UserGroupPermissions.prefix'=>'ASC', 'UserGroupPermissions.controller'=>'ASC', 'UserGroupPermissions.action'=>'ASC'])->enableHydration(false)->toArray();

		foreach($userGroupPermissions as $data) {
			$dbControllerActions[$data['prefix'].':'.$data['plugin'].':'.$data['controller']][$data['action']] = $data['action'];
		}

		$this->set(compact('filesControllerActions', 'dbControllerActions'));
	}
}
