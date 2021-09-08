<div class="card">
	<div class="card-header text-white bg-dark">
		<span class="card-title">
			<?php echo __('Edit Setting Option');?>
		</span>
		
		<span class="card-title float-right">
			<?php echo $this->Html->link(__('Back', true), ['action'=>'index'], ['class'=>'btn btn-secondary btn-sm']);?>
		</span>
	</div>

	<div class="card-body">
		<?php echo $this->element('Usermgmt.ajax_validation', ['formId'=>'editSettingOptionForm', 'submitButtonId'=>'editSettingOptionSubmitBtn']);?>
		<?php echo $this->Form->create($settingOptionEntity, ['id'=>'editSettingOptionForm']);?>

		<div class="row form-group">
			<label class="col-md-2 col-form-label required"><?php echo __('Title');?></label>
			<div class="col-md-4">
				<?php echo $this->Form->control('SettingOptions.title', ['type'=>'text', 'label'=>false, 'class'=>'form-control']);?>
			</div>
		</div>
		
		<div class="row form-group border-top pt-3">
			<div class="col">
				<?php echo $this->Form->Submit(__('Update Option'), ['class'=>'btn btn-primary', 'id'=>'editSettingOptionSubmitBtn']);?>
			</div>
		</div>
		
		<?php echo $this->Form->end();?>
	</div>
</div>