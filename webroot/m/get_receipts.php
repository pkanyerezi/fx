<?php
require "connection.php" ;
register_shutdown_function('handleShutdown');
ini_set('max_execution_time', 120);
$ip = $_SERVER['REMOTE_ADDR'];
$ip = str_replace('::1','127.0.0.1',$ip);
?>

<?php
	$json_result['Receipts']=array();
	while(1){
		$sql1=$dbh->query("SELECT * FROM sold_receipts WHERE status = 0 AND remote_addr='".$ip."'"); 
		if(count($sql1)){
			$ids="";
			$counter=0;
			foreach($sql1 as $row){
				if($counter==0){
					$ids.="'".$row['id']."'";
				}else{
					$ids.=",'".$row['id']."'";
				}
				$counter++;
				$row['reciept_type']='sold_receipts';
				$row['print_type']='single';
				array_push($json_result['Receipts'],$row);
			}
			if($counter>0){
				$sql2=$dbh->query("UPDATE sold_receipts SET status=1 WHERE id IN ($ids)");
				break;
			}
		}
		
		$sql2=$dbh->query("SELECT * FROM purchased_receipts WHERE status = 0 AND remote_addr='".$ip."'");
		if(count($sql2)){
			$ids="";
			$counter=0;
			foreach($sql2 as $row){
				if($counter==0){
					$ids.="'".$row['id']."'";
				}else{
					$ids.=",'".$row['id']."'";
				}
				$counter++;
				$row['reciept_type']='purchased_receipts';
				$row['print_type']='single';
				array_push($json_result['Receipts'],$row);
			}
			if($counter>0){
				$sql2=$dbh->query("UPDATE purchased_receipts SET status=1 WHERE id IN ($ids)");
				break;
			}
		}
		
		$sql3=$dbh->query("SELECT * FROM multiple_print_receipts WHERE was_printed = 0 AND remote_addr='".$ip."'");
		if(count($sql3)){
			foreach($sql3 as $row){
				$receipt_ids = explode(',',$row['receipts']);
				$counter=0;$ids="";
				foreach($receipt_ids as $receipt_id){
					if($counter==0){
						$ids.="'".$receipt_id."'";
					}else{
						$ids.=",'".$receipt_id."'";
					}
					$counter++;
				}
				
				
				$sql4=$dbh->query("SELECT * FROM ".$row['receipt_table']." WHERE id IN($ids)");
				if(count($sql4)){
					foreach($sql4 as $row1){
						$row1['receipt_table']=$row['receipt_table'];
						$row1['print_type']='multiple';
						$row1['reciept_type']=$row['receipt_table'];
						array_push($json_result['Receipts'],$row1);
					}
				}
				if($counter>0 and count($sql4)){
					$sql5=$dbh->query("UPDATE multiple_print_receipts SET was_printed=1");
					break;
				}
			}
			break;
		}
	}
	
	function handleShutdown(){
		$res = $GLOBALS['json_result'];
		echo json_encode($res);
	}
?>