<div class="card">
	<div class="card-header text-white bg-dark">
		<span class="card-title">
			<?php echo __('Scheduled Emails');?>
		</span>

		<span class="card-title float-right">
			<?php echo $this->Html->link(__('Schedule Email', true), ['controller'=>'UserEmails', 'action'=>'send'], ['class'=>'btn btn-secondary btn-sm']);?>
		</span>

		<span class="card-title float-right mr-2">
			<?php echo $this->Html->link(__('Sent Emails', true), ['controller'=>'UserEmails', 'action'=>'index'], ['class'=>'btn btn-secondary btn-sm']);?>
		</span>
	</div>

	<div class="card-body p-0">
		<?php echo $this->element('../ScheduledEmails/all_scheduled_emails');?>
	</div>
</div>