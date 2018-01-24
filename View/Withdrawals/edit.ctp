<?php echo $this->Html->script(array('script_dynamic_content'));?>
<div class="flash-message"><?php echo $this->Session->flash(); ?></div>
<div class="Withdrawal form well" style="float:left;margin-left:30%;width:30%;">
<?php echo $this->Form->create('Withdrawal'); ?>
	<fieldset>
		<?php if($customer_details['Customer']['is_bank']):?>
			<legend><?php echo __('Edit Deposit'); ?></legend>
		<?php else:?>
			<legend><?php echo __('Edit Withdrawal'); ?></legend>
		<?php endif;?>
		<?php if($super_admin):?>
			<?php echo $this->Form->input('user_id'); ?>
		<?php endif; ?>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('customer_id');
		echo $this->Form->input('amount');
		if($customer_details['Customer']['is_bank']){
			$reasons = [
				'FromBureau'=>'To '.$customer_details['Customer']['name']. ' from Bureau',
				'ToBank'=>'To '.$customer_details['Customer']['name']. ' as external deposit'
			];
			echo $this->Form->input('reason',['type'=>'select','options'=>$reasons]);
		}
		echo $this->Form->input('additional_info');
	?>
	<label>Date:</label>
	<div class="input-append date" id='dp_cred_edit' data-date="<?php echo $this->Form->value('date'); ?>" data-date-format="yyyy-mm-dd">
		<input style="width:93%;" class="span2" size="16" type="text" id='dp_today_selected' name="data[Withdrawal][date]" value="<?php echo $this->Form->value('date'); ?>">
		<span class="add-on"><i class="icon-th"></i></span>
	</div>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<script>
	$(document).ready(function(){
		$('#WithdrawalReason').change(function(){
			var info = $(this).find(':selected').text();
			$('#WithdrawalAdditionalInfo').val(info);
		});
	
		$('#dp_cbugx').datepicker({
			format: 'yyyy-mm-dd'
		});
	});
</script>	