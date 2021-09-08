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

	foreach($flashSession as $flashMessages) {
		if(!isset($flashMessages[0])) {
			$flashMessages = [$flashMessages];
		}

		foreach($flashMessages as $flash) {
			$flashMsgClass = 'success';
			$flashMsg = $flash['message'];
			
			if(!empty($flash['params']['class'])) {
				$flashMsgClass = $flash['params']['class'];
			}
			else {
				if(!empty($flash['element'])) {
					if(strpos($flash['element'], 'error') !== false) {
						$flashMsgClass = 'error';
					}
					else if(strpos($flash['element'], 'success') !== false) {
						$flashMsgClass = 'success';
					}
					else if(strpos($flash['element'], 'warning') !== false) {
						$flashMsgClass = 'warning';
					}
					else if(strpos($flash['element'], 'info') !== false) {
						$flashMsgClass = 'info';
					}
				}
			}?>

			<div class="messageHolder">
				<div class="<?php echo $flashMsgClass;?>" id="flashMessage">
					<span><?php echo $flashMsg;?></span>
					
					<a href="javascript:void(0)" class="closeMsg"><img src="<?php echo SITE_URL?>usermgmt/img/closelabel.png" border="0" alt="Close"></a>
				</div>
			</div>
		<?php
		}
	}
}?>

<style type="text/css">
	.messageHolder .info, .messageHolder .success, .messageHolder .warning, .messageHolder .error, .messageHolder .validation {
		border:1px solid;
		margin:10px;
		padding:15px 30px 15px 50px;
		background-repeat:no-repeat;
		background-position:10px center;
		border-radius:3px;
		position:relative;
	}
	.messageHolder .info {
		color:#00529B;
		background-color:#BDE5F8;
		background-image:url("<?php echo SITE_URL;?>usermgmt/img/info.png");
	}
	.messageHolder .success {
		color:#4F8A10;
		background-color:#DFF2BF;
		background-image:url("<?php echo SITE_URL;?>usermgmt/img/success.png");
	}
	.messageHolder .warning {
		color:#9F6000;
		background-color:#FEEFB3;
		background-image:url("<?php echo SITE_URL;?>usermgmt/img/warning.png");
	}
	.messageHolder .error {
		color:#D8000C;
		background-color:#FFBABA;
		background-image:url("<?php echo SITE_URL;?>usermgmt/img/error.png");
	}
	.messageHolder .closeMsg {
		position:absolute;
		right:5px;
		top:10px;
	}
	.messageHolder .closeMsgAjax {
		position:absolute;
		right:5px;
		top:10px;
	}
</style>

<script type="text/javascript">
	$(function(){
		$(document).on('click', '.closeMsg', function(){
			$(this).closest('.messageHolder').remove();
		});
	});
</script>