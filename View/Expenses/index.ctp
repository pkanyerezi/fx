<?php echo $this->Html->script(array('script_dynamic_content'));?>
<div class="flash-message"><?php echo $this->Session->flash(); ?></div>
<div class="expenses index well btn btn-small" style="text-align:left;cursor:auto;">
	<h2><?php echo __('Expenses'); ?></h2>
	<?php if(isset($from) && isset($to)):?>
		<h6><?php echo 'from '.$from.', to '.$to.' ('.$this->Time->timeAgoInWords($from, array('accuracy' => array('day' => 'day'),'end' => '1 year')).')';?></h6>
	<?php endif; ?>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('item_id'); ?></th>
			<th><?php echo $this->Paginator->sort('amount'); ?></th>
			<th><?php echo $this->Paginator->sort('description'); ?></th>
			<th><?php echo $this->Paginator->sort('date'); ?></th>
			<th><?php echo $this->Paginator->sort('user_id','By'); ?></th>
			<th class="actions"><?php echo __(''); ?></th>
	</tr>
	<?php $total=0;?>
	<?php foreach ($expenses as $expense): ?>
	<tr>
		<td>
			<a class="use-ajax" href="<?php echo $this->webroot;?>expenses/index/<?php echo $expense['Item']['id'];?>?date_from=<?php echo $from;?>&date_to=<?php echo $to;?>"><?php echo $expense['Item']['name'];?></a>
		</td>
		<td><?php echo h($expense['Expense']['amount']);$total+=$expense['Expense']['amount']; ?>&nbsp;</td>
		<td><?php echo h($expense['Expense']['description']); ?>&nbsp;</td>
		<td><?php echo h($expense['Expense']['date']); ?>&nbsp;</td>
		<td><?php echo h($expense['User']['name']); ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $expense['Expense']['id'])); ?>
			<?php if($super_admin):?>
				<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $expense['Expense']['id'])); ?>
				<?php echo $this->Html->link(__('Delete'), array('action' => 'delete', $expense['Expense']['id']),array('class'=>'action-delete confirm-first','data-confirm-text'=>'Are you sure you want to delete it?')); ?>
			<?php endif; ?>
		</td>
	</tr>
<?php endforeach; ?>
	<tr>
		<td style="background:#2c2c2c;color:#fff">Total:</td>
		<td colspan="5" style="background:#2c2c2c;color:#fff" class="ln"><?php echo $total; ?></td>
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
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('New Expense'), array('action' => 'add')); ?></li>
		<li><?php echo $this->Html->link(__('List Items'), array('controller' => 'items', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Item'), array('controller' => 'items', 'action' => 'add')); ?> </li>
	</ul>
</div>
