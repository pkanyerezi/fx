<?php echo $this->Html->script(array('script_dynamic_content'));?>
<div class="flash-message"><?php echo $this->Session->flash(); ?></div>
<div class="additionalProfits view well">
<h2><?php  echo __('Additional Profit'); ?></h2>
	<dl>
		<dt><?php echo __('Amount'); ?></dt>
		<dd>
			<?php echo h($additionalProfit['AdditionalProfit']['amount']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Additional Info'); ?></dt>
		<dd>
			<?php echo h($additionalProfit['AdditionalProfit']['additional_info']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Date'); ?></dt>
		<dd>
			<?php echo h($additionalProfit['AdditionalProfit']['date']); ?>
			&nbsp;
		</dd>
	</dl>
</div>