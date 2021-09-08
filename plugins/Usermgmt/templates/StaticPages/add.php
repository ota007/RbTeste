<div class="card">
	<div class="card-header text-white bg-dark">
		<span class="card-title">
			<?php echo __('Add Static Page');?>
		</span>

		<span class="card-title float-right">
			<?php echo $this->Html->link(__('Back', true), ['action'=>'index'], ['class'=>'btn btn-secondary btn-sm']);?>
		</span>
	</div>

	<div class="card-body">
		<?php echo $this->element('Usermgmt.ajax_validation', ['formId'=>'addPageForm', 'submitButtonId'=>'addPageSubmitBtn']);?>
		<?php echo $this->Form->create($staticPageEntity, ['id'=>'addPageForm']);?>
		
		<div class="row form-group">
			<label class="col-md-2 col-form-label required"><?php echo __('Page Name');?></label>
			<div class="col-md-5">
				<?php echo $this->Form->control('StaticPages.page_name', ['type'=>'text', 'label'=>false, 'class'=>'form-control']);?>
				<?php echo __('For ex. Contact Us, About Us');?>
			</div>
		</div>

		<div class="row form-group">
			<label class="col-md-2 col-form-label required"><?php echo __('Url Name');?></label>
			<div class="col-md-5">
				<?php echo $this->Form->control('StaticPages.url_name', ['type'=>'text', 'label'=>false, 'class'=>'form-control']);?>
				<?php echo __('For ex. contactus, contactus.html, aboutus, aboutus.html');?>
			</div>
		</div>

		<div class="row form-group">
			<label class="col-md-2 col-form-label"><?php echo __('Page Title');?></label>
			<div class="col-md-5">
				<?php echo $this->Form->control('StaticPages.page_title', ['type'=>'text', 'label'=>false, 'class'=>'form-control']);?>
				<?php echo __('For ex. Your Contact Details');?>
			</div>
		</div>

		<div class="row form-group">
			<label class="col-md-2 col-form-label required"><?php echo __('Page Content');?></label>
			<div class="col-md-8">
				<?php
				if(strtoupper(DEFAULT_HTML_EDITOR) == 'TINYMCE') {
					echo $this->Tinymce->textarea('StaticPages.page_content', ['type'=>'textarea', 'label'=>false, 'style'=>'height:800px', 'class'=>'form-control'], ['skin'=>'oxide'], 'full');
				}
				else if(strtoupper(DEFAULT_HTML_EDITOR) == 'CKEDITOR') {
					echo $this->Ckeditor->textarea('StaticPages.page_content', ['type'=>'textarea', 'label'=>false, 'style'=>'height:800px', 'class'=>'form-control'], [], 'full');
				}?>
			</div>
		</div>

		<div class="row form-group border-top pt-3">
			<div class="col">
				<?php echo $this->Form->Submit(__('Add Page'), ['class'=>'btn btn-primary', 'id'=>'addPageSubmitBtn']);?>
			</div>
		</div>

		<?php echo $this->Form->end();?>
	</div>
</div>