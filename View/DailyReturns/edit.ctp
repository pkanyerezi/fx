<div class="dailyReturns form">
<?php echo $this->Form->create('DailyReturn'); ?>
	<fieldset>
		<legend><?php echo __('Edit Daily Return'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('fox_id');
		echo $this->Form->input('date');
		echo $this->Form->input('daily_buying_return_id');
		echo $this->Form->input('daily_selling_return_id');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $this->Form->value('DailyReturn.id')), null, __('Are you sure you want to delete # %s?', $this->Form->value('DailyReturn.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Daily Returns'), array('action' => 'index')); ?></li>
		<li><?php echo $this->Html->link(__('List Foxes'), array('controller' => 'foxes', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Fox'), array('controller' => 'foxes', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Daily Buying Returns'), array('controller' => 'daily_buying_returns', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Daily Buying Return'), array('controller' => 'daily_buying_returns', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Daily Selling Returns'), array('controller' => 'daily_selling_returns', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Daily Selling Return'), array('controller' => 'daily_selling_returns', 'action' => 'add')); ?> </li>
	</ul>
</div>
