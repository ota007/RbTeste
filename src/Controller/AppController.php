<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Event\EventInterface;
use Cake\Core\Configure;
use Cake\Http\Exception\BadRequestException;

class AppController extends Controller {
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

		$this->loadComponent('Usermgmt.UserAuth');

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

	/* Override functions */
	public function paginate($object = null, array $settings = []) {
		$sessionKey = sprintf('UserAuth.Search.%s.%s', $this->getRequest()->getParam('controller'), $this->getRequest()->getParam('action'));

		if($this->getRequest()->getSession()->check($sessionKey)) {
			$persistedData = $this->getRequest()->getSession()->read($sessionKey);

			if(!empty($persistedData['page_limit'])) {
				$this->paginate['limit'] = $persistedData['page_limit'];
			}
		}

		return parent::paginate($object, $settings);
	}
}