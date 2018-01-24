<?php echo $this->Html->script(array('script_dynamic_content'));?>
<div class="flash-message"><?php echo $this->Session->flash(); ?></div>

<span class="btn btn-small pull-right" onclick="do_print();"><i class="icon icon-print"></i> Print Summary</span>
<div class="well well-new printable">
	<?php if(isset($from) && isset($to)):?>
		<h6><?php echo 'Summary from <span class="label">'.date('d M y',strtotime($from)).'</span> to <span class="label">'.date('d M y',strtotime($to)).'</span>';?></h6>
	<?php endif; ?>
	<table class="table">
		<tr style="background: #2e335b;color:#ddd">
			<td>Sales Ugx</td>
			<td>Purchases Ugx</td>
			<td>Profits</td>
			<td>Gross Profit</td>
			<td>Expenses</td>
			<td>Receivable Cash</td>
			<td>Withdrawal Cash</td>
			<td>Additional Profits</td>
			<td>Debtors</td>
			<td>Creditors</td>
		</tr>
		<tr>
			<td style="background:lime"><span class="ln"><?php echo $openings_date_range[0][0]['total_sales_ugx']; ?></span></td>
			<td style="background:lime"><span class="ln"><?php echo $openings_date_range[0][0]['total_purchases_ugx']; ?></span></td>
			<td style="background:lime">
				<span class="ln">
					<?php echo $openings_date_range[0][0]['total_gross_profit'] - $openings_date_range[0][0]['total_expenses']; ?>
				</span>
			</td>
			<td style="background:lime"><span class="ln"><?php echo $openings_date_range[0][0]['total_gross_profit']; ?></span></td>
			<td style="background:lime"><span class="ln"><?php echo $openings_date_range[0][0]['total_expenses']; ?></span></td>
			<td style="background:lime"><span class="ln"><?php echo $openings_date_range[0][0]['receivable_cash']; ?></span></td>
			<td style="background:lime"><span class="ln"><?php echo $openings_date_range[0][0]['withdrawal_cash']; ?></span></td>
			<td style="background:lime"><span class="ln"><?php echo $openings_date_range[0][0]['additional_profits']; ?></span></td>
			<td style="background:lime"><span class="ln"><?php echo $openings_date_range[0][0]['debtors']; ?></span></td>
			<td style="background:lime"><span class="ln"><?php echo $openings_date_range[0][0]['creditors']; ?></span></td>
		</tr>
	</table>
</div>

<div class="well well-new">	
	<?php if(count($openings)): ?>
	<p>
		<div style="font-size:150%"><b>Initial Position: </b><span class="ln"><?php echo $fox['Fox']['initial_position']; ?></span> UGX</div><hr/>
		<div style="font-size:100%"><b>Total Profits: </b><span class="ln"><?php echo $openings[0][0]['total_profits']; ?></span> UGX</div><br/>
		<div style="font-size:100%"><b>Total Expenses: </b><span class="ln"><?php echo $openings[0][0]['total_expenses']; ?></span> UGX</div><br/>
		<div style="font-size:100%"><b>Total Net Profits: </b><span class="ln"><?php echo ($openings[0][0]['total_profits']-$openings[0][0]['total_expenses']); ?></span> UGX</div><hr/>
		<div style="font-size:150%"><b>Current Position: </b><span class="ln"><?php echo ($fox['Fox']['initial_position'])+($openings[0][0]['total_profits']-$openings[0][0]['total_expenses']); ?></span> UGX</div>
	</p>
	<?php else: ?>
		No openings found in the system.
	<?php endif; ?>
</div>
<script>
	function do_print(){
		$('.non_printable').remove();
		var x=window.open("","");
		x.document.write($('.printable').html());
		x.window.print();
	}
</script>