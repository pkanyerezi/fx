<?php echo $this->Html->script(array('script_dynamic_content'));?>
<div class="flash-message"><?php echo $this->Session->flash(); ?></div>
<div class="cashAtBankForeigns form  well" style="float:left;margin-left:30%;width:30%;">
<?php echo $this->Form->create('CashAtBankForeign'); ?>
	<fieldset>
		<legend><?php echo __('Edit Foreign Cash At Bank'); ?></legend>
		<?php if($super_admin):?>
			<?php echo $this->Form->input('user_id'); ?>
		<?php endif; ?>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('currency_id');
		echo $this->Form->input('amount');
		echo $this->Form->input('bank_name');
		echo $this->Form->input('date');
	?><label>Date:</label>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>