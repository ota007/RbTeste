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

if(!isset($useAjax)) {
	$useAjax = true;
}

if(isset($updateDivId) && $useAjax) {?>
	<script type="text/javascript">
		var $updateDivId = '#<?php echo $updateDivId;?>';

		$(function(){
			$(document).on("mouseenter", "table tr th a", function() {
				$(this).attr('title', 'Click to sort records');
			});

			$($updateDivId+' .psorting a, '+$updateDivId+' .pagination a').click(function(e) {
				e.preventDefault();
				var url = $(this).attr('href');

				if(url) {
					$.ajax({
						type: 'GET',
						url: url,
						async: true,
						data: null,
						dataType: 'html',
						beforeSend : function (XMLHttpRequest) {
							$("#loader_modal").modal();
						},
						success: function (data, textStatus) {
							$($updateDivId).html(data);

							if(window.history.pushState) {
								window.history.pushState({},"", url);
							}
						},
						complete: function (data, textStatus) {
							$("#loader_modal").modal('hide');
						},
						error: function(jqXHR, textStatus, errorThrown) {
							alert(errorThrown)
						}
					});
				}
			});
		});
	</script>
<?php
}?>

<style type="text/css">
	table th a.asc:after {
		content:' ⇣';
	}
	table th a.desc:after {
		content:' ⇡';
	}
	@media (max-width: 767px) {
		.table-responsive .dropdown-menu, .table-responsive .dropdown-toggle {
			position: static !important;
		}
	}
	@media (min-width: 768px) {
		.table-responsive {
			overflow: visible;
		}
	}
</style>