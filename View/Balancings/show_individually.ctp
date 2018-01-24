<?php echo $this->Html->script(array('script_dynamic_content'));?>
<div class="flash-message"><?php echo $this->Session->flash(); ?></div>
<span class="btn btn-small pull-right" onclick="do_print();"><i class="icon icon-print"></i> Print Summary</span>
<span class="btn btn-small pull-right"><?php echo $this->Html->link('Refresh',array('controller'=>'balancings','action'=>'show_individually',0,0,0,$user_id),array('class'=>'use-ajax')); ?></span>
<?php
	if(isset($openings[0]['Opening']['opening_ugx'])){
		if(count($openings[0]['Opening']['opening_ugx'])){
			
		}else{
			echo 'No opening found for today';
			exit;
		}
	}else{
		echo 'No opening found for today';
		exit;
	}
	
?>

<div class="printable" style="display:none;text-align:center;font-size:11px;">
	<center>
	<?php $_fox=($this->Session->read('fox'));?>
	<b><?php echo $_fox['Fox']['name'].'<br/>'; ?></b><br>
	DAILY POSITION<br>
	DATE &nbsp;&nbsp;&nbsp;&nbsp;<?php echo date((' l jS F Y'),strtotime($date_today));?>
	</center>
</div>

<div class="well well-new">
	<div class="well">
	<?php 
		if($openings[0]['Opening']['status']){
			echo '<div class="btn btn-danger"><b>This is a saved balance position of </b>'.date((' l jS F Y'),strtotime($openings[0]['Opening']['date'])).'</b></div>';
		}else{
			echo '<div class="btn btn-success"><b>This balance position is awaiting saving. This balancing is of '.date((' l jS F Y'),strtotime($date_today)).'</b></div>';
		}
		
	?>
	</div>
	<p>
		<b>Opening UGX</b>: <span class="ln"><?php echo $openings[0]['Opening']['opening_ugx']; ?></span>
		<?php if(!$openings[0]['Opening']['status'] and $super_admin): ?>
		<a style="color:#fff" href="<?php echo $this->webroot.'openings/edit/'.($openings[0]['Opening']['id']).'/'.($openings[0]['Opening']['user_id']);?>"><span style="float:right;" class="btn btn-primary"><i class="icon-white icon-edit"></i> Edit</span></a>
		<?php endif; ?>
	</p>
	<table class="well">
		<?php
			$total_ugx=0;
			$fox = $this->Session->read('fox');
			$balance_with_safe = 0;
			if (isset($fox['Fox']['balance_with_safe'])) {
				$balance_with_safe = $fox['Fox']['balance_with_safe'];
			}

			$is_safe = 0;
			if (isset($userDetails['User']['is_safe'])) {
				$is_safe = $userDetails['User']['is_safe'];
			}
			
			$balance_with_purchases = 0;
			if (isset($userDetails['User']['balance_with_all_purchases_from_other_cashiers'])) {
				$balance_with_purchases = $userDetails['User']['balance_with_all_purchases_from_other_cashiers'];
			}

			$show_average_rates = 0;
			if (!$balance_with_safe || $is_safe) {
				$show_average_rates = 1;
			}
		?>
		<tr style="background: #2e335b;color:#ddd">
			<td><b>Currency</b></td>
			<td><b>Amount</b></td>
			<?php if($show_average_rates):?>
			<td><b>Rate</b></td>
			<td><b>UGX</b></td>
			<?php endif;?>
		</tr>
		<?php 
			
			$currency_details = json_decode($openings[0]['OpeningDetail']['currency_details'],true);
			foreach($currencies as $currency):
		?>
			<tr>
				<td>
					<b><?php echo $currency['Currency']['id']; ?></b>
				</td>
				<td>
					<span class="ln">
						<?php 
							@$amount = $currency_details[$currency['Currency']['id']]['CAMOUNT'];
							echo $amount; 
						?>
					</span>
				</td>
				<?php if($show_average_rates):?>
				<td>
					<span class="ln">
						<?php 
							@$rate = $currency_details[$currency['Currency']['id']]['CRATE'];
							echo $rate; 
						?>
					</span>
				</td>
				<?php $ugx=($amount)*($rate); ?>
				<td>
					<span class="ln"><?php echo $ugx; ?></span>
				</td>
				<?php $total_ugx+=$ugx; ?>
				<?php endif;?>
			</tr>
		<?php endforeach;?>
			<?php if($show_average_rates):?>
			<tr>
				<td></td><td></td><td></td>
				
				<td>
					<b>= <span class="ln"><?php echo $total_ugx; ?></span></b>
				</td>
			</tr>
			<?php endif;?>
	</table>
	<?php if($show_average_rates):?>
	<div class="printable">
	<p>
		<br/>
		<?php $total_opening_stock=$total_ugx+$openings[0]['Opening']['opening_ugx'];?>
		<b>Total Opening Stock: </b><span class="ln"><?php echo $total_ugx; ?></span> + <span class="ln"><?php echo $openings[0]['Opening']['opening_ugx']; ?></span> = <span class="ln"><?php echo $total_opening_stock ?></span>
	</p>
	</div>
	<?php endif;?>
	<table class="well" style="width:100%;font-size:11px;">
			<tr style="background: #2e335b;color:#ddd">
				<td>
					<b>Currency</b>
				</td>
				<td>
					<b>Purchases</b>
				</td>
				<td>
					<b>Purchases AVG rate</b>
				</td>
				<td>
					<b>Purchases UGX</b>
				</td>
				<td>
					<b>Sales</b>
				</td>
				<td>
					<b>Sales AVG rate</b>
				</td>
				<td>
					<b>Sales UGX</b>
				</td>
				
			</tr>
		<?php 
			$total_purchases_ugx=0;
			$total_purchases=0;
			$total_sales_ugx=0;
			$total_sales=0;
			$count=-1;
			foreach($currencies as $currency):
				$count++;
				/*if ($currency['Currency']['is_other_currency']) {
					$currency['Currency']['description'] = $currency['Currency']['id'];
				}*/
		?>
			
			<tr>
				<td>
					<b><?php echo $currency['Currency']['id']; ?></b>
				</td>
				<td style="background:lightgreen">
					<span class="ln"><?php echo $purchases[$count]['total_amount']; ?></span>
				</td>
				<td style="background:lightgreen">
					<span class="ln"><?php echo $purchases[$count]['av_rate']; ?></span>
				</td>
				<td style="background:lightgreen">
					<span class="ln"><?php echo $purchases[$count]['total_amount']*$purchases[$count]['av_rate']; ?></span>
				</td>
				<td style="background:skyblue">
					<span class="ln"><?php echo $sales[$count]['total_amount']; ?></span>
				</td>
				<td style="background:skyblue">
					<span class="ln"><?php echo $sales[$count]['av_rate']; ?></span>
				</td>		
				<td style="background:skyblue">
					<span class="ln"><?php echo $sales[$count]['total_amount']*$sales[$count]['av_rate']; ?></span>
				</td>
			</tr>
			
			<?php $total_purchases+=$purchases[$count]['total_amount'];?>
			<?php $total_sales+=$sales[$count]['total_amount'];?>
			
			<?php $total_purchases_ugx+=$purchases[$count]['total_amount']*$purchases[$count]['av_rate'];?>
			<?php $total_sales_ugx+=$sales[$count]['total_amount']*$sales[$count]['av_rate'];?>
			
		<?php endforeach;?>			
			<tr>
				<td></td>
				<td><!--<b>=<span class="ln"><?php echo $total_purchases; ?></span></b>--></td>
				<td></td>
				<td><b>=<span class="ln"><?php echo $total_purchases_ugx; ?></span></b></td>
				<td><!--<b>=<span class="ln"><?php echo $total_sales; ?></span></b>--></td>
				<td></td>
				<td><b>=<span class="ln"><?php echo $total_sales_ugx; ?></span></b></td>
			</tr>
	</table>	
	<br/><br/>
	
	<div>
	<h4>Today&apos;s Closing</h4>
	<table class="well">
			<tr style="background: #2e335b;color:#ddd">
				<td style="width: 50px;"><b>Currency</b></td>
				<td><b>Received(R)</b></td>
				<td><b>Sent(S)</b></td>
				<td><b>(R-S)</b></td>
				<td title="Closing Foreign"><b>Amount/Balance</b></td>

				<?php if($show_average_rates):?>
				<td title="Average closing rate"><b>Rate</b></td>
				<td title="Closing UGX"><b>UGX</b></td>
					<?php if($super_admin):?>
						<td><b>Gross Profit</b></td>				
					<?php endif;?>
				<?php endif;?>
				<!--<td><b>Profit</b></td>-->
			</tr>
		<?php 
			$total_purchases_ugx=0;
			$total_purchases=0;
			$total_sales_ugx=0;
			$total_sales=0;
			$total_profits=0;
			$total_gross_profits=0;
			$total_todays_close=0;
			$total_todays_close_ugx=0;
			
			$expenses=0;
			if(isset($total_expenses[0][0]['total_expenses'])){
				$expenses=(double)$total_expenses[0][0]['total_expenses'];
			}
			
			$count=-1;

			foreach($currencies as $currency):
				$count++;
		?>
							
			<tr>
				<td>
					<b><?php echo $currency['Currency']['id']; ?></b>
				</td>
				<?php $received=0;?>
				<?php if(isset($safe_transactions_received[$count]['total_amount'])):?>
					<?php						
						$received = $safe_transactions_received[$count]['total_amount'];
					?>
				<?php endif;?>
				<td><span class="ln"><?php echo $received;?></span></td>

				<?php $sent=0;?>
				<?php if(isset($safe_transactions_sent[$count]['total_amount'])):?>
					<?php						
						$sent = $safe_transactions_sent[$count]['total_amount'];
					?>
				<?php endif;?>
				<td><span class="ln"><?php echo $sent;?></span></td>
				<?php $balance = ($received-$sent);?>
				<td><span class="ln" style="<?php echo (($balance<0)?'color:red;font-weight:bold;':'');?>"><?php echo $balance;?></span></td>
				<td>
					<span class="">
					<?php
						@$todays_close=(($currency_details[$currency['Currency']['id']]['CAMOUNT'])+($purchases[$count]['total_amount']))-($sales[$count]['total_amount']);
						if ($balance_with_safe && $is_safe) {
							@$todays_close=(($currency_details[$currency['Currency']['id']]['CAMOUNT']));
						}else{
							@$todays_close=(($currency_details[$currency['Currency']['id']]['CAMOUNT'])+($purchases[$count]['total_amount']))-($sales[$count]['total_amount']);
						}
						
						if($balance_with_purchases && !$is_safe){
							@$todays_close=(($currency_details[$currency['Currency']['id']]['CAMOUNT']))-($sales[$count]['total_amount']);
						}
						$todays_close +=$balance;
						
						echo $todays_close;
						$total_todays_close+=$todays_close;
					?>
					</span>
				</td>

				
					<?php 
						$opening = $openings[0];
						@$_amount_ugx = $currency_details[$currency['Currency']['id']]['CAMOUNT'] * $currency_details[$currency['Currency']['id']]['CRATE'];

						$_purchase_ugx = $purchases[$count]['av_rate'] * $purchases[$count]['total_amount'];
						@$av_close_rate = ($_amount_ugx + $_purchase_ugx)/($purchases[$count]['total_amount'] + $currency_details[$currency['Currency']['id']]['CAMOUNT']);

						if(empty($_purchase_ugx)){
							@$_amount_ugx = $currency_details[$currency['Currency']['id']]['CRATE'] * $currency_details[$currency['Currency']['id']]['CAMOUNT'];
							$_purchase_ugx = $purchases_pre[$count]['av_rate'] * $purchases_pre[$count]['total_amount'];
							@$av_close_rate = ($_amount_ugx + $_purchase_ugx)/($purchases_pre[$count]['total_amount'] + $currency_details[$currency['Currency']['id']]['CAMOUNT']);
						}
						
						if(empty($av_close_rate)) $av_close_rate = $purchases_pre[$count]['av_rate'];

						if(empty($av_close_rate)) @$av_close_rate = $currency_details[$currency['Currency']['id']]['CRATE'];

						$todays_close_ugx=$av_close_rate*$todays_close;
						$total_todays_close_ugx+=$todays_close_ugx;

						$purchase_rate = ($purchases[$count]['av_rate']==0)? $av_close_rate : $purchases[$count]['av_rate'];

						$GP = ($sales[$count]['av_rate']-$av_close_rate) *$sales[$count]['total_amount'];
						$total_gross_profits+=$GP;
					?>
				<?php if($show_average_rates):?>
					<td><span class="ln"><?=$av_close_rate;?></span></td>
					<td>
						<span class="ln"><?=$todays_close_ugx;?></span>
					</td>
					<?php if($super_admin):?>
						<td><span class="ln"><?php echo $GP;?></span></td>
					<?php endif;?>
				<?php endif;?>
			</tr>
			<?php $total_purchases+=$purchases[$count]['total_amount'];?>
			<?php $total_sales+=$sales[$count]['total_amount'];?>
			
			<?php $total_purchases_ugx+=$purchases[$count]['total_amount']*$purchases[$count]['av_rate'];?>
			<?php $total_sales_ugx+=$sales[$count]['total_amount']*$sales[$count]['av_rate'];?>
			
		<?php endforeach;?>	
			<?php
				$total_profits+=$additional_profits;
			?>
			<?php if($show_average_rates):?>
			<tr>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td><b>=<span class="ln"><?php echo $total_todays_close_ugx; ?></span></b></td>
				<?php if($super_admin):?>
					<td><b>=<span class="ln"><?php echo $total_gross_profits; ?></span></b></td>
				<?php endif;?>
			</tr>
			<?php endif;?>
	</table>	
	</div>

	<p>
		<?php if($super_admin):?>
		<b>Total Profit: </b><span class="ln"><?php echo $total_gross_profits; ?></span> UGX, 
		<b>Total expenses: </b><span class="ln"><?php echo $expenses;?></span> UGX, 
		<b>Total Net Profit: </b><span class="ln"><?php echo $total_gross_profits-$expenses;?></span> UGX </b>
		<?php endif?>
		<b>Total UGX Sent To Cashiers: </b><span class="ln"><?php echo $safe_transactions_sent_ugx;?></span> UGX </b>
		<b>Total UGX Received From Cashiers: </b><span class="ln"><?php echo $safe_transactions_received_ugx;?></span> UGX </b>
	</p>
	
	<p>
		<?php if ($balance_with_safe && $is_safe):?>
			<?php $cash_at_hand=((($expenses)+$openings[0]['Opening']['opening_ugx']+$receivable_cash+$additional_profits)-($withdrawal_cash)) + $safe_transactions_received_ugx - $safe_transactions_sent_ugx;?>
		<?php elseif ($balance_with_purchases && !$is_safe):?>
			<?php $cash_at_hand=(($total_sales_ugx-($expenses)+$openings[0]['Opening']['opening_ugx']+$receivable_cash+$additional_profits)-($withdrawal_cash)) + $safe_transactions_received_ugx - $safe_transactions_sent_ugx;?>
		<?php else:?>
			<?php $cash_at_hand=(($total_sales_ugx-($expenses)+$openings[0]['Opening']['opening_ugx']+$receivable_cash+$additional_profits)-($total_purchases_ugx+$withdrawal_cash)) + $safe_transactions_received_ugx - $safe_transactions_sent_ugx;?>
		<?php endif;?>
		<div style="font-size:150%;<?php echo (($cash_at_hand<0)?'color:red;font-weight:bold;':'');?>"><b>Cash at hand: </b><span class="ln"><?php echo $cash_at_hand; ?></span> UGX</div>
		<div style="font-size:150%"><b>Total Closing Stock: </b><span class="ln"><?php 
