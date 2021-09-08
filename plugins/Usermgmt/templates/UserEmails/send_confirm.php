<div class="card">
	<div class="card-header text-white bg-dark">
		<span class="card-title">
			<?php echo __('Confirm Sending Email');?>
		</span>

		<span class="card-title float-right">
			<?php echo $this->Html->link(__('Edit', true), ['action'=>'send'], ['class'=>'btn btn-secondary btn-sm']);?>
		</span>
	</div>

	<div class="card-body p-0">
		<?php echo $this->Form->create($userEmailEntity, ['onsubmit'=>'return validateForm()']);?>
		
		<table class="table table-striped table-bordered table-sm table-hover">
			<tbody>
				<tr>
					<th style="width:200px;"><?php echo __('Email Type');?></th>
					<td>
						<?php
						if($userEmailEntity['type'] == 'USERS') {
							echo __('Selected Users');
						}
						else if($userEmailEntity['type'] == 'GROUPS') {
							echo __('Group Users');
						}
						else {
							echo __('Manual Emails');
						}?>
					</td>
				</tr>
				<?php
				if($userEmailEntity['type'] == 'GROUPS') {?>
					<tr>
						<th><?php echo __('Group(s)');?></th>
						<td><?php
							$groupNames = [];
							
							foreach($userEmailEntity['user_group_id'] as $groupId) {
								$groupNames[] = $groups[$groupId];
							}
							
							echo implode(', ', $groupNames);?>
						</td>
					</tr>
				<?php
				}?>

				<tr>
					<th><?php echo __('CC Email(s)');?></th>
					<td><?php echo $userEmailEntity['cc_to'];?></td>
				</tr>

				<tr>
					<th><?php echo __('From Name');?></th>
					<td><?php echo $userEmailEntity['from_name'];?></td>
				</tr>

				<tr>
					<th><?php echo __('From Email');?></th>
					<td><?php echo $userEmailEntity['from_email'];?></td>
				</tr>

				<tr>
					<th><?php echo __('Email Subject');?></th>
					<td><?php echo $userEmailEntity['subject'];?></td>
				</tr>

				<tr>
					<th><?php echo __('Schedule Date');?></th>
					<td><?php echo $this->UserAuth->getFormatDatetime($userEmailEntity['schedule_date']);?></td>
				</tr>

				<tr>
					<th><?php echo __('Email Message');?></th>
					<td><?php echo $userEmailEntity['modified_message'];?></td>
				</tr>
			</tbody>
		</table>
		<br/>

		<table class="table table-striped table-bordered table-sm table-hover">
			<thead>
				<tr>
					<th><?php echo __('#');?></th>
					<th class="align-top"><?php echo $this->Form->control('UserEmails.sel_all', ['type'=>'checkbox', 'label'=>false, 'checked'=>true, 'class'=>'emailcheckall ml-0']);?></th>
					<th><?php echo __('Name');?></th>
					<th><?php echo __('Email');?></th>
				</tr>
			</thead>

			<tbody>
				<?php
				if(!empty($users)) {
					$i = 1;
					
					foreach($users as $row) {
						$trclass = '';
						$checked = true;
						$cls = 'emailcheck';
						
						if(empty($row['email'])) {
							$trclass = 'error';
							$checked = false;
							$cls = '';
						}
						
						echo "<tr class='".$trclass."'>";
							echo "<td>".$i."</td>";

							echo "<td>";
								echo $this->Form->control('UserEmails.EmailList.'.$i.'.emailcheck', ['type'=>'checkbox', 'label'=>false, 'checked'=>$checked, 'class'=>'ml-0 '.$cls, 'hiddenField'=>false]);
								
								echo $this->Form->control('UserEmails.EmailList.'.$i.'.uid', ['type'=>'hidden', 'value'=>$row['id']]);
								
								echo $this->Form->control('UserEmails.EmailList.'.$i.'.email', ['type'=>'hidden', 'value'=>$row['email']]);
							echo "</td>";

							echo "<td>".$row['first_name']." ".$row['last_name']."</td>";

							echo "<td>".$row['email']."</td>";
						echo "</tr>";
						
						$i++;
					}
				} else {
					echo "<tr><td colspan=4><br/>".__('No Users')."</td></tr>";
				}?>
			</tbody>
		</table>
		
		<div class="row form-group border-top p-3 no-gutters">
			<div class="col">
				<?php
				if(!empty($userEmailEntity['schedule_date'])) {
					echo $this->Form->Submit(__('Schedule Email'), ['class'=>'btn btn-primary', 'name'=>'confirmEmail']);
				}
				else {
					echo $this->Form->Submit(__('Send Email'), ['class'=>'btn btn-primary', 'name'=>'confirmEmail']);
				}?>
			</div>
		</div>

		<?php echo $this->Form->end();?>
	</div>
</div>

<style type="text/css">
	.input.checkbox {
		margin:0;
	}
</style>

<script type="text/javascript">
	$(function(){
		$('.emailcheckall').change(function() {
			if($(this).is(':checked')) {
				$(".emailcheck").prop("checked", true);
			} else {
				$(".emailcheck").prop("checked", false);
			}
		});

		$('.emailcheck').change(function() {
			if(!$(this).is(':checked')) {
				$(".emailcheckall").prop("checked", false);
			}
		});
	});

	function validateForm() {
		if(!$(".emailcheck").is(':checked')) {
			alert("<?php echo __('Please select atleast one user to send email');?>");
			return false;
		} else {
			if(<?php echo (empty($userEmailEntity['schedule_date'])) ? 1 : 0;?>) {
				if(!confirm("<?php echo __('Are you sure, you want to continue sending emails?');?>")) {
					return false;
				}
			}
		}
		return true;
	}
</script>