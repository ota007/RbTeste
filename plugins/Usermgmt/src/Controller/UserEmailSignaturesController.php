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

class UserEmailSignaturesController extends UsermgmtAppController {
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
			'Usermgmt.UserEmailSignatures'=>[
				'UserEmailSignatures'=>[
					'type'=>'text',
					'label'=>'Search',
					'tagline'=>'Search by signature',
					'condition'=>'multiple',
					'searchFields'=>['UserEmailSignatures.signature_name', 'UserEmailSignatures.signature'],
					'inputOptions'=>['style'=>'width:300px;']
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

		$this->loadModel('Usermgmt.UserEmailSignatures');

		$action = $this->getRequest()->getParam('action');

		if(isset($this->FormProtection) && $this->getRequest()->is('ajax')) {
			$this->FormProtection->setConfig('unlockedActions', [$action]);
		}
	}

	/**
	 * It displays all email signatures
	 *
	 * @access public
	 * @return void
	 */
	public function index() {
		$cond = [];
		$cond['UserEmailSignatures.user_id'] = $this->UserAuth->getUserId();

		$cond = $this->Search->applySearch($cond);

		$this->paginate = ['limit'=>10, 'conditions'=>$cond, 'order'=>['UserEmailSignatures.id'=>'DESC']];

		$userEmailSignatures = $this->paginate($this->UserEmailSignatures)->toArray();

		$this->set(compact('userEmailSignatures'));

		if($this->getRequest()->is('ajax')) {
			$this->viewBuilder()->setLayout('ajax');
			$this->render('/UserEmailSignatures/all_user_email_signatures');
		}
	}

	/**
	 * It is used to add a new email signature
	 *
	 * @access public
	 * @return void
	 */
	public function add() {
		$userEmailSignatureEntity = $this->UserEmailSignatures->newEmptyEntity();

		if($this->getRequest()->is('post')) {
			$formdata = $this->getRequest()->getData();

			$formdata['UserEmailSignatures']['user_id'] = $this->UserAuth->getUserId();

			$userEmailSignatureEntity = $this->UserEmailSignatures->patchEntity($userEmailSignatureEntity, $formdata, ['validate'=>'forAdd']);

			$errors = $userEmailSignatureEntity->getErrors();

			if($this->getRequest()->is('ajax')) {
				if(empty($errors)) {
					$response = ['error'=>0, 'message'=>'success'];
				} else {
					$response = ['error'=>1, 'message'=>'failure'];
					$response['data']['UserEmailSignatures'] = $errors;
				}
				echo json_encode($response);exit;
			} else {
				if(empty($errors)) {
					$userEmailSignatureEntity['user_id'] = $this->UserAuth->getUserId();

					if($this->UserEmailSignatures->save($userEmailSignatureEntity, ['validate'=>false])) {
						$this->Flash->success(__('The email signature has been added successfully'));

						$this->redirect(['action'=>'index']);
					} else {
						$this->Flash->error(__('Unable to save email signature, please try again'));
					}
				}
			}
		}

		$this->set(compact('userEmailSignatureEntity'));
	}

	/**
	 * It is used to edit user email signature
	 *
	 * @access public
	 * @param integer $emailSignatureId email signature id
	 * @return void
	 */
	public function edit($emailSignatureId=null) {
		if($emailSignatureId) {
			$userId = $this->UserAuth->getUserId();

			$userEmailSignatureEntity = $this->UserEmailSignatures->find()->where(['UserEmailSignatures.id'=>$emailSignatureId, 'UserEmailSignatures.user_id'=>$userId])->first();

			if(!empty($userEmailSignatureEntity)) {
				if($this->getRequest()->is(['put', 'post'])) {
					$formdata = $this->getRequest()->getData();

					$formdata['UserEmailSignatures']['user_id'] = $userEmailSignatureEntity['user_id'];

					$userEmailSignatureEntity = $this->UserEmailSignatures->patchEntity($userEmailSignatureEntity, $formdata, ['validate'=>'forAdd']);

					$errors = $userEmailSignatureEntity->getErrors();

					if($this->getRequest()->is('ajax')) {
						if(empty($errors)) {
							$response = ['error'=>0, 'message'=>'success'];
						} else {
							$response = ['error'=>1, 'message'=>'failure'];
							$response['data']['UserEmailSignatures'] = $errors;
						}
						echo json_encode($response);exit;
					} else {
						if(empty($errors)) {
							if($this->UserEmailSignatures->save($userEmailSignatureEntity, ['validate'=>false])) {
								$this->Flash->success(__('The email signature has been updated successfully'));

								$this->redirect(['action'=>'index', '?'=>['page'=>$this->UserAuth->getPageNumber()]]);
							} else {
								$this->Flash->error(__('Unable to save email signature, please try again'));
							}
						}
					}
				}

				$this->set(compact('userEmailSignatureEntity'));
			} else {
				$this->Flash->error(__('Invalid email signature id'));
				$this->redirect(['action'=>'index']);
			}
		} else {
			$this->Flash->error(__('Missing email signature id'));
			$this->redirect(['action'=>'index']);
		}
	}

	/**
	 * It is used to delete the email signature
	 *
	 * @access public
	 * @param integer $emailSignatureId email signature id
	 * @return void
	 */
	public function delete($emailSignatureId=null) {
		if($emailSignatureId) {
			if($this->getRequest()->is('post')) {
				$userId = $this->UserAuth->getUserId();

				$userEmailSignatureEntity = $this->UserEmailSignatures->find()->where(['UserEmailSignatures.id'=>$emailSignatureId, 'UserEmailSignatures.user_id'=>$userId])->first();

				if(!empty($userEmailSignatureEntity)) {
					if($this->UserEmailSignatures->delete($userEmailSignatureEntity)) {
						$this->Flash->success(__('Selected email signature has been deleted successfully'));
					} else {
						$this->Flash->error(__('Unable to delete selected email signature, please try again'));
					}
				} else {
					$this->Flash->error(__('Invalid email signature id'));
				}
			}
		} else {
			$this->Flash->error(__('Missing email signature id'));
		}

		$this->redirect(['action'=>'index', '?'=>['page'=>$this->UserAuth->getPageNumber()]]);
	}
}
