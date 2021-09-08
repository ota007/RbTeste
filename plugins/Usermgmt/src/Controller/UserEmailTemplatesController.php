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

class UserEmailTemplatesController extends UsermgmtAppController {
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
			'Usermgmt.UserEmailTemplates'=>[
				'UserEmailTemplates'=>[
					'type'=>'text',
					'label'=>'Search',
					'tagline'=>'Search by email template name, header, footer',
					'condition'=>'multiple',
					'searchFields'=>['UserEmailTemplates.template_name', 'UserEmailTemplates.template_header', 'UserEmailTemplates.template_footer'],
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

		$this->loadModel('Usermgmt.UserEmailTemplates');

		$action = $this->getRequest()->getParam('action');

		if(isset($this->FormProtection) && $this->getRequest()->is('ajax')) {
			$this->FormProtection->setConfig('unlockedActions', [$action]);
		}
	}

	/**
	 * It displays all email templates
	 *
	 * @access public
	 * @return void
	 */
	public function index() {
		$cond = [];
		$cond['UserEmailTemplates.user_id'] = $this->UserAuth->getUserId();

		$cond = $this->Search->applySearch($cond);

		$this->paginate = ['limit'=>10, 'conditions'=>$cond, 'order'=>['UserEmailTemplates.id'=>'DESC']];

		$userEmailTemplates = $this->paginate($this->UserEmailTemplates)->toArray();

		$this->set(compact('userEmailTemplates'));

		if($this->getRequest()->is('ajax')) {
			$this->viewBuilder()->setLayout('ajax');
			$this->render('/UserEmailTemplates/all_user_email_templates');
		}
	}

	/**
	 * It is used to add a new email template
	 *
	 * @access public
	 * @return void
	 */
	public function add() {
		$userEmailTemplateEntity = $this->UserEmailTemplates->newEmptyEntity();

		if($this->getRequest()->is('post')) {
			$formdata = $this->getRequest()->getData();

			$formdata['UserEmailTemplates']['user_id'] = $this->UserAuth->getUserId();

			$userEmailTemplateEntity = $this->UserEmailTemplates->patchEntity($userEmailTemplateEntity, $formdata, ['validate'=>'forAdd']);

			$errors = $userEmailTemplateEntity->getErrors();

			if($this->getRequest()->is('ajax')) {
				if(empty($errors)) {
					$response = ['error'=>0, 'message'=>'success'];
				} else {
					$response = ['error'=>1, 'message'=>'failure'];
					$response['data']['UserEmailTemplates'] = $errors;
				}
				echo json_encode($response);exit;
			} else {
				if(empty($errors)) {
					if($this->UserEmailTemplates->save($userEmailTemplateEntity, ['validate'=>false])) {
						$this->Flash->success(__('The email template has been added successfully'));

						$this->redirect(['action'=>'index']);
					} else {
						$this->Flash->error(__('Unable to add email template, please try again'));
					}
				}
			}
		}

		$this->set(compact('userEmailTemplateEntity'));
	}

	/**
	 * It is used to edit email template
	 *
	 * @access public
	 * @param integer $emailTemplateId email template id
	 * @return void
	 */
	public function edit($emailTemplateId=null) {
		if($emailTemplateId) {
			$userId = $this->UserAuth->getUserId();

			$userEmailTemplateEntity = $this->UserEmailTemplates->find()->where(['UserEmailTemplates.id'=>$emailTemplateId, 'UserEmailTemplates.user_id'=>$userId])->first();

			if(!empty($userEmailTemplateEntity)) {
				if($this->getRequest()->is(['put', 'post'])) {
					$formdata = $this->getRequest()->getData();

					$formdata['UserEmailTemplates']['user_id'] = $userEmailTemplateEntity['user_id'];

					$userEmailTemplateEntity = $this->UserEmailTemplates->patchEntity($userEmailTemplateEntity, $formdata, ['validate'=>'forAdd']);

					$errors = $userEmailTemplateEntity->getErrors();

					if($this->getRequest()->is('ajax')) {
						if(empty($errors)) {
							$response = ['error'=>0, 'message'=>'success'];
						} else {
							$response = ['error'=>1, 'message'=>'failure'];
							$response['data']['UserEmailTemplates'] = $errors;
						}
						echo json_encode($response);exit;
					} else {
						if(empty($errors)) {
							if($this->UserEmailTemplates->save($userEmailTemplateEntity, ['validate'=>false])) {
								$this->Flash->success(__('The email template has been updated successfully'));

								$this->redirect(['action'=>'index', '?'=>['page'=>$this->UserAuth->getPageNumber()]]);
							} else {
								$this->Flash->error(__('Unable to update email template, please try again'));
							}
						}
					}
				}

				$this->set(compact('userEmailTemplateEntity'));
			} else {
				$this->Flash->error(__('Invalid email template id'));
				$this->redirect(['action'=>'index']);
			}
		} else {
			$this->Flash->error(__('Missing email template id'));
			$this->redirect(['action'=>'index']);
		}
	}

	/**
	 * It is used to delete the email template
	 *
	 * @access public
	 * @param integer $emailTemplateId email template id
	 * @return void
	 */
	public function delete($emailTemplateId=null) {
		if(!empty($emailTemplateId)) {
			if($this->getRequest()->is('post')) {
				$userId = $this->UserAuth->getUserId();

				$userEmailTemplate = $this->UserEmailTemplates->find()->where(['UserEmailTemplates.id'=>$emailTemplateId, 'UserEmailTemplates.user_id'=>$userId])->first();

				if(!empty($userEmailTemplate)) {
					if($this->UserEmailTemplates->delete($userEmailTemplate)) {
						$this->Flash->success(__('Selected email template has been deleted successfully'));
					} else {
						$this->Flash->error(__('Unable to delete selected email template, please try again'));
					}
				} else {
					$this->Flash->error(__('Invalid email template id'));
				}
			}
		} else {
			$this->Flash->error(__('Missing email template id'));
		}

		$this->redirect(['action'=>'index', '?'=>['page'=>$this->UserAuth->getPageNumber()]]);
	}
}
