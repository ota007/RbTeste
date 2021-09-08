<?php
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

use Cake\Routing\Router;
use Cake\Routing\RouteBuilder;

Router::scope('/', function($routes) {
	$routes->connect('/login/*', ['controller'=>'Users', 'action'=>'login', 'plugin'=>'Usermgmt']);
	$routes->connect('/logout', ['controller'=>'Users', 'action'=>'logout', 'plugin'=>'Usermgmt']);
	$routes->connect('/forgotPassword', ['controller'=>'Users', 'action'=>'forgotPassword', 'plugin'=>'Usermgmt']);
	$routes->connect('/emailVerification', ['controller'=>'Users', 'action'=>'emailVerification', 'plugin'=>'Usermgmt']);
	$routes->connect('/activatePassword', ['controller'=>'Users', 'action'=>'activatePassword', 'plugin'=>'Usermgmt']);
	$routes->connect('/register', ['controller'=>'Users', 'action'=>'register', 'plugin'=>'Usermgmt']);
	$routes->connect('/dashboard', ['controller'=>'Users', 'action'=>'dashboard', 'plugin'=>'Usermgmt']);
	$routes->connect('/contactUs', ['controller'=>'UserContacts', 'action'=>'contactUs', 'plugin'=>'Usermgmt']);
	$routes->connect('/userVerification/*', ['controller'=>'Users', 'action'=>'userVerification', 'plugin'=>'Usermgmt']);
	$routes->connect('/accessDenied', ['controller'=>'Users', 'action'=>'accessDenied', 'plugin'=>'Usermgmt']);
	$routes->connect('/myprofile', ['controller'=>'Users', 'action'=>'myprofile', 'plugin'=>'Usermgmt']);
	$routes->connect('/editProfile', ['controller'=>'Users', 'action'=>'editProfile', 'plugin'=>'Usermgmt']);
	$routes->connect('/StaticPages/*', ['controller'=>'StaticPages', 'action'=>'preview', 'plugin'=>'Usermgmt']);


	$routes->plugin('Usermgmt', ['path' => '/usermgmt'], function (RouteBuilder $builder) {
		$builder->setRouteClass(DashedRoute::class);

		$builder->fallbacks();
	});
});
