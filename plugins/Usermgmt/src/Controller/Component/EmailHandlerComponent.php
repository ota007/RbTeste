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

namespace Usermgmt\Controller\Component;

use Cake\Controller\Component;
use Cake\Datasource\FactoryLocator;
use Cake\Mailer\Mailer;
use Cake\Routing\Router;
use Cake\Event\EventInterface;
use Cake\Controller\ComponentRegistry;

class EmailHandlerComponent extends Component {

	public $components = ['Usermgmt.UserAuth'];

	public $registry;
	public $controller;
	public $request;
	public $response;
	public $session;

	public function __construct(ComponentRegistry $registry, array $config = []) {
		$this->registry = $registry;

		parent::__construct($registry, $config);
	}

	public function beforeFilter(EventInterface $event) {
		$this->controller = $this->getController();
		$this->request = $this->controller->getRequest();
		$this->response = $this->controller->getResponse();
		$this->session = $this->request->getSession();
	}

	/**
	 * Used to send change password email to user
	 *
	 * @access public
	 * @param array $userEntity user details
	 * @return void
	 */
	public function sendChangePasswordEmail($userEntity) {
		$fromEmailConfig = EMAIL_FROM_ADDRESS;
		$fromNameConfig = EMAIL_FROM_NAME;

		$emailObj = new Mailer('default');
		$emailObj->setEmailFormat('both');
		$emailObj->setFrom([$fromEmailConfig=>$fromNameConfig]);
		$emailObj->setSender([$fromEmailConfig=>$fromNameConfig]);
		$emailObj->setTo($userEntity['email']);

		$datetime = date('Y M d h:i:s', time());

		$subject = SITE_NAME_FULL.': '.__('Change Password Confirmation');

		$message = __('Hey {0},<br/><br/>You recently changed your password on {1}.<br/><br/>As a security precaution, this notification has been sent to your email address associated with your account.<br/><br/>Thanks,<br/>{2}', [$userEntity['first_name'], $datetime, SITE_NAME_FULL]);

		$emailObj->setSubject($subject);

		$is_email_sent = 0;

		try{
			$emailObj->deliver($message);
			$is_email_sent = 1;
		} catch (Exception $ex){
		}

		$this->UserEmails = FactoryLocator::get('Table')->get('Usermgmt.UserEmails');
		$this->UserEmailRecipients = FactoryLocator::get('Table')->get('Usermgmt.UserEmailRecipients');

		$userEmailEntity = $this->UserEmails->newEmptyEntity();

		$userEmailEntity['type'] = 'CHANGE_PASSWORD_CONFIRMATION';
		$userEmailEntity['from_name'] = $fromNameConfig;
		$userEmailEntity['from_email'] = $fromEmailConfig;
		$userEmailEntity['subject'] = $subject;
		$userEmailEntity['message'] = $message;
		$userEmailEntity['sent_by'] = $this->UserAuth->getUserId();

		$this->UserEmails->save($userEmailEntity, ['validate'=>false]);

		$userEmailRecipientEntity = $this->UserEmailRecipients->newEmptyEntity();

		$userEmailRecipientEntity['user_email_id'] = $userEmailEntity['id'];
		$userEmailRecipientEntity['user_id'] = $userEntity['id'];
		$userEmailRecipientEntity['email_address'] = $userEntity['email'];
		$userEmailRecipientEntity['is_email_sent'] = $is_email_sent;

		$this->UserEmailRecipients->save($userEmailRecipientEntity, ['validate'=>false]);
	}

