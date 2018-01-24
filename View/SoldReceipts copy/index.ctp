<?php echo $this->Html->script(array('script_dynamic_content'));?>
<div class="flash-message"><?php echo $this->Session->flash(); ?></div>
<div style="float:right;margin-right:2.3%;">
	<span class="btn btn-small" onclick="do_receipt();"><i class="icon icon-print"></i> Print</span>	
	<span class="btn btn-small" onclick="do_print_checked_receipts();"><i class="icon icon-print"></i> Print checked</span>
	<?php if(isset($large_cash)):?>
		<span class="btn btn-small"><a href="<?php echo $this->webroot.'sold_receipts/send_sales_large_cash';?>"><i class="icon icon-upload"></i> Send Only Sales Large Cash</span>
	<?php endif; ?>
</div>

<div class="soldReceipts printable well well-new">
	<style>
	<!--
		.my_data table tr td{
			border:1px solid #eee;
		}
	-->
	</style>
	<h2><?php echo __('Sales Receipts'); ?>
		<?php if(isset($large_cash)):?>
			<?php echo '(Large cash)'; ?> | <a class="btn btn-small no-ajax generate-excel" id="generate-excel" href="<?php echo $this->webroot;?>sold_receipts/excel_large_cash" target="_blank">Excel file(Both Purchase and Sales)</a>
			<script>
				$(document).ready(function(){
					$("#generate-excel").bind("click", function(e) { 
						e.preventDefault();
					   	var href=$(this).attr('href');
						href+='?date_from='+($('#dp_from_selected').val());
						href+='&date_to='+($('#dp_to_selected').val());
						location.href = href; 
					});
				});
			</script>
		<?php endif; ?>
	</h2>
	<?php if(isset($from) && isset($to)):?>
		<h6><?php echo 'from '.$from.', to '.$to.' <span class="non_printable">('.$this->Time->timeAgoInWords($from, array('accuracy' => array('day' => 'day'),'end' => '1 year')).')</span>';?></h6>
	<?php endif; ?>
	<a href="<?php echo $this->webroot;?>sold_receipts/add" onclick="return false;" style="float:right;" class="non_printable btn btn-small" >
		<i class="icon icon-plus-sign"></i> New Sales Receipt
	</a>
	
	<?php if(isset($large_cash)):?>
		<span class="non_printable" style="color:maroon;"><b><?php echo ('Average Dollar Rate Used: $<span class="ln">'.$dollar_av_rate.'</span>');?></b></span>
	<?php endif; ?>
	<?php if(!empty($soldReceipts)):?>
		<div class="alert alert-error" style="font-weight:bolder;position: fixed;bottom: 0;right: 0;width:150px;" onclick="$(this).fadeOut('slow');">
			<span style="font-size:15px;">HELP</span><hr/>
			<i class="icon icon-pencil"></i> Click a currency from &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;the receipts to view &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;only for that currency<br><br>
			<i class="icon icon-pencil"></i> Click a title to sort by &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;the title in ASCENDING &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;OR DESCENDING order
		</div>
	<?php endif;?>
	<div class="my_data">
		<table cellpadding="0" cellspacing="0" style="width:100%;text-align:center;">
		<tr>
				<th class="actions non_printable"><?php echo __(''); ?></th>
				<th class="actions non_printable"><?php echo __(''); ?></th>
				<th class="non_printable"><?php echo $this->Paginator->sort('id','Receipt number'); ?></th>
				<th>Amount</th>
				<th class="non_printable">Rate</th>
				<th><?php echo $this->Paginator->sort('amount_ugx'); ?></th>
				<th><?php echo $this->Paginator->sort('currency_id'); ?></th>
				<th><?php echo $this->Paginator->sort('instrument'); ?></th>
				<th><?php echo $this->Paginator->sort('customer_name'); ?></th>
				<th><?php echo $this->Paginator->sort('date'); ?></th>			
				<th><?php echo $this->Paginator->sort('t_time','Time'); ?></th>			
				<th class="non_printable">Created by</th>			
		</tr>
		<?php $total_ugx=0;?>
		<?php $total_foreign=0;?>
		<?php $total_rate=0;?>
		<?php $total_count=count($soldReceipts);?>
		<?php foreach ($soldReceipts as $soldReceipt): ?>
		<tr style="border-left:4px solid #F9F9F9;border-right:4px solid #F9F9F9;">
			<td class="non_printable">
				<div class="btn-group">
					<button class="btn dropdown-toggle" data-toggle="dropdown">
					  <i class="icon icon-certificate"></i>
					  <span class="caret"></span>
					</button>
					<ul class="dropdown-menu">
						<li><a href="<?php echo $this->webroot;?>sold_receipts/print_receipt/<?php echo h($soldReceipt['SoldReceipt']['id']); ?>" onclick="return false;" class="print-receipt no-ajax" ><i class="icon icon-print"></i> Print</a></li>
						
						<?php if($super_admin): ?>
							<li class="divider"></li>
							<?php if($soldReceipt['SoldReceipt']['is_uploaded']==1): ?>
								<li><a href="<?php echo $this->webroot;?>sold_receipts/should_upload/<?php echo h($soldReceipt['SoldReceipt']['id']); ?>/0" onclick="return false;" >Uploadable</a></li>
							<?php elseif($soldReceipt['SoldReceipt']['is_uploaded']==0): ?>
								<li><a href="<?php echo $this->webroot;?>sold_receipts/should_upload/<?php echo h($soldReceipt['SoldReceipt']['id']); ?>/1" onclick="return false;" >Dont upload</a></li>
							<?php endif; ?>
						<?php endif; ?>
						
						<li class="divider"></li>
						<li><?php echo $this->Html->link(__('View'), array('action' => 'view', $soldReceipt['SoldReceipt']['id']),array('class'=>'action-view')); ?></li>
						<?php //if($super_admin): ?>
							<li><?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $soldReceipt['SoldReceipt']['id']),array('class'=>'action-edit')); ?></li>						
							<li><?php echo $this->Html->link(__('Delete'), array('action' => 'delete', $soldReceipt['SoldReceipt']['id']),array('class'=>'action-delete confirm-first','data-confirm-text'=>'Are you sure you want to delete it?')); ?></li>
						<?php //endif;?>						
					</ul>
				</div>	
			</td>
			<td class="non_printable">
				<div><br>
					<input type="checkbox" class="sold-receipt" value="<?php echo h($soldReceipt['SoldReceipt']['id']); ?>">    
				</div>
			</td>
			<td class="non_printable"><?php echo h($soldReceipt['SoldReceipt']['id']); ?>&nbsp;</td>
			
			<td>
				<?php if($soldReceipt['Currency']['id']=='c8'): ?>
					<?php $total_foreign += $soldReceipt['SoldReceipt']['orig_amount']; echo h($soldReceipt['SoldReceipt']['orig_amount']); ?>
				<?php else: ?>
					<?php $total_foreign += $soldReceipt['SoldReceipt']['amount']; echo h($soldReceipt['SoldReceipt']['amount']); ?>
				<?php endif; ?>
				&nbsp;			
			</td>
			<td class="non_printable">
				<?php if($soldReceipt['Currency']['id']=='c8'): ?>
					<?php echo h($soldReceipt['SoldReceipt']['orig_rate']);$total_rate+=$soldReceipt['SoldReceipt']['orig_rate']; ?>
				<?php else: ?>
					<?php echo h($soldReceipt['SoldReceipt']['rate']);$total_rate+=$soldReceipt['SoldReceipt']['rate']; ?>
				<?php endif; ?>
				&nbsp;			
			</td>
			<td><?php echo h($soldReceipt['SoldReceipt']['amount_ugx']);$total_ugx+=($soldReceipt['SoldReceipt']['amount_ugx']); ?>&nbsp;</td>
			<td>
				<?php if($soldReceipt['Currency']['id']=='c8'): ?>
					<a class="use-ajax" href="<?php echo $this->webroot;?>sold_receipts/index/?currency=c8&other_currency=<?php echo $soldReceipt['SoldReceipt']['other_currency_id'];?>&date_from=<?php echo $from;?>&date_to=<?php echo $to;?>">
					<?php echo $soldReceipt['SoldReceipt']['other_name']; ?>
				<?php else: ?>
					<a class="use-ajax" href="<?php echo $this->webroot;?>sold_receipts/index/?currency=<?php echo $soldReceipt['SoldReceipt']['currency_id'];?>&date_from=<?php echo $from;?>&date_to=<?php echo $to;?>">
					<?php echo $soldReceipt['Currency']['description']; ?>
				<?php endif; ?>
				&nbsp;		
				</a>
			</td>
			<td><?php echo h($soldReceipt['SoldReceipt']['instrument']); ?>&nbsp;</td>
			<td><?php echo h($soldReceipt['SoldReceipt']['customer_name']); ?>&nbsp;</td>
			<td><?php echo h($soldReceipt['SoldReceipt']['date']); ?>&nbsp;</td>
			<td><?php echo date('h:i:s a',strtotime(h($soldReceipt['SoldReceipt']['t_time']))); ?>&nbsp;</td>
			<td class="non_printable"><?php echo h($soldReceipt['SoldReceipt']['name']); ?>&nbsp;</td>
			
		</tr>
	<?php endforeach; ?>
		<?php if(isset($setCurrency)):?>
		<tr  style="background:#2c2c2c;color:#fff;">
			<td colspan="2">Total(for the above records)</td>
			<td colspan="1"><span class="ln"><?php echo $total_foreign; ?></span></td>
			<td colspan="7">UGX:<span class="ln"><?php echo $total_ugx; ?></span></td>
			<td colspan="1"><span>AVG-RATE:<?php echo round($total_ugx/$total_foreign,2); ?></span></td>
			<td colspan="1"><span>AVG-RATE:<?php echo round($total_rate/$total_count,2); ?></span></td>
		</tr>
		<?php else:?>
		<tr  style="background:#2c2c2c;color:#fff;">
			<td colspan="3">Total(for the above records)</td>
			<td colspan="8">UGX:<span class="ln"><?php echo $total_ugx; ?></span></td>
		</tr>
		<?php endif;?>
		</table>
	</div>
	<p class="non_printable">
	<?php
	echo $this->Paginator->counter(array(
	'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
	));
	?>	</p>
	<div class="paging non_printable">
	<?php
		echo $this->Paginator->prev('< ' . __('previous'), array(), null, array('class' => 'prev disabled'));
		echo $this->Paginator->numbers(array('separator' => ''));
		echo $this->Paginator->next(__('next') . ' >', array(), null, array('class' => 'next disabled'));
	?>
	</div>
</div>
<script>
	$('.print-receipt').click(function(){
		$.get($(this).attr('href'),function(data){
			//alert(data);
		});
	});
	
	function do_receipt(){
		$('.non_printable').remove();
		var x=window.open("","");
		x.document.write($('.printable').html());
		x.window.print();
	}
	
	function do_print_checked_receipts(){
		var receipts = '';
		$('.sold-receipt:checked').each(function(){
			receipts+=((receipts.length>1)?','+($(this).val()):($(this).val()));
		});
		if(receipts.length>1){
			$.post('<?php echo $this->webroot;?>sold_receipts/print_multiple_receipts/'+receipts,function(data){
				alert(data);
			});
		}
	}
</script>
