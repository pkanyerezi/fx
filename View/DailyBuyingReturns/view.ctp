<div class="dailyBuyingReturns view">
<h2><?php  echo __('Daily Buying Return'); ?></h2>
	<dl>
		<dt><?php echo __('Id'); ?></dt>
		<dd>
			<?php echo h($dailyBuyingReturn['DailyBuyingReturn']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Fox'); ?></dt>
		<dd>
			<?php echo $this->Html->link($dailyBuyingReturn['Fox']['name'], array('controller' => 'foxes', 'action' => 'view', $dailyBuyingReturn['Fox']['id'])); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Daily Return Id'); ?></dt>
		<dd>
			<?php echo h($dailyBuyingReturn['DailyBuyingReturn']['daily_return_id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('C1'); ?></dt>
		<dd>
			<?php echo h($dailyBuyingReturn['DailyBuyingReturn']['c1']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('C2'); ?></dt>
		<dd>
			<?php echo h($dailyBuyingReturn['DailyBuyingReturn']['c2']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('C3'); ?></dt>
		<dd>
			<?php echo h($dailyBuyingReturn['DailyBuyingReturn']['c3']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Date'); ?></dt>
		<dd>
			<?php echo h($dailyBuyingReturn['DailyBuyingReturn']['date']); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Daily Buying Return'), array('action' => 'edit', $dailyBuyingReturn['DailyBuyingReturn']['id'])); ?> </li>
		<li><?php echo $this->Form->postLink(__('Delete Daily Buying Return'), array('action' => 'delete', $dailyBuyingReturn['DailyBuyingReturn']['id']), null, __('Are you sure you want to delete # %s?', $dailyBuyingReturn['DailyBuyingReturn']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Daily Buying Returns'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Daily Buying Return'), array('action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Foxes'), array('controller' => 'foxes', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Fox'), array('controller' => 'foxes', 'action' => 'add')); ?> </li>
	</ul>
</div>
