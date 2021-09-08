<div class="card">
	<div class="card-header text-white bg-dark">
		<span class="card-title">
			<?php echo __('Edit Setting');?>
		</span>

		<span class="card-title float-right">
			<?php echo $this->Html->link(__('Back', true), ['action'=>'index', '?'=>['page'=>$this->UserAuth->getPageNumber()]], ['class'=>'btn btn-secondary btn-sm']);?>
		</span>
	</div>

	<div class="card-body">
		<div class="card">
			<div class="card-body"><span class="badge badge-danger">Please Note</span> Do not edit "Setting Key" for existing settings, it may break your site code.</div>
		</div>
		<br/>

		<?php echo $this->element('Usermgmt.ajax_validation', ['formId'=>'editSettingForm', 'submitButtonId'=>'editSettingSubmitBtn']);?>
		<?php echo $this->Form->create($settingEntity, ['id'=>'editSettingForm']);?>

		<div class="row form-group">
			<label class="col-md-2 col-form-label required"><?php echo __('Category Type');?></label>
			<div class="col-md-4">
				<?php echo $this->Form->control('UserSettings.category_type', ['type'=>'radio', 'options'=>['existing'=>'Select Existing', 'new'=>'Add New'], 'label'=>false, 'legend'=>false, 'default'=>'existing', 'autocomplete'=>'off', 'class'=>'mx-2']);?>
			</div>
		</div>

		<div class="row form-group existing-category" style="<?php if(isset($settingEntity['category_type']) && $settingEntity['category_type'] != 'existing') { echo 'display:none'; } ?>">
			<label class="col-md-2 col-form-label required"><?php echo __('Category');?></label>
			<div class="col-md-4">
				<?php echo $this->Form->control('UserSettings.setting_category', ['type'=>'select', 'options'=>$settingCategories, 'label'=>false, 'class'=>'form-control']);?>
			</div>
		</div>

		<div class="row form-group new-category" style="<?php if(!isset($settingEntity['category_type']) || $settingEntity['category_type'] != 'new') { echo 'display:none'; } ?>">
			<label class="col-md-2 col-form-label required"><?php echo __('Category');?></label>
			<div class="col-md-4">
				<?php echo $this->Form->control('UserSettings.new_category', ['type'=>'text', 'label'=>false, 'class'=>'form-control']);?>
			</div>
		</div>

		<div class="row form-group">
			<label class="col-md-2 col-form-label required"><?php echo __('Input Type');?></label>
			<div class="col-md-4">
				<?php echo $this->Form->control('UserSettings.setting_type', ['type'=>'select', 'options'=>$settingInputTypes, 'label'=>false, 'class'=>'form-control']);?>
			</div>
		</div>

		<div class="row form-group">
			<label class="col-md-2 col-form-label required"><?php echo __('Setting Key');?></label>
			<div class="col-md-4">
				<?php echo $this->Form->control('UserSettings.setting_key', ['type'=>'text', 'label'=>false, 'class'=>'form-control']);?>
				<span class="form-text"><?php echo __('for ex. site_name_full (no space, only small letters, underscore)');?></span>
			</div>
		</div>

		<div class="row form-group">
			<label class="col-md-2 col-form-label required"><?php echo __('Setting Description');?></label>
			<div class="col-md-4">
				<?php echo $this->Form->control('UserSettings.setting_description', ['type'=>'textarea', 'label'=>false, 'class'=>'form-control']);?>
				<span class="form-text"><?php echo __('for ex. Enter Your Full Site Name');?></span>
			</div>
		</div>

		<div class="row form-group">
			<label class="col-md-2 col-form-label"><?php echo __('Value');?></label>
			<?php
				echo "<div class='col-md-4 inputValue'>".$this->Form->control('UserSettings.input_value', ['type'=>'text', 'label'=>false, 'class'=>'form-control'])."</div>";

				echo "<div class='col-md-1 checkboxValue'>".$this->Form->control('UserSettings.checkbox_value', ['type'=>'checkbox', 'label'=>false, 'style'=>'height: auto'])."</div>";

				echo "<div class='col-md-4 dropdownValue'>".$this->Form->control('UserSettings.dropdown_value', ['type'=>'select', 'options'=>$userSettingOptions, 'empty'=>__('Select'), 'label'=>false, 'class'=>'form-control'])."</div>";

				echo "<div class='col-md-4 radioValue'>".$this->Form->control('UserSettings.radio_value', ['type'=>'radio', 'options'=>$userSettingOptions, 'label'=>false, 'legend'=>false, 'class'=>'form-control'])."</div>";

				echo "<div class='col-md-5 textareaValue'>".$this->Form->control('UserSettings.textarea_value', ['type'=>'textarea', 'label'=>false, 'class'=>'form-control'])."</div>";

				echo "<div class='col-md-8 tinymceValue'>".$this->Tinymce->textarea('UserSettings.tinymce_value', ['type'=>'textarea', 'label'=>false, 'style'=>'height:300px', 'class'=>'form-control'], ['skin'=>'oxide'], 'full')."</div>";

				echo "<div class='col-md-8 ckeditorValue'>".$this->Ckeditor->textarea('UserSettings.ckeditor_value', ['type'=>'textarea', 'label'=>false, 'style'=>'height:300px', 'class'=>'form-control'], [], 'full')."</div>";

				echo $this->Html->link(__('Add/Remove Options', true), '#', ['class'=>'btn btn-primary btn-sm optionLink', 'style'=>'height:32px;']);
			?>
		</div>

		<div class="row form-group border-top pt-3">
			<div class="col">
				<div class="selected_options">
					<?php
					$i = 0;
					foreach($userSettingOptions as $key=>$val) {
						echo "<input type='hidden' value='".$key."' name='UserSettings[options][".$i."]'>";
						$i++;
					}?>
				</div>
				<?php echo $this->Form->Submit(__('Update Setting'), ['id'=>'editSettingSubmitBtn', 'class'=>'btn btn-primary']);?>
			</div>
		</div>

		<?php echo $this->Form->end();?>
	</div>
