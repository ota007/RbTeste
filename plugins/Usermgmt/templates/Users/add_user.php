<div class="card">
	<div class="card-header text-white bg-dark">
		<span class="card-title">
			<?php echo __('Add User');?>
		</span>

		<span class="card-title float-right">
			<?php echo $this->Html->link(__('Back', true), ['action'=>'index'], ['class'=>'btn btn-secondary btn-sm']);?>
		</span>
	</div>

	<div class="card-body">
		<?php echo $this->element('Usermgmt.ajax_validation', ['formId'=>'addUserForm', 'submitButtonId'=>'addUserSubmitBtn']);?>
		<?php echo $this->Form->create($userEntity, ['id'=>'addUserForm']);?>

		<div class="row form-group">
			<label class="col-md-2 col-form-label required"><?php echo __('Group');?></label>
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
			<label class="col-md-2 col-form-label required"><?php echo __('Password');?></label>
			<div class="col-md-4">
				<?php echo $this->Form->control('Users.password', ['type'=>'password', 'label'=>false, 'class'=>'form-control']);?>
			</div>
		</div>

		<div class="row form-group">
			<label class="col-md-2 col-form-label required"><?php echo __('Confirm Password');?></label>
			<div class="col-md-4">
				<?php echo $this->Form->control('Users.cpassword', ['type'=>'password', 'label'=>false, 'class'=>'form-control']);?>
			</div>
		</div>

		<div class="row form-group border-top pt-3">
			<div class="col">
				<?php echo $this->Form->Submit(__('Add User'), ['class'=>'btn btn-primary', 'id'=>'addUserSubmitBtn']);?>
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
	});
</script>