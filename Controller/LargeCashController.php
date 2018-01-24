<?php
App::uses('AppController', 'Controller');
class LargeCashController extends AppController {
	public $name = 'LargeCash';
	public $uses = array('PurchasedReceipt','SoldReceipt','Currency','PurchasedPurpose','Purpose');
	
	public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow(['large_cash_10_m']);
    }

	public function large_cash_10_m(){
		$large_amount=20000000;//Twenty Million
		$large_cash_records_limit = 10000;
		ignore_user_abort(1);
		ini_set('max_execution_time', 600);
		if(	isset($_REQUEST['date_from']) &&
			isset($_REQUEST['date_to'])){
			
			$from	=($_REQUEST['date_from']);
			$to	=($_REQUEST['date_to']);
			
			$this->set('from', $from);
			$this->set('to', $to);
			
			$large_purchased = $this->PurchasedReceipt->find('all',array(
				'conditions'=>array(
					'PurchasedReceipt.date >='=>$from,
					'PurchasedReceipt.date <='=>$to,
					'PurchasedReceipt.amount_ugx >='=>$large_amount,
					'PurchasedReceipt.purchased_purpose_id !='=>'p31',
				),
				'order' => 'PurchasedReceipt.date desc, PurchasedReceipt.t_time desc',
				'limit'=>$large_cash_records_limit
			));
			
			$nxt_limit = ($large_cash_records_limit - count($large_purchased));
			$large_sold = array();
			if($nxt_limit>0){
				$large_sold = $this->SoldReceipt->find('all',array(
					'conditions'=>array(
						'SoldReceipt.date >='=>$from,
						'SoldReceipt.date <='=>$to,
						'SoldReceipt.amount_ugx >='=>$large_amount,
					),
					'order' => 'SoldReceipt.date desc, SoldReceipt.t_time desc',
					'limit'=>$nxt_limit
				));
			}
						
			App::import('Vendor', 'PHPExcel/PHPExcel');
			$objPHPexcel = PHPExcel_IOFactory::load('ExcelFiles/templates/template_forex_bureaux_and_money_remitters.xls');
			$objWorksheet = $objPHPexcel->getSheet(0);
			
			$objWorksheet->getCell('I14')->setValue($from . ' - '. $to);
			$objWorksheet->getCell('I15')->setValue(date('Y-m-d'));
			
			$counter = 17;
			$_fox=($this->Session->read('fox'));
			$branch_name = $_fox['Fox']['name'];
			
			//Purchased Receipts
			$record_counter=1;
			foreach($large_purchased as $row){
				$counter++;
				$objWorksheet->getCell('A'.$counter)->setValue($record_counter);//Record
				$objWorksheet->getCell('B'.$counter)->setValue($branch_name);//BranchName
				$objWorksheet->getCell('C'.$counter)->setValue($row['PurchasedReceipt']['customer_name']);

				if(empty($row['PurchasedReceipt']['nationality'])){
					$row['PurchasedReceipt']['nationality'] = 'UGANDAN';
				}
				$row['PurchasedReceipt']['nationality'] = strtoupper($row['PurchasedReceipt']['nationality']);
				$objWorksheet->getCell('D'.$counter)->setValue($row['PurchasedReceipt']['nationality']);
				
				$IDTYPE = 'ID';
				$pass = trim($row['PurchasedReceipt']['passport_number']);
				if(!empty($pass) && strtoupper($pass[0])=='B'){$IDTYPE = 'Passport';}
				$objWorksheet->getCell('E'.$counter)->setValue($IDTYPE);
				
				$objWorksheet->getCell('F'.$counter)->setValue($row['PurchasedReceipt']['date']);
				$objWorksheet->getCell('G'.$counter)->setValue('Buy');
				
				$currency = '';
				if($row['PurchasedReceipt']['currency_id']=='c8'){
					$currency = strtoupper($row['PurchasedReceipt']['other_name']);
				}else{
					$currency = $row['Currency']['description'];
				}
				
				$objWorksheet->getCell('H'.$counter)->setValue($currency);
				$objWorksheet->getCell('I'.$counter)->setValue($row['PurchasedReceipt']['amount']);
				$objWorksheet->getCell('J'.$counter)->setValue($row['PurchasedReceipt']['amount_ugx']);
				$objWorksheet->getCell('K'.$counter)->setValue($row['PurchasedPurpose']['description']);
				$objWorksheet->getCell('L'.$counter)->setValue('');
				$record_counter++;
			}
			
			//Sold receipts
			foreach($large_sold as $row){
				$counter++;
				$objWorksheet->getCell('A'.$counter)->setValue($record_counter);//Record
				$objWorksheet->getCell('B'.$counter)->setValue($branch_name);//BranchName
				$objWorksheet->getCell('C'.$counter)->setValue($row['SoldReceipt']['customer_name']);
				
				if(empty($row['SoldReceipt']['nationality'])){
					$row['SoldReceipt']['nationality'] = 'UGANDAN';
				}
				$row['SoldReceipt']['nationality'] = strtoupper($row['SoldReceipt']['nationality']);
				$objWorksheet->getCell('D'.$counter)->setValue($row['SoldReceipt']['nationality']);

				$IDTYPE = $row['SoldReceipt']['passport_number'];
				$pass = trim($row['SoldReceipt']['passport_number']);
				if(!empty($pass) && strtoupper($pass[0])=='B'){$IDTYPE = 'Passport';}
				$objWorksheet->getCell('E'.$counter)->setValue($IDTYPE);
				
				$objWorksheet->getCell('F'.$counter)->setValue($row['SoldReceipt']['date']);
				$objWorksheet->getCell('G'.$counter)->setValue('Sale');
				
				$currency = '';
				if($row['SoldReceipt']['currency_id']=='c8'){
					$currency = strtoupper($row['SoldReceipt']['other_name']);
				}else{
					$currency = $row['Currency']['description'];
				}
				
				$objWorksheet->getCell('H'.$counter)->setValue($currency);
				$objWorksheet->getCell('I'.$counter)->setValue($row['SoldReceipt']['amount']);
				$objWorksheet->getCell('J'.$counter)->setValue($row['SoldReceipt']['amount_ugx']);
				$objWorksheet->getCell('K'.$counter)->setValue($row['Purpose']['description']);
				$objWorksheet->getCell('L'.$counter)->setValue('');
				$record_counter++;
			}
			
			
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPexcel, 'Excel5');

			if (isset($_REQUEST['apiRequest'])) {
				$newFileName = "ExcelFiles/templates/apirequest_FIA_large_cash_" . $_REQUEST['apiRequest'] . ".xls"; 
			} else {
				$newFileName = 'ExcelFiles/templates/FIA_large_cash_'.((isset($u['User']['name']))?$u['User']['name']:$this->Auth->User('name')).".xls"; 
			}
			
			$objWriter->save($newFileName);

			if(isset($_REQUEST['apiRequest'])) {
				echo json_encode([
					'apiRequest'=>$_REQUEST['apiRequest'],
					'filename'=>$newFileName
				]);
				exit();
			}
			$this->redirect('http://' . $this->downloadsIp . '/fx/'.$newFileName);
		}
		echo 'NaN';
		exit;
	}
	
}
