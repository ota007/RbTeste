<!DOCTYPE html>
<html lang="en">
<head>
	<?php echo $this->Html->charset();?>
	<title><?php echo $this->fetch('title');?> Cakephp 4.x User Management Premium Plugin with Twitter Bootstrap | Ektanjali Softwares</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<script language="javascript">
		var urlForJs="<?php echo SITE_URL;?>";
	</script>

	<?php
		echo $this->Html->meta('icon');

		/* Bootstrap CSS is taken from https://getbootstrap.com */
		echo $this->Html->css('/plugins/bootstrap/css/bootstrap.min.css?v=4.5.0');

		/* Bootstrap Datepicker is taken from https://github.com/uxsolutions/bootstrap-datepicker */
		echo $this->Html->css('/plugins/bootstrap-datepicker/css/bootstrap-datepicker.css?v=1.9.0');

		/* Bootstrap Datetimepicker is taken from https://github.com/smalot/bootstrap-datetimepicker */
		echo $this->Html->css('/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.css?v=2.4.4');

		/* Select2 is taken from https://github.com/select2/select2/ */
		echo $this->Html->css('/plugins/select2/css/select2.css?v=4.0.13');

		/* Jquery latest version is taken from http://jquery.com */
		echo $this->Html->script('/plugins/jquery-3.5.1.min.js');

		echo $this->fetch('meta');
		echo $this->fetch('css');
		echo $this->fetch('script');
	?>
</head>
<body>
	<div class="container-fluid">
		<div class="content">
			<?php echo $this->element('Usermgmt.message_notification');?>
			<?php echo $this->element('Usermgmt.header');?>

			<br/>

			<?php echo $this->fetch('content');?>
		</div>
	</div>

	<div style="background-color:#f5f5f5;padding:15px 0 5px;text-align:center;">
		<div class="container">
			<p class="muted">Copyright &copy; <?php echo date('Y');?> Your Site. All Rights Reserved. <a href="http://www.ektanjali.com/" target='_blank'>Developed By</a>.</p>
		</div>
	</div>

	<a href="#" class="back-to-top rounded text-center" id="back-to-top" style="display: none;">
		<i class="mdi mdi-chevron-up d-block"> </i>
	</a>

	<?php echo $this->element('Usermgmt.loader');?>

	<?php
		/* Bootstrap JS is taken from https://getbootstrap.com */
		echo $this->Html->script('/plugins/bootstrap/js/bootstrap.bundle.min.js?v=4.5.0');

		/* Bootstrap Datepicker is taken from https://github.com/uxsolutions/bootstrap-datepicker */
		echo $this->Html->script('/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js?v=1.9.0');

		/* Bootstrap Dateptimeicker is taken from https://github.com/smalot/bootstrap-datetimepicker */
		echo $this->Html->script('/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js?v=2.4.4');

		/* Bootstrap Typeahead is taken from https://github.com/biggora/bootstrap-ajax-typeahead */
		echo $this->Html->script('/plugins/bootstrap-ajax-typeahead/js/bootstrap-typeahead.js?v=0.0.6');

		/* Select2 is taken from https://github.com/select2/select2/ */
		echo $this->Html->script('/plugins/select2/js/select2.min.js?v=4.0.13');

		/* Usermgmt Plugin JS */
		echo $this->Html->script('/usermgmt/js/ajax.validation.js?q='.QRDN);
	?>
</body>
</html>