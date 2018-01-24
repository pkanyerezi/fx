<?php
App::uses('AppController', 'Controller');
/**
 * SoldReceipts Controller
 *
 * @property SoldReceipt $SoldReceipt
 */
class SoldReceiptsController extends AppController {
	public $uses =array('SoldReceipt','PurchasedReceipt','MultiplePrintReceipt','TtAccount','Opening');
		
	public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow(['excel_sales','excel_large_cash','get_excel_large_cash']);
    }
    
    public function excel_sales(){
		ignore_user_abort(1);
		ini_set('max_execution_time', 600);
		if(	isset($_REQUEST['date_from']) &&
			isset($_REQUEST['date_to'])){
			
			$from	=($_REQUEST['date_from']);
			$to	=($_REQUEST['date_to']);
			
			$this->set('from', $from);
			$this->set('to', $to);
			
			$sales = $this->SoldReceipt->find('all',array(
				'conditions'=>array(
					'SoldReceipt.date >='=>$from,
					'SoldReceipt.date <='=>$to,
				),
				'order' => 'SoldReceipt.date desc, SoldReceipt.t_time desc',
				'limit'=>0
			));
			
			App::import('Vendor', 'PHPExcel/PHPExcel');
			$objPHPexcel = PHPExcel_IOFactory::load('ExcelFiles/templates/receipts.xls');
			$objWorksheet = $objPHPexcel->getSheet(0);
			
			
			$counter = 5;
			$_fox=($this->Session->read('fox'));
			$branch_name = $_fox['Fox']['name'];
			
			$objWorksheet->getCell('D1')->setValue($branch_name);
			$objWorksheet->getCell('D2')->setValue('Sales Receipts');
			$objWorksheet->getCell('D3')->setValue('From '.$from . ' to '. $to);
			
			//Sold Receipts
			$record_counter=1;
			$objWorksheet->getCell('A'.$counter)->setValue('Receipt Number');
			$objWorksheet->getCell('B'.$counter)->setValue('Amount');
			$objWorksheet->getCell('C'.$counter)->setValue('Rate');
			$objWorksheet->getCell('D'.$counter)->setValue('UGX');
			$objWorksheet->getCell('E'.$counter)->setValue('Currency');
			$objWorksheet->getCell('F'.$counter)->setValue('Date');
			$objWorksheet->getCell('G'.$counter)->setValue('Time');
			foreach($sales as $row){
				$counter++;
				$objWorksheet->getCell('A'.$counter)->setValue("'".$row['SoldReceipt']['id']."'");//Record
				$currency = '';
				if($row['SoldReceipt']['currency_id']=='c8'){
					$currency = strtoupper($row['SoldReceipt']['other_name']);
					$rate = $row['SoldReceipt']['orig_rate'];
					$amount = $row['SoldReceipt']['orig_amount'];
				}else{
					$currency = $row['Currency']['id'];
					$rate = $row['SoldReceipt']['rate'];
					$amount = $row['SoldReceipt']['amount'];
				}
				$objWorksheet->getCell('B'.$counter)->setValue($amount);
				$objWorksheet->getCell('C'.$counter)->setValue($rate);
				$objWorksheet->getCell('D'.$counter)->setValue($row['SoldReceipt']['amount_ugx']);
				$objWorksheet->getCell('E'.$counter)->setValue($currency);
				$objWorksheet->getCell('F'.$counter)->setValue($row['SoldReceipt']['date']);
				$objWorksheet->getCell('G'.$counter)->setValue($row['SoldReceipt']['t_time']);
				$record_counter++;
			}
			
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPexcel, 'Excel5');

			if (isset($_REQUEST['apiRequest'])) {
				$newFileName = "ExcelFiles/templates/apirequest_sales_" . $_REQUEST['apiRequest'] . ".xls"; 
			} else {
				$newFileName = 'ExcelFiles/templates/sales'.((isset($u['User']['name']))?$u['User']['name']:$this->Auth->User('name')).".xls";
			}
			
			$objWriter->save($newFileName);

			if(isset($_REQUEST['apiRequest'])) {
				echo json_encode([
					'apiRequest'=>$_REQUEST['apiRequest'],
					'filename'=>$newFileName
				]);
				exit();
			}	
			// $this->downloadsIp = '';
			// http://localhost/fx/sold_receipts/excel_sales?date_from=2016-10-04&date_to=2016-10-04
			// 192.168.1.105
			$this->redirect('http://'.$this->downloadsIp.'/fx/'.$newFileName);
		}else{
			echo 'Select a date range';
		}
		exit;
	}
    
    //current
    public function excel_large_cash(){
    	$from	=(isset($_REQUEST['date_from']))?$_REQUEST['date_from']:date('2012-m-d');
		$to		=(isset($_REQUEST['date_to']))?$_REQUEST['date_to']:date('2016-m-d');
		
		//get Average Rate for Dollar
		$dollar_av_rate=$this->SoldReceipt->find('all',array(
			'recursive'=>-1,
			'conditions'=>array(
				'SoldReceipt.currency_id'=>'USD',
				'SoldReceipt.date >='=>$from,
				'SoldReceipt.date <='=>$to,
				//'SoldReceipt.fox_id'=>$this->Auth->User('fox_id')
			),
			'fields'=>array(
				'SUM(SoldReceipt.amount) as total_amount',
				'SUM(SoldReceipt.amount_ugx) as total_amount_ugx'
			)
		));
			
		if(isset($dollar_av_rate[0][0]['total_amount_ugx'])){
			$dollar_av_rate=($dollar_av_rate[0][0]['total_amount_ugx']!=0)?($dollar_av_rate[0][0]['total_amount_ugx']/$dollar_av_rate[0][0]['total_amount']):2400;
		}else{
			$dollar_av_rate=300;
		}
	
		$max_dollar_ugx=5000*$dollar_av_rate;
		
		
		$soldReceipts=$this->SoldReceipt->find('all',array(
			'conditions'=>array(
				'SoldReceipt.date >='=>$from,
				'SoldReceipt.date <='=>$to,
				'SoldReceipt.amount_ugx >='=>$max_dollar_ugx,
				//'SoldReceipt.fox_id'=>$this->Auth->User('fox_id')
			),
			'order' => 'SoldReceipt.date desc',
			'limit'=>0,
			'fields'=>array(
				'SoldReceipt.id',
				'SoldReceipt.date',
				'SoldReceipt.customer_name',
				'SoldReceipt.amount',
				'Currency.description',
				'OtherCurrency.name',
				'SoldReceipt.instrument',
			)
		));
		
		//get Average Rate for Dollar
		$dollar_av_rate=$this->PurchasedReceipt->find('all',array(
			'recursive'=>-1,
			'conditions'=>array(
				'PurchasedReceipt.currency_id'=>'USD',
				'PurchasedReceipt.date >='=>$from,
				'PurchasedReceipt.date <='=>$to,
				//'SoldReceipt.fox_id'=>$this->Auth->User('fox_id')
			),
			'fields'=>array(
				'SUM(PurchasedReceipt.amount) as total_amount',
				'SUM(PurchasedReceipt.amount_ugx) as total_amount_ugx'
			)
		));
			
		if(isset($dollar_av_rate[0][0]['total_amount_ugx'])){
			$dollar_av_rate=($dollar_av_rate[0][0]['total_amount_ugx']!=0)?($dollar_av_rate[0][0]['total_amount_ugx']/$dollar_av_rate[0][0]['total_amount']):2400;
		}else{
			$dollar_av_rate=300;
		}
	
		$max_dollar_ugx=5000*$dollar_av_rate;
		
		$purchasedReceipts=$this->PurchasedReceipt->find('all',array(
			'conditions'=>array(
				'PurchasedReceipt.date >='=>$from,
				'PurchasedReceipt.date <='=>$to,
				'PurchasedReceipt.amount_ugx >='=>$max_dollar_ugx,
				//'PurchasedReceipt.fox_id'=>$this->Auth->User('fox_id')
			),
			'order' => 'PurchasedReceipt.date desc',
			'limit'=>0,
			'fields'=>array(
				'PurchasedReceipt.id',
				'PurchasedReceipt.date',
				'PurchasedReceipt.customer_name',
				'PurchasedReceipt.amount',
				'Currency.description',
				'OtherCurrency.name',
				'PurchasedReceipt.instrument'
			)
		));
		
		App::import('Vendor', 'PHPExcel/PHPExcel');
		$objPHPexcel = PHPExcel_IOFactory::load('ExcelFiles/templates/large_cash.xls');
		$objWorksheet = $objPHPexcel->getSheet();//Get the SALES SHEET
		
		//Data
		$objWorksheet->setCellValueByColumnAndRow(1, 4, date('d'));
		$objWorksheet->setCellValueByColumnAndRow(1, 5, date('m'));
		$objWorksheet->setCellValueByColumnAndRow(1, 6, date('Y',strtotime($to)));
		$objWorksheet->setCellValueByColumnAndRow(5, 5, $from);
		$objWorksheet->setCellValueByColumnAndRow(5, 6, $to);
		
		$row=14;
		foreach($soldReceipts as $soldReceipt){
			$col=1;//move to the start column
			$objWorksheet->setCellValueByColumnAndRow($col, $row, $soldReceipt['SoldReceipt']['date']);$col++;
			$objWorksheet->setCellValueByColumnAndRow($col, $row, $soldReceipt['SoldReceipt']['customer_name']);$col++;
			$objWorksheet->setCellValueByColumnAndRow($col, $row, 'P');$col++;
			if(strlen($soldReceipt['OtherCurrency']['name']) && ($soldReceipt['OtherCurrency']['name'])!=null){
				$objWorksheet->setCellValueByColumnAndRow($col, $row, $soldReceipt['OtherCurrency']['name']);$col++;
			}else{
				$objWorksheet->setCellValueByColumnAndRow($col, $row, $soldReceipt['Currency']['description']);$col++;
			}
			$objWorksheet->setCellValueByColumnAndRow($col, $row, $soldReceipt['SoldReceipt']['amount']);$col++;
			$objWorksheet->setCellValueByColumnAndRow($col, $row, $soldReceipt['SoldReceipt']['instrument']);$col++;
			$row++;//move to the next row
		}
		
		foreach($purchasedReceipts as $purchasedReceipt){
			$col=1;//move to the start column
			$objWorksheet->setCellValueByColumnAndRow($col, $row, $purchasedReceipt['PurchasedReceipt']['date']);$col++;
			$objWorksheet->setCellValueByColumnAndRow($col, $row, $purchasedReceipt['PurchasedReceipt']['customer_name']);$col++;
			$objWorksheet->setCellValueByColumnAndRow($col, $row, 'R');$col++;
			if(strlen($purchasedReceipt['OtherCurrency']['name']) && ($purchasedReceipt['OtherCurrency']['name'])!=null){
				$objWorksheet->setCellValueByColumnAndRow($col, $row, $purchasedReceipt['OtherCurrency']['name']);$col++;
			}else{
				$objWorksheet->setCellValueByColumnAndRow($col, $row, $purchasedReceipt['Currency']['description']);$col++;
			}
			$objWorksheet->setCellValueByColumnAndRow($col, $row, $purchasedReceipt['PurchasedReceipt']['amount']);$col++;
			@$instrument = (($purchasedReceipt['PurchasedReceipt']['instrument'])=='TT')?'T.C':'Cash';
			$instrument = (empty($instrument))?'Cash':$instrument;
			$objWorksheet->setCellValueByColumnAndRow($col, $row, $instrument);$col++;
			$row++;//move to the next row*/
		}
		
		
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPexcel, 'Excel5');

		if (isset($_REQUEST['apiRequest'])) {
			$newFileName = "ExcelFiles/templates/apirequest_large_cash_" . $_REQUEST['apiRequest'] . ".xls"; 
		} else {
			$newFileName = 'ExcelFiles/templates/large_cash_'.((isset($u['User']['name']))?$u['User']['name']:$this->Auth->User('name')).".xls";
		}
		
		$objWriter->save($newFileName);

		if(isset($_REQUEST['apiRequest'])) {
			echo json_encode([
				'apiRequest'=>$_REQUEST['apiRequest'],
				'filename'=>$newFileName
			]);
			exit();
		}
		
		$this->redirect('http://'.$this->downloadsIp.'/fx/'.$newFileName);
    }

    public function get_excel_large_cash(){
    	$from	=(isset($_REQUEST['date_from']))?$_REQUEST['date_from']:date('Y-m-d');
		$to		=(isset($_REQUEST['date_to']))?$_REQUEST['date_to']:date('Y-m-d');
		
		//get Average Rate for Dollar
		$dollar_av_rate=$this->SoldReceipt->find('all',array(
			'recursive'=>-1,
			'conditions'=>array(
				'SoldReceipt.currency_id'=>'USD',
				'SoldReceipt.date >='=>$from,
				'SoldReceipt.date <='=>$to,
				//'SoldReceipt.fox_id'=>$this->Auth->User('fox_id')
			),
			'fields'=>array(
				'SUM(SoldReceipt.amount) as total_amount',
				'SUM(SoldReceipt.amount_ugx) as total_amount_ugx'
			)
		));
			
		if(isset($dollar_av_rate[0][0]['total_amount_ugx'])){
			$dollar_av_rate=($dollar_av_rate[0][0]['total_amount_ugx']!=0)?($dollar_av_rate[0][0]['total_amount_ugx']/$dollar_av_rate[0][0]['total_amount']):2400;
		}else{
			$dollar_av_rate=300;
		}
	
		$max_dollar_ugx=5000*$dollar_av_rate;
		
		
		$soldReceipts=$this->SoldReceipt->find('all',array(
			'conditions'=>array(
				'SoldReceipt.date >='=>$from,
				'SoldReceipt.date <='=>$to,
				'SoldReceipt.amount_ugx >='=>$max_dollar_ugx,
				//'SoldReceipt.fox_id'=>$this->Auth->User('fox_id')
			),
			'order' => 'SoldReceipt.date desc',
			'limit'=>0,
			'fields'=>array(
				'SoldReceipt.id',
				'SoldReceipt.date',
				'SoldReceipt.customer_name',
				'SoldReceipt.amount',
				'Currency.description',
				'OtherCurrency.name',
				'SoldReceipt.instrument',
			)
		));
		
		//get Average Rate for Dollar
		$dollar_av_rate=$this->PurchasedReceipt->find('all',array(
			'recursive'=>-1,
			'conditions'=>array(
				'PurchasedReceipt.currency_id'=>'USD',
				'PurchasedReceipt.date >='=>$from,
				'PurchasedReceipt.date <='=>$to,
				//'SoldReceipt.fox_id'=>$this->Auth->User('fox_id')
			),
			'fields'=>array(
				'SUM(PurchasedReceipt.amount) as total_amount',
				'SUM(PurchasedReceipt.amount_ugx) as total_amount_ugx'
			)
		));
			
		if(isset($dollar_av_rate[0][0]['total_amount_ugx'])){
			$dollar_av_rate=($dollar_av_rate[0][0]['total_amount_ugx']!=0)?($dollar_av_rate[0][0]['total_amount_ugx']/$dollar_av_rate[0][0]['total_amount']):2400;
		}else{
			$dollar_av_rate=300;
		}
	
		$max_dollar_ugx=5000*$dollar_av_rate;
		
		$purchasedReceipts=$this->PurchasedReceipt->find('all',array(
			'conditions'=>array(
				'PurchasedReceipt.date >='=>$from,
				'PurchasedReceipt.date <='=>$to,
				'PurchasedReceipt.amount_ugx >='=>$max_dollar_ugx,
				//'PurchasedReceipt.fox_id'=>$this->Auth->User('fox_id')
			),
			'order' => 'PurchasedReceipt.date desc',
			'limit'=>0,
			'fields'=>array(
				'PurchasedReceipt.id',
				'PurchasedReceipt.date',
				'PurchasedReceipt.customer_name',
				'PurchasedReceipt.amount',
				'Currency.description',
				'OtherCurrency.name',
			)
		));
			
		App::import('Vendor', 'PHPExcel/PHPExcel');
		$objPHPExcel = new PHPExcel();
		// Add data
		$objPHPExcel->setActiveSheetIndex(0);
		//SET HEADER
		//@param $col,$row,$data
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, 1, 'To: Director');
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, 2, 'Non Banking Financial Institutions Department');
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, 3, 'P. O. Box 7120, Kampala');
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, 4, 'Tel: 256 041 258441/6, 256 041 234652');
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, 5, 'Fax: 256 041 258739');
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, 6, 'Telex: 256 041 61059');
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, 7, 'CABLES: UGABANK');
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, 8, 'Website: www.bou.or.ug');
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, 10, 'From: Name of Authorised Dealer');
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, 11, '                                   ');
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, 12, '.................................................');
		
		
		
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, 14, 'LARGE CASH TRANSACTION REPORT');
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, 15, '(Stricktly Confidential)');
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, 17, 'Date due(First working day of the week)');
		
		//Data header
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, 20, 'Transaction Date');
		//$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, 20, 'Receipt Number');
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1, 20, 'Name of Customer');
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, 20, 'Nature of transaction Either R or P');
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, 20, 'Currency');
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4, 20, 'Amount');
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(5, 20, 'Form of transaction *');
		
		//Data
		$row=21;
		foreach($soldReceipts as $soldReceipt){
			$col=0;//move to the start column
			
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $soldReceipt['SoldReceipt']['date']);$col++;
			
			//$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $soldReceipt['SoldReceipt']['id']);$col++;
			
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $soldReceipt['SoldReceipt']['customer_name']);$col++;
			
			
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'P');$col++;
			
			if(strlen($soldReceipt['OtherCurrency']['name']) && ($soldReceipt['OtherCurrency']['name'])!=null){
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $soldReceipt['OtherCurrency']['name']);$col++;
			}else{
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $soldReceipt['Currency']['description']);$col++;
			}
			
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $soldReceipt['SoldReceipt']['amount']);$col++;
			
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $soldReceipt['SoldReceipt']['instrument']);$col++;
			
			$row++;//move to the next row
		}
		
		foreach($purchasedReceipts as $purchasedReceipt){
			$col=0;//move to the start column
			
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $purchasedReceipt['PurchasedReceipt']['date']);$col++;
			
			//$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $purchasedReceipt['PurchasedReceipt']['id']);$col++;
			
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $purchasedReceipt['PurchasedReceipt']['customer_name']);$col++;
			
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'R');$col++;
			
			if(strlen($purchasedReceipt['OtherCurrency']['name']) && ($purchasedReceipt['OtherCurrency']['name'])!=null){
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $purchasedReceipt['OtherCurrency']['name']);$col++;
			}else{
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $purchasedReceipt['Currency']['description']);$col++;
			}
			
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $purchasedReceipt['PurchasedReceipt']['amount']);$col++;
			
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Cash');$col++;
			$row++;//move to the next row
		}
		
		$row+=3;
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, 'Has any person(s)/law enforcement agency already been contacted by telephone, written communication, ');$row++;
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, 'or otherwise?(Yes/No):.............................. Who made the contact:....................................................');$row++;
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, 'If so, name the person(c)/Law Enforcement Agency contacted:...................................................................');$row++;
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, '');$row++;
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, 'Reported by:...................................... Signature:.............................. Posifiers:........................');$row++;
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, '');$row++;
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, '');$row++;
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, 'Date:........................');$row++;
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, '');$row++;
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, 'R = Purchases');$row++;
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, 'P = Sales');$row++;
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, $row, '* Form of transaction eg cas, travellers cheques, etc');$row++;
		// Rename sheet
		$objPHPExcel->getActiveSheet()->setTitle('Sales Large Cash');		
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');	

		if (isset($_REQUEST['apiRequest'])) {
			$newFileName = "ExcelFiles/templates/apirequest_sales_large_cash_" . $_REQUEST['apiRequest'] . ".xls"; 
		} else {
			$newFileName = 'ExcelFiles/sales_large_cash'.($this->Auth->User('name'))."_".($this->Auth->User('fox_id')).".xls"; 
		}
		
		$objWriter->save($newFileName);

		if(isset($_REQUEST['apiRequest'])) {
			echo json_encode([
				'apiRequest'=>$_REQUEST['apiRequest'],
				'filename'=>$newFileName
			]);
			exit();
		}
			
		$this->redirect('http://'.$this->downloadsIp.'/fx/'.$newFileName);
		exit;
    }
    	
	function print_receipt($id){
		if (!$this->SoldReceipt->exists($id)) {
			throw new NotFoundException(__('Invalid sales receipt'));
		}
		
		$this->request->data['SoldReceipt']['id']=$id;
		$this->request->data['SoldReceipt']['status']=0;
		//Set the remote address for this PC incase the printing will be done from this PC
		if($this->Auth->User('printing_place')==2){
			$this->request->data['SoldReceipt']['remote_addr'] = str_replace('::1','127.0.0.1',$_SERVER['REMOTE_ADDR']);
		}
		if ($this->SoldReceipt->save($this->request->data)) {
				$this->set('resp','Sent for printing.');
		} else {
			$this->set('resp','Not Sent for printing.');
		}
	}
	
	function should_upload($id,$indicator){
		if (!$this->SoldReceipt->exists($id)) {
			throw new NotFoundException(__('Invalid sales receipt'));
		}
		
		$this->request->data['SoldReceipt']['id']=$id;
		if($indicator==0 || $indicator==1){
			$this->request->data['SoldReceipt']['is_uploaded']=$indicator;
		}
		$this->SoldReceipt->save($this->request->data);		
		$this->redirect(array('action'=>'index'));
	}
	
	public function upload(){
		$options = array('conditions' => array('SoldReceipt.is_uploaded'=>0));
		$this->set('receipt_count', $this->SoldReceipt->find('count', $options));
	}
	
	public function get_new_receipts_count(){
		$this->set('count_new_receipts',$this->SoldReceipt->find('count',array('conditions'=>array('SoldReceipt.is_uploaded'=>0))));
	} 
	
	public function send_new_receipts(){
		$SoldReceipts=$this->SoldReceipt->find('all',array('recursive'=>-1,'limit'=>1000,'conditions'=>array('SoldReceipt.is_uploaded'=>0)));
		$resting=new $this->Resting;
		$_fox=($this->Session->read('fox'));
		$resting->api_username=$_fox['Fox']['un'];
		$resting->api_password=$_fox['Fox']['pwd'];
		$resting->authorisation_key=$_fox['Fox']['k'];
		$resting->url = $_fox['Fox']['url'];
		$response=$resting->XML_fetch_data('/sold_receipts/fox_add.json','<Receipts>'.(json_encode($SoldReceipts)).'</Receipts>');
		if($resting->has_response){
			$response_array=json_decode($response);
			if(isset($response_array->data->response->saved_string)){
				if(strlen($response_array->data->response->saved_string)){
					@$this->SoldReceipt->query('UPDATE sold_receipts set is_uploaded=1 where id in ('.($response_array->data->response->saved_string).')');
				}
			}else{
				echo "Error:Receipt could not be saved online! Access denied";
			}
		}else{
			pr("could not communicate with BOU/ Check your internet connection");
		}
		sleep(1);
	}
	
	//Command to send large_cash
	public function send_sales_large_cash(){
		ignore_user_abort(1);
		ini_set('max_execution_time', 600);
		
		if(	isset($_REQUEST['date_from']) &&
			isset($_REQUEST['date_to'])){
			$from	=($_REQUEST['date_from']);
			$to	=($_REQUEST['date_to']);
			
			$this->set('from', $from);
			$this->set('to', $to);
			
			$SoldReceiptsCount=$this->SoldReceipt->find('count',array('conditions'=>array('SoldReceipt.is_uploaded'=>0)));
			/*$PurchasedReceiptsCount=$this->PurchasedReceipt->find('count',array('conditions'=>array('PurchasedReceipt.is_uploaded'=>0)));
			*/
			if($SoldReceiptsCount){
				$this->Session->setFlash(__("Warning:".($SoldReceiptsCount).' sales receipt(s) not uploaded yet. Please upload to continue.'),'flash_warning');
				return;
			}
			
			/*
			if($PurchasedReceiptsCount){
				$this->Session->setFlash(__("Warning:".($PurchasedReceiptsCount).' purchase receipt(s) not uploaded yet. Please upload to continue.'));
				return;
			}*/
			
			
			$resting=new $this->Resting;
			$_fox=($this->Session->read('fox'));
			$resting->api_username=$_fox['Fox']['un'];
			$resting->api_password=$_fox['Fox']['pwd'];
			$resting->authorisation_key=$_fox['Fox']['k'];
			$resting->url = $_fox['Fox']['url'];
			$LargeCash['LargeCash']['date_from']	=$from;
			$LargeCash['LargeCash']['date_to']	=$to;
			
			$msgs='as';		
			$start_time=date('Y-m-d H:i:s');
			$response=$resting->XML_fetch_data('/sold_receipts/fox_send_sales_large_cash.json','<LargeCash>'.(json_encode($LargeCash)).'</LargeCash>');
			if($resting->has_response){
				$this->log($response);
				$response_array_full=json_decode($response);
				$response_array=array();
				if(isset($response_array_full->data->response->msgs)){
					$response_array=$response_array_full->data->response->msgs;
				}
			}else{
				$response_array=array("could not communicate with BOU/ Check your internet connection");		
			}
			
			$msgs='';$counter =0;
			foreach($response_array as $msg){			
				($counter==0)?$msgs=$msg:$msgs.=$msg;
				$counter++;
			}
			
			if(!strlen($msgs))
				$msgs='No response.';
			
			$this->Session->setFlash(__($msgs),'flash_error');		
		
		}else{
			echo 'Date range required.';exit();
		}
	}
	
	

