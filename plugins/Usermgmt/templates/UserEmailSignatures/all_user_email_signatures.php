<div id="updateUserEmailSignaturesIndex">
	<?php echo $this->Search->searchForm('UserEmailSignatures', ['legend'=>false, 'updateDivId'=>'updateUserEmailSignaturesIndex']);?>
	<?php echo $this->element('Usermgmt.paginator', ['useAjax'=>true, 'updateDivId'=>'updateUserEmailSignaturesIndex']);?>
	
	<div class="table-responsive">
		<table class="table table-striped table-bordered table-sm table-hover">
			<thead>
				<tr>
					<th><?php echo __('#');?></th>

					<th class="psorting"><?php echo $this->Paginator->sort('UserEmailSignatures.signature_name', __('Signature Name'));?></th>

					<th><?php echo __('Signature');?></th>

					<th class="psorting"><?php echo $this->Paginator->sort('UserEmailSignatures.created', __('Created'));?></th>

					<th><?php echo __('Action');?></th>
				</tr>
			</thead>

			<tbody>
				<?php
				if(!empty($userEmailSignatures)) {
					$i = $this->UserAuth->getPageStart();

					foreach($userEmailSignatures as $row) {
						$i++;
						
						echo "<tr>";
							echo "<td>".$i."</td>";

							echo "<td>".$row['signature_name']."</td>";

							echo "<td>".$row['signature']."</td>";

							echo "<td>".$this->UserAuth->getFormatDate($row['created'])."</td>";

							echo "<td>";
								echo "<div class='dropdown'>";
									echo "<button class='btn btn-dark btn-sm dropdown-toggle' type='button' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>".__('Action')."</button>";
									
									echo "<div class='dropdown-menu dropdown-menu-right'>";
										echo $this->Html->link(__('Edit Signature'), ['controller'=>'UserEmailSignatures', 'action'=>'edit', $row['id'], '?'=>['page'=>$this->UserAuth->getPageNumber()]], ['escape'=>false, 'class'=>'dropdown-item']);

										echo $this->Form->postLink(__('Delete Signature'), ['controller'=>'UserEmailSignatures', 'action'=>'delete', $row['id'], '?'=>['page'=>$this->UserAuth->getPageNumber()]], ['escape'=>false, 'class'=>'dropdown-item', 'confirm'=>__('Are you sure you want to delete this signature?')]);
									echo "</div>";
								echo "</div>";
							echo "</td>";
						echo "</tr>";
					}
				} else {
					echo "<tr><td colspan=5><br/>".__('No Records Available')."</td></tr>";
				}?>
			</tbody>
		</table>
	</div>
	
	<?php
	if(!empty($userEmailSignatures)) {
		echo $this->element('Usermgmt.pagination', ['paginationText'=>__('Number of Signatures')]);
	}?>
</div>