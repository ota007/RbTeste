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

class StaticPagesController extends UsermgmtAppController {
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
			'Usermgmt.StaticPages'=>[
				'StaticPages'=>[
					'type'=>'text',
					'label'=>'Search',
					'tagline'=>'Search by page name, title, url name',
					'condition'=>'multiple',
					'searchFields'=>['StaticPages.page_name', 'StaticPages.page_title', 'StaticPages.url_name'],
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

		$this->loadModel('Usermgmt.StaticPages');

		$action = $this->getRequest()->getParam('action');

		if(isset($this->FormProtection) && $this->getRequest()->is('ajax')) {
			$this->FormProtection->setConfig('unlockedActions', [$action]);
		}
	}

	/**
	 * It displays all static pages
	 *
	 * @access public
	 * @return void
	 */
	public function index() {
		$cond = [];
		$cond = $this->Search->applySearch($cond);

		$this->paginate = ['limit'=>10, 'conditions'=>$cond, 'order'=>['StaticPages.id'=>'DESC']];

		$staticPages = $this->paginate($this->StaticPages)->toArray();

		$this->set(compact('staticPages'));

		if($this->getRequest()->is('ajax')) {
			$this->viewBuilder()->setLayout('ajax');
			$this->render('/StaticPages/all_static_pages');
		}
	}

	/**
	 * It is used to create a new static page
	 *
	 * @access public
	 * @return void
	 */
	public function add() {
		$staticPageEntity = $this->StaticPages->newEmptyEntity();

		if($this->getRequest()->is('post')) {
			$staticPageEntity = $this->StaticPages->patchEntity($staticPageEntity, $this->getRequest()->getData(), ['validate'=>'forAdd']);

			$errors = $staticPageEntity->getErrors();

			if($this->getRequest()->is('ajax')) {
				if(empty($errors)) {
					$response = ['error'=>0, 'message'=>'success'];
				} else {
					$response = ['error'=>1, 'message'=>'failure'];
					$response['data']['StaticPages'] = $errors;
				}
				echo json_encode($response);exit;
			} else {
				if(empty($errors)) {
					if($this->StaticPages->save($staticPageEntity, ['validate'=>false])) {
						$this->Flash->success(__('The static page is successfully added'));

						$this->redirect(['action'=>'index']);
					} else {
						$this->Flash->error(__('Unable to add static page, please try again'));
					}
				}
			}
		}

		$this->set(compact('staticPageEntity'));
	}

	/**
	 * It is used to edit static page
	 *
	 * @access public
	 * @param integer $staticPageId static page id
	 * @return void
	 */
	public function edit($staticPageId=null) {
		if(!empty($staticPageId)) {
			$staticPageEntity = $this->StaticPages->find()->where(['StaticPages.id'=>$staticPageId])->first();

			if(!empty($staticPageEntity)) {
				if($this->getRequest()->is(['put', 'post'])) {
					$staticPageEntity = $this->StaticPages->patchEntity($staticPageEntity, $this->getRequest()->getData(), ['validate'=>'forAdd']);

					$errors = $staticPageEntity->getErrors();

					if($this->getRequest()->is('ajax')) {
						if(empty($errors)) {
							$response = ['error'=>0, 'message'=>'success'];
						} else {
							$response = ['error'=>1, 'message'=>'failure'];
							$response['data']['StaticPages'] = $errors;
						}
						echo json_encode($response);exit;
					} else {
						if(empty($errors)) {
							if($this->StaticPages->save($staticPageEntity, ['validate'=>false])) {
								$this->Flash->success(__('The static page has been updated successfully'));

								$this->redirect(['action'=>'index', '?'=>['page'=>$this->UserAuth->getPageNumber()]]);
							} else {
								$this->Flash->error(__('Unable to save static page, please try again'));
							}
						}
					}
				}

				$this->set(compact('staticPageEntity'));
			} else {
				$this->Flash->error(__('Invalid Static Page Id'));
				$this->redirect(['action'=>'index']);
			}
		} else {
			$this->Flash->error(__('Missing Static Page Id'));
			$this->redirect(['action'=>'index']);
		}
	}

	/**
	 * It is used to view static page detail
	 *
	 * @access public
	 * @param integer $staticPageId static page id
	 * @return void
	 */
	public function view($staticPageId=null) {
		if(!empty($staticPageId)) {
			$staticPage = $this->StaticPages->find()->where(['StaticPages.id'=>$staticPageId])->first();

			if(!empty($staticPage)) {
				$this->set(compact('staticPage'));
			} else {
				$this->Flash->error(__('Invalid Static Page Id'));
				$this->redirect(['action'=>'index']);
			}
		} else {
			$this->Flash->error(__('Missing Static Page Id'));
			$this->redirect(['action'=>'index']);
		}
	}

	/**
	 * It is used to delete the static page
	 *
	 * @access public
	 * @param integer $staticPageId static page id
	 * @return void
	 */
	public function delete($staticPageId=null) {
		if(!empty($staticPageId)) {
			if($this->getRequest()->is(['post'])) {
				$staticPageEntity = $this->StaticPages->find()->where(['StaticPages.id'=>$staticPageId])->first();

				if(!empty($staticPageEntity)) {
					if($this->StaticPages->delete($staticPageEntity)) {
						$this->Flash->success(__('Selected static page has been deleted successfully'));
					} else {
						$this->Flash->error(__('Selected static page can not be deleted, please try again'));
					}
				} else {
					$this->Flash->error(__('Invalid Static Page Id'));
				}
			}
		} else {
			$this->Flash->error(__('Missing Static Page Id'));
		}

		$this->redirect(['action'=>'index', '?'=>['page'=>$this->UserAuth->getPageNumber()]]);
	}

	/**
	 * It is used to preview static page contents by url
	 *
	 * @access public
	 * @param string $urlName page url name
	 * @return void
	 */
	public function preview($urlName=null) {
		$invalidPage = true;

		if(!empty($urlName)) {
			$staticPage = $this->StaticPages->find()->where(['StaticPages.url_name'=>$urlName])->first();

			if(!empty($staticPage)) {
				$invalidPage = false;

				$this->set(compact('staticPage'));
			}
		}

		if($invalidPage) {
			$this->redirect('/');
		}
	}
}
