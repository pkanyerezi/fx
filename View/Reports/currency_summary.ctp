<?php echo $this->Html->script(array('script_dynamic_content'));?>
<div class="flash-message"><?php echo $this->Session->flash(); ?></div>
<span class="btn btn-small pull-right" onclick="do_print();"><i class="icon icon-print"></i> Print Summary</span>
<div class="well well-new printable">
	<?php if(isset($from) && isset($to)):?>
		<h6><?php echo 'Currency Summary from '.date('d/M/y',strtotime($from)).' to '.date('d/M/y',strtotime($to)).' <span class="non_printable">('.$this->Time->timeAgoInWords($from, array('accuracy' => array('day' => 'day'),'end' => '1 year')).')</span>';?></h6>
	<?php endif; ?>
	<table class="well">
			<tr style="background: #2e335b;color:#ddd">
				<td><b>Currency</b></td>
				<td><b>Purchases</b></td>
				<td><b>Purchases AVG rate</b></td>
				<td><b>Purchases UGX</b></td>
				<td><b>Sales</b></td>
				<td><b>Sales AVG rate</b></td>
				<td><b>Sales UGX</b></td>
			</tr>
		<?php 
			$total_purchases_ugx=0;
			$total_purchases=0;
			$total_sales_ugx=0;
			$total_sales=0;
			$count=-1;
			foreach($currencies as $currency):
				$count++;
		?>
			<?php if($currency['Currency']['id']=='c8'):?>
				<?php 
					$other_count=-1;
					foreach($other_currencies as $other_currency):
						$other_count++;
					?>
					<tr>				
						<td>
							<b><?php echo $other_currency['OtherCurrency']['name']; ?></b>
						</td>
						<td style="background:lime">
							<span class="ln"><?php echo $other_currencies_purchases[$other_count]['total_amount']; ?></span>
						</td>
						<td style="background:lime">
							<span class="ln"><?php echo $other_currencies_purchases[$other_count]['av_rate']; ?></span>
						</td>
						<td style="background:lime">
							<span class="ln"><?php echo $other_currencies_purchases[$other_count]['total_amount']*$other_currencies_purchases[$other_count]['av_rate']; ?></span>
						</td>
						<td style="background:cyan">
							<span class="ln"><?php echo $other_currencies_sales[$other_count]['total_amount']; ?></span>
						</td>				
						<td style="background:cyan">
							<span class="ln"><?php echo $other_currencies_sales[$other_count]['av_rate']; ?></span>
						</td>				
						<td style="background:cyan">
							<span class="ln"><?php echo $other_currencies_sales[$other_count]['total_amount']*$other_currencies_sales[$other_count]['av_rate']; ?></span>
						</td>
					</tr>
				<?php endforeach; ?>
			<?php else: ?>
				<tr>
					
					<td>
						<b><?php echo $currency['Currency']['description']; ?></b>
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
			<?php endif;?>
			
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
<script>
	function do_print(){
		$('.non_printable').remove();
		var x=window.open("","");
		x.document.write($('.printable').html());
		x.window.print();
	}
</script>