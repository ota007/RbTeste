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
use Cake\Datasource\FactoryLocator;
use Cake\Event\EventInterface;

class SearchComponent extends Component {

	public $components = ['Usermgmt.UserAuth'];

	public $registry;
	public $controller;
	public $request;
	public $response;
	public $session;

	public $searchFields = [];
	public $formdata = [];
	public $orgconditions = [];

	public function __construct(ComponentRegistry $registry, array $config = []) {
		$this->registry = $registry;

		parent::__construct($registry, $config);
	}

	public function beforeFilter(EventInterface $event) {
		$this->controller = $this->getController();
		$this->request = $this->controller->getRequest();
		$this->response = $this->controller->getResponse();
		$this->session = $this->request->getSession();

		if(isset($this->controller->searchFields)) {
			$searchFields = $this->controller->searchFields;
			$action = $this->request->getParam('action');

			if(!empty($searchFields[$action]) && is_array($searchFields[$action])) {
				$this->searchFields = $searchFields[$action];

				foreach($this->searchFields as $model=>$fields) {
					$this->controller->loadModel($model);
					$model = $this->getModelName($model);

					if(!$this->controller->$model->hasBehavior('Searching')) {
						$this->controller->$model->addBehavior('Usermgmt.Searching');
					}

					foreach($fields as $field=>$options) {
						$fields[$field] = array_merge($this->getDefaultSearchOptions(), $options);
					}

					$this->controller->$model->setSearchFields($fields);
				}
			}
		}
	}

	public function startup() {
		if(!empty($this->searchFields)) {
			$sessionKey = sprintf('UserAuth.Search.%s.%s', $this->controller->getName(), $this->request->getParam('action'));
			$formdata = $this->request->getData();

			if($this->request->is('get') && isset($_GET['ump_search'])) {
				$this->formdata = $_GET;
				$this->session->write($sessionKey, $this->formdata);
			}
			else if($this->request->is('post') || isset($formdata['ump_search'])) {
				$this->formdata = $formdata;
				$this->session->write($sessionKey, $this->formdata);
			}
			else if($this->session->check($sessionKey)) {
				$this->formdata = $this->session->read($sessionKey);
			}

			if(isset($this->formdata['search_clear']) && $this->formdata['search_clear']) {
				$this->session->delete($sessionKey);
				$this->formdata = [];
			}

			foreach($this->searchFields as $model=>$search) {
				$model = $this->getModelName($model);

				$this->controller->$model->setSearchValues($this->formdata);
			}
		}
	}

	public function applySearch($conditions = []) {
		$this->orgconditions = $conditions;

		foreach($this->searchFields as $model=>$search) {
			$model = $this->getModelName($model);

			$conditions = $this->controller->$model->applySearchFilters($conditions);
		}

		return $conditions;
	}

	public function beforeRender(EventInterface $event) {
		if(!empty($this->searchFields)) {
			$viewSearchParams = [];

			foreach($this->searchFields as $model=>$fields) {
				$model = $this->getModelName($model);

				foreach($fields as $field=>$options) {
					$fields[$field] = array_merge($this->getDefaultSearchOptions(), $options);

					if(!is_array($fields[$field]['inputOptions'])) {
						$fields[$field]['inputOptions'] = [$fields[$field]['inputOptions']];
					}
				}

				foreach($fields as $field=>$options) {
					$fieldName = $field;
					$fieldModel = $model;

					if(strpos($field, '.') !== false) {
						list($fieldModel, $fieldName) = explode('.', $field);
					}

					$fieldWithModel = $fieldModel.'.'.$fieldName;

					if(isset($this->orgconditions[$fieldWithModel]) && $this->orgconditions[$fieldWithModel] != -1) {
						$options['value'] = $this->orgconditions[$fieldWithModel];
					}

					if(!empty($this->formdata) && isset($this->formdata[$fieldModel][$fieldName])) {
						$options['value'] = $this->formdata[$fieldModel][$fieldName];
					}

					if(!strlen((string)$options['value']) && strlen((string)$options['default'])) {
						$options['value'] = $options['default'];
					}

					switch($options['type']) {
						case 'select':
							if(empty($options['model'])) {
								$options['model'] = $fieldModel;
							}

							$workingModel = FactoryLocator::get('Table')->get($options['model']);

							if(!empty($options['selector'])) {
								if(!method_exists($workingModel, $options['selector'])) {
									trigger_error(__('Selector method {0} not found in model.table {1} for field {2} and make sure you passed model-table name (if model.table belongs to any plugin then pass plugin.model.table) in search fields variable of respective controller', $options['selector'], $options['model'], $fieldName));
									exit;
								}

								$selectorName = $options['selector'];

								if(is_array($options['selectorArguments']) && count($options['selectorArguments'])) {
									$options['options'] = $workingModel->$selectorName($options['selectorArguments']);
								}
								else if(!is_array($options['selectorArguments']) && isset($options['selectorArguments'])) {
									$options['options'] = $workingModel->$selectorName($options['selectorArguments']);
								}
								else {
									$options['options'] = $workingModel->$selectorName();
								}
							}
							break;

						case 'checkbox':
							if(!empty($this->formdata) && isset($this->formdata[$fieldModel][$fieldName])) {
								$options['checked'] = !!$options['value'];
							}
							else if(isset($options['default'])) {
								$options['checked'] = !!$options['default'];
							}
							break;

						default:
							break;
					}

					$options['field'] = $fieldName;

					if(!empty($options['searchSuggestion'])) {
						$options['fieldNameEncrypted'] = $this->UserAuth->encryptField($fieldName);
					}

					$viewSearchParams[] = ['modelField'=>sprintf('%s.%s', $fieldModel, $fieldName), 'options'=>$options];
				}
			}

			$this->controller->set(compact('viewSearchParams'));
		}
	}

