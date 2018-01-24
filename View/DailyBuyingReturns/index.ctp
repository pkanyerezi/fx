<div class="dailyBuyingReturns index">
	<h2><?php echo __('Daily Buying Returns'); ?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('id'); ?></th>
			<th><?php echo $this->Paginator->sort('fox_id'); ?></th>
			<th><?php echo $this->Paginator->sort('daily_return_id'); ?></th>
			<th><?php echo $this->Paginator->sort('c1'); ?></th>
			<th><?php echo $this->Paginator->sort('c2'); ?></th>
			<th><?php echo $this->Paginator->sort('c3'); ?></th>
			<th><?php echo $this->Paginator->sort('date'); ?></th>
			<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php foreach ($dailyBuyingReturns as $dailyBuyingReturn): ?>
	<tr>
		<td><?php echo h($dailyBuyingReturn['DailyBuyingReturn']['id']); ?>&nbsp;</td>
		<td>
			<?php echo $this->Html->link($dailyBuyingReturn['Fox']['name'], array('controller' => 'foxes', 'action' => 'view', $dailyBuyingReturn['Fox']['id'])); ?>
		</td>
		<td><?php echo h($dailyBuyingReturn['DailyBuyingReturn']['daily_return_id']); ?>&nbsp;</td>
		<td><?php echo h($dailyBuyingReturn['DailyBuyingReturn']['c1']); ?>&nbsp;</td>
		<td><?php echo h($dailyBuyingReturn['DailyBuyingReturn']['c2']); ?>&nbsp;</td>
		<td><?php echo h($dailyBuyingReturn['DailyBuyingReturn']['c3']); ?>&nbsp;</td>
		<td><?php echo h($dailyBuyingReturn['DailyBuyingReturn']['date']); ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $dailyBuyingReturn['DailyBuyingReturn']['id'])); ?>
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $dailyBuyingReturn['DailyBuyingReturn']['id'])); ?>
			<?php echo $this->Html->link(__('Delete'), array('action' => 'delete', $dailyBuyingReturn['DailyBuyingReturn']['id']),array('class'=>'action-delete confirm-first','data-confirm-text'=>'Are you sure you want to delete it?')); ?>
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
		<li><?php echo $this->Html->link(__('New Daily Buying Return'), array('action' => 'add')); ?></li>
		<li><?php echo $this->Html->link(__('List Foxes'), array('controller' => 'foxes', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Fox'), array('controller' => 'foxes', 'action' => 'add')); ?> </li>
	</ul>
</div>
