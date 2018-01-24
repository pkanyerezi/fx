<?php echo $this->Html->css(array('style_login'));?>
<div class="container-fluid">
<div class="row-fluid">
			
	<div class="row-fluid">
		<div class="login-box">
			<div class="icons">
				<?php echo $this->Html->image('pic/default.png', array('width'=>'50px'));?>
			</div>
			<h2>Login to your account</h2>
			<div class="flash-message">
				<?php echo $this->Session->flash('auth');?>
				<?php echo $this->Session->flash(); ?>
			</div>
			<?php echo $this->Form->create('User', array('action'=>'login')); ?>				
				<fieldset>
					<label style="margin-left:3%">Username:</label>
					<div class="input-prepend" title="Username">
						<span class="add-on"><i class="icon-user"></i></span>							
						<input class="input-large span10" name="data[User][username]" id="username" type="text" placeholder="type username" required=''>
					</div>
					<div class="clearfix"></div>
					
					<label style="margin-left:3%">Password:</label>
					<div class="input-prepend" title="Password">							
						<span class="add-on"><i class="icon-lock"></i></span>
						<input class="input-large span10" name="data[User][password]" id="password" type="password" placeholder="type password" required=''>
					</div>
					<div class="date-notification" style="width:90%;">
						<span class="btn btn-danger" style="width:100%;"><b>Is today</b> <?php echo date('l jS F Y');?><br/><br/> 
							<span class="btn-group">	
								<span class="btn btn-info is-correct-date"><i class="icon-white icon-ok"></i> yes</span>
								<span class="btn btn-info is-not-correct-date"><i class="icon-white icon-remove"></i> no</span>
							</span>
						</span>
					</div>
					<div class="date-notification-solution" style="width:90%;">
						<span class="btn btn-inverse" style="width:100%;"><b>Solution</b>: Change the computer clock to the correct time. <br/><br/>
							<span class="btn btn-info">
								<a style="color:#fff" href="<?php echo $this->webroot; ?>users/login"><i class="icon-white icon-ok"></i> done</a>
							</span>
						</span>
					</div>
					<div class="button-login" style="width:90%;">
						<button type="submit" style="float:right;" class="btn btn-primary"><i class="icon-off icon-white"></i> Login</button>												
					</div>
					<center>
						<div style="width:90%;">	
							<h3 style="font-size:10px;">All rights reserved &copy; 2013 Blueprint.inc</h3>											
						</div>
					</center>
					<div class="clearfix"></div>
			<hr>
		</fieldset></form></div><!--/span-->
	</div><!--/row-->
	
		</div><!--/fluid-row-->
		
</div><!--/.fluid-container-->


<script>
$('.button-login,.date-notification-solution').hide();
$('.is-correct-date').click(function(){
	$('.button-login').fadeIn('slow',function(){
		$('.date-notification,.date-notification-solution').fadeOut('slow');
	});
});
$('.is-not-correct-date').click(function(){
	$('.button-login,.date-notification').fadeOut('slow',function(){
		$('.date-notification-solution').fadeIn('slow');
	});
});
</script>