/**
 * index method
 *
 * @return void
 */
	public function index($large_cash=null) {
		$this->SoldReceipt->recursive = 0;
		$this->paginate=array('order' => 'SoldReceipt.date desc, SoldReceipt.t_time desc');
		if(	isset($_REQUEST['date_from']) &&
			isset($_REQUEST['date_to'])){
			$from	=($_REQUEST['date_from']);
			$to	=($_REQUEST['date_to']);
			
			$this->set('from', $from);
			$this->set('to', $to);
			
			if(isset($_REQUEST['search_query_string']) && !empty($_REQUEST['search_query_string'])){					
				if($this->Auth->User('role')=='super_admin'){
					$this->paginate = array(
						'conditions' => array(
							'OR' => array(
								'SoldReceipt.id LIKE' => '%' . $_REQUEST['search_query_string'] . '%',
								'SoldReceipt.customer_name LIKE' => '%' . $_REQUEST['search_query_string'] . '%'
							)
						),
						'order' => 'SoldReceipt.date desc, SoldReceipt.t_time desc',
						'limit'=>200
					);
				}else{
					$this->paginate = array(
						'conditions' => array(
							'OR' => array(
								'SoldReceipt.id LIKE' => '%' . $_REQUEST['search_query_string'] . '%',
								'SoldReceipt.customer_name LIKE' => '%' . $_REQUEST['search_query_string'] . '%'
							),
							'SoldReceipt.user_id'=>$this->Auth->User('id')
						),
						'order' => 'SoldReceipt.date desc, SoldReceipt.t_time desc',
						'limit'=>200
					);
				}
				
			}else{
				if(isset($_REQUEST['currency']) && !empty($_REQUEST['currency'])){
					$field = 'currency_id';
					if($_REQUEST['currency']=='c8'){
						$_REQUEST['currency']=$_REQUEST['other_currency'];
						$field = 'other_currency_id';
					}
					if($this->Auth->User('role')=='super_admin'){
						$this->paginate=array(
							'conditions'=>array(
								'SoldReceipt.date >='=>$from,
								'SoldReceipt.date <='=>$to,
								'SoldReceipt.'.$field=>$_REQUEST['currency']
							),
							'order' => 'SoldReceipt.date desc, SoldReceipt.t_time desc',					
							'limit'=>1000
						);
					}else{
						$this->paginate=array(
							'conditions'=>array(
								'SoldReceipt.date >='=>$from,
								'SoldReceipt.date <='=>$to,
								'SoldReceipt.'.$field=>$_REQUEST['currency'],
								'SoldReceipt.user_id'=>$this->Auth->User('id')
							),
							'order' => 'SoldReceipt.date desc, SoldReceipt.t_time desc',					
							'limit'=>1000
						);
					
					}
					$this->set('setCurrency',$_REQUEST['currency']);
				}else{			
					if($this->Auth->User('role')=='super_admin'){
						$this->paginate=array(
							'conditions'=>array(
								'SoldReceipt.date >='=>$from,
								'SoldReceipt.date <='=>$to
							),
							'order' => 'SoldReceipt.date desc, SoldReceipt.t_time desc',					
							'limit'=>1000
						);
					}else{
						$this->paginate=array(
							'conditions'=>array(
								'SoldReceipt.date >='=>$from,
								'SoldReceipt.date <='=>$to,
								'SoldReceipt.user_id'=>$this->Auth->User('id')
							),
							'order' => 'SoldReceipt.date desc, SoldReceipt.t_time desc',					
							'limit'=>1000
						);
					}
				}
			}
			
			if($large_cash){
				
				//get Average Rate for Dollar
				if($this->Auth->User('role')=='super_admin'){
					$dollar_av_rate=$this->SoldReceipt->find('all',array(
						'recursive'=>-1,
						'conditions'=>array(
							'SoldReceipt.currency_id'=>'USD',
							'SoldReceipt.date >='=>$from,
							'SoldReceipt.date <='=>$to
						),
						'fields'=>array(
							'SUM(SoldReceipt.amount) as total_amount',
							'SUM(SoldReceipt.amount_ugx) as total_amount_ugx'
						)
					));
				}else{
					$dollar_av_rate=$this->SoldReceipt->find('all',array(
						'recursive'=>-1,
						'conditions'=>array(
							'SoldReceipt.currency_id'=>'USD',
							'SoldReceipt.date >='=>$from,
							'SoldReceipt.date <='=>$to,
							'SoldReceipt.user_id'=>$this->Auth->User('id')
						),
						'fields'=>array(
							'SUM(SoldReceipt.amount) as total_amount',
							'SUM(SoldReceipt.amount_ugx) as total_amount_ugx'
						)
					));
				}
				
				if(isset($dollar_av_rate[0][0]['total_amount_ugx'])){
					$dollar_av_rate=($dollar_av_rate[0][0]['total_amount_ugx']!=0)?($dollar_av_rate[0][0]['total_amount_ugx']/$dollar_av_rate[0][0]['total_amount']):2400;
				}else{
					$dollar_av_rate=300;
				}

				$max_dollar_ugx=5000*$dollar_av_rate;
				
				if($this->Auth->User('role')=='super_admin'){
					$this->paginate=array(
						'conditions'=>array(
							'SoldReceipt.date >='=>$from,
							'SoldReceipt.date <='=>$to,
							'SoldReceipt.amount_ugx >='=>$max_dollar_ugx
						),
						'order' => 'SoldReceipt.date desc, SoldReceipt.t_time desc',
						'limit'=>10000
					);
				}else{
					$this->paginate=array(
						'conditions'=>array(
							'SoldReceipt.date >='=>$from,
							'SoldReceipt.date <='=>$to,
							'SoldReceipt.amount_ugx >='=>$max_dollar_ugx,
							'SoldReceipt.user_id'=>$this->Auth->User('id')
						),
						'order' => 'SoldReceipt.date desc, SoldReceipt.t_time desc',
						'limit'=>10000
					);					
				}
				$this->set('dollar_av_rate', $dollar_av_rate);
				$this->set('large_cash', $large_cash);
				
			}
		}
		$this->set('soldReceipts', $this->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->SoldReceipt->exists($id)) {
			throw new NotFoundException(__('Invalid sales receipt'));
		}
		$options = array('conditions' => array('SoldReceipt.' . $this->SoldReceipt->primaryKey => $id));
		$this->set('soldReceipt', $this->SoldReceipt->find('first', $options));
	}


