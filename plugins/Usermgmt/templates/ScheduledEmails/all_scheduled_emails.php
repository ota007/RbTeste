<div id="updateScheduledEmailIndex">
	<?php echo $this->Search->searchForm('ScheduledEmails', ['legend'=>false, 'updateDivId'=>'updateScheduledEmailIndex']);?>
	<?php echo $this->element('Usermgmt.paginator', ['useAjax'=>true, 'updateDivId'=>'updateScheduledEmailIndex']);?>
	
	<div class="table-responsive">
		<table class="table table-striped table-bordered table-sm table-hover">
			<thead>
				<tr>
					<th><?php echo __('#');?></th>

					<th class="psorting"><?php echo $this->Paginator->sort('ScheduledEmails.type', __('Type'));?></th>

					<th><?php echo __('Groups(s)');?></th>

					<th class="psorting"><?php echo $this->Paginator->sort('ScheduledEmails.from_name', __('From Name'));?></th>

					<th class="psorting"><?php echo $this->Paginator->sort('ScheduledEmails.from_email', __('From Email'));?></th>

					<th class="psorting"><?php echo $this->Paginator->sort('ScheduledEmails.subject', __('Subject'));?></th>

					<th class="psorting"><?php echo $this->Paginator->sort('Users.first_name', __('Scheduled By'));?></th>

					<th><?php echo __('Status');?></th>

					<th class="psorting"><?php echo $this->Paginator->sort('ScheduledEmails.schedule_date', __('Scheduled Date'));?></th>

					<th class="psorting"><?php echo $this->Paginator->sort('ScheduledEmails.created', __('Created'));?></th>

					<th><?php echo __('Action');?></th>
				</tr>
			</thead>

			<tbody>
				<?php
				if(!empty($scheduledEmails)) {
					$i = $this->UserAuth->getPageStart();

					foreach($scheduledEmails as $row) {
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
								if($row['is_sent']) {
									echo "<span class='badge badge-success'>".__('Sent')."</span>";
								}
								else if($row['total_sent_emails'] > 0) {
									echo "<span class='badge badge-info'>".__('Sending')."</span>";
								}
								else {
									echo "<span class='badge badge-danger'>".__('Not Sent')."</span>";
								}
							echo"</td>";

							echo "<td>".$this->UserAuth->getFormatDatetime($row['schedule_date'])."</td>";

							echo "<td>".$this->UserAuth->getFormatDate($row['created'])."</td>";

							echo "<td>";
								echo "<div class='dropdown'>";
									echo "<button class='btn btn-dark btn-sm dropdown-toggle' type='button' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>".__('Action')."</button>";
									
									echo "<div class='dropdown-menu dropdown-menu-right'>";
										echo $this->Html->link(__('View Full Email & Recipients'), ['action'=>'view', $row['id'], '?'=>['page'=>$this->UserAuth->getPageNumber()]], ['escape'=>false, 'class'=>'dropdown-item']);

										if(!$row['is_sent'] && !$row['total_sent_emails']) {
											echo $this->Html->link(__('Edit Email'), ['action'=>'edit', $row['id'], '?'=>['page'=>$this->UserAuth->getPageNumber()]], ['escape'=>false, 'class'=>'dropdown-item']);
										}
										
										if(!$row['is_sent']) {
											echo $this->Form->postLink(__('Delete Email & Recipients'), ['action'=>'delete', $row['id'], '?'=>['page'=>$this->UserAuth->getPageNumber()]], ['escape'=>false, 'class'=>'dropdown-item', 'confirm'=>__('Are you sure, you want to delete this scheduled email along with all recipients?')]);
										}
									echo "</div>";
								echo "</div>";
							echo "</td>";
						echo "</tr>";
					}
				} else {
					echo "<tr><td colspan=11><br/>".__('No Records Available')."</td></tr>";
				}?>
			</tbody>
		</table>
	</div>

	<?php
	if(!empty($scheduledEmails)) {
		echo $this->element('Usermgmt.pagination', ['paginationText'=>__('Number of Emails')]);
	}?>
</div>