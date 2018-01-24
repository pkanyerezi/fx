<div id="user-nav" class="navbar navbar-inverse">
	<ul class="nav btn-group" style="width: auto; margin: 0px;">
		<li style="margin-top: -4px;">
			<?php echo $this->Element('Notifications.NotificationInit'); ?>
			<?php echo $this->Element('Notifications.NotificationIcon'); ?>
			<?php /*echo $this->Element('Notifications.NotificationIcon', array(
								'all_notifications' => array('controller' => 'dashboard', 'action' => 'notifications'),
								'clear_notifications' => true,
					)); */
			?>
		</li>
		<li class="btn btn-inverse"><a class="tip-bottom no-ajax" data-original-title="Send Reports to BOU" href="https://bsa.bou.or.ug/BSAV30/zbsa_fmlogin.aspx" target="_blank"> <span class="text">BOU</span></a></li>
		<?php if($logged_in):?>
		<?php if($super_admin): ?>
		<li class="btn btn-inverse"><a class="tip-bottom use-ajax" data-original-title="company details" href="<?php echo $this->params->webroot.'foxes/edit'; ?>"><i class="icon icon-edit"></i> <span class="text">Company details</span></a></li>
		<?php endif; ?>
		<li class="btn btn-inverse"><a class="tip-bottom use-ajax" data-original-title="my profile" href="<?php echo $this->params->webroot.'users/view/'.($users_Id) ?>"><i class="icon icon-user"></i> <span class="text">Profile and Safe</span></a></li>
		<li class="btn btn-inverse"><a class="tip-bottom use-ajax" data-original-title="my settings" href="<?php echo $this->params->webroot.'users/settings/'.($users_Id) ?>"><i class="icon icon-cog"></i> <span class="text">Settings</span></a></li>
		<?php endif; ?>
		<li class="btn btn-inverse"><a href="<?php echo $this->params->webroot.'users/'.( ($logged_in)? 'logout':'login'); ?>"><i class="icon icon-share-alt"></i> <span class="text">
			<?php echo ($logged_in)? "Logout":"Login" ?>
		</span></a></li>
	</ul>
</div>

<script>
$(document).ready(function(){
	$('.ifrates').click(function(){
		var x=window.open("http://foxange.deeloz.ug","","height=700,width=500");
	});
});
</script>