<?php
App::uses('AppController', 'Controller');
/**
 * Openings Controller
 *
 * @property Opening $Opening
 */
class ReportsController extends AppController {
	public $uses=array('Asset','Opening','Currency','OtherCurrency','PurchasedReceipt','SoldReceipt','Expense','Item','CashAtBankForeign','CashAtBankUgx','Debtor','Creditor','Fox','Receivable','Withdrawal','AdditionalProfit');
	
	public function vvv(){
		exit;
	}
	
	public function quarterly_returns(){
		ignore_user_abort(1);
		ini_set('max_execution_time', 600);
		if(	isset($_REQUEST['date_from']) &&
			isset($_REQUEST['date_to'])){
			
			$from	=($_REQUEST['date_from']);
			$to	=($_REQUEST['date_to']);
			
			$this->set('from', $from);
			$this->set('to', $to);
			
			$purchases = $this->PurchasedReceipt->find('all',array(
				'conditions'=>array(
					'PurchasedReceipt.date >='=>$from,
					'PurchasedReceipt.date <='=>$to,
				),
				'order' => 'PurchasedReceipt.date desc, PurchasedReceipt.t_time desc',
				'limit'=>0
			));
			
			App::import('Vendor', 'PHPExcel/PHPExcel');
			$objPHPexcel = PHPExcel_IOFactory::load('ExcelFiles/templates/balance_sheet.xls');
			$objWorksheet = $objPHPexcel->getSheet(0);
			
			
			$counter = 5;
			$_fox=($this->Session->read('fox'));
			$branch_name = $_fox['Fox']['name'];
			
			$objWorksheet->getCell('C6')->setValue($branch_name);//Done
			$objWorksheet->getCell('C7')->setValue(date('d',strtotime($to)));//Dome
			$objWorksheet->getCell('C8')->setValue(date('m',strtotime($to)));//Done
			$objWorksheet->getCell('C9')->setValue(date('Y',strtotime($to)));//Done
			$objWorksheet->getCell('C10')->setValue($to);//Done
			
			$objWorksheet->getCell('F7')->setValue('');//Done
			$objWorksheet->getCell('F8')->setValue(date('Y',strtotime($to)));//Dome
			$objWorksheet->getCell('F9')->setValue($from);//Done
			$objWorksheet->getCell('F10')->setValue($to);//Done
			
			$objWorksheet->getCell('F11')->setValue('');//Done
			$objWorksheet->getCell('F12')->setValue('');//Done
			$objWorksheet->getCell('F13')->setValue('');//Done

			//Cash
			$objWorksheet->getCell('C19')->setValue(0);//Local Currency
			$objWorksheet->getCell('C20')->setValue(0);//Foreign Currency
			//Balance at Bank
			$objWorksheet->getCell('C22')->setValue(0);//Local Currency
			$objWorksheet->getCell('C23')->setValue(0);//Foreign Currency
			
			$objWorksheet->getCell('C24')->setValue(0);//Debtors
			$objWorksheet->getCell('C25')->setValue(0);//Prepayments
			
			//GET Fixed and Other/Current Assets
			$this->Asset->virtualFields['total_amount'] = 0;
			$this->Asset->virtualFields['asset_type'] = 0;
			$assets = $this->Asset->find('all',array(
				'conditions'=>array(
					'Asset.date >='=>$from,
					'Asset.date <='=>$to
				),
				'fields'=>array(
					'SUM(Asset.amount) as Asset__total_amount',
					'Asset.asset_name_id',
					'AssetName.asset_type as Asset__asset_type'
				),
				'group'=>array('Asset__asset_type'),
				'recursive'=>0
			));
			@$fixedAssets = $assets[1]['Asset']['total_amount'];
			@$otherAssets = $assets[0]['Asset']['total_amount'];
			$objWorksheet->getCell('C26')->setValue($fixedAssets);//Done //Fixed Assets
			$objWorksheet->getCell('C27')->setValue($otherAssets);//Done //Other Assets
			
			//Liabilities
			$this->Creditor->virtualFields['total_amount']=0;
			$directors_credit = $this->Creditor->find('all',array(
				'conditions'=>array(
					'Customer.is_director'=>1
				),
				'fields'=>array(
					'SUM(Creditor.amount) as Creditor__total_amount'
				),
				'recursive'=>0
			));
			$credit = $this->Creditor->find('all',array(
				'conditions'=>array(
					'Customer.is_director'=>0,
					'Customer.is_bank'=>0
				),
				'fields'=>array(
					'SUM(Creditor.amount) as Creditor__total_amount'
				),
				'recursive'=>0
			));
			$this->Receivable->virtualFields['total_amount']=0;
			$directors_deposit = $this->Receivable->find('all',array(
				'conditions'=>array(
					'Customer.is_director'=>1
				),
				'fields'=>array(
					'SUM(Receivable.amount) as Receivable__total_amount'
				),
				'recursive'=>0
			));
			$borrowed = $this->Receivable->find('all',array(
				'conditions'=>array(
					'Customer.is_director'=>0,
					'Customer.is_bank'=>0
				),
				'fields'=>array(
					'SUM(Receivable.amount) as Receivable__total_amount'
				),
				'recursive'=>0
			));

			$objWorksheet->getCell('C31')->setValue($borrowed[0]['Receivable']['total_amount']);//Done //Borrowings from clients as deposits
			$objWorksheet->getCell('C32')->setValue($directors_deposit[0]['Receivable']['total_amount'] + $directors_credit[0]['Creditor']['total_amount']);//Done //Director's loans as deposits
			//GetTotalCredit
			$objWorksheet->getCell('C33')->setValue($credit[0]['Creditor']['total_amount']);//Done //Creditors
			$objWorksheet->getCell('C34')->setValue(0);//DONE //Other Payables $ accruals
			$objWorksheet->getCell('C35')->setValue(0);//DONE //Tax payable
			
			//Capital and reserves
			$objWorksheet->getCell('C39')->setValue(0);//Paid up Capital
			$objWorksheet->getCell('C40')->setValue(0);//Current year profits / (Loses)
			$objWorksheet->getCell('C41')->setValue(0);//Retained Profits / (Losses)
			$objWorksheet->getCell('C42')->setValue(0);//Other Reserves
			
			//Income Statement
			///////////
			//Income
			//Sales of currency
			$objWorksheet->getCell('C50')->setValue(0);//Income
			$objWorksheet->getCell('C52')->setValue(0);//Sales to public
			$objWorksheet->getCell('C53')->setValue(0);//Sales to other Forex Bureaus
			$objWorksheet->getCell('C54')->setValue(0);//Sales to Commercial Banks
			
			//Cost of sales of currency
			$objWorksheet->getCell('C56')->setValue(0);//Opening Stock
			$objWorksheet->getCell('C57')->setValue(0);//Purchases
			$objWorksheet->getCell('C58')->setValue(0);//Closing stock
			
			//Other incomes
			$objWorksheet->getCell('C61')->setValue(0);//Foreign Exchange revaluation Gain/ (Los)
			$objWorksheet->getCell('C62')->setValue(0);//Interest Income
			$objWorksheet->getCell('C63')->setValue(0);//Commission from money remittances
			
			//Expenses
			$objWorksheet->getCell('C67')->setValue(0);//Interest Expense
			$objWorksheet->getCell('C68')->setValue(0);//Director's Emoluments
			$objWorksheet->getCell('C69')->setValue(0);//Salaries and Wages
			$objWorksheet->getCell('C70')->setValue(0);//Other Expenses
			
			$objWorksheet->getCell('C73')->setValue(0);//Tax
			
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPexcel, 'Excel5');
			$newFileName = 'ExcelFiles/templates/quartely'.((isset($u['User']['name']))?$u['User']['name']:$this->Auth->User('name')).".xls"; 
		
			$objWriter->save($newFileName);
			$this->redirect(Configure::read('domainAddress').'/fx/'.$newFileName);
		
		}
		echo 'Select a date range';
		exit;
	}
	
	public function currency_summary($user_id=null){
    	$from = (empty($_REQUEST['date_from']))?date('Y-m-d'):$_REQUEST['date_from'];
		$to	  = (empty($_REQUEST['date_from']))?date('Y-m-d'):$_REQUEST['date_to'];
		
		//Get all the currencies
		$currencies=$this->Currency->find('all',array(
			'recursive'=>-1,
			'conditions'=>array(
				'NOT'=>array(
					'Currency.id'=>'c00'
				)				
			),
			'order'=>'Currency.is_other_currency ASC,Currency.arrangement ASC, Currency.id ASC',
		));
		//Get all the other currencies
		$other_currencies=$this->OtherCurrency->find('all',array('recursive'=>-1));
		
		$purchases=array();
		$sales=array();
		$other_currencies_sales=array();
		$other_currencies_purchases=array();
		
		
		foreach($currencies as $currency){
			//Get for all other currencies
			if($currency['Currency']['id']=='c8'){
				foreach($other_currencies as $other_currency){
					//OtherCurrencies Pruchases
					$conditions = array(
						'PurchasedReceipt.currency_id'=>$currency['Currency']['id']	,
						'PurchasedReceipt.date >='=>$from,
						'PurchasedReceipt.date <='=>$to,
						'PurchasedReceipt.other_currency_id'=>$other_currency['OtherCurrency']['id']
					);
					if($this->Auth->User('role')=='super_admin' && !empty($user_id)){
						$conditions['PurchasedReceipt.user_id'] = $user_id;
					}
					$_other_currencies_purchases=$this->PurchasedReceipt->find('all',array(
						'recursive'=>-1,
						'fields'=>array(
							'SUM(PurchasedReceipt.orig_amount) as total_amount',
							'AVG(PurchasedReceipt.orig_rate) as av_rate',
							'SUM(PurchasedReceipt.amount_ugx) as total_amount_ugx'
						),
						'conditions'=>$conditions
					));
					
					//OtherCurrencies Sales
					$conditions = array(
						'SoldReceipt.currency_id'=>$currency['Currency']['id']	,
						'SoldReceipt.date >='=>$from,
						'SoldReceipt.date <='=>$to,
						'SoldReceipt.other_currency_id'=>$other_currency['OtherCurrency']['id']
					);
					if($this->Auth->User('role')=='super_admin' && !empty($user_id)){
						$conditions['SoldReceipt.user_id'] = $user_id;
					}
					$_other_currencies_sales=$this->SoldReceipt->find('all',array(
						'recursive'=>-1,
						'fields'=>array(
							'SUM(SoldReceipt.orig_amount) as total_amount',
							'AVG(SoldReceipt.orig_rate) as av_rate',
							'SUM(SoldReceipt.amount_ugx) as total_amount_ugx'
						),
						'conditions'=>$conditions
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
			
			//Purchases Conditions
			$conditions = array(
				'PurchasedReceipt.currency_id'=>$currency['Currency']['id']	,
				'PurchasedReceipt.date >='=>$from,
				'PurchasedReceipt.date <='=>$to
			);
			if($this->Auth->User('role')=='super_admin' && !empty($user_id)){
				$conditions['PurchasedReceipt.user_id'] = $user_id;
			}
			$_purchases=$this->PurchasedReceipt->find('all',array(
				'recursive'=>-1,
				'fields'=>array(
					'SUM(PurchasedReceipt.amount) as total_amount',
					'AVG(PurchasedReceipt.rate) as av_rate',
					'SUM(PurchasedReceipt.amount_ugx) as total_amount_ugx'
				),
				'conditions'=>$conditions
			));
			
			//Sales Conditions
			$conditions = array(
				'SoldReceipt.currency_id'=>$currency['Currency']['id']	,
				'SoldReceipt.date >='=>$from,
				'SoldReceipt.date <='=>$to
			);
			if($this->Auth->User('role')=='super_admin' && !empty($user_id)){
				$conditions['SoldReceipt.user_id'] = $user_id;
			}
			$_sales=$this->SoldReceipt->find('all',array(
				'recursive'=>-1,
				'fields'=>array(
					'SUM(SoldReceipt.amount) as total_amount',
					'AVG(SoldReceipt.rate) as av_rate',
					'SUM(SoldReceipt.amount_ugx) as total_amount_ugx',
				),
				'conditions'=>$conditions
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
			
			$this->set(compact('currencies','purchases','sales','other_currencies_purchases','other_currencies_sales','other_currencies'));
		}
		
		$this->set('from', $from);
		$this->set('to', $to);
    }
}
