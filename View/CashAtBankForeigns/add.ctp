<?php echo $this->Html->script(array('script_dynamic_content'));?>
<div class="flash-message"><?php echo $this->Session->flash(); ?></div>
<div class="cashAtBankForeigns form  well" style="float:left;margin-left:30%;width:30%;">
<?php echo $this->Form->create('CashAtBankForeign'); ?>
	<fieldset>
		<legend><?php echo __('Add Foreign Cash At Bank'); ?></legend>
		<?php if($super_admin):?>
			<?php echo $this->Form->input('user_id'); ?>
		<?php endif; ?>
	<?php
		echo $this->Form->input('currency_id');
		echo $this->Form->input('amount');
		echo $this->Form->input('bank_name');
	?>
	<label>Date:</label>
	<div class="input-append date" id='dp_cbf' data-date="<?php echo date('Y-m-d'); ?>" data-date-format="yyyy-mm-dd">
		<input style="width:93%;" class="span2" size="16" type="text" id='dp_today_selected' name="data[CashAtBankForeign][date]" value="<?php echo date('Y-m-d'); ?>">
		<span class="add-on"><i class="icon-th"></i></span>
	</div>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>

<script>
	$(document).ready(function(){
		$('#dp_cbf').datepicker({
			format: 'yyyy-mm-dd'
		});
	});
</script>	