	/**
	 * Used to send forgot password email to user
	 *
	 * @access public
	 * @param array $userEntity user details
	 * @return void
	 */
	public function sendForgotPasswordEmail($userEntity) {
		$fromEmailConfig = EMAIL_FROM_ADDRESS;
		$fromNameConfig = EMAIL_FROM_NAME;

		$emailObj = new Mailer('default');
		$emailObj->setEmailFormat('both');
		$emailObj->setFrom([$fromEmailConfig=>$fromNameConfig]);
		$emailObj->setSender([$fromEmailConfig=>$fromNameConfig]);
		$emailObj->setTo($userEntity['email']);

		$activate_key = $this->UserAuth->getActivationKey($userEntity['email'].$userEntity['password']);
		$link = Router::url("/activatePassword?ident=".$userEntity['id']."&activate=".$activate_key, true);
		$link = '<a href="'.$link.'">'.$link.'</a>';

		$subject = SITE_NAME_FULL.': '.__('Request to Reset Your Password');

		$message = __('Welcome {0},<br/><br/>You have requested to have your password reset on {1}. Please click the link below to reset your password now: <br/><br/>{2}<br/><br/>If clicking on the link doesn\'t work, try copying and pasting it into your browser.<br/><br/>Thanks,<br/>{3}', [$userEntity['first_name'], SITE_NAME_FULL, $link, SITE_NAME_FULL]);

		$emailObj->setSubject($subject);

		$is_email_sent = 0;

		try{
			$emailObj->deliver($message);
			$is_email_sent = 1;
		} catch (Exception $ex){
		}

		$this->UserEmails = FactoryLocator::get('Table')->get('Usermgmt.UserEmails');
		$this->UserEmailRecipients = FactoryLocator::get('Table')->get('Usermgmt.UserEmailRecipients');

		$userEmailEntity = $this->UserEmails->newEmptyEntity();

		$userEmailEntity['type'] = 'FORGOT_PASSWORD';
		$userEmailEntity['from_name'] = $fromNameConfig;
		$userEmailEntity['from_email'] = $fromEmailConfig;
		$userEmailEntity['subject'] = $subject;
		$userEmailEntity['message'] = $message;
		$userEmailEntity['sent_by'] = $this->UserAuth->getUserId();

		$this->UserEmails->save($userEmailEntity, ['validate'=>false]);

		$userEmailRecipientEntity = $this->UserEmailRecipients->newEmptyEntity();

		$userEmailRecipientEntity['user_email_id'] = $userEmailEntity['id'];
		$userEmailRecipientEntity['user_id'] = $userEntity['id'];
		$userEmailRecipientEntity['email_address'] = $userEntity['email'];
		$userEmailRecipientEntity['is_email_sent'] = $is_email_sent;

		$this->UserEmailRecipients->save($userEmailRecipientEntity, ['validate'=>false]);
	}

	/**
	 * Used to send email verification email to user
	 *
	 * @access public
	 * @param array $userEntity user details
	 * @return void
	 */
	public function sendVerificationEmail($userEntity) {
		$fromEmailConfig = EMAIL_FROM_ADDRESS;
		$fromNameConfig = EMAIL_FROM_NAME;

		$emailObj = new Mailer('default');
		$emailObj->setEmailFormat('both');
		$emailObj->setFrom([$fromEmailConfig=>$fromNameConfig]);
		$emailObj->setSender([$fromEmailConfig=>$fromNameConfig]);
		$emailObj->setTo($userEntity['email']);

		$activate_key = $this->UserAuth->getActivationKey($userEntity['email'].$userEntity['password']);
		$link = Router::url("/userVerification?ident=".$userEntity['id']."&activate=".$activate_key, true);
		$link = '<a href="'.$link.'">'.$link.'</a>';

		$subject = SITE_NAME_FULL.': '.__('Contact Email Confirmation');

		$message = __('Hey {0}, <br/><br/>You recently entered a contact email address. To confirm your contact email, follow the link below: <br/><br/>{1}<br/><br/>If clicking on the link doesn\'t work, try copying and pasting it into your browser.<br/><br/>Thanks,<br/>{2}', [$userEntity['first_name'], $link, SITE_NAME_FULL]);

		$emailObj->setSubject($subject);

		$is_email_sent = 0;

		try{
			$emailObj->deliver($message);
			$is_email_sent = 1;
		} catch (Exception $ex){
		}

		$this->UserEmails = FactoryLocator::get('Table')->get('Usermgmt.UserEmails');
		$this->UserEmailRecipients = FactoryLocator::get('Table')->get('Usermgmt.UserEmailRecipients');

		$userEmailEntity = $this->UserEmails->newEmptyEntity();

		$userEmailEntity['type'] = 'EMAIL_VERIFICATION';
		$userEmailEntity['from_name'] = $fromNameConfig;
		$userEmailEntity['from_email'] = $fromEmailConfig;
		$userEmailEntity['subject'] = $subject;
		$userEmailEntity['message'] = $message;
		$userEmailEntity['sent_by'] = $this->UserAuth->getUserId();

		$this->UserEmails->save($userEmailEntity, ['validate'=>false]);

		$userEmailRecipientEntity = $this->UserEmailRecipients->newEmptyEntity();

		$userEmailRecipientEntity['user_email_id'] = $userEmailEntity['id'];
		$userEmailRecipientEntity['user_id'] = $userEntity['id'];
		$userEmailRecipientEntity['email_address'] = $userEntity['email'];
		$userEmailRecipientEntity['is_email_sent'] = $is_email_sent;

		$this->UserEmailRecipients->save($userEmailRecipientEntity, ['validate'=>false]);
	}

