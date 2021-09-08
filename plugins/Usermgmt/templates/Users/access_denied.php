<div class="card">
	<div class="card-header text-white bg-dark">
		<span class="card-title">
			<?php echo __('Access Denied');?>
		</span>
	</div>

	<div class="card-body">
		<?php echo __('Sorry, You don\'t have permission to view that page. go to');?> <?php echo $this->Html->link(__('Dashboard'), ['controller'=>'Users', 'action'=>'dashboard', 'plugin'=>'Usermgmt']);?>
		
		<br/><br/>
		<br/><br/>
	</div>
</div>