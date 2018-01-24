<div class="purchasedPurposes form">
<?php echo $this->Form->create('PurchasedPurpose'); ?>
	<fieldset>
		<legend><?php echo __('Add Purchased Purpose'); ?></legend>
	<?php
		echo $this->Form->input('description');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('List Purchased Purposes'), array('action' => 'index')); ?></li>
		<li><?php echo $this->Html->link(__('List Purchased Receipts'), array('controller' => 'purchased_receipts', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Purchased Receipt'), array('controller' => 'purchased_receipts', 'action' => 'add')); ?> </li>
	</ul>
</div>
