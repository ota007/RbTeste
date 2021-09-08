<script type="text/javascript">
	$(function(){
		var formId = '#<?php echo $formId;?>';
		var button = '';

		<?php
		if(isset($submitButtonId)) {?>
			button = '#<?php echo $submitButtonId;?>';
		<?php
		} else if(isset($submitButtonClass)) {?>
			button = '.<?php echo $submitButtonClass;?>';
		<?php
		}?>

		if(button) {
			var validate = ajaxValidation();
			
			$(button).click(function(e){
				var self = this;
				var url = $(formId).attr('action');
				var formid = $(formId);

				validate.doPost({
					url: url,
					submitbtn: self,
					formid: formid,
					callback: function(message) {
						if(message == 'error') {
							$(self).unbind();
						}
						else {
							$(formId).submit();
						}
					}
				});

				return false;
			});
		}
	});
</script>

<style type="text/css">
	form .error-message {
		color:#9E2424;
	}
	form .required:after {
		color:#EE3322;
		content:"*";
		display:inline;
	}
</style>