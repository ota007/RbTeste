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
use Cake\Mailer\Mailer;
use Cake\I18n\Time;

class UserEmailsController extends UsermgmtAppController {
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
			'Usermgmt.UserEmails'=>[
				'UserEmails'=>[
					'type'=>'text',
					'label'=>'Search',
					'tagline'=>'Search by from name, email, subject, message',
					'condition'=>'multiple',
					'searchBreak'=>true,
					'matchAllWords'=>true,
					'searchFields'=>['UserEmails.from_name', 'UserEmails.from_email', 'UserEmails.subject', 'UserEmails.message'],
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

		$this->loadModel('Usermgmt.UserEmails');

		$action = $this->getRequest()->getParam('action');

		if(isset($this->FormProtection) && ($this->getRequest()->is('ajax') || $action == 'send')) {
			$this->FormProtection->setConfig('unlockedActions', [$action]);
		}
	}

	/**
	 * It displays all sent Emails
	 *
	 * @access public
	 * @return void
	 */
	public function index() {
		$cond = [];
		if(!$this->UserAuth->isAdmin()) {
			$cond['UserEmails.sent_by'] = $this->UserAuth->getUserId();
		}

		$cond = $this->Search->applySearch($cond);

		$this->paginate = ['limit'=>10, 'order'=>['UserEmails.id'=>'DESC'], 'contain'=>['Users'], 'conditions'=>$cond];

		$userEmails = $this->paginate($this->UserEmails)->toArray();

		$this->loadModel('Usermgmt.UserGroups');
		$this->loadModel('Usermgmt.UserEmailRecipients');

		foreach($userEmails as $key=>$row) {
			if(!empty($row['user_group_id'])) {
				$userEmails[$key]['group_name'] = $this->UserGroups->getGroupsByIds($row['user_group_id']);
			}

			$userEmails[$key]['total_sent_emails'] = $this->UserEmailRecipients->find()->where(['UserEmailRecipients.user_email_id'=>$row['id'], 'UserEmailRecipients.is_email_sent'=>1])->count();
		}

		$this->set(compact('userEmails'));

		if($this->getRequest()->is('ajax')) {
			$this->viewBuilder()->setLayout('ajax');
			$this->render('/UserEmails/all_user_emails');
		}
	}

	/**
	 * It is used to send emails to groups, selected users, manual emails
	 *
	 * @access public
	 * @return void
	 */
	public function send() {
		$confirmRender = false;

		$userEmailEntity = $this->UserEmails->newEmptyEntity();

		$this->loadModel('Usermgmt.UserEmailTemplates');
		$this->loadModel('Usermgmt.UserEmailSignatures');
		$this->loadModel('Usermgmt.Users');

		if($this->getRequest()->is('post')) {
			$formdata = $this->getRequest()->getData();

			$confirm = null;
			if(isset($formdata['confirmEmail'])) {
				$confirm = 'confirm';
			}

			if(isset($formdata['UserEmails']['type'])) {
				if($formdata['UserEmails']['type'] == 'USERS') {
					unset($formdata['UserEmails']['user_group_id']);
					unset($formdata['UserEmails']['to_email']);

					if(!is_array($formdata['UserEmails']['user_id'])) {
						$formdata['UserEmails']['user_id'] = [$formdata['UserEmails']['user_id']];
					}
				}
				else if($formdata['UserEmails']['type'] == 'GROUPS') {
					unset($formdata['UserEmails']['user_id']);
					unset($formdata['UserEmails']['to_email']);

					if(is_array($formdata['UserEmails']['user_group_id'])) {
						sort($formdata['UserEmails']['user_group_id']);
						$formdata['UserEmails']['user_group_id'] = implode(',', $formdata['UserEmails']['user_group_id']);
					}
				}
				else {
					unset($formdata['UserEmails']['user_id']);
					unset($formdata['UserEmails']['user_group_id']);
				}
			}

			if(!empty($formdata['UserEmails']['schedule_date'])) {
				$formdata['UserEmails']['schedule_date'] = date('Y-m-d H:i:s', strtotime($formdata['UserEmails']['schedule_date']));
			}

			$userEmailEntity = $this->UserEmails->patchEntity($userEmailEntity, $formdata, ['validate'=>'forSend']);

			$errors = $userEmailEntity->getErrors();

			if($this->getRequest()->is('ajax')) {
				if(empty($errors)) {
					$response = ['error'=>0, 'message'=>'success'];
				} else {
					$response = ['error'=>1, 'message'=>'failure'];
					$response['data']['UserEmails'] = $errors;
				}
				echo json_encode($response);exit;
			} else {
				if(empty($errors)) {
					$users = [];

					if(is_null($confirm)) {
						if($userEmailEntity['type'] == 'GROUPS') {
							$cond = $groupCond = [];

							$cond['Users.is_active'] = 1;

							$userEmailEntity['user_group_id'] = explode(',',  strval($userEmailEntity['user_group_id']));

							foreach($userEmailEntity['user_group_id'] as $groupId) {
								$groupCond[] = ['Users.user_group_id'=>$groupId];
								$groupCond[] = ['Users.user_group_id like'=>$groupId.',%'];
								$groupCond[] = ['Users.user_group_id like'=>'%,'.$groupId.',%'];
								$groupCond[] = ['Users.user_group_id like'=>'%,'.$groupId];
							}
							$cond['OR'] = $groupCond;

							$users = $this->Users->find()->select(['Users.id', 'Users.first_name', 'Users.last_name', 'Users.email'])->where($cond)->order(['Users.first_name'=>'ASC'])->enableHydration(false)->toArray();
						}
						else if($userEmailEntity['type'] == 'USERS') {
							$users = $this->Users->find()->select(['Users.id', 'Users.first_name', 'Users.last_name', 'Users.email'])->where(['Users.id IN'=>$userEmailEntity['user_id'], 'Users.is_active'=>1])->order(['Users.first_name'=>'ASC'])->enableHydration(false)->toArray();
						}
						else if($userEmailEntity['type'] == 'MANUAL') {
							$emails = array_filter(array_map('trim', explode(',', strtolower($userEmailEntity['to_email']))));
							$i = 0;

							foreach($emails as $email) {
								$users[$i]['email'] = $email;
								$users[$i]['id'] = null;
								$users[$i]['first_name'] = null;
								$users[$i]['last_name'] = null;

								$i++;
							}
						}
					}
					else if($confirm == 'confirm') {
						$i = 0;

						foreach($userEmailEntity['EmailList'] as $row) {
							if(isset($row['emailcheck']) && $row['emailcheck'] && !empty($row['email'])) {
								$users[$i]['id'] = $row['uid'];
								$users[$i]['email'] = $row['email'];

								$i++;
							}
						}
					}

					if(!empty($users)) {
						if(is_null($confirm)) {
							$template = $signature = [];

							$userEmailEntity['total_rows'] = count($users);

							if($userEmailEntity['template']) {
								$template = $this->UserEmailTemplates->getEmailTemplateById($userEmailEntity['template']);
							}

							if($userEmailEntity['signature']) {
								$signature = $this->UserEmailSignatures->getEmailSignatureById($userEmailEntity['signature']);
							}

							$message = '';

							if(!empty($template['template_header'])) {
								$message .= $template['template_header'];
							}

							$message .= $userEmailEntity['message'];

							if(!empty($signature['signature'])) {
								$message .= $signature['signature'];
							}

							if(!empty($template['template_footer'])) {
								$message .= $template['template_footer'];
							}

							$userEmailEntity['modified_message'] = $message;

							$this->getRequest()->getSession()->write('send_email_data', $userEmailEntity);

							$this->set(compact('users'));

							$confirmRender = true;
						}
						else if($confirm == 'confirm') {
							$data = $this->getRequest()->getSession()->read('send_email_data');
							$postRows = count($userEmailEntity['EmailList']);

							if($data['total_rows'] > $postRows) {
								die('We did not get all email rows in post data, please check max_input_vars configuration on server.');
							}

							if(!empty($data['schedule_date'])) {
								ini_set('memory_limit','256M');
								ini_set('max_execution_time', '5200');

								$scheduled = $this->saveScheduledEmails($data, $users);

								if($scheduled) {
									$this->getRequest()->getSession()->delete('send_email_data');
									$this->redirect(['controller'=>'ScheduledEmails', 'action'=>'index']);
								} else {
									$this->redirect(['action'=>'send']);
								}
							} else {
								$sent = $this->sendAndSaveUserEmail($data, $users);

								if($sent) {
									$this->getRequest()->getSession()->delete('send_email_data');
									$this->redirect(['action'=>'index']);
								} else {
									$this->redirect(['action'=>'send']);
								}
							}
						}
					}
					else {
						if($userEmailEntity['type'] == 'GROUPS') {
							$this->Flash->warning(__('No users found in selected group'));
						}
					}
				}
			}
		}
		else {
			$userEmailEntity['from_name'] = EMAIL_FROM_NAME;
			$userEmailEntity['from_email'] = EMAIL_FROM_ADDRESS;

			if($this->getRequest()->getSession()->check('send_email_data')) {
				$userEmailEntity = $this->getRequest()->getSession()->consume('send_email_data');
			}
		}

		if(!$confirmRender) {
			$sel_users = [];

			if(!empty($userEmailEntity['user_id'])) {
				$sel_users = $this->Users->getAllUsersWithUserIds($userEmailEntity['user_id']);
			}

			$templates = $this->UserEmailTemplates->getEmailTemplates($this->UserAuth->getUserId());
			$signatures = $this->UserEmailSignatures->getEmailSignatures($this->UserAuth->getUserId());

			$this->set(compact('sel_users', 'templates', 'signatures'));
		}

		$this->loadModel('Usermgmt.UserGroups');
		$groups = $this->UserGroups->getUserGroups(false);

		$this->set(compact('groups', 'userEmailEntity'));

		if($confirmRender) {
			$this->render('send_confirm');
		}
	}

	/**
	 * It is used to send email to user
	 *
	 * @access public
	 * @param integer $userId user id of user
	 * @return void
	 */
	public function sendToUser($userId=null) {
		$confirmRender = false;

		if($userId) {
			$this->loadModel('Usermgmt.Users');
			$user = $this->Users->getUserById($userId);

			if(!empty($user)) {
				$userEmailEntity = $this->UserEmails->newEmptyEntity();

				$this->loadModel('Usermgmt.UserEmailTemplates');
				$this->loadModel('Usermgmt.UserEmailSignatures');

				if($this->getRequest()->is('post')) {
					$formdata = $this->getRequest()->getData();

					$confirm = null;
					if(isset($formdata['confirmEmail'])) {
						$confirm = 'confirm';
					}

					$userEmailEntity = $this->UserEmails->patchEntity($userEmailEntity, $formdata, ['validate'=>'forSend']);

					$errors = $userEmailEntity->getErrors();

					if($this->getRequest()->is('ajax')) {
						if(empty($errors)) {
							$response = ['error'=>0, 'message'=>'success'];
						} else {
							$response = ['error'=>1, 'message'=>'failure'];
							$response['data']['UserEmails'] = $errors;
						}
						echo json_encode($response);exit;
					} else {
						if(empty($errors)) {
							if(is_null($confirm)) {
								$template = $signature = [];

								if($userEmailEntity['template']) {
									$template = $this->UserEmailTemplates->getEmailTemplateById($userEmailEntity['template']);
								}

								if($userEmailEntity['signature']) {
									$signature = $this->UserEmailSignatures->getEmailSignatureById($userEmailEntity['signature']);
								}

								$message = '';

								if(!empty($template['template_header'])) {
									$message .= $template['template_header'];
								}

								$message .= $userEmailEntity['message'];

								if(!empty($signature['signature'])) {
									$message .= $signature['signature'];
								}

								if(!empty($template['template_footer'])) {
									$message .= $template['template_footer'];
								}

								$userEmailEntity['modified_message'] = $message;

								$this->getRequest()->getSession()->write('send_user_email_data', $userEmailEntity);
								
								$confirmRender = true;
							}
							else if($confirm == 'confirm') {
								$data = $this->getRequest()->getSession()->read('send_user_email_data');
								$data['type'] = 'USERS';

								$users = [];
								$users[0]['id'] = $userId;
								$users[0]['email'] = $data['to'];

								$sent = $this->sendAndSaveUserEmail($data, $users);

								if($sent) {
									$this->getRequest()->getSession()->delete('send_user_email_data');

									if($sent) {
										$this->redirect(['controller'=>'Users', 'action'=>'index', '?'=>['page'=>$this->UserAuth->getPageNumber()]]);
									}
								} else {
									$this->redirect(['action'=>'sendToUser', $userId, '?'=>['page'=>$this->UserAuth->getPageNumber()]]);
								}
							}
						}
					}
				}
				else {
					$userEmailEntity['from_name'] = EMAIL_FROM_NAME;
					$userEmailEntity['from_email'] = EMAIL_FROM_ADDRESS;
					$userEmailEntity['to'] = $user['email'];

					if($this->getRequest()->getSession()->check('send_user_email_data')) {
						$userEmailEntity = $this->getRequest()->getSession()->consume('send_user_email_data');
					}
				}

				$templates = $this->UserEmailTemplates->getEmailTemplates($this->UserAuth->getUserId());
				$signatures = $this->UserEmailSignatures->getEmailSignatures($this->UserAuth->getUserId());

				$this->set(compact('userId', 'user', 'templates', 'signatures', 'userEmailEntity'));

				if($confirmRender) {
					$this->render('send_to_user_confirm');
				}
			} else {
				$this->Flash->error(__('Invalid User Id'));
				$this->redirect(['controller'=>'Users', 'action'=>'index']);
			}
		} else {
			$this->Flash->error(__('Missing User Id'));
			$this->redirect(['controller'=>'Users', 'action'=>'index']);
		}
	}
	private function sendAndSaveUserEmail($data, $users) {
		$data['sent_by'] = $this->UserAuth->getUserId();

		if(!empty($data['user_group_id'])) {
			sort($data['user_group_id']);
			$data['user_group_id'] = implode(',', $data['user_group_id']);
		}

		$data['message'] = $data['modified_message'];

		if($this->UserEmails->save($data, ['validate'=>false])) {
			$fromEmailConfig = $data['from_email'];
			$fromNameConfig = $data['from_name'];

			$emailObj = new Mailer('default');
			$emailObj->setFrom([$fromEmailConfig=>$fromNameConfig]);
			$emailObj->setSender([$fromEmailConfig=>$fromNameConfig]);
			$emailObj->setSubject($data['subject']);
			$emailObj->setEmailFormat('both');

			$totalSentEmails = $totalEmails = 0;
			$sentEmails = [];

			$this->loadModel('Usermgmt.UserEmailRecipients');

			foreach($users as $user) {
				if(!isset($sentEmails[$user['email']])) {
					$totalEmails++;

					$emailObj->setTo($user['email']);

					$userEmailRecipient = $this->UserEmailRecipients->newEmptyEntity();

					try{
						$result = $emailObj->deliver($data['message']);
						if($result) {
							$userEmailRecipient['is_email_sent'] = 1;
							$totalSentEmails++;
						}
					} catch (Exception $ex){
					}

					$userEmailRecipient['user_email_id'] = $data['id'];
					$userEmailRecipient['user_id'] = $user['id'];
					$userEmailRecipient['email_address'] = $user['email'];

					$this->UserEmailRecipients->save($userEmailRecipient, ['validate'=>false]);

					$sentEmails[$user['email']] = $user['email'];
				}
			}

			if(!empty($data['cc_to'])) {
				$data['cc_to'] = array_filter(array_map('trim', explode(',', strtolower($data['cc_to']))));

				foreach($data['cc_to'] as $ccEmail) {
					$emailObj->setTo($ccEmail);
					try{
						$emailObj->deliver($data['message']);
					} catch (Exception $ex) {
					}
				}
			}

			if($totalSentEmails) {
				if($totalSentEmails == $totalEmails) {
					$this->Flash->success(__('All Emails have been sent successfully'));
				} else {
					$this->Flash->success(__('Out of {0) Emails only {1} Emails have been sent successfully', [$totalEmails, $totalSentEmails]));
				}
				return true;
			} else {
				$this->Flash->error(__('There is problem in sending emails, please try again'));
				return false;
			}
		} else {
			$this->Flash->error(__('These is some problem in saving data, please try again'));
			return false;
		}
	}
	private function saveScheduledEmails($data, $users) {
		$data['scheduled_by'] = $this->UserAuth->getUserId();

		if(!empty($data['user_group_id'])) {
			sort($data['user_group_id']);
			$data['user_group_id'] = implode(',', $data['user_group_id']);
		}

		$data['message'] = $data['modified_message'];

		$this->loadModel('Usermgmt.ScheduledEmails');

		if($this->ScheduledEmails->save($data, ['validate'=>false])) {
			$scheduledEmails = [];
			$this->loadModel('Usermgmt.ScheduledEmailRecipients');

			foreach($users as $user) {
				if(!isset($scheduledEmails[$user['email']])) {
					$scheduledEmailRecipient = $this->ScheduledEmailRecipients->newEmptyEntity();

					$scheduledEmailRecipient['scheduled_email_id'] = $data['id'];
					$scheduledEmailRecipient['user_id'] = $user['id'];
					$scheduledEmailRecipient['email_address'] = $user['email'];

					$this->ScheduledEmailRecipients->save($scheduledEmailRecipient, ['validate'=>false]);

					$scheduledEmails[$user['email']] = $user['email'];
				}
			}

			$this->Flash->success(__('Email has been scheduled successfully'));
			return true;
		} else {
			$this->Flash->error(__('These is some problem in saving data, please try again'));
			return false;
		}
	}

	/**
	 * Used to view sent email details and it's recipients
	 *
	 * @access public
	 * @param integer $userEmailId user email id
	 * @return void
	 */
	public function view($userEmailId=null) {
		if(!empty($userEmailId)) {
			$cond = [];
			$cond['UserEmails.id'] = $userEmailId;
			if(!$this->UserAuth->isAdmin()) {
				$cond['UserEmails.sent_by'] = $this->UserAuth->getUserId();
			}

			$userEmail = $this->UserEmails->find()->where($cond)->contain(['Users'])->first();

			if(!empty($userEmail)) {
				if(!empty($userEmail['user_group_id'])) {
					$this->loadModel('Usermgmt.UserGroups');

					$userEmail['group_name'] = $this->UserGroups->getGroupsByIds($userEmail['user_group_id']);
				}

				$this->loadModel('Usermgmt.UserEmailRecipients');

				$userEmail['total_sent_emails'] = $this->UserEmailRecipients->find()->where(['UserEmailRecipients.user_email_id'=>$userEmail['id'], 'UserEmailRecipients.is_email_sent'=>1])->count();

				$userEmailRecipients = $this->UserEmailRecipients->find()->where(['UserEmailRecipients.user_email_id'=>$userEmailId])->contain(['Users'])->enableHydration(false)->toArray();

				$this->set(compact('userEmail', 'userEmailRecipients'));
			} else {
				$this->Flash->error(__('Invalid User Email Id'));
				$this->redirect(['action'=>'index']);
			}
		} else {
			$this->Flash->error(__('Missing User Email Id'));
			$this->redirect(['action'=>'index']);
		}
	}
}
