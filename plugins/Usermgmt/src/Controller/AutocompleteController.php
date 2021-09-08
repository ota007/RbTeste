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
use Cake\Utility\Security;

class AutocompleteController extends UsermgmtAppController {
	/**
	 * Called before the controller action. You can use this method to configure and customize components
	 * or perform logic that needs to happen before each controller action.
	 *
	 * @return void
	 */
	public function beforeFilter(EventInterface $event) {
		parent::beforeFilter($event);

		$action = $this->getRequest()->getParam('action');

		if(isset($this->FormProtection) && $this->getRequest()->is('ajax')) {
			$this->FormProtection->setConfig('unlockedActions', [$action]);
		}
	}

	/**
	 * It displays search suggestions on search filter form
	 *
	 * @access public
	 * @param string $model model name to identify table
	 * @param string $field model field name to identify table column name
	 * @return json
	 */
	public function fetch($model, $field) {
		$resultToPrint = $results = $cond = [];

		if($this->getRequest()->is('ajax')) {
			$field = $this->UserAuth->decryptField($field);

			if($field != 'unknown') {
				if(isset($_GET['term'])) {
					$query = trim($_GET['term']);
					$queryParts = explode(' ',  strval($query));

					foreach($queryParts as $queryPart) {
						$queryPart = trim($queryPart);

						if(strlen($queryPart)) {
							$cond['OR'][] = $model."." . $field . " LIKE '%" . $queryPart . "%'";
						}
					}

					if(!empty($cond)) {
						$this->loadModel($model);

						$results = $this->$model->find()
									->select([$model.".".$field])->distinct([$model.".".$field])
									->where($cond)
									->enableHydration(false)
									->toArray();
					}
				}

				foreach($results as $res) {
					$resultToPrint[] = ['name'=>(string)$res[$field]];
				}
			}
		}

		echo json_encode($resultToPrint);
		exit;
	}

	/**
	 * It displays search suggestions on all users index page
	 *
	 * @access public
	 * @return json
	 */
	public function userIndexSearch() {
		$resultToPrint = $results = $usernames = $names = $emails = [];

		if($this->getRequest()->is('ajax')) {
			if(isset($_GET['term'])) {
				$query = $_GET['term'];

				$this->loadModel('Usermgmt.Users');

				$results = $this->Users->find()
							->select(['Users.username', 'Users.first_name', 'Users.last_name', 'Users.email'])
							->where(['OR'=>[['Users.username LIKE'=>$query.'%'], ['Users.first_name LIKE'=>$query.'%'], ['Users.last_name LIKE'=>$query.'%'], ['Users.email LIKE'=>'%'.$query.'%@%'], ['Users.email LIKE'=>'%'.$query.'%']]])
							->enableHydration(false)
							->toArray();
			}

			foreach($results as $res) {
				if(stripos($res['first_name'], $query) !== false || stripos($res['last_name'], $query) !== false) {
					$names[] = $res['first_name'].' '.$res['last_name'];
				}

				if(stripos($res['email'], $query) !== false) {
					$emails[] = $res['email'];
				}

				if(stripos($res['username'], $query) !== false) {
					$usernames[] = $res['username'];
				}
			}

			$names = array_unique($names);
			$emails = array_unique($emails);
			$usernames = array_unique($usernames);

			$res = array_merge($usernames, $names, $emails);

			foreach($res as $row) {
				$resultToPrint[] = ['name'=>$row];
			}
		}

		echo json_encode($resultToPrint);
		exit;
	}

	/**
	 * It is used to search emails on send email page
	 *
	 * @access public
	 * @return json
	 */
	public function searchEmails() {
		$results = $resultToPrint = [];
		$query = '';

		if($this->getRequest()->is('ajax')) {
			if(isset($_POST['term'])) {
				$query = $_POST['term'];

				$this->loadModel('Usermgmt.Users');

				$results = $this->Users->find()->select(['Users.id', 'Users.first_name', 'Users.last_name', 'Users.email'])->where(['OR'=>[['Users.first_name LIKE'=>$query.'%'], ['Users.last_name LIKE'=>$query.'%'], ['Users.email LIKE'=>$query.'%@%'], ['Users.email LIKE'=>$query.'%']], 'Users.email IS NOT NULL', 'Users.email !='=>'', 'Users.is_active'=>1]);

				foreach($results as $res) {
					$resultToPrint[] = ['id'=>$res['id'], 'text'=>$res['first_name'].' '.$res['last_name'].' ( '.$res['email'].' )'];
				}
			}
		}

		echo json_encode(['q'=>$query, 'results'=>$resultToPrint]);
		exit;
	}
}
