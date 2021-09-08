<div id="updateUserEmailIndex">
	<?php echo $this->Search->searchForm('UserEmails', ['legend'=>false, 'updateDivId'=>'updateUserEmailIndex']);?>
	<?php echo $this->element('Usermgmt.paginator', ['useAjax'=>true, 'updateDivId'=>'updateUserEmailIndex']);?>

	<div class="table-responsive">
		<table class="table table-striped table-bordered table-sm table-hover">
			<thead>
				<tr>
					<th><?php echo __('#');?></th>

					<th class="psorting"><?php echo $this->Paginator->sort('UserEmails.type', __('Type'));?></th>

					<th><?php echo __('Groups(s)');?></th>

					<th class="psorting"><?php echo $this->Paginator->sort('UserEmails.from_name', __('From Name'));?></th>

					<th class="psorting"><?php echo $this->Paginator->sort('UserEmails.from_email', __('From Email'));?></th>

					<th class="psorting"><?php echo $this->Paginator->sort('UserEmails.subject', __('Subject'));?></th>

					<th class="psorting"><?php echo $this->Paginator->sort('Users.first_name', __('Sent By'));?></th>

					<th><?php echo __('Sent?');?></th>

					<th class="psorting"><?php echo $this->Paginator->sort('UserEmails.created', __('Date Sent'));?></th>

					<th><?php echo __('Action');?></th>
				</tr>
			</thead>

			<tbody>
				<?php
				if(!empty($userEmails)) {
					$i = $this->UserAuth->getPageStart();

					foreach($userEmails as $row) {
						$i++;

						echo "<tr>";
							echo "<td>".$i."</td>";

							echo "<td>";
								if($row['type'] == 'USERS') {
									echo __('Selected Users');
								}
								else if($row['type'] == 'GROUPS') {
									echo __('Group Users');
								}
								else {
									echo __('Manual Emails');
								}
							echo "</td>";

							echo "<td>";
								if(!empty($row['user_group_id'])) {
									echo $row['group_name'];
								}
								else {
									echo __('N/A');
								}
							echo "</td>";

							echo "<td>".$row['from_name']."</td>";

							echo "<td>".$row['from_email']."</td>";

							echo "<td>".$row['subject']."</td>";

							echo "<td>";
								if(!empty($row['user']['id'])) {
									echo $row['user']['first_name'].' '.$row['user']['last_name'];
								}
							echo "</td>";

							echo "<td>";
								if($row['total_sent_emails'] > 0) {
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
										echo $this->Html->link(__('View Full Email & Recipients'), ['action'=>'view', $row['id'], '?'=>['page'=>$this->UserAuth->getPageNumber()]], ['escape'=>false, 'class'=>'dropdown-item']);
									echo "</div>";
								echo "</div>";
							echo "</td>";
						echo "</tr>";
					}
				} else {
					echo "<tr><td colspan=10><br/>".__('No Records Available')."</td></tr>";
				}?>
			</tbody>
		</table>
	</div>

	<?php
	if(!empty($userEmails)) {
		echo $this->element('Usermgmt.pagination', ['paginationText'=>__('Number of Emails')]);
	}?>
</div>