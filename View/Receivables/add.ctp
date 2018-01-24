<?php echo $this->Html->script(array('script_dynamic_content'));?>
<div class="flash-message"><?php echo $this->Session->flash(); ?></div>
<div class="receivables form well" style="float:left;margin-left:30%;width:30%;">
<?php echo $this->Form->create('Receivable'); ?>
	<fieldset>
		<?php if($customer_details['Customer']['is_bank']):?>
			<legend><?php echo __('Add Withdraw'); ?></legend>
		<?php else:?>
			<legend><?php echo __('Add Deposit'); ?></legend>
		<?php endif;?>
		<?php if($super_admin):?>
			<?php echo $this->Form->input('user_id'); ?>
		<?php endif; ?>
	<?php
		echo $this->Form->input('customer_id');
		echo $this->Form->input('amount');
		$comment = '';
		if($customer_details['Customer']['is_bank']){
			$reasons = [
				'ToBureau'=>'From '.$customer_details['Customer']['name'] . ' To Bureau',
				'FromBank'=>'From '.$customer_details['Customer']['name']. ' as expense'
			];
			echo $this->Form->input('reason',['type'=>'select','options'=>$reasons]);
			$comment = 'To Bureau from '.$customer_details['Customer']['name'];
		}
		echo $this->Form->input('additional_info',['value'=>$comment]);
	?>
	<label>Date:</label>
	<div class="input-append date" id='dp_cred' data-date="<?php echo date('Y-m-d'); ?>" data-date-format="yyyy-mm-dd">
		<input style="width:93%;" class="span2" size="16" type="text" id='dp_today_selected' name="data[Receivable][date]" value="<?php echo date('Y-m-d'); ?>">
		<span class="add-on"><i class="icon-th"></i></span>
	</div>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<script>
	$(document).ready(function(){
		$('#ReceivableReason').change(function(){
			var info = $(this).find(':selected').text();
			$('#ReceivableAdditionalInfo').val(info);
		});
		$('#dp_cred').datepicker({
			format: 'yyyy-mm-dd'
		});
	});
</script>
