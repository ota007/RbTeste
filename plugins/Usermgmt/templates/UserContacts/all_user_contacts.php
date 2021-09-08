<div id="updateUserContactsIndex">
	<?php echo $this->Search->searchForm('UserContacts', ['legend'=>false, 'updateDivId'=>'updateUserContactsIndex']);?>
	<?php echo $this->element('Usermgmt.paginator', ['useAjax'=>true, 'updateDivId'=>'updateUserContactsIndex']);?>
	
	<div class="table-responsive">
		<table class="table table-striped table-bordered table-sm table-hover">
			<thead>
				<tr>
					<th><?php echo __('#');?></th>

					<th class="psorting"><?php echo $this->Paginator->sort('UserContacts.user_id', __('User ID'));?></th>

					<th class="psorting"><?php echo $this->Paginator->sort('UserContacts.name', __('Name'));?></th>

					<th class="psorting"><?php echo $this->Paginator->sort('UserContacts.email', __('Email'));?></th>

					<th class="psorting"><?php echo $this->Paginator->sort('UserContacts.phone', __('Contact Number'));?></th>

					<th><?php echo __('Requirement');?></th>

					<th><?php echo __('Reply Message');?></th>

					<th class="psorting"><?php echo $this->Paginator->sort('UserContacts.created', __('Date'));?></th>

					<th><?php echo __('Action');?></th>
				</tr>
			</thead>

			<tbody>
				<?php
				if(!empty($userContacts)) {
					$i = $this->UserAuth->getPageStart();

					foreach($userContacts as $row) {
						$i++;
						
						echo "<tr>";
							echo "<td>".$i."</td>";

							echo "<td>";
								if($row['user_id']) {
									echo $this->Html->link($row['user_id'], ['controller'=>'Users', 'action'=>'viewUser', $row['user_id'], '?'=>['page'=>$this->UserAuth->getPageNumber()]]);
								}
							echo "</td>";

							echo "<td>".$row['name']."</td>";

							echo "<td>".$row['email']."</td>";

							echo "<td>".$row['phone']."</td>";

							echo "<td>".nl2br($row['requirement'])."</td>";

							echo "<td>".$row['reply_message']."</td>";

							echo "<td>".$this->UserAuth->getFormatDate($row['created'])."</td>";

							echo "<td>";
								echo "<div class='dropdown'>";
									echo "<button class='btn btn-dark btn-sm dropdown-toggle' type='button' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>".__('Action')."</button>";
									
									echo "<div class='dropdown-menu dropdown-menu-right'>";
										echo $this->Html->link(__('Send Reply'), ['action'=>'sendReply', $row['id'], '?'=>['page'=>$this->UserAuth->getPageNumber()]], ['escape'=>false, 'class'=>'dropdown-item']);
									echo "</div>";
								echo "</div>";
							echo "</td>";
						echo "</tr>";
					}
				} else {
					echo "<tr><td colspan=9><br/>".__('No Records Available')."</td></tr>";
				}?>
			</tbody>
		</table>
	</div>

	<?php
	if(!empty($userContacts)) {
		echo $this->element('Usermgmt.pagination', ['paginationText'=>__('Number of Enquiries')]);
	}?>
</div>