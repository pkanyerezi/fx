<?php echo $this->Html->script(array('script_dynamic_content'));?>
<div class="flash-message"><?php echo $this->Session->flash(); ?></div>
<div class="contacts form well" style="border-left:none;width:82%;border-radius: 14px;border: px solid #ddd;display: block !important;box-shadow: 4px 4px #DDD;">
<?php echo $this->Form->create('Contact'); ?>
	<fieldset>
	<h6>(<?php echo $contacts_found; ?>) Contacts found in the contact list.</h6>
	<?php
		echo $this->Form->input('Message',array('name'=>'data[SMS][msg]','type'=>'textarea'));
	?>
	</fieldset>
<?php echo $this->Form->end(__('send')); ?>
</div>
