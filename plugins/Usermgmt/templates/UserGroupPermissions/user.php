<div class="card">
	<div class="card-header text-white bg-dark">
		<span class="card-title">
			<?php echo __('Site Permissions for').' '.$user['first_name'].' '.$user['last_name'];?>
		</span>

		<span class="card-title float-right">
			<?php echo $this->Html->link(__('Back', true), ['controller'=>'Users', 'action'=>'index', '?'=>['page'=>$this->UserAuth->getPageNumber()]], ['class'=>'btn btn-secondary btn-sm']);?>
		</span>
	</div>

	<div class="card-body p-0">
		<div style="padding:10px;font-weight:bold;"><?php echo __('Please make sure permissions are based on groups.');?></div>
		
		<table class="table table-striped table-bordered table-sm table-hover">
			<thead>
				<tr>
					<th><?php echo __('#');?></th>

					<th><?php echo __('Plugin');?></th>

					<th><?php echo __('Controller');?></th>

					<th><?php echo __('Action');?></th>

					<th><?php echo __('Group(s)');?></th>
				</tr>
			</thead>

			<tbody>
				<?php
				if(!empty($permissions)) {
					$i = 0;
					
					foreach($permissions as $row) {
						$i++;
						
						echo "<tr>";
							echo "<td>".$i."</td>";

							echo "<td>".$row['plugin']."</td>";

							echo "<td>".$row['controller']."</td>";

							echo "<td>".$row['action']."</td>";

							echo "<td>".$row['group']."</td>";
						echo "</tr>";
					}
				} else {
					echo "<tr><td colspan=5><br/>".__('No Records Available')."</td></tr>";
				}?>
			</tbody>
		</table>
	</div>
</div>