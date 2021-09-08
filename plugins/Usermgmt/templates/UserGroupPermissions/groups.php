<div class="card">
	<div class="card-header text-white bg-dark">
		<span class="card-title">
			<?php echo __('Parent Group Permissions Matrix');?>
		</span>

		<span class="card-title float-right">
			<?php echo "<a id='permissionOptions' href='javascript:void(0)' class='btn btn-secondary btn-sm'>Choose Options</a>";?>
		</span>

		<span class="card-title float-right mr-2">
			<div id="per_loading_text" style="color:red;text-decoration:blink;"><?php echo __('Please wait while page is loading...');?></div>
		</span>
	</div>

	<div class="card-body p-2">
		<div>
			<?php echo $this->Html->link(__('Check Permission Changes'), ['controller'=>'UserGroupPermissions', 'action'=>'printPermissionChanges', 'plugin'=>'Usermgmt'], ['class'=>'btn btn-primary btn-sm']);?>
			<br/><br/>
			
			<?php echo $this->Html->image(SITE_URL.'usermgmt/img/approve.png', ['alt'=>__('Yes')]);?> = <?php echo __("The group has permission of controller's action");?><br/><?php echo $this->Html->image(SITE_URL.'usermgmt/img/remove.png', ['alt'=>__('No')]);?> = <?php echo __("The group has not permission of controller's action");?><br/>
		</div>
		<br/>

		<div style="display:<?php if(!empty($selectedControllers)) { echo 'none'; } ?>" id="permissionMatrix">
			<?php echo $this->Form->create($userGroupPermissionEntity, ['onsubmit'=>'return validateForm()']);?>

			<div class="d-inline-block mr-3 align-top">
				<table class="table table-striped table-bordered table-sm table-hover">
					<thead>
						<tr>
							<th><?php
								$checked = false;
								if(!empty($userGroupPermissionEntity['sel_cont_all'])) {
									$checked = true;
								}
								
								echo $this->Form->control('UserGroupPermissions.sel_cont_all', ['type'=>'checkbox', 'label'=>false, 'checked'=>$checked, 'class'=>'contcheckall']);?>
							</th>
							<th><?php echo __('Prefix');?></th>
							<th><?php echo __('Plugin');?></th>
							<th><?php echo __('Controller');?></th>
						</tr>
					</thead>

					<tbody>
						<?php
						if(!empty($allControllerClasses)) {
							foreach($allControllerClasses as $key=>$controllerClass) {
								$ppc = $controllerClass['prefix'].":".$controllerClass['plugin'].":".$controllerClass['controller'];
								
								$checked = false;
								if(!empty($selectedControllers[$ppc])) {
									$checked = true;
								}
								
								echo "<tr>";
									echo "<td>";
										echo $this->Form->control('UserGroupPermissions.ControllerList.'.$key.'.name', ['type'=>'checkbox', 'label'=>false, 'hiddenField'=>false, 'checked'=>$checked, 'class'=>'contcheck', 'value'=>$ppc]);
									echo "</td>";
									
									echo "<td>".str_replace('.', '/', $controllerClass['prefix'])."</td>";
									
									echo "<td>".$controllerClass['plugin']."</td>";
									
									echo "<td>".$controllerClass['controller']."</td>";
								echo "</tr>";
							}
						}?>
					</tbody>
				</table>
			</div>
			
			<div class="d-inline-block mr-3 align-top">
				<table class="table table-striped table-bordered table-sm table-hover">
					<thead>
						<tr>
							<th><?php
								$checked = false;
								if(!empty($userGroupPermissionEntity['sel_grp_all']) || count($userGroups) == count($selectedUserGroups)) {
									$checked = true;
								}
								
								echo $this->Form->control('UserGroupPermissions.sel_grp_all', ['type'=>'checkbox', 'label'=>false, 'checked'=>$checked, 'class'=>'grpcheckall']);?>
							</th>
							<th><?php echo __('Parent Group');?></th>
						</tr>
					</thead>

					<tbody>
						<?php
						if(!empty($userGroups)) {
							foreach($userGroups as $group) {
								$checked = false;
								if(!empty($selectedUserGroupIds[$group['id']])) {
									$checked = true;
								}
								
								echo "<tr>";
									echo "<td>";
										echo $this->Form->control('UserGroupPermissions.GroupList.'.$group['id'].'.grpcheck', ['type'=>'checkbox', 'label'=>false, 'hiddenField'=>false, 'checked'=>$checked, 'class'=>'grpcheck']);
									echo "</td>";
									
									echo "<td>".$group['name']."</td>";
								echo "</tr>";
							}
						}?>
					</tbody>
				</table>
			</div>
			
			<div class="d-inline-block align-top">
				<?php echo $this->Form->Submit(__('Get Permissions'), ['class'=>'btn btn-primary']);?>
			</div>
			
			<?php echo $this->Form->end();?>
		</div>
		
		<?php
		$loadingImg = $this->Html->image(SITE_URL.'usermgmt/img/loading-circle.gif', ['alt'=>__('saving')]);
		$yes = $this->Html->image(SITE_URL.'usermgmt/img/approve.png', ['alt'=>__('Yes')]);
		$no = $this->Html->image(SITE_URL.'usermgmt/img/remove.png', ['alt'=>__('No')]);

		if(!empty($selectedControllers)) {
			foreach($selectedControllers as $key=>$row) {
				$ppc = $row['prefix'].':'.$row['plugin'].':'.$row['controller'];
				$plugin = ($row['plugin']) ? $row['plugin'] : 'false';
				$prefix = ($row['prefix']) ? $row['prefix'] : 'false';

				echo "<div style='position:relative;'>";
					echo "<hr/>";
					echo "<div class='text-center'><h4>Controller - ".$row['controller']."</h4></div>";
					echo "<hr/>";

					echo "<div class='table-responsive fix-column'>";
						echo "<table class='table table-striped table-bordered table-hover table-sm' style='width:auto;'>";
							echo "<tbody>";
								if(!empty($row['actions'])) {
									echo "<tr>";
										echo "<th class='fixcol'>".__('Actions')."</th>";
										
										//duplicate column
										echo "<th style='min-width:100px;' class='blankcol'>".__('Actions')."</th>";

										foreach($selectedUserGroups as $group) {
											echo "<th style='min-width:100px;' class='text-center'>".$group['name']."</th>";
										}
									echo "</tr>";

									foreach($row['actions'] as $action) {
										echo "<tr class='permission_row'>";
											echo "<td class='fixcol'>";
												echo $action;

												if(!empty($funcDesc[$ppc][$action])) {
													echo "<br/><span style='color:red; font-size:10px; font-style:italic'>".$funcDesc[$ppc][$action]."</span>";
												}
											echo "</td>";
											
											//duplicate column
											echo "<td style='min-width:100px;' class='blankcol'>";
												echo $action;

												if(!empty($funcDesc[$ppc][$action])) {
													echo "<br/><span style='color:red; font-size:10px; font-style:italic'>".$funcDesc[$ppc][$action]."</span>";
												}
											echo "</td>";

											foreach($selectedUserGroups as $group) {
												
												echo "<td style='min-width:100px;' class='text-center p-0' title='".$group['name']." (Click to change permission)'>";
													$img = $no;
													if(isset($dbPermissions[$ppc][$action][$group['id']]) && $dbPermissions[$ppc][$action][$group['id']] == 1) {
														$img = $yes;
													}

													echo $this->Html->link($img, ['action'=>'changeGroupPermission', $row['controller'], $action, $group['id'], $plugin, $prefix], ['escape'=>false, 'class'=>'change_permission']);
												echo "</td>";
											}
										echo "</tr>";
									}
								} else {
									echo "<tr><td colspan='".(count($selectedUserGroups) + 2)."'>No Actions</td></tr>";
								}
							echo "</tbody>";
						echo "</table>";
					echo "</div>";
				echo "</div>";
				echo "<br/><br/>";
			}
		}?>
	</div>
