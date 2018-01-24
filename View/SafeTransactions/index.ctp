<?php echo $this->Html->script(array('script_dynamic_content'));?>
<div class="flash-message"><?php echo $this->Session->flash(); ?></div>
<?php if(!empty($safeTransactions)):?>
<div>
	<a href="<?php echo $this->webroot;?>safe_transactions" class="btn btn-small">View All</a>
	<span class="btn btn-small" onclick="do_print();"><i class="icon icon-print"></i> Print page</span>
</div>
<?php endif;?>
<div class="soldReceipts printable well well-new">
	<style>
	<!--
		.my_data table tr td{
			border:1px solid #eee;
		}
	-->
	</style>
	<h2>Safe Transactions</h2>
	<?php if(isset($from) && isset($to)):?>
		<h6><?php echo 'from '.$from.', to '.$to.' <span class="non_printable">('.$this->Time->timeAgoInWords($from, array('accuracy' => array('day' => 'day'),'end' => '1 year')).')</span>';?></h6>
	<?php endif; ?>
	
	<div class="my_data">
		<table cellpadding="0" cellspacing="0" style="width:100%;text-align:center;">
		<tr>
				<th class="actions non_printable"><?php echo __(''); ?></th>
				<th><?php echo $this->Paginator->sort('date'); ?></th>
				<th><?php echo $this->Paginator->sort('amount'); ?></th>
				<th><?php echo $this->Paginator->sort('user_id','From'); ?></th>
				<th><?php echo $this->Paginator->sort('transaction_to','To'); ?></th>
				<th><?php echo $this->Paginator->sort('approved_by'); ?></th>
				<th><?php echo $this->Paginator->sort('transaction_type','Type'); ?></th>
				<th><?php echo $this->Paginator->sort('status'); ?></th>
				<th><?php echo $this->Paginator->sort('comment'); ?></th>
		</tr>
		<?php foreach ($safeTransactions as $safeTransaction): ?>
		<tr style="border-left:4px solid #F9F9F9;border-right:4px solid #F9F9F9;">
			<td class="non_printable">
				<div class="btn-group">
					<button class="btn dropdown-toggle" data-toggle="dropdown">
					  <i class="icon icon-certificate"></i>
					  <span class="caret"></span>
					</button>
					<ul class="dropdown-menu">
						<li><a href="<?php echo $this->webroot;?>safe_transactions/view/<?php echo h($safeTransaction['SafeTransaction']['id']); ?>" class="">View/Print</a></li>
						
						<?php if($cashier && $safeTransaction['SafeTransaction']['status']=='APPROVED' && $safeTransaction['SafeTransaction']['transaction_to']==$authUser['id']): ?>
							<li class="divider"></li>
							<li><?php echo $this->Html->link(__('Accept'), array('action' => 'accept', $safeTransaction['SafeTransaction']['id']),array('class'=>'action-delete confirm-first','data-confirm-text'=>'Do you accept that you have receieved the amount?')); ?></li>
						<?php endif; ?>

						<?php if($super_admin && $safeTransaction['SafeTransaction']['status']=='PENDING'): ?>
							<li class="divider"></li>
							<li><?php echo $this->Html->link(__('Approve'), array('action' => 'approve', $safeTransaction['SafeTransaction']['id']),array('class'=>'action-delete confirm-first','data-confirm-text'=>'Are you sure you want to approve this transaction ?')); ?></li>
						<?php endif; ?>
						<?php if($super_admin): ?>
							<li><?php echo $this->Html->link(__('Cancel/Delete'), array('action' => 'cancel', $safeTransaction['SafeTransaction']['id']),array('class'=>'action-delete confirm-first','data-confirm-text'=>'Are you sure? Transaction will be deleted')); ?></li>

							<li><?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $safeTransaction['SafeTransaction']['id']),array('class'=>'action-edit')); ?></li>

						<?php endif; ?>
					</ul>
				</div>	
			</td>
			<td><?php echo $safeTransaction['SafeTransaction']['date']?></td>
			<td><?php echo number_format($safeTransaction['SafeTransaction']['amount'])?> <?php echo $safeTransaction['SafeTransaction']['currency'];?></td>
			<td><a href="<?=$this->webroot;?>safe_transactions/index?transaction_from=<?=$safeTransaction['User']['id']?>"><?php echo ucwords($safeTransaction['User']['name'])?></a></td>
			<td><a href="<?=$this->webroot;?>safe_transactions/index?transaction_to=<?=$safeTransaction['To']['id']?>"><?php echo ucwords($safeTransaction['To']['name'])?></a></td>
			<td><a href="<?=$this->webroot;?>safe_transactions/index?approved_by=<?=$safeTransaction['Approver']['id']?>"><?php echo ucwords($safeTransaction['Approver']['name'])?></a></td>
			<td><?php echo $safeTransaction['SafeTransaction']['transaction_type']?></td>
			<td><?php echo $safeTransaction['SafeTransaction']['status']?></td>
			<td><?php echo $safeTransaction['SafeTransaction']['comment']?></td>
		</tr>
	<?php endforeach; ?>
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
