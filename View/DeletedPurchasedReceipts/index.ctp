<?php echo $this->Html->script(array('script_dynamic_content'));?>
<div class="flash-message"><?php echo $this->Session->flash(); ?></div>
<div style="float:right;margin-right:2.3%;">	
	<span class="btn btn-small" onclick="do_print();"><i class="icon icon-print"></i> Print</span>
</div>
<div class="deletedPurchasedReceipts printable well well-new">
	<style>
	<!--
		.my_data table tr td{
			border:1px solid #eee;
		}
	-->
	</style>
	<h2>
		<?php echo __('Deleted Purchase Receipts'); ?>
		<?php if(isset($large_cash)):?>
			<?php echo '(Large cash)'; ?>
		<?php endif; ?>
	</h2>
	<?php if(isset($from) && isset($to)):?>
		<h6><?php echo 'from '.$from.', to '.$to.' <span class="non_printable">('.$this->Time->timeAgoInWords($from, array('accuracy' => array('day' => 'day'),'end' => '1 year')).')</span>';?></h6>
	<?php endif; ?>
	
	<?php if(isset($large_cash)):?>
		<span class="non_printable" style="color:maroon;"><b><?php echo ('Average Dollar Rate Used: $<span class="ln">'.$dollar_av_rate.'</span>');?></b></span>
	<?php endif; ?>
	
	<div class="my_data">
		<table cellpadding="0" cellspacing="0" style="width:100%;text-align:center;">
		<tr>
				<th class="actions non_printable"><?php echo __(''); ?></th>
				<th class="non_printable"><?php echo $this->Paginator->sort('id','Receipt number'); ?></th>
				<th>Amount</th>
				<th>Rate</th>
				<th><?php echo $this->Paginator->sort('amount_ugx'); ?></th>
				<th><?php echo $this->Paginator->sort('currency_id'); ?></th>
				<th><?php echo $this->Paginator->sort('customer_name'); ?></th>
				<th><?php echo $this->Paginator->sort('date'); ?></th>
				<th><?php echo $this->Paginator->sort('t_time','Time'); ?></th>	
				<th class="non_printable">Created By</th>
		</tr>
		<style>
			<!--
			table tr:nth-child(even) {
				background: none;
			}
			-->
		</style>
		<?php $total_ugx=0;?>
		<?php foreach ($deletedPurchasedReceipts as $deletedPurchasedReceipt): ?>
		<tr>
			<td class="non_printable">
				<div class="btn-group">
					<button class="btn dropdown-toggle" data-toggle="dropdown">
					  <i class="icon icon-certificate"></i>
					  <span class="caret"></span>
					</button>
					<ul class="dropdown-menu">
						<li><?php echo $this->Html->link(__('View'), array('action' => 'view', $deletedPurchasedReceipt['DeletedPurchasedReceipt']['id']),array('class'=>'action-view')); ?></li>
						<li><?php echo $this->Html->link(__('Return'), array('action' => 'return_back', $deletedPurchasedReceipt['DeletedPurchasedReceipt']['id']),array('class'=>'action-return confirm-first','data-confirm-text'=>'Are you sure?')); ?></li>
						<?php if($super_admin): ?>
							<li><?php echo $this->Html->link(__('Delete'), array('action' => 'delete', $deletedPurchasedReceipt['DeletedPurchasedReceipt']['id']),array('class'=>'action-delete confirm-first','data-confirm-text'=>'Are you sure you want to delete it?')); ?></li>
						<?php endif;?>
					</ul>
				</div>
			</td>
			<td class="non_printable"><?php echo h($deletedPurchasedReceipt['DeletedPurchasedReceipt']['id']); ?>&nbsp;</td>
			<td>
				<?php if($deletedPurchasedReceipt['Currency']['id']=='c8'): ?>
					<?php echo h($deletedPurchasedReceipt['DeletedPurchasedReceipt']['orig_amount']); ?>
				<?php else: ?>
					<?php echo h($deletedPurchasedReceipt['DeletedPurchasedReceipt']['amount']); ?>
				<?php endif; ?>
				&nbsp;			
			</td>
			<td>
				<?php if($deletedPurchasedReceipt['Currency']['id']=='c8'): ?>
					<?php echo h($deletedPurchasedReceipt['DeletedPurchasedReceipt']['orig_rate']); ?>
				<?php else: ?>
					<?php echo h($deletedPurchasedReceipt['DeletedPurchasedReceipt']['rate']); ?>
				<?php endif; ?>
				&nbsp;			
			</td>
			<td><?php echo h($deletedPurchasedReceipt['DeletedPurchasedReceipt']['amount_ugx']);$total_ugx+=($deletedPurchasedReceipt['DeletedPurchasedReceipt']['amount_ugx']); ?>&nbsp;</td>
			<td>
				<?php if($deletedPurchasedReceipt['Currency']['id']=='c8'): ?>
					<?php echo $deletedPurchasedReceipt['DeletedPurchasedReceipt']['other_name']; ?>
				<?php else: ?>
					<?php echo $deletedPurchasedReceipt['Currency']['description']; ?>
				<?php endif; ?>
				&nbsp;			
			</td>
			<td><?php echo h($deletedPurchasedReceipt['DeletedPurchasedReceipt']['customer_name']); ?>&nbsp;</td>
			<td><?php echo h($deletedPurchasedReceipt['DeletedPurchasedReceipt']['date']); ?>&nbsp;</td>
			<td><?php echo date('h:i:s a',strtotime(h($deletedPurchasedReceipt['DeletedPurchasedReceipt']['t_time']))); ?>&nbsp;</td>
			<td class="non_printable"><?php echo h($deletedPurchasedReceipt['DeletedPurchasedReceipt']['name']); ?>&nbsp;</td>	
		</tr>
	<?php endforeach; ?>
		<tr>
			<td colspan="10" style="background:#2c2c2c;color:#fff;">
				<b>Total (UGX for the above records):</b> <span style="margin-left:30px;" class="ln"><?php echo $total_ugx; ?></span>
			</td>
		</tr>
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
	
	function do_print(){
		$('.non_printable').remove();
		var x=window.open("","");
		x.document.write($('.printable').html());
		x.window.print();
	}
</script>