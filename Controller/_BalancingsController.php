<?php
App::uses('AppController', 'Controller');
/**
 * Openings Controller
 *
 * @property Opening $Opening
 */
class BalancingsController extends AppController {
	public $uses=array('Opening','Currency','OtherCurrency','PurchasedReceipt','SoldReceipt','Expense','Item','CashAtBankForeign','CashAtBankUgx','Debtor','Creditor','Fox','Receivable','Withdrawal','AdditionalProfit');
	
	function beforeFilter() {
        parent::beforeFilter();		
        if ($this->action == 'show_cash_flow' || 
			$this->action == 'show_generally') {
			if($this->Auth->user('role')!='super_admin'){
				$this->Session->setFlash(__('Access Denied!!'));
				$this->redirect($this->Auth->logout());
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
						'opening.date'=>$loop_date_from
					)
				));
				
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
		
		$objWriter->save('ExcelFiles/cashflow.xls');
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
						'opening.date'=>$loop_date_from
					)
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
				$this->Session->setFlash(__('Please select when the next opening will occur. Thanks.'));
				$this->redirect(array('action' => 'show_individually'));
			}
			
			
			$total_expenses				=	$this->Session->read('total_expenses');			
			$currencies					=	$this->Session->read('currencies');
			$other_currencies			=	$this->Session->read('other_currencies');
			$purchases					=	$this->Session->read('purchases');
			$other_currencies_purchases	=	$this->Session->read('other_currencies_purchases');
			$openings					=	$this->Session->read('openings');
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
			
			//Date validation
			$ts1 = strtotime($openings[0]['Opening']['date']);
			$ts2 = strtotime($this->request->data['Opening']['date']);
			$seconds_diff = $ts2 - $ts1;
			
			if($seconds_diff<=0){
				$this->Session->setFlash(__('Please select a date greater than today for the next opening. Thanks.'));
				$this->redirect(array('action' => 'show_individually'));
			}
			
			//Check for weekends
			$fox=$this->Session->read('fox');
			$weekends=explode(',',$fox['Fox']['weekends']);
			foreach($weekends as $weekend){
				if($ts2==strtotime($weekend)){
					$this->Session->setFlash(__('Please select a working day.'));
					$this->redirect(array('action' => 'show_individually'));
				}
			}
			
			if($seconds_diff<=0){
				$this->Session->setFlash(__('Please select a date greater than today for the next opening. Thanks.'));
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
			foreach($currencies as $currency):
				$count++;
				
				if($currency['Currency']['id']=='c8'){
					$other_count=-1;
					$_data=json_decode($openings[0]['Opening']['other_currencies']);
					
					$arr['data']=array();
					$openings_other_currencies = array();
					foreach($other_currencies as $other_currency){
						$other_count++;
						$_amount=$_rate=0;
						
						foreach($_data as $_other_currencies){
							foreach($_other_currencies as $_other_currency){	
								if(isset($_other_currency->CID)){
									if($_other_currency->CID==$other_currency['OtherCurrency']['id']){
										$_amount=$_other_currency->CAMOUNT;
										$_rate=$_other_currency->CRATE;
										array_push($openings_other_currencies,array(
											'amount'=>$_amount,
											'av_rate'=>$_rate
										));
									}
								}
							}
						}
						
						//$av_ugx=($other_currencies_purchases[$other_count]['total_amount']*$other_currencies_purchases[$other_count]['av_rate'])+(($_amount)*($_rate));
						//$av_rate=($other_currencies_purchases[$other_count]['total_amount'])+($_amount);
						
						//New Av rate
						//$av_close_rate = ($av_rate!=0)?$av_ugx/$av_rate:0;		
						$av_close_rate = ($other_currencies_purchases[$other_count]['av_rate']==0)?$openings_other_currencies[$other_count]['av_rate']:$other_currencies_purchases[$other_count]['av_rate'];
						
						//New amount left
						$todays_close=(($_amount)+($other_currencies_purchases[$other_count]['total_amount']))-($other_currencies_sales[$other_count]['total_amount']);
						
						
						//$GP = $other_currencies_sales[$other_count]['total_amount']*($other_currencies_sales[$other_count]['av_rate']-$av_close_rate);
						//@$GP = $other_currencies_sales[$count]['total_amount_ugx'] - ($other_currencies_sales[$count]['total_amount']*$other_currencies_purchases[$count]['av_rate']);
						
						$purchase_rate = ($other_currencies_purchases[$other_count]['av_rate']==0)? $av_close_rate : $other_currencies_purchases[$other_count]['av_rate'];
						$GP = $other_currencies_sales[$other_count]['total_amount_ugx'] - ($other_currencies_sales[$other_count]['total_amount']*$purchase_rate);
					
						
						$NP=($GP);		
						$total_gross_profit+=$GP;
						$total_profits+=$NP;
						
						$total_purchases+=$other_currencies_purchases[$other_count]['total_amount'];
						$total_sales+=$other_currencies_sales[$other_count]['total_amount'];
						
						$total_purchases_ugx+=$other_currencies_purchases[$other_count]['total_amount']*$other_currencies_purchases[$other_count]['av_rate'];
						$total_sales_ugx+=$other_currencies_sales[$other_count]['total_amount']*$other_currencies_sales[$other_count]['av_rate'];
						
						array_push($arr['data'],array(
							'CID'=>$other_currency['OtherCurrency']['id'],
							''.($other_currency['OtherCurrency']['id'])=>$other_currency['OtherCurrency']['id'],
							'CRATE'=>$av_close_rate,
							'CAMOUNT'=>$todays_close,
							'CNAME'=>$other_currency['OtherCurrency']['name'],
						));
						
					}
					$this->request->data['Opening']['other_currencies']=json_encode($arr);
			
				}//don't put else clause to allow saving in the others(c8) currency table
				
				//$av_ugx=($purchases[$count]['total_amount']*$purchases[$count]['av_rate'])+(($openings[0]['Opening'][$currency['Currency']['id'].'a'])*($openings[0]['Opening'][$currency['Currency']['id'].'r']));
				//$av_rate=($purchases[$count]['total_amount'])+($openings[0]['Opening'][$currency['Currency']['id'].'a']);
				
				//New Av rate
				//$av_close_rate = ($av_rate!=0)?$av_ugx/$av_rate:0;	
				
				$av_close_rate = ($purchases[$count]['av_rate']==0)?$openings[0]['Opening'][$currency['Currency']['id'].'r']:$purchases[$count]['av_rate'];
				
				//Set New Av rate for saving as closing rate
				$this->request->data['Opening'][$currency['Currency']['id'].'r']=$av_close_rate;
				
				
				//New amount left
				$todays_close=(($openings[0]['Opening'][$currency['Currency']['id'].'a'])+($purchases[$count]['total_amount']))-($sales[$count]['total_amount']);
				//Set New amount for saving as closing amount for the foreign currency
				$this->request->data['Opening'][$currency['Currency']['id'].'a']=$todays_close;
				
				//$GP = $sales[$count]['total_amount']*($sales[$count]['av_rate']-$av_close_rate);
				
				//@$GP = $sales[$count]['total_amount_ugx'] - ($sales[$count]['total_amount']*$purchases[$count]['av_rate']);
				
				$purchase_rate = ($purchases[$count]['av_rate']==0)? $av_close_rate : $purchases[$count]['av_rate'];
				$GP = $sales[$count]['total_amount_ugx'] - ($sales[$count]['total_amount']*$purchase_rate);
					
				
				$NP=($GP);		
				if($currency['Currency']['id']!='c8'){
					$total_gross_profit+=$GP;
					$total_profits+=$NP;
					
					$total_purchases+=$purchases[$count]['total_amount'];
					$total_sales+=$sales[$count]['total_amount'];
					
					$total_purchases_ugx+=$purchases[$count]['total_amount']*$purchases[$count]['av_rate'];
					$total_sales_ugx+=$sales[$count]['total_amount']*$sales[$count]['av_rate'];
				}
				
			endforeach;
			
			$total_profits+=$additional_profits;//Include additional_profits
			
			//New cash at hand to be the opening cash for the next day selected
			$cash_at_hand=(($total_sales_ugx-($expenses)+$openings[0]['Opening']['opening_ugx']+$receivable_cash+$additional_profits)-($total_purchases_ugx+$withdrawal_cash));
			
			
			//Final cash at hand
			/*$cash_at_hand=($cash_at_hand)-($cash_at_bank_foreign+$cash_at_bank_ugx);
			$cash_at_hand_b=$cash_at_hand+$creditors;
			$cash_at_hand_e=$cash_at_hand_b-$debtors;
			$cash_at_hand_f=$cash_at_hand_e-$creditors;
			$cash_at_hand_g=$cash_at_hand_f+$debtors;
			
			$cash_at_hand=$cash_at_hand_g;
			*/
			$cash_at_hand=($cash_at_hand+$creditors)-($cash_at_bank_foreign+$cash_at_bank_ugx+$debtors);
			
			$this->request->data['Opening']['opening_ugx']=$cash_at_hand;
			if($this->Auth->User('role')=='super_admin' and isset($this->request->data['Opening_old']['user_id'])){
				$this->request->data['Opening']['user_id']=$this->request->data['Opening_old']['user_id'];
				$this->request->data['Opening']['id'].='_'.$this->request->data['Opening_old']['user_id'];
			}else{
				$this->request->data['Opening']['user_id']=$this->Auth->User('id');
				$this->request->data['Opening']['id'].='_'.$this->Auth->User('id');
			}
			
			$this->request->data['Opening']['status']=0;
			
			if ($this->Opening->save($this->request->data)) {
				$this->Session->setFlash(__('Saved'));
				
				$this->Opening->read(null, $openings[0]['Opening']['id']);
				
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
					$this->Session->setFlash(__('Saved.'));
				}else{
					$this->Opening->id = $this->request->data['Opening']['id'];
					$this->Opening->delete();
					$this->Session->setFlash(__('Not saved. Please, try again.'));
				}
			} else {
				$this->Session->setFlash(__('Not saved. Please, try again.'));
			}			
			$this->redirect(array('action' => 'show_individually'));
			
		}
		$this->Session->setFlash(__('Opening not saved. Please try again.'));
		$this->redirect(array('action' => 'show_individually'));
	}
	
	public function show_individually($receivable_cash=0,$withdrawal_cash=0,$additional_profits=0,$user_id=null) {
		date_default_timezone_set('Africa/Nairobi');
		$date_today=date('Y-m-d');
		if(isset($_REQUEST['date_today'])){
			$date_today	=($_REQUEST['date_today']);
		}
		
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
		
		$openings=$this->Opening->find('all',array(
			'recursive'=>-1,'Limit'=>1,
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
				'NOT'=>array(
					'Currency.id'=>'c00'
				)				
			)
		));
		
		$other_currencies=$this->OtherCurrency->find('all',array('recursive'=>-1,));
		
		$purchases=array();
		$sales=array();
		$cash_at_bank_foreign=0;
		$cash_at_bank_ugx=0;
		$debtors=0;
		$creditors=0;
		$other_currencies_sales=array();
		$other_currencies_purchases=array();
		
		
		
		foreach($currencies as $currency){
			//Get for all other currencies
			if($currency['Currency']['id']=='c8'){
				
				foreach($other_currencies as $other_currency){
					$_other_currencies_purchases=$this->PurchasedReceipt->find('all',array(
						'recursive'=>-1,
						'fields'=>array(
							'SUM(PurchasedReceipt.orig_amount) as total_amount',
							'AVG(PurchasedReceipt.orig_rate) as av_rate',
							'SUM(PurchasedReceipt.amount_ugx) as total_amount_ugx'
						),
						'conditions'=>array(
							'PurchasedReceipt.currency_id'=>$currency['Currency']['id']	,
							'PurchasedReceipt.user_id'=>(($this->Auth->User('role')=='super_admin' and $user_id)?$user_id:$this->Auth->User('id')),
							'PurchasedReceipt.date'=>$date_today,
							'PurchasedReceipt.other_currency_id'=>$other_currency['OtherCurrency']['id']
						)
					));
					
					$_other_currencies_sales=$this->SoldReceipt->find('all',array(
						'recursive'=>-1,
						'fields'=>array(
							'SUM(SoldReceipt.orig_amount) as total_amount',
							'AVG(SoldReceipt.orig_rate) as av_rate',
							'SUM(SoldReceipt.amount_ugx) as total_amount_ugx'
						),
						'conditions'=>array(
							'SoldReceipt.currency_id'=>$currency['Currency']['id']	,
							'SoldReceipt.user_id'=>(($this->Auth->User('role')=='super_admin' and $user_id)?$user_id:$this->Auth->User('id')),
							'SoldReceipt.date'=>$date_today,
							'SoldReceipt.other_currency_id'=>$other_currency['OtherCurrency']['id']
						)
					));
					
					array_push($other_currencies_purchases,
						array(
							'total_amount'=>$_other_currencies_purchases[0][0]['total_amount'],
							'av_rate'=>(($_other_currencies_purchases[0][0]['total_amount_ugx']==0)?0:($_other_currencies_purchases[0][0]['total_amount_ugx']/$_other_currencies_purchases[0][0]['total_amount'])),
							'total_amount_ugx'=>$_other_currencies_purchases[0][0]['total_amount_ugx']
						)
					);
					array_push($other_currencies_sales,
						array(
							'total_amount'=>$_other_currencies_sales[0][0]['total_amount'],
							'av_rate'=>(($_other_currencies_sales[0][0]['total_amount_ugx']==0)?0:($_other_currencies_sales[0][0]['total_amount_ugx']/$_other_currencies_sales[0][0]['total_amount'])),
							'total_amount_ugx'=>$_other_currencies_sales[0][0]['total_amount_ugx']
						)
					);
				}
			}
			$_purchases=$this->PurchasedReceipt->find('all',array(
				'recursive'=>-1,
				'fields'=>array(
					'SUM(PurchasedReceipt.amount) as total_amount',
					'AVG(PurchasedReceipt.rate) as av_rate',
					'SUM(PurchasedReceipt.amount_ugx) as total_amount_ugx'
				),
				'conditions'=>array(
					'PurchasedReceipt.currency_id'=>$currency['Currency']['id']	,
					'PurchasedReceipt.user_id'=>(($this->Auth->User('role')=='super_admin' and $user_id)?$user_id:$this->Auth->User('id')),
					'PurchasedReceipt.date'=>$date_today
				)
			));
			
			$_sales=$this->SoldReceipt->find('all',array(
				'recursive'=>-1,
				'fields'=>array(
					'SUM(SoldReceipt.amount) as total_amount',
					'AVG(SoldReceipt.rate) as av_rate',
					'SUM(SoldReceipt.amount_ugx) as total_amount_ugx',
				),
				'conditions'=>array(
					'SoldReceipt.currency_id'=>$currency['Currency']['id']	,
					'SoldReceipt.user_id'=>(($this->Auth->User('role')=='super_admin' and $user_id)?$user_id:$this->Auth->User('id')),
					'SoldReceipt.date'=>$date_today
				)
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
			
			array_push($purchases,
				array(
					'total_amount'=>$_purchases[0][0]['total_amount'],
					'av_rate'=>(($_purchases[0][0]['total_amount_ugx']==0)?0:($_purchases[0][0]['total_amount_ugx']/$_purchases[0][0]['total_amount'])),
					'total_amount_ugx'=>$_purchases[0][0]['total_amount_ugx']
				)
			);
			array_push($sales,
				array(
					'total_amount'=>$_sales[0][0]['total_amount'],
					'av_rate'=>(($_sales[0][0]['total_amount_ugx']==0)?0:($_sales[0][0]['total_amount_ugx']/$_sales[0][0]['total_amount'])),
					'total_amount_ugx'=>$_sales[0][0]['total_amount_ugx']
				)
			);
			
			if(isset($_cashAtBankForeign[0][0]['total_amount'])){
				//$cash_at_bank_foreign+=$_cashAtBankForeign[0][0]['total_amount']*$_purchases[0][0]['av_rate'];
				$av_ugx=($_purchases[0][0]['total_amount']*$_purchases[0][0]['av_rate'])+(($openings[0]['Opening'][$currency['Currency']['id'].'a'])*($openings[0]['Opening'][$currency['Currency']['id'].'r']));
				$av_rate=($_purchases[0][0]['total_amount'])+($openings[0]['Opening'][$currency['Currency']['id'].'a']);
				
				//New Av Closing rate
				$av_close_rate= ($av_rate!=0)?$av_ugx/$av_rate:0;			
				
				$cash_at_bank_foreign+=$_cashAtBankForeign[0][0]['total_amount']*$av_close_rate;
			}
		}
		
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
				'Receivable.date'=>$date_today
			),
			'fields'=>array(
				'SUM(Receivable.amount) as total_amount'
			)
		));
		$_withdrawals=$this->Withdrawal->find('all',array(
			'recursive'=>-1,
			'conditions'=>array(
				'Withdrawal.user_id'=>(($this->Auth->User('role')=='super_admin' and $user_id)?$user_id:$this->Auth->User('id')),
				'Withdrawal.date'=>$date_today
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
		$this->Session->write('currencies',$currencies);
		$this->Session->write('other_currencies',$other_currencies);
		$this->Session->write('purchases',$purchases);
		$this->Session->write('other_currencies_purchases',$other_currencies_purchases);
		$this->Session->write('sales',$sales);	
		$this->Session->write('other_currencies_sales',$other_currencies_sales);
		$this->Session->write('cash_at_bank_foreign',$cash_at_bank_foreign);			
		$this->Session->write('cash_at_bank_ugx',$cash_at_bank_ugx);			
		$this->Session->write('debtors',$debtors);			
		$this->Session->write('creditors',$creditors);
		$this->Session->write('receivable_cash',$receivable_cash);
		$this->Session->write('withdrawal_cash',$withdrawal_cash);
		$this->Session->write('additional_profits',$additional_profits);
		$this->set(compact('user_id','date_today','openings','currencies','purchases','sales','total_expenses','receivable_cash','withdrawal_cash','additional_profits','cash_at_bank_foreign','cash_at_bank_ugx','debtors','creditors','other_currencies_purchases','other_currencies_sales','other_currencies'));
		
	}
	
	public function show_generally() {
		date_default_timezone_set('Africa/Nairobi');
		$date_today=date('Y-m-d');
		if(isset($_REQUEST['date_today'])){
			$date_today	=($_REQUEST['date_today']);
		}
		
		$openings=$this->Opening->find('all',array(
			'recursive'=>1,'Limit'=>1,
			'conditions'=>array(
				'Opening.date'=>$date_today
				//'Opening.status'=>0,//fetch only new openings
			),
			'order'=>'Opening.date desc',
		));
		$other_currencies=$this->OtherCurrency->find('all',array('recursive'=>-1,));
		$this->set('openings',$openings);
		$this->set('other_currencies',$other_currencies);
		
		//But to show the total currencies at the end of the day, we need to get those that have been saved for the next opening day
		date_default_timezone_set('Africa/Nairobi');
		$tommorrow=date('Y-m-d',strtotime("+1 day",strtotime($date_today)));//move to next date
		$openings_tomorrow=$this->Opening->find('all',array(
			'recursive'=>1,'Limit'=>1,
			'conditions'=>array(
				'Opening.date'=>$tommorrow
				//'Opening.status'=>0,//fetch only new openings
			),
			'order'=>'Opening.date desc',
		));
		$this->set('openings_tomorrow',$openings_tomorrow);
		$this->set('date_today',$date_today);
	}
	
	public function show_generally_final() {
		date_default_timezone_set('Africa/Nairobi');
		$date_today=date('Y-m-d');
		if(isset($_REQUEST['date_today'])){
			$date_today	=($_REQUEST['date_today']);
		}
		
		$openings=$this->Opening->find('all',array(
			'recursive'=>0,'Limit'=>1,
			'order'=>'Opening.date desc',
			'fields'=>array(
				'SUM(Opening.total_profit) as total_profits',
				'SUM(Opening.total_expenses) as total_expenses'
			)
		));
		
		$fox=$this->Fox->find('first',array(
			'recursive'=>0,'Limit'=>1,
			'fields'=>array(
				'Fox.initial_position'
			)
		));
		
		$this->set('openings',$openings);
		$this->set('fox',$fox);
	}
}
