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

namespace Usermgmt\View\Helper;

use Cake\View\Helper;
use Cake\View\View;

class SearchHelper extends Helper {
	public $viewobj;

	public function __construct(View $View, array $config = []) {
		parent::__construct($View, $config);
		
		$this->viewobj = $View;
	}

	public function searchForm($modelName, $options) {
		$output = $this->viewobj->element('Usermgmt.search_form', ['plugin'=>'Usermgmt', 'modelName'=>$modelName, 'options'=>$options]);
		
		return $output;
	}
}
