<div class="card">
	<div class="card-header text-white bg-dark">
		<span class="card-title">
			<?php echo __('Change Password');?>
		</span>
	</div>

	<div class="card-body">
		<?php echo $this->Form->create($userEntity, ['novalidate'=>true]);?>

		<?php
		if(!$this->request->getSession()->check('Auth.SocialChangePassword')) {?>
			<div class="row form-group">
				<label class="col-md-2 col-form-label required"><?php echo __('Old Password');?></label>
				<div class="col-md-4">
					<?php echo $this->Form->control('Users.oldpassword', ['type'=>'password', 'label'=>false, 'class'=>'form-control']);?>
				</div>
			</div>
		<?php
		}?>

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
				<?php echo $this->Form->Submit(__('Change Password'), ['class'=>'btn btn-primary', 'id'=>'changePasswordSubmitBtn']);?>
			</div>
		</div>

		<?php echo $this->Form->end();?>
	</div>
</div>