<?php //echo $downloadsIp;exit; ?>
<?php echo $this->Html->script(array('script_dynamic_content'));?>
<div class="flash-message"><?php echo $this->Session->flash(); ?></div>
<div style="float:right;margin-right:2.3%;">	
	<span class="btn btn-small" onclick="do_print();"><i class="icon icon-print"></i> Print page</span>
	<span class="btn btn-small" onclick="do_print_checked_receipts();"><i class="icon icon-print"></i> Print checked</span>
	<?php if(isset($large_cash)):?>
		<!--<span class="btn btn-small"><a href="<?php echo $this->webroot.'purchased_receipts/send_purchase_large_cash';?>"><i class="icon icon-upload"></i> Send Only Sales Large Cash</span>-->
	<?php endif; ?>
</div>
<div class="purchasedReceipts printable well well-new" style="text-align:left;cursor:auto;">
	<style>
	<!--
		.my_data table tr td{
			border:1px solid #eee;
		}
	-->
	</style>
	<h2>
		<?php echo __('Purchase Receipts'); ?>
		<?php if(isset($large_cash)):?>
			<?php echo '(Large cash)'; ?> | <a class="btn btn-small no-ajax generate-excel" id="generate-excel" href="http://<?php echo $downloadsIp;?>/fx/sold_receipts/excel_large_cash" target="_blank">Excel file(Both Purchase and Sales)</a>
			<script>
				$(document).ready(function(){
					$("#generate-excel").bind("click", function(e) { 
						e.preventDefault();
					   	var href=$(this).attr('href');
						href+='?date_from='+($('#dp_from_selected').val());
						href+='&date_to='+($('#dp_to_selected').val());
						location.href = href; 
					});
				});
			</script>
		<?php endif; ?>
	</h2>
	<?php if(isset($from) && isset($to)):?>
		<h6><?php echo 'from '.$from.', to '.$to.' <span class="non_printable">('.$this->Time->timeAgoInWords($from, array('accuracy' => array('day' => 'day'),'end' => '1 year')).')</span>';?></h6>
	<?php endif; ?>
	<a href="<?php echo $this->webroot;?>purchased_receipts/add" onclick="return false;" style="float:right;" class="non_printable btn btn-small" >
		<i class="icon icon-plus-sign"></i> New Purchase Receipt
	</a>
	
	<?php if(isset($large_cash)):?>
		<span class="non_printable" style="color:maroon;"><b><?php echo ('Average Dollar Rate Used: $<span class="ln">'.$dollar_av_rate.'</span>');?></b></span>
	<?php endif; ?>
	
	<?php if(!empty($purchasedReceipts)):?>
		<div class="alert alert-error non_printable" style="font-weight:bolder;position: fixed;bottom: 0;right: 0;width:150px;" onclick="$(this).fadeOut('slow');">
			<span style="font-size:15px;">HELP</span><hr/>
			<i class="icon icon-pencil"></i> Click a currency from &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;the receipts to view &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;only for that currency<br><br>
			<i class="icon icon-pencil"></i> Click a title to sort by &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;the title in ASCENDING &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;OR DESCENDING order
		</div>
	<?php endif;?>
	<div class="my_data">
		<table cellpadding="0" cellspacing="0" style="width:100%;text-align:center;">
		<tr>
				<th class="actions non_printable"><?php echo __(''); ?></th>
				<th class="actions non_printable"><?php echo __(''); ?></th>
				<th><?php echo $this->Paginator->sort('id','Receipt number'); ?></th>
				<th>Amount</th>
				<th>Rate</th>
				<th><?php echo $this->Paginator->sort('amount_ugx'); ?></th>
				<th><?php echo $this->Paginator->sort('currency_id'); ?></th>
				<th class="non_printable"><?php echo $this->Paginator->sort('customer_name'); ?></th>
				<th><?php echo $this->Paginator->sort('date'); ?></th>
				<th><?php echo $this->Paginator->sort('t_time','Time'); ?></th>	
				<th><?php echo $this->Paginator->sort('instrument'); ?></th>	
				<th class="non_printable">Created By</th>
		</tr>
		<style>
			<!--
			table tr:nth-child(even) {
				background: none;
			}
			-->
		</style>
		<?php $total_ugx=0;?>
		<?php $total_foreign=0;?>
		<?php foreach ($purchasedReceipts as $purchasedReceipt): ?>
		<tr>
			<td class="non_printable">
				<div class="btn-group">
					<button class="btn dropdown-toggle" data-toggle="dropdown">
					  <i class="icon icon-certificate"></i>
					  <span class="caret"></span>
					</button>
					<ul class="dropdown-menu">
						<li><a href="<?php echo $this->webroot;?>purchased_receipts/print_receipt/<?php echo h($purchasedReceipt['PurchasedReceipt']['id']); ?>" onclick="return false;" class="print-receipt no-ajax" ><i class="icon icon-print"></i> Print</a></li>
						<?php if($super_admin): ?>
							<li class="divider"></li>
							<?php if($purchasedReceipt['PurchasedReceipt']['is_uploaded']==1): ?>
								<li><a href="<?php echo $this->webroot;?>purchased_receipts/should_upload/<?php echo h($purchasedReceipt['PurchasedReceipt']['id']); ?>/0" onclick="return false;" >Uploadable</a></li>
							<?php elseif($purchasedReceipt['PurchasedReceipt']['is_uploaded']==0): ?>
								<li><a href="<?php echo $this->webroot;?>purchased_receipts/should_upload/<?php echo h($purchasedReceipt['PurchasedReceipt']['id']); ?>/1" onclick="return false;" >Dont upload</a></li>
							<?php endif; ?>
						<?php endif;?>
						
						<li class="divider"></li>
						<li><?php echo $this->Html->link(__('View'), array('action' => 'view', $purchasedReceipt['PurchasedReceipt']['id']),array('class'=>'action-view')); ?></li>
						
						<?php if($authUser['can_edit_receipt']): ?>
							<li><?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $purchasedReceipt['PurchasedReceipt']['id']),array('class'=>'action-edit')); ?></li>
						<?php endif;?>
						<?php if($authUser['can_delete_receipt']): ?>
							<li><?php echo $this->Html->link(__('Delete'), array('action' => 'delete', $purchasedReceipt['PurchasedReceipt']['id']),array('class'=>'action-delete confirm-first','data-confirm-text'=>'Are you sure you want to delete it?')); ?></li>
						<?php endif;?>
					</ul>
				</div>
			</td>
			<td class="non_printable">
				<div><br>
					<input type="checkbox" style="float: left;" class="purchase-receipt" value="<?php echo h($purchasedReceipt['PurchasedReceipt']['id']); ?>">
				</div>
			</td>
			<td><?php echo h($purchasedReceipt['PurchasedReceipt']['id']); ?>&nbsp;</td>
			<td>
				<?php if($purchasedReceipt['Currency']['id']=='c8'): ?>
					<?php $total_foreign+=$purchasedReceipt['PurchasedReceipt']['orig_amount']; echo h($purchasedReceipt['PurchasedReceipt']['orig_amount']); ?>
				<?php else: ?>
					<?php $total_foreign+=$purchasedReceipt['PurchasedReceipt']['amount']; echo h($purchasedReceipt['PurchasedReceipt']['amount']); ?>
				<?php endif; ?>
				&nbsp;			
			</td>
			<td>
				<?php if($purchasedReceipt['Currency']['id']=='c8'): ?>
					<?php echo h($purchasedReceipt['PurchasedReceipt']['orig_rate']); ?>
				<?php else: ?>
					<?php echo h($purchasedReceipt['PurchasedReceipt']['rate']); ?>
				<?php endif; ?>
				&nbsp;			
			</td>
			<td><?php echo h($purchasedReceipt['PurchasedReceipt']['amount_ugx']);$total_ugx+=($purchasedReceipt['PurchasedReceipt']['amount_ugx']); ?>&nbsp;</td>
			<td>
				<?php if($purchasedReceipt['Currency']['id']=='c8'): ?>
					<a class="use-ajax" href="<?php echo $this->webroot;?>purchased_receipts/index/?currency=c8&other_currency=<?php echo $purchasedReceipt['PurchasedReceipt']['other_currency_id'];?>&date_from=<?php echo $from;?>&date_to=<?php echo $to;?>">
					<?php echo $purchasedReceipt['PurchasedReceipt']['other_name']; ?>
				<?php else: ?>
					<a class="use-ajax" href="<?php echo $this->webroot;?>purchased_receipts/index/?currency=<?php echo $purchasedReceipt['PurchasedReceipt']['currency_id'];?>&date_from=<?php echo $from;?>&date_to=<?php echo $to;?>">
					<?php echo $purchasedReceipt['Currency']['id']; ?>
				<?php endif; ?>
				&nbsp;		
				</a>
			</td>
			<td class="non_printable"><?php echo h($purchasedReceipt['PurchasedReceipt']['customer_name']); ?>&nbsp;</td>
			<td><?php echo h($purchasedReceipt['PurchasedReceipt']['date']); ?>&nbsp;</td>
			<td><?php echo date('h:i:s a',strtotime(h($purchasedReceipt['PurchasedReceipt']['t_time']))); ?>&nbsp;</td>
			<td><?php echo $purchasedReceipt['PurchasedReceipt']['instrument']; ?>&nbsp;</td>
			<td class="non_printable"><?php echo h($purchasedReceipt['PurchasedReceipt']['name']); ?>&nbsp;</td>	
		</tr>
	<?php endforeach; ?>
		<?php if(isset($setCurrency)):?>
		<tr  style="background:#2c2c2c;color:#fff;">
			<td colspan="2">Total(for the above records)</td>
			<td colspan="1"><span class="ln"><?php echo $total_foreign; ?></span></td>
			<td colspan="9">UGX:<span class="ln"><?php echo $total_ugx; ?></span></td>
		</tr>
		<?php else:?>
		<tr  style="background:#2c2c2c;color:#fff;">
			<td colspan="3">Total(for the above records)</td>
			<td colspan="9">UGX:<span class="ln"><?php echo $total_ugx; ?></span></td>
		</tr>
		<?php endif;?>
		</table>
	</div>
	<p class="non_printable">
	<?php
	echo $this->Paginator->counter(array(
	'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
	));
	?>	</p>
	<div class="paging non_printable">
	<?php
		echo $this->Paginator->prev('< ' . __('previous'), array(), null, array('class' => 'prev disabled'));
		echo $this->Paginator->numbers(array('separator' => ''));
		echo $this->Paginator->next(__('next') . ' >', array(), null, array('class' => 'next disabled'));
	?>
	</div>
</div>

<script>
	$('.print-receipt').click(function(){
		$.get($(this).attr('href'),function(data){
			//alert(data);
		});
	});
	
	function do_print(){
		$('.non_printable').remove();
		var x=window.open("","");
		x.document.write($('.printable').html());
		x.window.print();
	}
	
	function do_print_checked_receipts(){
		var receipts = '';
		$('.purchase-receipt:checked').each(function(){
			receipts+=((receipts.length>1)?','+($(this).val()):($(this).val()));
		});
		if(receipts.length>1){
			$.post('<?php echo $this->webroot;?>purchased_receipts/print_multiple_receipts/'+receipts,function(data){
				alert(data);
			});
		}
	}
</script>