<div class="card">
	<div class="card-header text-white bg-dark">
		<span class="card-title">
			<?php echo __('Send Reply to').' '.$userContact['name'];?>
		</span>

		<span class="card-title float-right">
			<?php echo $this->Html->link(__('Back', true), ['action'=>'index', '?'=>['page'=>$this->UserAuth->getPageNumber()]], ['class'=>'btn btn-secondary btn-sm']);?>
		</span>
	</div>

	<div class="card-body">
		<?php echo $this->element('Usermgmt.ajax_validation', ['formId'=>'sendMailForm', 'submitButtonId'=>'sendMailSubmitBtn']);?>
		<?php echo $this->Form->create($userEmailEntity, ['id'=>'sendMailForm']);?>

		<div class="row form-group">
			<label class="col-md-2 col-form-label required"><?php echo __('To');?></label>
			<div class="col-md-5">
				<?php echo $this->Form->control('UserEmails.to', ['type'=>'text', 'label'=>false, 'class'=>'form-control']);?>
			</div>
		</div>

		<div class="row form-group">
			<label class="col-md-2 col-form-label"><?php echo __('CC To');?></label>
			<div class="col-md-5">
				<?php echo $this->Form->control('UserEmails.cc_to', ['type'=>'text', 'label'=>false, 'class'=>'form-control']);?>
				<span class="tagline"><?php echo __('multiple emails comma separated');?></span>
			</div>
		</div>

		<div class="row form-group">
			<label class="col-md-2 col-form-label required"><?php echo __('From Name');?></label>
			<div class="col-md-5">
				<?php echo $this->Form->control('UserEmails.from_name', ['type'=>'text', 'label'=>false, 'class'=>'form-control']);?>
			</div>
		</div>

		<div class="row form-group">
			<label class="col-md-2 col-form-label required"><?php echo __('From Email');?></label>
			<div class="col-md-5">
				<?php echo $this->Form->control('UserEmails.from_email', ['type'=>'text', 'label'=>false, 'class'=>'form-control']);?>
			</div>
		</div>

		<div class="row form-group">
			<label class="col-md-2 col-form-label"><?php echo __('Select Template');?></label>
			<div class="col-md-5">
				<?php echo $this->Form->control('UserEmails.template', ['type'=>'select', 'options'=>$templates, 'label'=>false, 'autocomplete'=>'off', 'class'=>'form-control']);?>
			</div>
		</div>

		<div class="row form-group">
			<label class="col-md-2 col-form-label"><?php echo __('Select Signature');?></label>
			<div class="col-md-5">
				<?php echo $this->Form->control('UserEmails.signature', ['type'=>'select', 'options'=>$signatures, 'label'=>false, 'autocomplete'=>'off', 'class'=>'form-control']);?>
			</div>
		</div>

		<div class="row form-group">
			<label class="col-md-2 col-form-label required"><?php echo __('Subject');?></label>
			<div class="col-md-5">
				<?php echo $this->Form->control('UserEmails.subject', ['type'=>'text', 'label'=>false, 'class'=>'form-control']);?>
			</div>
		</div>

		<div class="row form-group">
			<label class="col-md-2 col-form-label required"><?php echo __('Message');?></label>
			<div class="col-md-8">
				<?php
				if(strtoupper(DEFAULT_HTML_EDITOR) == 'TINYMCE') {
					echo $this->Tinymce->textarea('UserEmails.message', ['type'=>'textarea', 'label'=>false, 'style'=>'height:400px', 'class'=>'form-control'], ['skin'=>'oxide'], 'full');
				}
				else if(strtoupper(DEFAULT_HTML_EDITOR) == 'CKEDITOR') {
					echo $this->Ckeditor->textarea('UserEmails.message', ['type'=>'textarea', 'label'=>false, 'style'=>'height:400px', 'class'=>'form-control'], [], 'full');
				}?>
			</div>
		</div>

		<div class="row form-group border-top pt-3">
			<div class="col">
				<?php echo $this->Form->Submit(__('Next'), ['class'=>'btn btn-primary', 'id'=>'sendMailSubmitBtn']);?>
			</div>
		</div>

		<?php echo $this->Form->end();?>
	</div>
</div>