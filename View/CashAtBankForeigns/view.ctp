<?php echo $this->Html->script(array('script_dynamic_content'));?>
<div class="flash-message"><?php echo $this->Session->flash(); ?></div>
<div class="cashAtBankForeigns view well">
<h2><?php  echo __('Cash At Bank Foreign'); ?></h2>
	<dl>
		<dt><?php echo __('Amount'); ?></dt>
		<dd>
			<?php echo h($cashAtBankForeign['CashAtBankForeign']['amount']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Bank Name'); ?></dt>
		<dd>
			<?php echo h($cashAtBankForeign['CashAtBankForeign']['bank_name']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Currency'); ?></dt>
		<dd>
			<?php echo $this->Html->link($cashAtBankForeign['Currency']['description'], array('controller' => 'currencies', 'action' => 'view', $cashAtBankForeign['Currency']['id'])); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Date'); ?></dt>
		<dd>
			<?php echo h($cashAtBankForeign['CashAtBankForeign']['date']); ?>
			&nbsp;
		</dd>
	</dl>
</div>