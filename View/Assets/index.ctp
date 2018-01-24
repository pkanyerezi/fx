<?php echo $this->Html->script(array('script_dynamic_content'));?>
<div class="flash-message"><?php echo $this->Session->flash(); ?></div>
<div class="contacts index well well-new-small">
	<h2><?php echo __('Assets'); ?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('date'); ?></th>
			<th><?php echo $this->Paginator->sort('name'); ?></th>
			<th><?php echo $this->Paginator->sort('amount'); ?></th>
			<th class="actions"><?php echo __(' '); ?></th>
	</tr>
	<?php foreach ($assets as $asset): ?>
	<tr>
		<td><?php echo h($asset['Asset']['date']); ?>&nbsp;</td>
		<td><?php echo h($asset['AssetName']['name']); ?>&nbsp;</td>
		<td><?php echo h($asset['Asset']['amount']); ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $asset['Asset']['id'])); ?>
			<?php if($super_admin): ?>
				<?php echo $this->Html->link(__('Delete'), array('action' => 'delete', $asset['Asset']['id']),array('class'=>'action-delete confirm-first confirm-first','data-confirm-text'=>'Are you sure you want to delete this asset record?')); ?>
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