<?php echo $this->Html->script(array('script_dynamic_content'));?>
<div class="flash-message"><?php echo $this->Session->flash(); ?></div>
<div class="withdrawals view well">
<h2><?php  echo __('Withdrawal'); ?></h2>
	<dl>
		<dt><?php echo __('Customer'); ?></dt>
		<dd>
			<?php echo h($withdrawal['Withdrawal']['customer']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Amount'); ?></dt>
		<dd>
			<?php echo h($withdrawal['Withdrawal']['amount']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Additional info'); ?></dt>
		<dd>
			<?php echo h($withdrawal['Withdrawal']['additional_info']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Date'); ?></dt>
		<dd>
			<?php echo h($withdrawal['Withdrawal']['date']); ?>
			&nbsp;
		</dd>
	</dl>
</div>