echo $cash_at_hand+$total_todays_close_ugx; ?></span> UGX</div>
	</p>
	<hr/>
	<h6 style="background:#2e335b;color:#ddd;text-align:center">Other Summary</h6>
	<div class="well btn btn-small" style="width: 97.8%;text-align: left;">
		<div style="vertical-align:middle;margin-left: 4%;">
			<div class="row">
				<div class="span4">					
					<p><?php echo 'Withdrawal Cash:<span class="ln">'.$withdrawal_cash.'</span>';?></p>
					<p><?php echo 'Receivable Cash:<span class="ln">'.$receivable_cash.'</span>';?></p>
					<p><?php echo 'Expenses:<span class="ln">'.$expenses.'</span>';?></p>	
				</div>
				<div class="span4">	
					<p><?php echo 'Total Sales UGX:<span class="ln">'.$total_sales_ugx.'</span>';?></p>
					<p><?php echo 'Total Purchases UGX:<span class="ln">'.$total_purchases_ugx.'</span>';?></p>	
					<p><?php echo 'Additional Profits:<span class="ln">'.$additional_profits.'</span>';?></p>				
				</div>
				<div class="span4">
					<?php if($super_admin):?>
						<p><?php echo 'Total Profits:<span class="ln">'.$total_profits.'</span>';?></p>
					<?php endif;?>
					<p><?php echo 'Opening UGX:<span class="ln">'.$openings[0]['Opening']['opening_ugx'].'</span>';?></p>					
				</div>
			</div><hr/>
			<div class="row">
				<div class="span4">					
					<p><?php echo 'Cash at bank Foreign:<span class="ln">'.$cash_at_bank_foreign.'</span>';?></p>
					<p><?php echo 'Cash at bank Ugx:<span class="ln">'.$cash_at_bank_ugx.'</span>';?></p>
					
				</div>
				<div class="span4">	
					<p><?php echo 'Debtors:<span class="ln">'.$debtors.'</span>';?></p>
					<p><?php echo 'Creditors:<span class="ln">'.$creditors.'</span>';?></p>					
				</div>
				<div class="span4">	
					<p>UGX Sent To Cashiers: <span class="ln"><?php echo $safe_transactions_sent_ugx;?></span> UGX</p>
					<p>UGX Received From Cashiers: <span class="ln"><?php echo $safe_transactions_received_ugx;?></span> UGX</p>
				</div>
			</div>
			<p>
				
			</p>
			<p><div style="font-size:150%"><b>Final Cash at hand: </b><span class="ln"><?php echo ($cash_at_hand+$total_todays_close_ugx+$creditors)-($cash_at_bank_foreign+$cash_at_bank_ugx+$debtors); ?></span> UGX</div></p>
			
		</div>
	</div>
	
	
	
	<?php if(1):?>
	
	<!--Printable details-->
	<div class="printable" style="display:none;">
		<table class="well" style="width:100%;font-size:11px;">
				<tr style="border-bottom: 1px solid #000;">
					<td style="border-bottom: 1px solid #000;"><b>Currency</b></td>
					<td style="border-bottom: 1px solid #000;"><b>Opening Balance</b></td>
					<td style="border-bottom: 1px solid #000;"><b>Purchases</b></td>
					<td style="border-bottom: 1px solid #000;"><b>Sales</b></td>
					<?php if($show_average_rates):?>
					<td style="border-bottom: 1px solid #000;"><b>Closing Rate</b></td>
					<?php endif;?>
					<td style="border-bottom: 1px solid #000;"><b>Closing Balance</b></td>
				</tr>
			<?php 
				$count=-1;
				foreach($currencies as $currency):
					$count++;
					/*if ($currency['Currency']['is_other_currency']) {
						$currency['Currency']['id'] = $currency['Currency']['id'];
					}*/
			?>
			
							
				<tr>
					<td>
						<b><?php echo $currency['Currency']['id']; ?></b>
					</td>
					<td><span class="ln"><?php echo $currency_details[$currency['Currency']['id']]['CAMOUNT']; ?></span></td>
					<td><span class="ln"><?php echo $purchases[$count]['total_amount'];?></span></td>
					<td><span class="ln"><?php echo $sales[$count]['total_amount'];?></span></td>
					<?php if($show_average_rates):?>
					<td><span class="ln">
						<?php
							$opening = $openings[0];
							/*if(isset($opening_safe[0])){
								$opening = $opening_safe[0];
								$currency_details = json_decode($opening['OpeningDetail']['currency_details'],true);
							}*/

							//$_amount_ugx = $currency_details[$currency['Currency']['id']]['CRATE'] * $currency_details[$currency['Currency']['id']]['CAMOUNT'];
							//$_purchase_ugx = $purchases[$count]['av_rate'] * $purchases[$count]['total_amount'];
							//@$av_close_rate = ($_amount_ugx + $_purchase_ugx)/($purchases[$count]['total_amount'] + $opening['Opening'][$currency['Currency']['id'].'a']);

						@$_amount_ugx = $currency_details[$currency['Currency']['id']]['CAMOUNT'] * $currency_details[$currency['Currency']['id']]['CRATE'];

						$_purchase_ugx = $purchases[$count]['av_rate'] * $purchases[$count]['total_amount'];
						@$av_close_rate = ($_amount_ugx + $_purchase_ugx)/($purchases[$count]['total_amount'] + $currency_details[$currency['Currency']['id']]['CAMOUNT']);


							if(empty($_purchase_ugx)){
								$_amount_ugx = $currency_details[$currency['Currency']['id']]['CRATE'] * $currency_details[$currency['Currency']['id']]['CAMOUNT'];
								$_purchase_ugx = $purchases_pre[$count]['av_rate'] * $purchases_pre[$count]['total_amount'];
								@$av_close_rate = ($_amount_ugx + $_purchase_ugx)/($purchases_pre[$count]['total_amount'] + $currency_details[$currency['Currency']['id']]['CAMOUNT']);
							}
							
							if(empty($av_close_rate)) $av_close_rate = $purchases_pre[$count]['av_rate'];

							if(empty($av_close_rate)) $av_close_rate = $currency_details[$currency['Currency']['id']]['CRATE'];
							
							echo $av_close_rate;
						?>
						</span>
					</td>
					<?php endif;?>
					<td>
						<span class="">
						<?php
							$opening = $openings[0];
							$currency_details = json_decode($opening['OpeningDetail']['currency_details'],true);
						?>
						<?php
							@$todays_close=(($currency_details[$currency['Currency']['id']]['CAMOUNT'])+($purchases[$count]['total_amount']))-($sales[$count]['total_amount']);
							echo $todays_close;
						?>
						</span>
					</td>
				</tr>
			
			<?php endforeach;?>	
		</table>	
	</div>
	<div class="printable" style="display:none;"><br>
		<table style="width:100%;font-size:11px;">
			<tr style="background:#D4D8DD;">
				<td>Sales UGX<td>
				<td>Purchase UGX<td>
				<td>Expenses</td>
				<td>Withdrawal Cash</td>
				<td>Receivable Cash</td>
				<td>Additional Profits<td>
			</tr>
			<tr style="background:#f1f2f3;">
				<td><span class="ln"><?php echo $total_sales_ugx;?></span><td>
				<td><span class="ln"><?php echo $total_purchases_ugx;?></span><td>
				<td><span class="ln"><?php echo $expenses;?></span></td>
				<td><span class="ln"><?php echo $withdrawal_cash;?></span></td>
				<td><span class="ln"><?php echo $receivable_cash;?></span></td>
				<td><span class="ln"><?php echo $additional_profits;?></span><td>
			</tr>
			
			<tr style="background:#D4D8DD;">
				<td>Cash at bank Foreign<td>
				<td>Cash at bank Ugx<td>
				<td>Debtors</td>
				<td>Creditors</td>
				<td>Opening UGX</td>
				<td>Closing UGX:</td>
			</tr>
			<tr style="background:#f1f2f3;">
				<td><span class="ln"><?php echo $cash_at_bank_foreign;?></span><td>
				<td><span class="ln"><?php echo $cash_at_bank_ugx;?></span><td>
				<td><span class="ln"><?php echo $debtors;?></span></td>
				<td><span class="ln"><?php echo $creditors;?></span></td>
				<td><span class="ln"><?php echo $openings[0]['Opening']['opening_ugx'];?></span></td>
				<td><span class="ln"><?php echo $cash_at_hand;?></span><td>
			</tr>
		</table>
		<br>		
		<p><b>Closing Stock </b><span class="ln"><?php 