</div>

<script type="text/javascript">
	$loadingImg = '<?php echo $loadingImg;?>';
	$yes = '<?php echo $yes;?>';
	$no = '<?php echo $no;?>';

	$(function() {
		$(".fix-column tbody tr").each(function () {
			$td = $(this).find('.blankcol');
			$($td).css("width", Math.ceil($($td).width()));
			$(this).find('.fixcol').css("width", Math.ceil($($td).outerWidth()));
			$(this).find('.fixcol').css("height", Math.ceil($($td).outerHeight()));
		});
		
		$('.contcheckall').change(function() {
			if($(this).is(':checked')) {
				$(".contcheck").prop("checked", true);
			} else {
				$(".contcheck").prop("checked", false);
			}
		});
		
		$('.contcheck').change(function() {
			if(!$(this).is(':checked')) {
				$(".contcheckall").prop("checked", false);
			}
		});
		
		$('.grpcheckall').change(function() {
			if($(this).is(':checked')) {
				$(".grpcheck").prop("checked", true);
			} else {
				$(".grpcheck").prop("checked", false);
			}
		});
		
		$('.grpcheck').change(function() {
			if(!$(this).is(':checked')) {
				$(".grpcheckall").prop("checked", false);
			}
		});
		
		$('#permissionOptions').click(function() {
			$('#permissionMatrix').slideToggle();
		});
		
		$(document).on('click', '.permission_row .change_permission', function(e) {
			e.preventDefault();
			
			$(this).html($loadingImg);

			var url = $(this).attr('href');
			
			$.ajax({
				async : true,
				data : null,
				dataType : 'html',
				context : this,
				success : function (result, textStatus, jqXHR) {
					if(result == '1') {
						$(this).html($yes);
					} else {
						$(this).html($no);
					}
				},
				error : function(jqXHR, textStatus, errorThrown) {
					alert('Permission not saved, Please refresh page and try again');
				},
				type : 'POST',
				url : url,
				headers: {"X-CSRF-Token": "<?php echo $this->request->getCookie('csrfToken');?>"}
			});
		});
		
		$('#per_loading_text').remove();
	});
	
	function validateForm() {
		if(!$(".contcheck").is(':checked')) {
			alert('Please select atleast one controller to get permissions');
			return false;
		}
		return true;
	}
</script>

<style type="text/css">
	.permission_row .change_permission img {
		padding:5px;
	}
	.fix-column thead .fixcol {
		position: absolute;
		left: 0;
		margin-top: -1px;
		height: 37px;
		border-right: 0;
	}
	.fix-column tbody .fixcol {
		position: absolute;
		left: 0;
		margin-top: -1px;
		background-color:bisque;
	}
	.fix-column tbody .blankcol {
		white-space: nowrap;
	}
	.fix-column tbody .blankcol span {
		white-space: normal;
	}
</style>