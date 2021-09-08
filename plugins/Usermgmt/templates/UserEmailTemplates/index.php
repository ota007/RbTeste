<div class="card">
	<div class="card-header text-white bg-dark">
		<span class="card-title">
			<?php echo __('All Email Templates');?>
		</span>

		<span class="card-title float-right">
			<?php echo $this->Html->link(__('Add Template', true), ['action'=>'add'], ['class'=>'btn btn-secondary btn-sm']);?>
		</span>
	</div>

	<div class="card-body p-0">
		<?php echo $this->element('../UserEmailTemplates/all_user_email_templates');?>
	</div>
</div>