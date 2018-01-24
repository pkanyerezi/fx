<?php echo $this->Html->script(array('script_dynamic_content'));?>
<div class="flash-message"><?php echo $this->Session->flash(); ?></div>
<div class="currencies form">
<?php echo $this->Form->create('Currency'); ?>	
	<div class="alert alert-error">Please Make a database backup Before you continue!!!</div>
	<?php foreach($otherCurrencies as $key=>$otherCurrency): ?>
		<?php echo $this->Form->input('currency_from',['name'=>'data[Currency][currency_from][]','value'=>$key,'label'=>'FromCurrency ' . $otherCurrency,'class'=>'form-control']);?>
		<?php echo $this->Form->input('currency_id',['name'=>'data[Currency][currency_to][]','label'=>'ToCurrency','value'=>$key,'class'=>'form-control']);?>
		<hr>
	<?php endforeach;?>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('List Currencies'), array('action' => 'index')); ?></li>
		<li><?php echo $this->Html->link(__('List Other Currencies'), array('action' => 'index','controller'=>'OtherCurrencies')); ?></li>
	</ul>
</div>
