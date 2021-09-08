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

namespace Usermgmt\Auth;

use Cake\Controller\ComponentRegistry;
use Cake\Auth\BasicAuthenticate;
use Cake\Http\ServerRequest;
use Cake\Http\Response;
use Cake\Utility\Inflector;
use Cake\Datasource\FactoryLocator;

class PermissionAuthenticate extends BasicAuthenticate {

	public $controller;
	public $session;

	public function __construct(ComponentRegistry $registry, array $config = []) {
		parent::__construct($registry, $config);

		$this->controller = $registry->getController();
		$this->session = $registry->getController()->getRequest()->getSession();
	}

	public function authenticate(ServerRequest $request, Response $response) {
		return $this->getUser($request);
	}

	public function unauthenticated(ServerRequest $request, Response $response) {
		$controller = Inflector::camelize($request->getParam('controller'));
		$action = $request->getParam('action');
		$plugin = $request->getParam('plugin');

		$actionUrl = $controller.'/'.$action;

		if(!empty($plugin)) {
			$actionUrl = $plugin.'/'.$actionUrl;
		}

		$prefix = null;

		if(!empty($request->getParam('prefix'))) {
			$prefix = strtolower(Inflector::camelize($request->getParam('prefix')));
		}

		$requested = (!empty($request->getParam('requested'))) ? true : false;

		if(!$requested && !defined('CRON_DISPATCHER')) {
			$userGroupTable = FactoryLocator::get('Table')->get('Usermgmt.UserGroups');

			if(!$userGroupTable->isGuestAccess($controller, $action, $plugin, $prefix)) {
				if($request->is('ajax')) {
					$response = ['error'=>1, 'message'=>__('You need to be signed in to perform this action.')];

					echo json_encode($response);exit;
				}
				else {
					$this->controller->Auth->setConfig('authError', __('You need to be signed in to view that page.'));

					return null;
				}
			}
		}

		return true;
	}
}
