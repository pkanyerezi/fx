<?php
//var_dump($openings);
?>
<?php echo $this->Html->script(array('script_dynamic_content'));?>
<div class="flash-message"><?php echo $this->Session->flash(); ?></div>
<div class="well well-new">
	<p class="pull-left">NB. Double click a column to exclude it from being printed</p>
	<span class="btn btn-small pull-right" onclick="do_print('printable','non_printable');"><i class="icon icon-print"></i> Print All</span>
</div>

<div class="well">
	<span class="btn btn-small pull-right printbtn1" onclick="do_print('printable1','non_printable1');"><i class="icon icon-print"></i> Print</span>
	<span class="btn btn-small pull-right" onclick="$('.printable1, .non_printable1, .printbtn1').remove();$(this).remove();"><i class="icon icon-remove-sign"></i> Remove</span>
	<span class="btn btn-inverse">UGX General Closing of <b><?php echo date('l jS F Y',strtotime($date_today)); ?></b></span><hr/>
	<div class="printable printable1" style="display:none;text-align:center;font-size:11px;">
		<center>
		<?php $_fox=($this->Session->read('fox'));?>
		<b><?php echo $_fox['Fox']['name'].'<br/>'; ?></b>
		<span style="font-size:10.5px;">General Closing of <?php echo date((' l jS F Y'),strtotime($date_today));?></span><br><br>
		</center>
	</div>
	<div style="overflow-x:auto;" class="printable printable1">
		<table class="well" style="font-size: 10px;width:100%;">
			<tr style="background: #2e335b;color:#ddd">
				<td><b>Cashier</b></td>
				<td><b>Opening</b></td>
				<td title="Closing Cash At Hand (UGX)"><b>Closing</b></td>
				<td><b>Expense</b></td>
				<td title="Gross Profit"><b>Gross</b></td>
				<td title="Net Profit"><b>Profit</b></td>
				<td title="Receivable Cash"><b>Receivable</b></td>
				<td title="Withdrawal Cash"><b>Withdrawal</b></td>
				<td title="Additional Profits"><b>Additional Profits</b></td>
				<td><b>Purchase</b></td>
				<td><b>Sale</b></td>
				<td title="Ugx At Bank"><b>Bank</b></td>
				<td title="Foreign Cash At Bank"><b>Bank(Foreign)</b></td>
				<td><b>Debtor</b></td>
				<td><b>Creditor</b></td>
				<td title="Close Foreign Value(UGX)"><b>Foreign</b></td>
			</tr>
			<?php 
				$fox = $this->Session->read('fox');
				$balance_with_safe = 0;
				if (isset($fox['Fox']['balance_with_safe'])) {
					$balance_with_safe = $fox['Fox']['balance_with_safe'];
				}

				$opening_ugx=0;
				$total_gross_profit=0;
				$total_expenses=0;
				$receivable_cash=0;
				$withdrawal_cash=0;
				$additional_profits=0;
				$total_purchases_ugx=0;			
				$total_sales_ugx=0;			
				$total_profit=0;			
				$total_cash_at_bank_foreign=0;			
				$total_cash_at_bank_ugx=0;			
				$total_debtors=0;			
				$total_creditors=0;		
				$total_close_ugx=0;	
				$total_cash_at_hand=0;	
				$counter = -1;
				foreach($openings as $opening):
					if ($balance_with_safe && $opening['User']['is_safe']) {
						$counter ++;
						$total_gross_profit			+=	$opening['Opening']['total_gross_profit'];
						$total_expenses				+=	$opening['Opening']['total_expenses'];
						$receivable_cash			+=	$opening['Opening']['receivable_cash'];
						$withdrawal_cash			+=	$opening['Opening']['withdrawal_cash'];
						$additional_profits			+=	$opening['Opening']['additional_profits'];
						$total_purchases_ugx		+=	$opening['Opening']['total_purchases_ugx'];
						$total_sales_ugx			+=	$opening['Opening']['total_sales_ugx'];
						$total_profit				+=	$opening['Opening']['total_profit'];				
						$total_cash_at_bank_foreign	+=	$opening['Opening']['cash_at_bank_foreign'];				
						$total_cash_at_bank_ugx		+=	$opening['Opening']['cash_at_bank_ugx'];				
						$total_debtors				+=	$opening['Opening']['debtors'];				
						$total_creditors			+=	$opening['Opening']['creditors'];	
						$total_close_ugx			+=	$opening['Opening']['close_ugx'];
					}	
			?>
			<tr>
				<td><?php echo $opening['User']['name'];?></td>
				<?php 
					@$opn = $opening['Opening']['opening_ugx'];
					
					if ($balance_with_safe && $opening['User']['is_safe']) {
						$opening_ugx = $opn;
					}elseif(!$balance_with_safe){
						$opening_ugx +=	$opn;
					}
					
					$cash_at_hand = 0;
					foreach($openings_tomorrow as $op_tmr){
						if($op_tmr['User']['id']==$opening['User']['id']){
							@$cash_at_hand = $op_tmr['Opening']['opening_ugx'];
							//$opening = $op_tmr;
							break;
						} 
					}
					
					if ($balance_with_safe && $opening['User']['is_safe']) {
						$total_cash_at_hand=$cash_at_hand;
					}elseif(!$balance_with_safe){
						$total_cash_at_hand+=$cash_at_hand;
					}
				?>
				<td class="ln"><?php echo $opn;?></td>
				<td class="ln"><?php echo $cash_at_hand;?></td>
				<td class="ln"><?php echo $opening['Opening']['total_expenses'];?></td>
				<td class="ln"><?php echo $opening['Opening']['total_gross_profit'];?></td>
				<td class="ln"><?php echo $opening['Opening']['total_profit']-$opening['Opening']['total_expenses'];?></td>
				<td class="ln"><?php echo $opening['Opening']['receivable_cash'];?></td>
				<td class="ln"><?php echo $opening['Opening']['withdrawal_cash'];?></td>
				<td class="ln"><?php echo $opening['Opening']['additional_profits'];?></td>
				<td class="ln"><?php echo $opening['Opening']['total_purchases_ugx'];?></td>
				<td class="ln"><?php echo $opening['Opening']['total_sales_ugx'];?></td>
				<td class="ln"><?php echo $opening['Opening']['cash_at_bank_ugx'];?></td>
				<td class="ln"><?php echo $opening['Opening']['cash_at_bank_foreign'];?></td>
				<td class="ln"><?php echo $opening['Opening']['debtors'];?></td>
				<td class="ln"><?php echo $opening['Opening']['creditors'];?></td>
				<td class="ln"><?php echo $opening['Opening']['close_ugx'];?></td>			
			</tr>
			<?php endforeach; ?>
		
			<tr style="background: #2e335b;color:#ddd">
				<?php if ($balance_with_safe):?>
					<td>SafeOnly</td>
				<?php else:?>
					<td>Total</td>
				<?php endif;?>
				<td><b><span class="ln"><?php echo $opening_ugx;?></span></b></td>
				<td><b><span class="ln"><?php echo $total_cash_at_hand;?></span></b></td>
				<td><b><span class="ln"><?php echo $total_expenses;?></span></b></td>
				<td><b><span class="ln"><?php echo $total_gross_profit;?></span></b></td>
				<td><b><span class="ln"><?php echo $total_profit-$total_expenses;?></span></b></td>
				<td><b><span class="ln"><?php echo $receivable_cash;?></span></b></td>
				<td><b><span class="ln"><?php echo $withdrawal_cash;?></span></b></td>
				<td><b><span class="ln"><?php echo $additional_profits;?></span></b></td>
				<td><b><span class="ln"><?php echo $total_purchases_ugx;?></span></b></td>
				<td><b><span class="ln"><?php echo $total_sales_ugx;?></span></b></td>
				<td><b><span class="ln"><?php echo $total_cash_at_bank_foreign;?></span></b></td>
				<td><b><span class="ln"><?php echo $total_cash_at_bank_ugx;?></span></b></td>
				<td><b><span class="ln"><?php echo $total_debtors;?></span></b></td>
				<td><b><span class="ln"><?php echo $total_creditors;?></span></b></td>
				<td><b><span class="ln"><?php echo $total_close_ugx;?></span></b></td>
			</tr>
		</table>
	</div>
	
	<br/><br/><br/>
	<p class="pull-left"><b>Transfers Made When the day Closed</b> - (S:Sent, R:Received, B:Balance(R-S))</p>
	<span class="btn btn-small pull-right printbtn2" onclick="do_print('printable2','non_printable2');"><i class="icon icon-print"></i> Print</span>
	<span class="btn btn-small pull-right" onclick="$('.printable2 ,.non_printable2, .printbtn2').remove();$(this).remove();"><i class="icon icon-remove-sign"></i> Remove</span>
	<div class="printable printable2" style="display:none;text-align:center;font-size:11px;">
		<center>
		<?php $_fox=($this->Session->read('fox'));?>
		<b><?php echo $_fox['Fox']['name'].'<br/>'; ?></b>
		<span style="font-size:10.5px;">Transfers Made When the day Closed on <?php echo date((' l jS F Y'),strtotime($date_today));?></span><br><br>
		</center>
	</div>
	<div style="text-align:left;width:97%;overflow-x:auto;" class="printable printable2">
		<table class="table table-responsive" style="font-size: 10px;width:100%;">
			<?php 
				$transfers_made_array = [];
				$counter = 0;
				foreach($openings_tomorrow as $opening):
					if(!isset($opening['Opening']['transfers_made']) || empty($opening['Opening']['transfers_made'])) continue;
					@$transfers_made = json_decode($opening['Opening']['transfers_made'],true);
					if(empty($transfers_made)) continue;
					
					@$transfers_made_others = json_decode($opening['Opening']['other_currencies'],true);
					if(!empty($transfers_made_others)){
						$transfers_made = array_merge($transfers_made,$transfers_made_others['data']);
					}
					$transfers_made = json_decode(json_encode($transfers_made));
					$counter ++;
			?>
				<?php if($counter==1):?>
					<tr style="background: #2e335b;color:#ddd">
						<td><b>Cashier</b></td>
						<?php foreach($transfers_made as $value):?>
							<td><b><?=$value->CNAME?></b></td>
						<?php endforeach; ?>
					</tr>
				<?php endif;?>
				<?php
					$counter1 = 0;
					foreach($transfers_made as $value):
						$counter1++;
						$c = $value->CID;
						$transfers_made_array[$c]['S'] = (isset($transfers_made_array[$c]['S']))?$transfers_made_array[$c]['S']+$value->SENT:$value->SENT;
						$transfers_made_array[$c]['R'] = (isset($transfers_made_array[$c]['R']))?$transfers_made_array[$c]['R']+$value->RECEIVED:$value->RECEIVED;
					
				?>
					<?php if($counter1==1):?>
						<tr>
						<td><?php echo $opening['User']['name'];?></td>
					<?php endif;?>
					<td>
						S:<span class="ln"><?php echo $value->SENT;?></span><br>
						R:<span class="ln"><?php echo $value->RECEIVED;?></span><br>
						B:<span class="ln"><?php echo ($value->RECEIVED - $value->SENT);?></span>
					</td>
				<?php endforeach; ?>
				
				<?php if($counter1==count($openings_tomorrow)):?>
					</tr>
				<?php endif;?>
			<?php endforeach; ?>
		
			<tr style="background: #2e335b;color:#ddd">
				<td>Total</td>
				<?php foreach($transfers_made_array as $key=>$value):?>
					<td>
						S:<b><span class="ln"><?php echo $value['S'];?></span></b><br>
						R:<b><span class="ln"><?php echo $value['R'];?></span></b><br>
						B:<b><span class="ln"><?php echo $value['R']-$value['S'];?></span></b><br>
					</td>
				<?php endforeach; ?>
			</tr>
		</table>
	</div>
	<br><br><br>
	

	<span class="btn btn-small pull-right printbtn3" onclick="do_print('printable3','non_printable3');"><i class="icon icon-print"></i> Print</span>
	<span class="btn btn-small pull-right" onclick="$('.printable3, .non_printable3, .printbtn3').remove();$(this).remove();"><i class="icon icon-remove-sign"></i> Remove</span>
	<div class="printable printable3" style="display:none;text-align:center;font-size:11px;">
		<center>
		<?php $_fox=($this->Session->read('fox'));?>
		<b><?php echo $_fox['Fox']['name'].'<br/>'; ?></b>
		<span style="font-size:10.5px;">Amount Per Currency When the day Opened on <?php echo date((' l jS F Y'),strtotime($date_today));?></span><br><br>
		</center>
	</div>
	<span class="btn btn-danger"><b>When the day Opened</b></span>
	<div style="text-align:left;width:97%;overflow-x:auto;" class="printable printable3">
		<table class="table table-responsive" style="font-size: 10px;width:100%;">
			<tr style="background: #2e335b;color:#ddd">
				<td><b>Cashier</b></td>
				<?php foreach($currencies as $currency):?>
				<td><b><?=$currency['Currency']['id'];?></b></td>
				<?php endforeach; ?>
			</tr>
			<?php
			$sums = [];
			foreach($openings as $opening):
				$currency_details = json_decode($opening['OpeningDetail']['currency_details'],true);
			?>
			<tr>
				<td><?php echo $opening['User']['name'];?></td>
				<?php foreach($currencies as $currency):?>
					<?php 
						$sums[$currency['Currency']['id']] = (isset($sums[$currency['Currency']['id']]))?$sums[$currency['Currency']['id']]:0;
						$sums[$currency['Currency']['id']] += $currency_details[$currency['Currency']['id']]['CAMOUNT'];
					?>
				<td class="ln"><?php echo $currency_details[$currency['Currency']['id']]['CAMOUNT'];?></td>
				<?php endforeach; ?>
			</tr>
			<?php endforeach; ?>
		
			<tr style="background: #2e335b;color:#ddd">
				<td>Total</td>
				<?php foreach($sums as $sum):?>
				<td><b><span class="ln"><?php echo $sum;?></span></b></td>
				<?php endforeach; ?>
			</tr>
		</table>
	</div>
	
	
	
	
	
	<br/><br/><br/>
	<br/><br/><br/>
	
	
	<span class="btn btn-small pull-right printbtn4" onclick="do_print('printable4','non_printable4');"><i class="icon icon-print"></i> Print</span>
	<span class="btn btn-small pull-right" onclick="$('.printable4, .non_printable4, .printbtn4').remove();$(this).remove();"><i class="icon icon-remove-sign"></i> Remove</span>
	<div class="printable printable4" style="display:none;text-align:center;font-size:11px;">
		<center>
		<?php $_fox=($this->Session->read('fox'));?>
		<b><?php echo $_fox['Fox']['name'].'<br/>'; ?></b>
		<span style="font-size:10.5px;">Amount Per Currency When the day Closed on <?php echo date((' l jS F Y'),strtotime($date_today));?></span><br><br>
		</center>
	</div>
	<span class="btn btn-success"><b>When the day Closed</b></span>
	<div style="text-align:left;width:97%;overflow-x:auto;" class="printable printable4">
		<table class="table table-responsive" style="font-size: 10px;width:100%;">
			<tr style="background: #2e335b;color:#ddd">
				<td><b>Cashier</b></td>
				<?php foreach($currencies as $currency):?>
				<td><b><?=$currency['Currency']['id'];?></b></td>
				<?php endforeach; ?>
			</tr>
			<?php
			$sums = [];
			foreach($openings_tomorrow as $opening):
				$currency_details = json_decode($opening['OpeningDetail']['currency_details'],true);
			?>
			<tr>
				<td><?php echo $opening['User']['name'];?></td>
				<?php foreach($currencies as $currency):?>
					<?php 
						$sums[$currency['Currency']['id']] = (isset($sums[$currency['Currency']['id']]))?$sums[$currency['Currency']['id']]:0;
						$sums[$currency['Currency']['id']] += $currency_details[$currency['Currency']['id']]['CAMOUNT'];
					?>
				<td class="ln"><?php echo $currency_details[$currency['Currency']['id']]['CAMOUNT'];?></td>
				<?php endforeach; ?>
			</tr>
			<?php endforeach; ?>
		
			<tr style="background: #2e335b;color:#ddd">
				<td>Total</td>
				<?php foreach($sums as $sum):?>
				<td><b><span class="ln"><?php echo $sum;?></span></b></td>
				<?php endforeach; ?>
			</tr>
		</table>
	</div>
</div>

<script>
	// classname = printable
	// exc_classname = non_printable
	function do_print(classname,exc_classname){
		$('.' + exc_classname).remove();
		var x=window.open("","");
		var xx='';
		$.each($('.' + classname),function(){
			xx+=$(this).html();
		});
		x.document.write(xx);
		x.window.print();
	}
</script>
<script>
	$("tr > td").dblclick(function ( event ) {
	    var ndx = $(this).prevAll("td").length;
	    $(this).closest("table").find("tr").find("td:eq(" + ndx + ")").remove();
	});
</script>
