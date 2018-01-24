<?php
require "connection.php" ;
register_shutdown_function('handleShutdown');
ini_set('max_execution_time', 60);
?>

<?php
	$result='Error:Error occured';
	if(isset($_REQUEST['company_id']) && !empty($_REQUEST['company_id'])){
		if(isset($_REQUEST['receipt_type'])){
			$receipt_type=(int)$_REQUEST['receipt_type'];//0-Sold Receipt, 1-Purchased Receipt
			
			$sql1 	= $dbh->query(sprintf("SELECT * FROM receipt_tracks WHERE id = '%d' limit 1",
						($_REQUEST['company_id'])));
			if(count($sql1)){
				foreach($sql1 as $row){
					$receipt_number=0;
					if(!$receipt_type){//if(Sold Receipt){
						$receipt_number=(($row['my_count_sold_receipts'])+1);
					}else{
						$receipt_number=(($row['my_count_purchased_receipts'])+1);
					}
					$result=$receipt_number;
				}
				
				if(!$receipt_type){//if(Sold Receipt)
					$dbh->query(sprintf("UPDATE receipt_tracks SET my_count_sold_receipts=my_count_sold_receipts+1 WHERE id = '%d' limit 1",
							($_REQUEST['company_id'])));
				}else{
					$dbh->query(sprintf("UPDATE receipt_tracks SET my_count_purchased_receipts=my_count_purchased_receipts+1 WHERE id = '%d' limit 1",
							($_REQUEST['company_id'])));
				}
			}else{
				$result='Error:Invalid Company ID provided';
			}
		}else{
			$result='Error:Unset receipt type';
		}
	}else{
		$result='Error:Company ID not found';
	}
	
	//$json_result="{'name':'Sally Smith'}";
	//echo json_encode($json_result['Receipts'][0]);
	
	function handleShutdown(){
		echo $GLOBALS['result'];
	}
?>