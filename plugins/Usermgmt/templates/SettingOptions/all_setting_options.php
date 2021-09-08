<div id="updateSettingOptionsIndex">
	<?php echo $this->Search->searchForm('SettingOptions', ['legend'=>false, 'updateDivId'=>'updateSettingOptionsIndex']);?>
	<?php echo $this->element('Usermgmt.paginator', ['useAjax'=>true, 'updateDivId'=>'updateSettingOptionsIndex']);?>
	
	<div class="table-responsive">
		<table class="table table-striped table-bordered table-sm table-hover">
			<thead>
				<tr>
					<th><?php echo __('#');?></th>

					<th class="psorting"><?php echo $this->Paginator->sort('SettingOptions.title', __('Title'));?></th>

					<th class="psorting"><?php echo $this->Paginator->sort('SettingOptions.created', __('Created'));?></th>

					<th><?php echo __('Action');?></th>
				</tr>
			</thead>

			<tbody>
				<?php
				if(!empty($settingOptions)) {
					$i = $this->UserAuth->getPageStart();

					foreach($settingOptions as $row) {
						$i++;
						
						echo "<tr>";
							echo "<td>".$i."</td>";
							
							echo "<td>".$row['title']."</td>";
							
							echo "<td>".$this->UserAuth->getFormatDate($row['created'])."</td>";
							
							echo "<td>";
								echo "<div class='dropdown'>";
									echo "<button class='btn btn-dark btn-sm dropdown-toggle' type='button' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>".__('Action')."</button>";
									
									echo "<div class='dropdown-menu dropdown-menu-right'>";
										echo $this->Html->link(__('Edit'), ['controller'=>'SettingOptions', 'action'=>'edit', $row['id'], '?'=>['page'=>$this->UserAuth->getPageNumber()]], ['escape'=>false, 'class'=>'dropdown-item']);

										echo $this->Form->postLink(__('Delete'), ['controller'=>'SettingOptions', 'action'=>'delete', $row['id'], '?'=>['page'=>$this->UserAuth->getPageNumber()]], ['escape'=>false, 'class'=>'dropdown-item', 'confirm'=>__('Are you sure you want to delete this option?')]);
									echo "</div>";
								echo "</div>";
							echo "</td>";
						echo "</tr>";
					}
				} else {
					echo "<tr><td colspan=4><br/>".__('No Records Available')."</td></tr>";
				}?>
			</tbody>
		</table>
	</div>

	<?php
	if(!empty($settingOptions)) {
		echo $this->element('Usermgmt.pagination', ['paginationText'=>__('Number of Options')]);
	}?>
</div>