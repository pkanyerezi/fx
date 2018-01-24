<?php echo $this->Html->script(array('script_dynamic_content'));?>
<div class="flash-message"><?php echo $this->Session->flash(); ?></div>
<div class="contacts well form" style="border-left:none;width:82%;border-radius: 14px;border: px solid #ddd;display: block !important;box-shadow: 4px 4px #DDD;">
<?php echo $this->Form->create('Asset'); ?>
	<fieldset>
		<legend><?php echo __('Add Asset'); ?></legend>
		<table>
			<tr>
				<td><?php echo $this->Form->input('asset_name_id'); ?></td>
				<td><?php echo $this->Form->input('amount',array('label'=>'Amount(UGX)')); ?></td>
			</tr>
			<tr>
				<td colspan="2"><?php echo $this->Form->input('date'); ?></td>
			</tr>
		</table>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>