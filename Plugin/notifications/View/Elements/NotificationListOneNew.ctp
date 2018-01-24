<?php if ($notifications): ?>
    <?php
    foreach ($notifications as $notification) {
        echo $this->Element('Notifications.NotificationItemOneNew', array('notification' => $notification));
    }
    ?>
   
   <script>	
	//remove/read notification
	$('.use-ajax-notifs-ReadOnly').click(function() {

		var readurl = $(this).attr('href');
		var readelemet = $(this);
		
		$.ajax({
			url: readurl,
			success: function() {
				$('#'+($(readelemet).attr('my-id'))).fadeOut('slow');
				pollNewNotification();
			}
		});
		return false;
	});
</script>
<?php endif; ?>