<?php echo $this->Html->script(array('script_dynamic_content'));?>
<div class="flash-message"><?php echo $this->Session->flash(); ?></div>
<div class="currencies form">
<?php echo $this->Form->create('Currency'); ?>
	
		<div class="well">
		<h5><?php echo __($oldCurrency['OtherCurrency']['name'] . ' - Upgrade Receipt Currencies from ' . $oldCurrency['OtherCurrency']['name']); ?></h5>
		<small><?=$soldReceipts?> SalesReceipts and <?=$purchasedReceipts?> Purchase Receipts to be converted</small>
		</div>
		<div class="alert alert-error">Please Make a database backup Before you continue!!!</div>
	<?php
		echo $this->Form->input('currency_id',['label'=>'Convert Receipts to','class'=>'form-control']);
	?>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('List Currencies'), array('action' => 'index')); ?></li>
		<li><?php echo $this->Html->link(__('List Other Currencies'), array('action' => 'index','controller'=>'OtherCurrencies')); ?></li>
	</ul>
</div>
