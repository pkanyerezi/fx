<script type="text/javascript" src="<?php echo $this->webroot;?>js/toastr.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo $this->webroot;?>css/toastr.css" />
<script type="text/javascript">
	(function() {
	 toastr.options = {
	  "closeButton": true,
	  "positionClass": "toast-top-center",
	  "timeOut": 0,
	  "extendedTimeOut": 0,
	  "hideEasing": "linear",
	  "showMethod": "fadeIn",
	  "hideMethod": "fadeOut",
	  "tapToDismiss": false
	}
	toastr.warning('<?php echo $message ?>', 'Warning');
	}).call(this);
</script>