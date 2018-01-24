<?php
require "connection.php" ;
register_shutdown_function('handleShutdown');
ini_set('max_execution_time', 60);
$currencies_available=3;
?>

<?php
	$result="Local-Error:Data missing.";
	if(	isset($_REQUEST['company_id']) && !empty($_REQUEST['company_id']) &&
		isset($_REQUEST['currency_rates_buying']) && !empty($_REQUEST['currency_rates_buying']) &&
		isset($_REQUEST['currency_rates_selling']) && !empty($_REQUEST['currency_rates_selling']) &&
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
		
		$company_id=(int)$_REQUEST['company_id'];
		$lun=$_REQUEST['lun'];
		$luid=$_REQUEST['luid'];
		
		//Validation
		$currency_rates_buying=explode(',',$_REQUEST['currency_rates_buying']);
		$currency_rates_selling=explode(',',$_REQUEST['currency_rates_selling']);
		if(count($currency_rates_buying)!=$currencies_available){
			//some currency values are missing.
			//return
		}		
		if(count($currency_rates_selling)!=$currencies_available){
			//some currency values are missing.
			//return
		}
		
		$date=date('Y-m-d');
		$daily_return_id=(((string)$company_id).''.((string)date('Ymd')));
		
		$q=$dbh->query("select id from daily_returns where id='".$daily_return_id."'");
		if(count($q)){
			$result="Today's return Exists. Contact admin to edit it.";
		}else{
			@$dbh->query("INSERT into daily_returns(id,fox_id,date,daily_buying_return_id,daily_selling_return_id,user_id,name) 
				values('$daily_return_id','$company_id','$date','$daily_return_id','$daily_return_id','$luid','$lun')");		
			@$dbh->query("INSERT into daily_buying_returns(id,fox_id,daily_return_id,c1,c2,c3,c4,c5,c6,c7,c8,date) 
				values('$daily_return_id','$company_id','$daily_return_id',".($_REQUEST['currency_rates_buying']).",'$date')");
			@$dbh->query("INSERT into daily_selling_returns(id,fox_id,daily_return_id,c1,c2,c3,c4,c5,c6,c7,c8,date) 
				values('$daily_return_id','$company_id','$daily_return_id',".($_REQUEST['currency_rates_selling']).",'$date')");
			
			echo "Successfully saved locally!";exit;
		}
		
	}
	getOut:
	
	function handleShutdown(){
		echo $GLOBALS['result'];
	}
	
	function cSQL($data){
		return (strip_tags($data));
	}
?>