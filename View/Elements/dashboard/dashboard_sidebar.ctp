<div id="sidebar">
	<a href="#" class="visible-phone"><i class="icon icon-home"></i> Dashboard</a>
	<ul style="display: block;">
		<li class="submenu linc3">
			<a href="#" linc='linc3'><i class="icon icon-file"></i> <span>Purchase Receipts</span></span></a>
			<ul>
				<li><?php echo $this->Html->link('Add New Purchase receipts',array('controller'=>'purchased_receipts','action'=>'add'),array('class'=>'use-ajax sub_link','linc'=>'linc3','sub_linc'=>'linc3d')); ?></li>
				<li><?php echo $this->Html->link('List Purchase receipts',array('controller'=>'purchased_receipts','action'=>'index'),array('class'=>'use-ajax sub_link','linc'=>'linc3','sub_linc'=>'linc3a')); ?></li>

				<?php if($super_admin || $authUser['can_view_large_cash_receipts']):?>
				<li><?php echo $this->Html->link('Large Cash Purchase receipts',array('controller'=>'purchased_receipts','action'=>'index','large_cash'),array('class'=>'use-ajax sub_link','linc'=>'linc3','sub_linc'=>'linc3b')); ?></li>
				<?php endif;?>

				<?php if($super_admin || $authUser['can_view_currency_summary']):?>
					<li><?php echo $this->Html->link('Currency Summary',array('controller'=>'reports','action'=>'currency_summary'),array('class'=>'use-ajax sub_link','linc'=>'linc3','sub_linc'=>'linc3b')); ?></li>
				<?php endif;?>

				<?php if($super_admin || $authUser['can_download_receipts_excelfile']):?>
				<li><a id="extract_purchases_excel" href="<?php echo $this->params->webroot.'purchased_receipts/excel_purchases'; ?>" class="extract-excel no-ajax" >Extract Excel</a></li>
				<?php endif;?>

				<script>
					$(document).ready(function(){
						$("#extract_purchases_excel").bind("click", function(e) { 
							e.preventDefault();
							var href=$(this).attr('href');
							href+='?date_from='+($('#dp_from_selected').val());
							href+='&date_to='+($('#dp_to_selected').val());
							location.href = href; 
						});
					});
				</script>
				<?php if($super_admin): ?>
					<li><?php echo $this->Html->link('Upload/Send Purchase receipts',array('controller'=>'purchased_receipts','action'=>'upload'),array('class'=>'no-ajax sub_link','linc'=>'linc3','sub_linc'=>'linc3c')); ?></li>
				<?php endif; ?>				
			</ul>
		</li>
		<li class="submenu linc2">
			<a href="#" linc='linc2'><i class="icon icon-file"></i> <span>Sales Receipts</span></a>
			<ul>
				<li><?php echo $this->Html->link('Add New Sales receipts',array('controller'=>'sold_receipts','action'=>'add'),array('class'=>'use-ajax sub_link','linc'=>'linc2','sub_linc'=>'linc2d')); ?></li>
				<li><?php echo $this->Html->link('List Sales receipts',array('controller'=>'sold_receipts','action'=>'index'),array('class'=>'use-ajax sub_link','linc'=>'linc2','sub_linc'=>'linc2a')); ?></li>
				
				<?php if($super_admin || $authUser['can_view_large_cash_receipts']):?>
				<li><?php echo $this->Html->link('Large Cash Sales receipts',array('controller'=>'sold_receipts','action'=>'index','large_cash'),array('class'=>'use-ajax sub_link','linc'=>'linc2','sub_linc'=>'linc2b')); ?></li>
				<?php endif;?>

				<?php if($super_admin || $authUser['can_view_currency_summary']):?>
				<li><?php echo $this->Html->link('Currency Summary',array('controller'=>'reports','action'=>'currency_summary'),array('class'=>'use-ajax sub_link','linc'=>'linc2','sub_linc'=>'linc2b')); ?></li>
				<?php endif;?>

				<?php if($super_admin || $authUser['can_download_receipts_excelfile']):?>
				<li><a id="extract_sales_excel" href="<?php echo $this->params->webroot.'sold_receipts/excel_sales'; ?>" class="extract-excel no-ajax" >Extract Excel</a></li>
				<?php endif;?>
				<script>
					$(document).ready(function(){
						$("#extract_sales_excel").bind("click", function(e) { 
							e.preventDefault();
							var href=$(this).attr('href');
							href+='?date_from='+($('#dp_from_selected').val());
							href+='&date_to='+($('#dp_to_selected').val());
							location.href = href; 
						});
					});
				</script>
				<?php if($super_admin): ?>
					<li><?php echo $this->Html->link('Upload/Send Sales receipts',array('controller'=>'sold_receipts','action'=>'upload'),array('class'=>'no-ajax sub_link','linc'=>'linc2','sub_linc'=>'linc2c')); ?></li>
				<?php endif; ?>				
			</ul>
		</li>

		<li class="submenu linc4">
			<a href="#" linc='linc4'><i class="icon icon-book"></i> <span>Book Keeping </span></a>
			<ul>
				<?php if($super_admin): ?>
					<li><?php echo $this->Html->link('Openings',array('controller'=>'openings','action'=>'index'),array('class'=>'use-ajax sub_link','linc'=>'linc4','sub_linc'=>'linc1b')); ?></li>
					<li><?php echo $this->Html->link('Individual Closing Position',array('controller'=>'users','action'=>'index'),array('class'=>'use-ajax sub_link','linc'=>'linc4','sub_linc'=>'linc4b')); ?></li>
				<?php else:?>
					<?php if($authUser['can_view_closing_balance_summary']):?>
					<li><?php echo $this->Html->link('Individual Closing Position',array('controller'=>'balancings','action'=>'show_individually'),array('class'=>'use-ajax sub_link','linc'=>'linc4','sub_linc'=>'linc4b')); ?></li>
					<?php endif;?>
				<?php endif;?>
		
				<?php if($super_admin): ?>
				<li><?php echo $this->Html->link('Daily General Closing Position',array('controller'=>'balancings','action'=>'show_generally'),array('class'=>'use-ajax sub_link','linc'=>'linc4','sub_linc'=>'linc4b')); ?></li>
				<li><?php echo $this->Html->link('Final General Closing Position',array('controller'=>'balancings','action'=>'show_generally_final'),array('class'=>'use-ajax sub_link','linc'=>'linc4','sub_linc'=>'linc4b')); ?></li>
				<li><?php echo $this->Html->link('Cash Flow',array('controller'=>'balancings','action'=>'show_cash_flow'),array('class'=>'use-ajax sub_link','linc'=>'linc4','sub_linc'=>'linc9b')); ?></li>
				<?php endif;?>
				<li><?php echo $this->Html->link('Add Expense',array('controller'=>'expenses','action'=>'add'),array('class'=>'use-ajax sub_link','linc'=>'linc4','sub_linc'=>'linc8b')); ?></li>
				<li><?php echo $this->Html->link('List Expenses',array('controller'=>'expenses','action'=>'index'),array('class'=>'use-ajax sub_link','linc'=>'linc4','sub_linc'=>'linc8b')); ?></li>
				<li><?php echo $this->Html->link('Add Currency',array('controller'=>'currencies','action'=>'add'),array('class'=>'use-ajax sub_link','linc'=>'linc4','sub_linc'=>'linc8b')); ?></li>
				<li><?php echo $this->Html->link('List Currencies',array('controller'=>'currencies','action'=>'index'),array('class'=>'use-ajax sub_link','linc'=>'linc4','sub_linc'=>'linc8b')); ?></li>
				<li><?php echo $this->Html->link('Other Currencies',array('controller'=>'other_currencies','action'=>'index'),array('class'=>'use-ajax sub_link','linc'=>'linc4','sub_linc'=>'linc8b')); ?></li>
				<li><?php echo $this->Html->link('Currency board',array('controller'=>'currencies','action'=>'currency_board'),array('class'=>'use-ajax sub_link','linc'=>'linc4','sub_linc'=>'linc1b')); ?></li>
				<?php if($super_admin): ?>
					<li><?php echo $this->Html->link('TT Account balances',array('controller'=>'ttaccounts','action'=>'index'),array('class'=>'use-ajax sub_link','linc'=>'linc4','sub_linc'=>'linc1b')); ?></li>
					<li><?php echo $this->Html->link('Deleted Sales Receipts',array('controller'=>'deleted_sold_receipts','action'=>'index'),array('class'=>'use-ajax sub_link','linc'=>'linc4','sub_linc'=>'linc1b')); ?></li>
					<li><?php echo $this->Html->link('Deleted Purchase receipts',array('controller'=>'deleted_purchased_receipts','action'=>'index'),array('class'=>'use-ajax sub_link','linc'=>'linc4','sub_linc'=>'linc4b')); ?></li>
				<?php endif;?>
			</ul>
		</li>
		<?php if($super_admin): ?>
		<li class="submenu linc9">
			<a href="#" linc='linc9'><i class="icon icon-bold"></i> <span>BOU Reports</span></a>
			<ul>				
				<li><?php echo $this->Html->link('Chats/Graphs',array('controller'=>'dashboards','action'=>'chats'),array('class'=>'no-ajax sub_link','linc'=>'linc9','sub_linc'=>'linc9b')); ?></li>
				<li><?php echo $this->Html->link('General Sales returns',array('controller'=>'returns','action'=>'returns_weekly'),array('class'=>'use-ajax sub_link','linc'=>'linc9','sub_linc'=>'linc9b')); ?></li>
				<li><?php echo $this->Html->link('General Purchased returns',array('controller'=>'returns','action'=>'returns_weekly_purchases'),array('class'=>'use-ajax sub_link','linc'=>'linc9','sub_linc'=>'linc9b')); ?></li>
				<li><?php echo $this->Html->link('Add New Daily return',array('controller'=>'daily_returns','action'=>'add'),array('class'=>'use-ajax sub_link','linc'=>'linc9','sub_linc'=>'linc5a')); ?></li>
				<li><?php echo $this->Html->link('List Daily Returns',array('controller'=>'daily_returns','action'=>'index'),array('class'=>'use-ajax sub_link','linc'=>'linc9','sub_linc'=>'linc9b')); ?></li>
		
			</ul>
		</li>
		
		<li class="submenu linc90">
			<a href="#" linc='linc90'><i class="icon icon-play"></i> <span>Assets</span></a>
			<ul>				
				<li><?php echo $this->Html->link('Add Asset',array('controller'=>'assets','action'=>'add'),array('class'=>'use-ajax sub_link','linc'=>'linc9','sub_linc'=>'linc9b')); ?></li>
				<li><?php echo $this->Html->link('List Assets',array('controller'=>'assets','action'=>'list_items'),array('class'=>'use-ajax sub_link','linc'=>'linc9','sub_linc'=>'linc9b')); ?></li>
				<li><?php echo $this->Html->link('Add Asset Names',array('controller'=>'asset_names','action'=>'add'),array('class'=>'use-ajax sub_link','linc'=>'linc9','sub_linc'=>'linc5a')); ?></li>
				<li><?php echo $this->Html->link('List Asset Names',array('controller'=>'asset_names','action'=>'index'),array('class'=>'use-ajax sub_link','linc'=>'linc9','sub_linc'=>'linc9b')); ?></li>
		
			</ul>
		</li>
		<?php endif;?>

		<?php if($super_admin):?>
		<li class="submenu linc1tt">
			<a href="#" linc='linc1tt'><i class="icon icon-stop"></i> <span>Banking</span></a>
			<ul>				
				<li><?php echo $this->Html->link('Banks',array('controller'=>'banks','action'=>'index'),array('class'=>'use-ajax sub_link','linc'=>'linc1tt','sub_linc'=>'linc9b')); ?></li>
				<li><?php echo $this->Html->link('Ugx Deposited to bank',array('controller'=>'cash_at_bank_ugxes','action'=>'index','deposit'),array('class'=>'use-ajax sub_link','linc'=>'linc1tt','sub_linc'=>'linc9b')); ?></li>
				<li><?php echo $this->Html->link('Ugx Withdrawn from bank',array('controller'=>'cash_at_bank_ugxes','action'=>'index','withdraw'),array('class'=>'use-ajax sub_link','linc'=>'linc1tt','sub_linc'=>'linc9b')); ?></li>
				<li><?php echo $this->Html->link('Foreign cash Deposited to bank',array('controller'=>'cash_at_bank_foreigns','action'=>'index','deposit'),array('class'=>'use-ajax sub_link','linc'=>'linc1tt','sub_linc'=>'linc9b')); ?></li>
				<li><?php echo $this->Html->link('Foreign cash Withdrawn from bank',array('controller'=>'cash_at_bank_foreigns','action'=>'index','withdraw'),array('class'=>'use-ajax sub_link','linc'=>'linc1tt','sub_linc'=>'linc9b')); ?></li>
			</ul>
		</li>
		<?php endif; ?>
		
		<li class="submenu linc10">
			<a href="#" linc='linc10'><i class="icon icon-th"></i> <span>Cash Position</span></a>
			<ul>
				<li><?php echo $this->Html->link('Creditors',array('controller'=>'creditors','action'=>'index'),array('class'=>'use-ajax sub_link','linc'=>'linc10','sub_linc'=>'linc5a')); ?></li>
				<li><?php echo $this->Html->link('Debtors ',array('controller'=>'debtors','action'=>'index'),array('class'=>'use-ajax sub_link','linc'=>'linc10','sub_linc'=>'linc9b')); ?></li>
				<li><?php echo $this->Html->link('Deposits',array('controller'=>'receivables','action'=>'index'),array('class'=>'use-ajax sub_link','linc'=>'linc10','sub_linc'=>'linc9b')); ?></li>
				<li><?php echo $this->Html->link('Withdrawals ',array('controller'=>'withdrawals','action'=>'index'),array('class'=>'use-ajax sub_link','linc'=>'linc10','sub_linc'=>'linc9b')); ?></li>
				<li><?php echo $this->Html->link('Additional Profits ',array('controller'=>'additional_profits','action'=>'index'),array('class'=>'use-ajax sub_link','linc'=>'linc10','sub_linc'=>'linc9b')); ?></li>
		
			</ul>
		</li>


		<li class="submenu linc6">
			<a href="#" linc='linc6'><i class="icon icon-pencil"></i> <span>Customers/Bank/Director</span></a>
			<ul>				
				<li><?php echo $this->Html->link('List',array('controller'=>'users','action'=>'index','customers'),array('class'=>'use-ajax sub_link','linc'=>'linc6','sub_linc'=>'linc7b')); ?></li>
				<li><?php echo $this->Html->link('Add',array('controller'=>'users','action'=>'add_customers'),array('class'=>'use-ajax sub_link','linc'=>'linc1','sub_linc'=>'linc1b')); ?></li>
			</ul>
		</li>
		
		<?php if($super_admin): ?>
		<li class="submenu active linc1">
			<a href="#" linc='linc1'><i class="icon icon-user"></i> <span>Cashiers/Admins</span></a>
			<ul>
				<li><?php echo $this->Html->link('List',array('controller'=>'users','action'=>'index'),array('class'=>'use-ajax sub_link','linc'=>'linc1','sub_linc'=>'linc1b')); ?></li>
				<li><?php echo $this->Html->link('Add',array('controller'=>'users','action'=>'add'),array('class'=>'use-ajax sub_link','linc'=>'linc1','sub_linc'=>'linc1b')); ?></li>
			</ul>
		</li>
		<?php endif; ?>

		<?php if($super_admin): ?>
		<li class="submenu linc1r">
			<a href="#" linc='linc1r'><i class="icon icon-bell"></i> <span>Report Notification Emails</span></a>
			<ul>
				<li>
				<?php 
					$ip = $_SERVER['REMOTE_ADDR'];
					$ip = str_replace('::1','localhost',$ip);
					$ip = str_replace('127.0.0.1','localhost',$ip);
					$url = ('http://'.$ip.'/fx/m/notifier.php');
				?>

				<a class="no-ajax sub_link" linc="linc1r" sub_linc="linc1r" href="<?php echo $url;?>" target="blank"><i class="icon icon-play"></i> Start E-Mailing Server</a></li>
				<li><?php echo $this->Html->link('List',array('controller'=>'report_notification_emails','action'=>'index'),array('class'=>'use-ajax sub_link','linc'=>'linc1r','sub_linc'=>'linc1r')); ?></li>
				<li><?php echo $this->Html->link('Add',array('controller'=>'report_notification_emails','action'=>'add'),array('class'=>'use-ajax sub_link','linc'=>'linc1r','sub_linc'=>'linc1r')); ?></li>
				<li><?php echo $this->Html->link('Report Types',array('controller'=>'report_types','action'=>'index'),array('class'=>'use-ajax sub_link','linc'=>'linc1r','sub_linc'=>'linc1r')); ?></li>
			</ul>
		</li>
		<?php endif; ?>
		
	</ul>
</div>

<script>
	var current_linc='linc_a';//set the first active link
	$(document).ready(function(){
		$('#sidebar a').click(function(){
			
			$('#sidebar ul li').removeClass('active');
			
			var linc=$(this).attr('linc');
			$('.'+linc).addClass('active');
			$('.display-none').fadeIn('slow');
			
			var _url=$(this).attr('data-taget');
			
			if($(this).hasClass('sub_link')){
				linc=$(this).attr('sub_linc');
			}
			
			if(_url!='' && _url!='#' && current_linc!=linc ){
				current_linc=linc;
				show_loading();
				$.get(_url, function(data) {
					after_fetching_data(data);
					remove_loading();
				});
			}
			
		});
		
	});
</script>