<div class="card">
	<div class="card-header text-white bg-dark">
		<span class="card-title">
			<?php echo __('Cake Logs');?>
		</span>
	</div>

	<div class="card-body p-0">
		<table class="table table-striped table-bordered table-sm table-hover">
			<thead>
				<tr>
					<th><?php echo __('#');?></th>

					<th><?php echo __('Log File');?></th>

					<th><?php echo __('File Size');?></th>

					<th><?php echo __('Last Modified');?></th>

					<th style="width:170px;"><?php echo __('Action');?></th>
				</tr>
			</thead>

			<tbody>
				<?php
				$i = 0;
				
				foreach($logFiles as $logFile) {
					$i++;
					$pathinfo = pathinfo($logFile);
					$filesize = round((filesize($logFile) / 1024), 2);
					$filesizeText = $filesize.' KB';
					
					if($filesize > 1024) {
						$filesize = round(($filesize / 1024), 2);
						$filesizeText = $filesize.' MB';
					}
					
					$filemtime = filemtime($logFile);

					echo "<tr>";
						echo "<td>".$i."</td>";
						
						echo "<td>".$pathinfo['basename']."</td>";
						
						echo "<td>".$filesizeText."</td>";
						
						echo "<td>".date('d-M-Y h:i:s A', $filemtime)."</td>";
						
						echo "<td>";
							echo $this->Html->link(__('View/Edit', true), ['action'=>'cakelog', $pathinfo['basename']]);
							echo "<br/>";

							echo $this->Form->postLink(__('Create Backup Copy', true), ['action'=>'cakelogbackup', $pathinfo['basename']], ['confirm'=>__('Are you sure, you want to create a copy of ').$pathinfo['basename'].'?']);
							echo "<br/>";

							echo $this->Form->postLink(__('Delete', true), ['action'=>'cakelogdelete', $pathinfo['basename']], ['confirm'=>__('Are you sure, you want to delete the log file ').$pathinfo['basename'].'?']);
							echo "<br/>";

							echo $this->Form->postLink(__('Empty File', true), ['action'=>'cakelogempty', $pathinfo['basename']], ['confirm'=>__('Are you sure, you want to make empty the log file ').$pathinfo['basename'].'? '.__('You should create a backup before making empty this file.')]);
						echo "</td>";
					echo "</tr>";
				}?>
			</tbody>
		</table>
		
		<div style="padding:15px">
			<?php echo __('I recommend you to take a backup of log files then make empty them weekly or monthly. It can improve site performance.');?>
			<br/><br/>
			
			<?php
			if(!empty($filename)) {
				$filepath = LOGS.$filename;
				$filesize = round((filesize($filepath) / 1024), 1);
				$pathinfo = pathinfo($filepath);?>

				<div class="clearfix">
					<div class="float-right">
						<?php echo $this->Html->link(__('Close', true), ['action'=>'cakelog'], ['class'=>'btn btn-primary btn-sm']);?>
					</div>
					<h4><?php echo $filename.__(' details');?></h4>
				
				</div>
				<br/>

				<?php echo $this->Form->create(null, ['onsubmit'=>'return confirm("Are you sure, Saving this file will overwrite existing file")']);?>

				<?php echo $this->Form->control('UserSettings.logfile', ['type'=>'textarea', 'label'=>false, 'class'=>'p-3', 'style'=>'width:99%;height:200px', 'value'=>file_get_contents($filepath)]);?>
				
				<div class="row form-group border-top pt-3">
					<div class="col">
						<?php echo $this->Form->Submit(__('Save'), ['class'=>'btn btn-primary']);?>
					</div>
				</div>
				
				<?php echo $this->Form->end();?>
			<?php
			}?>
		</div>
	</div>
</div>