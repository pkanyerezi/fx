<?php if($logged_in):?>
<script>
	/*var req = function () {
		$.ajax({
			url : '<?php echo $this->webroot;?>users/pong',
			complete : function () {
				setTimeout(function () {
					req();
				}, 5000);
			},
			statusCode: {
			  403: function (response) {window.location.href='<?php echo $this->webroot;?>users/login';}
		   }
		});
	};
	req();*/
</script>


<body class="pushmenu-push" style="position: absolute; width: 100%; margin-top: 35px; height: auto !important;">
	<div id="container">
		<div id="header">
			<h1><a href="#">Admin</a></h1>		
		</div>

			<?php echo $this->element('dashboard/dashboard_top_most_nav'); ?>
			<?php echo $this->element('dashboard/dashboard_top_nav'); ?>
			<div class="pushmenu pushmenu-left" style="top:2px; !important;">
				<?php echo $this->element('dashboard/dashboard_search'); ?>	
				<?php echo $this->element('dashboard/dashboard_sidebar'); ?>
			</div >
			
		<div id="content" style="width:70%;border-radius:10px;">
			<div id="content-header">
				<h1>
					<?php
						$_fox=($this->Session->read('fox'));
						echo $_fox['Fox']['name'].'<br/>'; 
					?>
				</h1><br/>
				<span style="margin-left:30px;"><?php echo $name_of_user; ?></span>
				<?php if($logged_in):?>
					<?php echo $this->element('dashboard/dashboard_utilities'); ?>
				<?php endif; ?>
			</div>
			<div id="breadcrumb">
				<div>
					<table class="table">
						<tbody>					
							<tr >
								<td>
									<label>Today:</label>
									<div class="input-append date" id='dp_today' data-date="<?php echo date('Y-m-d'); ?>" data-date-format="yyyy-mm-dd">
										<input class="span2" size="16" type="text" id='dp_today_selected' value="<?php echo date('Y-m-d'); ?>">
										<span class="add-on"><i class="icon-th"></i></span>
									</div>
								</td>
								<td style="border-left:1px solid #ccc;">
									<label>From:</label>
									<div class="input-append date" id='dp_from' data-date="<?php echo date('Y-m-d'); ?>" data-date-format="yyyy-mm-dd">
										<input class="span2" size="16" type="text" id='dp_from_selected' value="<?php echo date('Y-m-d'); ?>">
										<span class="add-on"><i class="icon-th"></i></span>
									</div>
								</td>
								<td>
									<label>To:</label>
									<div class="input-append date" id="dp_to" data-date="<?php echo date('Y-m-d'); ?>" data-date-format="yyyy-mm-dd">
										<input class="span2" size="16" type="text" id="dp_to_selected" value="<?php echo date('Y-m-d'); ?>" >
										<span class="add-on"><i class="icon-th"></i></span>
										
											<div class="btn-group" style="margin-left:10%;">
												<button class="btn dropdown-toggle" data-toggle="dropdown">
												  <i class="icon icon-certificate"></i> Action
												  <span class="caret"></span>
												</button>
												<ul class="dropdown-menu">
													<li><?php echo $this->Html->link('Daily returns',array('controller'=>'daily_returns','action'=>'index'),array('class'=>'use-ajax','style'=>'margin-left:10px;')); ?></li>
													
													<?php if($super_admin || $authUser['can_view_sales_and_purchase_returns']):?>
													<li><?php echo $this->Html->link('General Sales returns',array('controller'=>'returns','action'=>'returns_weekly'),array('class'=>'use-ajax','style'=>'margin-left:10px;')); ?></li>
													<li><?php echo $this->Html->link('General Purchase returns',array('controller'=>'returns','action'=>'returns_weekly_purchases'),array('class'=>'use-ajax','style'=>'margin-left:10px;')); ?></li>
													<?php endif;?>
													
													<li class="divider"></li>
													<li><?php echo $this->Html->link('Sales Recceipts',array('controller'=>'sold_receipts','action'=>'index'),array('class'=>'use-ajax','style'=>'margin-left:10px;')); ?></li>
													<li><?php echo $this->Html->link('Purchase Recceipts',array('controller'=>'purchased_receipts','action'=>'index'),array('class'=>'use-ajax','style'=>'margin-left:10px;')); ?></li>
													
													<?php if($super_admin || $authUser['can_view_large_cash_receipts']):?>
														<li class="divider"></li>
														<li><?php echo $this->Html->link('Large Cash Sales',array('controller'=>'sold_receipts','action'=>'index','large_cash'),array('class'=>'use-ajax','style'=>'margin-left:10px;')); ?></li>
														<li><?php echo $this->Html->link('Large Cash Purchase',array('controller'=>'purchased_receipts','action'=>'index','large_cash'),array('class'=>'use-ajax','style'=>'margin-left:10px;')); ?></li>
													<?php endif;?>
												</ul>												
											</div>
									</div>
									
								</td>
							</tr>
						</tbody>
					</table>
				</div>	
				<script>
					$(document).ready(function(){
						$('#dp_today').datepicker({
							format: 'yyyy-mm-dd'
						});
						$('#dp_from').datepicker({
							format: 'yyyy-mm-dd'
						});
						$('#dp_to').datepicker({
							format: 'yyyy-mm-dd'
						});
					});
				</script>			
				
			</div>
			<div class="container-fluid">
				
				<div class="row-fluid dynamic-content" >
					<?php echo $this->Session->flash(); ?>
					
					
						<?php echo $this->Html->script(array('script_dynamic_content'));?>
						<div class="flash-message"><?php echo $this->Session->flash(); ?></div>
<?php endif; ?>					
					
					<?php echo $this->fetch('content'); ?>
<?php if($logged_in):?>
				</div>
				<hr>
				<div class="row-fluid">
					<div class="span6">
						Approved By Bank Of Uganda
					</div>
					<div class="span6" style="text-align:right;">
						&copy; 2013 - <?php echo date('Y');?> By <a href="https://facebook.com/namanyahillary1" target="_blank">BluePrint Softwares Ltd.</a>
					</div>
				</div>
				
			</div>
		</div>
		
		<div id="footer">
		</div>
	</div>
<?php endif; ?>	
	
	
	<?php echo $this->Html->script('required_script_bottom'); ?>
</body>

<div id="view-modal" style="width:50% !important;" class="modal hide fade">
	<div class="modal-header">
	  <button class="close" data-dismiss="modal">&times;</button>	  
	</div>          
	<div class="modal-body">	
	
	</div>            
</div>