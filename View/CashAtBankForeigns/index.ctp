<?php echo $this->Html->script(array('script_dynamic_content'));?>
<div class="flash-message"><?php echo $this->Session->flash(); ?></div>
<div class="cashAtBankForeigns index well">
	<h2><?php echo __((($type=='deposit')?'Deposited':'Withdrawn').' Foreign Bank Cash'); ?></h2>
	<?php if(isset($from) && isset($to)):?>
		<h6><?php echo 'from '.$from.', to '.$to.' ('.$this->Time->timeAgoInWords($from, array('accuracy' => array('day' => 'day'),'end' => '1 year')).')';?></h6>
	<?php endif; ?>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('amount'); ?></th>
			<th><?php echo $this->Paginator->sort('bank_id'); ?></th>
			<th><?php echo $this->Paginator->sort('currency_id'); ?></th>
			<th><?php echo $this->Paginator->sort('date'); ?></th>
			<th><?php echo $this->Paginator->sort('user_id','Creator'); ?></th>
			<th class="actions"><?php echo __(''); ?></th>
	</tr>
	<?php foreach ($cashAtBankForeigns as $cashAtBankForeign): ?>
	<tr>
		<td><?php echo abs(h($cashAtBankForeign['CashAtBankForeign']['amount'])); ?>&nbsp;</td>
		<td><?php echo h($cashAtBankForeign['Bank']['name']); ?>&nbsp;</td>
		<td>
			<?php echo $this->Html->link($cashAtBankForeign['Currency']['description'], array('controller' => 'currencies', 'action' => 'view', $cashAtBankForeign['Currency']['id'])); ?>
		</td>
		<td><?php echo h($cashAtBankForeign['CashAtBankForeign']['date']); ?>&nbsp;</td>
		<td><?php echo h($cashAtBankForeign['User']['name']); ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $cashAtBankForeign['CashAtBankForeign']['id'])); ?>
			<?php if($super_admin): ?>
				<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $cashAtBankForeign['CashAtBankForeign']['id'])); ?>
				<?php echo $this->Html->link(__('Delete'), array('action' => 'delete', $cashAtBankForeign['CashAtBankForeign']['id']),array('class'=>'action-delete confirm-first','data-confirm-text'=>'Are you sure you want to delete it?')); ?>
			<?php endif; ?>
		</td>		
	</tr>
<?php endforeach; ?>
	</table>
	<p>
	<?php
	echo $this->Paginator->counter(array(
	'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
	));
	?>	</p>
	<div class="paging">
	<?php
		echo $this->Paginator->prev('< ' . __('previous'), array(), null, array('class' => 'prev disabled'));
		echo $this->Paginator->numbers(array('separator' => ''));
		echo $this->Paginator->next(__('next') . ' >', array(), null, array('class' => 'next disabled'));
	?>
	</div>
</div>
<div class="actions">
	<h3><?php echo __(''); ?></h3>
	<ul>
		<?php if($type=='deposit'):?>
			<li><?php echo $this->Html->link(__('Add Foreign deposited'), array('action' => 'deposited_to_bank')); ?></li>
		<?php else: ?>
			<li><?php echo $this->Html->link(__('Add Foreign withdrawn'), array('action' => 'withdrawn_from_bank')); ?></li>
		<?php endif;?>
	</ul>
</div>
