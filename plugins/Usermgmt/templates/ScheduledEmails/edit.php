<div class="card">
	<div class="card-header text-white bg-dark">
		<span class="card-title">
			<?php echo __('Edit Scheduled Email');?>
		</span>

		<span class="card-title float-right">
			<?php echo $this->Html->link(__('Back', true), ['action'=>'index', '?'=>['page'=>$this->UserAuth->getPageNumber()]], ['class'=>'btn btn-secondary btn-sm']);?>
		</span>
	</div>

	<div class="card-body">
		<?php echo $this->element('Usermgmt.ajax_validation', ['formId'=>'scheduledEmailForm', 'submitButtonId'=>'scheduledEmailSubmitBtn']);?>
		<?php echo $this->Form->create($scheduledEmailEntity, ['id'=>'scheduledEmailForm', 'novalidate'=>true]);?>

		<div class="row form-group">
			<label class="col-md-2 col-form-label"><?php echo __('Type');?></label>
			<div class="col-md-5">
				<?php
				if($scheduledEmailEntity['type'] == 'USERS') {
					echo __('Selected Users');
				}
				else if($scheduledEmailEntity['type'] == 'GROUPS') {
					echo __('Group Users');
				}
				else {
					echo __('Manual Emails');
				}?>
			</div>
		</div>

		<?php
		if($scheduledEmailEntity['type'] == 'GROUPS') {?>
			<div class="row form-group">
				<label class="col-md-2 col-form-label"><?php echo __('Group(s)');?></label>
				<div class="col-md-5">
					<?php
					$groupNames = [];
					$scheduledEmailEntity['user_group_id'] = explode(',',  strval($scheduledEmailEntity['user_group_id']));
					
					foreach($scheduledEmailEntity['user_group_id'] as $groupId) {
						$groupNames[] = $groups[$groupId];
					}
					
					echo implode(', ', $groupNames);?>
				</div>
			</div>
		<?php
		}?>

		<div class="row form-group">
			<label class="col-md-2 col-form-label"><?php echo __('CC To');?></label>
			<div class="col-md-5">
				<?php echo $this->Form->control('ScheduledEmails.cc_to', ['type'=>'text', 'label'=>false, 'class'=>'form-control']);?>
				<span class="tagline"><?php echo __('multiple emails comma separated');?></span>
			</div>
		</div>

		<div class="row form-group">
			<label class="col-md-2 col-form-label required"><?php echo __('From Name');?></label>
			<div class="col-md-5">
				<?php echo $this->Form->control('ScheduledEmails.from_name', ['type'=>'text', 'label'=>false, 'class'=>'form-control']);?>
			</div>
		</div>

		<div class="row form-group">
			<label class="col-md-2 col-form-label required"><?php echo __('From Email');?></label>
			<div class="col-md-5">
				<?php echo $this->Form->control('ScheduledEmails.from_email', ['type'=>'text', 'label'=>false, 'class'=>'form-control']);?>
			</div>
		</div>

		<div class="row form-group">
			<label class="col-md-2 col-form-label required"><?php echo __('Subject');?></label>
			<div class="col-md-5">
				<?php echo $this->Form->control('ScheduledEmails.subject', ['type'=>'text', 'label'=>false, 'class'=>'form-control']);?>
			</div>
		</div>

		<div class="row form-group">
			<label class="col-md-2 col-form-label required"><?php echo __('Schedule Date');?></label>
			<div class="col-md-5">
				<?php echo $this->Form->control('ScheduledEmails.schedule_date', ['type'=>'text', 'label'=>false, 'class'=>'form-control datetimepicker', 'autocomplete'=>'off']);?>
				<span class="form-text"><?php echo __('Emails will not be send right now. You need to setup cron job to send scheduled emails. Please refer to documentation for cron job setup.');?></span>
			</div>
		</div>

		<div class="row form-group">
			<label class="col-md-2 col-form-label required"><?php echo __('Message');?></label>
			<div class="col-md-8">
				<?php
				if(strtoupper(DEFAULT_HTML_EDITOR) == 'TINYMCE') {
					echo $this->Tinymce->textarea('ScheduledEmails.message', ['type'=>'textarea', 'label'=>false, 'style'=>'height:400px', 'class'=>'form-control'], ['skin'=>'oxide'], 'full');
				}
				else if(strtoupper(DEFAULT_HTML_EDITOR) == 'CKEDITOR') {
					echo $this->Ckeditor->textarea('ScheduledEmails.message', ['type'=>'textarea', 'label'=>false, 'style'=>'height:400px', 'class'=>'form-control'], [], 'full');
				}?>
			</div>
		</div>

		<div class="row form-group border-top pt-3">
			<div class="col">
				<?php echo $this->Form->Submit(__('Update'), ['class'=>'btn btn-primary', 'id'=>'scheduledEmailSubmitBtn']);?>
			</div>
		</div>

		<?php echo $this->Form->end();?>
	</div>
</div>

<script type="text/javascript">
	$(function(){
		$(document).on("focus", ".datetimepicker", function() {
			$(this).datetimepicker({
				bootcssVer : 4,
				format: 'dd-M-yyyy HH:ii P',
				autoclose: true,
				showMeridian: true
			});
		});
	});
</script>