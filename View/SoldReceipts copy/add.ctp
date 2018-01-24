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
	
	table tr td {border-bottom: 1px solid #f3f4f5;}
	table tr:nth-child(even) {background: none;}
	.row{margin-left:10px;}
	label{font-weight:bold;}
</style>
<?php echo $this->Html->script(array('script_dynamic_content'));?>
<div class="flash-message"><?php echo $this->Session->flash(); ?></div>
<div class="soldReceipts form" style="border-left:none;width:95%;margin-top: -66px;">
	<div class="widget-box">
		<div class="widget-title">
			<span class="icon">
				<i class="icon-align-justify"></i>									
			</span>
			<h5 style="color:maroon;">Add Sales Receipt</h5>
		</div>
		<div class="widget-content nopadding">
			<?php echo $this->Form->create('SoldReceipt'); ?>
				<fieldset>				
					<?php if($super_admin):?>
						<div class="row" style="margin:left:10%;">
							<span class="span2">
								<?php echo $this->Form->input('user_id'); ?>
							</span>
						</div>
					<?php endif; ?><br/>
					<div class="row btn-group">
						<span class="span4">
							<label for="SoldReceiptNumber">&nbsp;</label>
							<?php $unused_sales_receipt_id=$this->Session->read('unused_sales_receipt_id'); ?>
							<?php if(!$unused_sales_receipt_id): ?>
								<span class="btn btn-inverse generate-rid"><i class="icon-white icon-refresh"></i> Generate Receipt number</span>
							<?php endif; ?>
						</span>
						<span class="span4">
							<span class="input select required"><label for="SoldReceiptNumber">Receipt number</label>
								<input type="text" name="data[SoldReceipt][id]" required="" value="<?php if($unused_sales_receipt_id) echo $unused_sales_receipt_id; ?>" class="sold_receipt_number" />
							</span>
						</span>
						<span class="span3">
							<span class="input select required"><label for="SoldReceiptInstrument">Instrument</label>
								<select name="data[SoldReceipt][instrument]" id="SoldReceiptInstrument">
									<option value="Cash">Cash</option>
									<option value="Cheque">Cheque</option>
								</select>
							</span>
						</span>
					</div><br/><br/>
					
					
					
					
					<div class="row">
						<span class="span6">
						<?php echo $this->Form->input('currency_id',array('class'=>'my_currencies','style'=>'width:100%')); ?>
						<span  class="my_oh_my span4"><?php echo $this->Form->input('other_currency_id',array('class'=>'other_name','options'=>$other_currencies)); ?></span>
						</span>			
						<span class="span6"><?php echo $this->Form->input('purpose_id',array('label'=>'Purpose of transaction','style'=>'width:100%')); ?></span>
					</div>
					
					<div class="row">
						<span class="span6"><?php echo $this->Form->input('customer_name',array('style'=>'font-size: 10px;')); ?></span>
						<span class="span6"><?php echo $this->Form->input('amount',array('class'=>'amount','style'=>'font-size: 10px;')); ?></span>
					</div>
					
					<div class="row">
						<span class="span6"><?php echo $this->Form->input('rate',array('class'=>'rate','style'=>'font-size: 10px;')); ?></span>
						<span class="span6"><?php echo $this->Form->input('amount_ugx',array('class'=>'amount_ugx','readonly'=>'true','style'=>'font-size: 10px;')); ?></span>					
					</div>
					
					<div class="row">
						<span class="span6"><?php echo $this->Form->input('passport_number',array('style'=>'font-size: 10px;')); ?></span>				
						<span class="span6"><?php echo $this->Form->input('nationality',array('style'=>'font-size: 10px;')); ?></span>
					</div>
					<div class="row">
						<span class="span6"><?php echo $this->Form->input('address',array('style'=>'font-size: 10px;height:'.(($super_admin)? "20px;":"30px;"),'type'=>'textarea')); ?></span>
							
						<?php if($super_admin): ?>		
								<span class="span6">
									<div class="input">
										<label>Date:</label>
										<span class="input-append date" id='dp_x' data-date="<?php echo date('Y-m-d'); ?>" data-date-format="yyyy-mm-dd">
											<input style="width:210px;" class="span2" size="16" type="text" id='dp_today_selected' value="<?php echo date('Y-m-d'); ?>" name="data[SoldReceipt][date]"/>
											<span class="add-on"><i class="icon-th"></i></span>
										</span>
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
				</fieldset>
				<div id="test-group" class="input-prepend btn-group" data-toggle="buttons-radio" data-toggle-name="testOption" style="float:right;margin-left: 70%;">
					<input type="hidden" name="data[SoldReceipt][print]" value="print"/>
					<button type="button" class="btn btn-danger active" data-toggle-value="print">Print</button>
					<button type="button" class="btn btn-danger" data-toggle-value="dont_print">Dont' print</button>
				</div>
			<?php echo $this->Form->end(__('Submit')); ?>
		</div>
		<br/></br><br/><br/><br/>
	</div>
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
		
		var my_oh_my=$('.my_oh_my').html();
		var OtherNameValue=$('.OtherNameValue').val();
		$('.my_oh_my').html('');
		$('.my_oh_my').hide();
		
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
