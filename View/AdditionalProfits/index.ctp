<?php echo $this->Html->script(array('script_dynamic_content'));?>
<div class="flash-message"><?php echo $this->Session->flash(); ?></div>
<div class="additionalProfits index well">
	<h2><?php echo __('AdditionalProfit'); ?></h2>
	<?php if(isset($from) && isset($to)):?>
		<h6><?php echo 'from '.$from.', to '.$to.' ('.$this->Time->timeAgoInWords($from, array('accuracy' => array('day' => 'day'),'end' => '1 year')).')';?></h6>
	<?php endif; ?>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('amount'); ?></th>
			<th><?php echo $this->Paginator->sort('additional_info'); ?></th>
			<th><?php echo $this->Paginator->sort('date'); ?></th>
			<th><?php echo $this->Paginator->sort('user_id','Creator'); ?></th>
			<th class="actions"><?php echo __(''); ?></th>
	</tr>
	<?php $total_amount=0;?>
	<?php foreach ($additionalProfits as $additionalProfit): ?>
	<tr>
		<td><?php echo h($additionalProfit['AdditionalProfit']['amount']);$total_amount+=$additionalProfit['AdditionalProfit']['amount']; ?>&nbsp;</td>
		<td><?php echo h($additionalProfit['AdditionalProfit']['additional_info']); ?>&nbsp;</td>
		<td><?php echo h($additionalProfit['AdditionalProfit']['date']); ?>&nbsp;</td>
		<td><?php echo h($additionalProfit['User']['name']); ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $additionalProfit['AdditionalProfit']['id'])); ?>
			<?php if($super_admin): ?>
				<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $additionalProfit['AdditionalProfit']['id'])); ?>
				<?php echo $this->Html->link(__('Delete'), array('action' => 'delete', $additionalProfit['AdditionalProfit']['id']),array('class'=>'action-delete confirm-first','data-confirm-text'=>'Are you sure you want to delete it?')); ?>
			<?php endif; ?>
		</td>
	</tr>
<?php endforeach; ?>
	<tr>
		<td style="background:#2c2c2c;color:#fff">Total:</td>
		<td style="background:#2c2c2c;color:#fff" colspan="2" class="ln"><?php echo $total_amount; ?></td>
		<td style="background:#2c2c2c;color:#fff"></td>
		<td style="background:#2c2c2c;color:#fff"></td>
	</tr>
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
		<li><?php echo $this->Html->link(__('Add AdditionalProfit'), array('action' => 'add')); ?></li>
	</ul>
</div>
