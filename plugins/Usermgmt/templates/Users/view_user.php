<div class="card">
	<div class="card-header text-white bg-dark">
		<span class="card-title">
			<?php echo __('User Detail');?>
		</span>

		<span class="card-title float-right">
			<?php echo $this->Html->link(__('Back', true), ['action'=>'index', '?'=>['page'=>$this->UserAuth->getPageNumber()]], ['class'=>'btn btn-secondary btn-sm']);?>
		</span>

		<span class="card-title float-right mr-2">
			<?php echo $this->Html->link(__('Edit', true), ['action'=>'editUser', $userId, '?'=>['page'=>$this->UserAuth->getPageNumber()]], ['class'=>'btn btn-secondary btn-sm']);?>
		</span>
	</div>

	<div class="card-body">
		<div style="display:inline-block;">
			<?php
			if(!empty($user)) {?>
				<table class="table-sm" style="width:auto">
					<tbody>
						<tr>
							<td>
								<div class="profile">
									<img alt="<?php echo $user['first_name'].' '.$user['last_name'];?>" src="<?php echo $this->Image->resize('library/'.IMG_DIR, $user['photo'], ['width'=>200, 'aspect'=>true]);?>">
								</div>
							</td>
							<td><h1><?php echo $user['first_name'].' '.$user['last_name'];?></h1></td>
						</tr>
						
						<tr>
							<td style="text-align:right"><strong><?php echo __('Group(s)');?>:</strong></td>
							<td><?php echo $user['group_name'];?></td>
						</tr>
						
						<tr>
							<td style="text-align:right"><strong><?php echo __('Username');?>:</strong></td>
							<td><?php echo $user['username'];?></td>
						</tr>

						<tr>
							<td style="text-align:right"><strong><?php echo __('Email');?>:</strong></td>
							<td><?php echo $user['email'];?></td>
						</tr>

						<tr>
							<td style="text-align:right"><strong><?php echo __('Gender');?>:</strong></td>
							<td><?php echo ucwords($user['gender']);?></td>
						</tr>

						<tr>
							<td style="text-align:right"><strong><?php echo __('Birthday');?>:</strong></td>
							<td><?php echo $this->UserAuth->getFormatDate($user['bday']);?></td>
						</tr>

						<tr>
							<td style="text-align:right"><strong><?php echo __('Cellphone');?>:</strong></td>
							<td><?php echo $user['user_detail']['cellphone'];?></td>
						</tr>

						<tr>
							<td style="text-align:right"><strong><?php echo __('Location');?>:</strong></td>
							<td><?php echo $user['user_detail']['location'];?></td>
						</tr>

						<tr>
							<td style="text-align:right"><strong><?php echo __('Status');?>:</strong></td>
							<td><?php echo ($user['is_active']) ? __('Active') : __('Inactive');?></td>
						</tr>

						<tr>
							<td style="text-align:right"><strong><?php echo __('Email Verified');?></strong></td>
							<td><?php echo ($user['is_email_verified']) ? __('Yes') : __('No');?></td>
						</tr>

						<tr>
							<td style="text-align:right"><strong><?php echo __('Ip Address');?>:</strong></td>
							<td><?php echo $user['ip_address'];?></td>
						</tr>

						<tr>
							<td style="text-align:right"><strong><?php echo __('Joined');?>:</strong></td>
							<td><?php echo $this->UserAuth->getFormatDate($user['created']);?></td>
						</tr>

						<tr>
							<td style="text-align:right"><strong><?php echo __('Created By');?>:</strong></td>
							<td><?php echo ($user['created_by']) ? $user['created_by'] : '';?></td>
						</tr>

						<tr>
							<td style="text-align:right"><strong><?php echo __('Last Login');?>:</strong></td>
							<td><?php echo $this->UserAuth->getFormatDate($user['last_login']);?></td>
						</tr>
					</tbody>
				</table>
			<?php
			}?>
		</div>
	</div>
</div>

<style type="text/css">
	.profile img {
		border:1px solid #DFDCDC;
		display:block;
		margin:0;
		padding:5px;
		width:100%;
		max-width:200px;
	}
</style>