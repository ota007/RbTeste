<div id="updateUserEmailTemplatesIndex">
	<?php echo $this->Search->searchForm('UserEmailTemplates', ['legend'=>false, 'updateDivId'=>'updateUserEmailTemplatesIndex']);?>
	<?php echo $this->element('Usermgmt.paginator', ['useAjax'=>true, 'updateDivId'=>'updateUserEmailTemplatesIndex']);?>

	<div class="table-responsive">
		<table class="table table-striped table-bordered table-sm table-hover">
			<thead>
				<tr>
					<th><?php echo __('#');?></th>

					<th class="psorting"><?php echo $this->Paginator->sort('UserEmailTemplates.template_name', __('Template Name'));?></th>

					<th><?php echo __('Header');?></th>

					<th><?php echo __('Footer');?></th>

					<th class="psorting"><?php echo $this->Paginator->sort('UserEmailTemplates.created', __('Created'));?></th>

					<th><?php echo __('Action');?></th>
				</tr>
			</thead>

			<tbody>
				<?php
				if(!empty($userEmailTemplates)) {
					$i = $this->UserAuth->getPageStart();

					foreach($userEmailTemplates as $row) {
						$i++;

						echo "<tr>";
							echo "<td>".$i."</td>";

							echo "<td>".$row['template_name']."</td>";

							echo "<td>".$row['template_header']."</td>";

							echo "<td>".$row['template_footer']."</td>";

							echo "<td>".$this->UserAuth->getFormatDate($row['created'])."</td>";

							echo "<td>";
								echo "<div class='dropdown'>";
									echo "<button class='btn btn-dark btn-sm dropdown-toggle' type='button' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>".__('Action')."</button>";
									
									echo "<div class='dropdown-menu dropdown-menu-right'>";
										echo $this->Html->link(__('Edit Template'), ['controller'=>'UserEmailTemplates', 'action'=>'edit', $row['id'], '?'=>['page'=>$this->UserAuth->getPageNumber()]], ['escape'=>false, 'class'=>'dropdown-item']);

										echo $this->Form->postLink(__('Delete Template'), ['controller'=>'UserEmailTemplates', 'action'=>'delete', $row['id'], '?'=>['page'=>$this->UserAuth->getPageNumber()]], ['escape'=>false, 'class'=>'dropdown-item', 'confirm'=>__('Are you sure you want to delete this template?')]);
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
	if(!empty($userEmailTemplates)) {
		echo $this->element('Usermgmt.pagination', ['paginationText'=>__('Number of Email Templates')]);
	}?>
</div>