<?php echo $this->Html->script(array('script_dynamic_content'));?>
<div class="flash-message"><?php echo $this->Session->flash(); ?></div>
<div class="otherCurrencies form well">
<?php echo $this->Form->create('OtherCurrency'); ?>
	<fieldset>
		<legend><?php echo __('Add Other Currency'); ?></legend>
	<?php
		echo $this->Form->input('other_currency',array('label'=>'Currency ID eg. USD,GBP,Kshs'));
		echo $this->Form->input('name',array('label'=>'Name eg. USD,GBP,Kshs'));
		echo $this->Form->input('description',array('label'=>'Description eg. US Dollar, Pound, Kenyan Shs'));
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>