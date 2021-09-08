<div class="card">
	<div class="card-header text-white bg-dark">
		<span class="card-title">
			<?php echo __('All Sent Emails');?>
		</span>

		<span class="card-title float-right">
			<?php echo $this->Html->link(__('Send Email', true), ['action'=>'send'], ['class'=>'btn btn-secondary btn-sm']);?>
		</span>

		<span class="card-title float-right mr-2">
			<?php echo $this->Html->link(__('Scheduled Emails', true), ['controller'=>'ScheduledEmails', 'action'=>'index'], ['class'=>'btn btn-secondary btn-sm']);?>
		</span>
	</div>

	<div class="card-body p-0">
		<?php echo $this->element('../UserEmails/all_user_emails');?>
	</div>
</div>