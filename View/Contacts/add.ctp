<?php echo $this->Html->script(array('script_dynamic_content'));?>
<div class="flash-message"><?php echo $this->Session->flash(); ?></div>
<div class="contacts well form" style="border-left:none;width:82%;border-radius: 14px;border: px solid #ddd;display: block !important;box-shadow: 4px 4px #DDD;">
<?php echo $this->Form->create('Contact'); ?>
	<fieldset>
		<legend><?php echo __('Add Contact'); ?></legend>
		<table>
			<tr>
				<td><?php echo $this->Form->input('contact_list_id'); ?></td>	
				<td><?php echo $this->Form->input('name'); ?></td>
				<td><?php echo $this->Form->input('phone_number',array('class'=>'amount','label'=>'Phone','placeholder'=>'eg. 256704543171')); ?></td>
				<td><?php echo $this->Form->input('email',array('class'=>'rate')); ?></td>
			</tr>
		</table>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>