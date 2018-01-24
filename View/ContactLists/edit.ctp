<?php echo $this->Html->script(array('script_dynamic_content'));?>
<div class="flash-message"><?php echo $this->Session->flash(); ?></div>
<div class="contactLists form well" style="border-left:none;width:82%;border-radius: 14px;border: px solid #ddd;display: block !important;box-shadow: 4px 4px #DDD;">
<?php echo $this->Form->create('ContactList'); ?>
	<fieldset>
		<legend><?php echo __('Edit Contact List'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('name');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>