</div>

<div class="modal fade" role="dialog" aria-labelledby="settingOptionsLabel" id="settingOptionsModal">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-body">
				<div class="card">
					<div class="card-header text-white bg-dark"><?php echo __('Selected Options');?></div>
					
					<div class="card-body">
						<ul class="setting-options selected-setting-options">
						<?php
						foreach($userSettingOptions as $key=>$val) {
							echo "<li>";
								echo "<label for='available-options-".$key."'>";
									echo "<input id='available-options-".$key."' type='checkbox' value='".$key."' name='options[]' checked='checked' class='soption'>";
									echo "<span class='option-title'>".$val."</span>";
								echo "</label>";
							echo "</li>";
						}?>
						</ul>
					</div>
				</div>
				<br/>
				
				<div class="card">
					<div class="card-header text-white bg-dark"><?php echo __('Available Options');?></div>
					<div class="card-body">
						<ul class="setting-options available-setting-options">
						<?php
						foreach($settingOptions as $key=>$val) {
							if(!isset($userSettingOptions[$key])) {
								echo "<li>";
									echo "<label for='available-options-".$key."'>";
										echo "<input id='available-options-".$key."' type='checkbox' value='".$key."' name='options[]' class='soption'>";
										echo "<span class='option-title'>".$val."</span>";
									echo "</label>";
								echo "</li>";
							}
						}?>
						</ul>
					</div>
				</div>
				<br/>
				
				<div class="new-option-section">
					<label><?php echo __('Add New Option');?></label>
					<?php echo $this->Form->control('new_option', ['type'=>'text', 'label'=>false, 'class'=>'form-control']);?>
					<br/>
					<?php echo $this->Html->link(__('Add Option', true), '#', ['class'=>'btn btn-primary btn-sm addNewOption']);?>
				</div>
			</div>
			
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo __('Done');?></button>
			</div>
		</div>
	</div>
</div>

<style type="text/css">
	.radioValue .radio label {
		display:block;
	}
	.setting-options {
		list-style: none;
		padding: 0;
		margin:0;
	}
	.setting-options:after {
		clear:both;
		content:' ';
		display:block;
	}
	.setting-options li {
		float: left;
		margin-bottom: 10px;
		margin-right: 15px;
	}
	.setting-options li label {
		font-weight: normal;
	}
	.setting-options li label input {
		margin-right: 5px;
		margin-top: 0;
	}
</style>

