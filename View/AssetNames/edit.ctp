<?php echo $this->Html->script(array('script_dynamic_content'));?>
<div class="flash-message"><?php echo $this->Session->flash(); ?></div>
<div class="contacts well form" style="border-left:none;width:82%;border-radius: 14px;border: px solid #ddd;display: block !important;box-shadow: 4px 4px #DDD;">
<?php echo $this->Form->create('AssetName'); ?>
	<fieldset>
		<legend><?php echo __('Edit Asset'); ?></legend>
		<table>
			<tr>
				<td>
					<?php echo $this->Form->input('id'); ?>
					<?php echo $this->Form->input('name'); ?>
					<td><?php echo $this->Form->input('asset_type',array('label'=>'Fixed/Current','type'=>'select','options'=>array('Fixed'=>'Fixed','Current'=>'Current'))); ?></td>
				</td>	
			</tr>
		</table>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>