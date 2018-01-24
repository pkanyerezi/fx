<div class="dailySellingReturns form">
<?php echo $this->Form->create('DailySellingReturn'); ?>
	<fieldset>
		<legend><?php echo __('Edit Daily Selling Return'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('fox_id');
		echo $this->Form->input('daily_return_id');
		echo $this->Form->input('c1');
		echo $this->Form->input('c2');
		echo $this->Form->input('c3');
		echo $this->Form->input('date');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $this->Form->value('DailySellingReturn.id')), null, __('Are you sure you want to delete # %s?', $this->Form->value('DailySellingReturn.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Daily Selling Returns'), array('action' => 'index')); ?></li>
		<li><?php echo $this->Html->link(__('List Foxes'), array('controller' => 'foxes', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Fox'), array('controller' => 'foxes', 'action' => 'add')); ?> </li>
	</ul>
</div>
