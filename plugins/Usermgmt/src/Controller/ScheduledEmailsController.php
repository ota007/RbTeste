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
use Cake\Mailer\Email;

class ScheduledEmailsController extends UsermgmtAppController {
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
			'Usermgmt.ScheduledEmails'=>[
				'ScheduledEmails'=>[
					'type'=>'text',
					'label'=>'Search',
					'tagline'=>'Search by from name, email, subject, message',
					'condition'=>'multiple',
					'searchBreak'=>true,
					'matchAllWords'=>true,
					'searchFields'=>['ScheduledEmails.from_name', 'ScheduledEmails.from_email', 'ScheduledEmails.subject', 'ScheduledEmails.message'],
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

		$this->loadModel('Usermgmt.ScheduledEmails');

		$action = $this->getRequest()->getParam('action');

		if(isset($this->FormProtection) && $this->getRequest()->is('ajax')) {
			$this->FormProtection->setConfig('unlockedActions', [$action]);
		}
	}

	/**
	 * It displays all scheduled Emails
	 *
	 * @access public
	 * @return void
	 */
	public function index() {
		$cond = [];

		if(!$this->UserAuth->isAdmin()) {
			$cond['ScheduledEmails.scheduled_by'] = $this->UserAuth->getUserId();
		}

		$cond = $this->Search->applySearch($cond);

		$this->paginate = ['limit'=>10, 'order'=>['ScheduledEmails.id'=>'DESC'], 'contain'=>['Users'], 'conditions'=>$cond];

		$scheduledEmails = $this->paginate($this->ScheduledEmails)->toArray();

		$this->loadModel('Usermgmt.UserGroups');
		$this->loadModel('Usermgmt.ScheduledEmailRecipients');

		foreach($scheduledEmails as $key=>$row) {
			if(!empty($row['user_group_id'])) {
				$scheduledEmails[$key]['group_name'] = $this->UserGroups->getGroupsByIds($row['user_group_id']);
			}

			$scheduledEmails[$key]['total_sent_emails'] = $this->ScheduledEmailRecipients->find()->where(['ScheduledEmailRecipients.scheduled_email_id'=>$row['id'], 'ScheduledEmailRecipients.is_email_sent'=>1])->count();
		}

		$this->set(compact('scheduledEmails'));

		if($this->getRequest()->is('ajax')) {
			$this->viewBuilder()->setLayout('ajax');
			$this->render('/ScheduledEmails/all_scheduled_emails');
		}
	}

	/**
	 * Used to edit scheduled email
	 *
	 * @access public
	 * @param integer $scheduledEmailId scheduled email id
	 * @return void
	 */
	public function edit($scheduledEmailId=null) {
		if(!empty($scheduledEmailId)) {
			$cond = [];
			$cond['ScheduledEmails.id'] = $scheduledEmailId;

			if(!$this->UserAuth->isAdmin()) {
				$cond['ScheduledEmails.scheduled_by'] = $this->UserAuth->getUserId();
			}

			$scheduledEmailEntity = $this->ScheduledEmails->find()->where($cond)->first();

			if(!empty($scheduledEmailEntity)) {
				if($this->getRequest()->is(['put', 'post'])) {
					$formdata = $this->getRequest()->getData();

					if(!empty($formdata['ScheduledEmails']['schedule_date'])) {
						$formdata['ScheduledEmails']['schedule_date'] = date('Y-m-d H:i:s', strtotime($formdata['ScheduledEmails']['schedule_date']));
					}

					$scheduledEmailEntity = $this->ScheduledEmails->patchEntity($scheduledEmailEntity, $formdata, ['validate'=>'forSend']);

					$errors = $scheduledEmailEntity->getErrors();

					if($this->getRequest()->is('ajax')) {
						if(empty($errors)) {
							$response = ['error'=>0, 'message'=>'success'];
						} else {
							$response = ['error'=>1, 'message'=>'failure'];
							$response['data']['ScheduledEmails'] = $errors;
						}
						echo json_encode($response);exit;
					} else {
						if(empty($errors)) {

							if($this->ScheduledEmails->save($scheduledEmailEntity, ['validate'=>false])) {
								$this->Flash->success(__('The scheduled email has been updated successfully'));

								$this->redirect(['action'=>'index', '?'=>['page'=>$this->UserAuth->getPageNumber()]]);
							} else {
								$this->Flash->error(__('Unable to update scheduled email, please try again.'));
							}
						}
					}
				} else {
					if(!empty($scheduledEmailEntity['schedule_date'])) {
						$scheduledEmailEntity['schedule_date'] = $this->UserAuth->getFormatDatetime($scheduledEmailEntity['schedule_date']);
					}
				}

				$this->loadModel('Usermgmt.UserGroups');
				$groups = $this->UserGroups->getUserGroups(false);

				$this->set(compact('groups', 'scheduledEmailEntity'));
			} else {
				$this->Flash->error(__('Invalid Scheduled Email Id'));
				$this->redirect(['action'=>'index']);
			}
		} else {
			$this->Flash->error(__('Missing Scheduled Email Id'));
			$this->redirect(['action'=>'index']);
		}
	}

	/**
	 * Used to view scheduled email details and it's recipients
	 *
	 * @access public
	 * @param integer $scheduledEmailId scheduled email id
	 * @return void
	 */
	public function view($scheduledEmailId=null) {
		if(!empty($scheduledEmailId)) {
			$cond = [];
			$cond['ScheduledEmails.id'] = $scheduledEmailId;

			if(!$this->UserAuth->isAdmin()) {
				$cond['ScheduledEmails.scheduled_by'] = $this->UserAuth->getUserId();
			}

			$scheduledEmail = $this->ScheduledEmails->find()->where($cond)->contain(['Users'])->first();

			if(!empty($scheduledEmail)) {
				if(!empty($scheduledEmail['user_group_id'])) {
					$this->loadModel('Usermgmt.UserGroups');
					$scheduledEmail['group_name'] = $this->UserGroups->getGroupsByIds($scheduledEmail['user_group_id']);
				}

				$this->loadModel('Usermgmt.ScheduledEmailRecipients');

				$scheduledEmail['total_sent_emails'] = $this->ScheduledEmailRecipients->find()->where(['ScheduledEmailRecipients.scheduled_email_id'=>$scheduledEmailId, 'ScheduledEmailRecipients.is_email_sent'=>1])->count();

				$scheduledEmailRecipients = $this->ScheduledEmailRecipients->find()->where(['ScheduledEmailRecipients.scheduled_email_id'=>$scheduledEmailId])->contain(['Users'])->enableHydration(false)->toArray();

				$this->set(compact('scheduledEmail', 'scheduledEmailRecipients'));
			} else {
				$this->Flash->error(__('Invalid Scheduled Email Id'));
				$this->redirect(['action'=>'index']);
			}
		} else {
			$this->Flash->error(__('Missing Scheduled Email Id'));
			$this->redirect(['action'=>'index']);
		}
	}

	/**
	 * Used to delete scheduled email
	 *
	 * @access public
	 * @param integer $scheduledEmailId scheduled email id
	 * @return void
	 */
	public function delete($scheduledEmailId=null) {
		if(!empty($scheduledEmailId)) {
			$cond = [];
			$cond['ScheduledEmails.id'] = $scheduledEmailId;

			if(!$this->UserAuth->isAdmin()) {
				$cond['ScheduledEmails.scheduled_by'] = $this->UserAuth->getUserId();
			}

			$scheduledEmail = $this->ScheduledEmails->find()->where($cond)->first();

			if(!empty($scheduledEmail)) {
				$this->loadModel('Usermgmt.ScheduledEmailRecipients');

				if(!$scheduledEmail['is_sent']) {
					$total_sent_emails = $this->ScheduledEmailRecipients->find()->where(['ScheduledEmailRecipients.scheduled_email_id'=>$scheduledEmailId, 'ScheduledEmailRecipients.is_email_sent'=>1])->count();

					if($total_sent_emails) {
						$this->ScheduledEmailRecipients->deleteAll(['scheduled_email_id'=>$scheduledEmailId, 'is_email_sent'=>0]);

						$scheduledEmail['is_sent'] = 1;
						$this->ScheduledEmails->save($scheduledEmail, ['validate'=>false]);

						$this->Flash->success(__('Only few recipients have been deleted successfully'));
					} else {
						$this->ScheduledEmails->delete($scheduledEmail);
						$this->Flash->success(__('Scheduled Email and recipients have been deleted successfully'));
					}
				} else {
					$this->Flash->error(__('All emails have been sent already so this scheduled email cannot be deleted'));
				}
			} else {
				$this->Flash->error(__('Invalid User Email Id'));
			}
		} else {
			$this->Flash->error(__('Missing User Email Id'));
		}

		$this->redirect(['action'=>'index', '?'=>['page'=>$this->UserAuth->getPageNumber()]]);
	}

	/**
	 * Used to delete scheduled email recipient
	 *
	 * @access public
	 * @param integer $scheduledEmailRecipientId scheduled email recipient id
	 * @return void
	 */
	public function deleteRecipient($scheduledEmailRecipientId=null) {
		$scheduledEmailId = null;
		
		$response = ['error'=>1, 'message'=>'failure'];

		if(!empty($scheduledEmailRecipientId)) {
			$cond = [];
			$cond['ScheduledEmailRecipients.id'] = $scheduledEmailRecipientId;

			if(!$this->UserAuth->isAdmin()) {
				$cond['ScheduledEmails.scheduled_by'] = $this->UserAuth->getUserId();
			}

			$this->loadModel('Usermgmt.ScheduledEmailRecipients');

			$scheduledEmailRecipient = $this->ScheduledEmailRecipients->find()->where($cond)->contain(['ScheduledEmails'])->first();

			if(!empty($scheduledEmailRecipient)) {
				$scheduledEmailId = $scheduledEmailRecipient['scheduled_email_id'];

				if($this->getRequest()->is('post') || isset($_SERVER['HTTP_REFERER'])) {
					if(!$scheduledEmailRecipient['is_email_sent']) {
						$this->ScheduledEmailRecipients->delete($scheduledEmailRecipient);

						$response = ['error'=>0, 'message'=>__('Selected recipient has been deleted successfully')];
					} else {
						$response['message'] = __('Selected Recipient cannot be deleted');
					}
				} else {
					$response['message'] = __('Invalid Request');
				}
			} else {
				$response['message'] = __('Invalid Scheduled Email Recipient Id');
			}
		} else {
			$response['message'] = __('Missing Scheduled Email Recipient Id');
		}

		if($this->getRequest()->is('ajax')) {
			echo json_encode($response);exit;
		} else {
			if($response['error']) {
				$this->Flash->error($response['message']);
			} else {
				$this->Flash->success($response['message']);
			}

			if($scheduledEmailId) {
				$this->redirect(['action'=>'view', $scheduledEmailId, '?'=>['page'=>$this->UserAuth->getPageNumber()]]);
			} else {
				$this->redirect(['action'=>'index', '?'=>['page'=>$this->UserAuth->getPageNumber()]]);
			}
		}
	}
}
