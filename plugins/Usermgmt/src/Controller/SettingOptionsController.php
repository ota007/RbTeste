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

class SettingOptionsController extends UsermgmtAppController {
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
			'Usermgmt.SettingOptions'=>[
				'SettingOptions.title'=>[
					'type'=>'text',
					'label'=>'Search',
					'tagline'=>'Search by title',
					'searchSuggestion'=>true,
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
	}

	/**
	 * Called before the controller action. You can use this method to configure and customize components
	 * or perform logic that needs to happen before each controller action.
	 *
	 * @return void
	 */
	public function beforeFilter(EventInterface $event) {
		parent::beforeFilter($event);

		$this->loadModel('Usermgmt.SettingOptions');

		$action = $this->getRequest()->getParam('action');

		if(isset($this->FormProtection) && $this->getRequest()->is('ajax')) {
			$this->FormProtection->setConfig('unlockedActions', [$action]);
		}
	}

	/**
	 * It displays all setting options
	 *
	 * @access public
	 * @return void
	 */
	public function index() {
		$cond = [];
		$cond = $this->Search->applySearch($cond);

		$this->paginate = ['limit'=>10, 'conditions'=>$cond, 'order'=>['SettingOptions.title'=>'ASC']];

		$settingOptions = $this->paginate($this->SettingOptions)->toArray();

		$this->set(compact('settingOptions'));

		if($this->getRequest()->is('ajax')) {
			$this->viewBuilder()->setLayout('ajax');
			$this->render('/SettingOptions/all_setting_options');
		}
	}

	/**
	 * It is used to add setting option
	 *
	 * @access public
	 * @return void
	 */
	public function add() {
		$settingOptionEntity = $this->SettingOptions->newEmptyEntity();

		if($this->getRequest()->is('post')) {
			$settingOptionEntity = $this->SettingOptions->patchEntity($settingOptionEntity, $this->getRequest()->getData(), ['validate'=>'forAdd']);

			$errors = $settingOptionEntity->getErrors();

			if($this->getRequest()->is('ajax')) {
				if(empty($errors)) {
					$response = ['error'=>0, 'message'=>'success'];
				} else {
					$response = ['error'=>1, 'message'=>'failure'];
					$response['data']['SettingOptions'] = $errors;
				}
				echo json_encode($response);exit;
			} else {
				if(empty($errors)) {
					if($this->SettingOptions->save($settingOptionEntity, ['validate'=>false])) {
						$this->Flash->success(__('The Setting Option has been added successfully'));

						$this->redirect(['action'=>'add']);
					} else {
						$this->Flash->error(__('Unable to save Setting Option, please try again'));
					}
				}
			}
		}

		$this->set(compact('settingOptionEntity'));
	}

	/**
	 * It is used to edit setting option
	 *
	 * @access public
	 * @param integer $settingOptionId setting option id
	 * @return void
	 */
	public function edit($settingOptionId=null) {
		if($settingOptionId) {
			$settingOptionEntity = $this->SettingOptions->find()->where(['SettingOptions.id'=>$settingOptionId])->first();

			if(!empty($settingOptionEntity)) {
				if($this->getRequest()->is(['post', 'put'])) {
					$settingOptionEntity = $this->SettingOptions->patchEntity($settingOptionEntity, $this->getRequest()->getData(), ['validate'=>'forAdd']);

					$errors = $settingOptionEntity->getErrors();

					if($this->getRequest()->is('ajax')) {
						if(empty($errors)) {
							$response = ['error'=>0, 'message'=>'success'];
						} else {
							$response = ['error'=>1, 'message'=>'failure'];
							$response['data']['SettingOptions'] = $errors;
						}
						echo json_encode($response);exit;
					} else {
						if(empty($errors)) {
							if($this->SettingOptions->save($settingOptionEntity, ['validate'=>false])) {
								$this->Flash->success(__('The Setting Option has been updated successfully'));

								$this->redirect(['action'=>'index', '?'=>['page'=>$this->UserAuth->getPageNumber()]]);
							} else {
								$this->Flash->error(__('Unable to save Setting Option, please try again'));
							}
						}
					}
				}

				$this->set(compact('settingOptionEntity'));
			} else {
				$this->Flash->error(__('Invalid Setting Option id'));
				$this->redirect(['action'=>'index']);
			}
		} else {
			$this->Flash->error(__('Missing Setting Option id'));
			$this->redirect(['action'=>'index']);
		}
	}

	/**
	 * It is used to delete setting option
	 *
	 * @access public
	 * @param integer $settingOptionId setting option id
	 * @return void
	 */
	public function delete($settingOptionId=null) {
		if(!empty($settingOptionId)) {
			$settingOption = $this->SettingOptions->find()->where(['SettingOptions.id'=>$settingOptionId])->first();

			if(!empty($settingOption)) {
				if($this->getRequest()->is('post')) {
					$this->loadModel('UserSettingOptions');

					if(!$this->UserSettingOptions->exists(['UserSettingOptions.setting_option_id'=>$settingOptionId])) {
						if($this->SettingOptions->delete($settingOption)) {
							$this->Flash->success(__('Selected setting option is deleted successfully'));
						} else {
							$this->Flash->error(__('Unable to delete setting option, please try again'));
						}
					} else {
						$this->Flash->error(__('This setting option exists in other tables so cannot be deleted'));
					}
				}
			} else {
				$this->Flash->error(__('Invalid Setting Option Id'));
			}
		} else {
			$this->Flash->error(__('Missing Setting Option Id'));
		}

		$this->redirect(['action'=>'index', '?'=>['page'=>$this->UserAuth->getPageNumber()]]);
	}
}
