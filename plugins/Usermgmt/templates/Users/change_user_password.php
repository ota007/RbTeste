<div class="card">
	<div class="card-header text-white bg-dark">
		<span class="card-title">
			<?php echo __('Change Password for').' '.$user['first_name'].' '.$user['last_name'];?>
		</span>

		<span class="card-title float-right">
			<?php echo $this->Html->link(__('Back', true), ['action'=>'index', '?'=>['page'=>$this->UserAuth->getPageNumber()]], ['class'=>'btn btn-secondary btn-sm']);?>
		</span>
	</div>

	<div class="card-body">
		<?php echo $this->element('Usermgmt.ajax_validation', ['formId'=>'changeUserPasswordForm', 'submitButtonId'=>'changeUserPasswordSubmitBtn']);?>
		<?php echo $this->Form->create($userEntity, ['id'=>'changeUserPasswordForm']);?>

		<div class="row form-group">
			<label class="col-md-2 col-form-label required"><?php echo __('New Password');?></label>
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
				<?php echo $this->Form->Submit(__('Change Password'), ['class'=>'btn btn-primary', 'id'=>'changeUserPasswordSubmitBtn']);?>
			</div>
		</div>

		<?php echo $this->Form->end();?>
	</div>
</div>