<div class="card">
	<div class="card-header text-white bg-dark">
		<span class="card-title">
			<?php echo __('Email Details');?>
		</span>

		<span class="card-title float-right">
			<?php echo $this->Html->link(__('Back', true), ['action'=>'index', '?'=>['page'=>$this->UserAuth->getPageNumber()]], ['class'=>'btn btn-secondary btn-sm']);?>
		</span>
	</div>

	<div class="card-body p-0">
		<table class="table table-striped table-bordered table-sm table-hover">
			<tbody>
				<tr>
					<th style="width:200px;"><?php echo __('Email Type');?></th>
					<td>
						<?php
						if($userEmail['type'] == 'USERS') {
							echo __('Selected Users');
						}
						else if($userEmail['type'] == 'GROUPS') {
							echo __('Group Users');
						}
						else {
							echo __('Manual Emails');
						}?>
					</td>
				</tr>

				<?php
				if($userEmail['type'] == 'GROUPS') {?>
					<tr>
						<th><?php echo __('Group(s)');?></th>
						<td><?php echo $userEmail['group_name'];?></td>
					</tr>
				<?php
				}?>

				<tr>
					<th><?php echo __('CC Email(s)');?></th>
					<td><?php echo $userEmail['cc_to'];?></td>
				</tr>
				
				<tr>
					<th><?php echo __('From Name');?></th>
					<td><?php echo $userEmail['from_name'];?></td>
				</tr>
				
				<tr>
					<th><?php echo __('From Email');?></th>
					<td><?php echo $userEmail['from_email'];?></td>
				</tr>
				
				<tr>
					<th><?php echo __('Email Subject');?></th>
					<td><?php echo $userEmail['subject'];?></td>
				</tr>
				
				<tr>
					<th><?php echo __('Email Message');?></th>
					<td><?php echo $userEmail['message'];?></td>
				</tr>
				
				<tr>
					<th><?php echo __('Sent By');?></th>
					<td>
						<?php
						if(!empty($userEmail['user']['id'])) {
							echo $userEmail['user']['first_name'].' '.$userEmail['user']['last_name'];
						}?>
					</td>
				</tr>
				
				<tr>
					<th><?php echo __('Sent?');?></th>
					<td>
						<?php
						if(!empty($userEmail['total_sent_emails'])) {
							echo "<span class='badge badge-success'>".__('Yes')."</span>";
						}
						else {
							echo "<span class='badge badge-danger'>".__('No')."</span>";
						}?>
					</td>
				</tr>

				<tr>
					<th><?php echo __('Date Sent');?></th>
					<td><?php echo $this->UserAuth->getFormatDatetime($userEmail['created']);?></td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
<br/>

<div class="card">
	<div class="card-header text-white bg-dark">
		<span class="card-title">
			<?php echo __('Email Recipients');?>
		</span>
	</div>
	<div class="card-body p-0">
		<table class="table table-striped table-bordered table-sm table-hover">
			<thead>
				<tr>
					<th><?php echo __('Id');?></th>
					<th><?php echo __('Name');?></th>
					<th><?php echo __('Email');?></th>
					<th><?php echo __('Sent?');?></th>
				</tr>
			</thead>

			<tbody>
				<?php
				if(!empty($userEmailRecipients)) {
					foreach($userEmailRecipients as $row) {
						echo "<tr>";
							echo "<td>".$row['id']."</td>";

							echo "<td>";
								if(!empty($row['user']['id'])) {
									echo $row['user']['first_name'].' '.$row['user']['last_name'];
								}
							echo "</td>";

							echo "<td>".$row['email_address']."</td>";

							echo "<td>";
								if($row['is_email_sent']) {
									echo "<span class='badge badge-success'>".__('Yes')."</span>";
								}
								else {
									echo "<span class='badge badge-danger'>".__('No')."</span>";
								}
							echo "</td>";
						echo "</tr>";
					}
				} else {
					echo "<tr><td colspan=4><br/>".__('No Recipient Available')."</td></tr>";
				}?>
			</tbody>
		</table>
	</div>
</div>