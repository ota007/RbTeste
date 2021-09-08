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

class UserContactsController extends UsermgmtAppController {
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
			'Usermgmt.UserContacts'=>[
				'UserContacts'=>[
					'type'=>'text',
					'label'=>'Search',
					'tagline'=>'Search by name, phone, email, requirement, message',
					'condition'=>'multiple',
					'searchFields'=>['UserContacts.name', 'UserContacts.phone', 'UserContacts.email', 'UserContacts.requirement', 'UserContacts.reply_message'],
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
		$this->loadComponent('Usermgmt.EmailHandler');
	}

	/**
	 * Called before the controller action. You can use this method to configure and customize components
	 * or perform logic that needs to happen before each controller action.
	 *
	 * @return void
	 */
	public function beforeFilter(EventInterface $event) {
		parent::beforeFilter($event);

		$this->loadModel('Usermgmt.UserContacts');

		$action = $this->getRequest()->getParam('action');

		if(isset($this->FormProtection) && $this->getRequest()->is('ajax')) {
			$this->FormProtection->setConfig('unlockedActions', [$action]);
		}
	}

	/**
	 * It displays all contacts enquiries
	 *
	 * @access public
	 * @return void
	 */
	public function index() {
		$cond = [];
		$cond = $this->Search->applySearch($cond);

		$this->paginate = ['limit'=>10, 'conditions'=>$cond, 'order'=>['UserContacts.id'=>'DESC']];

		$userContacts = $this->paginate($this->UserContacts)->toArray();

		$this->set(compact('userContacts'));

		if($this->getRequest()->is('ajax')) {
			$this->viewBuilder()->setLayout('ajax');
			$this->render('/UserContacts/all_user_contacts');
		}
	}

	/**
	 * It is used to show contact enquiry form
	 *
	 * @access public
	 * @return void
	 */
	public function contactUs() {
		$userId = $this->UserAuth->getUserId();
		$user = [];

		$this->loadModel('Usermgmt.Users');

		if($userId) {
			$user = $this->Users->getUserById($userId);
		}

		$userContactEntity = $this->UserContacts->newEmptyEntity();

		if($this->getRequest()->is('post')) {
			$formdata = $this->getRequest()->getData();

			if($this->UserAuth->canUseRecaptha('contactus') && !$this->getRequest()->is('ajax')) {
				$formdata['UserContacts']['captcha']= (isset($formdata['g-recaptcha-response'])) ? $formdata['g-recaptcha-response'] : "";
			}

			$userContactEntity = $this->UserContacts->patchEntity($userContactEntity, $formdata, ['validate'=>'forContact']);

			$errors = $userContactEntity->getErrors();

			if($this->getRequest()->is('ajax')) {
				if(empty($errors)) {
					$response = ['error'=>0, 'message'=>'success'];
				} else {
					$response = ['error'=>1, 'message'=>'failure'];
					$response['data']['UserContacts'] = $errors;
				}
				echo json_encode($response);exit;
			} else {
				if(empty($errors)) {
					$userContactEntity['user_id'] = $userId;

					$this->UserContacts->save($userContactEntity, ['validate'=>false]);

					$this->EmailHandler->sendContactEnquiryToAdmin($userContactEntity);

					$this->Flash->success(__('Thank you for contacting us. we will be in touch with you very soon'));
					$this->redirect('/');
				}
			}
		} else {
			if(!empty($user)) {
				$userContactEntity['name'] = $user['first_name'].' '.$user['last_name'];
				$userContactEntity['email'] = $user['email'];
				$userContactEntity['phone'] = $user['user_detail']['cellphone'];
			}
		}

		$this->set(compact('userContactEntity'));
	}
	/**
	 * It is used to send reply of contact enquiry
	 *
	 * @access public
	 * @param integer $userContactId user contact id
	 * @return void
	 */
	public function sendReply($userContactId=null) {
		if(!empty($userContactId)) {
			$userContact = $this->UserContacts->find()->where(['UserContacts.id'=>$userContactId])->first();

			if(!empty($userContact)) {
				$this->loadModel('Usermgmt.UserEmails');
				$this->loadModel('Usermgmt.UserEmailTemplates');
				$this->loadModel('Usermgmt.UserEmailSignatures');

				$userEmailEntity = $this->UserEmails->newEmptyEntity();

				$confirmRender = false;

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

								$this->getRequest()->getSession()->write('send_reply_email_data', $userEmailEntity);
								
								$confirmRender = true;
							}
							else if($confirm == 'confirm') {
								$data = $this->getRequest()->getSession()->read('send_reply_email_data');

								$fromEmailConfig = $data['from_email'];
								$fromNameConfig = $data['from_name'];

								$emailObj = new Mailer('default');
								$emailObj->setFrom([$fromEmailConfig=>$fromNameConfig]);
								$emailObj->setSender([$fromEmailConfig=>$fromNameConfig]);
								$emailObj->setSubject($data['subject']);
								$emailObj->setEmailFormat('both');

								$sent = false;
								$emailObj->setTo($data['to']);

								if(!empty($data['cc_to'])) {
									$data['cc_to'] = array_filter(array_map('trim', explode(',', strtolower($data['cc_to']))));
									$emailObj->setCc($data['cc_to']);
								}
								try{
									$result = $emailObj->deliver($data['modified_message']);

									if($result) {
										$sent = true;
									}
								} catch (Exception $ex){

								}

								if($sent) {
									$this->getRequest()->getSession()->delete('send_reply_email_data');

									$msg = $userContact['reply_message'];

									if(empty($msg)) {
										$userContact['reply_message'] = 'Reply On '.date('d M Y').' at '.date('h:i A').'<br/>'.$data['modified_message'];
									} else {
										$userContact['reply_message'] = 'Reply On '.date('d M Y').' at '.date('h:i A').'<br/>'.$data['modified_message'].'<br/><br/>'.$msg;
									}

									$this->UserContacts->save($userContact, ['validate'=>false]);

									$this->Flash->success(__('Contact Reply has been sent successfully'));
									$this->redirect(['action'=>'index', '?'=>['page'=>$this->UserAuth->getPageNumber()]]);
								} else {
									$this->Flash->error(__('We could not send Reply Email'));
									$this->redirect(['action'=>'sendReply', $userContactId]);
								}
							}
						}
					}
				} else {
					$userEmailEntity['from_name'] = EMAIL_FROM_NAME;
					$userEmailEntity['from_email'] = EMAIL_FROM_ADDRESS;
					$userEmailEntity['to'] = $userContact['email'];
					$userEmailEntity['subject'] = 'Re: '.SITE_NAME_FULL;

					$userEmailEntity['message'] = '<br/><p>-------------------------------------------<br/>On '.$this->UserAuth->getFormatDate($userContact['created']).' at '.$this->UserAuth->getFormatTime($userContact['created']).'<br/>'.$userContact['name'].' wrote:</p>'.$userContact['requirement'];

					if($this->getRequest()->getSession()->check('send_reply_email_data')) {
						$userEmailEntity = $this->getRequest()->getSession()->consume('send_reply_email_data');
					}
				}

				$templates = $signatures = [];

				if(!$confirmRender) {
					$templates = $this->UserEmailTemplates->getEmailTemplates($this->UserAuth->getUserId());
					$signatures = $this->UserEmailSignatures->getEmailSignatures($this->UserAuth->getUserId());
				}

				$this->set(compact('userContactId', 'userContact', 'userEmailEntity', 'templates', 'signatures'));

				if($confirmRender) {
					$this->render('send_reply_confirm');
				}
			} else {
				$this->Flash->error(__('Invalid Contact Id'));
				$this->redirect(['action'=>'index']);
			}
		} else {
			$this->Flash->error(__('Missing Contact Id'));
			$this->redirect(['action'=>'index']);
		}
	}
}
