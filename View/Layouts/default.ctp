<!DOCTYPE html>
<html lang="en" style="height: 332px; <?php if($this->params->url!='pages/home'){echo 'margin-top: 34px !important;';}?>" toolbar_fixed="1">
<head>
	<?php echo $this->Html->script('required_script'); ?>
	<?php echo $this->Html->charset(); ?>
	
	<title>
		<?php echo __('Admin'); ?>
		<?php echo $title_for_layout; ?>
	</title>
	
	<?php
		echo $this->Html->meta('icon');
		echo $this->Html->css(array('bootstrap.min','bootstrap-responsive.min'));
		echo $this->Html->script(array('jquery.min','scroll'));
		echo $this->Html->css(array('unicorn.main','cake.generic',
					'select2','unicorn.blue','unicorn.red','unicorn.','datepicker'));
		echo $this->Html->css('unicorn.grey', null, array('class' => 'skin-color'));
		
		echo $this->Html->script(array('excanvas.min','jquery.ui.custom','bootstrap.min',
									'jquery.peity.min','unicorn','bootstrap-datepicker','Notifications.notifications'));
		
		echo $this->fetch('meta');
		echo $this->fetch('css');
		echo $this->fetch('script');
	?>
</head>

<!--Body-->

<?php if($this->params->url=='pages/home' || $this->params->url=='')://If this is lauched as a home page ?>
	<?php echo $this->element('dashboard/dashboard'); ?>
	<?php //echo $this->element('home/home'); ?>
<?php else: ?>
		<?php echo $this->element('dashboard/dashboard'); ?>
<?php endif; ?>
</html>
