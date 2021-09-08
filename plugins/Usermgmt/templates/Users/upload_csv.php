<div class="card">
	<div class="card-header text-white bg-dark">
		<span class="card-title">
			<?php echo __('Add Multiple Users');?>
		</span>

		<span class="card-title float-right">
			<?php echo $this->Html->link(__('Back', true), ['action'=>'index'], ['class'=>'btn btn-secondary btn-sm']);?>
		</span>
	</div>

	<div class="card-body">
		<?php echo $this->Form->create(null, ['novalidate'=>true, 'type'=>'file']);?>

		<div class="form-row align-items-center">
			<div class="col-auto">
				<label class="col-form-label required"><?php echo __('Select csv file');?></label>
			</div>

			<div class="col-auto">
				<?php echo $this->Form->control('csv_file', ['type'=>'file', 'label'=>false, 'style'=>'width:auto;display:inline']);?>
			</div>

			<div class="col-auto">
				<?php echo $this->Form->Submit(__('Upload'), ['class'=>'btn btn-primary btn-sm', 'style'=>'margin-bottom:0']);?>
			</div>
		</div>

		<?php echo $this->Form->end();?>

		<hr/>

		<div>
			<strong style="margin-left:5px;"><?php echo __('Instructions for CSV file');?></strong>
			<br/><br/>

			<ol>
				<li><?php echo __('First line should be table fields name');?></li>

				<li><?php echo __('You can add one or more than one users');?></li>

				<li><?php echo __('leave blank for empty values');?></li>

				<li>
					<?php echo __('For user group id field value should be in following');?>
					<?php foreach($userGroups as $key=>$val) {
						echo "<br/><strong>For ".$val." set ".$key."</strong>";
					}?>
				</li>

				<li><?php echo __('For multiple groups set group ids comma separated without space for e.g. 1,2');?></li>

				<li>
					<?php echo __('For gender field value should be in following');?>
					<?php foreach($genders as $key=>$val) {
						echo "<br/><strong>".$key."</strong>";
					}?>
				</li>

				<li><?php echo __('For Birthday date format should be Ymd format for e.g. 1999-01-25');?></li>

				<li><a href="<?php echo SITE_URL;?>usermgmt/files/sample_multiple_users.csv" target="_blank"><?php echo __('Sample CSV File');?></a> <?php echo __('for multiple users');?></li>
			</ol>
		</div>
	</div>
</div>