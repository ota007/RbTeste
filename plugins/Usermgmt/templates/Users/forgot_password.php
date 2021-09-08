<div class="row justify-content-center">
	<div class="col-lg-6">
		<div class="card">
			<div class="card-header text-white bg-dark">
				<span class="card-title">
					<?php echo __('Forgot Password');?>
				</span>
			</div>

			<div class="card-body">
				<?php echo $this->Form->create($userEntity, ['novalidate'=>true]);?>

				<div class="row form-group">
					<label class="col-md-4 col-form-label required"><?php echo __('Enter Email / Username');?></label>
					<div class="col-md-8">
						<?php echo $this->Form->control('Users.email', ['type'=>'text', 'label'=>false, 'class'=>'form-control']);?>
					</div>
				</div>

				<?php
				if($this->UserAuth->canUseRecaptha('forgotPassword')) {
					$errors = $userEntity->getErrors();
					$error = "";
				
					if(isset($errors['captcha'])) {
						foreach($errors['captcha'] as $er) {
							$error = $er;
						}	
					}?>

					<div class="row form-group">
						<label class="col-md-4 col-form-label required"><?php echo __('Prove you\'re not a robot');?></label>
						<div class="col-md-8">
							<?php echo $this->UserAuth->showCaptcha($error);?>
						</div>
					</div>
				<?php
				}?>

				<div class="row form-group border-top pt-3">
					<div class="col">
						<?php echo $this->Form->Submit(__('Send Email'), ['class'=>'btn btn-primary']);?>
					</div>
				</div>

				<?php echo $this->Form->end();?>
			</div>
		</div>
	</div>
</div>