	/**
	 * Used to send registration email to newly created user
	 *
	 * @access public
	 * @param array $userEntity user details
	 * @return void
	 */
	public function sendRegistrationEmail($userEntity) {
		$fromEmailConfig = EMAIL_FROM_ADDRESS;
		$fromNameConfig = EMAIL_FROM_NAME;

		$emailObj = new Mailer('default');
		$emailObj->setEmailFormat('both');
		$emailObj->setFrom([$fromEmailConfig=>$fromNameConfig]);
		$emailObj->setSender([$fromEmailConfig=>$fromNameConfig]);
		$emailObj->setTo($userEntity['email']);

		$subject = SITE_NAME_FULL.': '.__('Registration is Complete');

		$message = __('Welcome {0},<br/><br/>Thank you for your registration on {1}.<br/><br/>Thanks,<br/>{2}',[$userEntity['first_name'], SITE_URL, SITE_NAME_FULL]);

		$emailObj->setSubject($subject);

		$is_email_sent = 0;

		try{
			$emailObj->deliver($message);
			$is_email_sent = 1;
		} catch (Exception $ex){
		}

		$this->UserEmails = FactoryLocator::get('Table')->get('Usermgmt.UserEmails');
		$this->UserEmailRecipients = FactoryLocator::get('Table')->get('Usermgmt.UserEmailRecipients');

		$userEmailEntity = $this->UserEmails->newEmptyEntity();

		$userEmailEntity['type'] = 'USER_REGISTRATION';
		$userEmailEntity['from_name'] = $fromNameConfig;
		$userEmailEntity['from_email'] = $fromEmailConfig;
		$userEmailEntity['subject'] = $subject;
		$userEmailEntity['message'] = $message;
		$userEmailEntity['sent_by'] = $this->UserAuth->getUserId();

		$this->UserEmails->save($userEmailEntity, ['validate'=>false]);

		$userEmailRecipientEntity = $this->UserEmailRecipients->newEmptyEntity();

		$userEmailRecipientEntity['user_email_id'] = $userEmailEntity['id'];
		$userEmailRecipientEntity['user_id'] = $userEntity['id'];
		$userEmailRecipientEntity['email_address'] = $userEntity['email'];
		$userEmailRecipientEntity['is_email_sent'] = $is_email_sent;

		$this->UserEmailRecipients->save($userEmailRecipientEntity, ['validate'=>false]);
	}

	/**
	 * Used to send contact enquiry to admin
	 *
	 * @access public
	 * @param array $userContactEntity user contact details
	 * @return void
	 */
	public function sendContactEnquiryToAdmin($userContactEntity) {
		if(ADMIN_EMAIL_ADDRESS) {
			$fromEmailConfig = EMAIL_FROM_ADDRESS;
			$fromNameConfig = EMAIL_FROM_NAME;

			$emailObj = new Mailer('default');
			$emailObj->setEmailFormat('both');
			$emailObj->setFrom([$fromEmailConfig=>$fromNameConfig]);
			$emailObj->setSender([$fromEmailConfig=>$fromNameConfig]);
			$emailObj->setTo(ADMIN_EMAIL_ADDRESS);
			$emailObj->setSubject(__('Contact Enquiry Received'));

			$message = __('Hi Admin, <br/><br/>A contact enquiry has been saved. Here are the details - <br/><br/>Name- {0} <br/>Email - {1} <br/>Contact No - {2} <br/>Requirement - {3} <br/><br/>Thanks', [$userContactEntity['name'], $userContactEntity['email'], $userContactEntity['phone'], nl2br($userContactEntity['requirement'])]);

			try{
				$emailObj->deliver($message);
			} catch (Exception $ex) {
			}
		}
	}
}
