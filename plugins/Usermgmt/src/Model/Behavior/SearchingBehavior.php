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

namespace Usermgmt\Model\Behavior;

use Cake\ORM\Behavior;
use Cake\ORM\Table;

class SearchingBehavior extends Behavior {

	public $searchValues = [];
	public $alias;
	public $config = [];
	public $searchFields = [];

	public function __construct(Table $table, array $config = []) {
		$this->alias = $table->getAlias();
		$this->config = $config;

		parent::__construct($table, $config);
	}

	public function setSearchFields($searchFields = []) {
		foreach($searchFields as $key=>$value) {
			$this->searchFields[$this->alias][$key] = $value;
		}

		$this->searchValues[$this->alias] = [];
	}

	public function setSearchValues($searchValues = []) {
		if(is_array($searchValues) && !empty($searchValues)) {
			$this->searchValues[$this->alias] = array_merge($this->searchValues[$this->alias], $searchValues);
		}
	}

	public function applySearchFilters($conditions) {
		if(!empty($this->searchFields[$this->alias])) {
			$searchFields = $this->searchFields[$this->alias];
			$searchValues = $this->searchValues[$this->alias];

			foreach($searchFields as $field=>$options) {
				$fieldModelName = $this->alias;
				$fieldName = $field;

				if(strpos($field, '.') !== false) {
					list($fieldModelName, $fieldName) = explode('.', $field);
				}

				if(!isset($searchValues[$fieldModelName][$fieldName]) && strlen((string)$options['default'])) {
					$searchValues[$fieldModelName][$fieldName] = $options['default'];
				}

				if(!isset($searchValues[$fieldModelName][$fieldName]) || is_null($searchValues[$fieldModelName][$fieldName])) {
					// no value to search with, just skip this field
					continue;
				}

				$searchValue = $searchValues[$fieldModelName][$fieldName];

				if(strlen($options['searchField'])) {
					$fieldName = $options['searchField'];

					if(strpos($fieldName, '.') !== false) {
						list($fieldModelName, $fieldName) = explode('.', $fieldName);
					}
				}

				$realSearchField = sprintf('%s.%s', $fieldModelName, $fieldName);

				$textTransformFields = [];

				if(!empty($options['textTransform'])) {
					$textTransformValue = $this->getTransformText($options['textTransform'], $searchValue);

					if($options['condition'] != 'multiple') {
						$searchValue = $textTransformValue;
					}

					if(!empty($options['textTransformFields'])) {
						$textTransformFields = array_flip($options['textTransformFields']);
					}
				}

				$is_date = $this->is_date($searchValue);

				switch($options['type']) {
					case 'text':
						if(strlen(trim(strval($searchValue))) == 0) {
							break;
						}

						switch($options['condition']) {
							case 'like':
							case 'contains':
								$conditions[$realSearchField.' like'] = '%'.$searchValue.'%';
								break;

							case 'startswith':
								$conditions[$realSearchField.' like'] = $searchValue.'%';
								break;

							case 'endswith':
								$conditions[$realSearchField.' like'] = '%'.$searchValue;
								break;

							case '=':
								$conditions[$realSearchField] = $searchValue;
								break;

							case '<':
								$conditions[$realSearchField.' <'] = $searchValue;
								break;

							case '>':
								$conditions[$realSearchField.' >'] = $searchValue;
								break;

							case '<=':
								if($is_date) {
									$searchValue = date('Y-m-d 23:59:59', strtotime($searchValue));
								}
								$conditions[$realSearchField.' <='] = $searchValue;
								break;

							case '>=':
								if($is_date) {
									$searchValue = date('Y-m-d 00:00:00', strtotime($searchValue));
								}
								$conditions[$realSearchField.' >='] = $searchValue;
								break;

							case 'comma':
								$cond = [[$realSearchField=>$searchValue], [$realSearchField.' like'=>$searchValue.',%'], [$realSearchField.' like'=>'%,'.$searchValue.',%'], [$realSearchField.' like'=>'%,'.$searchValue]];
								$conditions['AND'][] = ['OR'=>$cond];
								break;

							case 'semicolon':
								$cond = [[$realSearchField=>$searchValue], [$realSearchField.' like'=>$searchValue.';%'], [$realSearchField.' like'=>'%;'.$searchValue.';%'], [$realSearchField.' like'=>'%;'.$searchValue]];

								$conditions['AND'][] = ['OR'=>$cond];
								break;

							case 'multiple':
								if(!empty($options['searchFields']) && is_array($options['searchFields'])) {
									$cond = [];

									if($options['searchBreak']) {
										$valueArray = explode(' ', $searchValue);

										if($options['matchAllWords']) {
											$i = 0;

											foreach($options['searchFields'] as $searchField) {
												foreach($valueArray as $v) {
													if(!empty($v)) {
														if($this->is_date($v)) {
															$v = date('Y-m-d', strtotime($v));
														}

														if(isset($textTransformFields[$searchField])) {
															$v = $this->getTransformText($options['textTransform'], $v);
														}

														$cond[$i][] = [$searchField.' like'=>'%'.$v.'%'];
													}
												}

												$i++;
											}
										}
										else {
											foreach($valueArray as $v) {
												if(!empty($v)) {
													if($this->is_date($v)) {
														$v = date('Y-m-d', strtotime($v));
													}

													foreach($options['searchFields'] as $searchField) {
														if(isset($textTransformFields[$searchField])) {
															$v = $this->getTransformText($options['textTransform'], $v);
														}

														$cond[] = [$searchField.' like'=>'%'.$v.'%'];
													}
												}
											}
										}
									}
									else {
										$v = $searchValue;

										if(!empty($v)) {
											if($this->is_date($v)) {
												$v = date('Y-m-d', strtotime($v));
											}
											foreach($options['searchFields'] as $searchField) {
												if(isset($textTransformFields[$searchField])) {
													$v = $this->getTransformText($options['textTransform'], $v);
												}
												$cond[] = [$searchField.' like'=>'%'.$v.'%'];
											}
										}
									}
									$conditions['AND'][] = ['OR'=>$cond];
								}
								break;

							default:
								$conditions[$realSearchField.' '.$options['condition']] = $searchValue;
								break;
						}
						break;

					case 'select':
						if(!is_array($searchValue)) {
							if(strlen(trim(strval($searchValue))) == 0) {
								if(isset($conditions[$realSearchField])) {
									unset($conditions[$realSearchField]);
								}
								break;
							}
						}

						switch($options['condition']) {
							case 'multiple':
								$conditions[$realSearchField] = $searchValue;
								break;

							case 'comma':
								$cond = [[$realSearchField=>$searchValue], [$realSearchField.' like'=>$searchValue.',%'], [$realSearchField.' like'=>'%,'.$searchValue.',%'], [$realSearchField.' like'=>'%,'.$searchValue]];
								$conditions['AND'][] = ['OR'=>$cond];
								break;

							case 'semicolon':
								$cond = [[$realSearchField=>$searchValue], [$realSearchField.' like'=>$searchValue.';%'], [$realSearchField.' like'=>'%;'.$searchValue.';%'], [$realSearchField.' like'=>'%;'.$searchValue]];
								$conditions['AND'][] = ['OR'=>$cond];
								break;

							case 'null':
								if($searchValue == 'NULL') {
									$conditions[] = $realSearchField.' IS NULL';
								} else if($searchValue == 'NOT_NULL') {
									$conditions[] = $realSearchField.' IS NOT NULL';
								}
								break;

							default:
								$conditions[$realSearchField] = $searchValue;
								break;
						}
						break;

					case 'checkbox':
						$conditions[$realSearchField] = $searchValue;
						break;
				}
			}
		}

		return $conditions;
	}
	public function is_date($str) {
		if(is_array($str)) {
			return false;
		}

		if(is_numeric($str)) {
			return false;
		}

		$strtotime = strtotime($str);

		if(is_numeric($strtotime)) {
			if($str == date('Y-m-d', $strtotime)) {
				return true;
			}
			else if($str == date('d-M-Y', $strtotime)) {
				return true;
			}
			//add more date formats
		}

		return false;
	}
	public function getTransformText($textTransformOption, $searchValue) {
		if(strtolower($textTransformOption) == 'uppercase') {
			$searchValue = strtoupper($searchValue);
		}
		else if(strtolower($textTransformOption) == 'lowercase') {
			$searchValue = strtolower($searchValue);
		}
		else if(strtolower($textTransformOption) == 'capitalize') {
			$searchValue = ucwords($searchValue);
		}

		return $searchValue;
	}
}
