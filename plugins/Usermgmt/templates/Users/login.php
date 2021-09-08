<?php $this->layout = "CakeLte.login" ?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Document</title>
</head>
<body class="login-page" >
<div class="card">

<div class="card-body login-card-body">

	<p class="login-box-msg"><?= __('Sign in to start your session') ?></p>


	<?php echo $this->element('Usermgmt.ajax_validation', ['formId' => 'loginForm', 'submitButtonId' => 'loginSubmitBtn']); ?>
	<?php echo $this->Form->create($userEntity, ['id' => 'loginForm']); ?>
	<?= $this->Form->control('Users.email', ['type' => 'text', 'label' => false, 'class' => 'form-control', 'placeholder' => __('Username'), 'append' => '<i class="fas fa-user"></i>',]) ?>
	<?= $this->Form->control('Users.password', ['type' => 'password', 'label' => false, 'class' => 'form-control', 'placeholder' => __('Password'), 'append' => '<i class="fas fa-lock"></i>']); ?>

	<div class="row">

		<div class="col-8">
			<?php
			if (USE_REMEMBER_ME) {
				if (!isset($userEntity['remember'])) {
					$userEntity['remember'] = false;
				} ?>

				<div class="row form-group">

					<div class="col-md-1">
						<?php echo $this->Form->control('Users.remember', ['type' => 'checkbox', 'label' => false]); ?>
					</div>
					<label class="col-md-8"><?php echo __('Remember'); ?></label>
				</div>

			<?php }
			if ($this->UserAuth->canUseRecaptha('login')) {
				$errors = $userEntity->getErrors();
				$error = "";
				if (isset($errors['captcha'])) {
					foreach ($errors['captcha'] as $er) {
						$error = $er;
					}
				}
			} ?>
		</div>
		<!-- /.col -->
		<div class="col-4">
			<?= $this->Form->Submit(__('Sign In'), ['class' => 'btn btn-primary', 'id' => 'loginSubmitBtn']); ?>
		</div>
		<!-- /.col -->
	</div>

	<?= $this->Form->end() ?>
	<div class="social-auth-links text-center mb-3">
		<p>- OR -</p>
		<?php
		echo $this->Html->link(
			'<i class="fab fa-facebook-f mr-2"></i>' . __('Sign in using Facebook'),
			'#',
			['class' => 'btn btn-block btn-primary', 'escape' => false]
		);
		?>
		<?php
		echo $this->Html->link(
			'<i class="fab fa-google mr-2"></i>' . __('Sign in using Google'),
			'#',
			['class' => 'btn btn-block btn-danger', 'escape' => false]
		);
		?>
	</div>
	<!-- /.social-auth-links -->
	<p class="mb-1">
		<?php if (SITE_REGISTRATION) { ?>
			<?php echo $this->Html->link(__('Sign Up', true), ['controller' => 'Users', 'action' => 'register', 'plugin' => 'Usermgmt']); ?>
		<?php } ?>

	</p>

	<p class="mb-1">
		<?= $this->Html->link(__('Forgot Password?'), '/forgotPassword'); ?>
	</p>
	<p class="mb-0">
		<?= $this->Html->link(__('Email Verification'), '/emailVerification'); ?>
	</p>
</div>
<!-- /.login-card-body -->
</div>
</body>
</html>

