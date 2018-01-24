<?php echo $this->Html->script(array('script_dynamic_content'));?>
<div class="flash-message"><?php echo $this->Session->flash(); ?></div>
<div class="cashAtBankUgxes view well">
<h2><?php  echo __('Cash At Bank Ugx'); ?></h2>
	<dl>
		<dt><?php echo __('Amount'); ?></dt>
		<dd>
			<?php echo h($cashAtBankUgx['CashAtBankUgx']['amount']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Bank Name'); ?></dt>
		<dd>
			<?php echo h($cashAtBankUgx['CashAtBankUgx']['bank_name']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Date'); ?></dt>
		<dd>
			<?php echo h($cashAtBankUgx['CashAtBankUgx']['date']); ?>
			&nbsp;
		</dd>
	</dl>
</div>