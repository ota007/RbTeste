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
use Cake\Controller\ComponentRegistry;
use Cake\Event\EventInterface;
use DocBlock\Reflection\DocBlock;

class FunctionBlockComponent extends Component {

	public $registry;

	public $reflectClasses = [];

	public function __construct(ComponentRegistry $registry, array $config = []) {
		$this->registry = $registry;

		parent::__construct($registry, $config);
	}

	public function beforeFilter(EventInterface $event) {
		require_once(USERMGMT_PATH.DS.'vendor'.DS.'docblock'.DS.'src'.DS.'DocBlock.php');
	}

	public function getComment($class, $action) {
		if(!isset($reflectClasses[$class])) {
			$reflectClasses[$class] = new \ReflectionClass($class);
		}

		$reflect = $reflectClasses[$class];

		$doc = new DocBlock($reflect->getMethod($action));

		return $doc->getComment();
	}
}
