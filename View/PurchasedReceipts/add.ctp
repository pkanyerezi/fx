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
		padding: .7em;
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
	.row{
		margin-left:10px;
	}
	label{
		font-weight:bold;
	}
	.large_cash_detail{display: none;}
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
<div class="purchaseReceipts form" style="border-left:none;width:95%;margin-top: 0px;">
	<div class="widget-box">
		<div class="widget-title">
			<span class="icon">
				<i class="icon-align-justify"></i>									
			</span>
			<h5 style="color:maroon;">Add Purchase Receipt</h5>
		</div>
		<div class="widget-content nopadding">
				<?php echo $this->Form->create('PurchasedReceipt'); ?>
				<fieldset>				
					<div class="row">
						<?php if($super_admin):?>
						<span class="span4"><?php echo $this->Form->input('user_id'); ?></span>
						<?php endif; ?>
						<span class="span4 pull-right">
							<span class="input select required"><label for="PurchasedReceiptInstrument">Instrument</label>
								<select name="data[PurchasedReceipt][instrument]" id="PurchasedReceiptInstrument">
									<option value="Cash">Cash</option>
									<option value="TT">TT</option>
								</select>
							</span>
						</span>
					</div>
					
					<div class="row">
						<span class="span6">
							<?php echo $this->Form->input('currency_id',array('class'=>'my_currencies','empty'=>'Select Currency','style'=>'width:100%')); ?>
								<span class="my_oh_my span4"><?php echo $this->Form->input('other_currency_id',array('class'=>'other_name','options'=>$other_currencies)); ?></span>
						</span>
						<span class="span6">
							<?php echo $this->Form->input('purchased_purpose_id',array('label'=>'Source of funds','style'=>'width:100%')); ?>
						</span>
					</div>
					
					
					<div class="row">
						<span class="span6"><?php echo $this->Form->input('amount',array('class'=>'p_amount','style'=>'font-size: 10px;')); ?></span>
						<span class="span6"><?php echo $this->Form->input('rate',array('class'=>'p_rate','style'=>'font-size: 10px;')); ?></span>
					</div>
					
					<div class="row">
						<span class="span6"><?php echo $this->Form->input('amount_ugx',array('class'=>'p_amount_ugx','readonly'=>'true','style'=>'font-size: 10px;')); ?></span>
						<?php if($super_admin): ?>		
								<span class="span6">
									<div class="input">
										<label>Date:</label>
										<div class="input-append date" id='dp_x' data-date="<?php echo date('Y-m-d'); ?>" data-date-format="yyyy-mm-dd">
											<input style="width:210px;" class="span2" size="16" type="text" id='dp_today_selected' value="<?php echo date('Y-m-d'); ?>" name="data[PurchasedReceipt][date]"/>
											<span class="add-on"><i class="icon-th"></i></span>
										</div>
									</div>
								</span>
								
								<script>
									$(document).ready(function(){
										$('#dp_x').datepicker({
											format: 'yyyy-mm-dd'
										});
									});
								</script>	
						<?php endif;?>
					</div>
					<div class="row large_cash_detail_link">
						<div class="span12">
							<span class="large_cash_detail_link_btn btn btn-small"><i class="icon icon-user"></i> Add Customer Details</span>
						</div>
					</div>
					<div class="row large_cash_detail">
						<span class="span6"><?php echo $this->Form->input('customer_name',array('style'=>'font-size: 10px;')); ?></span>
						<span class="span6"><?php echo $this->Form->input('passport_number',array('style'=>'font-size: 10px;')); ?></span>
					</div>
					
					<div class="row large_cash_detail">
						<span class="span6"><?php echo $this->Form->input('nationality',array('style'=>'font-size: 10px;')); ?></span>
						<span class="span6"><?php echo $this->Form->input('phone_number',array('style'=>'font-size: 10px;')); ?></span>
					</div>

					<div class="row large_cash_detail">
						<span class="<?php echo ($super_admin)? 'span12':'span12' ?>">
							<?php echo $this->Form->input('address',array('style'=>'font-size: 10px;height:'.(($super_admin)? "20px;":"30px;"),'type'=>'textarea')); ?>
						</span>
					</div>

					<div class="large_cash_detail_required"></div>
				</fieldset>
				<div id="test-group" class="input-prepend btn-group" data-toggle="buttons-radio" data-toggle-name="testOption" style="float:right;margin-left: 70%;">
					<input type="hidden" name="data[PurchasedReceipt][print]" value="print"/>
					<button type="button" class="btn btn-danger active" data-toggle-value="print">Print</button>
					<button type="button" class="btn btn-danger" data-toggle-value="dont_print">Dont' print</button>
				</div>
			<?php echo $this->Form->end(__('Submit')); ?>
		</div>

		<br/></br><br/><br/><br/>

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
			<?php if($authUser['require_board_rate']):?>
				$('.p_rate').attr('readonly','true');
			<?php endif;?>
		</script>
	<?php endif;?>

<script>
	function updateCurrency(){
		var selected_currency = $('.my_currencies').val();
		var currency_id = selected_currency;
		if(selected_currency=='c8'){
			currency_id = $('#PurchasedReceiptOtherCurrencyId').val();
		}
		<?php if($use_system_board_rates && $authUser['require_board_rate']):?>
			$('.p_rate').val($('#board'+currency_id).attr('buy'));
		<?php endif;?>

		<?php if(!$authUser['require_board_rate']):?>
			if($('.p_rate').val().length<=0){
				$('.p_rate').val($('#board'+currency_id).attr('buy'));
			}
		<?php endif;?>

		updateUGX();
	}
	function updateUGX(){
		var amount=Number($('.p_amount').val());
		var rate =Number($('.p_rate').val());
		$('.p_amount_ugx').val(amount*rate);
	}
	$(document).ready(function(){
	
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
		
		$('.p_amount, .p_amount_ugx, .p_rate').change(function(){
			updateCurrency();
			updateUGX();
			check_require_customer_details();
		});
		
		var my_oh_my=$('.my_oh_my').html();
		var OtherNameValue=$('.OtherNameValue').val();
		$('.my_oh_my').html('');
		$('.my_oh_my').hide();
		$('.my_currencies option').click(function(){
			$('.p_rate').val('');
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