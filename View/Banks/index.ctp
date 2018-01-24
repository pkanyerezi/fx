<?php echo $this->Html->script(array('script_dynamic_content'));?>
<div class="flash-message"><?php echo $this->Session->flash(); ?></div>
<div class="banks index well">
	<h2><?php echo __('Banks'); ?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('name'); ?></th>
			<th class="actions"><?php echo __(' '); ?></th>
	</tr>
	<?php foreach ($banks as $bank): ?>
	<tr>
		<td><?php echo h($bank['Bank']['name']); ?>&nbsp;</td>
		<td class="actions">
			<?php if($super_admin): ?>
				<?php echo $this->Html->link(__('UGX DEPOSITED'), array('controller'=>'cash_at_bank_ugxes','action' => 'index','deposit', $bank['Bank']['id'])); ?>
				<?php echo $this->Html->link(__('UGX WITHDRAWN'), array('controller'=>'cash_at_bank_ugxes','action' => 'index','withdraw', $bank['Bank']['id'])); ?>
				<?php echo $this->Html->link(__('Foreign DEPOSITED'), array('controller'=>'cash_at_bank_foreigns','action' => 'index','deposit', $bank['Bank']['id'])); ?>
				<?php echo $this->Html->link(__('Foreign WITHDRAWN'), array('controller'=>'cash_at_bank_foreigns','action' => 'index','withdraw', $bank['Bank']['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $bank['Bank']['id'])); ?>
				<?php echo $this->Html->link(__('Delete'), array('action' => 'delete', $bank['Bank']['id']),array('class'=>'action-delete confirm-first','data-confirm-text'=>'Are you sure you want to delete it?')); ?>
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
		<li><?php echo $this->Html->link(__('New Bank'), array('action' => 'add')); ?></li>
	</ul>
</div>
