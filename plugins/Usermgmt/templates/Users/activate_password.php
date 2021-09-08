<div class="row justify-content-center">
	<div class="col-lg-6">
		<div class="card">
			<div class="card-header text-white bg-dark">
				<span class="card-title">
					<?php echo __('Reset Password');?>
				</span>
			</div>

			<div class="card-body">
				<?php echo $this->Form->create($userEntity, ['novalidate'=>true]);?>

				<div class="row form-group">
					<label class="col-md-4 col-form-label required"><?php echo __('New Password');?></label>
					<div class="col-md-8">
						<?php echo $this->Form->control('Users.password', ['type'=>'password', 'label'=>false, 'class'=>'form-control']);?>
					</div>
				</div>

				<div class="row form-group">
					<label class="col-md-4 col-form-label required"><?php echo __('Confirm Password');?></label>
					<div class="col-md-8">
						<?php echo $this->Form->control('Users.cpassword', ['type'=>'password', 'label'=>false, 'class'=>'form-control']);?>
					</div>
				</div>

				<div class="row form-group border-top pt-3">
					<div class="col">
						<?php echo $this->Form->Submit(__('Save New Password'), ['class'=>'btn btn-primary']);?>
					</div>
				</div>

				<?php echo $this->Form->end();?>
			</div>
		</div>
	</div>
</div>