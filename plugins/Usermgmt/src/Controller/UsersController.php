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
use Cake\I18n\Time;

class UsersController extends UsermgmtAppController {
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
			'Usermgmt.Users'=>[
				'Users'=>[
					'type'=>'text',
					'label'=>'Search',
					'tagline'=>'Search by name, username, email',
					'condition'=>'multiple',
					'searchFields'=>['Users.first_name', 'Users.last_name', 'Users.username', 'Users.email'],
					'searchFunc'=>['plugin'=>'Usermgmt', 'controller'=>'Autocomplete', 'function'=>'userIndexSearch'],
					'inputOptions'=>['style'=>'width:200px;']
				],
				'Users.id'=>[
					'type'=>'text',
					'condition'=>'=',
					'label'=>'User Id',
					'inputOptions'=>['style'=>'width:50px;'],
					'searchSuggestion'=>true
				],
				'Users.user_group_id'=>[
					'type'=>'select',
					'condition'=>'comma',
					'label'=>'Group',
					'model'=>'Usermgmt.UserGroups',
					'selector'=>'getUserGroups'
				],
				'Users.is_email_verified'=>[
					'type'=>'select',
					'label'=>'Email Verified',
					'options'=>[''=>'Select', '0'=>'No', '1'=>'Yes']
				],
				'Users.is_active'=>[
					'type'=>'select',
					'label'=>'Status',
					'options'=>[''=>'Select', '1'=>'Active', '0'=>'Inactive']
				],
				'Users.created1'=>[
					'type'=>'text',
					'condition'=>'>=',
					'label'=>'From',
					'searchField'=>'created',
					'inputOptions'=>['style'=>'width:120px;', 'class'=>'datepicker']
				],
				'Users.created2'=>[
					'type'=>'text',
					'condition'=>'<=',
					'label'=>'To',
					'searchField'=>'created',
					'inputOptions'=>['style'=>'width:120px;', 'class'=>'datepicker']
				]
			]
		],
		'online'=>[
			'Usermgmt.UserActivities'=>[
				'UserActivities'=>[
					'type'=>'text',
					'label'=>'Search',
					'tagline'=>'Search by name, email, ip address',
					'condition'=>'multiple',
					'searchFields'=>['Users.first_name', 'Users.last_name', 'Users.email', 'UserActivities.ip_address'],
					'inputOptions'=>['style'=>'width:200px;']
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
		$this->loadComponent('Usermgmt.UserConnect');
		$this->loadComponent('Usermgmt.EmailHandler');
		$this->loadComponent('Usermgmt.ControllerList');
	}

	/**
	 * Called before the controller action. You can use this method to configure and customize components
	 * or perform logic that needs to happen before each controller action.
	 *
	 * @return void
	 */
	public function beforeFilter(EventInterface $event) {
		parent::beforeFilter($event);

		$this->loadModel('Usermgmt.Users');
		$this->Users->UserAuth = $this->UserAuth;
		$this->Users->ControllerList = $this->ControllerList;

		if(isset($this->Auth)) {
			$this->Auth->allow(['login', 'logout', 'register', 'userVerification', 'forgotPassword', 'activatePassword', 'accessDenied', 'emailVerification']);
		}

		$action = $this->getRequest()->getParam('action');

		if(isset($this->FormProtection) && ($this->getRequest()->is('ajax') || $action == 'login' || $action == 'uploadCsv' || $action == 'addMultipleUsers')) {
			$this->FormProtection->setConfig('unlockedActions', [$action]);
		}
	}

	/**
	 * It displays dashboard for logged in user
	 *
	 * @access public
	 * @return void
	 */
	public function dashboard() {
		/* Do here something for user */
	}

	/**
	 * It displays all userss
	 *
	 * @access public
	 * @return void
	 */
	public function index() {
		$this->loadModel('Usermgmt.UserGroups');

		$cond = [];
		$cond = $this->Search->applySearch($cond);

		$this->paginate = ['limit'=>10, 'conditions'=>$cond, 'order'=>['Users.id'=>'DESC']];

		$users = $this->paginate($this->Users)->toArray();

		foreach($users as $key=>$user) {
			$users[$key]['user_group_name'] = $this->UserGroups->getGroupsByIds($user['user_group_id']);
		}

		$this->set(compact('users'));

		if($this->getRequest()->is('ajax')) {
			$this->viewBuilder()->setLayout('ajax');
			$this->render('/Users/all_users');
		}
	}

	/**
	 * It displays all online users with in specified time
	 *
	 * @access public
	 * @return void
	 */
	public function online() {
		$this->loadModel('Usermgmt.UserActivities');

		$cond = [];
		$cond['UserActivities.modified >'] = date('Y-m-d H:i:s', strtotime('-'.VIEW_ONLINE_USER_TIME.' minutes', time()));
		$cond['UserActivities.is_logout'] = 0;

		$cond = $this->Search->applySearch($cond);

		$this->paginate = ['limit'=>10, 'order'=>['UserActivities.last_action'=>'DESC'], 'conditions'=>$cond, 'contain'=>['Users']];

		$users = $this->paginate($this->UserActivities)->toArray();

		$this->set(compact('users'));

		if($this->getRequest()->is('ajax')) {
			$this->viewBuilder()->setLayout('ajax');
			$this->render('/Users/all_online_users');
		}
	}

	/**
	 * It is used to add user
	 *
	 * @access public
	 * @return void
	 */
	public function addUser() {
		$this->loadModel('Usermgmt.UserDetails');
		$this->loadModel('Usermgmt.UserGroups');

		$userEntity = $this->Users->newEmptyEntity();

		if($this->getRequest()->is('post')) {
			$formdata = $this->getRequest()->getData();

			if(is_array($formdata['Users']['user_group_id'])) {
				sort($formdata['Users']['user_group_id']);
				$formdata['Users']['user_group_id'] = implode(',', $formdata['Users']['user_group_id']);
			}

			$userEntity = $this->Users->patchEntity($userEntity, $formdata, ['validate'=>'forAddUser', 'associated'=>['UserDetails'=>['validate'=>'forAddUser']]]);

			$errors = $userEntity->getErrors();

			if($this->getRequest()->is('ajax')) {
				if(empty($errors)) {
					$response = ['error'=>0, 'message'=>'success'];
				} else {
					$response = ['error'=>1, 'message'=>'failure'];
					$response['data']['Users'] = $errors;
				}
				echo json_encode($response);exit;
			} else {
				if(empty($errors)) {
					$userEntity['is_active'] = 1;
					$userEntity['is_email_verified'] = 1;
					$userEntity['created_by'] = $this->UserAuth->getUserId();
					$userEntity['password'] = $this->UserAuth->makeHashedPassword($userEntity['password']);

					if($this->Users->save($userEntity, ['validate'=>false])) {
						if(!isset($userEntity['user_detail']['id'])) {
							$userDetailEntity = $this->UserDetails->newEmptyEntity();
							$userDetailEntity['user_id'] =$userEntity['id'];

							$this->UserDetails->save($userDetailEntity, ['validate'=>false]);
						}

						$this->Flash->success(__('The user has been added successfully'));
						$this->redirect(['action'=>'index']);
					} else {
						$this->Flash->error(__('Unable to save user, please try again'));
					}
				}
			}
		}

		$userGroups = $this->UserGroups->getUserGroups(false);

		$this->set(compact('userGroups', 'userEntity'));
	}

	/**
	 * It is used to edit user
	 *
	 * @access public
	 * @param integer $userId user id
	 * @return void
	 */
	public function editUser($userId=null) {
		if($userId) {
			$userEntity = $this->Users->getUserById($userId);

			if(!empty($userEntity)) {
				$this->loadModel('Usermgmt.UserDetails');

				if($this->getRequest()->is(['post', 'put'])) {
					$formdata = $this->getRequest()->getData();

					if(is_array($formdata['Users']['user_group_id'])) {
						sort($formdata['Users']['user_group_id']);
						$formdata['Users']['user_group_id'] = implode(',', $formdata['Users']['user_group_id']);
					}

					if(!empty($formdata['Users']['bday'])) {
						$formdata['Users']['bday'] = date('Y-m-d', strtotime($formdata['Users']['bday']));
					}

					$old_user_group_id = $userEntity['user_group_id'];
					$existing_photo = $userEntity['photo'];

					$userEntity = $this->Users->patchEntity($userEntity, $formdata, ['validate'=>'forEditUser', 'associated'=>['UserDetails'=>['validate'=>'forEditUser']]]);

					$errors = $userEntity->getErrors();

					if($this->getRequest()->is('ajax')) {
						if(empty($errors)) {
							$response = ['error'=>0, 'message'=>'success'];
						} else {
							$response = ['error'=>1, 'message'=>'failure'];
							$response['data']['Users'] = $errors;
						}
						echo json_encode($response);exit;
					} else {
						if(empty($errors)) {
							if(!empty($formdata['Users']['photo_file'])) {
								$fileObject = $formdata['Users']['photo_file'];

								if(!$fileObject->getError()) {
									$filename = $fileObject->getClientFilename();

									$path_info = pathinfo($filename);
									$userEntity['photo'] = time().mt_rand().".".$path_info['extension'];

									$fullpath = WWW_ROOT."library".DS.IMG_DIR;
									if(!is_dir($fullpath)) {
										mkdir($fullpath, 0777, true);
									}

									$fileObject->moveTo($fullpath.DS.$userEntity['photo']);

									if(!empty($existing_photo) && file_exists($fullpath.DS.$existing_photo)) {
										@unlink($fullpath.DS.$existing_photo);
									}
								}
							}

							if($old_user_group_id != $userEntity['user_group_id']) {
								$this->loadModel('Usermgmt.UserActivities');

								$this->UserActivities->updateAll(['is_logout'=>1], ['user_id'=>$userId]);
							}

							$userEntity['modified_by'] = $this->UserAuth->getUserId();

							if($this->Users->save($userEntity, ['validate'=>false])) {
								$this->Flash->success(__('The user has been updated successfully'));

								$this->redirect(['action'=>'index', '?'=>['page'=>$this->UserAuth->getPageNumber()]]);
							} else {
								$this->Flash->error(__('Unable to save user, please try again'));
							}
						}
					}
				} else {
					if(!empty($userEntity['bday'])) {
						$userEntity['bday'] = $this->UserAuth->getFormatDate($userEntity['bday']);
					}

					if(!empty($userEntity['user_group_id'])) {
						$userEntity['user_group_id'] = explode(',', $userEntity['user_group_id']);
					}
				}
			} else {
				$this->Flash->error(__('Invalid user id'));
				$this->redirect(['action'=>'index']);
			}
		} else {
			$this->Flash->error(__('Missing user id'));
			$this->redirect(['action'=>'index']);
		}

		$this->loadModel('Usermgmt.UserGroups');

		$userGroups = $this->UserGroups->getUserGroups(false);
		$genders = $this->Users->getGenders(false);

		$this->set(compact('userGroups', 'userEntity', 'genders'));
	}

	/**
	 * It displays user's full details
	 *
	 * @access public
	 * @param integer $userId user id
	 * @return void
	 */
	public function viewUser($userId=null) {
		if($userId) {
			$user = $this->Users->getUserById($userId);

			if(!empty($user)) {
				$this->loadModel('Usermgmt.UserGroups');

				$user['group_name'] = $this->UserGroups->getGroupsByIds($user['user_group_id']);
				$user['created_by'] = $this->Users->getNameById($user['created_by']);

				$this->set(compact('userId', 'user'));
			} else {
				$this->Flash->error(__('Invalid user id'));
				$this->redirect(['action'=>'index']);
			}
		} else {
			$this->Flash->error(__('Missing user id'));
			$this->redirect(['action'=>'index']);
		}
	}

	/**
	 * It is used to delete user
	 *
	 * @access public
	 * @param integer $userId user id of user
	 * @return void
	 */
	public function deleteUser($userId=null) {
		if(!empty($userId)) {
			$user = $this->Users->find()->where(['Users.id'=>$userId])->first();

			if(!empty($user)) {
				if($this->getRequest()->is('post')) {
					if($this->Users->delete($user)) {
						$this->loadModel('Usermgmt.UserDetails');
						$this->loadModel('Usermgmt.LoginTokens');
						$this->loadModel('Usermgmt.UserActivities');
						$this->loadModel('Usermgmt.UserSocials');

						$this->UserDetails->deleteAll(['user_id'=>$userId]);
						$this->LoginTokens->deleteAll(['user_id'=>$userId]);
						$this->UserSocials->deleteAll(['user_id'=>$userId]);
						$this->UserActivities->updateAll(['is_deleted'=>1], ['user_id'=>$userId]);

						$this->Flash->success(__('Selected user is deleted successfully'));
					} else {
						$this->Flash->error(__('Unable to delete user, please try again'));
					}
				}
			} else {
				$this->Flash->error(__('Invalid User Id'));
			}
		} else {
			$this->Flash->error(__('Missing User Id'));
		}

		$this->redirect(['action'=>'index', '?'=>['page'=>$this->UserAuth->getPageNumber()]]);
	}

	/**
	 * It is used to activate user
	 *
	 * @access public
	 * @param integer $userId user id
	 * @return void
	 */
	public function setActive($userId=null) {
		if(!empty($userId)) {
			$user = $this->Users->find()->where(['Users.id'=>$userId])->first();

			if(!empty($user)) {
				if($this->getRequest()->is('post')) {
					$userEntity = $this->Users->newEmptyEntity();
					$userEntity['id'] = $userId;
					$userEntity['is_active'] = 1;

					$this->Users->save($userEntity, ['validate'=>false]);

					$this->loadModel('Usermgmt.UserActivities');

					$this->UserActivities->updateAll(['is_logout'=>0], ['user_id'=>$userId]);

					$this->Flash->success(__('Selected user is activated successfully'));
				}
			} else {
				$this->Flash->error(__('Invalid User Id'));
			}
		} else {
			$this->Flash->error(__('Missing User Id'));
		}

		$this->redirect(['action'=>'index', '?'=>['page'=>$this->UserAuth->getPageNumber()]]);
	}

	/**
	 * It is used to inactivate user
	 *
	 * @access public
	 * @param integer $userId user id
	 * @return void
	 */
	public function setInactive($userId=null) {
		if(!empty($userId)) {
			$user = $this->Users->find()->where(['Users.id'=>$userId])->first();

			if(!empty($user)) {
				if($this->getRequest()->is('post')) {
					$userEntity = $this->Users->newEmptyEntity();
					$userEntity['id'] = $userId;
					$userEntity['is_active'] = 0;

					$this->Users->save($userEntity, ['validate'=>false]);

					$this->loadModel('Usermgmt.UserActivities');

					$this->UserActivities->updateAll(['is_logout'=>1], ['user_id'=>$userId]);

					$this->Flash->success(__('Selected user is de-activated successfully'));
				}
			} else {
				$this->Flash->error(__('Invalid User Id'));
			}
		} else {
			$this->Flash->error(__('Missing User Id'));
		}

		if(!empty($_GET['return']) && $_GET['return'] == 'online_users') {
			$this->redirect(['action'=>'online', '?'=>['page'=>$this->UserAuth->getPageNumber()]]);
		} else {
			$this->redirect(['action'=>'index', '?'=>['page'=>$this->UserAuth->getPageNumber()]]);
		}
	}

	/**
	 * It is used to mark verified email of user from all users page
	 *
	 * @access public
	 * @param integer $userId user id of user
	 * @return void
	 */
	public function verifyEmail($userId=null) {
		if(!empty($userId)) {
			$user = $this->Users->find()->where(['Users.id'=>$userId])->first();

			if($user) {
				if($this->getRequest()->is('post')) {
					$userEntity = $this->Users->newEmptyEntity();
					$userEntity['id'] = $userId;
					$userEntity['is_email_verified'] = 1;

					$this->Users->save($userEntity, ['validate'=>false]);

					$this->Flash->success(__('Email of selected user is marked as verified successfully'));
					$this->redirect(['action'=>'index', '?'=>['page'=>$this->UserAuth->getPageNumber()]]);
				}
			} else {
				$this->Flash->error(__('Invaid User Id'));
				$this->redirect(['action'=>'index']);
			}
		} else {
			$this->Flash->error(__('Missing User Id'));
			$this->redirect(['action'=>'index']);
		}
	}

	/**
	 * It is used to change password of user by admin
	 *
	 * @access public
	 * @param integer $userId user id
	 * @return void
	 */
	public function changeUserPassword($userId=null) {
		if(!empty($userId)) {
			$user = $this->Users->find()->where(['Users.id'=>$userId])->first();

			if(!empty($user)) {
				$userEntity = $this->Users->newEmptyEntity();

				if($this->getRequest()->is('post')) {
					$userEntity = $this->Users->patchEntity($userEntity, $this->getRequest()->getData(), ['validate'=>'forChangeUserPassword']);

					$errors = $userEntity->getErrors();

					if($this->getRequest()->is('ajax')) {
						if(empty($errors)) {
							$response = ['error'=>0, 'message'=>'success'];
						} else {
							$response = ['error'=>1, 'message'=>'failure'];
							$response['data']['Users'] = $errors;
						}
						echo json_encode($response);exit;
					} else {
						if(empty($errors)) {
							$userEntity['id'] = $userId;
							$userEntity['password'] = $this->UserAuth->makeHashedPassword($userEntity['password']);

							$this->Users->save($userEntity, ['validate'=>false]);

							$this->loadModel('Usermgmt.LoginTokens');
							$this->loadModel('Usermgmt.UserActivities');

							$this->LoginTokens->deleteAll(['user_id'=>$userId]);
							$this->UserActivities->updateAll(['is_logout'=>1], ['user_id'=>$userId]);

							$this->Flash->success(__('Password for {0} changed successfully', [$user['first_name'].' '.$user['last_name']]));
							$this->redirect(['action'=>'index', '?'=>['page'=>$this->UserAuth->getPageNumber()]]);
						}
					}
				}

				$this->set(compact('userEntity', 'user'));
			} else {
				$this->Flash->error(__('Invalid User Id'));
				$this->redirect(['action'=>'index']);
			}
		} else {
			$this->Flash->error(__('Missing User Id'));
			$this->redirect(['action'=>'index']);
		}
	}

	/**
	 * It is used to logout user by Admin from online users page
	 *
	 * @access public
	 * @param integer $userId user id
	 * @return void
	 */
	public function logoutUser($userId=null) {
		if(!empty($userId)) {
			$user = $this->Users->find()->where(['Users.id'=>$userId])->first();

			if(!empty($user)) {
				if($this->getRequest()->is('post')) {
					$this->loadModel('Usermgmt.UserActivities');

					$this->UserActivities->updateAll(['is_logout'=>1], ['user_id'=>$userId]);

					$this->Flash->success(__('User is successfully signed out'));
				}
			} else {
				$this->Flash->error(__('Invalid User Id'));
			}
		} else {
			$this->Flash->error(__('Missing User Id'));
		}

		$this->redirect(['action'=>'online', '?'=>['page'=>$this->UserAuth->getPageNumber()]]);
	}

	/**
	 * It is used to upload csv file to add multiple users
	 *
	 * @access public
	 * @return void
	 */
	public function uploadCsv() {
		if($this->getRequest()->is('post')) {
			$formdata = $this->getRequest()->getData();

			if(!empty($formdata['csv_file'])) {
				$fileObject = $formdata['csv_file'];

				if(!$fileObject->getError()) {
					$filename = $fileObject->getClientFilename();

					$path_info = pathinfo($filename);
					if(strtolower($path_info['extension']) == 'csv') {
						$filename = time().".".$path_info['extension'];

						$fullpath = WWW_ROOT."files".DS."csv_users";
						if(!is_dir($fullpath)) {
							mkdir($fullpath, 0777, true);
						}

						$fileObject->moveTo($fullpath.DS.$filename);

						$this->redirect(['action'=>'addMultipleUsers', $filename]);
					} else {
						$this->Flash->warning(__('Please upload CSV file only'));
					}
				} else {
					$this->Flash->error(__('Please upload valid CSV file'));
				}
			} else {
				$this->Flash->error(__('Please upload CSV file'));
			}
		}

		$this->loadModel('Usermgmt.UserGroups');

		$userGroups = $this->UserGroups->getUserGroups(false);
		$genders = $this->Users->getGenders(false);

		$this->set(compact('userGroups', 'genders'));
	}

	/**
	 * It is used to add multiple users by Admin
	 *
	 * @access public
	 * @param string $csv_file csv file name
	 * @return void
	 */
	public function addMultipleUsers($csv_file=null) {
		if($csv_file) {
			$fullpath = WWW_ROOT."files".DS."csv_users";

			if(file_exists($fullpath.DS.$csv_file)) {
				$userEntities = $this->Users->newEntities([]);
				$users = [];

				if($this->getRequest()->is('post')) {
					$formdata = $this->getRequest()->getData();

					$selectedUsersCount = 0;
					$this->Users->multiUsers = $formdata;

					if(isset($formdata['Users'])) {
						foreach($formdata['Users'] as $key=>$row) {
							if(is_array($row['user_group_id'])) {
								sort($row['user_group_id']);
								$formdata['Users'][$key]['user_group_id'] = implode(',', $row['user_group_id']);
							}

							if(isset($row['usercheck']) && $row['usercheck']) {
								$selectedUsersCount++;
							}
						}
					}

					if($selectedUsersCount > 0) {
						$userEntities = $this->Users->patchEntities($userEntities, $formdata['Users'], ['validate'=>'forMultipleUsers', 'associated'=>['UserDetails'=>['validate'=>'forMultipleUsers']]]);
					}

					$errors = [];
					foreach($userEntities as $key=>$userEntity) {
						$userError = $userEntity->getErrors();

						if(isset($userEntity['usercheck']) && $userEntity['usercheck']) {
							if(!empty($userError)) {
								$errors[$key] = $userError;
							}
						} else {
							unset($userEntities[$key]);
						}
					}

					if($this->getRequest()->is('ajax')) {
						if(empty($errors)) {
							$response = ['error'=>0, 'message'=>'success'];
						} else {
							$response = ['error'=>1, 'message'=>'failure'];
							foreach($errors as $key=>$val) {
								foreach($val as $k=>$v) {
									$response['data']['Users'][$key.'_'.$k] = $v;
								}
							}
						}
						echo json_encode($response);exit;
					} else {
						if($selectedUsersCount > 0) {
							if(empty($errors)) {
								foreach($userEntities as $key=>$row) {
									$userEntity = $row;
									$userEntity['password'] = $this->UserAuth->makeHashedPassword($row['password']);
									$userEntity['is_active'] = 1;
									$userEntity['is_email_verified'] = 1;
									$userEntity['created_by'] = $this->UserAuth->getUserId();

									$this->Users->save($userEntity, ['validate'=>false, 'associated'=>['UserDetails'=>['validate'=>false]]]);
								}

								$this->Flash->success(__('All users information have been saved'));
								$this->redirect(['action'=>'index']);
							}
						} else {
							$this->Flash->warning(__('Please select at least one user to add'));
						}

						foreach($userEntities as $key=>$row) {
							if(!is_array($row['user_group_id'])) {
								$userEntities[$key]['user_group_id'] = explode(',',  strval($row['user_group_id']));
							}
						}

						$users = $userEntities;
					}
				}
				else {
					$i = $j = 0;
					$dbfields = $dbvalues = [];

					if(($handle = fopen($fullpath.DS.$csv_file, "r")) !== false) {
						while(($data = fgetcsv($handle, 1000, ",")) !== false) {
							if($i == 0) {
								if(!empty($data[0])) {
									$dbfields = $data;

									foreach($data as $key=>$val) {
										$val = trim($val);

										if(in_array($val, ['user_group_id', 'first_name', 'last_name', 'username', 'email', 'password', 'gender', 'bday'])) {
											$dbvalues[$key] = null;
										}
										else if(in_array($val, ['location', 'cellphone'])) {
											$dbvalues[$key] = 'user_detail';
										}
									}
								}
							}
							else {
								if(!empty($dbfields)) {
									foreach($data as $key=>$val) {
										$val = trim($val);

										if($dbfields[$key] == 'bday') {
											$val = date('Y-m-d', strtotime($val));
										}

										if($dbfields[$key] == 'user_group_id') {
											$val = explode(',', $val);
										}

										if(is_null($dbvalues[$key])) {
											$users[$j][$dbfields[$key]] = $val;
										}
										else {
											$users[$j][$dbvalues[$key]][$dbfields[$key]] = $val;
										}

										$users[$j]['usercheck'] = 1;
									}

									$j++;
								}
							}

							$i++;
						}

						fclose($handle);
					}

					if(!empty($users)) {
						$userEntities = $this->Users->patchEntities($userEntities, $users);

						foreach($users as $i=>$user) {
							foreach($user as $field=>$value) {
								$userEntities[$i][$field] = $value;
							}
						}
					} else {
						$this->Flash->info(__('Invalid or empty data in CSV file, please try again'));
						$this->redirect(['action'=>'uploadCsv']);
					}
				}

				$this->loadModel('Usermgmt.UserGroups');

				$userGroups = $this->UserGroups->getUserGroups();
				$genders = $this->Users->getGenders();

				$this->set(compact('userGroups', 'genders', 'userEntities', 'users'));
			} else {
				$this->Flash->info(__('CSV file was not uploaded or does not exist, please try again'));
				$this->redirect(['action'=>'uploadCsv']);
			}
		} else {
			$this->redirect(['action'=>'uploadCsv']);
		}
	}

	/**
	 * It displays access denied page if user wants to view the page without permission
	 *
	 * @access public
	 * @return void
	 */
	public function accessDenied() {

	}

	/**
	 * It is used to login user
	 *
	 * @access public
	 * @param string $connect social connect name fb, twt, ldn, gmail
	 * @return void
	 */
	public function login($connect=null) {
		if($this->UserAuth->isLogged()) {
			if($connect) {
				$this->render('popup');
			} else {
				$this->redirect(['action'=>'dashboard']);
			}
		}

		if($connect == 'fb') {
			$this->login_facebook();
			$this->render('popup');
		}
		else if($connect == 'twt') {
			$this->login_twitter();
			$this->render('popup');
		}
		else if($connect == 'gmail') {
			$this->login_gmail();
			$this->render('popup');
		}
		else if($connect == 'ldn') {
			$this->login_linkedin();
			$this->render('popup');
		}
		else {
			$userEntity = $this->Users->newEmptyEntity();

			if($this->getRequest()->is('post')) {
				$formdata = $this->getRequest()->getData();

				if($this->UserAuth->canUseRecaptha('login') && !$this->getRequest()->is('ajax')) {
					$formdata['Users']['captcha'] = (isset($formdata['g-recaptcha-response'])) ? $formdata['g-recaptcha-response'] : "";
				}

				$userEntity = $this->Users->patchEntity($userEntity, $formdata, ['validate'=>'forLogin']);

				$errors = $userEntity->getErrors();

				$errorMsg = "";
				$loginValid = false;
				$user = [];

				if(empty($errors)) {
					if(!empty($userEntity['email']) && !empty($userEntity['password'])) {
						$email = $userEntity['email'];
						$password = $userEntity['password'];

						$user = $this->Users->findByUsernameOrEmail($email, $email)->contain('UserDetails')->first();

						if(!empty($user) && $this->UserAuth->checkPassword($password, $user['password'])) {
							if($user['is_active']) {
								if($user['is_email_verified']) {
									$loginValid = true;
								} else {
									$errorMsg = __('Your email has not been confirmed please verify your email or contact to support', true);
								}
							} else {
								$errorMsg = __('Sorry your account is not active, please contact to support', true);
							}
						} else {
							$this->UserAuth->setBadLoginCount();
							$errorMsg = __('Incorrect Email/Username or Password', true);
						}
					} else {
						$errorMsg = __('Something went wrong, please try again', true);
					}
				}

				if($this->getRequest()->is('ajax')) {
					if(empty($errors) && $loginValid) {
						$response = ['error'=>0, 'message'=>'success'];
					} else {
						$response = ['error'=>1, 'message'=>'failure'];
						if(empty($errorMsg)) {
							$response['data']['Users'] = $errors;
						} else {
							if($this->UserAuth->captchaOnBadLogin()) {
								// need to submit login for captcha validation
								$response = ['error'=>0, 'message'=>'success'];
							} else {
								$response['data']['Users'] = ['email'=>[$errorMsg]];
							}
						}
					}

					echo json_encode($response);exit;
				}
				else {
					if(empty($errors) && $loginValid) {
						$user = $user->toArray();
						$this->UserAuth->login($user);

						if(!empty($userEntity['remember'])) {
							$this->UserAuth->persist('2 weeks');

							//this enables auto login by cookie saved on user browser. If you want to change validation on auto login, please check getUserByCookieToken function in users model table.
						}

						$this->redirect($this->Auth->redirectUrl());
					} else {
						if(!empty($errorMsg)) {
							$this->Flash->error($errorMsg);
						}
						$userEntity['password'] = '';
					}
				}
			}

			$this->set(compact('userEntity'));
		}
	}

	private function add_social_user($socialData) {
		$userForLogin = [];

		if(SITE_REGISTRATION) {
			$userEntity = $this->Users->newEmptyEntity();
			$userEntity['user_group_id'] = DEFAULT_GROUP_ID;

			if(!empty($socialData['username'])) {
				$userEntity['username'] = $this->UserAuth->generateUserName($socialData['username']);
			} else {
				$userEntity['username'] = $this->UserAuth->generateUserName($socialData['name']);
			}

			$password = $this->UserAuth->generatePassword();
			$userEntity['password'] = $this->UserAuth->makeHashedPassword($password);

			$userEntity['first_name'] = $socialData['first_name'];
			$userEntity['last_name'] = $socialData['last_name'];
			$userEntity['gender'] = $socialData['gender'];
			$userEntity['is_active'] = 1;

			if(!empty($socialData['email'])) {
				$userEntity['email'] = $socialData['email'];
				$userEntity['is_email_verified'] = 1;
			}

			$userEntity['ip_address'] = $this->getRequest()->clientIp();

			if(!empty($socialData['picture'])) {
				$userEntity['photo'] = $this->UserAuth->updateProfilePic($socialData['picture']);
			}

			if($this->Users->save($userEntity, ['validate'=>false])) {
				$userId = $userEntity['id'];

				if(!isset($userEntity['user_detail']['id'])) {
					$this->loadModel('Usermgmt.UserDetails');

					$userDetailEntity = $this->UserDetails->newEmptyEntity();
					$userDetailEntity['user_id'] = $userId;
					$userDetailEntity['location'] = $socialData['location'];

					$this->UserDetails->save($userDetailEntity, ['validate'=>false]);
				}

				if(!empty($socialData['id']) && !empty($socialData['type'])) {
					$this->loadModel('Usermgmt.UserSocials');

					$this->UserSocials->add_social_account($socialData, $userId);
				}

				$userForLogin = $this->Users->getUserById($userId);

				if(CHANGE_PASSWORD_ON_SOCIAL_REGISTRATION) {
					$this->getRequest()->getSession()->write('Auth.SocialChangePassword', true);
				}
			}
		}
		else {
			$this->Flash->info(__('Sorry new registration is currently disabled, please try again later'));
		}

		return $userForLogin;
	}

	private function login_social_user($userForLogin, $socialData) {
		if(!empty($userForLogin)) {
			if($userForLogin['is_active']) {
				$photoPath = WWW_ROOT."library".DS.IMG_DIR.DS.$userForLogin['photo'];

				if((empty($userForLogin['photo']) || !file_exists($photoPath)) && !empty($socialData['picture'])) {
					$userForLogin['photo'] = $this->UserAuth->updateProfilePic($socialData['picture']);
				}

				$this->Users->save($userForLogin, ['validate'=>false]);

				$userForLogin = $userForLogin->toArray();
				$changePassword = $this->getRequest()->getSession()->read('Auth.SocialChangePassword');

				$this->UserAuth->login($userForLogin);

				if(!empty($changePassword)) {
					$this->getRequest()->getSession()->write('Auth.SocialLogin', true);
					$this->getRequest()->getSession()->write('Auth.SocialChangePassword', true);
				}
			} else {
				$this->Flash->info(__('Sorry your account is not active, please contact to support'));
			}
		}
	}

	private function login_facebook() {
		$this->viewBuilder()->disableAutoLayout();

		$fbData = $this->UserConnect->facebook_connect();

		if(!empty($fbData['redirectURL'])) {
			$this->redirect($fbData['redirectURL']);
		}
		else {
			if(!empty($fbData['id'])) {
				$this->loadModel('Usermgmt.UserSocials');

				$fbData['type'] = 'FACEBOOK';
				$userByFbId = $userByFbEmail = $userForLogin = [];

				$userSocial = $this->UserSocials->find()->where(['UserSocials.type'=>$fbData['type'], 'UserSocials.socialid'=>$fbData['id']])->first();

				if(!empty($fbData['email'])) {
					$userByFbEmail = $this->Users->getUserByEmail($fbData['email']);
				}

				if(!empty($userSocial)) {
					$userSocial['access_token'] = $fbData['access_token'];

					$this->UserSocials->save($userSocial, ['validate'=>false]);

					$userByFbId = $this->Users->getUserById($userSocial['user_id']);
				}
				else if(!empty($userByFbEmail)) {
					$this->UserSocials->add_social_account($fbData, $userByFbEmail['id']);
				}

				if(!empty($userByFbId) && !empty($userByFbEmail)) {
					$userForLogin = $userByFbId;
				}
				else if(!empty($userByFbId)) {
					if(empty($userByFbId['email']) && !empty($fbData['email'])) {
						$userByFbId['email'] = $fbData['email'];
					}

					$userForLogin = $userByFbId;
				}
				else if(!empty($userByFbEmail)) {
					$userForLogin = $userByFbEmail;
				}
				else {
					$userForLogin = $this->add_social_user($fbData);
				}

				$this->login_social_user($userForLogin, $fbData);
			} else {
				if(!empty($fbData['error']) && Configure::read('debug')) {
					$this->Flash->error($fbData['error'].' UserConnectComponent line number '.$fbData['line_number']);
				} else {
					$this->Flash->error(__('Something went wrong, please try again or use other login options'));
				}
			}
		}
	}

	private function login_twitter() {
		$this->viewBuilder()->disableAutoLayout();

		$twtData = $this->UserConnect->twitter_connect();

		if(!empty($twtData['redirectURL'])) {
			$this->redirect($twtData['redirectURL']);
		}
		else {
			if(!empty($twtData['id'])) {
				$this->loadModel('Usermgmt.UserSocials');

				$twtData['type'] = 'TWITTER';
				$userByTwtId = $userByTwtEmail = $userForLogin = [];

				$userSocial = $this->UserSocials->find()->where(['UserSocials.type'=>$twtData['type'], 'UserSocials.socialid'=>$twtData['id']])->first();

				if(!empty($twtData['email'])) {
					$userByTwtEmail = $this->Users->getUserByEmail($twtData['email']);
				}

				if(!empty($userSocial)) {
					$userSocial['access_token'] = $twtData['access_token'];
					$userSocial['access_secret'] = $twtData['access_secret'];

					$this->UserSocials->save($userSocial, ['validate'=>false]);

					$userByTwtId = $this->Users->getUserById($userSocial['user_id']);
				}
				else if(!empty($userByTwtEmail)) {
					$this->UserSocials->add_social_account($twtData, $userByTwtEmail['id']);
				}

				if(!empty($userByTwtId) && !empty($userByTwtEmail)) {
					$userForLogin = $userByTwtId;
				}
				else if(!empty($userByTwtId)) {
					if(empty($userByTwtId['email']) && !empty($twtData['email'])) {
						$userByTwtId['email'] = $twtData['email'];
					}

					$userForLogin = $userByTwtId;
				}
				else if(!empty($userByTwtEmail)) {
					$userForLogin = $userByTwtEmail;
				}
				else {
					$userForLogin = $this->add_social_user($twtData);
				}

				$this->login_social_user($userForLogin, $twtData);
			}
		}
	}

	private function login_linkedin() {
		$this->viewBuilder()->disableAutoLayout();

		$ldnData = $this->UserConnect->linkedin_connect();

		if(!empty($ldnData['redirectURL'])) {
			$this->redirect($ldnData['redirectURL']);
		}
		else {
			if(!empty($ldnData['id'])) {
				$this->loadModel('Usermgmt.UserSocials');

				$ldnData['type'] = 'LINKEDIN';
				$userByLdnId = $userByLdnEmail = $userForLogin = [];

				$userSocial = $this->UserSocials->find()->where(['UserSocials.type'=>$ldnData['type'], 'UserSocials.socialid'=>$ldnData['id']])->first();

				if(!empty($ldnData['email'])) {
					$userByLdnEmail = $this->Users->getUserByEmail($ldnData['email']);
				}

				if(!empty($userSocial)) {
					$userSocial['access_token'] = $ldnData['access_token'];

					$this->UserSocials->save($userSocial, ['validate'=>false]);

					$userByLdnId = $this->Users->getUserById($userSocial['user_id']);
				}
				else if(!empty($userByLdnEmail)) {
					$this->UserSocials->add_social_account($ldnData, $userByLdnEmail['id']);
				}

				if(!empty($userByLdnId) && !empty($userByLdnEmail)) {
					$userForLogin = $userByLdnId;
				}
				else if(!empty($userByLdnId)) {
					if(empty($userByLdnId['email']) && !empty($ldnData['email'])) {
						$userByLdnId['email'] = $ldnData['email'];
					}
					$userForLogin = $userByLdnId;
				}
				else if(!empty($userByLdnEmail)) {
					$userForLogin = $userByLdnEmail;
				}
				else {
					$userForLogin = $this->add_social_user($ldnData);
				}

				$this->login_social_user($userForLogin, $ldnData);
			}
		}
	}

	private function login_gmail() {
		$this->viewBuilder()->disableAutoLayout();

		$gmailData = $this->UserConnect->gmail_connect();

		if(!empty($gmailData['redirectURL'])) {
			$this->redirect($gmailData['redirectURL']);
		}
		else {
			if(!empty($gmailData['email'])) {
				$userForLogin = $this->Users->getUserByEmail($gmailData['email']);

				if(empty($userForLogin)) {
					$userForLogin = $this->add_social_user($gmailData);
				}

				$this->login_social_user($userForLogin, $gmailData);
			} else {
				if(!empty($gmailData['error']) && Configure::read('debug')) {
					$this->Flash->error($gmailData['error'].' UserConnectComponent line number '.$gmailData['line_number']);
				} else {
					$this->Flash->error(__('Something went wrong, please try again or use other login options'));
				}
			}
		}
	}

	/**
	 * It is used to logout user from the site
	 *
	 * @access public
	 * @param boolean $msg true for flash message on logout
	 * @return void
	 */
	public function logout($msg=true) {
		$this->UserAuth->logout();

		if($msg) {
			$this->Flash->success(__('You are successfully signed out'));
		}
	}

	/**
	 * It is used to register a user
	 *
	 * @access public
	 * @return void
	 */
	public function register() {
		$userId = $this->UserAuth->getUserId();

		if($userId) {
			$this->redirect(['action'=>'dashboard']);
		}

		if(SITE_REGISTRATION) {
			$this->loadModel('Usermgmt.UserGroups');

			$userGroups = $this->UserGroups->getGroupsForRegistration();

			$userEntity = $this->Users->newEmptyEntity();

			if($this->getRequest()->is('post')) {
				$formdata = $this->getRequest()->getData();

				if($this->getRequest()->is('post') && $this->UserAuth->canUseRecaptha('registration') && !$this->getRequest()->is('ajax')) {
					$formdata['Users']['captcha'] = (isset($formdata['g-recaptcha-response'])) ? $formdata['g-recaptcha-response'] : "";
				}

				$userEntity = $this->Users->patchEntity($userEntity, $formdata, ['validate'=>'forRegister']);

				$errors = $userEntity->getErrors();

				if($this->getRequest()->is('ajax')) {
					if(empty($errors)) {
						$response = ['error'=>0, 'message'=>'success'];
					} else {
						$response = ['error'=>1, 'message'=>'failure'];
						$response['data']['Users'] = $errors;
					}
					echo json_encode($response);exit;
				} else {
					if(empty($errors)) {
						if(!isset($formdata['Users']['user_group_id'])) {
							$userEntity['user_group_id'] = DEFAULT_GROUP_ID;
						}
						else if(!isset($userGroups[$formdata['Users']['user_group_id']])) {
							$userEntity['user_group_id'] = DEFAULT_GROUP_ID;
						}

						if(!EMAIL_VERIFICATION) {
							$userEntity['is_email_verified'] = 1;
						}

						$userEntity['is_active'] = 1;
						$userEntity['ip_address'] = $this->getRequest()->clientIp();
						$userEntity['password'] = $this->UserAuth->makeHashedPassword($userEntity['password']);

						if($this->Users->save($userEntity, ['validate'=>false])) {
							$userId = $userEntity['id'];

							$this->loadModel('Usermgmt.UserDetails');

							$userDetailEntity = $this->UserDetails->newEmptyEntity();
							$userDetailEntity['user_id'] = $userId;

							$this->UserDetails->save($userDetailEntity, ['validate'=>false]);

							if(EMAIL_VERIFICATION) {
								$this->EmailHandler->sendVerificationEmail($userEntity);
							}

							if(SEND_REGISTRATION_MAIL) {
								$this->EmailHandler->sendRegistrationEmail($userEntity);
							}

							if(isset($userEntity['is_active']) && $userEntity['is_active'] && !EMAIL_VERIFICATION) {
								$user = $this->Users->getUserById($userId);
								$user = $user->toArray();

								$this->UserAuth->login($user);
								$this->redirect($this->Auth->redirectUrl());
							}
							else {
								$this->Flash->success(__('Your account has been created. You should receive an e-mail shortly to authenticate your account. Once validated you will be able to login.'));
								$this->redirect(['action'=>'login']);
							}
						} else {
							$this->Flash->error(__('Unable to register user, please try again'));
						}
					}
				}
			}

			$this->set(compact('userGroups', 'userEntity'));
		} else {
			$this->Flash->info(__('Sorry new registration is currently disabled, please try again later'));
			$this->redirect(['action'=>'login']);
		}
	}

	/**
	 * It displays loggedin users profile details
	 *
	 * @access public
	 * @return void
	 */
	public function myprofile() {
		$userId = $this->UserAuth->getUserId();
		$user = $this->Users->getUserById($userId);

		if(!empty($user)) {
			$this->loadModel('Usermgmt.UserGroups');

			$user['user_group_name'] = $this->UserGroups->getGroupsByIds($user['user_group_id']);

			$this->set(compact('user'));
		} else {
			$this->Flash->info(__('Profile details not found'));
			$this->redirect(['action'=>'dashboard']);
		}
	}

	/**
	 * It is used to edit personal profile by user
	 *
	 * @access public
	 * @return void
	 */
	public function editProfile() {
		$userId = $this->UserAuth->getUserId();

		if(!empty($userId)) {
			$userEntity = $this->Users->getUserById($userId);

			if(!empty($userEntity)) {
				$this->loadModel('Usermgmt.UserDetails');

				if($this->getRequest()->is(['post', 'put'])) {
					$existing_email = $userEntity['email'];
					$existing_photo = $userEntity['photo'];

					$formdata = $this->getRequest()->getData();

					if(!ALLOW_CHANGE_USERNAME && !empty($userEntity['username'])) {
						unset($formdata['Users']['username']);
					}

					if(!empty($formdata['Users']['bday'])) {
						$formdata['Users']['bday'] = date('Y-m-d', strtotime($formdata['Users']['bday']));
					}

					$userEntity = $this->Users->patchEntity($userEntity, $formdata, ['validate'=>'forEditProfile', 'associated'=>['UserDetails'=>['validate'=>'forEditProfile']]]);

					$errors = $userEntity->getErrors();

					if($this->getRequest()->is('ajax')) {
						if(empty($errors)) {
							$response = ['error'=>0, 'message'=>'success'];
						} else {
							$response = ['error'=>1, 'message'=>'failure'];
							$response['data']['Users'] = $errors;
						}
						echo json_encode($response);exit;
					} else {
						if(empty($errors)) {
							if(!empty($formdata['Users']['photo_file'])) {
								$fileObject = $formdata['Users']['photo_file'];

								if(!$fileObject->getError()) {
									$filename = $fileObject->getClientFilename();

									$path_info = pathinfo($filename);
									$userEntity['photo'] = time().mt_rand().".".$path_info['extension'];

									$fullpath = WWW_ROOT."library".DS.IMG_DIR;
									if(!is_dir($fullpath)) {
										mkdir($fullpath, 0777, true);
									}

									$fileObject->moveTo($fullpath.DS.$userEntity['photo']);

									if(!empty($existing_photo) && file_exists($fullpath.DS.$existing_photo)) {
										@unlink($fullpath.DS.$existing_photo);
									}
								}
							}

							if(!$this->UserAuth->isAdmin() && $existing_email != $userEntity['email']) {
								$userEntity['is_email_verified'] = 0;
							}

							unset($userEntity['user_group_id']);

							if(empty($userEntity['ip_address'])) {
								$userEntity['ip_address'] = $this->getRequest()->clientIp();
							}

							$this->Users->save($userEntity, ['validate'=>false]);

							if(isset($userEntity['is_email_verified']) && !$userEntity['is_email_verified']) {
								$this->loadModel('Usermgmt.LoginTokens');

								$this->LoginTokens->deleteAll(['user_id'=>$userId]);

								$userEntity = $this->Users->find()->where(['Users.id'=>$userId])->first();

								$this->EmailHandler->sendVerificationEmail($userEntity);
							}

							$this->Flash->success(__('Your profile has been successfully updated'));
							$this->redirect(['action'=>'myprofile']);
						}
					}
				} else {
					if(!empty($userEntity['bday'])) {
						$userEntity['bday'] = $this->UserAuth->getFormatDate($userEntity['bday']);
					}
				}

				$genders = $this->Users->getGenders();

				$this->set(compact('userEntity', 'genders'));
			} else {
				$this->Flash->error(__('Invalid User Id'));
				$this->redirect(['action'=>'myprofile']);
			}
		} else {
			$this->Flash->error(__('Invalid User Id'));
			$this->redirect(['action'=>'myprofile']);
		}
	}

	/**
	 * It is used to change password
	 *
	 * @access public
	 * @return void
	 */
	public function changePassword() {
		$userId = $this->UserAuth->getUserId();

		if($userId) {
			$userEntity = $this->Users->newEmptyEntity();

			if($this->getRequest()->is('post')) {
				$userEntity = $this->Users->patchEntity($userEntity, $this->getRequest()->getData(), ['validate'=>'forChangePassword']);

				$errors = $userEntity->getErrors();

				if(empty($errors)) {
					$userEntity['id'] = $userId;
					$userEntity['password'] = $this->UserAuth->makeHashedPassword($userEntity['password']);

					$this->Users->save($userEntity, ['validate'=>false]);

					$this->loadModel('Usermgmt.LoginTokens');

					$this->LoginTokens->deleteAll(['user_id'=>$userId]);

					if(SEND_PASSWORD_CHANGE_MAIL) {
						$userEntity = $this->Users->find()->where(['Users.id'=>$userId])->first();

						$this->EmailHandler->sendChangePasswordEmail($userEntity);
					}

					$this->getRequest()->getSession()->delete('Auth.SocialChangePassword');

					$this->Flash->success(__('Password changed successfully'));
					$this->redirect(['action'=>'dashboard']);
				}
			}

			$this->set(compact('userEntity'));
		} else {
			$this->Flash->error(__('Invalid User Id'));
			$this->redirect(['action'=>'dashboard']);
		}
	}

	/**
	 * It is used to delete user account by itself If allowed by admin in All settings
	 *
	 * @access public
	 * @return void
	 */
	public function deleteAccount() {
		$userId = $this->UserAuth->getUserId();

		if(!empty($userId)) {
			if($this->getRequest()->is('post')) {
				if(ALLOW_DELETE_ACCOUNT && $userId != 1) {
					$user = $this->Users->find()->where(['Users.id'=>$userId])->first();

					if($this->Users->delete($user)) {
						$this->loadModel('Usermgmt.UserDetails');
						$this->loadModel('Usermgmt.LoginTokens');
						$this->loadModel('Usermgmt.UserActivities');
						$this->loadModel('Usermgmt.UserSocials');

						$this->UserDetails->deleteAll(['user_id'=>$userId]);
						$this->LoginTokens->deleteAll(['user_id'=>$userId]);
						$this->UserSocials->deleteAll(['user_id'=>$userId]);
						$this->UserActivities->updateAll(['is_deleted'=>1], ['user_id'=>$userId]);

						$this->Flash->success(__('Your account is successfully deleted'));
						$this->logout(false);
					}
				} else {
					$this->Flash->info(__('You are not allowed to delete account'));
				}
			}
		}

		$this->redirect(['action'=>'dashboard']);
	}

	/**
	 * It is used to reset password, this function sends email with link to reset the password
	 *
	 * @access public
	 * @return void
	 */
	public function forgotPassword() {
		$userEntity = $this->Users->newEmptyEntity();

		if($this->getRequest()->is('post')) {
			$formdata = $this->getRequest()->getData();

			if($this->UserAuth->canUseRecaptha('forgotPassword') && !$this->getRequest()->is('ajax')) {
				$formdata['Users']['captcha'] = (isset($formdata['g-recaptcha-response'])) ? $formdata['g-recaptcha-response'] : "";
			}

			$userEntity = $this->Users->patchEntity($userEntity, $formdata, ['validate'=>'forForgotPassword']);

			$errors = $userEntity->getErrors();

			if(empty($errors)) {
				$email = $userEntity['email'];
				$user = $this->Users->findByUsernameOrEmail($email, $email)->first();

				if(!empty($user)) {
					if($user['is_email_verified']) {
						$this->EmailHandler->sendForgotPasswordEmail($user);

						$this->Flash->success(__('We have sent an email to you, please click on the link in your email to reset your password'));
					} else {
						$this->Flash->info(__('Your registration has not been confirmed yet please verify your email address before reset password'));
					}

					$this->redirect(['action'=>'login']);
				} else {
					$this->Flash->error(__('Incorrect Email/Username'));
				}
			}
		}

		$this->set(compact('userEntity'));
	}

	/**
	 * It is used to reset password when users clicks the link in their email
	 *
	 * @access public
	 * @return void
	 */
	public function activatePassword() {
		$userEntity = $this->Users->newEmptyEntity();

		if(!empty($_GET['ident']) && !empty($_GET['activate'])) {
			$userId = $_GET['ident'];
			$activateKey = $_GET['activate'];

			$user = $this->Users->find()->where(['Users.id'=>$userId])->first();

			if(!empty($user)) {
				$theKey = $this->UserAuth->getActivationKey($user['email'].$user['password']);

				if($theKey === $activateKey) {
					if($this->getRequest()->is('post')) {
						$userEntity = $this->Users->patchEntity($userEntity, $this->getRequest()->getData(), ['validate'=>'forActivatePassword']);

						$errors = $userEntity->getErrors();

						if(empty($errors)) {
							$userEntity['id'] = $userId;
							$userEntity['password'] = $this->UserAuth->makeHashedPassword($userEntity['password']);

							$this->Users->save($userEntity, ['validate'=>false]);

							$this->Flash->success(__('Your password has been updated successfully'));
							$this->redirect(['action'=>'login']);
						}
					}
				} else {
					$this->Flash->info(__('Something went wrong, please request to reset password again'));
					$this->redirect(['action'=>'login']);
				}
			} else {
				$this->Flash->info(__('Something went wrong, please request to reset password again'));
				$this->redirect(['action'=>'login']);
			}
		} else {
			$this->Flash->info(__('Something went wrong, please click again on reset password link'));
			$this->redirect(['action'=>'login']);
		}

		$this->set(compact('userEntity'));
	}

	/**
	 * It is used to send email verification mail to user with link to verify the email address
	 *
	 * @access public
	 * @return void
	 */
	public function emailVerification() {
		$userEntity = $this->Users->newEmptyEntity();

		if($this->getRequest()->is('post')) {
			$formdata = $this->getRequest()->getData();

			if($this->getRequest()->is('post') && $this->UserAuth->canUseRecaptha('emailVerification') && !$this->getRequest()->is('ajax')) {
				$formdata['Users']['captcha'] = (isset($formdata['g-recaptcha-response'])) ? $formdata['g-recaptcha-response'] : "";
			}

			$userEntity = $this->Users->patchEntity($userEntity, $formdata, ['validate'=>'forEmailVerification']);

			$errors = $userEntity->getErrors();

			if(empty($errors)) {
				$email = $userEntity['email'];
				$user = $this->Users->findByUsernameOrEmail($email, $email)->first();

				if(!empty($user)) {
					if(!$user['is_email_verified']) {
						$this->EmailHandler->sendVerificationEmail($user);

						$this->Flash->success(__('We have sent an email to you, please confirm your email address'));
					} else {
						$this->Flash->success(__('Your email is already verified'));
					}

					$this->redirect(['action'=>'login']);
				} else {
					$this->Flash->error(__('Incorrect Email/Username'));
				}
			}
		}

		$this->set(compact('userEntity'));
	}

	/**
	 * It is used to verify user's email address when user click on the link sent to their email address
	 *
	 * @access public
	 * @return void
	 */
	public function userVerification() {
		if(isset($_GET['ident']) && isset($_GET['activate'])) {
			$userId = $_GET['ident'];
			$activateKey = $_GET['activate'];

			$user = $this->Users->find()->where(['Users.id'=>$userId])->first();

			if(!empty($user)) {
				if(!$user['is_email_verified']) {
					$password = $user['password'];
					$theKey = $this->UserAuth->getActivationKey($user['email'].$password);

					if($activateKey === $theKey) {
						$user['is_email_verified'] = 1;

						$user = $this->Users->patchEntity($user, ['validate'=>false]);

						$this->Users->save($user, ['validate'=>false]);

						$this->Flash->success(__('Thank you, your email has been verified successfully'));
					}
				} else {
					$this->Flash->success(__('Thank you, your email is already verified'));
				}
			} else {
				$this->Flash->info(__('Sorry something went wrong, please click on the link again'));
			}
		} else {
			$this->Flash->info(__('Sorry something went wrong, please click on the link again'));
		}

		$this->redirect(['action'=>'login']);
	}

	/**
	 * It id used to delete cache of cakephp on production
	 *
	 * @access public
	 * @return void
	 */
	public function deleteCache() {
		if(!Configure::read('debug')) {
			Configure::write('debug', true);
		}

		$success = $this->UserAuth->deleteCache(['type'=>'all', 'increase_qrdn'=>true, 'truncate_user_activities_table'=>true]);

		if($success) {
			$this->Flash->success(__('Cache has been deleted successfully'));
			$this->redirect(['action'=>'dashboard']);
		} else {
			echo __('Few cache files were not deleted, please delete them manually.');
			exit;
		}
	}
}
