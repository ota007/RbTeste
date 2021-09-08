<?php
/**
 * CakePHP 4.x User Management Plugin
 * Copyright (c) Chetan Varshney (The Director of Ektanjali Softwares Pvt Ltd), Product Copyright No- 11498/2012-CO/L
 *
 * Licensed under The GPL License
 * For full copyright and license information, please see the LICENSE.txt
 *
 * Product From - https://ektanjali.com
 * Product Demo - https://cakephp4-user-management.ektanjali.com
 */

if(!isset($paginationText)) {
	$paginationText =__('Total Records');
}

// these templates are customized as per bootstrap pagination
$this->Paginator->setTemplates([
	'nextActive'=>'<li class="next page-item d-inline-block"><a class="page-link" rel="next" href="{{url}}">{{text}}</a></li>',
	'prevActive'=>'<li class="prev page-item d-inline-block"><a class="page-link" rel="prev" href="{{url}}">{{text}}</a></li>',
	'first'=>'<li class="first page-item d-inline-block"><a class="page-link" href="{{url}}">{{text}}</a></li>',
	'last'=>'<li class="last page-item d-inline-block"><a class="page-link" href="{{url}}">{{text}}</a></li>',
	'number'=>'<li class="page-item d-inline-block"><a class="page-link" href="{{url}}">{{text}}</a></li>',
	'current'=>'<li class="active page-item d-inline-block"><a class="page-link" href="">{{text}}</a></li>'
]);?>

<div class="pagination2 p-3">
	<ul class="pagination" style="display:block;">
		<?php
		echo "<div class='pagination-navigation pr-3 mb-3 d-inline-block'>";
			$firstP = $this->Paginator->first(__('First'));
			
			if(!empty($firstP)) {
				echo $firstP;
			} else {
				echo "<li class='page-item d-inline-block disabled'><a class='page-link' href='#'>".__('First')."</a></li>";
			}

			if($this->Paginator->hasPrev()) {
				echo $this->Paginator->prev(__('Previous'));
			} else {
				echo "<li class='page-item d-inline-block disabled'><a class='page-link' href='#'>".__('Previous')."</a></li>";
			}

			if($this->Paginator->hasNext()) {
				echo $this->Paginator->next(__('Next'));
			} else {
				echo "<li class='page-item d-inline-block disabled'><a class='page-link' href='#'>".__('Next')."</a></li>";
			}

			$lastP = $this->Paginator->last(__('Last'));
			
			if(!empty($lastP)) {
				echo $lastP;
			} else {
				echo "<li class='page-item d-inline-block disabled'><a class='page-link' href='#'>".__('Last')."</a></li>";
			}
		echo "</div>";

		echo "<div class='pagination-numbers pr-3 mb-3 d-inline-block'>";
			echo $this->Paginator->numbers(['separator'=>'', 'currentTag'=>'span']);
		echo "</div>";
		
		echo "<div class='pagination-info pr-3 mb-3 d-inline-block'>";
			echo "<li class='page-item disabled d-inline-block'><a class='page-link' href='#'>".$this->Paginator->counter($paginationText.' {{count}}')."</a></li>";

			echo "<li class='page-item d-inline-block disabled'><a class='page-link' href='#'>".$this->Paginator->counter(__('Page').' {{page}} '.__('of').' {{pages}}')."</a></li>";

		echo "</div>";?>
	</ul>
</div>