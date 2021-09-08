<div class="card">
	<div class="card-header text-white bg-dark">
		<span class="card-title">
			<?php echo __('Static Page Detail');?>
		</span>

		<span class="card-title float-right">
			<?php echo $this->Html->link(__('Back', true), ['action'=>'index', '?'=>['page'=>$this->UserAuth->getPageNumber()]], ['class'=>'btn btn-secondary btn-sm']);?>
		</span>

		<span class="card-title float-right mr-2">
			<?php echo $this->Html->link(__('Edit', true), ['action'=>'edit', $staticPage['id'], '?'=>['page'=>$this->UserAuth->getPageNumber()]], ['class'=>'btn btn-secondary btn-sm']);?>
		</span>
	</div>

	<div class="card-body p-0">
		<table class="table table-striped table-bordered table-sm">
			<tbody>
				<tr>
					<td><strong><?php echo __('Page Name');?></strong></td>
					<td><?php echo $staticPage['page_name'];?></td>
				</tr>
				
				<tr>
					<td><strong><?php echo __('Url Name');?></strong></td>
					<td><?php echo $staticPage['url_name'];?></td>
				</tr>
				
				<tr>
					<td><strong><?php echo __('Page Link');?></strong></td>
					<td><a href="<?php echo SITE_URL.'StaticPages/'.$staticPage['url_name'];?>"><?php echo SITE_URL.'StaticPages/'.$staticPage['url_name'];?></a></td>
				</tr>
				
				<tr>
					<td><strong><?php echo __('Page Title');?></strong></td>
					<td><?php echo $staticPage['page_title'];?></td>
				</tr>
				
				<tr>
					<td><strong><?php echo __('Page Content');?></strong></td>
					<td><?php echo $staticPage['page_content'];?></td>
				</tr>
				
				<tr>
					<td><strong><?php echo __('Created');?></strong></td>
					<td><?php echo $this->UserAuth->getFormatDatetime($staticPage['created']);?></td>
				</tr>
				
				<tr>
					<td><strong><?php echo __('Modified');?></strong></td>
					<td><?php echo $this->UserAuth->getFormatDatetime($staticPage['modified']);?></td>
				</tr>
			</tbody>
		</table>
	</div>
</div>