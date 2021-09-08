<?php echo $this->element('Usermgmt.ajax_validation', ['formId'=>'contactForm', 'submitButtonId'=>'contactSubmitBtn']);?>

<div style="padding:10px">
	<?php echo $this->Form->create($userContactEntity, ['url'=>['controller'=>'UserContacts', 'action'=>'contactUs', 'plugin'=>'Usermgmt'], 'id'=>'contactForm']);?>

	<?php echo $this->Form->control('UserContacts.name', ['type'=>'text', 'label'=>false, 'placeholder'=>__('Name'), 'title'=>__('Name'), 'class'=>'form-control']);?><br/>

	<?php echo $this->Form->control('UserContacts.email', ['type'=>'text', 'label'=>false, 'placeholder'=>__('Email'), 'title'=>__('Email'), 'class'=>'form-control']);?><br/>

	<?php echo $this->Form->control('UserContacts.phone', ['type'=>'text', 'label'=>false, 'placeholder'=>__('Contact No'), 'title'=>__('Contact No'), 'class'=>'form-control']);?><br/>

	<?php echo $this->Form->control('UserContacts.requirement', ['type'=>'textarea', 'label'=>false, 'placeholder'=>__('Requirement'), 'title'=>__('Requirement'), 'class'=>'form-control']);?><br/>

	<?php
	if($this->UserAuth->canUseRecaptha('contactus')) {
		$errors = $userContactEntity->getErrors();
		$error = "";
		
		if(isset($errors['captcha'])) {
			foreach($errors['captcha'] as $er) {
				$error = $er;
			}	
		}
		
		echo $this->UserAuth->showCaptcha($error);
		echo "<br/>";
	}?>

	<?php echo $this->Form->Submit(__('Submit'), ['id'=>'contactSubmitBtn', 'class'=>'btn btn-primary']);?>
	
	<?php echo $this->Form->end();?>
</div>