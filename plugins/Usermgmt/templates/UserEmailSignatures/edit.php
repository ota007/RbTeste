<div class="card">
	<div class="card-header text-white bg-dark">
		<span class="card-title">
			<?php echo __('Edit Signature');?>
		</span>

		<span class="card-title float-right">
			<?php echo $this->Html->link(__('Back', true), ['action'=>'index', '?'=>['page'=>$this->UserAuth->getPageNumber()]], ['class'=>'btn btn-secondary btn-sm']);?>
		</span>
	</div>

	<div class="card-body">
		<?php echo $this->element('Usermgmt.ajax_validation', ['formId'=>'editSignatureForm', 'submitButtonId'=>'editSignatureSubmitBtn']);?>
		<?php echo $this->Form->create($userEmailSignatureEntity, ['id'=>'editSignatureForm']);?>

		<div class="row form-group">
			<label class="col-md-2 col-form-label required"><?php echo __('Signature Name');?></label>
			<div class="col-md-6">
				<?php echo $this->Form->control('UserEmailSignatures.signature_name', ['type'=>'text', 'label'=>false, 'class'=>'form-control']);?>
			</div>
		</div>

		<div class="row form-group">
			<label class="col-md-2 col-form-label"><?php echo __('Email Signature');?></label>
			<div class="col-md-6">
				<?php
				if(strtoupper(DEFAULT_HTML_EDITOR) == 'TINYMCE') {
					echo $this->Tinymce->textarea('UserEmailSignatures.signature', ['type'=>'textarea', 'label'=>false, 'class'=>'form-control'], ['skin'=>'oxide'], 'full');
				}
				else if(strtoupper(DEFAULT_HTML_EDITOR) == 'CKEDITOR') {
					echo $this->Ckeditor->textarea('UserEmailSignatures.signature', ['type'=>'textarea', 'label'=>false, 'class'=>'form-control'], [], 'full');
				}?>
			</div>
		</div>

		<div class="row form-group border-top pt-3">
			<div class="col">
				<?php echo $this->Form->Submit(__('Update Signature'), ['class'=>'btn btn-primary', 'id'=>'editSignatureSubmitBtn']);?>
			</div>
		</div>

		<?php echo $this->Form->end();?>
	</div>
</div>