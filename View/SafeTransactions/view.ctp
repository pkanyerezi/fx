<?php echo $this->Html->script(array('script_dynamic_content'));?>
<div class="flash-message"><?php echo $this->Session->flash(); ?></div>
<div class="btn-group pull-right">
	<span onclick="do_receipt();" class="print-receipt btn btn-small"><i class="icon icon-print"></i> Print</span>
	<a href="<?php echo $this->webroot;?>safe_transactions" class="btn btn-small"><i class="icon icon-th"></i> View transactions</a>
</div>
<div class="soldReceipts printable well well-new">
	<style>
	<!--
		.my_data table tr td{
			border:1px solid #eee;
		}
	-->
	</style>
	<h2>Safe Transaction #<?php echo $safeTransaction['SafeTransaction']['id']?></h2>
	
	<div class="my_data printable">
		<table cellpadding="0" cellspacing="0" style="width:100%;text-align:center;" class="table table-bordered">
			<tr>
				<td style="text-align:left;padding: 0px 15px 0px 0px;">Amount</td>
				<td style="text-align:left;"><?php echo number_format($safeTransaction['SafeTransaction']['amount'])?> <?php echo $safeTransaction['SafeTransaction']['currency'];?></td>
			</tr>
			<tr>
				<td style="text-align:left;padding: 0px 15px 0px 0px;">Type</td>
				<td style="text-align:left;"><?php echo $safeTransaction['SafeTransaction']['transaction_type']?></td>
			</tr>
			<tr>
				<td style="text-align:left;padding: 0px 15px 0px 0px;">Status</td>
				<td style="text-align:left;"><?php echo $safeTransaction['SafeTransaction']['status']?></td>
			</tr>
			<tr>
				<td style="text-align:left;padding: 0px 15px 0px 0px;">Created on</td>
				<td style="text-align:left;"><?php echo $safeTransaction['SafeTransaction']['date']?></td></tr>
			<tr>
				<td style="text-align:left;padding: 0px 15px 0px 0px;">Created By</td>
				<td style="text-align:left;"><?php echo ucwords($safeTransaction['User']['name'])?></td>
			</tr>
			<tr>
				<td style="text-align:left;padding: 0px 15px 0px 0px;">Sent To</td>
				<td style="text-align:left;"><?php echo ucwords($safeTransaction['To']['name'])?></td>
			</tr>
			<tr>
				<td style="text-align:left;padding: 0px 15px 0px 0px;">Sent From</td>
				<td style="text-align:left;"><?php echo ucwords($safeTransaction['From']['name'])?></td>
			</tr>
			<tr>
				<td style="text-align:left;padding: 0px 15px 0px 0px;">Approved By</td>
				<td style="text-align:left;"><?php echo ucwords($safeTransaction['Approver']['name'])?></td>
			</tr>
			<tr>
				<td style="text-align:left;padding: 0px 15px 0px 0px;">Approved On</td>
				<td style="text-align:left;"><?php echo ($safeTransaction['SafeTransaction']['approved_at'])?></td>
			</tr>
			<tr>
				<td style="text-align:left;padding: 0px 15px 0px 0px;">Accepted on</td>
				<td style="text-align:left;"><?php echo ($safeTransaction['SafeTransaction']['accepted_at'])?></td>
			</tr>
			<tr>
				<td style="text-align:left;padding: 0px 15px 0px 0px;">Notes</td>
				<td style="text-align:left;"><?php echo $safeTransaction['SafeTransaction']['comment']?></td>
			</tr>
		</table>
	</div>
</div>
<script>
	function do_receipt(){
		$('.non_printable').remove();
		var x=window.open("","");
		x.document.write($('.printable').html());
		x.window.print();
	}
</script>
