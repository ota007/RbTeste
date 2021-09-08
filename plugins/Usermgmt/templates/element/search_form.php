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

use Cake\Utility\Text;
use Cake\Routing\Router;

$isAjax = $clear = true;
$formType = 'POST';
$urlParams = $searchFields = [];
$page_limit = '';
$searchFormId = $modelName.'Usermgmt';
$updateDivId = $options['updateDivId'];

$targetUrl = $this->request->getAttribute('here');
$formUrl = $this->request->getPath();
$clearUrl = $this->request->getAttribute('here');
$queryAttributes = $this->request->getQuery();
$pagingAttributes = $this->request->getAttribute('paging');

if(isset($options['useAjax']) && !$options['useAjax']) {
	$isAjax = false;
}
if(isset($options['clear']) && !$options['clear']) {
	$clear = false;
}
if(isset($options['formType']) && strtolower($options['formType']) == 'get') {
	$formType = 'GET';
}

if($formType == 'GET') {
	$formUrl = Router::url($formUrl, true);
}

if(isset($viewSearchParams)) {
	foreach($viewSearchParams as $searchParam) {
		$searchFields[$searchParam['modelField']] = $searchParam['modelField'];
	}
}

if(!empty($queryAttributes)) {
	$targetUrlParams = $formUrlParams = [];

	foreach($queryAttributes as $k=>$v) {
		if(strtolower($k) != 'page') {
			if(is_array($v)) {
				foreach($v as $k1=>$v1) {
					$targetUrlParams[] = $k.'['.$k1.']='.$v1;
					
					if(!isset($searchFields[$k.'.'.$k1])) {
						$urlParams[$k.'.'.$k1] = $v1;
					}

					if($formType == 'POST') {
						$formUrlParams[] = $k.'['.$k1.']='.$v1;
					}
				}
			} else {
				$targetUrlParams[] = $k.'='.$v;

				if(!isset($searchFields[$k])) {
					$urlParams[$k] = $v;
				}

				if($formType == 'POST') {
					$formUrlParams[] = $k.'='.$v;
				}
			}
		}
	}

	if(!empty($targetUrlParams)) {
		$targetUrl .= '?'.implode('&', $targetUrlParams);
	}

	if(!empty($formUrlParams)) {
		$formUrl .= '?'.implode('&', $formUrlParams);
	}
}

unset($urlParams['page_limit'], $urlParams['search_clear']);

if(!isset($urlParams['ump_search'])) {
	$urlParams['ump_search'] = 1;
}

if(!empty($pagingAttributes)) {
	$page_limit = $pagingAttributes[$modelName]['perPage'];
}

if($formType == 'GET') {
	$clearUrlParams = [];

	foreach($urlParams as $k=>$v) {
		$k = explode('.', $k);
		
		if(count($k) > 1) {
			$clearUrlParams[] = $k[0].'['.$k[1].']='.urlencode($v);
		} else {
			$clearUrlParams[] = $k[0].'='.urlencode($v);
		}
	}

	if(!empty($clearUrlParams)) {
		$clearUrl .= '?'.implode('&', $clearUrlParams);
	}
}

