<div id="updateStaticPagesIndex">
	<?php echo $this->Search->searchForm('StaticPages', ['legend'=>false, 'updateDivId'=>'updateStaticPagesIndex']);?>
	<?php echo $this->element('Usermgmt.paginator', ['useAjax'=>true, 'updateDivId'=>'updateStaticPagesIndex']);?>

	<div class="table-responsive">
		<table class="table table-striped table-bordered table-sm table-hover">
			<thead>
				<tr>
					<th><?php echo __('#');?></th>

					<th class="psorting"><?php echo $this->Paginator->sort('StaticPages.page_name', __('Page Name'));?></th>

					<th class="psorting"><?php echo $this->Paginator->sort('StaticPages.url_name', __('Url Name'));?></th>

					<th class="psorting"><?php echo $this->Paginator->sort('StaticPages.page_title', __('Page Title'));?></th>

					<th><?php echo __('Page Link');?></th>

					<th class="psorting"><?php echo $this->Paginator->sort('StaticPages.created', __('Created'));?></th>

					<th><?php echo __('Action');?></th>
				</tr>
			</thead>

			<tbody>
				<?php
				if(!empty($staticPages)) {
					$i = $this->UserAuth->getPageStart();

					foreach($staticPages as $row) {
						$i++;
						
						echo "<tr>";
							echo "<td>".$i."</td>";

							echo "<td>".$row['page_name']."</td>";

							echo "<td>".$row['url_name']."</td>";

							echo "<td>".$row['page_title']."</td>";

							echo "<td>";
								echo "<a href='".SITE_URL.'StaticPages/'.$row['url_name']."'>".SITE_URL.'StaticPages/'.$row['url_name']."</a>";
							echo "</td>";

							echo "<td>".$this->UserAuth->getFormatDate($row['created'])."</td>";

							echo "<td>";
								echo "<div class='dropdown'>";
									echo "<button class='btn btn-dark btn-sm dropdown-toggle' type='button' data-toggle='dropdown' aria-haspopup='true' aria-expanded='false'>".__('Action')."</button>";
									
									echo "<div class='dropdown-menu dropdown-menu-right'>";
										echo $this->Html->link(__('View Page'), ['controller'=>'StaticPages', 'action'=>'view', $row['id'], '?'=>['page'=>$this->UserAuth->getPageNumber()]], ['escape'=>false, 'class'=>'dropdown-item']);

										echo $this->Html->link(__('Edit Page'), ['controller'=>'StaticPages', 'action'=>'edit', $row['id'], '?'=>['page'=>$this->UserAuth->getPageNumber()]], ['escape'=>false, 'class'=>'dropdown-item']);

										echo $this->Form->postLink(__('Delete Page'), ['controller'=>'StaticPages', 'action'=>'delete', $row['id']], ['escape'=>false, 'class'=>'dropdown-item', 'confirm'=>__('Are you sure you want to delete this page?')]);
									echo "</div>";
								echo "</div>";
							echo "</td>";
						echo "</tr>";
					}
				} else {
					echo "<tr><td colspan=7><br/>".__('No Records Available')."</td></tr>";
				}?>
			</tbody>
		</table>
	</div>

	<?php
	if(!empty($staticPages)) {
		echo $this->element('Usermgmt.pagination', ['paginationText'=>__('Number of Pages')]);
	}?>
</div>