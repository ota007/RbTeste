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
use Cake\Event\EventInterface;
use Cake\Controller\ComponentRegistry;
use Cake\Routing\Router;

class SslComponent extends Component {

	public $registry;
	public $controller;
	public $request;

	public function __construct(ComponentRegistry $registry, array $config = []) {
		$this->registry = $registry;

		$this->controller = $registry->getController();
		$this->request = $registry->getController()->getRequest();

		parent::__construct($registry, $config);
	}

	public function beforeFilter(EventInterface $event) {
		$this->controller = $this->getController();
		$this->request = $this->getController()->getRequest();
	}

	public function force() {
		if(!$this->request->is('ssl')) {
			$this->convertAndRedirect(true);
		}
	}

	public function unforce() {
		if($this->request->is('ssl')) {
			$this->convertAndRedirect(false);
		}
	}

	private function convertAndRedirect($ssl) {
		$fullurl = Router::url($this->request->getRequestTarget(), true);

		if($ssl) {
			$fullurl = str_replace('http://', 'https://', $fullurl);
		} else {
			$fullurl = str_replace('https://', 'http://', $fullurl);
		}

		$this->controller->redirect($fullurl);
	}
}
