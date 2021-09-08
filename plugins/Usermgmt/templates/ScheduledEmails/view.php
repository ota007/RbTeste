<div class="card">
	<div class="card-header text-white bg-dark">
		<span class="card-title">
			<?php echo __('Scheduled Email Details');?>
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
						if($scheduledEmail['type'] == 'USERS') {
							echo __('Selected Users');
						}
						else if($scheduledEmail['type'] == 'GROUPS') {
							echo __('Group Users');
						}
						else {
							echo __('Manual Emails');
						}?>
					</td>
				</tr>

				<?php
				if($scheduledEmail['type'] == 'GROUPS') {?>
					<tr>
						<th><?php echo __('Group(s)');?></th>
						<td><?php echo $scheduledEmail['group_name'];?></td>
					</tr>
				<?php }?>

				<tr>
					<th><?php echo __('CC Email(s)');?></th>
					<td><?php echo $scheduledEmail['cc_to'];?></td>
				</tr>

				<tr>
					<th><?php echo __('From Name');?></th>
					<td><?php echo $scheduledEmail['from_name'];?></td>
				</tr>

				<tr>
					<th><?php echo __('From Email');?></th>
					<td><?php echo $scheduledEmail['from_email'];?></td>
				</tr>

				<tr>
					<th><?php echo __('Email Subject');?></th>
					<td><?php echo $scheduledEmail['subject'];?></td>
				</tr>

				<tr>
					<th><?php echo __('Email Message');?></th>
					<td><?php echo $scheduledEmail['message'];?></td>
				</tr>

				<tr>
					<th><?php echo __('Scheduled By');?></th>
					<td>
						<?php
						if(!empty($scheduledEmail['user']['id'])) {
							echo $scheduledEmail['user']['first_name'].' '.$scheduledEmail['user']['last_name'];
						}?>
					</td>
				</tr>

				<tr>
					<th><?php echo __('Status');?></th>
					<td>
						<?php
						if($scheduledEmail['is_sent']) {
							echo "<span class='badge badge-success'>".__('Sent')."</span>";
						}
						else if($scheduledEmail['total_sent_emails'] > 0) {
							echo "<span class='badge badge-info'>".__('Sending')."</span>";
						}
						else {
							echo "<span class='badge badge-danger'>".__('Not Sent')."</span>";
						}?>
					</td>
				</tr>

				<tr>
					<th><?php echo __('Scheduled Date');?></th>
					<td><?php echo $this->UserAuth->getFormatDatetime($scheduledEmail['schedule_date']);?></td>
				</tr>

				<tr>
					<th><?php echo __('Created');?></th>
					<td><?php echo $this->UserAuth->getFormatDatetime($scheduledEmail['created']);?></td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
<br/>

<div class="card">
	<div class="card-header text-white bg-dark">
		<span class="card-title">
			<?php echo __('Scheduled Email Recipients');?>
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
					<th><?php echo __('Action');?></th>
				</tr>
			</thead>

			<tbody>
				<?php
				if(!empty($scheduledEmailRecipients)) {
					foreach($scheduledEmailRecipients as $row) {
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

							echo "<td>";
								if(!$row['is_email_sent']) {
									echo $this->Html->link(__('Delete', true), ['action'=>'deleteRecipient', $row['id'], '?'=>['page'=>$this->UserAuth->getPageNumber()]], ['class'=>'btn btn-primary btn-sm delete-recipient']);
								}
							echo "</td>";
						echo "</tr>";
					}
				} else {
					echo "<tr><td colspan=5><br/>".__('No Recipient Available')."</td></tr>";
				}?>
			</tbody>
		</table>
	</div>
</div>

<script type="text/javascript">
	$(function(){
		$(".delete-recipient").click(function(e) {
			e.preventDefault();
			var self = this;

			if(!confirm("<?php echo __('Are you sure, you want to delete this recipient?');?>")) {
				return false;
			}

			var url = $(this).attr('href');
			var pelem = $(this).closest('tr');

			$(this).html('<img src="'+urlForJs+'usermgmt/img/loading-circle.gif" alt="Delete"/>');
			
			$.ajax({
				type : 'POST',
				url : url,
				headers: {"X-CSRF-Token": "<?php echo $this->request->getCookie('csrfToken');?>"},
				async : true,
				cache : false,
				data : null,
				dataType : 'html',
				beforeSend : function (XMLHttpRequest) {
					$("#loader_modal").modal();
				},
				success : function (data, textStatus) {
					try {
						var data = JSON.parse(data);
						if(data.error == 0) {
							$(pelem).hide('slow', function(){ $(this).remove(); });
						} else {
							$(self).html('Delete');
							alert(data.message);
						}
					} catch(e) {
						$(self).html('Delete');
						alert("<?php echo __('Something went wrong.. Please Try Again');?>");
					}
				},
				complete: function (data, textStatus) {
					$("#loader_modal").modal('hide');
				},
				error: function(jqXHR, textStatus, errorThrown) {
					alert(errorThrown)
				}
			});
		});
	});
</script>