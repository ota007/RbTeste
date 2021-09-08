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

define('GMAIL_LOGIN', true);
define('LDN_LOGIN', true);

// if(USE_FB_LOGIN || USE_TWT_LOGIN || USE_GMAIL_LOGIN || USE_LDN_LOGIN) {
if(USE_FB_LOGIN || USE_TWT_LOGIN || GMAIL_LOGIN || LDN_LOGIN) {?>
	<hr/>
	<div class="social_logins">
		<?php
		if(LDN_LOGIN){?>
			<a href="#" class="btn btn-block btn-primary">
				<i class="fab fa-linkedin-in mr-2"></i>
				Registrar com LinkedIn
			</a>
			<!-- <a href="#" id="ldn" class="ldn_btn">
				<img src="<?php //echo SITE_URL;?>usermgmt/img/linkedin-logo.png" width="50" height="50" border="0" alt=""><span>Continue with LinkedIn</span>
			</a> -->
		<?php
		}
		if(GMAIL_LOGIN){?>
			<a href="#" class="btn btn-block btn-danger">
				<i class="fab fa-google mr-2"></i>
				Registrar com Google
			</a>
			<!-- <a href="#" id="gmail" class="google_btn">
				<img src="<?php //echo SITE_URL;?>usermgmt/img/google-logo.svg" width="50" height="50" border="0" alt=""><span>Continue with Google</span>
			</a> -->
		
		<?php
		}
		if(USE_FB_LOGIN){?>
			<a href="#" id="fb" class="fb_btn">
				<img src="<?php echo SITE_URL;?>usermgmt/img/facebook-logo.png" width="50" height="50" border="0" alt=""><span>Continue with Facebook</span>
			</a>
		<?php
		}
		if(USE_TWT_LOGIN){?>
			<a href="#" id="twt" class="twt_btn">
				<img src="<?php echo SITE_URL;?>usermgmt/img/twitter-logo.png" width="50" height="50" border="0" alt=""><span>Continue with Twitter</span>
			</a>
		<?php
		}?>
	</div>
<?php
}?>

<style type="text/css">
	.social_logins {
		max-width: 280px;
		margin:0 auto;
	}
	.social_logins a {
		border-radius: 5px;
		padding: 5px 10px;
		width: 100%;
		font-size: 106%;
		letter-spacing: .4px;
		display: block;
		margin: 15px 0;
		color:#ffffff;
	}
	.social_logins a span {
		margin-left: 10px;
	}
	.social_logins .fb_btn {
		background: #4267B2;
	}
	.social_logins .google_btn {
		background: #4285F4;
	}
	.social_logins .twt_btn {
		background: #1DA1F2;
	}
	.social_logins .ldn_btn {
		background: #0077B5;
	}
</style>
<script type="text/javascript">
	$(function(){
		$(".social_logins a").click(function(e) {
			var social_account = $(this).attr('id');
			e.preventDefault();

			var screenX = typeof window.screenX != 'undefined' ? window.screenX : window.screenLeft;
			var screenY = typeof window.screenY != 'undefined' ? window.screenY : window.screenTop;
			var outerWidth = typeof window.outerWidth != 'undefined' ? window.outerWidth : document.body.clientWidth;
			var outerHeight = typeof window.outerHeight != 'undefined' ? window.outerHeight : (document.body.clientHeight - 22);
			var width = 580;
			var height = 500;
			var left = parseInt(screenX + ((outerWidth - width) / 2), 10);
			var top = parseInt(screenY + ((outerHeight - height) / 2.5), 10);
			var features = (
					'width=' + width +
					',height=' + height +
					',left=' + left +
					',top=' + top+
					',scrollbars=yes'
				);

			var newwindow = window.open('login/'+social_account, '', features);

			if(window.focus) {
				newwindow.focus()
			}
		});
	});
</script>
