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

use App\Controller\AppController;
use Cake\Event\EventInterface;
use Cake\Core\Configure;
use Cake\Http\Exception\BadRequestException;

class UsermgmtAppController extends AppController {
	/**
	 * Initialization hook method.
	 *
	 * Use this method to add common initialization code like loading components.
	 *
	 * @return void
	 */
	public function initialize(): void {
		parent::initialize();

		$this->loadComponent('RequestHandler');
		$this->loadComponent('Flash');
		$this->loadComponent('FormProtection');

		$this->FormProtection->setConfig('validationFailureCallback', function (BadRequestException $exception) {
			if(Configure::read('debug')) {
				throw $exception;
			} else {
				$currentUrl = $this->getRequest()->getRequestTarget();
				$this->Flash->error(__('The request has been black-holed'));

				return $this->redirect($currentUrl);
			}
		});
	}
}
