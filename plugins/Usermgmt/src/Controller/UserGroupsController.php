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

class UserGroupsController extends UsermgmtAppController {
	/**
	 * This controller uses following default pagination values
	 *
	 * @var array
	 */
	public $paginate = [
		'limit'=>25
	];

	/**
	 * This controller uses search filters in following functions for ex index, online function
	 * For all possible option see Search Component getDefaultSearchOptions function
	 *
	 * @var array
	 */
	public $searchFields = [
		'index'=>[
			'Usermgmt.UserGroups'=>[
				'UserGroups'=>[
					'type'=>'text',
					'label'=>'Search',
					'tagline'=>'Search by group name, description',
					'condition'=>'multiple',
					'searchFields'=>['UserGroups.name', 'UserGroups.description'],
					'inputOptions'=>['style'=>'width:200px;']
				],
				'UserGroups.is_registration_allowed'=>[
					'type'=>'select',
					'label'=>'Registration Allowed',
					'options'=>[''=>'Select', '1'=>'Yes', '0'=>'No']
				]
			]
		]
	];

	/**
	 * Initialization hook method.
	 *
	 * Use this method to add common initialization code like loading components.
	 *
	 * @return void
	 */
	public function initialize(): void {
		parent::initialize();

		$this->loadComponent('Usermgmt.Search');
	}

	/**
	 * Called before the controller action. You can use this method to configure and customize components
	 * or perform logic that needs to happen before each controller action.
	 *
	 * @return void
	 */
	public function beforeFilter(EventInterface $event) {
		parent::beforeFilter($event);

		$this->loadModel('Usermgmt.UserGroups');

		$action = $this->getRequest()->getParam('action');

		if(isset($this->FormProtection) && $this->getRequest()->is('ajax')) {
			$this->FormProtection->setConfig('unlockedActions', [$action]);
		}
	}

	/**
	 * It displays all user groups
	 *
	 * @access public
	 * @return void
	 */
	public function index() {
		$cond = [];
		$cond = $this->Search->applySearch($cond);

		$this->paginate = ['limit'=>10, 'conditions'=>$cond, 'order'=>['UserGroups.id'=>'ASC']];

		$userGroups = $this->paginate($this->UserGroups)->toArray();

		$allGroups = $this->UserGroups->getAllGroups(false);

		$this->set(compact('allGroups', 'userGroups'));

		if($this->getRequest()->is('ajax')) {
			$this->viewBuilder()->setLayout('ajax');
			$this->render('/UserGroups/all_user_groups');
		}
	}

	/**
	 * It is used to add a new group
	 *
	 * @access public
	 * @return void
	 */
	public function add() {
		$userGroupEntity = $this->UserGroups->newEmptyEntity();

		if($this->getRequest()->is('post')) {
			$userGroupEntity = $this->UserGroups->patchEntity($userGroupEntity, $this->getRequest()->getData(), ['validate'=>'forAdd']);

			$errors = $userGroupEntity->getErrors();

			if($this->getRequest()->is('ajax')) {
				if(empty($errors)) {
					$response = ['error'=>0, 'message'=>'success'];
				} else {
					$response = ['error'=>1, 'message'=>'failure'];
					$response['data']['UserGroups'] = $errors;
				}
				echo json_encode($response);exit;
			} else {
				if(empty($errors)) {
					if($this->UserGroups->save($userGroupEntity, ['validate'=>false])) {
						$this->Flash->success(__('The group has been added successfully'));

						$this->redirect(['action'=>'index']);
					} else {
						$this->Flash->error(__('Unable to add group, please try again'));
					}
				}
			}
		}

		$parentGroups = $this->UserGroups->getParentGroups();

		$this->set(compact('parentGroups', 'userGroupEntity'));
	}

	/**
	 * It is used to edit existing group
	 *
	 * @access public
	 * @param integer $userGroupId user group id
	 * @return void
	 */
	public function edit($userGroupId=null) {
		if($userGroupId) {
			$userGroupEntity = $this->UserGroups->find()->where(['UserGroups.id'=>$userGroupId])->first();

			if(!empty($userGroupEntity)) {
				if($this->getRequest()->is(['put', 'post'])) {
					$userGroupEntity = $this->UserGroups->patchEntity($userGroupEntity, $this->getRequest()->getData(), ['validate'=>'forAdd']);

					$errors = $userGroupEntity->getErrors();

					if($this->getRequest()->is('ajax')) {
						if(empty($errors)) {
							$response = ['error'=>0, 'message'=>'success'];
						} else {
							$response = ['error'=>1, 'message'=>'failure'];
							$response['data']['UserGroups'] = $errors;
						}
						echo json_encode($response);exit;
					} else {
						if(empty($errors)) {
							if($this->UserGroups->save($userGroupEntity, ['validate'=>false])) {
								$this->Flash->success(__('The group has been updated successfully'));

								$this->redirect(['action'=>'index', '?'=>['page'=>$this->UserAuth->getPageNumber()]]);
							} else {
								$this->Flash->error(__('Unable to update group, please try again.'));
							}
						}
					}
				}

				$parentGroups = $this->UserGroups->getParentGroups($userGroupId);

				$this->set(compact('userGroupEntity', 'parentGroups'));
			} else {
				$this->Flash->error(__('Invalid group id'));
				$this->redirect(['action'=>'index']);
			}
		} else {
			$this->Flash->error(__('Missing group id'));
			$this->redirect(['action'=>'index']);
		}
	}

	/**
	 * It is used to delete existing group, it also checks if any user is associated with group before delete
	 *
	 * @access public
	 * @param integer $userGroupId user group id
	 * @return void
	 */
	public function delete($userGroupId=null) {
		if(!empty($userGroupId)) {
			$userGroup = $this->UserGroups->find()->where(['UserGroups.id'=>$userGroupId])->first();

			if(!empty($userGroup)) {
				if($this->getRequest()->is('post')) {
					$this->loadModel('Usermgmt.Users');

					if($this->Users->isUserAssociatedWithGroup($userGroupId)) {
						$this->Flash->warning(__('Sorry some users are associated with this group, You cannot delete this group'));
					}
					else {
						if(!$this->UserGroups->exists(['UserGroups.parent_id'=>$userGroupId])) {
							if($this->UserGroups->delete($userGroup)) {
								$this->Flash->success(__('Selected group has been deleted successfully'));
							} else {
								$this->Flash->warning(__('Selected group could not be deleted, please try again'));
							}
						} else {
							$this->Flash->error(__('Sorry sub group(s) exist for this group, You cannot delete this group'));
						}
					}
				}
			} else {
				$this->Flash->error(__('Invalid User Group Id'));
			}
		} else {
			$this->Flash->error(__('Missing User Group Id'));
		}

		$this->redirect(['action'=>'index', '?'=>['page'=>$this->UserAuth->getPageNumber()]]);
	}
}
