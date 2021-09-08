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

class UserSettingsController extends UsermgmtAppController {
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
			'Usermgmt.UserSettings'=>[
				'UserSettings.setting_description'=>[
					'type'=>'text',
					'label'=>'Setting Name',
					'searchSuggestion'=>true,
					'inputOptions'=>['style'=>'width:300px;']
				],
				'UserSettings.setting_category'=>[
					'type'=>'select',
					'condition'=>'=',
					'label'=>'Category',
					'model'=>'Usermgmt.UserSettings',
					'selector'=>'getSettingCategories'
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

		$this->loadModel('Usermgmt.UserSettings');

		$action = $this->getRequest()->getParam('action');

		if(isset($this->FormProtection) && ($this->getRequest()->is('ajax') || $action == 'cakelog' || $action == 'editSetting')) {
			$this->FormProtection->setConfig('unlockedActions', [$action]);
		}
	}

	/**
	 * It displays all settings
	 *
	 * @access public
	 * @return void
	 */
	public function index() {
		$cond = [];
		$cond = $this->Search->applySearch($cond);

		$this->paginate = ['limit'=>10, 'conditions'=>$cond, 'order'=>['UserSettings.id'=>'ASC']];

		$userSettings = $this->paginate($this->UserSettings)->toArray();

		$this->loadModel('Usermgmt.SettingOptions');

		foreach($userSettings as $key=>$row) {
			if($row['setting_type'] == 'dropdown' || $row['setting_type'] == 'radio') {
				$userSettings[$key]['setting_value'] = $this->SettingOptions->getTitleById($row['setting_value']);
			}
		}

		$this->set(compact('userSettings'));

		if($this->getRequest()->is('ajax')) {
			$this->viewBuilder()->setLayout('ajax');
			$this->render('/UserSettings/all_user_settings');
		}
	}

	/**
	 * It is used to add setting
	 *
	 * @access public
	 * @return void
	 */
	public function addSetting() {
		$settingEntity = $this->UserSettings->newEmptyEntity();

		if($this->getRequest()->is('post')) {
			$formdata = $this->getRequest()->getData();

			if(isset($formdata['UserSettings']['category_type'])) {
				if($formdata['UserSettings']['category_type'] == 'existing') {
					unset($formdata['UserSettings']['new_category']);
				}
				else if($formdata['UserSettings']['category_type'] == 'new') {
					unset($formdata['UserSettings']['setting_category']);
				}
			}

			$settingEntity = $this->UserSettings->patchEntity($settingEntity, $formdata, ['validate'=>'forAdd']);

			$errors = $settingEntity->getErrors();

			if($this->getRequest()->is('ajax')) {
				if(empty($errors)) {
					$response = ['error'=>0, 'message'=>'success'];
				} else {
					$response = ['error'=>1, 'message'=>'failure'];
					$response['data']['UserSettings'] = $errors;
				}
				echo json_encode($response);exit;
			} else {
				if(empty($errors)) {
					if($settingEntity['category_type'] == 'new') {
						$settingEntity['setting_category'] = $settingEntity['new_category'];
					}

					$settingEntity['setting_key'] = trim(strtolower($settingEntity['setting_key']));

					if($this->UserSettings->save($settingEntity, ['validate'=>false])) {
						$this->Flash->success(__('New setting has been added successfully, please add value for it'));

						$this->redirect(['action'=>'editSetting', $settingEntity['id']]);
					} else {
						$this->Flash->error(__('Unable to add a setting, please try again'));
					}
				}
			}
		}

		$settingCategories = $this->UserSettings->getSettingCategories();
		$settingInputTypes = $this->UserSettings->getSettingInputTypes();

		$this->set(compact('settingCategories', 'settingInputTypes', 'settingEntity'));
	}

	/**
	 * It is used to edit setting value
	 *
	 * @access public
	 * @param integer $userSettingId user setting id
	 * @return void
	 */
	public function editSetting($userSettingId=null) {
		if($userSettingId) {
			$settingEntity = $this->UserSettings->find()
								->where(['UserSettings.id'=>$userSettingId])
								->contain(['UserSettingOptions'])
								->first();

			if(!empty($settingEntity)) {
				$this->loadModel('Usermgmt.UserSettingOptions');
				$this->loadModel('Usermgmt.SettingOptions');

				if($this->getRequest()->is(['put', 'post'])) {
					$formdata = $this->getRequest()->getData();

					if(isset($formdata['UserSettings']['category_type'])) {
						if($formdata['UserSettings']['category_type'] == 'existing') {
							unset($formdata['UserSettings']['new_category']);
						}
						else if($formdata['UserSettings']['category_type'] == 'new') {
							unset($formdata['UserSettings']['setting_category']);
						}

						$selectedType = $formdata['UserSettings']['setting_type'];
						$formdata['UserSettings']['setting_value'] = $formdata['UserSettings'][$selectedType.'_value'];
					}

					$settingEntity = $this->UserSettings->patchEntity($settingEntity, $formdata, ['validate'=>'forAdd']);

					$errors = $settingEntity->getErrors();

					if($this->getRequest()->is('ajax')) {
						if(empty($errors)) {
							$response = ['error'=>0, 'message'=>'success'];
						} else {
							$response = ['error'=>1, 'message'=>'failure'];
							$response['data']['UserSettings'] = $errors;
						}
						echo json_encode($response);exit;
					} else {
						if(empty($errors)) {
							if(!empty($formdata['UserSettings']['options']) && in_array($settingEntity['setting_type'], ['dropdown', 'radio'])) {
								$existingIds = [];

								foreach($settingEntity['user_setting_options'] as $option) {
									$existingIds[$option['setting_option_id']] = $option['id'];
								}

								foreach($formdata['UserSettings']['options'] as $option) {
									$pos = strpos($option, 'newoption-');

									if($pos !== false) {
										$newoption = implode('', explode('newoption-', $option, 2));

										$settingOption = $this->SettingOptions->find()->where(['SettingOptions.title'=>$newoption])->first();

										if(empty($settingOption)) {
											$settingOption = $this->SettingOptions->newEmptyEntity();
											$settingOption['title'] = $newoption;

											$this->SettingOptions->save($settingOption, ['validate'=>false]);
										}

										if($settingEntity['setting_value'] == $option) {
											$settingEntity['setting_value'] = $settingOption['id'];
										}

										$option = $settingOption['id'];
									}

									if(isset($existingIds[$option])) {
										unset($existingIds[$option]);
									}
									else {
										$userSettingOption = $this->UserSettingOptions->newEmptyEntity();

										$userSettingOption['user_setting_id'] = $userSettingId;
										$userSettingOption['setting_option_id'] = $option;

										$this->UserSettingOptions->save($userSettingOption, ['validate'=>false]);
									}
								}

								if(!empty($existingIds)) {
									$this->UserSettingOptions->deleteAll(['user_setting_id'=>$userSettingId, 'id IN'=>$existingIds]);
								}
							}

							if($settingEntity['category_type'] == 'new') {
								$settingEntity['setting_category'] = $settingEntity['new_category'];
							}

							$settingEntity['setting_key'] = trim(strtolower($settingEntity['setting_key']));

							if($this->UserSettings->save($settingEntity, ['validate'=>false])) {
								$this->deleteCache();

								$this->Flash->success(__('Selected setting has been updated successfully'));
								$this->redirect(['action'=>'index', '?'=>['page'=>$this->UserAuth->getPageNumber()]]);
							} else {
								$this->Flash->error(__('Unable to update setting, please try again'));
							}
						}
					}
				} else {
					$settingEntity[$settingEntity['setting_type'].'_value'] = $settingEntity['setting_value'];
				}

				$settingCategories = $this->UserSettings->getSettingCategories();
				$settingInputTypes = $this->UserSettings->getSettingInputTypes();
				$settingOptions = $this->SettingOptions->getSettingOptions(false);
				$userSettingOptions = $this->UserSettingOptions->getUserSettingOptions($userSettingId, false);

				$this->set(compact('userSettingId', 'settingCategories', 'settingInputTypes', 'userSettingOptions', 'settingEntity', 'settingOptions'));
			} else {
				$this->Flash->error(__('Invalid setting id'));
				$this->redirect(['action'=>'index']);
			}
		} else {
			$this->Flash->error(__('Missing setting id'));
			$this->redirect(['action'=>'index']);
		}
	}

	/**
	 * It is used to delete cache of permissions and used when any permission gets changed
	 *
	 * @access private
	 * @return void
	 */
	private function deleteCache() {
		$this->UserAuth->deleteCache(['type'=>'user_settings']);
	}

	/**
	 * It is used to display cake log files
	 *
	 * @access public
	 * @param string $filename file name
	 * @return void
	 */
	public function cakelog($filename=null) {
		$fullpath = LOGS;

		if($this->getRequest()->isPost()) {
			$formdata = $this->getRequest()->getData();

			$fp = fopen($fullpath.$filename, "w");
			fwrite($fp, $formdata['UserSettings']['logfile']);
			fclose($fp);

			$this->Flash->success(__('{0} has been modified successfully', [$filename]));
			$this->redirect(['action'=>'cakelog']);
		}

		$logFiles = glob($fullpath."*.log");

		$this->set(compact('logFiles', 'filename'));
	}

	/**
	 * It is used to create backup of log file
	 *
	 * @access public
	 * @param string $filename file name
	 * @return void
	 */
	public function cakelogbackup($filename=null) {
		if($this->getRequest()->isPost()) {
			if(!empty($filename)) {
				$filepath = LOGS.$filename;

				if(file_exists($filepath)) {
					$pathinfo = pathinfo($filepath);
					$newfile = $pathinfo['filename'].'_'.date('d-M-Y_H-i', time()).'.'.$pathinfo['extension'];

					if(copy($filepath, LOGS.$newfile)) {
						$this->Flash->success(__('{0} has been copied to {1}', [$filename, $newfile]));
					} else {
						$this->Flash->error(__('{0} file could not be copied', [$filename]));
					}
				} else {
					$this->Flash->warning(__('{0} file does not exist', [$filename]));
				}
			} else {
				$this->Flash->error(__('Missing Filename'));
			}
		}

		$this->redirect(['action'=>'cakelog']);
	}

	/**
	 * It is used to delete log file
	 *
	 * @access public
	 * @param string $filename file name
	 * @return void
	 */
	public function cakelogdelete($filename=null) {
		if($this->getRequest()->isPost()) {
			if(!empty($filename)) {
				$filepath = LOGS.$filename;

				if(file_exists($filepath)) {
					if(unlink($filepath)) {
						$this->Flash->success(__('{0} has been deleted successfully', [$filename]));
					} else {
						$this->Flash->error(__('{0} file could not be deleted', [$filename]));
					}
				} else {
					$this->Flash->warning(__('{0} file does not exist', [$filename]));
				}
			} else {
				$this->Flash->error(__('Missing Filename'));
			}
		}

		$this->redirect(['action'=>'cakelog']);
	}

	/**
	 * It is used to make empty log file
	 *
	 * @access public
	 * @param string $filename file name
	 * @return void
	 */
	public function cakelogempty($filename=null) {
		if($this->getRequest()->isPost()) {
			if(!empty($filename)) {
				$filepath = LOGS.$filename;
				$f = @fopen($filepath, "r+");

				if($f !== false) {
					ftruncate($f, 0);
					fclose($f);

					$this->Flash->success(__('{0} has been emptied', [$filename]));
				} else {
					$this->Flash->warning(__('{0} file does not exist', [$filename]));
				}
			} else {
				$this->Flash->error(__('Missing Filename'));
			}
		}

		$this->redirect(['action'=>'cakelog']);
	}
}
