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
	#PurchasedReceiptDateMonth,#PurchasedReceiptDateDay,#PurchasedReceiptDateYear{width: 80px;}
</style>
<?php echo $this->Html->script(array('script_dynamic_content'));?>
<?php
	$_fox=($this->Session->read('fox'));
	@$use_system_board_rates = $_fox['Fox']['use_system_board_rates'];
?>
<style type="text/css">
	<?php if($use_system_board_rates):?>
		.large_cash_detail{display: none;}
	<?php else:?>
		#large_cash_detail_link{display: none;}
	<?php endif;?>
</style>
<div class="flash-message"><?php echo $this->Session->flash(); ?></div>
<div class="soldReceipts form btn btn-small" style="border-left:none;width:82%;text-align:left;cursor:auto;">
<?php echo $this->Form->create('PurchasedReceipt'); ?>
	<fieldset>
		<legend><?php echo __('Edit Purchase Receipt'); ?></legend>
		<?php echo $this->Form->input('id'); ?>
		<table class="well">
			<?php if($super_admin):?>
				<tr>
					<td valign="center" colspan="3">
						<?php echo $this->Form->input('user_id'); ?>
					</td>
				</tr>	
			<?php endif; ?>
			<tr>
				<td>
					<select name="data[PurchasedReceipt][instrument]" id="SoldReceiptInstrument">
						<option value="Cash" <?php if($this->data['PurchasedReceipt']['instrument']=='Cash') echo 'selected';?>>Cash</option>
						<option value="Cheque" <?php if($this->data['PurchasedReceipt']['instrument']=='Cheque') echo 'selected';?>>Cheque</option>
						<option value="TT" <?php if($this->data['PurchasedReceipt']['instrument']=='TT') echo 'selected';?>>TT</option>
					</select>
				</td>
			</tr>	
		<table>
		<table class="well">
				<tr>
					<td>
					<?php echo $this->Form->input('currency_id',array('class'=>'my_currencies')); ?>
					<div class="my_oh_my"><?php echo $this->Form->input('other_currency_id',array('class'=>'other_name','options'=>$other_currencies)); ?></div>
					</td>					
					<td><?php echo $this->Form->input('purchased_purpose_id',array('label'=>'Source of funds')); ?></td>	
				</tr>
				<tr>
					<td>
						
						<?php if($this->Form->value('currency_id')=='c8'): ?>
							<?php echo $this->Form->input('amount',array('class'=>'p_amount','style'=>'font-size: 10px;','value'=>$this->Form->value('orig_amount'))); ?>
						<?php else:?>
							<?php echo $this->Form->input('amount',array('class'=>'p_amount','style'=>'font-size: 10px;')); ?>
						<?php endif; ?>
					</td>
					<td>
						<?php if($this->Form->value('currency_id')=='c8'): ?>
							<?php echo $this->Form->input('rate',array('class'=>'p_rate','style'=>'font-size: 10px;','value'=>$this->Form->value('orig_rate'))); ?>
						<?php else:?>
							<?php echo $this->Form->input('rate',array('class'=>'p_rate','style'=>'font-size: 10px;')); ?>
						<?php endif; ?>
					</td>
				</tr>
				<tr>
					<td><?php echo $this->Form->input('amount_ugx',array('class'=>'p_amount_ugx','readonly'=>'true','style'=>'font-size: 10px;')); ?></td>	
					<td colspan="3">
						<?php if($super_admin): ?>		
								<?php echo $this->Form->input('date'); ?>
						<?php else: ?>
								<?php echo $this->Form->input('date',array('type'=>'hidden')); ?>
						<?php endif;?>
					</td>				
				</tr>

				<tr class="large_cash_detail_link">
					<td>
						<div class="span12">
							<span class="large_cash_detail_link_btn btn btn-small"><i class="icon icon-user"></i> Add Customer Details</span>
						</div>
					</td>				
				</tr>
				
				<tr class="large_cash_detail">
					<td><?php echo $this->Form->input('customer_name',array('style'=>'font-size: 10px;')); ?></td>
					<td><?php echo $this->Form->input('passport_number',array('style'=>'font-size: 10px;')); ?></td>				
					
				</tr>
				<tr class="large_cash_detail">
					<td><?php echo $this->Form->input('phone_number',array('style'=>'font-size: 10px;')); ?></td>
					<td><?php echo $this->Form->input('nationality',array('style'=>'font-size: 10px;')); ?></td>
				</tr>
				<tr class="large_cash_detail">
					<td colspan="<?php echo ($super_admin)? 2:2 ?>"><?php echo $this->Form->input('address',array('style'=>'font-size: 10px;height:'.(($super_admin)? "20px;":"30px;"),'type'=>'textarea')); ?></td>
				</tr>
				<div class="large_cash_detail_required"></div>
		</table>		
	</fieldset>
	<div id="test-group" class="input-prepend btn-group" data-toggle="buttons-radio" data-toggle-name="testOption">
		<input type="hidden" name="data[PurchasedReceipt][print]" value="dont_print"/>
		<button type="button" class="btn" data-toggle-value="print">Print</button>
		<button type="button" class="btn active" data-toggle-value="dont_print">Dont' print</button>
	</div>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
