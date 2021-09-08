<div class="card">
	<div class="card-header text-white bg-dark">
		<span class="card-title">
			<?php echo __('Permission Changes');?>
		</span>
	</div>

	<div class="card-body p-2">
		<h5><?php echo __('Controllers Not Added in Permission');?></h5>

		<table class="table table-striped table-bordered table-sm table-hover">
			<thead>
				<tr>
					<th><?php echo __('Prefix');?></th>
					<th><?php echo __('Plugin');?></th>
					<th><?php echo __('Controller');?></th>
				</tr>
			</thead>

			<tbody>
				<?php
				$noFound = true;

				foreach($filesControllerActions as $key=>$val) {
					if(!isset($dbControllerActions[$key])) {
						$keyArr = explode(':', $key);
						$noFound = false;

						echo "<tr>";
							echo "<td>".str_replace('.', '/', $keyArr[0])."</td>";

							echo "<td>".$keyArr[1]."</td>";

							echo "<td>".$keyArr[2]."</td>";
						echo "</tr>";
					}
				}
				
				if($noFound) {
					echo "<tr><td colspan=3>".__('No Records Available')."</td></tr>";	
				}?>
			</tbody>
		</table>

		<br/>
		<hr style="border-color:red;"/>
		<br/>

		<h5><?php echo __('Actions Not Added in Permission');?></h5>

		<table class="table table-striped table-bordered table-sm table-hover">
			<thead>
				<tr>
					<th><?php echo __('Prefix');?></th>
					<th><?php echo __('Plugin');?></th>
					<th><?php echo __('Controller');?></th>
					<th><?php echo __('Action');?></th>
				</tr>
			</thead>

			<tbody>
				<?php
				$noFound = true;

				foreach($filesControllerActions as $key=>$val) {
					$keyArr = explode(':', $key);

					foreach($val as $act) {
						if(!isset($dbControllerActions[$key][$act])) {
							$noFound = false;

							echo "<tr>";
								echo "<td>".str_replace('.', '/', $keyArr[0])."</td>";

								echo "<td>".$keyArr[1]."</td>";

								echo "<td>".$keyArr[2]."</td>";

								echo "<td>".$act."</td>";
							echo "</tr>";
						}
					}
				}
				
				if($noFound) {
					echo "<tr><td colspan=4>".__('No Records Available')."</td></tr>";	
				}?>
			</tbody>
		</table>
		
		<br/>
		<hr style="border-color:red;"/>
		<br/>

		<h5><?php echo __('Extra Controllers Added in Permission Table');?></h5>

		<table class="table table-striped table-bordered table-sm table-hover">
			<thead>
				<tr>
					<th><?php echo __('Prefix');?></th>
					<th><?php echo __('Plugin');?></th>
					<th><?php echo __('Controller');?></th>
					<th></th>
				</tr>
			</thead>

			<tbody>
				<?php
				$noFound = true;

				foreach($dbControllerActions as $key=>$val) {
					if(!isset($filesControllerActions[$key])) {
						$keyArr = explode(':', $key);

						$prefix = ($keyArr[0]) ? $keyArr[0] : 'false';
						$plugin = ($keyArr[1]) ? $keyArr[1] : 'false';

						$noFound = false;

						echo "<tr>";
							echo "<td>".str_replace('.', '/', $keyArr[0])."</td>";

							echo "<td>".$keyArr[1]."</td>";

							echo "<td>".$keyArr[2]."</td>";

							echo "<td>".$this->Form->postLink(__('Remove'), ['controller'=>'UserGroupPermissions', 'action'=>'printPermissionChanges', 'plugin'=>'Usermgmt', $prefix, $plugin, $keyArr[2]], ['class'=>'btn btn-primary btn-sm', 'confirm'=>__('Are you sure, you want to delete permissions of this controller along with it\'s actions?')])."</td>";
						echo "</tr>";
					}
				}

				if($noFound) {
					echo "<tr><td colspan=4>".__('No Records Available')."</td></tr>";	
				}?>
			</tbody>
		</table>

		<br/>
		<hr style="border-color:red;"/>
		<br/>

		<h5><?php echo __('Extra Actions Added in Permission Table');?></h5>

		<table class="table table-striped table-bordered table-sm table-hover">
			<thead>
				<tr>
					<th><?php echo __('Prefix');?></th>
					<th><?php echo __('Plugin');?></th>
					<th><?php echo __('Controller');?></th>
					<th><?php echo __('Action');?></th>
					<th></th>
				</tr>
			</thead>

			<tbody>
				<?php
				$noFound = true;

				foreach($dbControllerActions as $key=>$val) {
					$keyArr = explode(':', $key);

					$prefix = ($keyArr[0]) ? $keyArr[0] : 'false';
					$plugin = ($keyArr[1]) ? $keyArr[1] : 'false';

					foreach($val as $act) {
						if(!isset($filesControllerActions[$key][$act])) {
							$noFound = false;

							echo "<tr>";
								echo "<td>".str_replace('.', '/', $keyArr[0])."</td>";

								echo "<td>".$keyArr[1]."</td>";

								echo "<td>".$keyArr[2]."</td>";

								echo "<td>".$act."</td>";


								echo "<td>".$this->Form->postLink(__('Remove'), ['controller'=>'UserGroupPermissions', 'action'=>'printPermissionChanges', 'plugin'=>'Usermgmt', $prefix, $plugin, $keyArr[2], $act], ['class'=>'btn btn-primary btn-sm', 'confirm'=>__('Are you sure, you want to delete permissions of this action?')])."</td>";
							echo "</tr>";
						}
					}
				}
				
				if($noFound) {
					echo "<tr><td colspan=5>".__('No Records Available')."</td></tr>";	
				}?>
			</tbody>
		</table>
	</div>
</div>