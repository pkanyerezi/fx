<div class="dailySellingReturns view">
<h2><?php  echo __('Daily Selling Return'); ?></h2>
	<dl>
		<dt><?php echo __('Id'); ?></dt>
		<dd>
			<?php echo h($dailySellingReturn['DailySellingReturn']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Fox'); ?></dt>
		<dd>
			<?php echo $this->Html->link($dailySellingReturn['Fox']['name'], array('controller' => 'foxes', 'action' => 'view', $dailySellingReturn['Fox']['id'])); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Daily Return Id'); ?></dt>
		<dd>
			<?php echo h($dailySellingReturn['DailySellingReturn']['daily_return_id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('C1'); ?></dt>
		<dd>
			<?php echo h($dailySellingReturn['DailySellingReturn']['c1']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('C2'); ?></dt>
		<dd>
			<?php echo h($dailySellingReturn['DailySellingReturn']['c2']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('C3'); ?></dt>
		<dd>
			<?php echo h($dailySellingReturn['DailySellingReturn']['c3']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Date'); ?></dt>
		<dd>
			<?php echo h($dailySellingReturn['DailySellingReturn']['date']); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Daily Selling Return'), array('action' => 'edit', $dailySellingReturn['DailySellingReturn']['id'])); ?> </li>
		<li><?php echo $this->Form->postLink(__('Delete Daily Selling Return'), array('action' => 'delete', $dailySellingReturn['DailySellingReturn']['id']), null, __('Are you sure you want to delete # %s?', $dailySellingReturn['DailySellingReturn']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Daily Selling Returns'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Daily Selling Return'), array('action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Foxes'), array('controller' => 'foxes', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Fox'), array('controller' => 'foxes', 'action' => 'add')); ?> </li>
	</ul>
</div>
