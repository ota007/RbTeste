<div class="card">
	<div class="card-header text-white bg-dark">
		<span class="card-title">
			<?php echo __('All Users');?>
		</span>

		<span class="card-title float-right">
			<?php echo $this->Html->link(__('Add User', true), ['action'=>'addUser'], ['class'=>'btn btn-secondary btn-sm']);?>
		</span>
	</div>

	<div class="card-body p-0">
		<?php echo $this->element('../Users/all_users');?>
	</div>
</div>