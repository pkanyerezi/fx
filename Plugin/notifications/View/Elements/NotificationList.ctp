<div class='my-notification-items'>
<?php if ($notifications): ?>
    <?php
    foreach ($notifications as $notification) {
        echo $this->Element('Notifications.NotificationItem', array('notification' => $notification));
    }
    ?>
<?php else: ?>
    <li class="notification-empty">
        <h4>No notifications</h4>
    </li>
<?php endif; ?>
</div>
<script>
    $('.deletelink, .use-ajax-notifs').click(function() {
        
        var deleteurl = $(this).attr('href');
        var theparent = $(this).parent();
        
        $.ajax({
            url: deleteurl,
            success: function(data) {
                theparent.slideUp();
                poll();
            }
        });
        return false;
    });
</script>

<script>
$(document).ready(function(){
	$(function(){
	  $('.my-notification-items').slimScroll({
		  height: '250px',
		  alwaysVisible: false,
		  start: 'top',
		  wheelStep: 10
	  });
  });
});
</script>