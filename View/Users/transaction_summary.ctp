<?php echo $this->Html->script(array('script_dynamic_content'));?>
<div class="flash-message"><?php echo $this->Session->flash(); ?></div>
<div style="float:right;margin-right:2.3%;">
	<span class="btn btn-small" onclick="do_receipt();"><i class="icon icon-print"></i> Print</span>
</div>
<div class="withdrawals well">
	<?php if(isset($customer_id)):?>
		<a href="<?php echo $this->webroot.'users/view/'.$customer_id;?>"><span class="btn btn-inverse"><i class="icon-white icon-arrow-left"></i> back</span></a>
	<?php else:?>
		<?php $customer_id = null;?>
	<?php endif; ?>
	<div class="printable">
	<h2><?php echo __('Transaction Summary - '.ucwords($customer['Customer']['name'])); ?></h2>
	<?php if(isset($from) && isset($to)):?>
		<h6><?php echo 'from '.$from.', to '.$to.' ('.$this->Time->timeAgoInWords($from, array('accuracy' => array('day' => 'day'),'end' => '1 year')).')';?></h6>
	<?php endif; ?>
	
	<table cellpadding="0" cellspacing="0" style="width:100%;">
	<tr>
			<th style="text-align: left;">Bank</th>
			<th style="text-align: left;">Debit</th>
			<th style="text-align: left;">Credit</th>
			<th style="text-align: left;">AdditionalInfo</th>
			<th style="text-align: left;">Date</th>
	</tr>
	<?php $total_amount=0;?>
	<?php foreach ($transactions as $transaction): ?>
	<?php $transaction = $transaction[0];?>
	<tr>
		<td><?php echo h($transaction['customer']);?>&nbsp;</td>
		<td><?php 
			if(in_array($transaction['reason'],['ToBureau','FromBank','Debt'])){
				echo h($transaction['amount']);
				$total_amount_debit+=$transaction['amount']; 
			}else{
				echo '-';
			}
		?>&nbsp;</td>
		<td><?php 
			if(in_array($transaction['reason'],['FromBureau','ToBank','Credit'])){
				echo h($transaction['amount']);
				$total_amount_credit+=$transaction['amount']; 
			}else{
				echo '-';
			}
		?>&nbsp;</td>
		<td><?php echo h($transaction['additional_info']); ?>&nbsp;</td>
		<td><?php echo h($transaction['date']); ?>&nbsp;</td>
	</tr>
<?php endforeach; ?>
	<tr>
		<td style="background:#2c2c2c;color:#fff">Total:</td>
		<td style="background:#2c2c2c;color:#fff" class="ln"><?php echo $total_amount_debit; ?></td>
		<td style="background:#2c2c2c;color:#fff" class="ln"><?php echo $total_amount_credit; ?></td>
		<td style="background:#2c2c2c;color:#fff">Balance =<?=number_format($total_amount_credit-$total_amount_debit)?></td>
		<td style="background:#2c2c2c;color:#fff"></td>
	</tr>
	</table>
	</div>
	
	<?php $total_records = count($transactions);?>
	<p>Page <?=$page?>, showing <?=$total_records;?> records</p>
	<div class="paging">
		
		<?php if(!empty($from)) $ajax = 'user-ajax'; else $ajax = 'no-ajax';?>
		
		<?php if($page>1):?>
			<a href="<?=$this->webroot?>users/transaction_summary/<?=$customer_id?>/<?=($page-1)?> class="prev <?=$ajax?>">&lt; previous</a>
		<?php else:?>
			<span class="prev disabled">&lt; previous</span>
		<?php endif;?>
		
		<?php if($total_records==$limit):?>
			<a href="<?=$this->webroot?>users/transaction_summary/<?=$customer_id?>/<?=($page+1)?>" class="next <?=$ajax?>">next &gt;</a>
		<?php else:?>
			<span class="next disabled">next &gt;</span>
		<?php endif;?>
	</div>
</div>

<script>
	function do_receipt(){
		$('.non_printable').remove();
		var x=window.open("","");
		x.document.write($('.printable').html());
		x.window.print();
	}
</script>
