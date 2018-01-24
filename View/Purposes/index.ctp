<?php echo $this->Html->script(array('script_dynamic_content'));?>
<div class="flash-message"><?php echo $this->Session->flash(); ?></div>
<div class="purposes index">
	<h2><?php echo __('Purposes'); ?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('id'); ?></th>
			<th><?php echo $this->Paginator->sort('description'); ?></th>
			<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php foreach ($purposes as $purpose): ?>
	<tr>
		<td><?php echo h($purpose['Purpose']['id']); ?>&nbsp;</td>
		<td><?php echo h($purpose['Purpose']['description']); ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $purpose['Purpose']['id'])); ?>
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $purpose['Purpose']['id'])); ?>
			<?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $purpose['Purpose']['id']), null, __('Are you sure you want to delete # %s?', $purpose['Purpose']['id'])); ?>
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
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('New Purpose'), array('action' => 'add')); ?></li>
		<li><?php echo $this->Html->link(__('List Purchased Receipts'), array('controller' => 'purchased_receipts', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Purchased Receipt'), array('controller' => 'purchased_receipts', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Sold Receipts'), array('controller' => 'sold_receipts', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Sold Receipt'), array('controller' => 'sold_receipts', 'action' => 'add')); ?> </li>
	</ul>
</div>
