<?php echo $this->Html->script(array('script_dynamic_content'));?>
<div class="flash-message"><?php echo $this->Session->flash(); ?></div>
<div class="expenses view well">
<h2><?php  echo __('Expense'); ?></h2>
	<dl>
		<dt><?php echo __('Item'); ?></dt>
		<dd>
			<?php echo $this->Html->link($expense['Item']['name'], array('controller' => 'items', 'action' => 'view', $expense['Item']['id'])); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Amount'); ?></dt>
		<dd>
			<?php echo h($expense['Expense']['amount']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Date'); ?></dt>
		<dd>
			<?php echo h($expense['Expense']['date']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Description'); ?></dt>
		<dd>
			<?php echo h($expense['Expense']['description']); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __(''); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Expense'), array('action' => 'edit', $expense['Expense']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Expenses'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Expense'), array('action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Items'), array('controller' => 'items', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Item'), array('controller' => 'items', 'action' => 'add')); ?> </li>
	</ul>
</div>
