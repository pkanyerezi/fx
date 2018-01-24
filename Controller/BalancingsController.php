<?php
App::uses('AppController', 'Controller');
/**
 * Openings Controller
 *
 * @property Opening $Opening
 */
class BalancingsController extends AppController {
	public $uses=array('Opening','Currency','OtherCurrency','PurchasedReceipt','SoldReceipt','Expense','Item','CashAtBankForeign','CashAtBankUgx','Debtor','Creditor','Fox','Receivable','Withdrawal','AdditionalProfit','SafeTransaction');
	
	function beforeFilter() {
        parent::beforeFilter();	
        if (!empty($_REQUEST['apiRequest']))	{
        	$this->Auth->allow();
        } else {
	        if ($this->action == 'show_cash_flow' || $this->action == 'show_generally') {
				if($this->Auth->user('role')!='super_admin'){
					$this->Session->setFlash(__('Access Denied!!'),'flash_error');
					$this->redirect($this->Auth->logout());
				}
	        }
    	}
    } 
    
    
	public function generate_excel_cash_flow(){
		
		$items=array();
		$result=array();
		ignore_user_abort(1);
		ini_set('max_execution_time', 600);
		
		if(isset($_REQUEST['date_from']) && isset($_REQUEST['date_to'])){
			$date_from	=($_REQUEST['date_from']);
			$date_to	=($_REQUEST['date_to']);
			
			if(strtotime($date_to) < strtotime($date_from)){
				//Invalid date range
			}
			
			//recursion
			$this->Expense->recursive=-1;
			$this->Item->recursive=-1;
			$this->Opening->recursive=-1;
			
			//Item
			$items=$this->Item->find('all');
			
			$result=array('CashFlow'=>array(),'Dates'=>array());
			
			$loop_date_from=$date_from;
			while(strtotime($loop_date_from) <= strtotime($date_to)){
				array_push($result['Dates'],$loop_date_from);
				$result['CashFlow'][''.$loop_date_from]['items']=array();
				$result['CashFlow'][''.$loop_date_from]['others']=array();
				
				$total_items_expenses=0;
				foreach($items as $item){
					////get total expense for item on loop_date_from
					$expense=$this->Expense->find('all',array(
							'fields'=>array(
								'SUM(amount) as total_amount'
							),
							'conditions'=>array(
								'Expense.item_id'=>$item['Item']['id'],
								'Expense.date'=>$loop_date_from
							)
						)
					);
					$total_items_expenses+=$expense[0][0]['total_amount'];
					array_push($result['CashFlow'][''.$loop_date_from]['items'],$expense[0][0]['total_amount']);
				}
				
				$opening=$this->Opening->find('all',array(
					'fields'=>array(
						'SUM(total_gross_profit) as total_gross_profit',
						'SUM(additional_profits) as total_additional_profits'
					),
					'conditions'=>array(
						'Opening.date'=>$loop_date_from
					)
				));
				
				// $opening[0][0]['total_gross_profit'] = 22;

				array_push($result['CashFlow'][''.$loop_date_from]['others'],$opening[0][0]['total_gross_profit']);
				array_push($result['CashFlow'][''.$loop_date_from]['others'],$total_items_expenses);
				array_push($result['CashFlow'][''.$loop_date_from]['others'],$opening[0][0]['total_additional_profits']);
				
				date_default_timezone_set('Africa/Nairobi');
				$loop_date_from=date('Y-m-d',strtotime("+1 day",strtotime($loop_date_from)));//move to next date
			}
		}
		//$this->set(compact('result','items'));
		App::import('Vendor', 'PHPExcel/PHPExcel');
		$objPHPExcel = new PHPExcel();
		// Add some data
		$objPHPExcel->setActiveSheetIndex(0);
		
		
		//SET HEADER
		//@param $col,$row,$data
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0, 1, 'Date');
		$item_totals=array('Items'=>array());
		$col=1;
		$row=1;
		foreach($items as $item){
			array_push($item_totals['Items'],0);
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, 1, $item['Item']['name']);
			$col++;
		}
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Gross Profit');$col++;array_push($item_totals['Items'],0);
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Total Expenses');$col++;array_push($item_totals['Items'],0);
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Total Additional Profits');$col++;array_push($item_totals['Items'],0);
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Total Net Profit');$col++;array_push($item_totals['Items'],0);
		
		
		//Add other fields with the data
		foreach($result['Dates'] as $cfDate){
			$row++;
			$col=0;
			
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $cfDate);$col++;
			
			$item_count=0; 
			foreach($result['CashFlow'][''.$cfDate]['items'] as $item){
				$item_totals['Items'][$item_count]+=$item;
				$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $item);$col++;				
				$item_count++;
			}
			
			$item_totals['Items'][count($item_totals['Items'])-3]+=$result['CashFlow'][''.$cfDate]['others'][1];			
			$item_totals['Items'][count($item_totals['Items'])-4]+=$result['CashFlow'][''.$cfDate]['others'][0];
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $result['CashFlow'][''.$cfDate]['others'][0]);$col++;

			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $result['CashFlow'][''.$cfDate]['others'][1]);$col++;	
			
			$item_totals['Items'][count($item_totals['Items'])-2]+=$result['CashFlow'][''.$cfDate]['others'][2];
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $result['CashFlow'][''.$cfDate]['others'][2]);$col++;
			
			$total_net_profit=(($result['CashFlow'][''.$cfDate]['others'][0]+$result['CashFlow'][''.$cfDate]['others'][2])-$result['CashFlow'][''.$cfDate]['others'][1]);
			
			$item_totals['Items'][count($item_totals['Items'])-1]+=$total_net_profit;			
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $total_net_profit);$col++;
			
		}
		
		$row++;$col=0;
		$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, 'Total (UGX)');$col++;
		
		foreach($item_totals['Items'] as $overall_total){
			$objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, $row, $overall_total);$col++;
		}
		
		
		// Rename sheet
		$objPHPExcel->getActiveSheet()->setTitle('Cash Flow');		
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		
		if (isset($_REQUEST['apiRequest'])) {
			$newFileName = "ExcelFiles/templates/apirequest_cashflow_" . $_REQUEST['apiRequest'] . ".xls"; 
		} else {
			$newFileName = 'ExcelFiles/cashflow.xls';
		}
		
		$objWriter->save($newFileName);

		if(isset($_REQUEST['apiRequest'])) {
			echo json_encode([
				'apiRequest'=>$_REQUEST['apiRequest'],
				'filename'=>$newFileName
			]);
			exit();
		}
	}
	
	public function show_cash_flow(){
		if(isset($_REQUEST['date_from']) && isset($_REQUEST['date_to'])){
			$date_from	=($_REQUEST['date_from']);
			$date_to	=($_REQUEST['date_to']);
			
			if(strtotime($date_to) < strtotime($date_from)){
				//Invalid date range
			}
			
			//recursion
			$this->Expense->recursive=-1;
			$this->Item->recursive=-1;
			$this->Opening->recursive=-1;
			
			//Item
			$items=$this->Item->find('all');
			
			$result=array('CashFlow'=>array(),'Dates'=>array());

			$fox=$this->Session->read('fox');
			$safe = null;
			if (isset($fox['Fox']['balance_with_safe']) && !empty($fox['Fox']['balance_with_safe'])) {
				$safe = $this->User->find('first',[
					'conditions'=>['User.is_safe'=>1],
					'recursive'=>-1,
					'fields'=>['User.id']
				]);
			}

			$conditions_expenses = [];
			$conditions_openings = [];
			if (!empty($safe)) {
				$conditions_expenses['Expense.user_id']=$safe['User']['id'];	
				$conditions_openings['Opening.user_id']=$safe['User']['id'];	
			}
			
			$loop_date_from=$date_from;
			while(strtotime($loop_date_from) <= strtotime($date_to)){
				array_push($result['Dates'],$loop_date_from);
				$result['CashFlow'][''.$loop_date_from]['items']=array();
				$result['CashFlow'][''.$loop_date_from]['others']=array();
				
				$total_items_expenses=0;
				foreach($items as $item){
					////get total expense for item on loop_date_from
					$expense=$this->Expense->find('all',array(
							'fields'=>array(
								'SUM(amount) as total_amount'
							),
							'conditions'=>array_merge($conditions_expenses,[
								'Expense.item_id'=>$item['Item']['id'],
								'Expense.date'=>$loop_date_from
							])
						)
					);
					$total_items_expenses+=$expense[0][0]['total_amount'];
					array_push($result['CashFlow'][''.$loop_date_from]['items'],$expense[0][0]['total_amount']);
				}
				
				$opening=$this->Opening->find('all',array(
					'fields'=>array(
						'SUM(total_gross_profit) as total_gross_profit',
						'SUM(additional_profits) as total_additional_profits'
					),
					'conditions'=>array_merge($conditions_openings,[
						'Opening.date'=>$loop_date_from
					])
				));
				
				array_push($result['CashFlow'][''.$loop_date_from]['others'],$opening[0][0]['total_gross_profit']);
				array_push($result['CashFlow'][''.$loop_date_from]['others'],$total_items_expenses);
				array_push($result['CashFlow'][''.$loop_date_from]['others'],$opening[0][0]['total_additional_profits']);
				
				date_default_timezone_set('Africa/Nairobi');
				$loop_date_from=date('Y-m-d',strtotime("+1 day",strtotime($loop_date_from)));//move to next date
			}
			$this->set(compact('result','items'));
		}
	}
	
	public function save_opening(){
		if ($this->request->is('post')) {			
			
			if(!isset($this->request->data['Opening']['date'])){
				$this->Session->setFlash(__('Please select when the next opening will occur. Thanks.'),'flash_warning');
				$this->redirect(array('action' => 'show_individually'));
			}
			
			
			$total_expenses				=	$this->Session->read('total_expenses');			
			$currencies					=	$this->Session->read('currencies');
			$other_currencies			=	$this->Session->read('other_currencies');
			$purchases					=	$this->Session->read('purchases');
			$purchases_pre				=	$this->Session->read('purchases_pre');
			$other_currencies_purchases	=	$this->Session->read('other_currencies_purchases');
			$other_currencies_purchases_pre	=	$this->Session->read('other_currencies_purchases_pre');
			$openings					=	$this->Session->read('openings');
			// $opening_safe				=	$this->Session->read('opening_safe');
			$sales						=	$this->Session->read('sales');	
			$other_currencies_sales		=	$this->Session->read('other_currencies_sales');	
			$cash_at_bank_foreign		=	$this->Session->read('cash_at_bank_foreign');	
			$cash_at_bank_ugx			=	$this->Session->read('cash_at_bank_ugx');	
			$debtors					=	$this->Session->read('debtors');	
			$creditors					=	$this->Session->read('creditors');
			
			$receivable_cash=0;//(M)
			$withdrawal_cash=0;//(M)
			$additional_profits=0;//(M)
			
			$receivable_cash			=	$this->Session->read('receivable_cash');
			$withdrawal_cash			=	$this->Session->read('withdrawal_cash');
			$additional_profits			=	$this->Session->read('additional_profits');

			$safe_transactions_received = $this->Session->read('safe_transactions_received');
			$safe_transactions_sent = $this->Session->read('safe_transactions_sent');
			$safe_transactions_received_ugx = $this->Session->read('safe_transactions_received_ugx');
			$safe_transactions_sent_ugx = $this->Session->read('safe_transactions_sent_ugx');
			$other_currencies_safe_transactions_received = $this->Session->read('other_currencies_safe_transactions_received');
			$other_currencies_safe_transactions_sent = $this->Session->read('other_currencies_safe_transactions_sent');
		
			
			//Date validation
			$ts1 = strtotime($openings[0]['Opening']['date']);
			$ts2 = strtotime($this->request->data['Opening']['date']);
			$seconds_diff = $ts2 - $ts1;
			
			if($seconds_diff<=0){
				$this->Session->setFlash(__('Please select a date greater than today for the next opening. Thanks.'),'flash_warning');
				$this->redirect(array('action' => 'show_individually'));
			}
			
			//Check for weekends
			$fox=$this->Session->read('fox');
			$balance_with_safe = 0;
			if (isset($fox['Fox']['balance_with_safe'])) {
				$balance_with_safe = $fox['Fox']['balance_with_safe'];
			}
			
			
			$is_safe = 0;
			$userDetails = $this->Session->read('userDetails');
			if (isset($userDetails['User']['is_safe'])) {
				$is_safe = $userDetails['User']['is_safe'];
			}
			
			$balance_with_purchases = 0;
			if (isset($userDetails['User']['balance_with_all_purchases_from_other_cashiers'])) {
				$balance_with_purchases = $userDetails['User']['balance_with_all_purchases_from_other_cashiers'];
			}
			
			$weekends=explode(',',$fox['Fox']['weekends']);
			foreach($weekends as $weekend){
				if($ts2==strtotime($weekend)){
					$this->Session->setFlash(__('Please select a working day.'),'flash_warning');
					$this->redirect(array('action' => 'show_individually'));
				}
			}
			
			if($seconds_diff<=0){
				$this->Session->setFlash(__('Please select a date greater than today for the next opening. Thanks.'),'flash_warning');
				$this->redirect(array('action' => 'show_individually'));
			}
			
			
			
			$func=$this->Func;
			$_id=(((string)Configure::read('foxId')).''.((string)date('Ymd',strtotime($this->request->data['Opening']['date']))));			
			$this->request->data['Opening']['id']=$_id;
			$this->request->data['Opening']['date']=$this->request->data['Opening']['date'];
			
			$total_purchases_ugx=0;
			$total_purchases=0;
			$total_sales_ugx=0;
			$total_sales=0;
			$total_profits=0;
			$total_gross_profit=0;
			$total_todays_close=0;
			$total_todays_close_ugx=0;
			$expenses=0;//(C)
			
			if(isset($total_expenses[0][0]['total_expenses']))
				$expenses=(double)$total_expenses[0][0]['total_expenses'];
			$count=-1;
			$transfers_made = [];//Stores the sent and received of each currency
			
			$transfers_made[] = array(
				'CID'=>'ugx',
				'CNAME'=>'UGX',
				'SENT'=>$safe_transactions_sent_ugx,
				'RECEIVED'=>$safe_transactions_received_ugx
			);

			$arr['data']=array();
			$currencies_added = [];
			foreach($currencies as $currency):
				$count++;
					
				//pr($openings[0]);exit;	
				$currency_details = json_decode($openings[0]['OpeningDetail']['currency_details'],true);
				
				@$_amount_ugx = $currency_details[$currency['Currency']['id']]['CRATE'] * $currency_details[$currency['Currency']['id']]['CAMOUNT'];
				@$_purchase_ugx = $purchases[$count]['av_rate'] * $purchases[$count]['total_amount'];
				@$av_close_rate = ($_amount_ugx + $_purchase_ugx)/($purchases[$count]['total_amount'] + $currency_details[$currency['Currency']['id']]['CAMOUNT']);
				if(empty($av_close_rate)) $av_close_rate=0;

				if(empty($_purchase_ugx)){
					@$_amount_ugx = $currency_details[$currency['Currency']['id']]['CRATE'] * $currency_details[$currency['Currency']['id']]['CAMOUNT'];
					@$_purchase_ugx = $purchases_pre[$count]['av_rate'] * $purchases_pre[$count]['total_amount'];
					@$av_close_rate = ($_amount_ugx + $_purchase_ugx)/($purchases_pre[$count]['total_amount'] + $currency_details[$currency['Currency']['id']]['CAMOUNT']);
				}

				if(empty($av_close_rate)) @$av_close_rate = $purchases_pre[$count]['av_rate'];

				if(empty($av_close_rate)) @$av_close_rate = $currency_details[$currency['Currency']['id']]['CRATE'];
				
				//Set New Av rate for saving as closing rate
				// $this->request->data['Opening'][$currency['Currency']['id'].'r']=$av_close_rate;
				$currency_details_new[$currency['Currency']['id']]['CRATE'] = $av_close_rate;
				
				
				//New amount left
				@$todays_close=(($currency_details[$currency['Currency']['id']]['CAMOUNT'])+($purchases[$count]['total_amount']))-($sales[$count]['total_amount']);
				if ($balance_with_safe && $is_safe) {
					@$todays_close=(($currency_details[$currency['Currency']['id']]['CAMOUNT']));
				}
				
				if($balance_with_purchases && !$is_safe){
					@$todays_close=(($currency_details[$currency['Currency']['id']]['CAMOUNT']))-($sales[$count]['total_amount']);
				}
			
				//CASHIER CASH TRANSFER
				//Use the cashier amount transfered to to affect the next opening amount
				$received=0;
				if(isset($safe_transactions_received[$count]['total_amount'])){
					$received = $safe_transactions_received[$count]['total_amount'];
				}
				$sent=0;
				if(isset($safe_transactions_sent[$count]['total_amount'])){
					$sent = $safe_transactions_sent[$count]['total_amount'];
				}

				$todays_close += ($received-$sent);
				if(!in_array($currency['Currency']['id'],['c8','c00'])){
					$transfers_made[] = array(
						'CID'=>$currency['Currency']['id'],
						'CNAME'=>$currency['Currency']['id'],
						'SENT'=>$sent,
						'RECEIVED'=>$received
					);
				}

				//Set New amount for saving as closing amount for the foreign currency
				$currency_details_new[$currency['Currency']['id']]['CAMOUNT']=$todays_close;
				
				$GP = $sales[$count]['total_amount']*($sales[$count]['av_rate']-$av_close_rate);
				
				//@$GP = $sales[$count]['total_amount_ugx'] - ($sales[$count]['total_amount']*$purchases[$count]['av_rate']);
				
				//$purchase_rate = ($purchases[$count]['av_rate']==0)? $av_close_rate : $purchases[$count]['av_rate'];
				//$GP = $sales[$count]['total_amount_ugx'] - ($sales[$count]['total_amount']*$purchase_rate);
				//$GP = ($sales[$count]['av_rate']-$av_close_rate) *$sales[$count]['total_amount'];
					
				
				$NP=($GP);		
				if ($currency['Currency']['is_other_currency']) {
					@$currency_details_new[$currency['Currency']['id']]['CAMOUNT'] = $data_other_currencies[$currency['Currency']['id']]['CAMOUNT'];
					@$openings[0]['Opening'][$currency['Currency']['id'].'r'] = $data_other_currencies[$currency['Currency']['id']]['CRATE'];

					$currency['Currency']['description'] = $currency['Currency']['id'];

					
				}

				$detail = [
					'CID'=>$currency['Currency']['id'],
					''.($currency['Currency']['id'])=>$currency['Currency']['id'],
					'CRATE'=>$av_close_rate,
					'CAMOUNT'=>$todays_close,
					'CNAME'=>$currency['Currency']['id'],
					'SENT'=>$sent,
					'RECEIVED'=>$received
				];
				$currencies_added[$detail['CID']] = $detail;

				$total_gross_profit+=$GP;
				$total_profits+=$NP;
				
				$total_purchases+=$purchases[$count]['total_amount'];
				$total_sales+=$sales[$count]['total_amount'];
				
				$total_purchases_ugx+=$purchases[$count]['total_amount']*$purchases[$count]['av_rate'];
				$total_sales_ugx+=$sales[$count]['total_amount']*$sales[$count]['av_rate'];
				
			endforeach;
			$this->request->data['Opening']['other_currencies']=json_encode($currencies_added);
			$this->request->data['Opening']['transfers_made'] = json_encode($transfers_made);
			
			$total_profits+=$additional_profits;//Include additional_profits
			
			//New cash at hand to be the opening cash for the next day selected
			if ($balance_with_safe && $is_safe){
				$cash_at_hand=((($expenses)+$openings[0]['Opening']['opening_ugx']+$receivable_cash+$additional_profits)-($withdrawal_cash));
			}elseif($balance_with_purchases && !$is_safe){
				$cash_at_hand=(($total_sales_ugx-($expenses)+$openings[0]['Opening']['opening_ugx']+$receivable_cash+$additional_profits)-($withdrawal_cash));
			}else{
				$cash_at_hand=(($total_sales_ugx-($expenses)+$openings[0]['Opening']['opening_ugx']+$receivable_cash+$additional_profits)-($total_purchases_ugx+$withdrawal_cash));
			}
			
			//Final cash at hand
			/*$cash_at_hand=($cash_at_hand)-($cash_at_bank_foreign+$cash_at_bank_ugx);
			$cash_at_hand_b=$cash_at_hand+$creditors;
			$cash_at_hand_e=$cash_at_hand_b-$debtors;
			$cash_at_hand_f=$cash_at_hand_e-$creditors;
			$cash_at_hand_g=$cash_at_hand_f+$debtors;
			
			$cash_at_hand=$cash_at_hand_g;
			*/
			$cash_at_hand=($cash_at_hand+$creditors)-($cash_at_bank_foreign+$cash_at_bank_ugx+$debtors);

			//CASHIER CASH TRANSFER
			//Use the cashtranfers to affect the next openingUGX incase there was any UGX transfer
			$cash_at_hand = $cash_at_hand + $safe_transactions_received_ugx - $safe_transactions_sent_ugx;
			
			$this->request->data['Opening']['opening_ugx']=$cash_at_hand;
			if($this->Auth->User('role')=='super_admin' and isset($this->request->data['Opening_old']['user_id'])){
				$this->request->data['Opening']['user_id']=$this->request->data['Opening_old']['user_id'];
				$this->request->data['Opening']['id'].='_'.$this->request->data['Opening_old']['user_id'];
			}else{
				$this->request->data['Opening']['user_id']=$this->Auth->User('id');
				$this->request->data['Opening']['id'].='_'.$this->Auth->User('id');
			}
			
			$this->request->data['Opening']['status']=0;
			
			if(1){
			if ($this->Opening->save($this->request->data)) {

				$data['currency_details'] = json_encode($currencies_added);
				
				// Check if there's already an opening for the next day that we are trying to resave
				$next_opening = $this->Opening->find('first',[
					'conditions'=>[
						'Opening.id'=>$this->request->data['Opening']['id']
					],
					'fields'=>[
						'Opening.id','Opening.opening_detail_id'
					],
					'recursive'=>-1
				]);
				
				if (!empty($next_opening['Opening']['opening_detail_id'])) {
					$data['id'] = $next_opening['Opening']['opening_detail_id'];
				}else{
					// Create a new ID
					$lastRecord = $this->Opening->OpeningDetail->find('first',['order'=> 'OpeningDetail.id DESC']);
					if (empty($lastRecord)) {
						$data['id'] = 1;
					}else{
						$data['id'] = $lastRecord['OpeningDetail']['id'] + 1;
					}
				}
				
				$this->Opening->set('opening_detail_id', 				$data['id']);
				$this->Opening->save();

				// Save OpeningDetail
				if ($this->Opening->OpeningDetail->save(['OpeningDetail'=>$data])) {
					
					$this->Opening->set('id', 				$openings[0]['Opening']['id']);
					$this->Opening->set('opening_detail_id', $openings[0]['Opening']['opening_detail_id']);
					$this->Opening->set('status', 				1);
					$this->Opening->set('total_profit', 		$total_profits);
					$this->Opening->set('total_gross_profit', 	$total_gross_profit);
					$this->Opening->set('total_expenses', 		$expenses);
					$this->Opening->set('receivable_cash', 		$receivable_cash);
					$this->Opening->set('withdrawal_cash', 		$withdrawal_cash);
					$this->Opening->set('total_purchases_ugx', 	$total_purchases_ugx);
					$this->Opening->set('total_sales_ugx', 		$total_sales_ugx);
					$this->Opening->set('additional_profits', 	$additional_profits);
					$this->Opening->set('cash_at_bank_foreign', $cash_at_bank_foreign);
					$this->Opening->set('cash_at_bank_ugx', 	$cash_at_bank_ugx);
					$this->Opening->set('debtors', 				$debtors);
					$this->Opening->set('creditors', 			$creditors);
					if(isset($this->request->data['Opening_old']['total_todays_close_ugx'])){
						$this->Opening->set('close_ugx', $this->request->data['Opening_old']['total_todays_close_ugx']);
					}
					
					if(($this->Opening->save())){					
						$this->Session->setFlash(__('Saved.'),'flash_success');
					}else{
						$this->Opening->id = $this->request->data['Opening']['id'];
						$this->Opening->delete();

						$this->Opening->OpeningDetail->id = $data['id'];
						$this->Opening->OpeningDetail->delete();
						$this->Session->setFlash(__('Not saved. Please, try again.'),'flash_error');
					}
				}else{
					$this->Opening->id = $this->request->data['Opening']['id'];
					$this->Opening->delete();
					$this->Session->setFlash(__('Details Not saved. Please, try again.'),'flash_error');
				}
			} else {
				$this->Session->setFlash(__('Not saved. Please, try again.'),'flash_error');
			}			
			}
			$this->redirect(array('action' => 'show_individually'));
			
		}
		$this->Session->setFlash(__('Opening not saved. Please try again.'),'flash_error');
		$this->redirect(array('action' => 'show_individually'));
	}
	
	public function clean_safe_transactions(){
		$currencies = $this->Currency->find('list',['limit'=>0]);
		//pr($currencies);
		foreach($currencies as $key=>$val){
			$this->Currency->query("update safe_transactions set currency='$key' where currency='$key-$key'");
		}
		$currencies_old = [
			'Euro-EUR'=>'EUR',
			'UGX-ugx'=>'UGX',
			'Kshs-KES'=>'KES',
			'SAR-ZAR'=>'ZAR',
			'Tzshs-TZS'=>'TZS'
			
		];
		foreach($currencies_old as $key=>$val){
			$this->Currency->query("update safe_transactions set currency='$val' where currency='$key'");
		}
		/*
		foreach($currencies_old as $key=>$val){
			$this->Currency->query("update safe_transactions set currency='$key' where currency='$val'");
		}*/
		exit;
	}
	
	public function show_individually($receivable_cash=0,$withdrawal_cash=0,$additional_profits=0,$user_id=null) {
		$userDetails = $this->User->find('first',['conditions'=>['User.id'=>(($this->Auth->User('role')=='super_admin' and $user_id)?$user_id:$this->Auth->User('id'))]]);
		date_default_timezone_set('Africa/Nairobi');
		$date_today=date('Y-m-d');
		if(isset($_REQUEST['date_today'])){
			$date_today	=($_REQUEST['date_today']);
		}

		$this->set('userDetails',$userDetails);
		$this->Session->write('userDetails',$userDetails);
		
		$total_expenses=0;
		
		$total_expenses=$this->Expense->find('all',array(
			'recursive'=>-1,
			'fields'=>array(
				'SUM(Expense.amount) as total_expenses'					
			),
			'conditions'=>array(
				'Expense.user_id'=>(($this->Auth->User('role')=='super_admin' and $user_id)?$user_id:$this->Auth->User('id')),
				'Expense.date'=>$date_today
			)
		));	
		$opening_safe = [];
		if($userDetails['User']['balance_with_all_purchases_from_other_cashiers']){

			//use the openings from the safe account
			$safe = $this->User->find('first',[
				'conditions'=>[
					'User.is_safe'=>1
				],
				'recursive'=>-1
			]);

			if(empty($safe)){
				$this->Session->setFlash(__('Safe Account Not Found!'));
				$this->redirect(array('controller'=>'users','action' => 'view',$this->Auth->User('id')));
			}
			
			$this->Opening->unBindModel([
				'belongsTo'=>[
					'User'
				]
			]);
			$opening_safe=$this->Opening->find('all',array(
				'recursive'=>0,'Limit'=>1,
				'conditions'=>array(
					'Opening.user_id'=>$safe['User']['id'],
					'Opening.date'=>$date_today,
					//'Opening.status'=>0,//fetch only new openings
				),
				'order'=>'Opening.date desc'
			));
		}
		
		$this->Opening->unBindModel([
			'belongsTo'=>[
				'User'
			]
		]);
		$openings=$this->Opening->find('all',array(
			'recursive'=>0,'Limit'=>1,
			'conditions'=>array(
				'Opening.user_id'=>(($this->Auth->User('role')=='super_admin' and $user_id)?$user_id:$this->Auth->User('id')),
				'Opening.date'=>$date_today,
				//'Opening.status'=>0,//fetch only new openings
			),
			'order'=>'date desc'
		));
		
		$currencies=$this->Currency->find('all',array(
			'recursive'=>-1,
			'conditions'=>array(
				'NOT'=>[
					'Currency.id'=>['c00','c8']
				]
			),
			'order'=>'Currency.is_other_currency ASC,Currency.arrangement ASC, Currency.id ASC'
		));
		
		$other_currencies=$this->OtherCurrency->find('all',array('recursive'=>-1,));
		
		$purchases=array();
		$purchases_pre=array();
		$sales=array();
		$safe_transactions_received = array();
		$safe_transactions_sent = array();
		$cash_at_bank_foreign=0;
		$cash_at_bank_ugx=0;
		$debtors=0;
		$creditors=0;
		$other_currencies_sales=array();
		$other_currencies_purchases=array();
		$other_currencies_purchases_pre=array();
		$other_currencies_safe_transactions_received = array();
		$other_currencies_safe_transactions_sent = array();
		
		
		
		foreach($currencies as $currency){

			if($userDetails['User']['balance_with_all_purchases_from_other_cashiers']){
				$conditions_purchase = array(
					'NOT'=>array(
						'PurchasedReceipt.instrument'=>'TT'
					),
					'PurchasedReceipt.currency_id'=>$currency['Currency']['id'],
					'PurchasedReceipt.date'=>$date_today
				);

				$conditions_sold = array(
					'SoldReceipt.currency_id'=>$currency['Currency']['id'],
					'SoldReceipt.date'=>$date_today,
					'NOT'=>array(
						'SoldReceipt.instrument'=>'TT'
					)
				);
			}else{
				$conditions_purchase = array(
					'NOT'=>array(
						'PurchasedReceipt.instrument'=>'TT'
					),
					'PurchasedReceipt.currency_id'=>$currency['Currency']['id']	,
					'PurchasedReceipt.user_id'=>(($this->Auth->User('role')=='super_admin' and $user_id)?$user_id:$this->Auth->User('id')),
					'PurchasedReceipt.date'=>$date_today
				);

				$conditions_sold = array(
					'SoldReceipt.currency_id'=>$currency['Currency']['id']	,
					'SoldReceipt.user_id'=>(($this->Auth->User('role')=='super_admin' and $user_id)?$user_id:$this->Auth->User('id')),
					'SoldReceipt.date'=>$date_today,
					'NOT'=>array(
						'SoldReceipt.instrument'=>'TT'
					)
				);
			}

			$_purchases=$this->PurchasedReceipt->find('all',array(
				'recursive'=>-1,
				'fields'=>array(
					'SUM(PurchasedReceipt.amount) as total_amount',
					'AVG(PurchasedReceipt.rate) as av_rate',
					'SUM(PurchasedReceipt.amount_ugx) as total_amount_ugx'
				),
				'conditions'=>$conditions_purchase
			));

			// _purchases_pre
			$_purchases_pre_conditions = array(
				'NOT'=>array(
					'PurchasedReceipt.instrument'=>'TT'
				),
				'PurchasedReceipt.currency_id'=>$currency['Currency']['id']	,
				'PurchasedReceipt.date'=>$date_today
			);
			if (!$userDetails['User']['balance_with_all_purchases_from_other_cashiers']) {
				$_purchases_pre_conditions['PurchasedReceipt.user_id']=(($this->Auth->User('role')=='super_admin' and $user_id)?$user_id:$this->Auth->User('id'));
			}

			$_purchases_pre=$this->PurchasedReceipt->find('all',array(
				'recursive'=>-1,
				'fields'=>array(
					'SUM(PurchasedReceipt.amount) as total_amount',
					'AVG(PurchasedReceipt.rate) as av_rate',
					'SUM(PurchasedReceipt.amount_ugx) as total_amount_ugx'
				),
				'conditions'=>$_purchases_pre_conditions
			));
		
			
			
			$_sales=$this->SoldReceipt->find('all',array(
				'recursive'=>-1,
				'fields'=>array(
					'SUM(SoldReceipt.amount) as total_amount',
					'AVG(SoldReceipt.rate) as av_rate',
					'SUM(SoldReceipt.amount_ugx) as total_amount_ugx',
				),
				'conditions'=>$conditions_sold
			));
			
			$_cashAtBankForeign=$this->CashAtBankForeign->find('all',array(
				'recursive'=>-1,
				'conditions'=>array(
					'CashAtBankForeign.currency_id'=>$currency['Currency']['id']	,
					'CashAtBankForeign.user_id'=>(($this->Auth->User('role')=='super_admin' and $user_id)?$user_id:$this->Auth->User('id')),
					'CashAtBankForeign.date'=>$date_today
				),
				'fields'=>array(
					'SUM(CashAtBankForeign.amount) as total_amount'
				)
			));
			$_safe_transactions_received = $this->SafeTransaction->find('all',[
				'conditions'=>[
					'DATE(SafeTransaction.date)'=>$date_today,
					'SafeTransaction.transaction_to'=>(($this->Auth->User('role')=='super_admin' and $user_id)?$user_id:$this->Auth->User('id')),
					'SafeTransaction.status'=>'ACCEPTED',
					'SafeTransaction.currency'=>$currency['Currency']['id'],
					//'SafeTransaction.currency LIKE'=>'%-'.$currency['Currency']['id'].'%',
				],
				'recursive'=>-1,
				'fields'=>[
					'SUM(SafeTransaction.amount) as total_amount',
				]
			]);
			$_safe_transactions_sent = $this->SafeTransaction->find('all',[
				'conditions'=>[
					'DATE(SafeTransaction.date)'=>$date_today,
					'SafeTransaction.transaction_from'=>(($this->Auth->User('role')=='super_admin' and $user_id)?$user_id:$this->Auth->User('id')),
					'SafeTransaction.status'=>'ACCEPTED',
					'SafeTransaction.currency'=>$currency['Currency']['id'],
					//'SafeTransaction.currency LIKE'=>'%-'.$currency['Currency']['id'].'%',
				],
				'recursive'=>-1,
				'fields'=>[
					'SUM(SafeTransaction.amount) as total_amount',
				]
			]);
			
			
			array_push($purchases,
				array(
					'total_amount'=>$_purchases[0][0]['total_amount'],
					'av_rate'=>(($_purchases_pre[0][0]['total_amount_ugx']==0)?0:($_purchases_pre[0][0]['total_amount_ugx']/$_purchases_pre[0][0]['total_amount'])),
					'total_amount_ugx'=>$_purchases[0][0]['total_amount_ugx']
				)
			);

			array_push($purchases_pre,
				array(
					'total_amount'=>$_purchases_pre[0][0]['total_amount'],
					'av_rate'=>(($_purchases_pre[0][0]['total_amount_ugx']==0)?0:($_purchases_pre[0][0]['total_amount_ugx']/$_purchases_pre[0][0]['total_amount'])),
					'total_amount_ugx'=>$_purchases_pre[0][0]['total_amount_ugx']
				)
			);

			array_push($sales,
				array(
					'total_amount'=>$_sales[0][0]['total_amount'],
					'av_rate'=>(($_sales[0][0]['total_amount_ugx']==0)?0:($_sales[0][0]['total_amount_ugx']/$_sales[0][0]['total_amount'])),
					'total_amount_ugx'=>$_sales[0][0]['total_amount_ugx']
				)
			);

			array_push($safe_transactions_received,
				array('total_amount'=>$_safe_transactions_received[0][0]['total_amount'])
			);
			array_push($safe_transactions_sent,
				array('total_amount'=>$_safe_transactions_sent[0][0]['total_amount'])
			);
			
			if(isset($_cashAtBankForeign[0][0]['total_amount'])){

				$opening = $openings[0];
				if(isset($opening_safe)){
					$opening = $opening_safe[0];
				}

				//$cash_at_bank_foreign+=$_cashAtBankForeign[0][0]['total_amount']*$_purchases[0][0]['av_rate'];
				$av_ugx=($_purchases[0][0]['total_amount']*$_purchases[0][0]['av_rate'])+(($opening['Opening'][$currency['Currency']['id'].'a'])*($opening['Opening'][$currency['Currency']['id'].'r']));
				$av_rate=($_purchases[0][0]['total_amount'])+($opening['Opening'][$currency['Currency']['id'].'a']);
				
				//New Av Closing rate
				$av_close_rate= ($av_rate!=0)?$av_ugx/$av_rate:0;			
				
				$cash_at_bank_foreign+=$_cashAtBankForeign[0][0]['total_amount']*$av_close_rate;
			}
		}

		$_safe_transactions_sent_ugx = $this->SafeTransaction->find('all',[
			'conditions'=>[
				'DATE(SafeTransaction.date)'=>$date_today,
				'SafeTransaction.transaction_from'=>(($this->Auth->User('role')=='super_admin' and $user_id)?$user_id:$this->Auth->User('id')),
				'SafeTransaction.status'=>'ACCEPTED',
				'SafeTransaction.currency'=>'UGX',
			],
			'recursive'=>-1,
			'fields'=>[
				'SUM(SafeTransaction.amount) as total_amount',
			]
		]);
		$_safe_transactions_received_ugx = $this->SafeTransaction->find('all',[
			'conditions'=>[
				'DATE(SafeTransaction.date)'=>$date_today,
				'SafeTransaction.transaction_to'=>(($this->Auth->User('role')=='super_admin' and $user_id)?$user_id:$this->Auth->User('id')),
				'SafeTransaction.status'=>'ACCEPTED',
				'SafeTransaction.currency'=>'UGX',
			],
			'recursive'=>-1,
			'fields'=>[
				'SUM(SafeTransaction.amount) as total_amount',
			]
		]);
		@$safe_transactions_received_ugx = $_safe_transactions_received_ugx[0][0]['total_amount'];
		@$safe_transactions_sent_ugx = $_safe_transactions_sent_ugx[0][0]['total_amount'];
		
		//pr($safe_transactions_received_ugx);
		//pr($safe_transactions_sent_ugx);
		
		$_cashAtBankUgx=$this->CashAtBankUgx->find('all',array(
			'recursive'=>-1,
			'conditions'=>array(
				'CashAtBankUgx.user_id'=>(($this->Auth->User('role')=='super_admin' and $user_id)?$user_id:$this->Auth->User('id')),
				'CashAtBankUgx.date'=>$date_today
			),
			'fields'=>array(
				'SUM(CashAtBankUgx.amount) as total_amount'
			)
		));
		
		$_debtors=$this->Debtor->find('all',array(
			'recursive'=>-1,
			'conditions'=>array(
				'Debtor.user_id'=>(($this->Auth->User('role')=='super_admin' and $user_id)?$user_id:$this->Auth->User('id')),
				'Debtor.date'=>$date_today
			),
			'fields'=>array(
				'SUM(Debtor.amount) as total_amount'
			)
		));
		
		$_creditors=$this->Creditor->find('all',array(
			'recursive'=>-1,
			'conditions'=>array(
				'Creditor.user_id'=>(($this->Auth->User('role')=='super_admin' and $user_id)?$user_id:$this->Auth->User('id')),
				'Creditor.date'=>$date_today
			),
			'fields'=>array(
				'SUM(Creditor.amount) as total_amount'
			)
		));
		
		$_receivables=$this->Receivable->find('all',array(
			'recursive'=>-1,
			'conditions'=>array(
				'Receivable.user_id'=>(($this->Auth->User('role')=='super_admin' and $user_id)?$user_id:$this->Auth->User('id')),
				'Receivable.date'=>$date_today,
				'Receivable.reason'=>'ToBureau'
			),
			'fields'=>array(
				'SUM(Receivable.amount) as total_amount'
			)
		));
		$_withdrawals=$this->Withdrawal->find('all',array(
			'recursive'=>-1,
			'conditions'=>array(
				'Withdrawal.user_id'=>(($this->Auth->User('role')=='super_admin' and $user_id)?$user_id:$this->Auth->User('id')),
				'Withdrawal.date'=>$date_today,
				'Withdrawal.reason'=>'FromBureau'
			),
			'fields'=>array(
				'SUM(Withdrawal.amount) as total_amount'
			)
		));
		$_additionalProfits=$this->AdditionalProfit->find('all',array(
			'recursive'=>-1,
			'conditions'=>array(
				'AdditionalProfit.user_id'=>(($this->Auth->User('role')=='super_admin' and $user_id)?$user_id:$this->Auth->User('id')),
				'AdditionalProfit.date'=>$date_today
			),
			'fields'=>array(
				'SUM(AdditionalProfit.amount) as total_amount'
			)
		));
		
		if(isset($_cashAtBankUgx[0][0]['total_amount'])){
			$cash_at_bank_ugx=$_cashAtBankUgx[0][0]['total_amount'];
		}
		if(isset($_debtors[0][0]['total_amount'])){
			$debtors=$_debtors[0][0]['total_amount'];
		}
		if(isset($_creditors[0][0]['total_amount'])){
			$creditors=$_creditors[0][0]['total_amount'];
		}
		if(isset($_receivables[0][0]['total_amount'])){
			$receivable_cash=$_receivables[0][0]['total_amount'];
		}
		if(isset($_withdrawals[0][0]['total_amount'])){
			$withdrawal_cash=$_withdrawals[0][0]['total_amount'];
		}
		if(isset($_additionalProfits[0][0]['total_amount'])){
			$additional_profits=$_additionalProfits[0][0]['total_amount'];
		}
		
		$this->Session->write('total_expenses',$total_expenses);
		$this->Session->write('openings',$openings);
		$this->Session->write('opening_safe',$opening_safe);
		$this->Session->write('currencies',$currencies);
		$this->Session->write('other_currencies',$other_currencies);
		$this->Session->write('purchases',$purchases);
		$this->Session->write('purchases_pre',$purchases_pre);
		$this->Session->write('other_currencies_purchases',$other_currencies_purchases);
		$this->Session->write('other_currencies_purchases_pre',$other_currencies_purchases_pre);
		$this->Session->write('sales',$sales);	
		$this->Session->write('other_currencies_sales',$other_currencies_sales);
		$this->Session->write('cash_at_bank_foreign',$cash_at_bank_foreign);			
		$this->Session->write('cash_at_bank_ugx',$cash_at_bank_ugx);			
		$this->Session->write('debtors',$debtors);			
		$this->Session->write('creditors',$creditors);
		$this->Session->write('receivable_cash',$receivable_cash);
		$this->Session->write('withdrawal_cash',$withdrawal_cash);
		$this->Session->write('additional_profits',$additional_profits);
		$this->Session->write('safe_transactions_received',$safe_transactions_received);
		$this->Session->write('safe_transactions_sent',$safe_transactions_sent);
		$this->Session->write('safe_transactions_received_ugx',$safe_transactions_received_ugx);
		$this->Session->write('safe_transactions_sent_ugx',$safe_transactions_sent_ugx);
		$this->Session->write('other_currencies_safe_transactions_received',$other_currencies_safe_transactions_received);
		$this->Session->write('other_currencies_safe_transactions_sent',$other_currencies_safe_transactions_sent);
		$this->set(compact('user_id','date_today','openings','opening_safe','currencies','purchases','purchases_pre','sales','total_expenses','receivable_cash','withdrawal_cash','additional_profits','cash_at_bank_foreign','cash_at_bank_ugx','debtors','creditors','other_currencies_purchases','other_currencies_purchases_pre','other_currencies_sales','other_currencies',
			'safe_transactions_received','other_currencies_safe_transactions_received',
			'safe_transactions_sent','other_currencies_safe_transactions_sent',
			'safe_transactions_received_ugx','safe_transactions_sent_ugx'));
		
	}
	
	public function show_generally() {

		$currencies=$this->Currency->find('all',array(
			'recursive'=>-1,
			'conditions'=>array(
				'NOT'=>[
					'Currency.id'=>['c00','c8']
				]
			),
			'order'=>'Currency.is_other_currency ASC,Currency.arrangement ASC, Currency.id ASC',
			'limit'=>0
		));
		$this->set('currencies',$currencies);

		date_default_timezone_set('Africa/Nairobi');
		$date_today=date('Y-m-d');
		if(isset($_REQUEST['date_today'])){
			$date_today	=($_REQUEST['date_today']);
		}

		$conditions_openings = ['Opening.date'=>$date_today];
		/*if ($balance_with_safe) {
			$safe = $this->User->find('first',[
				'conditions'=>[
					'is_safe'=>1,
					'balance_with_all_purchases_from_other_cashiers'=>1
				],
				'fields'=>[
					'User.id'
				],
				'recursive'=>-1
			]);
			if (!empty($safe)) {
				$conditions_openings['Opening.user_id'] = $safe['User']['id'];
			}
		}*/
		
		$openings=$this->Opening->find('all',array(
			'recursive'=>1,'Limit'=>1,
			'conditions'=>$conditions_openings,
			'order'=>'Opening.user_id asc',
		));
		// $other_currencies=$this->OtherCurrency->find('all',array('recursive'=>-1,));
		$other_currencies=$this->Currency->find('all',array('recursive'=>-1,
			'conditions'=>['Currency.is_other_currency'=>1],
			'order'=>'Currency.is_other_currency ASC, Currency.id ASC',
			'limit'=>0
		));
		$this->set('openings',$openings);
		$this->set('other_currencies',$other_currencies);
		
		//But to show the total currencies at the end of the day, we need to get those that have been saved for the next opening day
		date_default_timezone_set('Africa/Nairobi');
		$tommorrow=date('Y-m-d',strtotime("+1 day",strtotime($date_today)));//move to next date
		$openings_tomorrow=$this->Opening->find('all',array(
			'recursive'=>1,'Limit'=>1,
			'conditions'=>array(
				'Opening.date'=>$tommorrow
			),
			//'order'=>'Opening.date desc',
			'order'=>'Opening.user_id asc',
		));
		$this->set('openings_tomorrow',$openings_tomorrow);

		if (empty($openings_tomorrow) && !empty($openings)) {
			// Get the next opening date first
			$openings_tomorrow_date = $this->Opening->find('first',array(
				'recursive'=>1,'Limit'=>1,
				'conditions'=>array(
					'Opening.date >'=>$tommorrow
				),
				'order'=>'Opening.date asc',
				'fields'=>['Opening.date']
			));

			if (!empty($openings_tomorrow_date)) {
				$tommorrow = $openings_tomorrow_date['Opening']['date'];
				$openings_tomorrow=$this->Opening->find('all',array(
					'recursive'=>1,'Limit'=>1,
					'conditions'=>array(
						'Opening.date'=>$tommorrow
					),
					'order'=>'Opening.user_id asc',
				));
			}
		}

		$this->set('date_today',$date_today);
		$this->set('tommorrow',$tommorrow);
	}
	
	public function show_generally_final() {
		date_default_timezone_set('Africa/Nairobi');
		$date_today=date('Y-m-d');
		if(isset($_REQUEST['date_today'])){
			$date_today	=($_REQUEST['date_today']);
		}
		
		$from = (empty($_REQUEST['date_from']))?date('Y-m-d'):$_REQUEST['date_from'];
		$to	  = (empty($_REQUEST['date_from']))?date('Y-m-d'):$_REQUEST['date_to'];
		

		$fox=$this->Session->read('fox');
		$safe = null;
		if (isset($fox['Fox']['balance_with_safe']) && !empty($fox['Fox']['balance_with_safe'])) {
			$safe = $this->User->find('first',[
				'conditions'=>['User.is_safe'=>1],
				'recursive'=>-1,
				'fields'=>['User.id','User.name']
			]);
		}
		
		$conditions = [];
		if (!empty($safe)) {
			$conditions['Opening.user_id']=$safe['User']['id'];	
		}

		$openings=$this->Opening->find('all',array(
			'recursive'=>0,'Limit'=>1,
			'order'=>'Opening.date desc',
			'fields'=>array(
				'SUM(Opening.total_profit) as total_profits',
				'SUM(Opening.total_expenses) as total_expenses'
			),
			'conditions'=>$conditions
		));
		
		$conditions['Opening.date >='] = $from;
		$conditions['Opening.date <='] = $to;
		
		$openings_date_range = $this->Opening->find('all',array(
			'recursive'=>0,'Limit'=>1,
			'order'=>'Opening.date desc',
			'conditions'=>$conditions,
			'fields'=>array(
				'SUM(Opening.total_profit) as total_profits',
				'SUM(Opening.total_gross_profit) as total_gross_profit',
				'SUM(Opening.total_expenses) as total_expenses',
				'SUM(Opening.receivable_cash) as receivable_cash',
				'SUM(Opening.withdrawal_cash) as withdrawal_cash',
				'SUM(Opening.additional_profits) as additional_profits',
				'SUM(Opening.total_sales_ugx) as total_sales_ugx',
				'SUM(Opening.total_purchases_ugx) as total_purchases_ugx',
				'SUM(Opening.debtors) as debtors',
				'SUM(Opening.creditors) as creditors'
			)
		));
		
		$this->set('openings',$openings);
		$this->set('openings_date_range',$openings_date_range);
		$this->set('fox',$fox);
		$this->set('from',$from);
		$this->set('to',$to);
	}
}
