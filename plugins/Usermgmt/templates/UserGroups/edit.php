<div class="card">
	<div class="card-header text-white bg-dark">
		<span class="card-title">
			<?php echo __('Edit User Group');?>
		</span>

		<span class="card-title float-right">
			<?php echo $this->Html->link(__('Back', true), ['action'=>'index', '?'=>['page'=>$this->UserAuth->getPageNumber()]], ['class'=>'btn btn-secondary btn-sm']);?>
		</span>
	</div>

	<div class="card-body">
		<?php echo $this->element('Usermgmt.ajax_validation', ['formId'=>'editUserGroupForm', 'submitButtonId'=>'editUserGroupSubmitBtn']);?>
		<?php echo $this->Form->create($userGroupEntity, ['id'=>'editUserGroupForm', 'novalidate'=>true]);?>

		<div class="row form-group">
			<label class="col-md-2 col-form-label required"><?php echo __('Group Name');?></label>
			<div class="col-md-4">
				<?php echo $this->Form->control('UserGroups.name', ['type'=>'text', 'label'=>false, 'class'=>'form-control']);?>
				<span class="tagline"><?php echo __('for ex. Business User');?></span>
			</div>
		</div>

		<div class="row form-group">
			<label class="col-md-2 col-form-label"><?php echo __('Parent Group');?></label>
			<div class="col-md-4">
				<?php echo $this->Form->control('UserGroups.parent_id', ['type'=>'select', 'options'=>$parentGroups, 'label'=>false, 'class'=>'form-control']);?>
			</div>
		</div>

		<div class="row form-group">
			<label class="col-md-2 col-form-label"><?php echo __('Description');?></label>
			<div class="col-md-4">
				<?php echo $this->Form->control('UserGroups.description', ['type'=>'textarea', 'label'=>false, 'class'=>'form-control']);?>
			</div>
		</div>

		<div class="row form-group">
			<label class="col-md-2 col-form-label"><?php echo __('New Registration Allowed?');?></label>
			<div class="col-md-1">
				<?php echo $this->Form->control('UserGroups.is_registration_allowed', ['type'=>'checkbox', 'label'=>false, 'autocomplete'=>'off', 'class'=>'ml-0']);?>
			</div>
		</div>

		<div class="row form-group border-top pt-3">
			<div class="col">
				<?php echo $this->Form->Submit(__('Save'), ['class'=>'btn btn-primary', 'id'=>'editUserGroupSubmitBtn']);?>
			</div>
		</div>

		<?php echo $this->Form->end();?>
	</div>
</div>