<div id="updateUserSettingsIndex">
	<?php echo $this->Search->searchForm('UserSettings', ['legend'=>false, 'updateDivId'=>'updateUserSettingsIndex']);?>
	<?php echo $this->element('Usermgmt.paginator', ['useAjax'=>true, 'updateDivId'=>'updateUserSettingsIndex']);?>

	<div class="table-responsive">
		<table class="table table-striped table-bordered table-sm table-hover">
			<thead>
				<tr>
					<th><?php echo __('#');?></th>

					<th class="psorting"><?php echo $this->Paginator->sort('UserSettings.setting_category', __('Category'));?></th>

					<th class="psorting"><?php echo $this->Paginator->sort('UserSettings.setting_key', __('Setting Key'));?></th>

					<th class="psorting"><?php echo $this->Paginator->sort('UserSettings.setting_description', __('Setting Description'));?></th>

					<th><?php echo __('Setting Value');?></th>

					<th><?php echo __('Action');?></th>
				</tr>
			</thead>

			<tbody>
				<?php
				if(!empty($userSettings)) {
					$i = $this->UserAuth->getPageStart();

					foreach($userSettings as $row) {
						$i++;

						echo "<tr>";
							echo "<td>".$i."</td>";
							
							echo "<td>".ucwords(strtolower($row['setting_category']))."</td>";
							
							echo "<td>".$row['setting_key']."</td>";
							
							echo "<td>".nl2br($row['setting_description'])."</td>";
							
							echo "<td class='text-break'>";
								if($row['setting_type'] == 'input' || $row['setting_type'] == 'dropdown' || $row['setting_type'] == 'radio') {
									echo $row['setting_value'];
								}
								else if($row['setting_type'] == 'checkbox') {
									if(!empty($row['setting_value'])) {
										echo "<span class='badge badge-success'>".__('Yes')."</span>";
									}
									else {
										echo "<span class='badge badge-danger'>".__('No')."</span>";
									}
								}
								else if($row['setting_type'] == 'textarea') {
									echo nl2br($row['setting_value']);
								}
								else if($row['setting_type'] == 'tinymce' || $row['setting_type'] == 'ckeditor') {
									echo $row['setting_value'];
								}
							echo"</td>";

							echo "<td>";
								echo "<div class='dropdown'>";
									echo "<button class='btn btn-dark btn-sm dropdown-toggle' type='button' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>".__('Action')."</button>";
									
									echo "<div class='dropdown-menu dropdown-menu-right'>";
										echo $this->Html->link(__('Edit Setting'), ['controller'=>'UserSettings', 'action'=>'editSetting', $row['id'], '?'=>['page'=>$this->UserAuth->getPageNumber()]], ['escape'=>false, 'class'=>'dropdown-item']);
									echo "</div>";
								echo "</div>";
							echo "</td>";
						echo "</tr>";
					}
				} else {
					echo "<tr><td colspan=6><br/>".__('No Records Available')."</td></tr>";
				}?>
			</tbody>
		</table>
	</div>

	<?php
	if(!empty($userSettings)) {
		echo $this->element('Usermgmt.pagination', ['paginationText'=>__('Number of Settings')]);
	}?>
</div>