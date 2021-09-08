<div class="card">
	<div class="card-header text-white bg-dark">
		<span class="card-title">
			<?php echo __('Add Setting');?>
		</span>

		<span class="card-title float-right">
			<?php echo $this->Html->link(__('Back', true), ['action'=>'index'], ['class'=>'btn btn-secondary btn-sm']);?>
		</span>
	</div>

	<div class="card-body">
		<?php echo $this->element('Usermgmt.ajax_validation', ['formId'=>'addSettingForm', 'submitButtonId'=>'addSettingSubmitBtn']);?>
		<?php echo $this->Form->create($settingEntity, ['id'=>'addSettingForm']);?>

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

		<div class="row form-group border-top pt-3">
			<div class="col">
				<?php echo $this->Form->Submit(__('Next'), ['id'=>'addSettingSubmitBtn', 'class'=>'btn btn-primary']);?>
			</div>
		</div>

		<?php echo $this->Form->end();?>
	</div>
</div>

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
	});
</script>