echo "<div class='usermgmtSearchForm clearfix border-bottom pt-2'>";

	if($isAjax && $formType == 'POST') {?>
		<script type="text/javascript">
			//&lt;![CDATA[
			$(document).ready(function () {
				$("#<?php echo $searchFormId;?>").bind("submit", function (event) {
					$.ajax({
						type: "POST",
						url: "<?php echo $targetUrl;?>",
						async: true,
						beforeSend : function (XMLHttpRequest) {
							$("#loader_modal").modal();
						},
						data: $("#<?php echo $searchFormId;?>").serialize(),
						dataType: "html",
						success: function (data, textStatus) {
							$("#<?php echo $updateDivId;?>").html(data);
							$('#<?php echo $searchFormId;?> .searchClearInput').val(0);

							if(window.history.pushState) {
								window.history.pushState({},"", "<?php echo $targetUrl;?>");
							}
						},
						complete: function (data, textStatus) {
							$("#loader_modal").modal('hide');
						},
						error: function(jqXHR, textStatus, errorThrown) {
							alert(errorThrown)
						}
					});

					return false;
				});
			});
			//]]&gt;
		</script>
	<?php
	}

	echo $this->Form->create(null, ['url'=>$formUrl, 'id'=>$searchFormId, 'role'=>'form', 'type'=>$formType]);
	
	if(!empty($options['legend'])) {
		echo "<div class='pl-2 pt-2 font-weight-bold'>".$options['legend']."</div>";
	}
	
	if($formType == 'POST') {
		echo $this->Form->control('ump_search', ['type'=>'hidden', 'value'=>'1']);
	}
	else {
		foreach($urlParams as $param=>$value) {
			echo $this->Form->control($param, ['type'=>'hidden', 'value'=>$value]);
		}
	}

	if(isset($viewSearchParams)) {
		$jq = "<script type='text/javascript'>";

		foreach($viewSearchParams as $searchParam) {
			if(!$searchParam['options']['adminOnly'] || ($searchParam['options']['adminOnly'] && $this->UserAuth->isAdmin())) {
				$search_options = $searchParam['options'];
				$input_options = $search_options['inputOptions'];
				
				$input_options['label'] = false;
				$input_options['autoComplete'] = "off";
				$input_options['type'] = $search_options['type'];
				$input_options['value'] = $search_options['value'];
				$input_options['default'] = $search_options['default'];
				
				if($search_options['type'] != 'text') {
					$input_options['options'] = $search_options['options'];
				}
				
				if($search_options['type'] == 'checkbox' && isset($search_options['checked'])) {
					unset($input_options['options']);
					$input_options['checked'] = $search_options['checked'];
				}
				
				$input_options['class'] = (isset($input_options['class'])) ? $input_options['class']." form-control" : "form-control";

				$style = '';

				if($search_options['type'] == 'text' || $search_options['type'] == 'select') {
					$style = 'display:inline-block;padding:0 5px;height:30px;';
				}
				else if($search_options['type'] == 'checkbox') {
					$style = 'display:inline-block;margin-0;height:auto;vertical-align:bottom;';
				}

				if(!empty($style)) {
					if(isset($input_options['style'])) {
						$input_options['style'] = $style.$input_options['style'];
					}
					else {
						$input_options['style'] = $style;
					}
				}

				echo "<div class='d-inline-block my-2'>";
					if($search_options['label']) {
						echo "<div class='float-left py-1 pl-3'>".$this->Form->label($search_options['label'], null, ['style'=>'font-size:12px;font-weight:bold;'])."</div>";
					}
					
					echo "<div class='position-relative float-left pb-1 pl-2'>";
						if(!empty($search_options['tagline'])) {
							echo "<span class='font-italic position-absolute' style='font-size:11px;margin-top:30px;'>".$search_options['tagline']."</span>";
						}
						
						echo $this->Form->control($searchParam['modelField'], $input_options);
						
						$loadingId = uniqid();
						
						if($search_options['type'] == 'text' && ($search_options['condition'] != 'multiple' || !empty($search_options['searchFunc']))) {
							echo "<span id='".$loadingId."' class='position-absolute' style='right:5px;top:0; display:none;'>".$this->Html->image(SITE_URL.'usermgmt/img/loading-circle.gif')."</span>";
						}
					echo "</div>";
				echo "</div>";

				if($search_options['type'] == 'text' && ($search_options['condition'] != 'multiple' || !empty($search_options['searchFunc']))) {
					list($fieldModel, $fieldName) = explode('.', $searchParam['modelField']);
					$fieldId = mb_strtolower(Text::slug($searchParam['modelField'], '-'));
					
					$url = '';

					if(!empty($search_options['searchFunc'])) {
						$plugin = false;
						if(!empty($search_options['searchFunc']['plugin'])) {
							$plugin = $search_options['searchFunc']['plugin'];
						}
						
						$url = Router::url(['controller'=>$search_options['searchFunc']['controller'], 'action'=>$search_options['searchFunc']['function'], 'plugin'=>$plugin]);
					} else {
						if($search_options['searchSuggestion']) {
							$url = SITE_URL."usermgmt/Autocomplete/fetch/".$fieldModel."/".$search_options['fieldNameEncrypted'];
						}
					}

					if(!empty($url)) {
						$jq .= "$(function() {
									if($.isFunction($.fn.typeahead)) {
										$('#".$searchFormId." #".$fieldId."').typeahead({
											ajax: {
												url: '".$url."',
												timeout: 500,
												triggerLength: 1,
												method: 'get',
												preDispatch: function (query) {
													$('#".$loadingId."').css('display', '');
													return {
														term: query
													}
												},
												preProcess: function (data) {
													$('#".$loadingId."').hide();
													return data;
												}
											}
										});
									}
								});";
					}
				}
			}
		}

		$jq .= "var clearUrl = '".$clearUrl."'; var searchformtype = '".$formType."';";
		
		$jq .= "$(function() {
					$('#".$searchFormId." .searchClearBtn').click(function(){
						if(searchformtype == 'GET') {
							window.location = clearUrl;
						} else {
							$('#".$searchFormId." .searchClearInput').val(1);
							$('#".$searchFormId." .searchSubmitBtn').trigger('click');
						}
					});

					$('#".$searchFormId." .searchPageLimitInput').change(function() {
						$('#".$searchFormId." .searchSubmitBtn').trigger('click');
					});
				});";
		$jq .= "</script>";
		
		echo $jq;

		echo '<style type="text/css">
			.typeahead .dropdown-item {
				white-space: normal;
				font-size: 80%;
			}
		</style>';
	}

	echo "<div class='float-right px-2 pb-1 mt-2'>";
		echo "<div class='d-inline-block ml-2 align-top'>".$this->Form->submit(__('Search'), ['class'=>'btn btn-primary btn-sm searchSubmitBtn'])."</div>";
		
		if($clear) {
			if($formType == 'POST') {
				$this->Form->unlockField('search_clear');
			}

			echo "<div class='d-inline-block ml-2 align-top'>".$this->Form->hidden('search_clear', ['class'=>'searchClearInput', 'value'=>0])."<button type='button' class='btn btn-danger btn-sm searchClearBtn'>".__('Clear')."</button></div>";
		}

		if(!empty($pagingAttributes)) {
			echo "<div class='d-inline-block ml-2 align-top'>".$this->Form->control('page_limit', ['label'=>false, 'type'=>'select', 'options'=>[''=>'Limit', '10'=>'10', '20'=>'20', '30'=>'30', '40'=>'40', '50'=>'50', '60'=>'60', '70'=>'70', '80'=>'80', '90'=>'90', '100'=>'100', '200'=>'200', '500'=>'500', '1000'=>'1000'], 'value'=>$page_limit, 'autocomplete'=>'off', 'class'=>'form-control searchPageLimitInput m-0 pl-1 pr-0 py-1', 'style'=>'height:30px;'])."</div>";
		}
	echo "</div>";
	
	echo $this->Form->end();
echo "</div>";