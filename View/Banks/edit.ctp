<?php echo $this->Html->script(array('script_dynamic_content'));?>
<div class="flash-message"><?php echo $this->Session->flash(); ?></div>
<div class="banks form well">
<?php echo $this->Form->create('Bank'); ?>
	<fieldset>
		<legend><?php echo __('Edit Bank'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('name');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __(''); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('List Banks'), array('action' => 'index')); ?></li>
	</ul>
</div>
