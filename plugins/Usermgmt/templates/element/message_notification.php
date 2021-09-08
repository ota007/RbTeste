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

if($this->request->getSession()->check('Flash')) {
	$flashSession = $this->request->getSession()->consume('Flash');

	echo "<div style='position:absolute; top:20px; right:20px; z-index:100000; width:auto;'>";
		foreach($flashSession as $flashMessages) {
			if(!isset($flashMessages[0])) {
				$flashMessages = [$flashMessages];
			}
			
			foreach($flashMessages as $flash) {
				$bgClass = 'bg-success';
				$flashTitle = 'Success';
				$flashMsg = $flash['message'];

				if(!empty($flash['element'])) {
					if(strpos($flash['element'], 'error') !== false) {
						$bgClass = 'bg-danger';
					}
					else if(strpos($flash['element'], 'success') !== false) {
						$bgClass = 'bg-success';
					}
					else if(strpos($flash['element'], 'warning') !== false) {
						$bgClass = 'bg-warning';
					}
					else if(strpos($flash['element'], 'info') !== false) {
						$bgClass = 'bg-info';
					}
				}
				
				if(!empty($flash['params']['title'])) {
					$flashTitle = $flash['params']['title'];
				}
				else if($bgClass == 'bg-danger') {
					$flashTitle = 'Error';
				}
				else if($bgClass == 'bg-warning') {
					$flashTitle = 'Warning';
				}
				else if($bgClass == 'bg-info') {
					$flashTitle = 'Info';
				}?>

				<div class="toast <?php echo $bgClass;?>" role="alert" aria-live="assertive" aria-atomic="true" style="width:100%;">
					<div class="toast-header">
						<strong class="mr-auto"><?php echo $flashTitle;?></strong>
						<button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="toast-body text-white">
						<?php echo $flashMsg;?>
					</div>
				</div>
			<?php
			}
		}
	echo "</div>";
}?>

<script type="text/javascript">
	$(function() {
		$('.toast').toast({
			autohide : false
		}).toast('show');
	});
</script>