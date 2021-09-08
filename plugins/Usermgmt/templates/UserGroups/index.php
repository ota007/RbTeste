<div class="card">
	<div class="card-header text-white bg-dark">
		<span class="card-title">
			<?php echo __('All User Groups');?>
		</span>
		
		<span class="card-title float-right">
			<?php echo $this->Html->link(__('Add User Group', true), ['action'=>'add'], ['class'=>'btn btn-secondary btn-sm']);?>
		</span>
	</div>
	
	<div class="card-body p-0">
		<?php echo $this->element('../UserGroups/all_user_groups');?>
	</div>
</div>