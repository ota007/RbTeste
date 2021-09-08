<div class="card">
	<div class="card-header text-white bg-dark">
		<span class="card-title">
			<?php echo __('Edit User');?>
		</span>

		<span class="card-title float-right">
			<?php echo $this->Html->link(__('Back', true), ['action'=>'index', '?'=>['page'=>$this->UserAuth->getPageNumber()]], ['class'=>'btn btn-secondary btn-sm']);?>
		</span>
	</div>

	<div class="card-body">
		<?php echo $this->element('Usermgmt.ajax_validation', ['formId'=>'editUserForm', 'submitButtonId'=>'editUserSubmitBtn']);?>
		<?php echo $this->Form->create($userEntity, ['id'=>'editUserForm', 'type'=>'file']);?>

		<div class="row form-group">
			<label class="col-md-2 col-form-label required"><?php echo __('User Group');?></label>
			<div class="col-md-4">
				<?php echo $this->Form->control('Users.user_group_id', ['type'=>'select', 'label'=>false, 'multiple'=>true, 'class'=>'form-control user_group_id_input', 'data-placeholder'=>'Select Group(s)']);?>
			</div>
		</div>

		<div class="row form-group">
			<label class="col-md-2 col-form-label required"><?php echo __('Username');?></label>
			<div class="col-md-4">
				<?php echo $this->Form->control('Users.username', ['type'=>'text', 'label'=>false, 'class'=>'form-control']);?>
			</div>
		</div>

		<div class="row form-group">
			<label class="col-md-2 col-form-label required"><?php echo __('First Name');?></label>
			<div class="col-md-4">
				<?php echo $this->Form->control('Users.first_name', ['type'=>'text', 'label'=>false, 'class'=>'form-control']);?>
			</div>
		</div>

		<div class="row form-group">
			<label class="col-md-2 col-form-label"><?php echo __('Last Name');?></label>
			<div class="col-md-4">
				<?php echo $this->Form->control('Users.last_name', ['type'=>'text', 'label'=>false, 'class'=>'form-control']);?>
			</div>
		</div>

		<div class="row form-group">
			<label class="col-md-2 col-form-label required"><?php echo __('Email');?></label>
			<div class="col-md-4">
				<?php echo $this->Form->control('Users.email', ['type'=>'text', 'label'=>false, 'class'=>'form-control']);?>
			</div>
		</div>

		<div class="row form-group">
			<label class="col-md-2 col-form-label"><?php echo __('Gender');?></label>
			<div class="col-md-4">
				<?php echo $this->Form->control('Users.gender', ['type'=>'radio', 'label'=>false, 'options'=>$genders, 'class'=>'mx-2']);?>
			</div>
		</div>

		<div class="row form-group">
			<label class="col-md-2 col-form-label"><?php echo __('Birthday');?></label>
			<div class="col-md-4">
				<?php echo $this->Form->control('Users.bday', ['type'=>'text', 'label'=>false, 'class'=>'form-control datepicker']);?>
			</div>
		</div>

		<div class="row form-group">
			<label class="col-md-2 col-form-label"><?php echo __('Photo');?></label>
			<div class="col-md-4">
				<?php $this->Form->unlockField('Users.photo_file');?>
				<?php echo $this->Form->control('Users.photo_file', ['type'=>'file', 'label'=>false]);?>
			</div>
		</div>

		<div class="row form-group">
			<label class="col-md-2 col-form-label"><?php echo __('Location');?></label>
			<div class="col-md-4">
				<?php echo $this->Form->control('Users.user_detail.location', ['type'=>'text', 'label'=>false, 'class'=>'form-control']);?>
			</div>
		</div>

		<div class="row form-group">
			<label class="col-md-2 col-form-label"><?php echo __('Cellphone');?></label>
			<div class="col-md-4">
				<?php echo $this->Form->control('Users.user_detail.cellphone', ['type'=>'text', 'label'=>false, 'class'=>'form-control']);?>
			</div>
		</div>

		<div class="row form-group border-top pt-3">
			<div class="col">
				<?php echo $this->Form->Submit(__('Save User'), ['class'=>'btn btn-primary', 'id'=>'editUserSubmitBtn']);?>
			</div>
		</div>

		<?php echo $this->Form->end();?>
	</div>
</div>

<script type="text/javascript">
	$(function(){
		$(".user_group_id_input").select2({
			//options
		});

		$(document).on("focus", ".datepicker", function() {
			$(this).datepicker({
				format: 'dd-M-yyyy',
				autoclose: true
			});
		});
	});
</script>