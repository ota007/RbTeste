<div id="updateGroupIndex">
	<?php echo $this->Search->searchForm('UserGroups', ['legend'=>false, 'updateDivId'=>'updateGroupIndex']);?>
	<?php echo $this->element('Usermgmt.paginator', ['useAjax'=>true, 'updateDivId'=>'updateGroupIndex']);?>

	<div class="table-responsive">
		<table class="table table-striped table-bordered table-sm table-hover">
			<thead>
				<tr>
					<th><?php echo __('#');?></th>

					<th class="psorting"><?php echo $this->Paginator->sort('UserGroups.id', __('Group Id'));?></th>

					<th class="psorting"><?php echo $this->Paginator->sort('UserGroups.name', __('Group Name'));?></th>

					<th><?php echo __('Parent Group');?></th>

					<th><?php echo __('Description');?></th>

					<th><?php echo __('New Registration Allowed?');?></th>

					<th><?php echo __('Created');?></th>

					<th><?php echo __('Action');?></th>
				</tr>
			</thead>

			<tbody>
				<?php
				if(!empty($userGroups)) {
					$i = $this->UserAuth->getPageStart();
					
					foreach($userGroups as $row) {
						$i++;

						echo "<tr>";
							echo "<td>".$i."</td>";

							echo "<td>".$row['id']."</td>";

							echo "<td>".$row['name']."</td>";

							echo "<td>";
								if($row['parent_id'] > 0) {
									echo $allGroups[$row['parent_id']];
								}
							echo "</td>";

							echo "<td>".nl2br($row['description'])."</td>";

							echo "<td>";
								if($row['is_registration_allowed']) {
									echo "<span class='badge badge-success'>".__('Yes')."</span>";
								}
								else {
									echo "<span class='badge badge-danger'>".__('No')."</span>";
								}
							echo"</td>";

							echo "<td>".$this->UserAuth->getFormatDate($row['created'])."</td>";

							echo "<td>";
								echo "<div class='dropdown'>";
									echo "<button class='btn btn-dark btn-sm dropdown-toggle' type='button' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>".__('Action')."</button>";
									
									echo "<div class='dropdown-menu dropdown-menu-right'>";
										echo $this->Html->link(__('Edit Group'), ['controller'=>'UserGroups', 'action'=>'edit', $row['id'], '?'=>['page'=>$this->UserAuth->getPageNumber()]], ['escape'=>false, 'class'=>'dropdown-item']);

										if($row['id'] != ADMIN_GROUP_ID) {
											echo $this->Form->postLink(__('Delete Group'), ['controller'=>'UserGroups', 'action'=>'delete', $row['id'], '?'=>['page'=>$this->UserAuth->getPageNumber()]], ['escape'=>false, 'class'=>'dropdown-item', 'confirm'=>__('Are you sure you want to delete this group? Delete it your own risk')]);
										}
									echo "</div>";
								echo "</div>";
							echo "</td>";
						echo "</tr>";
					}
				} else {
					echo "<tr><td colspan=8><br/>".__('No Records Available')."</td></tr>";
				}?>
			</tbody>
		</table>
	</div>
	
	<?php
	if(!empty($userGroups)) {
		echo $this->element('Usermgmt.pagination', ['paginationText'=>__('Number of Groups')]);
	}?>
</div>