<?php
require "connection.php" ;
register_shutdown_function('handleShutdown');
ini_set('max_execution_time', 60);
?>

<?php
	$result="Local-Error:Data missing.";
	if(	isset($_REQUEST['receipt_number']) && !empty($_REQUEST['receipt_number']) &&
		isset($_REQUEST['company_id']) && !empty($_REQUEST['company_id']) &&
		isset($_REQUEST['amount']) && !empty($_REQUEST['amount']) &&
		isset($_REQUEST['purpose_id']) && !empty($_REQUEST['purpose_id']) &&
		isset($_REQUEST['rate']) && !empty($_REQUEST['rate']) &&
		isset($_REQUEST['amount_ugx']) && !empty($_REQUEST['amount_ugx']) &&
		isset($_REQUEST['currency_id']) && !empty($_REQUEST['currency_id']) &&
		isset($_REQUEST['instrument']) && !empty($_REQUEST['instrument']) &&
		isset($_REQUEST['lun']) && !empty($_REQUEST['lun']) &&
		isset($_REQUEST['luid']) && !empty($_REQUEST['luid'])
		){
		$result='';
		//val tym
		
		$qq=$dbh->query("SELECT prev_d,weekends FROM foxes where id = '9265236542' limit 1");
		if(count($qq)>0){
			foreach($qq as $row){
				if(isset($row['prev_d']) and isset($row['weekends'])){					
					//Det val
					$ts1 = strtotime(date('Y-m-d'));$ts2 = strtotime($row['prev_d']);$seconds_diff = $ts2 - $ts1;
					if($seconds_diff>0){echo "WARNING:Invalid system date. Correct it to continue. Thanks.";exit;}
					
					//Val wkends
					$weekends=explode(',',$row['weekends']);
					foreach($weekends as $weekend){
						if($ts1==strtotime($weekend)){echo "WARNING:Its a weekend.";exit;}
					}
				}else{echo "ERROR:DATE FIELD NOT FOUND";exit;}
			}			
		}else{echo "ERROR:DATE NOT FOUND";exit;}
		
		$receipt_id=trim($_REQUEST['receipt_number']);
		$customer_name=$_REQUEST['customer_name'];
		$amount=(double)$_REQUEST['amount'];
		$company_id=(int)$_REQUEST['company_id'];
		
		$purpose_id=$_REQUEST['purpose_id'];
		$rate=(double)$_REQUEST['rate'];
		$amount_ugx=(double)$_REQUEST['amount_ugx'];
		$currency_id=$_REQUEST['currency_id'];
		$instrument=$_REQUEST['instrument'];
		
		$nationality=$_REQUEST['nationality'];
		$address=$_REQUEST['address'];
		$passport_number=$_REQUEST['passport_number'];
		
		$lun=$_REQUEST['lun'];
		$luid=$_REQUEST['luid'];
		
		$date=date('Y-m-d');
		
		$orig_amount=0;
		$orig_rate=0;
		$other_currency_name='';
		$other_currency_id='';
		
		//for others
		if($_REQUEST['currency_id']=='c8'){
			$orig_amount=$amount;
			$orig_rate=$rate;
			$rate=1000;//fixed others rate
			$amount=0;
			@$amount=($orig_amount*$orig_rate)/$rate;

			//Set the correct customer_name and other_currency name
			$res=explode('_',$customer_name);
			$count_res=count($res);
			
			$customer_name=trim($res[0]);
			$other_currency_id=strtoupper(trim($res[$count_res-1]));	
			
			$qx=$dbh->query(sprintf("SELECT id, name FROM other_currencies where id = '%s' limit 1",$other_currency_id));
			if(count($qx)>0){
				foreach($qx as $row){
					$other_currency_name=$row['name'];
				}
			}else{
				echo 'Other-Currency ID not found. Provide a correct one.';exit;
			}
		}
		
		$status=0;
		if(isset($_REQUEST['save_only']))
			$status=1;
		
		$q=$dbh->query(sprintf("SELECT COUNT(*) as c FROM sold_receipts where id = '%s' limit 1",$receipt_id));
		if(count($q)>0){
			foreach($q as $row){
				if($row['c']){
					$dbh->query(sprintf("UPDATE sold_receipts set status='$status' where id = '%s' limit 1",$receipt_id));
					if(isset($_REQUEST['save_only'])){
						echo "Receipt saved.";exit;
					}else{
						echo "Re-printing...";exit;
					}
					goto getOut;				
				}
			}			
		}
		
		@$dbh->query(sprintf("INSERT into sold_receipts(id,fox_id,customer_name,amount,purpose_id,rate,amount_ugx,currency_id,instrument,date,nationality,address,passport_number,status,user_id,name,orig_amount,orig_rate,other_name,other_currency_id) 
			values('$receipt_id','%d','%s','$amount','%s','$rate','$amount_ugx','%s','%s','$date','%s','%s','%s','$status','%s','%s','$orig_amount','$orig_rate','$other_currency_name','$other_currency_id')",
			cSQL($company_id),cSQL($customer_name),cSQL($purpose_id),cSQL($currency_id),cSQL($instrument),cSQL($nationality),cSQL($address),cSQL($passport_number),cSQL($luid),cSQL($lun)));
			
			
		echo "Success:Receipt Sent Locally!";exit;
		
	}
	
	getOut:
	
	function handleShutdown(){
		echo $GLOBALS['result'];
	}
	
	function cSQL($data){
		return (strip_tags($data));
	}
?>