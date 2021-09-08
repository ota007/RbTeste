<div class="card">
	<div class="card-header text-white bg-dark">
		<span class="card-title">
			<?php echo __('Confirm Sending Reply to').' '.$userContact['name'];?>
		</span>

		<span class="card-title float-right">
			<?php echo $this->Html->link(__('Edit', true), ['action'=>'sendReply', $userContactId], ['class'=>'btn btn-secondary btn-sm']);?>
		</span>
	</div>

	<div class="card-body p-0">
		<?php echo $this->Form->create($userEmailEntity, ['onsubmit'=>'return validateForm()']);?>
		
		<table class="table table-striped table-bordered table-sm">
			<tbody>
				<tr>
					<th style="width:200px;"><?php echo __('To');?></th>
					<td><?php echo $userEmailEntity['to'];?></td>
				</tr>
				<tr>
					<th><?php echo __('CC To');?></th>
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
					<th><?php echo __('Email Message');?></th>
					<td><?php echo $userEmailEntity['modified_message'];?></td>
				</tr>
			</tbody>
		</table>
		
		<div class="row form-group border-top p-3 no-gutters">
			<div class="col">
				<?php echo $this->Form->Submit(__('Send Reply'), ['class'=>'btn btn-primary', 'name'=>'confirmEmail']);?>
			</div>
		</div>
		
		<?php echo $this->Form->end();?>
	</div>
</div>

<script type="text/javascript">
	function validateForm() {
		if(!confirm("<?php echo __('Are you sure, you want to send this email?');?>")) {
			return false;
		}
		return true;
	}
</script>