	private function getModelName($model) {
		if(strpos($model, '.') !== false) {
			list($plugin, $model) = explode('.', $model);
		}

		return $model;
	}

	private function getDefaultSearchOptions() {
		$default = [
					'type'=>'text',
					'value'=>'',
					'default'=>'',
					'condition'=>'like',
					'label'=>'',
					'tagline'=>'',
					'adminOnly'=>false,
					'model'=>'',
					'selector'=>'',
					'selectorArguments'=>[],
					'options'=>[],
					'inputOptions'=>[],
					'searchField'=>'',
					'searchFields'=>[],
					'searchBreak'=>true,
					'matchAllWords'=>false,
					'searchFunc'=>[],
					'searchSuggestion'=>false,
					'textTransform'=>'',
					'textTransformFields'=>[]
				];

				/*
				possible values are below, you can most of usage in users controller

				-- 'type' = 'text', 'select', 'checkbox'

				-- 'value' = you can pass value for any search field

				-- 'default' = you can pass default value for any search field

				-- 'condition' =
						For Text type field
							'like', 'contains', 'startswith', 'endswith', '=', '<', '>', '<=', '>=', 'comma', 'semicolon', 'multiple'
						For select type field
							'multiple', 'comma', 'semicolon', 'null'
						For checkbox type field
							no need to paas condition

				-- 'label' = search filter field label name

				-- 'tagline' = it's a text which comes below search filter field

				-- 'adminOnly' =  true or false

				-- 'model' = model name from you want to call action for select options

				-- 'selector' = it is action name in model, you want to call it for select options

				-- 'selectorArguments' = you can pass arguments for selector action of model

				-- 'options' = it's a array of options for select field, either use 'options' or combination of 'model' & 'selector' to set select input search field

				-- 'inputOptions' = you can pass filter input html attributes like style, class, placeHolder etc

				-- 'searchField' = it's database search field useful when you want 2 or more conditions on single field
									check Users controller index filters created1 and created2

				-- 'searchFields' = it's a array of database search fields

				-- 'searchBreak' = works with 'condition' = 'multiple',
									for e.g. if you type 'User Plugin' in search input
									if 'searchBreak' is false then 'User Plugin' text will be searched as single word in database fields

				-- 'matchAllWords' = works with 'condition' = 'multiple' and 'searchBreak' = true
										for e.g. if you type 'User Plugin' in search input
										'User' and 'Plugin' both text should be matched in database records

				-- 'searchFunc' = it's a array of url attributes to show custom suggestion when you type in search filter inputs

				-- 'searchSuggestion' pass true if you want auto search suggestion on text type field

				-- 'textTransform' = 'uppercase', 'lowercase', 'capitalize'

				-- 'textTransformFields' = you can specify fields to apply text transform of value
											this works with 'textTransform' option
											'textTransformFields' only applicable with 'condition' = 'multiple'
											for e.g. if you have multilple condition with more than 1 fields and you want to apply text transform of few fields
											...[
												'type'=>'text',
												'label'=>'Search',
												'tagline'=>'Search by name, username, email',
												'condition'=>'multiple',
												'searchFields'=>['Users.first_name', 'Users.last_name', 'Users.username', 'Users.email'],
												'textTransform'=>'uppercase',
												'textTransformFields'=>['Users.username']
												.....
											]
				*/

		return $default;
	}
}
