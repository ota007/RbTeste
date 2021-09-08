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

namespace Usermgmt\Model\Table;

use Usermgmt\Model\Table\UsermgmtAppTable;
use Cake\Validation\Validator;

class StaticPagesTable extends UsermgmtAppTable {

	public function initialize(array $config): void {
		$this->addBehavior('Timestamp');
	}

	public function validationForAdd($validator) {
		$validator
			->add('page_name', [
				'notBlank'=>[
					'rule'=>'notBlank',
					'message'=>__('Please enter page name'),
					'last'=>true
				]
			])

			->add('url_name', [
				'notBlank'=>[
					'rule'=>'notBlank',
					'message'=>__('Please enter page url'),
					'last'=>true
				],
				'unique'=>[
					'rule'=>'validateUnique',
					'provider'=>'table',
					'message'=>__('This url name already exist')
				]
			])

			->add('page_content', [
				'notBlank'=>[
					'rule'=>'notBlank',
					'message'=>__('Please enter page content'),
					'last'=>true
				]
			]);

		return $validator;
	}
}