echo $cash_at_hand+$total_todays_close_ugx;
//echo ($cash_at_hand+$total_todays_close_ugx+$creditors)-($cash_at_bank_foreign+$cash_at_bank_ugx+$debtors);?></span> UGX<td>
	</div>
	<div class="printable" style="display:none;font-size:11px;"><br><br><br>
		<table style="width:100%;">
			<tr>
				<td>Cashier Signature</td>
				<td>Officer's Signature</td>
			</tr>
		</table>
	</div>
	<?php endif;?>
	
	
	
	
	<hr/>
	<?php if($openings[0]['Opening']['status']): ?>
			<?php echo '<b style="color:red;">This is a saved opening of </b>'.date((' l jS F Y'),strtotime($openings[0]['Opening']['date'])); ?>
			<div class="well">
			<?php echo $this->Form->create('Balancing',array('action'=>'save_opening',$user_id,array('class'=>'form-price-additions no-ajax'))); ?>
					<input type="hidden" name="data[Opening_old][user_id]" value="<?php echo $user_id; ?>" />
					<input type="hidden" name="data[Opening_old][total_todays_close_ugx]" value="<?php echo $total_todays_close_ugx; ?>" />
					<hr/>
					<center>
					<div class="btn btn-small">
						<label style="font-size:120%;"><b>Select the Next opening day:</b></label>
						<div class="input-append date" id='dp_next' data-date="<?php echo date('Y-m-d'); ?>" data-date-format="yyyy-mm-dd">
							<input class="span2" size="16" style="width:175px;" type="text" id='dp_today_selected' name="data[Opening][date]" value="<?php echo date('Y-m-d'); ?>" />
							<span class="add-on"><i class="icon-th"></i></span>
						</div>
					</div>
					</center>
			<span style="float:right;"><?php echo $this->Form->end(__('Re-SAVE')); ?></span>	
		</div><br/><br/>
	<?php else:?>		
		<div class="well">
			<?php echo $this->Form->create('Balancing',array('action'=>'save_opening',$user_id,array('class'=>'form-price-additions no-ajax'))); ?>
					<input type="hidden" name="data[Opening_old][user_id]" value="<?php echo $user_id; ?>" />
					<input type="hidden" name="data[Opening_old][total_todays_close_ugx]" value="<?php echo $total_todays_close_ugx; ?>" />
					<hr/>
					<center>
					<div class="btn btn-small">
						<label style="font-size:120%;"><b>Select the Next opening day:</b></label>
						<div class="input-append date" id='dp_next' data-date="<?php echo date('Y-m-d'); ?>" data-date-format="yyyy-mm-dd">
							<input class="span2" size="16" style="width:175px;" type="text" id='dp_today_selected' name="data[Opening][date]" value="<?php echo date('Y-m-d'); ?>" />
							<span class="add-on"><i class="icon-th"></i></span>
						</div>
					</div>
					</center>
			<span style="float:right;"><?php echo $this->Form->end(__('SAVE')); ?></span>	
		</div><br/><br/>
	<?php endif; ?>
		
		<script>
			$(document).ready(function(){
				$('#dp_next').datepicker({
					format: 'yyyy-mm-dd'
				});
				
				$('.apply-additions').click(function(){
					console.log($('.receivable_cash').val());
					var receivable_cash=$('.receivable_cash').val();
					var withdrawal_cash=$('.withdrawal_cash').val();
					var additional_profits=$('.additional_profits').val();
					data={'date_today':$('#dp_today_selected').val()};
					
					$.ajax({type: "POST",url: '<?php echo $this->webroot.'balancings/show_individually';?>/'+receivable_cash+'/'+withdrawal_cash+'/'+additional_profits+'/<?php echo $user_id;?>',data: data,dataType: "html",
						success: function(data) {$('.dynamic-content').html(data);} 
					});
				});
			});
		</script>