<script type="text/javascript">
	$(function(){
		$("#usersettings-category-type-existing").click(function(e) {
			$(".existing-category").show();
			$(".new-category").hide();
		});
		
		$("#usersettings-category-type-new").click(function(e) {
			$(".new-category").show();
			$(".existing-category").hide();
		});
		
		$("#usersettings-setting-type").change(function(e) {
			$.umpuso.showInput();
		});
		
		$(".optionLink").click(function(e) {
			e.preventDefault();
			$('#settingOptionsModal').modal({
				keyboard: false
			});
		});
		
		$(document).on('click', '.selected-setting-options .soption', function(e) {
			var pelem = $(this).closest('li');
			$('.available-setting-options').append($(pelem));
			$.umpuso.sortOptions('.available-setting-options');
			$.umpuso.updateOptions();
		});
		
		$(document).on('click', '.available-setting-options .soption', function(e) {
			var pelem = $(this).closest('li');
			$('.selected-setting-options').append($(pelem));
			$.umpuso.sortOptions('.selected-setting-options');
			$.umpuso.updateOptions();
		});
		
		var tmpvalue = 9999999999;
		
		$(document).on('click', '.addNewOption', function(e) {
			e.preventDefault();

			$(".new-option-section .error-message").remove();
			
			var newoption = $.trim($(".new-option-section #new-option").val());
			
			if(newoption) {
				var tobeadded = true;
				var alloptions = $('.selected-setting-options li');
				
				$.each(alloptions, function (key, val) {
					if($(this).find('.option-title').html() == newoption) {
						tobeadded = false;
						$(".new-option-section #new-option").closest('.input').after('<div class="error-message" style="color:red;">This option alreay exist in selected options</div>');
						return false;
					}
				});
				
				var alloptions = $('.available-setting-options li');
				
				$.each(alloptions, function (key, val) {
					if($(this).find('.option-title').html() == newoption) {
						tobeadded = false;
						$(".new-option-section #new-option").closest('.input').after('<div class="error-message" style="color:red;">This option alreay exist in available options</div>');
						return false;
					}
				});
				
				if(tobeadded) {
					var html = '<li><label for="available-options-'+tmpvalue+'"><input type="checkbox" class="soption" checked="checked" name="options[]" value="newoption-'+newoption+'" id="available-options-'+tmpvalue+'"><span class="option-title">'+newoption+'</span></label></li>';

					$('.selected-setting-options').append($(html));
					$(".new-option-section #new-option").val('');
					$(".new-option-section #new-option").focus();

					$.umpuso.sortOptions('.selected-setting-options');
					$.umpuso.updateOptions();
					
					tmpvalue++;
				}
			} else {
				alert('Please enter new option value');
			}
		});
		
		$.umpuso.showInput();
	});

	(function($) {
		$.umpuso = {
			updateOptions: function() {
				var alloptions = $('.selected-setting-options li');
				
				if($("#usersettings-setting-type").val() == 'dropdown') {
					var selected = $('#usersettings-dropdown-value').val();
					var dpoptions = '<option selected="selected" value="">Select</option>';
					
					$.each(alloptions, function (key, val) {
						dpoptions += '<option value="'+$(this).find('.soption').val()+'">'+$(this).find('.option-title').html()+'</option>';
					});
					
					$('#usersettings-dropdown-value').html(dpoptions);
					$('#usersettings-dropdown-value').val(selected);
				}
				else if($("#usersettings-setting-type").val() == 'radio') {
					var selected = '';
					if($(".radioValue input[type='radio']:checked").length) {
						selected = $(".radioValue input[type='radio']:checked").val();
					}
					
					var raoptions = '<div class="input radio"><input type="hidden" value="" name="UserSettings[radio_value]">';
					
					$.each(alloptions, function (key, val) {
						val = $(this).find('.soption').val();
						var checked = '';
						
						if(selected == val) {
							var checked = 'checked="checked"';
						}
						
						raoptions += '<label for="usersettings-radio-value-'+val+'"><input type="radio" id="usersettings-radio-value-'+val+'" value="'+val+'" name="UserSettings[radio_value]" '+checked+'>&nbsp;&nbsp;&nbsp;'+$(this).find('.option-title').html()+'</label>';
					});
					
					raoptions += '</div>';
					
					$('.radioValue').html(raoptions);
				}
				
				$('.selected_options').html('');
				
				$.each(alloptions, function (key, val) {
					$('.selected_options').append('<input type="hidden" value="'+$(this).find('.soption').val()+'" name="UserSettings[options]['+key+']">');
				});
			},
			sortOptions: function(idx) {
				var alloptions = $(idx+' li');
				
				alloptions.sort(function(a, b) {
					return $(a).find('.option-title').text().toUpperCase().localeCompare($(b).find('.option-title').text().toUpperCase());
				});
				
				$.each(alloptions, function (key, val) {
					$(idx).append($(this));
				});
			},
			showInput: function() {
				var type = $("#usersettings-setting-type").val();
				
				$(".inputValue, .checkboxValue, .dropdownValue, .radioValue, .tinymceValue, .ckeditorValue, .textareaValue, .optionLink").hide();
				
				if(type) {
					$('.'+type+"Value").show();
					
					if(type == 'dropdown' || type == 'radio') {
						$(".optionLink").show();
						$.umpuso.updateOptions();
					}
				}
			}
		}
	})(jQuery);
</script>