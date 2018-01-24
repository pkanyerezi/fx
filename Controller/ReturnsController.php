<?php
App::uses('AppController', 'Controller');
/**
 * Users Controller
 *
 * @property User $User
 */
class ReturnsController extends AppController {
	var $uses = array('Purpose','PurchasedPurpose','Currency','SoldReceipt','PurchasedReceipt');

	public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow();
    }
	
	function returns_weekly(){
		$d=$purposes=$currencies=array();
		if(	isset($_REQUEST['date_from']) &&
			isset($_REQUEST['date_to'])){
			$from	=($_REQUEST['date_from']);
			$to	=($_REQUEST['date_to']);
			
			$this->set('from', $from);
			$this->set('to', $to);
			
			$purposes=$this->Purpose->find('all',array('recursive'=>-1,'conditions'=>array('id !='=>'p000'),'order'=>'id ASC'));
			$currencies=$this->Currency->find('all',array('recursive'=>-1,
				'conditions'=>array(
					'NOT'=>[
						'Currency.id'=>['c00','c8']
					]
				),
				//'order'=>'Currency.is_other_currency ASC, Currency.id ASC',
				'order'=>'Currency.is_other_currency ASC,Currency.arrangement ASC, Currency.id ASC',
			));
			$d=array();
			$currency_details=array();
			foreach($purposes as $purpose){
					$d[$purpose['Purpose']['id']]=array();
				foreach($currencies as $currency){
						$amount=$this->Currency->query("SELECT SUM(amount) as amount, SUM(amount_ugx) as total_ugx from sold_receipts
												WHERE purpose_id='".$purpose['Purpose']['id']."'
												and currency_id='".$currency['Currency']['id']."'
												and date >= '$from'
												and	date <= '$to'
												");
						array_push($d[$purpose['Purpose']['id']],array($currency['Currency']['id']=>$amount[0][0]['amount'],'UGX'.$currency['Currency']['id']=>$amount[0][0]['total_ugx']));
				}
			}
			foreach($currencies as $currency){
					$details=$this->Currency->query("SELECT SUM(amount) as amount, AVG(rate) as av_rate, SUM(amount_ugx) as amount_ugx from sold_receipts
												WHERE currency_id='".$currency['Currency']['id']."'
												and date >= '$from'
												and	date <= '$to'
												");
					array_push($currency_details,array(array('amount'=>$details[0][0]['amount'],'amount_ugx'=>$details[0][0]['amount_ugx'],'av_rate'=>$details[0][0]['av_rate'])));
			}
			
		}else{
			pr('Date range required.');exit();
		}
		
		$this->set('my_data',$d);
		$this->set('purposes',$purposes);
		$this->set('currencies',$currencies);
		$this->set('currency_details',$currency_details);
	}
	
	function returns_weekly_purchases(){
		$d=$purposes=$currencies=array();
		if(	isset($_REQUEST['date_from']) &&
			isset($_REQUEST['date_to'])){
			$from	=($_REQUEST['date_from']);
			$to	=($_REQUEST['date_to']);
			
			$this->set('from', $from);
			$this->set('to', $to);
			
			$purposes=$this->PurchasedPurpose->find('all',array('recursive'=>-1,'conditions'=>array('id !='=>'p000')));
			$currencies=$this->Currency->find('all',array('recursive'=>-1,
					'conditions'=>array(
						'NOT'=>[
							'Currency.id'=>['c00','c8']
						]
					),
					//'order'=>'Currency.is_other_currency ASC, Currency.id ASC'
					'order'=>'Currency.is_other_currency ASC,Currency.arrangement ASC, Currency.id ASC',
			));
			$d=array();
			$currency_details=array();
			foreach($purposes as $purpose){
					$d[$purpose['PurchasedPurpose']['id']]=array();
				foreach($currencies as $currency){
						$amount=$this->Currency->query("SELECT SUM(amount) as amount, SUM(amount_ugx) as total_ugx from purchased_receipts
												WHERE purchased_purpose_id='".$purpose['PurchasedPurpose']['id']."'
												and currency_id='".$currency['Currency']['id']."'
												and date >= '$from'
												and	date <= '$to'
												");
						array_push($d[$purpose['PurchasedPurpose']['id']],array($currency['Currency']['id']=>$amount[0][0]['amount'],'UGX'.$currency['Currency']['id']=>$amount[0][0]['total_ugx']));
				}
			}
			
			foreach($currencies as $currency){
					$details=$this->Currency->query("SELECT SUM(amount) as amount, AVG(rate) as av_rate, SUM(amount_ugx) as amount_ugx from purchased_receipts
												WHERE currency_id='".$currency['Currency']['id']."'
												and date >= '$from'
												and	date <= '$to'
												");
					array_push($currency_details,array(array('amount'=>$details[0][0]['amount'],'amount_ugx'=>$details[0][0]['amount_ugx'],'av_rate'=>$details[0][0]['av_rate'])));
			}
			
		}else{
			pr('Date range required.');exit();
		}
		
		$this->set('my_data',$d);
		$this->set('purposes',$purposes);
		$this->set('currencies',$currencies);
		$this->set('currency_details',$currency_details);
	}
	
	public function send_returns($return_type='weekly'){
		ignore_user_abort(1);
		ini_set('max_execution_time', 600);
		
		@$from	=	($_REQUEST['date_from']);
		@$to	=	($_REQUEST['date_to']);
		
		/**
		 *===============================
		 *===============================
		 *GENERATE EXCEL SHEET FOR SALES
		 *===============================
		 *===============================
		 */	
		$response['msgs']=array();
		
		$purposes=$this->Purpose->find('all',array('recursive'=>-1,'order'=>'arrangement ASC','conditions'=>array('NOT'=>array('Purpose.id'=>'p000'))));
		$currencies=$this->Currency->find('all',array('recursive'=>-1,'order'=>'arrangement ASC',
			'conditions'=>array(
				'NOT'=>[
					'Currency.id'=>['c00','c8']
				]
			),
			//'order'=>'Currency.is_other_currency ASC, Currency.id ASC'
			'order'=>'Currency.is_other_currency ASC,Currency.arrangement ASC, Currency.id ASC',
		));
		$d=array();
		$currency_details=array();
		
		$excludable_purposes=array('p37','p38','p39','p45');//Purposes that are not included in the Excel file generated.
		foreach($purposes as $purpose){
				if(in_array($purpose['Purpose']['id'],$excludable_purposes)){
					continue;//Skip the purposes that dont need to be representes in the Excel sheet
				}
				$d[$purpose['Purpose']['id']]=array();
			foreach($currencies as $currency){
					$amount=$this->Currency->query("SELECT SUM(amount) as amount, SUM(amount_ugx) as total_ugx from sold_receipts
											WHERE purpose_id='".$purpose['Purpose']['id']."'
											and currency_id='".$currency['Currency']['id']."'
											and date >= '$from'
											and	date <= '$to'
											");
					array_push($d[$purpose['Purpose']['id']],array($currency['Currency']['id']=>$amount[0][0]['amount'],'UGX'.$currency['Currency']['id']=>$amount[0][0]['total_ugx']));
				
			}
		}
		
		foreach($currencies as $currency){
				//exclude sum for the ignored purposes
				$details=$this->SoldReceipt->find('all',array(
					'recursive'=>-1,
					'conditions'=>array(
						'SoldReceipt.currency_id'=>$currency['Currency']['id'],
						'SoldReceipt.date >='=>$from,
						'SoldReceipt.date <='=>$to,
						'NOT'=>array(
							'SoldReceipt.purpose_id'=>$excludable_purposes
						)
					),
					'fields'=>array(
						'SUM(amount) as amount',
						'SUM(amount_ugx) as amount_ugx'
					)
				));
				$av_rate = 0;
				if($details[0][0]['amount_ugx']>0){
					$av_rate = $details[0][0]['amount_ugx']/$details[0][0]['amount'];
				}
				array_push($currency_details,array(array('amount'=>$details[0][0]['amount'],'amount_ugx'=>$details[0][0]['amount_ugx'],'av_rate'=>$av_rate,'currency_id'=>$currency['Currency']['id'],'is_other_currency'=>$currency['Currency']['is_other_currency'])));
		}
		
		$others_rate = Configure::read('others');

		$currenciesDetails = $this->SoldReceipt->Currency->find('first',array('conditions'=>array('Currency.id'=>'c1'),'recursive'=>-1));
		$USDRate = 3300;
		if (!empty($currenciesDetails)) {
			if (!isset($currenciesDetails['Currency']['sell']) && !empty($currenciesDetails['Currency']['sell'])) {
				$USDRate = $currenciesDetails['Currency']['sell'];
			}
		}

		
		/**
		 *GENERATING EXCEL FILE
		 *================================
		 */
		App::import('Vendor', 'PHPExcel/PHPExcel');
		if($return_type=='monthly')
			$objPHPexcel = PHPExcel_IOFactory::load('ExcelFiles/templates/returns_monthly.xls');
		else
			$objPHPexcel = PHPExcel_IOFactory::load('ExcelFiles/templates/returns_weekly.xls');
		$objWorksheet = $objPHPexcel->getSheet(1);//Get the SALES SHEET
		
		 //SET CURRENCY HEADERS
		 //////////////////////////////////////
		/*$objWorksheet->getCell('B14')->setValue('USD');	  $objWorksheet->getCell('C14')->setValue('Euro');  
		$objWorksheet->getCell('D14')->setValue('GBP');	  $objWorksheet->getCell('E14')->setValue('Kshs');  
		$objWorksheet->getCell('F14')->setValue('Tzsh');  $objWorksheet->getCell('G14')->setValue('SAR');	  
		$objWorksheet->getCell('H14')->setValue('Others');//$objWorksheet->getCell('I14')->setValue('SP');
*/
		//INSERT VALUES
		//////////////////////////////////////
		
		//USD,EURO,GBP,Kshs,Tzsh,SAR,Others,SP
		$currency_cell_cols=array('B','C','D','E','F','G','H','H','H');//Last 2 cols are both 'H' so that Others replaces SP value
		$purpose_count=17;
		$skipable_rows=array(17,19,26,30,31,39,49,55,59,60,61);
		if($return_type=='monthly'){
			$purpose_count=15;
			$skipable_rows=array(17,19,26,30,31,39,49,55,59,60,61);
			foreach($currency_cell_cols as $col){
				$objWorksheet->getCell($col.'62')->setValue(0);
				$objWorksheet->getCell($col.'64')->setValue(0);
				$objWorksheet->getCell($col.'66')->setValue(0);
			}
		}else{
			$purpose_count=17;
			$skipable_rows=array(19,21,28,32,33,41,51,57,61,62,63);
			foreach($currency_cell_cols as $col){
				$objWorksheet->getCell($col.'64')->setValue(0);
				$objWorksheet->getCell($col.'65')->setValue(0);
				$objWorksheet->getCell($col.'66')->setValue(0);
			}
		}
		
		
		foreach($purposes as $purpose){
			if($purpose=='p37'){
			
			}
			if(in_array($purpose['Purpose']['id'],$excludable_purposes)){
				continue;//Skip the purposes that dont need to be representes in the Excel sheet
			}
			
			while(in_array($purpose_count,$skipable_rows)){
				$purpose_count++;//Skip the rows in the Excel that should not contain data
			}
			
			$currency_count=0;
			//get the USD average rate for converting others currencies to the USD as required
			//get the average_rate of the USD from the total in UGX devided by the amount in USD
			if($currency_details[0][0]['amount_ugx']>0 && $currency_details[0][0]['amount']>0 ){
				@$x = $currency_details[0][0]['amount_ugx']/$currency_details[0][0]['amount'];
				if(!empty($x) and $x>0) $others_rate = $x;
			}


			$others_amount = 0;
			$others_amount_ugx = 0;
			foreach($currencies as $currency){
				$amount=$d[$purpose['Purpose']['id']][$currency_count][$currency['Currency']['id']];
				$amount_ugx=$d[$purpose['Purpose']['id']][$currency_count]['UGX'.$currency['Currency']['id']];
				
				if (!$currency['Currency']['is_other_currency']) {
					$objWorksheet->getCell(($currency_cell_cols[$currency_count]).''.$this->getExelFieldSales($purpose['Purpose']['id'],$return_type))
							->setValue(($amount)?$amount:0);
				}else{
					$others_amount += $amount;
					$others_amount_ugx += $amount_ugx;
				}
				$currency_count++;
			}
			$objWorksheet->getCell(('H').''.($this->getExelFieldSales($purpose['Purpose']['id'],$return_type)))
							->setValue(($USDRate)?$others_amount_ugx/$USDRate:0);
			$purpose_count++;
		}
		
		
		//INCLUDE ROW WITH UGX CURRENCIES
		$currency_count=0;
		$total_ugx_others = 0;
		foreach($currency_details as $currency_detail){
			$amount=$currency_detail[0]['amount_ugx'];
			if(!$currency_detail[0]['is_other_currency']){
				if($return_type=='monthly'){
					$objWorksheet->getCell(($currency_cell_cols[$currency_count]).'70')
								->setValue(($amount)?$amount:0);
				}else{
					$objWorksheet->getCell(($currency_cell_cols[$currency_count]).'72')
								->setValue(($amount)?$amount:0);
				}
			}else{
				$total_ugx_others+=$amount=$currency_detail[0]['amount_ugx'];
			}
			
			$currency_count++;
		}

		if($return_type=='monthly'){
			$objWorksheet->getCell(('H').'70')
						->setValue(($total_ugx_others)?$total_ugx_others:0);
		}else{
			$objWorksheet->getCell(('H').'72')
						->setValue(($total_ugx_others)?$total_ugx_others:0);
		}

		
		//Fill other columns that have no values with zeros to remove the error 7 from the excel file
		if($return_type=='monthly'){
			$zeros=array(19,39,61,63,68);
		}else{
			//$zeros=array(19,21,39,41,61,63,68,70);
			$zeros=array(21,41,63,70);
		}
		foreach($zeros as $zero){
			$currency_count=0;
			foreach($currency_details as $currency_detail){
				/*@$t = ($currency_cell_cols[$currency_count]).''.$zero;
				pr($t);*/
				if (isset($currency_cell_cols[$currency_count])) {
					$objWorksheet->getCell(($currency_cell_cols[$currency_count]).''.$zero)->setValue(0);
				}
				
				$currency_count++;
			}
		}
		
		$objWorksheet->getCell('D11')->setValue(0);
		$objWorksheet->getCell('D12')->setValue(0);
		
		$officer_name='officer_name';//$this->Auth->User('officer_name');
		$officer_title='officer_title';//$this->Auth->User('officer_title');
		$officer_phone='officer_phone';//$this->Auth->User('officer_phone');
		
		/*
		$objWorksheet->getCell('B7')->setValue($from);//date_from	
		$objWorksheet->getCell('B8')->setValue($to);//date_to
		$objWorksheet->getCell('B9')->setValue(date('d.m.Y'));//Year
		$objWorksheet->getCell('B10')->setValue((isset($u['User']['name']))?$u['User']['name']:$this->Auth->User('name'));//Year
		$objWorksheet->getCell('E6')->setValue($officer_name);//officer's name
		$objWorksheet->getCell('E7')->setValue($officer_title);//Title
		$objWorksheet->getCell('E8')->setValue($officer_phone);//Telephone number
		*/
		
		$objWorksheet->getCell('E7')->setValue(date('Y/m/d',strtotime($from)));//date_from
		$objWorksheet->getCell('E8')->setValue(date('Y/m/d',strtotime($to)));//date_to
		

		
		
		
		
		
		
		/**
		 *===============================
		 *===============================
		 *GENERATE EXCEL SHEET FOR PURCHASES
		 *===============================
		 *===============================
		 */
		 $purposes=$this->PurchasedPurpose->find('all',array('recursive'=>-1,'order'=>'arrangement ASC','conditions'=>array('NOT'=>array('PurchasedPurpose.id'=>'p000'))));
		//$currencies=$this->Currency->find('all',array('recursive'=>-1));
		$d=array();
		$currency_details=array();
		
		$excludable_purposes=array('p38','p39','p42');//Purposes that are not included in the Excel file generated.
		foreach($purposes as $purpose){
				if(in_array($purpose['PurchasedPurpose']['id'],$excludable_purposes)){
					continue;//Skip the purposes that dont need to be representes in the Excel sheet
				}
				$d[$purpose['PurchasedPurpose']['id']]=array();
			foreach($currencies as $currency){
					$amount=$this->Currency->query("SELECT SUM(amount) as amount, SUM(amount_ugx) as total_ugx from purchased_receipts
											WHERE purchased_purpose_id='".$purpose['PurchasedPurpose']['id']."'
											and currency_id='".$currency['Currency']['id']."'
											and date >= '$from'
											and	date <= '$to'
											");
					array_push($d[$purpose['PurchasedPurpose']['id']],array($currency['Currency']['id']=>$amount[0][0]['amount'],'UGX'.$currency['Currency']['id']=>$amount[0][0]['total_ugx']));
				
			}
		}
		
		foreach($currencies as $currency){
				//exclude sum for the ignored purposes
				$details=$this->PurchasedReceipt->find('all',array(
					'recursive'=>-1,
					'conditions'=>array(
						'PurchasedReceipt.currency_id'=>$currency['Currency']['id'],
						'PurchasedReceipt.date >='=>$from,
						'PurchasedReceipt.date <='=>$to,
						'NOT'=>array(
							'PurchasedReceipt.purchased_purpose_id'=>$excludable_purposes
						)
					),
					'fields'=>array(
						'SUM(amount) as amount',
						'SUM(amount_ugx) as amount_ugx'
					)
				));
				$av_rate = 0;
				if($details[0][0]['amount_ugx']>0){
					$av_rate = $details[0][0]['amount_ugx']/$details[0][0]['amount'];
				}
				array_push($currency_details,array(array('amount'=>$details[0][0]['amount'],'amount_ugx'=>$details[0][0]['amount_ugx'],'av_rate'=>$av_rate,'currency_id'=>$currency['Currency']['id'],'is_other_currency'=>$currency['Currency']['is_other_currency'])));
		}
		
		/**
		 *GENERATING EXCEL FILE
		 *================================
		 */
		$Purchased_objWorksheet = $objPHPexcel->getSheet(0);//Get the purchase SHEET
		
		 //SET CURRENCY HEADERS
		 //////////////////////////////////////
		/*$Purchased_objWorksheet->getCell('B17')->setValue('Euro');	  	$Purchased_objWorksheet->getCell('C17')->setValue('USD');  
		$Purchased_objWorksheet->getCell('D17')->setValue('GBP');	  	$Purchased_objWorksheet->getCell('E17')->setValue('KES');  
		$Purchased_objWorksheet->getCell('F17')->setValue('TZS');  		$Purchased_objWorksheet->getCell('G17')->setValue('ZAR');	  
		$Purchased_objWorksheet->getCell('H17')->setValue('Others(in USD)*');	//$Purchased_objWorksheet->getCell('I14')->setValue('SP');
*/
		//INSERT VALUES
		//////////////////////////////////////
		
		//USD,EURO,GBP,Kshs,Tzsh,SAR,Others,SP
		$currency_cell_cols=array('B','C','D','E','F','G','H','H','H');//Last 2 cols are both 'H' so that Others replaces SP value
		//$purpose_count=15;
		$purpose_count=18;
		$skipable_rows=array(20,26,30,31,39,43,49,55,59,60,61);
		foreach($currency_cell_cols as $col){
			$Purchased_objWorksheet->getCell($col.'64')->setValue(0);
		}
		
		foreach($purposes as $purpose){
			if(in_array($purpose['PurchasedPurpose']['id'],$excludable_purposes)){
				continue;//Skip the purposes that dont need to be representes in the Excel sheet
			}
			
			while(in_array($purpose_count,$skipable_rows)){
				$purpose_count++;//Skip the rows in the Excel that should not contain data
			}
			
			$currency_count=0;
			//get the USD average rate for converting others currencies to the USD as required
			//get the average_rate of the USD from the total in UGX devided by the amount in USD
			if($currency_details[0][0]['amount_ugx']>0 && $currency_details[0][0]['amount']>0 ){
				@$x = $currency_details[0][0]['amount_ugx']/$currency_details[0][0]['amount'];
				if(!empty($x) and $x>0) $others_rate = $x;
			}

			$others_amount = 0;
			$others_amount_ugx = 0;
			foreach($currencies as $currency){
				$amount=$d[$purpose['PurchasedPurpose']['id']][$currency_count][$currency['Currency']['id']];
				$amount_ugx=$d[$purpose['PurchasedPurpose']['id']][$currency_count]['UGX'.$currency['Currency']['id']];
				
				/*if($currency['Currency']['id']=='c8'){
					$amount = ($d[$purpose['PurchasedPurpose']['id']][6]['UGXc7'] + $d[$purpose['PurchasedPurpose']['id']][7]['UGXc8']);
					$amount = round($amount/$others_rate,4);
				}*/
				
				if (!$currency['Currency']['is_other_currency']) {
					$Purchased_objWorksheet->getCell(($currency_cell_cols[$currency_count]).''.($this->getExelFieldPurchases($purpose['PurchasedPurpose']['id'])))
							->setValue(($amount)?$amount:0);
				}else{
					$others_amount += $amount;
					$others_amount_ugx += $amount_ugx;
				}
				
				$currency_count++;
			}

			$Purchased_objWorksheet->getCell(('H').''.($this->getExelFieldPurchases($purpose['PurchasedPurpose']['id'])))
							->setValue(($USDRate)?$others_amount_ugx/$USDRate:0);

			$purpose_count++;
		}
		
		//INCLUDE ROW WITH UGX CURRENCIES
		$currency_count=0;
		$total_ugx_others = 0; 
		foreach($currency_details as $currency_detail){
			if($currency_detail[0]['is_other_currency']){
				$total_ugx_others+=$amount=$currency_detail[0]['amount_ugx'];
			}else{
				$amount=$currency_detail[0]['amount_ugx'];
				$Purchased_objWorksheet->getCell(($currency_cell_cols[$currency_count]).'70')
							->setValue(($amount)?$amount:0);
			}
			$currency_count++;
		}
		$Purchased_objWorksheet->getCell(('H').'70')
							->setValue(($total_ugx_others)?$total_ugx_others:0);
		
		//Fill other columns that have no values with zeros to remove the error 7 from the excel file
		//$zeros=array(20,26,30,31,39,43,49,55,59,60,61);
		$zeros = array(39,61,68);
		foreach($zeros as $zero){
			$currency_count=0;
			foreach($currency_details as $currency_detail){
				if (isset($currency_cell_cols[$currency_count])) {
					$Purchased_objWorksheet->getCell(($currency_cell_cols[$currency_count]).''.$zero)->setValue(0);
				}
				$currency_count++;
			}
		}
		
		
		$Purchased_objWorksheet->getCell('D15')->setValue(0);
		//Other fields/cells
		date_default_timezone_set ( 'Africa/Nairobi' );
		$Purchased_objWorksheet->getCell('B6')->setValue(date('d',strtotime($to)));
		$Purchased_objWorksheet->getCell('B7')->setValue(date('m',strtotime($to)));
		$Purchased_objWorksheet->getCell('B8')->setValue(date('Y',strtotime($to)));
		$Purchased_objWorksheet->getCell('E7')->setValue($from);//date_from
		$Purchased_objWorksheet->getCell('E8')->setValue($to);//date_to
		$Purchased_objWorksheet->getCell('B9')->setValue(date('d.m.Y',strtotime($to)));//Year
		$Purchased_objWorksheet->getCell('E6')->setValue(date('Y',strtotime($to)));//officer's name
		$Purchased_objWorksheet->getCell('E7')->setValue(date('Y/m/d',strtotime($from)));//Start Date
		$Purchased_objWorksheet->getCell('E8')->setValue(date('Y/m/d',strtotime($to)));//End date
				
		
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPexcel, 'Excel5');

		if (isset($_REQUEST['apiRequest'])) {
			$newFileName = "ExcelFiles/templates/apirequest_FIA_large_cash_" . $_REQUEST['apiRequest'] . ".xls"; 
			if ($return_type=='monthly') {
				$newFileName = 'ExcelFiles/templates/apirequest_returns_monthly_'  . $_REQUEST['apiRequest'] . ".xls"; 
			} else {
				$newFileName = 'ExcelFiles/templates/apirequest_returns_' . $return_type . '_'  . $_REQUEST['apiRequest'] . ".xls"; 
			}
		} else {
			if ($return_type=='monthly') {
				$newFileName = 'ExcelFiles/templates/returns_monthly_'.((isset($u['User']['name']))?$u['User']['name']:$this->Auth->User('name')).".xls"; 
			} else {
				$newFileName = 'ExcelFiles/templates/returns_'.$return_type.'_'.((isset($u['User']['name']))?$u['User']['name']:$this->Auth->User('name')).".xls"; 
			}
		}
		
		$objWriter->save($newFileName);

		if(isset($_REQUEST['apiRequest'])) {
			echo json_encode([
				'apiRequest'=>$_REQUEST['apiRequest'],
				'filename'=>$newFileName
			]);
			exit();
		}

		$this->redirect('http://'.$this -> downloadsIp.'/fx/'.$newFileName);
		
		/*
		if(	isset($_REQUEST['date_from']) &&
			isset($_REQUEST['date_to'])){
			$from	=($_REQUEST['date_from']);
			$to	=($_REQUEST['date_to']);
			
			$this->set('from', $from);
			$this->set('to', $to);
			
			$SoldReceiptsCount=$this->SoldReceipt->find('count',array('conditions'=>array('SoldReceipt.is_uploaded'=>0)));
			$PurchasedReceiptsCount=$this->PurchasedReceipt->find('count',array('conditions'=>array('PurchasedReceipt.is_uploaded'=>0)));
			
			if($SoldReceiptsCount){
				$this->Session->setFlash(__("Warning:".($SoldReceiptsCount).' sales receipt(s) not uploaded yet. Please upload to continue.'));
				return;
			}
			
			if($PurchasedReceiptsCount){
				$this->Session->setFlash(__("Warning:".($PurchasedReceiptsCount).' purchase receipt(s) not uploaded yet. Please upload to continue.'));
				return;
			}
			
			
			$resting=new $this->Resting;
			$_fox=($this->Session->read('fox'));
			$resting->api_username=$_fox['Fox']['un'];
			$resting->api_password=$_fox['Fox']['pwd'];
			$resting->authorisation_key=$_fox['Fox']['k'];
			$resting->url = $_fox['Fox']['url'];
			$Returns['Returns']['date_from']	=$from;
			$Returns['Returns']['date_to']		=$to;
			if(isset($this->request->data['Return']['auditors_email'])){
				if(strlen($this->request->data['Return']['auditors_email'])>5){
					$Returns['Returns']['auditor_email']=$this->request->data['Return']['auditors_email'];
				}
			}
			
			$msgs='as';		
			$start_time=date('Y-m-d H:i:s');
			$response=$resting->XML_fetch_data('/returns/send_returns.json','<Returns>'.(json_encode($Returns)).'</Returns>');
			if($resting->has_response){
				$response_array_full=json_decode($response);
				$response_array=array();
				if(isset($response_array_full->data->response->msgs)){
					$response_array=$response_array_full->data->response->msgs;
				}
			}else{
				if(isset($this->request->data['Return']['auditors_email'])){
					$min_stop_time=date('Y-m-d H:i:s',strtotime($start_time.'+1minute'));
					if(strtotime(date('Y-m-d H:i:s'))>=strtotime($min_stop_time)){
						$response_array=array("Sent.Please check your email to confirm.");
					}else{
						$response_array=array("Not sent. Could not communicate with BOU/ Check your internet connection");
					}					
				}else{
					$response_array=array("could not communicate with BOU/ Check your internet connection");
				}				
			}
			
			$msgs='';$counter =0;
			foreach($response_array as $msg){			
				($counter==0)?$msgs=$msg:$msgs.=$msg;
				$counter++;
			}
			
			if(!strlen($msgs))
				$msgs='No response.';
			
			$this->Session->setFlash(__($msgs));		
		
		}else{
			echo 'Date range required.';exit();
		}*/
	}
	
	
	public function getExelFieldSales($purpose_id,$type='weekly'){
		$minus = 0;
		if($type=='monthly') $minus  = 2;
	//return 3;
		switch($purpose_id){
			case 'p1':
				return 17-$minus;//'Transaction between Uganda Residents';
			case 'p2':
				return 18-$minus;//'Currency Holdings/Deposits';
			case 'p3':
				return 20-$minus;//'Govt. Imports';
			case 'p4':
				return 22-$minus;//'Private Imports - Oil';
			case 'p5':
				return 23-$minus;//'Private Imports - Gold';
			case 'p6':
				return 24-$minus;//'Private Imports - Repair';
			case 'p7':
				return 25-$minus;//'Private Imports - Goods produced in ports by carriers';
			case 'p8':
				return 26-$minus;//'Private Imports - Goods for processing';
			case 'p9':
				return 27-$minus;//'Private Imports - Other Imports';
			case 'p10':
				return 29-$minus;//'Income Payments - Interest paid on external liabilities';
			case 'p11':
				return 30-$minus;//'Income Payments - Dividends/profits paid';
			case 'p12':
				return 31-$minus;//'Income Payments - Wages/Salaries';
			case 'p13':
				return 34-$minus;//'Service Payments - Transportation - Freight';
			case 'p14':
				return 35-$minus;//'Service Payments - Transportation - Passanger';
			case 'p15':
				return 36-$minus;//'Service Payments - Transportation - Other';
			case 'p16':
				return 37-$minus;//'Service Payments - Communication services';
			case 'p17':
				return 38-$minus;//'Service Payments - Construction services';
			case 'p18':
				return 39-$minus;//'Service Payments - Insurance & Re-insurance';
			case 'p19':
				return 40-$minus;//'Service Payments - Financial services';
			case 'p20':
				return 42-$minus;//'Service Payments - Travel - Business/Official';
			case 'p21':
				return 43-$minus;//'Service Payments - Travel - Education';
			case 'p22':
				return 44-$minus;//'Service Payments - Travel - Medical;
			case 'p23':
				return 45-$minus;//'Service Payments - Travel - Other Personal';
			case 'p24':
				return 46-$minus;//'Service Payments - Computer & info services';
			case 'p25':
				return 47-$minus;//'Service Payments - Royalties & licence fees';
			case 'p26':
				return 48-$minus;//'Service Payments - Other business services';
			case 'p27':
				return 49-$minus;//'Service Payments - Personal, cultural, & recreational services';
			case 'p28':
				return 50-$minus;//'Service Payments - Governmen services, n.i.e';
			case 'p29':
				return 52-$minus;//'Transfers - NGO outflows';
			case 'p30':
				return 53-$minus;//'Transfers - Government Grants';
			case 'p31':
				return 54-$minus;//'Transfers - Worker\'s remittances';
			case 'p32':
				return 55-$minus;//'Transfers - Other transfers';
			case 'p33':
				return 56-$minus;//'Foreign Direct equity Investment';
			case 'p34':
				return 58-$minus;//'Portfolio Investment - By Government';
			case 'p35':
				return 59-$minus;//'Portfolio Investment - By Banks';
			case 'p36':
				return 60-$minus;//'Portfolio Investment - By Other';
			case 'p37':
				return 0;//'Portfolio Investment - Other transfers';
			case 'p38':
				return 64-$minus;//'Loans Extended abroad - By commercial Banks - Short term';
			case 'p39':
				return 65-$minus;//'Loans Extended abroad - By commercial Banks - Long term';
			case 'p40':
				return 0;//'Loans Extended abroad - By Others - Private-Short term';
			case 'p41':
				return 0;//'Loans Extended abroad - By Others - Private-Long term';
			case 'p42':
				return 0;//'Loans Extended abroad - By Others - Government';
			case 'p43':
				return 67-$minus;//'Loan Repaymen (Principal)';
			case 'p44':
				return 68;//'Bank/bureaux';
			case 'p45':
				return 0;//'Interbank';
			case 'p46':
				return 69-$minus;//'Interbureaux';
			default:
				return 0;
		}
	}
	
	function getExelFieldPurchases($purpose_id,$amount=0){
	//return 3;
	// $this->log($purpose_id.':'.$amount,'xx');
		switch($purpose_id){
			case 'p1':
				return '18';
			case 'p2':
				return '19';
			case 'p3':
				return '21';
			case 'p4':
				return '22';
			case 'p5':
				return '23';
			case 'p6':
				return '24';
			case 'p7':
				return '25';
			case 'p8':
				return '27';
			case 'p9':
				return '28';
			case 'p10':
				return '29';
			case 'p11':
				return '32';
			case 'p12':
				return '33';
			case 'p13':
				return '34';
			case 'p14':
				return '35';
			case 'p15':
				return '36';
			case 'p16':
				return '37';
			case 'p17':
				return '38';
			case 'p18':
				return '40';
			case 'p19':
				return '41';
			case 'p20':
				return '42';
			case 'p21':
				return '43';
			case 'p22':
				return '44';
			case 'p23':
				return '45';
			case 'p24':
				return '46';
			case 'p25':
				return '47';
			case 'p26':
				return '48';
			case 'p27':
				return '50';
			case 'p28':
				return '51';
			case 'p29':
				return '52';
			case 'p30':
				return '53';
			case 'p31':
				return '66';
			case 'p32':
				return '54';
			case 'p33':
				return '56';
			case 'p34':
				return '57';
			case 'p35':
				return '58';
			case 'p36':
				return '62';
			case 'p37':
				return '63';
			case 'p38':
				return 0;//'Loan - Loan Received - By Others - Private Short term';
			case 'p39':
				return 0;//'Loan - Loan Received - By Others - Private Long term';
			case 'p40':
				return 0;//'Loan - Loan Received - By Others - Government';
			case 'p41':
				return '65';
			case 'p42':
				return 0;//'Interbank';
			case 'p43':
				return '67';
			default:
				return 0;
		}
	}
	
}
