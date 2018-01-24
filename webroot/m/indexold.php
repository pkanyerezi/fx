<?php require "connection.php" ;?>
<!DOCTYPE html>
<html>
<head>
	<title>Forex Buerau</title>
	<script type="text/javascript" src="js/jquery1.7.2mini.js"></script>
	<script type="text/javascript" src="js/script.js"></script>	
	<style>
		#new-receipts{font-size:12px !important;}
		.summary td{border:1px solid #999;}
	</style>
</head>

<body>
	<div id="new-receipts" style="font-size:12px;">
		<center style="font-size:17px;">
			<?php 
				$name='';$location='';
				$sql1=$dbh->query("select name,location from foxes limit 1"); 
				if(count($sql1)){
					$ids="";
					foreach($sql1 as $row){
						$name = $row['name'];
						$location = $row['location'];
					}
				}
			?>
			<b style="font-size:17px"><?php echo $name;?></b><br/>
			<?php echo $location;?>
			<table width="65%" style="margin-left:180px">
				<tr>
					<td style="font-size:12px;">
						<b class="xchange">Foreign Exchange Inflows</b><br/>
					</td>
					<td style="font-size:12px;">
						<b class="receipt_type">Form P(cash sales)</b><br/>
					</td>
					<td style="font-size:12px;">
						<div style="float:right">
						<b>Receipt No: <span class="receipt_number"></span></b>
						</div>
					</td>
				</tr>
			</table>
		</center>
		<center>
			<table width="90%" class="summary" style="border-spacing:0;" >
				<tr style="font-size:12px;">
					<td style="text-align:center;"><b>Currency</b></td>
					<td class="receipt_instrument" style="text-align:center;"><b>Instrument</b></td>
					<td style="text-align:center;"><b>Amount</b></td>
					<td style="text-align:center;"><b>Rate</b></td>
					<td style="text-align:center;"><b>Amount in UShs.</b></td>
				</tr>
				<tr class="summary-middle" style="font-size:12px;">
					<td class="receipt_currency">&nbsp;</td>
					<td class="receipt_instrument receipt_instrument_data">&nbsp;</td>
					<td class="receipt_amount" style="text-align:right;">&nbsp;</td>
					<td class="receipt_rate" style="text-align:right;">&nbsp;</td>
					<td class="receipt_amount_ugx" style="text-align:right;">&nbsp;</td>
				</tr>
				<tr class="summary-middle total_sum" style="font-size:12px;">
					<td style="border:0px">&nbsp;</td>
					<td class="receipt_instrument_empty" style="border:0px">&nbsp;</td>
					<td style="border:0px">&nbsp;</td>
					<td style="text-align:right;"><b>Total UGX:</b>&nbsp;</td>
					<td class="receipt_total_amount_ugx" style="text-align:right;"></td>
				</tr>
			</table>
		</center>
		
		<b style="font-size:12px;">A.&nbsp;&nbsp;<u class="purpose-title">Purpose of purchase</u></b>
		<span style="font-size:12px;" class="receipt_purpose"></span><br/><br/>
		
		<div style="font-size:12px;" >
			<b>
				<u class="particular-item">
					<div class="particular-item">Particulars of Buyer:</div>
				</u>
			</b>
			<br/>To be completed for transactions of US$5,000= and above or its equivalent
			<table width="100%" style="font-size:12px;" >
				<tr style="height:20px;font-size:12px;">
					<td width="25%" style="font-weight:bold">Name</td>
					<td ><span class="receipt_customer_name"></span></td> 
					<td width="30%" style="font-weight:bold">Address</td> 
					<td ><span class="receipt_customer_address"></span></td>
				</tr>
				<tr style="height:20px;font-size:12px;">
					<td style="font-weight:bold">Nationality</td>
					<td ><span class="receipt_customer_nationality"></span></td> 
					<td style="font-weight:bold;font-size:12px;">Passport/ID No.</td> 
					<td ><span class="receipt_customer_passport_number"></span></td>
				</tr>
				<tr style="height:20px;font-size:12px;">
					<td style="font-weight:bold">Customer signature</td>  
					<td ></td> 
					<td style="font-weight:bold;font-size:12px;">Dealer's signature &amp; stamp</td> 
					<td ></td>
				</tr>
				<tr >
					<td colspan="2">
						<span style="float:left;font-size:80%;">
							<b>Date:&nbsp;&nbsp;</b>
							<span class="datetime">.../.../...</span>
						</span>	
					</td>
					<td style="font-size:80%;"><b>Cashier:</b> <span class="cashier"></span></td>
					<td >
						<span style="float:right;font-weight:bold;font-size:80%;">
							Approved by Bank of Uganda
						</span>
					</td>
				</tr>
			</table>
		</div>
	</div><br/><br/><br/><br/><br/>
	<div id="new-receipts" style="font-size:12px;">
		<center style="font-size:17px;">
			<?php 
				$name='';$location='';
				$sql1=$dbh->query("select name,location from foxes limit 1"); 
				if(count($sql1)){
					$ids="";
					foreach($sql1 as $row){
						$name = $row['name'];
						$location = $row['location'];
					}
				}
			?>
			<b style="font-size:17px"><?php echo $name;?></b><br/>
			<?php echo $location;?>
			<table width="65%" style="margin-left:180px">
				<tr>
					<td style="font-size:12px;">
						<b class="xchange">Foreign Exchange Inflows</b><br/>
					</td>
					<td style="font-size:12px;">
						<b class="receipt_type">Form P(cash sales)</b><br/>
					</td>
					<td style="font-size:12px;">
						<div style="float:right">
						<b>Receipt No: <span class="receipt_number"></span></b>
						</div>
					</td>
				</tr>
			</table>
		</center>
		<center>
			<table width="90%" class="summary" style="border-spacing:0;" >
				<tr style="font-size:12px;">
					<td style="text-align:center;"><b>Currency</b></td>
					<td class="receipt_instrument" style="text-align:center;"><b>Instrument</b></td>
					<td style="text-align:center;"><b>Amount</b></td>
					<td style="text-align:center;"><b>Rate</b></td>
					<td style="text-align:center;"><b>Amount in UShs.</b></td>
				</tr>
				<tr class="summary-middle" style="font-size:12px;">
					<td class="receipt_currency">&nbsp;</td>
					<td class="receipt_instrument receipt_instrument_data">&nbsp;</td>
					<td class="receipt_amount" style="text-align:right;">&nbsp;</td>
					<td class="receipt_rate" style="text-align:right;">&nbsp;</td>
					<td class="receipt_amount_ugx" style="text-align:right;">&nbsp;</td>
				</tr>
				<tr class="summary-middle total_sum" style="font-size:12px;">
					<td style="border:0px">&nbsp;</td>
					<td class="receipt_instrument_empty" style="border:0px">&nbsp;</td>
					<td style="border:0px">&nbsp;</td>
					<td style="text-align:right;"><b>Total UGX:</b>&nbsp;</td>
					<td class="receipt_total_amount_ugx" style="text-align:right;"></td>
				</tr>
			</table>
		</center>
		
		<b style="font-size:12px;">A.&nbsp;&nbsp;<u class="purpose-title">Purpose of purchase</u></b>
		<span style="font-size:12px;" class="receipt_purpose"></span><br/><br/>
		
		<div style="font-size:12px;" >
			<b>
				<u class="particular-item">
					<div class="particular-item">Particulars of Buyer:</div>
				</u>
			</b>
			<br/>To be completed for transactions of US$5,000= and above or its equivalent
			<table width="100%" style="font-size:12px;" >
				<tr style="height:20px;font-size:12px;">
					<td width="25%" style="font-weight:bold">Name</td>
					<td ><span class="receipt_customer_name"></span></td> 
					<td width="30%" style="font-weight:bold">Address</td> 
					<td ><span class="receipt_customer_address"></span></td>
				</tr>
				<tr style="height:20px;font-size:12px;">
					<td style="font-weight:bold">Nationality</td>
					<td ><span class="receipt_customer_nationality"></span></td> 
					<td style="font-weight:bold;font-size:12px;">Passport/ID No.</td> 
					<td ><span class="receipt_customer_passport_number"></span></td>
				</tr>
				<tr style="height:20px;font-size:12px;">
					<td style="font-weight:bold">Customer signature</td>  
					<td ></td> 
					<td style="font-weight:bold;font-size:12px;">Dealer's signature &amp; stamp</td> 
					<td ></td>
				</tr>
				<tr >
					<td colspan="2">
						<span style="float:left;font-size:80%;">
							<b>Date:&nbsp;&nbsp;</b>
							<span class="datetime">.../.../...</span>
						</span>	
					</td>
					<td style="font-size:80%;"><b>Cashier:</b> <span class="cashier"></span></td>
					<td >
						<span style="float:right;font-weight:bold;font-size:80%;">
							Approved by Bank of Uganda
						</span>
					</td>
				</tr>
			</table>
		</div>
	</div><br/><br/><br/><br/><br/><br/>
	<div id="new-receipts" style="font-size:12px;">
		<center style="font-size:17px;">
			<?php 
				$name='';$location='';
				$sql1=$dbh->query("select name,location from foxes limit 1"); 
				if(count($sql1)){
					$ids="";
					foreach($sql1 as $row){
						$name = $row['name'];
						$location = $row['location'];
					}
				}
			?>
			<b style="font-size:17px"><?php echo $name;?></b><br/>
			<?php echo $location;?>
			<table width="65%" style="margin-left:180px">
				<tr>
					<td style="font-size:12px;">
						<b class="xchange">Foreign Exchange Inflows</b><br/>
					</td>
					<td style="font-size:12px;">
						<b class="receipt_type">Form P(cash sales)</b><br/>
					</td>
					<td style="font-size:12px;">
						<div style="float:right">
						<b>Receipt No: <span class="receipt_number"></span></b>
						</div>
					</td>
				</tr>
			</table>
		</center>
		<center>
			<table width="90%" class="summary" style="border-spacing:0;" >
				<tr style="font-size:12px;">
					<td style="text-align:center;"><b>Currency</b></td>
					<td class="receipt_instrument" style="text-align:center;"><b>Instrument</b></td>
					<td style="text-align:center;"><b>Amount</b></td>
					<td style="text-align:center;"><b>Rate</b></td>
					<td style="text-align:center;"><b>Amount in UShs.</b></td>
				</tr>
				<tr class="summary-middle" style="font-size:12px;">
					<td class="receipt_currency">&nbsp;</td>
					<td class="receipt_instrument receipt_instrument_data">&nbsp;</td>
					<td class="receipt_amount" style="text-align:right;">&nbsp;</td>
					<td class="receipt_rate" style="text-align:right;">&nbsp;</td>
					<td class="receipt_amount_ugx" style="text-align:right;">&nbsp;</td>
				</tr>
				<tr class="summary-middle total_sum" style="font-size:12px;">
					<td style="border:0px">&nbsp;</td>
					<td class="receipt_instrument_empty" style="border:0px">&nbsp;</td>
					<td style="border:0px">&nbsp;</td>
					<td style="text-align:right;"><b>Total UGX:</b>&nbsp;</td>
					<td class="receipt_total_amount_ugx" style="text-align:right;"></td>
				</tr>
			</table>
		</center>
		
		<b style="font-size:12px;">A.&nbsp;&nbsp;<u class="purpose-title">Purpose of purchase</u></b>
		<span style="font-size:12px;" class="receipt_purpose"></span>
		
		<div style="font-size:12px;" >
			<b>
				<u class="particular-item">
					<div class="particular-item">Particulars of Buyer:</div>
				</u>
			</b>
			To be completed for transactions of US$5,000= and above or its equivalent
			<table width="100%" style="font-size:12px;" >
				<tr style="height:15px;font-size:12px;">
					<td width="25%" style="font-weight:bold">Name</td>
					<td ><span class="receipt_customer_name"></span></td> 
					<td width="30%" style="font-weight:bold">Address</td> 
					<td ><span class="receipt_customer_address"></span></td>
				</tr>
				<tr style="height:15px;font-size:12px;">
					<td style="font-weight:bold">Nationality</td>
					<td ><span class="receipt_customer_nationality"></span></td> 
					<td style="font-weight:bold;font-size:12px;">Passport/ID No.</td> 
					<td ><span class="receipt_customer_passport_number"></span></td>
				</tr>
				<tr style="height:15px;font-size:12px;">
					<td style="font-weight:bold">Customer signature</td>  
					<td ></td> 
					<td style="font-weight:bold;font-size:12px;">Dealer's signature &amp; stamp</td> 
					<td ></td>
				</tr>
				<tr >
					<td colspan="2">
						<span style="float:left;font-size:80%;">
							<b>Date:&nbsp;&nbsp;</b>
							<span class="datetime">.../.../...</span>
						</span>	
					</td>
					<td style="font-size:70%;"><b>Cashier:</b> <span class="cashier"></span></td>
					<td >
						<span style="float:right;font-weight:bold;font-size:80%;">
							Approved by Bank of Uganda
						</span>
					</td>
				</tr>
			</table>
		</div>
	</div>
</body>
</html>