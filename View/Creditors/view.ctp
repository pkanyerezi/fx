<?php echo $this->Html->script(array('script_dynamic_content'));?>
<div class="flash-message"><?php echo $this->Session->flash(); ?></div>
<div class="creditors view well">
<h2><?php  echo __('Creditor'); ?></h2>
	<dl>
		<dt><?php echo __('Customer'); ?></dt>
		<dd>
			<?php echo h($creditor['Creditor']['customer']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Amount'); ?></dt>
		<dd>
			<?php echo h($creditor['Creditor']['amount']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Date'); ?></dt>
		<dd>
			<?php echo h($creditor['Creditor']['date']); ?>
			&nbsp;
		</dd>
	</dl>
</div>