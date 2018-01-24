<?php echo $this->Html->script(array('script_dynamic_content'));?>
<div class="flash-message"><?php echo $this->Session->flash(); ?></div>
<div class="receivables view well">
<h2><?php  echo __('Deposit'); ?></h2>
	<dl>
		<dt><?php echo __('Customer'); ?></dt>
		<dd>
			<?php echo h($receivable['Receivable']['customer']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Amount'); ?></dt>
		<dd>
			<?php echo h($receivable['Receivable']['amount']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Date'); ?></dt>
		<dd>
			<?php echo h($receivable['Receivable']['date']); ?>
			&nbsp;
		</dd>
	</dl>
</div>