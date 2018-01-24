<?php echo $this->Html->script(array('script_dynamic_content'));?>
<div class="flash-message"><?php echo $this->Session->flash(); ?></div>
<div class="currencies form">
<?php echo $this->Form->create('Currency'); ?>
	<fieldset>
		<legend><?php echo __('Edit Currency'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('description');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $this->Form->value('Currency.id')), null, __('Are you sure you want to delete # %s?', $this->Form->value('Currency.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Currencies'), array('action' => 'index')); ?></li>
		<li><?php echo $this->Html->link(__('List Purchased Receipts'), array('controller' => 'purchased_receipts', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Purchased Receipt'), array('controller' => 'purchased_receipts', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Sold Receipts'), array('controller' => 'sold_receipts', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Sold Receipt'), array('controller' => 'sold_receipts', 'action' => 'add')); ?> </li>
	</ul>
</div>