/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {	
			$_date=date('Y-m-d H:i:s');
			//Check whether there's an opening for today, else redirect user to create an opening
			if(empty($this->request->data['SoldReceipt']['date']))
			{
				$this->request->data['SoldReceipt']['date'] = date('Y-m-d H:i:s');
			}
			$openingtoday = $this->Opening->find('first',array(
				'conditions'=>array(	
					'Opening.date'=>$_date,
					'Opening.user_id'=>(($this->Auth->User('role')=='super_admin')?$this->request->data['SoldReceipt']['user_id']:$this->Auth->User('id'))
				),
				'recursive'=>-1
			));
			if(empty($openingtoday) && $this->request->data['SoldReceipt']['instrument']=='TT'){
				$this->Session->setFlash(__('Please create an opening for '.$this->request->data['SoldReceipt']['date'],true),'flash_error');
				$this->redirect(array('action'=>'add','controller'=>'openings'));
			}
			
			if(($this->request->data['SoldReceipt']['currency_id'])=='c00'){
				$this->Session->setFlash(__('Invalid Currency selected.'),'flash_error');
				$this->redirect(array('action' => 'add'));
			}
			
			if(($this->request->data['SoldReceipt']['purpose_id'])=='p000'){
				$this->Session->setFlash(__('Invalid Purpose of transaction selected.'),'flash_error');
				$this->redirect(array('action' => 'add'));
			}

			$tt_currency_id = $this->request->data['SoldReceipt']['currency_id'];
			if($this->request->data['SoldReceipt']['currency_id']=='c8'){
				$other_currency=$this->SoldReceipt->OtherCurrency->find('first',array(
					'conditions'=>array(
						'OtherCurrency.id'=>$this->request->data['SoldReceipt']['other_currency_id']
					)
				));
				
				if(isset($other_currency['OtherCurrency']['name'])){					
					$this->request->data['SoldReceipt']['other_name']=$other_currency['OtherCurrency']['name'];
					$tt_currency_id = $other_currency['OtherCurrency']['id'];
					$tt_currency_name = $other_currency['OtherCurrency']['name'];
				}else{
					$this->Session->setFlash(__('Other currency not found.'),'flash_error');
					$this->redirect(array('action' => 'add'));
				}
			}else{
				if($this->request->data['SoldReceipt']['instrument']=='TT'){
					$_currency=$this->SoldReceipt->Currency->find('first',array(
						'conditions'=>array(
							'Currency.id'=>$this->request->data['SoldReceipt']['currency_id']
						),'recursive'=>-1
					));
					$tt_currency_id = $_currency['Currency']['id'];
					$tt_currency_name = $_currency['Currency']['description'];
				}
				unset($this->request->data['SoldReceipt']['other_currency_id']);
				unset($this->request->data['SoldReceipt']['other_name']);
			}
			
			if($this->request->data['SoldReceipt']['print']=='dont_print'){
				$this->request->data['SoldReceipt']['status']=1;
			}else{
				$this->request->data['SoldReceipt']['status']=0;
			}
			
			$ttamount = $this->request->data['SoldReceipt']['amount'];
			if($this->request->data['SoldReceipt']['currency_id']=='c8'){
				$_amount=$this->request->data['SoldReceipt']['amount'];
				$_rate=$this->request->data['SoldReceipt']['rate'];
				
				$rate=Configure::read('others');
				$amount=0;
				@$amount=($_amount*$_rate)/$rate;
				
				$this->request->data['SoldReceipt']['amount']=$amount;
				$this->request->data['SoldReceipt']['rate']=$rate;
				$this->request->data['SoldReceipt']['orig_amount']=$_amount;
				$this->request->data['SoldReceipt']['orig_rate']=$_rate;
				
			}
			
			if($this->Auth->User('role')!='super_admin'){
				$this->request->data['SoldReceipt']['user_id']=$this->Auth->User('id');
				$this->request->data['SoldReceipt']['name']=$this->Auth->User('name');
			}else{
				$user=$this->SoldReceipt->User->find('first',array(
					'conditions'=>array(
						'User.id'=>$this->request->data['SoldReceipt']['user_id']
					),
					'fields'=>array(
						'name'
					)
				));
				$this->request->data['SoldReceipt']['name']=$user['User']['name'];
			}	
			$this->request->data['SoldReceipt']['fox_id']=Configure::read('foxId');
			$_date=date('Y-m-d H:i:s');
			if($this->Auth->User('role')!='super_admin'){
				$this->request->data['SoldReceipt']['date']=date('Y-m-d',strtotime($_date));
			}
			$this->request->data['SoldReceipt']['t_time']=date('H:i:s',strtotime($_date));
			
			$receipt_number = $this->SoldReceipt->query("SELECT * FROM receipt_tracks limit 1");
			$this->request->data['SoldReceipt']['id'] = $receipt_number[0]['receipt_tracks']['my_count_sold_receipts'];
			
			//Set the remote address for this PC incase the printing will be done from this PC
			if($this->Auth->User('printing_place')==2){
				$this->request->data['SoldReceipt']['remote_addr'] = str_replace('::1','127.0.0.1',$_SERVER['REMOTE_ADDR']);
			}


			// Start large cash validations here
			$valid = true;
			$flashMessage = '';
			$isLargeCash = false;

			//Validate USD large cash
			$largeCashUSD = 5000;
			$conversionAmountUGX = $this->request->data['SoldReceipt']['amount_ugx'];
			$conversionCurrency = $this->request->data['SoldReceipt']['currency_id'];
			$USDEquivalent = 0;
			$defaultUSDRate = 300;

			if ($conversionCurrency=='USD') {
				$USDEquivalent = $this->request->data['SoldReceipt']['amount'];
			}else {
				$currenciesDetails = $this->SoldReceipt->Currency->find('first',array('conditions'=>array('Currency.id'=>'USD'),'recursive'=>-1));
				$rate = $defaultUSDRate;
				if (!empty($currenciesDetails)) {
					if (!empty($currenciesDetails['Currency']['sell'])) {
						$rate = $currenciesDetails['Currency']['sell'];
					}
				}
				@$USDEquivalent = $conversionAmountUGX/$rate;
			}

            if ($USDEquivalent>=$largeCashUSD) {
            	$x = $this->request->data['SoldReceipt'];
            	$y = 'customer_name|nationality|address|passport_number|phone_number';
            	$z = explode('|', $y);
            	foreach ($z as $value) {
            		if(empty($x[''.$value])){
            			$flashMessage = (__($value . ' is required for large Cash'));
						$valid = false;
						break;
            		}
            	}
            }
            // End LargeCashValidation

            if ($valid) {
            	$this->SoldReceipt->create();
				if ($this->SoldReceipt->save($this->request->data)) {
					$this->Session->setFlash(__('The sales receipt has been saved'),'flash_success');
					$this->Session->delete('unused_sales_receipt_id');
					$this->SoldReceipt->query("UPDATE receipt_tracks SET my_count_sold_receipts=my_count_sold_receipts+1");
					
					if($this->request->data['SoldReceipt']['instrument']=='TT'){
						//Update the opening UGX,
						$this->Opening->save(array(
							'Opening'=>array(
								'id'=>$openingtoday['Opening']['id'],
								'opening_ugx'=>$openingtoday['Opening']['opening_ugx'] + $this->request->data['SoldReceipt']['amount_ugx'] 
							)
						));
						//save the amount to the TtAccount
						$ttAcount = $this->TtAccount->find('first',array('conditions'=>array('TtAccount.id'=> $tt_currency_id)));
						@$ttAccountBalance = $ttAcount['TtAccount']['balance'];
						$this->TtAccount->save(array(
							'TtAccount'=>array('id'=>$tt_currency_id,'balance'=>($ttAccountBalance - $ttamount),'currency_name'=>$tt_currency_name)
						));
					}
					
					//Save transaction log
					$func=$this->Func;
					$action_performed=$this->Auth->User('name').' added sales receipt of '.(date('Y-m-d',strtotime($this->request->data['SoldReceipt']['date']))).' with amount '.(($this->request->data['SoldReceipt']['currency_id']!='c8')?$this->request->data['SoldReceipt']['amount']:$this->request->data['SoldReceipt']['orig_amount']).' at rate '.(($this->request->data['SoldReceipt']['currency_id']!='c8')?$this->request->data['SoldReceipt']['rate']:$this->request->data['SoldReceipt']['orig_rate']).' on '.(date('M d Y h:i:sa',strtotime($_date)));
					
					$action_log=array(
						'ActionLog'=>array(
							'id'=>$func->getUID1(),
							'user_id'=>$this->Auth->User('id'),
							'action_performed'=>$action_performed,
							'date_created'=>date('Y-m-d',strtotime($_date)),
							'date_time_created'=>$_date
						)
					);				
					$this->ActionLog->save($action_log);
					$this->redirect(array('action' => 'view',$this->request->data['SoldReceipt']['id']));
				} else {
					$this->Session->setFlash(__('The sales receipt could not be saved. Please, try again.'),'flash_error');
				}
            }else{
            	$this->Session->setFlash(__($flashMessage),'flash_error');
            }
		}
		$purposes = $this->SoldReceipt->Purpose->find('list',array(
			'conditions'=>array(
				'NOT'=>array(
					'Purpose.id'=>array('p37','p40','p41','p42','p45')
				)
			)
		));
		$users = $this->SoldReceipt->User->find('list',array(
			'conditions'=>array(
				'NOT'=>array(
					'User.role'=>array(
						'super_admin','customer'
					)
				)
			),
			'recursive'=>-1
		));
		$currencies = $this->SoldReceipt->Currency->find('list',[
			'order'=>'Currency.is_other_currency ASC,Currency.arrangement ASC, Currency.id ASC',
		]);
		$other_currencies = $this->SoldReceipt->OtherCurrency->find('list');
		$this->set(compact('purposes', 'currencies','users','other_currencies'));
		
		$_fox=($this->Session->read('fox'));
		@$use_system_board_rates = $_fox['Fox']['use_system_board_rates'];
		if($use_system_board_rates){
			$currenciesDetails = $this->SoldReceipt->Currency->find('all',array('recursive'=>-1));
			$otherCurrenciesDetails = $this->SoldReceipt->OtherCurrency->find('all',array('recursive'=>-1));
			$this->set('currenciesDetails',$currenciesDetails);
			$this->set('otherCurrenciesDetails',$otherCurrenciesDetails);
		}
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
        if(!$this->Auth->User('can_edit_receipt')){
        	$this->Session->setFlash(__('Access denied',true),'flash_error');
			$this->redirect(array('action'=>'index'));
        }

		if (!$this->SoldReceipt->exists($id)) {
			throw new NotFoundException(__('Invalid sales receipt'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			$_date = $this->request->data['SoldReceipt']['date']['year'].'-'.$this->request->data['SoldReceipt']['date']['month'].'-'.$this->request->data['SoldReceipt']['date']['day'];
			//Check whether there's an opening for today, else redirect user to create an opening
			$openingtoday = $this->Opening->find('first',array(
				'conditions'=>array(	
					'Opening.date'=>$_date,
					'Opening.user_id'=>(($this->Auth->User('role')=='super_admin')?$this->request->data['SoldReceipt']['user_id']:$this->Auth->User('id'))
				),
				'recursive'=>-1
			));
			if(empty($openingtoday) && $this->request->data['SoldReceipt']['instrument']=='TT'){
				$this->Session->setFlash(__('Please create an opening for this receipt date',true),'flash_error');
				$this->redirect(array('action'=>'add','controller'=>'openings'));
			}
			
			$oldReceipt = $this->SoldReceipt->find('first',array('conditions'=>array('SoldReceipt.id'=>$id),'recursive'=>-1));
			
			if(strlen($this->request->data['SoldReceipt']['id'])<4){
				$this->Session->setFlash(__('Invalid Receipt number.'),'flash_error');
				$this->redirect(array('action' => 'edit',$id));
			}
			
			if(($this->request->data['SoldReceipt']['currency_id'])=='c00'){
				$this->Session->setFlash(__('Invalid Currency selected.'),'flash_error');
				$this->redirect(array('action' => 'edit',$id));
			}
			
			if(($this->request->data['SoldReceipt']['purpose_id'])=='p000'){
				$this->Session->setFlash(__('Invalid Purpose of transaction selected.'),'flash_error');
				$this->redirect(array('action' => 'edit',$id));
			}

			$tt_currency_id = $this->request->data['SoldReceipt']['currency_id'];
			if($this->request->data['SoldReceipt']['currency_id']=='c8'){
				$other_currency=$this->SoldReceipt->OtherCurrency->find('first',array(
					'conditions'=>array(
						'OtherCurrency.id'=>(!empty($this->request->data['SoldReceipt']['other_currency_id']))?$this->request->data['SoldReceipt']['other_currency_id']:$oldReceipt['SoldReceipt']['other_currency_id']
					)
				));
				
				if(isset($other_currency['OtherCurrency']['name'])){					
					$this->request->data['SoldReceipt']['other_name']=$other_currency['OtherCurrency']['name'];
					$tt_currency_id = $other_currency['OtherCurrency']['id'];
					$tt_currency_name = $other_currency['OtherCurrency']['name'];
				}else{
					$this->Session->setFlash(__('Other currency not found.'),'flash_error');
					$this->redirect(array('action' => 'add'));
				}
			}else{
				if($this->request->data['SoldReceipt']['instrument']=='TT'){
					$_currency=$this->SoldReceipt->Currency->find('first',array(
						'conditions'=>array(
							'Currency.id'=>$this->request->data['SoldReceipt']['currency_id']
						),'recursive'=>-1
					));
					$tt_currency_id = $_currency['Currency']['id'];
					$tt_currency_name = $_currency['Currency']['description'];
				}
				unset($this->request->data['SoldReceipt']['other_currency_id']);
				unset($this->request->data['SoldReceipt']['other_name']);
			}
			
			if($this->request->data['SoldReceipt']['currency_id']!='c8'){
				unset($this->request->data['SoldReceipt']['other_name']);
				$this->request->data['SoldReceipt']['orig_amount']=0;
				$this->request->data['SoldReceipt']['orig_rate']=0;
			}
			
			$ttamount = $this->request->data['SoldReceipt']['amount'];
			if($this->request->data['SoldReceipt']['currency_id']=='c8'){
				$_amount=$this->request->data['SoldReceipt']['amount'];
				$_rate=$this->request->data['SoldReceipt']['rate'];
				
				$rate=Configure::read('others');
				$amount=0;
				@$amount=($_amount*$_rate)/$rate;
				
				$this->request->data['SoldReceipt']['amount']=$rate;
				$this->request->data['SoldReceipt']['rate']=$amount;
				$this->request->data['SoldReceipt']['orig_amount']=$_amount;
				$this->request->data['SoldReceipt']['orig_rate']=$_rate;
				
			}
			
			$date=$this->request->data['SoldReceipt']['date']['year'].'-'.$this->request->data['SoldReceipt']['date']['month'].'-'.$this->request->data['SoldReceipt']['date']['day'];
			$_date=date('Y-m-d H:i:s');
			
			if($this->Auth->User('role')!='super_admin'){
				$this->request->data['SoldReceipt']['user_id']=$this->Auth->User('id');
				$this->request->data['SoldReceipt']['name']=$this->Auth->User('name');
			}else{
				$user=$this->SoldReceipt->User->find('first',array(
					'conditions'=>array(
						'User.id'=>$this->request->data['SoldReceipt']['user_id']
					),
					'fields'=>array(
						'name'
					)
				));
				$this->request->data['SoldReceipt']['name']=$user['User']['name'];
			}	


			// Start large cash validations here
			$valid = true;
			$flashMessage = '';
			$isLargeCash = false;

			//Validate USD large cash
			$largeCashUSD = 5000;
			$conversionAmountUGX = $this->request->data['SoldReceipt']['amount_ugx'];
			$conversionCurrency = $this->request->data['SoldReceipt']['currency_id'];
			$USDEquivalent = 0;
			$defaultUSDRate = 300;

			if ($conversionCurrency=='USD') {
				$USDEquivalent = $this->request->data['SoldReceipt']['amount'];
			}else {
				$currenciesDetails = $this->SoldReceipt->Currency->find('first',array('conditions'=>array('Currency.id'=>'USD'),'recursive'=>-1));
				$rate = $defaultUSDRate;
				if (!empty($currenciesDetails)) {
					if (!empty($currenciesDetails['Currency']['sell'])) {
						$rate = $currenciesDetails['Currency']['sell'];
					}
				}
				@$USDEquivalent = $conversionAmountUGX/$rate;
			}

            if ($USDEquivalent>=$largeCashUSD) {
            	$x = $this->request->data['SoldReceipt'];
            	$y = 'customer_name|nationality|address|passport_number|phone_number';
            	$z = explode('|', $y);
            	foreach ($z as $value) {
            		if(empty($x[''.$value])){
            			$flashMessage = (__($value . ' is required for large Cash'));
						$valid = false;
						break;
            		}
            	}
            }
            // End LargeCashValidation
			if ($valid) {
				if ($this->SoldReceipt->save($this->request->data)) {
					$this->Session->setFlash(__('The sales receipt has been saved'),'flash_success');
					
					//Try to work on the old receipt
					if($oldReceipt['SoldReceipt']['instrument']=='TT'){
						//Get the old opening
						$oldopeningtoday = $this->Opening->find('first',array(
							'conditions'=>array(	
								'Opening.date'=>$oldReceipt['SoldReceipt']['date'],
								'Opening.user_id'=>(($this->Auth->User('role')=='super_admin')?$oldReceipt['SoldReceipt']['user_id']:$this->Auth->User('id'))
							),
							'recursive'=>-1
						));
						//Update the opening UGX,
						$this->Opening->save(array(
							'Opening'=>array(
								'id'=>$oldopeningtoday['Opening']['id'],
								'opening_ugx'=>($oldopeningtoday['Opening']['opening_ugx']-$oldReceipt['SoldReceipt']['amount_ugx'])
							)
						));
						//save the amount to the TtAccount
						$_ttcurrency_id = ($oldReceipt['SoldReceipt']['currency_id']=='c8')?$oldReceipt['SoldReceipt']['other_currency_id']:$oldReceipt['SoldReceipt']['currency_id'];
						$_ttamount = ($oldReceipt['SoldReceipt']['currency_id']=='c8')?$oldReceipt['SoldReceipt']['orig_amount']:$oldReceipt['SoldReceipt']['amount'];
						$_ttAcount = $this->TtAccount->find('first',array('conditions'=>array('TtAccount.id'=>$_ttcurrency_id)));
						@$_ttAccountBalance = $_ttAcount['TtAccount']['balance'];
						$this->TtAccount->save(array('TtAccount'=>array('id'=>$_ttcurrency_id,'balance'=>($_ttAccountBalance + $_ttamount))));
					}
					
					
					if($this->request->data['SoldReceipt']['instrument']=='TT'){
						$openingtoday = $this->Opening->find('first',array(
							'conditions'=>array(	
								'Opening.id'=>$openingtoday['Opening']['id'],
							),
							'recursive'=>-1
						));
						//Update the opening UGX,
						$this->Opening->save(array(
							'Opening'=>array(
								'id'=>$openingtoday['Opening']['id'],
								'opening_ugx'=>($openingtoday['Opening']['opening_ugx'] + $this->request->data['SoldReceipt']['amount_ugx'])
							)
						));
						//save the amount to the TtAccount
						$ttAcount = $this->TtAccount->find('first',array('conditions'=>array('TtAccount.id'=> $tt_currency_id)));
						@$ttAccountBalance = $ttAcount['TtAccount']['balance'];
						$this->TtAccount->save(array(
							'TtAccount'=>array('id'=>$tt_currency_id,'balance'=>($ttAccountBalance - $ttamount),'currency_name'=>$tt_currency_name)
						));
					}
					
					//Save transaction log
					$func=$this->Func;
					$action_performed=
						$this->Auth->User('name').
						' edited sales receipt['.($this->request->data['SoldReceipt']['id']).'] of '.
						(date('Y-m-d',strtotime($date))).
						' with amount '.
						(($this->request->data['SoldReceipt']['currency_id']!='c8')?$this->request->data['SoldReceipt']['amount']:$this->request->data['SoldReceipt']['orig_amount']).
						' at rate '.
						(($this->request->data['SoldReceipt']['currency_id']!='c8')?$this->request->data['SoldReceipt']['rate']:$this->request->data['SoldReceipt']['orig_rate']).
						' on '.(date('M d Y h:i:sa',strtotime($_date)));
					
					$action_log=array(
						'ActionLog'=>array(
							'id'=>$func->getUID1(),
							'user_id'=>$this->Auth->User('id'),
							'action_performed'=>$action_performed,
							'date_created'=>date('Y-m-d',strtotime($_date)),
							'date_time_created'=>$_date
						)
					);				
					$this->ActionLog->save($action_log);
					
					$this->redirect(array('action' => 'view',$id));
				} else {
					$this->Session->setFlash(__('The sales receipt could not be saved. Please, try again.'),'flash_error');
				}
			}else{
            	$this->Session->setFlash(__($flashMessage),'flash_error');
            }
		} else {
			$options = array('conditions' => array('SoldReceipt.' . $this->SoldReceipt->primaryKey => $id));
			$this->request->data = $this->SoldReceipt->find('first', $options);
		}
		$purposes = $this->SoldReceipt->Purpose->find('list',array(
			'conditions'=>array(
				'NOT'=>array(
					'Purpose.id'=>array('p37','p40','p41','p42','p45')
				)
			)
		));
		$users = $this->SoldReceipt->User->find('list',array(
			'conditions'=>array(
				'NOT'=>array(
					'User.role'=>array(
						'super_admin','customer'
					)
				)
			),
			'recursive'=>-1
		));
		$currencies = $this->SoldReceipt->Currency->find('list',[
			'order'=>'Currency.is_other_currency ASC,Currency.arrangement ASC, Currency.id ASC',
		]);
		$other_currencies = $this->SoldReceipt->OtherCurrency->find('list');
		$this->set(compact('purposes', 'currencies','users','other_currencies'));
		
		$_fox=($this->Session->read('fox'));
		@$use_system_board_rates = $_fox['Fox']['use_system_board_rates'];
		if($use_system_board_rates){
			$currenciesDetails = $this->SoldReceipt->Currency->find('all',array('recursive'=>-1));
			$otherCurrenciesDetails = $this->SoldReceipt->OtherCurrency->find('all',array('recursive'=>-1));
			$this->set('currenciesDetails',$currenciesDetails);
			$this->set('otherCurrenciesDetails',$otherCurrenciesDetails);
		}
	}

/**
 * delete method
 *
 * @throws NotFoundException
 * @throws MethodNotAllowedException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {

		if(!$this->Auth->User('can_delete_receipt')){
        	$this->Session->setFlash(__('Access denied',true),'flash_error');
			$this->redirect(array('action'=>'index'));
        }

		$this->SoldReceipt->id = $id;
		if (!$this->SoldReceipt->exists()) {
			throw new NotFoundException(__('Invalid sales receipt'));
		}
		
		$sold_receipt=$this->SoldReceipt->find('first',array(
			'conditions'=>array(
				'SoldReceipt.id'=>$id
			)
		));		
		$deleted_sold_receipt['DeletedSoldReceipt']=$sold_receipt['SoldReceipt'];
		$_date=date('Y-m-d H:i:s');
		if ($this->SoldReceipt->delete()) {
			$this->Session->setFlash(__('Sales receipt deleted'),'flash_success');			
			$this->DeletedSoldReceipt->save($deleted_sold_receipt);	
				
			//Save transaction log
			$func=$this->Func;
			$action_performed=
				$this->Auth->User('name').
				' deleted purchase receipt['.($sold_receipt['SoldReceipt']['id']).'] of '.
				($sold_receipt['SoldReceipt']['date']).' '.(date('h:i:sa',strtotime($sold_receipt['SoldReceipt']['t_time']))).
				' with amount '.
				(($sold_receipt['SoldReceipt']['currency_id']!='c8')?$sold_receipt['SoldReceipt']['amount']:$sold_receipt['SoldReceipt']['orig_amount']).
				' at rate '.
				(($sold_receipt['SoldReceipt']['currency_id']!='c8')?$sold_receipt['SoldReceipt']['rate']:$sold_receipt['SoldReceipt']['orig_rate']).
				' on '.(date('M d Y h:i:sa',strtotime($_date)));
			
			$action_log=array(
				'ActionLog'=>array(
					'id'=>$func->getUID1(),
					'user_id'=>$this->Auth->User('id'),
					'action_performed'=>$action_performed,
					'date_created'=>date('Y-m-d',strtotime($_date)),
					'date_time_created'=>$_date
				)
			);				
			$this->ActionLog->save($action_log);
			
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Sales receipt was not deleted'),'flash_error');
		$this->redirect(array('action' => 'index'));
	}
	
	function print_multiple_receipts($receipts){
		$data = array(
			'MultiplePrintReceipt'=>array(
				'id'=>'12345',
				'receipts'=>$receipts,
				'was_printed'=>0,
				'receipt_table'=>'sold_receipts'
			)
		);
		if($this->Auth->User('printing_place')==2){
			$data['MultiplePrintReceipt']['remote_addr'] = str_replace('::1','127.0.0.1',$_SERVER['REMOTE_ADDR']);;
		}
		$this->MultiplePrintReceipt->save($data);	
		echo 'done';
		exit;
	}
}
