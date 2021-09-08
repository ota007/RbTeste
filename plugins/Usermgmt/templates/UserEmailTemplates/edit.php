<div class="card">
	<div class="card-header text-white bg-dark">
		<span class="card-title">
			<?php echo __('Edit Email Template');?>
		</span>

		<span class="card-title float-right">
			<?php echo $this->Html->link(__('Back', true), ['action'=>'index', '?'=>['page'=>$this->UserAuth->getPageNumber()]], ['class'=>'btn btn-secondary btn-sm']);?>
		</span>
	</div>

	<div class="card-body">
		<?php echo $this->element('Usermgmt.ajax_validation', ['formId'=>'editTemplateForm', 'submitButtonId'=>'editTemplateSubmitBtn']);?>
		<?php echo $this->Form->create($userEmailTemplateEntity, ['id'=>'editTemplateForm']);?>
		
		<div class="row form-group">
			<label class="col-md-2 col-form-label required"><?php echo __('Template Name');?></label>
			<div class="col-md-6">
				<?php echo $this->Form->control('UserEmailTemplates.template_name', ['type'=>'text', 'label'=>false, 'class'=>'form-control']);?>
			</div>
		</div>

		<div class="row form-group">
			<label class="col-md-2 col-form-label"><?php echo __('Email Header');?></label>
			<div class="col-md-6">
				<?php
				if(strtoupper(DEFAULT_HTML_EDITOR) == 'TINYMCE') {
					echo $this->Tinymce->textarea('UserEmailTemplates.template_header', ['type'=>'textarea', 'label'=>false, 'class'=>'form-control'], ['skin'=>'oxide'], 'full');
				}
				else if(strtoupper(DEFAULT_HTML_EDITOR) == 'CKEDITOR') {
					echo $this->Ckeditor->textarea('UserEmailTemplates.template_header', ['type'=>'textarea', 'label'=>false, 'class'=>'form-control'], [], 'full');
				}?>
			</div>
		</div>

		<div class="row form-group">
			<label class="col-md-2 col-form-label"><?php echo __('Email Footer');?></label>
			<div class="col-md-6">
				<?php
				if(strtoupper(DEFAULT_HTML_EDITOR) == 'TINYMCE') {
					echo $this->Tinymce->textarea('UserEmailTemplates.template_footer', ['type'=>'textarea', 'label'=>false, 'class'=>'form-control'], ['skin'=>'oxide'], 'full');
				}
				else if(strtoupper(DEFAULT_HTML_EDITOR) == 'CKEDITOR') {
					echo $this->Ckeditor->textarea('UserEmailTemplates.template_footer', ['type'=>'textarea', 'label'=>false, 'class'=>'form-control'], [], 'full');
				}?>
			</div>
		</div>

		<div class="row form-group border-top pt-3">
			<div class="col">
				<?php echo $this->Form->Submit(__('Update Template'), ['class'=>'btn btn-primary', 'id'=>'editTemplateSubmitBtn']);?>
			</div>
		</div>

		<?php echo $this->Form->end();?>
	</div>
</div>