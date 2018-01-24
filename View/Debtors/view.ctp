<?php echo $this->Html->script(array('script_dynamic_content'));?>
<div class="flash-message"><?php echo $this->Session->flash(); ?></div>
<div class="debtors view well">
<h2><?php  echo __('Debtor'); ?></h2>
	<dl>
		<dt><?php echo __('Customer'); ?></dt>
		<dd>
			<?php echo h($debtor['Debtor']['customer']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Amount'); ?></dt>
		<dd>
			<?php echo h($debtor['Debtor']['amount']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Date'); ?></dt>
		<dd>
			<?php echo h($debtor['Debtor']['date']); ?>
			&nbsp;
		</dd>
	</dl>
</div>