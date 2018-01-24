<?php echo $this->Html->script(array('script_dynamic_content'));?>
<div class="flash-message"><?php echo $this->Session->flash(); ?></div>
<div class="cashAtBankUgxes form well" style="float:left;margin-left:30%;width:30%;">
<?php echo $this->Form->create('CashAtBankUgx'); ?>
	<fieldset>
		<legend><?php echo __('Edit Ugx Cash At Bank'); ?></legend>
		<?php if($super_admin):?>
			<?php echo $this->Form->input('user_id'); ?>
		<?php endif; ?>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('amount');
		echo $this->Form->input('bank_name');
		echo $this->Form->input('date');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>