</div>
	<?php if($use_system_board_rates):?>
		<div id="system_board_rates" style="display:none;">
			<?php foreach($currenciesDetails as $val):?>
				<span id="board<?=$val['Currency']['id']?>" buy="<?=$val['Currency']['buy']?>" sell="<?=$val['Currency']['sell']?>"></span>
			<?php endforeach;?>
			<?php foreach($otherCurrenciesDetails as $val):?>
				<span id="board<?=$val['OtherCurrency']['id']?>" buy="<?=$val['OtherCurrency']['buy']?>" sell="<?=$val['OtherCurrency']['sell']?>"></span>
			<?php endforeach;?>
		</div>
		<script>
			//$('.p_rate').attr('readonly','true');
		</script>
	<?php endif;?>

<script>
	function updateCurrency(){
		var selected_currency = $('.my_currencies').val();
		var currency_id = selected_currency;
		if(selected_currency=='c8'){
			currency_id = $('#PurchasedReceiptOtherCurrencyId').val();
		}
		<?php if($use_system_board_rates):?>
			//$('.p_rate').val($('#board'+currency_id).attr('buy'));
		<?php endif;?>
		updateUGX();
	}
	function updateUGX(){
		var amount=Number($('.p_amount').val());
		var rate =Number($('.p_rate').val());
		$('.p_amount_ugx').val(amount*rate);
	}
	$(document).ready(function(){
		check_require_customer_details();
		
		$(document).on('change','.my_currencies,.p_amount',function(){
			var currency = $('.my_currencies').val();
			var amount = $('.p_amount').val();
			if(amount >= 5000){
				if(currency=='USD' || currency=='GBP'){
					$('.large_cash_detail').toggle();
					$('.large_cash_detail_required').html('');
				}
			}
		});
		
		$('.large_cash_detail_link_btn').click(function(){
			$('.large_cash_detail').toggle();
			$('.large_cash_detail_required').html('');
		});
		
		$('body').on('submit', 'form', function(e) {
			$('.submit').hide();
			var self = $(this);
			if (self.data('alreadySubmitted')) {
				e.stopImmediatePropagation();
				e.preventDefault();
			} else {
				self.data('alreadySubmitted', true);
			}
		});

		$('#test-group button').click(function(){
			$('#test-group input').val($(this).data("toggle-value"));
		});
		
		$('.p_amount, .p_amount_ugx,.p_rate').change(function(){
			updateCurrency();
			updateUGX();
			check_require_customer_details();
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
		
		$('.my_currencies option').click(function(){
			updateCurrency();
		});
		$('.my_currencies').change(function(){
			updateCurrency();
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
	var amountUSD,amountMX = 5000;
	function check_require_customer_details()
	{
		// Convert from Ncurrency to UGX then from UGX to USD using the average rate btn the BUY and SELL
		var amountUGX = $('.p_amount_ugx').val();
		var rateUSD = $('#boardc1').attr('buy');
		amountUSD= amountUGX/rateUSD;
		if(amountUSD>=amountMX){require_customer_details(true);}else{require_customer_details(false);}
	}

	function require_customer_details(show)
	{
		<?php if($use_system_board_rates):?>
		if(show){
			$('.large_cash_detail').toggle();
			$('.large_cash_detail_required').html('');
			
			if(amountUSD>=amountMX){
				var requiredV = '<input name="requiredInput" value="PurchasedReceiptCustomerName|PurchasedReceiptNationality|PurchasedReceiptAddress|PurchasedReceiptPassportNumber|PurchasedReceiptPhoneNumber" type="hidden">';
				requiredV += '<input name="requiredInputLabels" value="Customer Name|Nationality|Address|Passport/ID Number|Phone Number" type="hidden">';
			}
			$('.large_cash_detail_required').append(requiredV);
		}else{
			$('.large_cash_detail').hide();
			$('.large_cash_detail_required').html('');
		}
		<?php else:?>
			// $('.large_cash_detail').toggle();
			$('.large_cash_detail_required').html('');
		<?php endif;?>
	}
</script>