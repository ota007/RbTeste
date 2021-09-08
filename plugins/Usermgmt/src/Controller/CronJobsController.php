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

class CronJobsController extends UsermgmtAppController {
	/**
	 * Called before the controller action. You can use this method to configure and customize components
	 * or perform logic that needs to happen before each controller action.
	 *
	 * @return void
	 */
	public function beforeFilter(EventInterface $event) {
		parent::beforeFilter($event);
	}

	/**
	 * It is used to send emails by cron job
	 *
	 * @access public
	 * @return void
	 */
	public function sendScheduledEmails() {
		$this->loadModel('Usermgmt.ScheduledEmails');
		$this->loadModel('Usermgmt.ScheduledEmailRecipients');
		$this->loadModel('Usermgmt.UserEmails');
		$this->loadModel('Usermgmt.UserEmailRecipients');

		$date = date('Y-m-d H:i:s');

		$scheduledEmails = $this->ScheduledEmails
								->find()
								->where(['ScheduledEmails.is_sent'=>0, 'ScheduledEmails.schedule_date <='=>$date])
								->order(['ScheduledEmails.created'=>'ASC'])
								->all();

		$ecount = $rcount = 0;

		if(!empty($scheduledEmails)) {
			ini_set('memory_limit', '256M');
			ini_set('max_execution_time', '5200');

			foreach($scheduledEmails as $row) {
				if(empty($row['user_email_id'])) {
					$userEmail = $this->UserEmails->newEmptyEntity();

					$userEmail['type'] = $row['type'];
					$userEmail['user_group_id'] = $row['user_group_id'];
					$userEmail['cc_to'] = $row['cc_to'];
					$userEmail['from_name'] = $row['from_name'];
					$userEmail['from_email'] = $row['from_email'];
					$userEmail['subject'] = $row['subject'];
					$userEmail['message'] = $row['message'];
					$userEmail['sent_by'] = $row['scheduled_by'];

					if($this->UserEmails->save($userEmail, ['validate'=>false])) {
						$row['user_email_id'] = $userEmail['id'];

						$this->ScheduledEmails->save($row, ['validate'=>false]);
					} else {
						continue;
					}
				}

				while(1) {
					// we are fetching recipients one by one to get with latest status. It will avoid duplicate email issue from multiple cron job executions.

					$recipient = $this->ScheduledEmailRecipients
									->find()
									->where(['ScheduledEmailRecipients.scheduled_email_id'=>$row['id'], 'ScheduledEmailRecipients.is_email_sent'=>0])
									->order(['ScheduledEmailRecipients.id'=>'ASC'])
									->first();

					if(!empty($recipient)) {
						$this->sendAndSaveEmail($row, $recipient);
						$rcount++;
					} else {
						break;
					}
				}

				if(!empty($row['cc_to'])) {
					$row['cc_to'] = array_filter(array_map('trim', explode(',', strtolower($row['cc_to']))));

					foreach($row['cc_to'] as $ccEmail) {
						$this->sendAndSaveEmail($row, ['email_address'=>$ccEmail]);
					}
				}

				if(!$this->ScheduledEmailRecipients->find()->where(['ScheduledEmailRecipients.scheduled_email_id'=>$row['id'], 'ScheduledEmailRecipients.is_email_sent'=>0])->count()) {
					$row['is_sent'] = 1;
					unset($row['modified']);

					$this->ScheduledEmails->save($row, ['validate'=>false]);
				}

				$ecount++;
			}
		}

		echo __('{0} Scheduled Emails and {1} recipients processed', $ecount, $rcount);
		exit;
	}
	private function sendAndSaveEmail($data, $recipient) {
		$fromEmailConfig = $data['from_email'];
		$fromNameConfig = $data['from_name'];

		$emailObj = new Mailer('default');
		$emailObj->setFrom([$fromEmailConfig=>$fromNameConfig]);
		$emailObj->setSender([$fromEmailConfig=>$fromNameConfig]);
		$emailObj->setSubject($data['subject']);
		$emailObj->setEmailFormat('both');
		$emailObj->setTo($recipient['email_address']);

		$userEmailRecipient = $this->UserEmailRecipients->newEmptyEntity();
		$userEmailRecipient['is_email_sent'] = 0;

		try {
			$result = $emailObj->deliver($data['message']);
			if($result) {
				$userEmailRecipient['is_email_sent'] = 1;
			}
		} catch (Exception $ex){
		}

		if(!empty($recipient['id']) && $userEmailRecipient['is_email_sent']) {
			$userEmailRecipient['user_email_id'] = $data['user_email_id'];
			$userEmailRecipient['user_id'] = $recipient['user_id'];
			$userEmailRecipient['email_address'] = $recipient['email_address'];

			$this->UserEmailRecipients->save($userEmailRecipient, ['validate'=>false]);

			$recipient['is_email_sent'] = 1;
			unset($recipient['modified']);

			$this->ScheduledEmailRecipients->save($recipient, ['validate'=>false]);
		}
	}
}
