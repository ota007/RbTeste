<?php $this->layout = "CakeLte.login" ?>

<div class="card">
	<div class="card-body register-card-body">
		<p class="login-box-msg"><?= __('Register a new membership') ?></p>
		<span class="card-title text-center">
			<?php echo $this->Html->link(__('Sign In', true), ['controller' => 'Users', 'action' => 'login', 'plugin' => 'Usermgmt'], ['class' => 'btn btn-secondary btn-sm']); ?>
		</span>

		<?php echo $this->element('Usermgmt.ajax_validation', ['formId' => 'registerForm', 'submitButtonId' => 'registerSubmitBtn']); ?>
		<?php echo $this->Form->create($userEntity, ['id' => 'registerForm', 'novalidate' => true]); ?>

		<?= $this->Form->create() ?>

		<?= $this->Form->control('Users.user_group_id', [
			'type' => 'select',
			'options' => $userGroups,
			'label' => false,
			'class' => 'form-control'
		]); ?>

		<?= $this->Form->control('Users.username', [
			'type' => 'text',
			'label' => false,
			'placeholder' => __('Username'),
			'class' => 'form-control',
			'append' => '<i class="fas fa-user"></i>'
		]); ?>

		<?= $this->Form->control('Users.first_name', [
			'type' => 'text',
			'placeholder' => __('First Name'),
			'label' => false,
			'class' => 'form-control',
			'append' => '<i class="fas fa-user"></i>'
		]); ?>

		<?= $this->Form->control('Users.last_name', [
			'type' => 'text',
			'placeholder' => __('Last Name'),
			'label' => false,
			'class' => 'form-control',
			'append' => '<i class="fas fa-user"></i>'
		]); ?>


		<?= $this->Form->control('Users.email', [
			'type' => 'text',
			'label' => false,
			'placeholder' => __('Email'),
			'class' => 'form-control',
			'append' => '<i class="fas fa-envelope"></i>'
		]); ?>


		<?= $this->Form->control('Users.password', [
			'type' => 'password',
			'label' => false,
			'placeholder' => __('Password'),
			'class' => 'form-control',
			'append' => '<i class="fas fa-lock"></i>'
		]) ?>

		<?= $this->Form->control('Users.cpassword', [
			'type' => 'password',
			'label' => false,
			'class' => 'form-control',
			'placeholder' => __('Confirm Password'),
			'append' => '<i class="fas fa-lock"></i>'
		]) ?>

		<div class="row">
			<div class="col-8">
				<?= $this->Form->control('agree_terms', [
					'label' => 'I agree to the <a href="#">terms</a>',
					'type' => 'checkbox',
					'custom' => true,
					'escape' => false
				]) ?>
			</div>
			<div class="col-4">
				<?= $this->Form->Submit(__('Register'), ['class' => 'btn btn-primary btn-block', 'id' => 'registerSubmitBtn']); ?>
			</div>

		</div>

		<?= $this->Form->end() ?>

		<div class="social-auth-links text-center mb-3">
			<p>- OR -</p>
			<?php
			echo $this->Html->link(
				'<i class="fab fa-facebook-f mr-2"></i>' . __('Sign up using Facebook'),
				'#',
				['class' => 'btn btn-block btn-primary', 'escape' => false]
			);
			?>
			<?php
			echo $this->Html->link(
				'<i class="fab fa-google mr-2"></i>' . __('Sign up using Google'),
				'#',
				['class' => 'btn btn-block btn-danger', 'escape' => false]
			);
			?>
		</div>
		<!-- /.social-auth-links -->
	</div>
	<!-- /.register-card-body -->
</div>