<div>

<?php if($show_average_rates):?>
<h3>Trial balance</h3>
<div>
	<table class="well">
		<tr style="background: #2e335b;color:#ddd">
			<td>
				&nbsp;
			</td>
			<td>
				<b>DEBIT</b>
			</td>
			<td>
				<b>CREDIT</b>
			</td>
		</tr>
		<?php $debit=0;$credit=0;?>
		<tr style="background: #2e335b;color:#ddd">
			<td>Opening Cash</td>
			<td>&nbsp;</td>
			<td><span class="ln"><?php echo $openings[0]['Opening']['opening_ugx'];$credit+=$openings[0]['Opening']['opening_ugx']; ?></span>&nbsp;</td>
		</tr>
		<?php if (!($balance_with_safe && $is_safe)):?>
		<tr style="background: #2e335b;color:#ddd">
			<td>Purchases</td>
			<td><span class="ln"><?php echo $total_purchases_ugx;$debit+=$total_purchases_ugx;?></span>&nbsp;</td>
			<td>&nbsp;</td>
		</tr>
		<tr style="background: #2e335b;color:#ddd">
			<td>Sales</td>
			<td>&nbsp;</td>
			<td><span class="ln"><?php echo $total_sales_ugx;$credit+=$total_sales_ugx;?></span>&nbsp;</td>
		</tr>
		<?php endif;?>
		<tr style="background: #2e335b;color:#ddd">
			<td>Withdrawal cash</td>
			<td><?php echo '<span class="ln">'.$withdrawal_cash.'</span>';$debit+=$withdrawal_cash;?></td>
			<td>&nbsp;</td>
		</tr>
		<tr style="background: #2e335b;color:#ddd">
			<td>Receivable cash</td>
			<td>&nbsp;</td>
			<td><?php echo '<span class="ln">'.$receivable_cash.'</span>';$credit+=$receivable_cash;?></td>
		</tr>
		<tr style="background: #2e335b;color:#ddd">
			<td>Expenses</td>
			<td><?php echo '<span class="ln">'.$expenses.'</span>';$debit+=$expenses;?></td>
			<td>&nbsp;</td>
		</tr>
		<tr style="background: #2e335b;color:#ddd">
			<td>Additional Profits</td>
			<td>&nbsp;</td>
			<td><?php echo '<span class="ln">'.$additional_profits.'</span>';$credit+=$additional_profits;?></td>
		</tr>
		<tr style="background: #2e335b;color:#ddd">
			<td>Creditors</td>
			<td>&nbsp;</td>
			<td><?php echo '<span class="ln">'.$creditors.'</span>';$credit+=$creditors;?></td>
		</tr>
		<tr style="background: #2e335b;color:#ddd">
			<td>Debtors</td>
			<td><?php echo '<span class="ln">'.$debtors.'</span>';$debit+=$debtors;?></td>
			<td>&nbsp;</td>
		</tr>
		<tr style="background: #2e335b;color:#ddd">
			<td>Cash at bank</td>
			<td><span class="ln"><?php echo $cash_at_bank_foreign+$cash_at_bank_ugx;$debit+=$cash_at_bank_foreign+$cash_at_bank_ugx;?></span></td>
			<td>&nbsp;</td>
		</tr>
		<tr style="background: #2e335b;color:#ddd">
			<td>Closing cash at hand</td>
			<td><span class="ln">
				<?php echo ($cash_at_hand);
				$debit+=$cash_at_hand; 
				?></span></td>
			<td>&nbsp;</td>
		</tr>
		<tr style="background: #2e335b;color:#ddd">
			<td>Recieved From cashiers</td>
			<td>&nbsp;</td>
			<td><span class="ln">
				<?php 
					echo ($safe_transactions_received_ugx);
					$credit+=($safe_transactions_received_ugx); 
				?>
			</span></td>
		</tr>
		<tr style="background: #2e335b;color:#ddd">
			<td>Sent To cashiers</td>
			<td><span class="ln">
				<?php 
					echo ($safe_transactions_sent_ugx);
					$debit+=($safe_transactions_sent_ugx); 
				?>
			</span></td>
			<td>&nbsp;</td>
		</tr>

		<tr style="background: <?php echo ($debit==$credit)?'green':'red';?>;color:#ddd;font-weight:bold;">
			<td>&nbsp;</td>
			<td><span class="ln"><?php echo $debit; ?></span></td>
			<td><span class="ln"><?php echo $credit; ?></span></td>
		</tr>
	</table>
	<!--<p><a target="_blank" class="no-ajax btn btn-small" href="<?php echo $this->webroot;?>balancing/trial_balance_excell/?opening_cash=<?php echo $openings[0]['Opening']['opening_ugx'];?>&purchases=<?php echo $total_purchases_ugx;?>&sales=<?php echo $total_sales_ugx;?>&withdrawals=<?php echo $withdrawal_cash;?>&receivables=<?php echo $receivable_cash;?>&expenses=<?php echo $expenses;?>&additional_profits=<?php echo $additional_profits;?>&creditors=<?php echo $creditors;?>&debtors=<?php echo $debtors;?>&cash_at_bank=<?php echo $cash_at_bank_foreign+$cash_at_bank_ugx;?>&closing_cash_at_hand=<?php echo $cash_at_hand;?>&debit=<?php echo $debit;?>&credit=<?php echo $credit;?>"><i class="icon icon-file"></i>Excel file</a></p>-->
</div>
<?php endif;?>
<script>
	function do_print(){
		$('.non_printable').remove();
		var x=window.open("","");
		var xx='';
		$.each($('.printable'),function(){
			xx+=$(this).html();
		});
		x.document.write(xx);
		x.window.print();
	}
</script>
</script>