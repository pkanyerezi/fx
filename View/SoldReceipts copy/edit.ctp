<style>
	select, textarea, input, .uneditable-input {
		height: 25px;
		padding: -1px -1px;
		margin-bottom: 10px;
		font-size: 11px;
	}
	.well{
		border-radius: 14px;border: px solid #ddd;display: block !important;box-shadow: 4px 4px #DDD;
		width:80%;
	}
	form div {
		clear: both;
		margin-bottom: -23px;
		padding: .5em;
		vertical-align: text-top;
	}
	
	form .submit input[type=submit] {
		border-color: #eee;
		text-shadow: rgba(0, 0, 0, 0.5) 0px -1px 0px;
		padding: 0px 10px;
		float: right;
	}
	
	table tr td {
		border-bottom: 1px solid #f3f4f5;
	}
	table tr:nth-child(even) {
		background: none;
	}
</style>
<?php echo $this->Html->script(array('script_dynamic_content'));?>
<div class="flash-message"><?php echo $this->Session->flash(); ?></div>
<div class="soldReceipts form" style="border-left:none;width:82%">
<?php echo $this->Form->create('SoldReceipt'); ?>
	<fieldset>
		<legend><?php echo __('Edit Sold Receipt'); ?></legend>
		<?php echo $this->Form->input('id'); ?>
		<?php if($super_admin):?>
			<table class="well">
				<tr>
					<td valign="center" colspan="3">
						<?php echo $this->Form->input('user_id'); ?>
					</td>
				</tr>			
			<table>
		<?php endif; ?>
		
		<table class="well">
			<tr>
				<td>
					<div class="input select required"><label for="SoldReceiptInstrument">Instrument</label>
						<select name="data[SoldReceipt][instrument]" id="SoldReceiptInstrument">
							<option value="Cash">Cash</option>
							<option value="Cheque">Cheque</option>
						</select>
					</div>
				</td>
			</tr>			
		<table>
		
		<table class="well">
				<!--<tr>
					<td><?php echo $this->Form->input('currency_id',array('class'=>'my_currencies')); ?>
					<div class="my_oh_my"><div class="other_name"><label>Other name</label><input name="data[SoldReceipt][other_name]" style="font-size: 10px;" maxlength="10" class="OtherNameValue" type="text" id="SoldReceiptOtherName" value="<?php echo $this->Form->value('other_name');?>" required="" /><br/></div></div>
					</td>			
					<td><?php echo $this->Form->input('purpose_id',array('label'=>'Purpose of transaction')); ?></td>
				</tr>-->
				<tr>
					<td>
					<?php echo $this->Form->input('currency_id',array('class'=>'my_currencies')); ?>
					<div class="my_oh_my"><?php echo $this->Form->input('other_currency_id',array('class'=>'other_name','options'=>$other_currencies)); ?></div>
					<!--<div class="my_oh_my"><div class="other_name"><label>Other name</label><input class="OtherNameValue" name="data[SoldReceipt][other_name]" style="font-size: 10px;" maxlength="10" type="text" id="SoldReceiptOtherName" required="" /><br/></div></div>-->
					</td>			
					<td><?php echo $this->Form->input('purpose_id',array('label'=>'Purpose of transaction')); ?></td>
				</tr>
				<tr>
					<td><?php echo $this->Form->input('customer_name',array('style'=>'font-size: 10px;')); ?></td>
					<td>
						<?php if($this->Form->value('currency_id')=='c8'): ?>
							<?php echo $this->Form->input('amount',array('class'=>'amount','style'=>'font-size: 10px;','value'=>$this->Form->value('orig_amount'))); ?>
						<?php else:?>
							<?php echo $this->Form->input('amount',array('class'=>'amount','style'=>'font-size: 10px;')); ?>
						<?php endif; ?>
					</td>
				</tr>
				<tr>
					<td>
						<?php if($this->Form->value('currency_id')=='c8'): ?>
							<?php echo $this->Form->input('rate',array('class'=>'rate','style'=>'font-size: 10px;','value'=>$this->Form->value('orig_rate'))); ?>
						<?php else:?>
							<?php echo $this->Form->input('rate',array('class'=>'rate','style'=>'font-size: 10px;')); ?>
						<?php endif; ?>
					<td><?php echo $this->Form->input('amount_ugx',array('class'=>'amount_ugx','readonly'=>'true','style'=>'font-size: 10px;')); ?></td>					
				</tr>
				
				<tr>
					<td><?php echo $this->Form->input('passport_number',array('style'=>'font-size: 10px;')); ?></td>				
					<td><?php echo $this->Form->input('nationality',array('style'=>'font-size: 10px;')); ?></td>
				</tr>
				<tr>
					<td colspan="<?php echo ($super_admin)? 1:2 ?>"><?php echo $this->Form->input('address',array('style'=>'font-size: 10px;height:'.(($super_admin)? "20px;":"30px;"),'type'=>'textarea')); ?></td>
					<td colspan="3">	
						<?php if($super_admin): ?>		
								<?php echo $this->Form->input('date'); ?>
						<?php else: ?>
								<?php echo $this->Form->input('date',array('type'=>'hidden')); ?>
						<?php endif;?>
					</td>
				</tr>
		</table>		
	</fieldset>
	<div id="test-group" class="input-prepend btn-group" data-toggle="buttons-radio" data-toggle-name="testOption">
		<input type="hidden" name="data[SoldReceipt][print]" value="dont_print"/>
		<button type="button" class="btn" data-toggle-value="print">Print</button>
		<button type="button" class="btn active" data-toggle-value="dont_print">Dont' print</button>
	</div>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
</div>

<script>
	$(document).ready(function(){
	
		$('#test-group button').click(function(){
			$('#test-group input').val($(this).data("toggle-value"));
		});
		
		$('.generate-rid').click(function(){
			$.ajax({
				url: "<?php echo $this->webroot;?>m/get_receipt_number.php",
				data: {'company_id':<?php echo Configure::read('foxId');?>,'receipt_type':0},
				success: function(data){
					$('.sold_receipt_number').val(data);
				}
			});
		});
		
		$('.amount, .amount_ugx, .rate').change(function(){
			var amount=$('.amount').val();
			var rate =$('.rate').val();
			if(amount>0 && rate>0)
				$('.amount_ugx').val(amount*rate);
		});
		
		
		var my_oh_my=null;
		var OtherNameValue=null;
		
		if($('.my_currencies').val()=='c8'){
			$('.my_oh_my').show();
			OtherNameValue=$('.OtherNameValue').val();
		}else{
			my_oh_my=$('.my_oh_my').html();
			$('.my_oh_my').html('');			
			$('.my_oh_my').hide();
		}
		
		$('.my_currencies').change(function(){
			
			if($(this).val()=='c8'){
				$('.my_oh_my').show();
				$('.my_oh_my').html(my_oh_my);
				$('.OtherNameValue').val(OtherNameValue);
			}else{
				$('.my_oh_my').html(my_oh_my);
				my_oh_my=$('.my_oh_my').html();
				OtherNameValue=$('.OtherNameValue').val();
				$('.my_oh_my').html('');
				$('.my_oh_my').hide();
			}			
		});

	});
</script>

