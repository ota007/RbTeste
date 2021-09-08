<div class="card">
	<div class="card-header text-white bg-dark">
		<span class="card-title">
			<?php echo __('Add Multiple Users' , true);?>
		</span>

		<span class="card-title float-right">
			<?php echo $this->Html->link(__('Back', true), ['action'=>'uploadCsv'], ['class'=>'btn btn-secondary btn-sm']);?>
		</span>
	</div>

	<div class="card-body p-0">
		<?php echo $this->element('Usermgmt.ajax_validation', ['formId'=>'addMultipleUserForm', 'submitButtonId'=>'addMultipleUserSubmitBtn']);?>
		<?php echo $this->Form->create($userEntities, ['id'=>'addMultipleUserForm', 'onsubmit'=>'return validateForm()']);?>

		<div style="padding:15px;"><strong><?php echo __('Please Note: Unchecked rows will not be saved in database.');?></strong></div>

		<div class="table-responsive">
			<table class="table table-striped table-bordered table-sm table-hover">
				<thead>
					<tr>
						<th><?php echo $this->Form->control('Select.all', ['type'=>'checkbox', 'label'=>false, 'class'=>'usercheckall ml-0 position-relative', 'autocomplete'=>'off']);?></th>
						
						<th><?php echo __('User Group');?></th>

						<th><?php echo __('First Name');?></th>

						<th><?php echo __('Last Name');?></th>

						<th><?php echo __('Username');?></th>

						<th><?php echo __('Email');?></th>

						<th><?php echo __('Password');?></th>

						<th><?php echo __('Gender');?></th>

						<th><?php echo __('Birthday');?></th>

						<th><?php echo __('Location');?></th>

						<th><?php echo __('Cellphone');?></th>
					</tr>
				</thead>

				<tbody>
					<?php
					if(!empty($users)) {
						foreach($users as $i=>$user) {?>
							<tr>
								<td style="text-align:left">
									<?php echo $this->Form->control('Users.'.$i.'.usercheck', ['type'=>'checkbox', 'label'=>false, 'class'=>'usercheck ml-0', 'hiddenField'=>false, 'autocomplete'=>'off']);?>
								</td>

								<td>
									<?php echo $this->Form->control('Users.'.$i.'.user_group_id', ['type'=>'select', 'options'=>$userGroups, 'multiple'=>true, 'label'=>false, 'data-placeholder'=>'Select Group(s)', 'class'=>'form-control user_group_id_input', 'style'=>'width:100px']);?>
								</td>

								<td>
									<?php echo $this->Form->control('Users.'.$i.'.first_name', ['type'=>'text', 'label'=>false, 'style'=>'width:100px', 'class'=>'form-control']);?>
								</td>

								<td>
									<?php echo $this->Form->control('Users.'.$i.'.last_name', ['type'=>'text', 'label'=>false, 'style'=>'width:100px', 'class'=>'form-control']);?>
								</td>

								<td>
									<?php echo $this->Form->control('Users.'.$i.'.username', ['type'=>'text', 'label'=>false, 'style'=>'width:100px', 'class'=>'form-control']);?>
								</td>

								<td>
									<?php echo $this->Form->control('Users.'.$i.'.email', ['type'=>'text', 'label'=>false, 'style'=>'width:200px', 'class'=>'form-control']);?>
								</td>

								<td>
									<?php echo $this->Form->control('Users.'.$i.'.password', ['type'=>'text', 'label'=>false, 'type'=>'text', 'style'=>'width:100px', 'class'=>'form-control']);?>
								</td>

								<td>
									<?php echo $this->Form->control('Users.'.$i.'.gender', ['type'=>'select', 'options'=>$genders, 'label'=>false, 'style'=>'width:100px', 'class'=>'form-control']);?>
								</td>

								<td>
									<?php echo $this->Form->control('Users.'.$i.'.bday', ['type'=>'text', 'label'=>false, 'style'=>'width:120px', 'class'=>'form-control']);?>
								</td>

								<td>
									<?php echo $this->Form->control('Users.'.$i.'.user_detail.location', ['type'=>'text', 'label'=>false, 'style'=>'width:100px', 'class'=>'form-control']);?>
								</td>

								<td>
									<?php echo $this->Form->control('Users.'.$i.'.user_detail.cellphone', ['type'=>'text', 'label'=>false, 'style'=>'width:120px', 'class'=>'form-control']);?>
								</td>
							</tr>
						<?php
						}
					}?>
				</tbody>
			</table>
		</div>

		<div class="row form-group border-top p-3 no-gutters">
			<div class="col">
				<?php echo $this->Form->Submit(__('Add Users'), ['class'=>'btn btn-primary', 'id'=>'addMultipleUserSubmitBtn']);?>
			</div>
		</div>

		<?php echo $this->Form->end();?>
	</div>
</div>

<style type="text/css">
	form {
		width:100%
	}
	input, textarea,select {
		font-size: 100%;
	}
</style>

<script type="text/javascript">
	$(function(){
		$('.usercheckall').change(function() {
			if($(this).is(':checked')) {
				$(".usercheck").prop("checked", true);
			} else {
				$(".usercheck").prop("checked", false);
			}
		});

		$('.usercheck').change(function() {
			if(!$(this).is(':checked')) {
				$(".usercheckall").prop("checked", false);
			}
		});
		
		$(".user_group_id_input").select2({
			//options
		});
	});
	
	function validateForm() {
		if(!$(".usercheck").is(':checked')) {
			alert("<?php echo __('Please select atleast one user to add');?>");
			return false;
		}
		return true;
	}
</script>