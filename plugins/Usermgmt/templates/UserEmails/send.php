<div class="card">
	<div class="card-header text-white bg-dark">
		<span class="card-title">
			<?php echo __('Send Email');?>
		</span>

		<span class="card-title float-right">
			<?php echo $this->Html->link(__('Back', true), ['action'=>'index'], ['class'=>'btn btn-secondary btn-sm']);?>
		</span>
	</div>

	<div class="card-body">
		<?php echo $this->element('Usermgmt.ajax_validation', ['formId'=>'sendMailForm', 'submitButtonId'=>'sendMailSubmitBtn']);?>
		<?php echo $this->Form->create($userEmailEntity, ['id'=>'sendMailForm', 'novalidate'=>true]);?>

		<?php
			$userSearch = $groupSearch = $manualEmail = 'none';
			
			if(!isset($userEmailEntity['type']) || (isset($userEmailEntity['type']) && $userEmailEntity['type'] == 'USERS')) {
				$userSearch = '';
			}
			if(isset($userEmailEntity['type']) && $userEmailEntity['type'] == 'GROUPS') {
				$groupSearch = '';
			}
			if(isset($userEmailEntity['type']) && $userEmailEntity['type'] == 'MANUAL') {
				$manualEmail = '';
			}
		?>
		
		<div class="row form-group">
			<label class="col-md-2 col-form-label required"><?php echo __('Type');?></label>
			<div class="col-md-5">
				<?php echo $this->Form->control('UserEmails.type', ['type'=>'radio', 'options'=>['USERS'=>'Selected Users', 'GROUPS'=>'Group Users', 'MANUAL'=>'Manual Emails'], 'label'=>false, 'legend'=>false, 'default'=>'USERS', 'autocomplete'=>'off', 'class'=>'mx-2']);?>
			</div>
		</div>
		
		<div class="row form-group" id="userSearch" style="display:<?php echo $userSearch;?>">
			<label class="col-md-2 col-form-label required"><?php echo __('Select User(s)');?></label>
			<div class="col-md-5">
				<?php echo $this->Form->control('UserEmails.user_id', ['type'=>'select', 'multiple'=>true, 'options'=>$sel_users, 'label'=>false, 'autocomplete'=>'off', 'data-placeholder'=>'Select User(s)', 'class'=>'form-control user_id_input']);?>
			</div>
		</div>
		
		<div class="row form-group" id="groupSearch" style="display:<?php echo $groupSearch;?>">
			<label class="col-md-2 col-form-label required"><?php echo __('Select Groups(s)');?></label>
			<div class="col-md-5">
				<?php echo $this->Form->control('UserEmails.user_group_id', ['type'=>'select', 'multiple'=>true, 'options'=>$groups, 'label'=>false, 'autocomplete'=>'off', 'data-placeholder'=>'Select Group(s)', 'class'=>'form-control user_group_id_input']);?>
			</div>
		</div>
		
		<div class="row form-group" id="manualEmail" style="display:<?php echo $manualEmail;?>">
			<label class="col-md-2 col-form-label required"><?php echo __('To Email(s)');?></label>
			<div class="col-md-5">
				<?php echo $this->Form->control('UserEmails.to_email', ['type'=>'textarea', 'label'=>false, 'class'=>'form-control']);?>
				<span class="tagline"><?php echo __('multiple emails comma separated');?></span>
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
			<label class="col-md-2 col-form-label required"><?php echo __('Subject');?></label>
			<div class="col-md-5">
				<?php echo $this->Form->control('UserEmails.subject', ['type'=>'text', 'label'=>false, 'class'=>'form-control']);?>
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
			<label class="col-md-2 col-form-label"><?php echo __('Schedule Date');?></label>
			<div class="col-md-5">
				<?php echo $this->Form->control('UserEmails.schedule_date', ['type'=>'text', 'label'=>false, 'class'=>'form-control datetimepicker', 'autocomplete'=>'off']);?>
				<span class="form-text"><?php echo __('If you enter schedule date, emails will not be send right now. You need to setup cron job to send scheduled emails. Please refer to documentation for cron job setup.');?></span>
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

<style type="text/css">
	.select2-container, .select2-container .select2-search__field {
		width:100% !important;
	}
</style>

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

		$(".user_group_id_input").select2({
			//options
		});

		$(".user_id_input").select2({
			//options
			minimumInputLength: 2,
			ajax: {
				type: 'post',
				url: urlForJs+'usermgmt/Autocomplete/searchEmails',
				dataType: 'json',
				delay: 1000,
				headers: {"X-CSRF-Token": "<?php echo $this->request->getCookie('csrfToken');?>"}
			}
		});

		$("#useremails-type-users").click(function() {
			$("#groupSearch, #manualEmail").hide();
			$("#userSearch").show();
		});

		$("#useremails-type-groups").click(function() {
			$("#userSearch, #manualEmail").hide();
			$("#groupSearch").show();
		});

		$("#useremails-type-manual").click(function() {
			$("#userSearch, #groupSearch").hide();
			$("#manualEmail").show();
		});
	});
</script>