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
use Cake\Core\Plugin;
use Cake\Core\Configure;
use Cake\Filesystem\Folder;
use Cake\Event\EventInterface;
use Cake\Controller\ComponentRegistry;

class ControllerListComponent extends Component {

	public $registry;

	public function __construct(ComponentRegistry $registry, array $config = []) {
		$this->registry = $registry;

		parent::__construct($registry, $config);
	}
	/**
	 * Used to get all controllers with all methods for permissions
	 *
	 * @access public
	 * @return array
	 */
	public function getControllerAndActions() {
		$controllersList = [];
		$controllerClasses = $this->getControllerClasses();

		foreach($controllerClasses as $i=>$controllerClass) {
			$controllersList[$i]['controller'] = $controllerClass['controller'];
			$controllersList[$i]['prefix'] = $controllerClass['prefix'];
			$controllersList[$i]['plugin'] = $controllerClass['plugin'];

			$controllerClassName = $this->getControllerClassName($controllerClass['controller'], $controllerClass['prefix'], $controllerClass['plugin']);

			$controllersList[$i]['actions'] = $this->getClassPublicMethods($controllerClassName);
		}

		return $controllersList;
	}
	private function getControllerClassName($controller, $prefix=null, $plugin=null) {
		if($prefix) {
			$prefix = str_replace('.', '\\', $prefix).'\\';
		}

		if(empty($plugin)) {
			$base = Configure::read('App.namespace');
			$controllerClassName = $base.'\Controller\\'.$prefix.$controller.'Controller';
		}
		else {
			$controllerClassName = $plugin.'\Controller\\'.$prefix.$controller.'Controller';
		}

		return $controllerClassName;
	}
	private function getClassPublicMethods($className) {
		$methods = [];

		if(class_exists($className)) {
			$class = new \ReflectionClass($className);

			$ignoreList = ['initialize', 'beforeFilter', 'afterFilter', 'beforeRender', 'beforeRedirect', 'paginate'];

			foreach($class->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
				if($method->class == $className && !in_array($method->name, $ignoreList)) {
					$methods[] = $method->name;
				}
			}
		}

		return $methods;
	}
	/**
	 * Used to get controller classes list
	 *
	 * @access public
	 * @return array
	 */
	public function getControllerClasses() {
		$controllerClasses = [];
		$i = 0;

		$path = APP.'Controller';
		$dir = new Folder($path);

		$controllers = $dir->findRecursive('.*Controller.php');

		foreach($controllers as $controller) {
			$pathinfo = pathinfo($controller);

			$controllerClasses[$i]['controller'] = str_replace('Controller.php', '', $pathinfo['basename']);
			$controllerClasses[$i]['prefix'] = '';
			$controllerClasses[$i]['plugin'] = '';

			$prefix = str_replace($path, '', $pathinfo['dirname']);

			if(!empty($prefix)) {
				$prefix = ltrim($prefix, DS);
				$controllerClasses[$i]['prefix'] = str_replace(DS, '.', $prefix);
			}

			$i++;
		}

		$plugins = Plugin::loaded();

		foreach($plugins as $plugin) {
			$path = Plugin::classPath($plugin).'Controller';
			$dir = new Folder($path);
			$path = $dir->path;

			$controllers = $dir->findRecursive('.*Controller.php');

			foreach($controllers as $controller) {
				$pathinfo = pathinfo($controller);

				$controllerClasses[$i]['controller'] = str_replace('Controller.php', '', $pathinfo['basename']);
				$controllerClasses[$i]['prefix'] = '';
				$controllerClasses[$i]['plugin'] = $plugin;

				$prefix = str_replace($path, '', $pathinfo['dirname']);

				if(!empty($prefix)) {
					$prefix = ltrim($prefix, DS);
					$controllerClasses[$i]['prefix'] = str_replace(DS, '.', $prefix);
				}

				$i++;
			}
		}

		return $controllerClasses;
	}
	/**
	 * Used to get all controller and plugin names
	 *
	 * @access public
	 * @return array
	 */
	public function getAllControllerAndPluginNames() {
		$controllerClasses = $this->getControllerClasses();

		$controllerAndPluginNames = [];

		foreach($controllerClasses as $row) {
			$row['controller'] = strtolower($row['controller']);
			$row['plugin'] = strtolower($row['plugin']);

			if(!empty($row['controller'])) {
				$controllerAndPluginNames[$row['controller']] = $row['controller'];
			}

			if(!empty($row['plugin'])) {
				$controllerAndPluginNames[$row['plugin']] = $row['plugin'];
			}
		}

		return $controllerAndPluginNames;
	}
}
