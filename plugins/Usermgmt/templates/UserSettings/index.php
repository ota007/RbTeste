<div class="card">
	<div class="card-header text-white bg-dark">
		<span class="card-title">
			<?php echo __('All Settings');?>
		</span>

		<span class="card-title float-right">
			<?php echo $this->Html->link(__('Add Setting', true), ['action'=>'addSetting'], ['class'=>'btn btn-warning btn-sm text-white']);?>
		</span>

		<span class="card-title float-right mr-2">
			<?php echo $this->Html->link(__('Setting Options', true), ['controller'=>'SettingOptions', 'action'=>'index'], ['class'=>'btn btn-warning btn-sm text-white']);?>
		</span>
	</div>

	<div class="card-body p-0">
		<div class="card bg-light m-2">
			<div class="card-body">
				<span class="badge badge-success">Help: How to use these settings</span>
				<br/><br/>

				<ul>
					<li>Configure::read(key); for e.g. Configure::read('site_name_full'); , Configure::read('site_name_short'); <strong>Please Note: You may need to add "use Cake\Core\Configure;" (without quotes) in the file</strong></li>

					<li>You can also use as constants (defines) in upper case of key for e.g. SITE_NAME_FULL , SITE_NAME_SHORT</li>
				</ul>
			</div>
		</div>

		<?php echo $this->element('../UserSettings/all_user_settings');?>
	</div>
</div>