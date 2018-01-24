<?php
App::uses('AppController', 'Controller');
/**
 * PurchasedReceipts Controller
 *
 * @property PurchasedReceipt $PurchasedReceipt
 */
class PurchasedReceiptsController extends AppController {
	
	public $uses = array('PurchasedReceipt','MultiplePrintReceipt','TtAccount','Opening','Purpose','PurchasedPurpose','SoldReceipt');
	
	function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow(['excel_purchases']);
    }


	public function excel_purchases(){
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
			$objPHPexcel = PHPExcel_IOFactory::load('ExcelFiles/templates/receipts.xls');
			$objWorksheet = $objPHPexcel->getSheet(0);
			
			
			$counter = 5;
			$_fox=($this->Session->read('fox'));
			$branch_name = $_fox['Fox']['name'];
			
			$objWorksheet->getCell('D1')->setValue($branch_name);
			$objWorksheet->getCell('D2')->setValue('Purchase Receipts');
			$objWorksheet->getCell('D3')->setValue('From '.$from . ' to '. $to);
			
			//Purchased Receipts
			$record_counter=1;
			$objWorksheet->getCell('A'.$counter)->setValue('Receipt Number');
			$objWorksheet->getCell('B'.$counter)->setValue('Amount');
			$objWorksheet->getCell('C'.$counter)->setValue('Rate');
			$objWorksheet->getCell('D'.$counter)->setValue('UGX');
			$objWorksheet->getCell('E'.$counter)->setValue('Currency');
			$objWorksheet->getCell('F'.$counter)->setValue('Date');
			$objWorksheet->getCell('G'.$counter)->setValue('Time');
			foreach($purchases as $row){
				$counter++;
				$objWorksheet->getCell('A'.$counter)->setValue("'".$row['PurchasedReceipt']['id']."'");//Record
				$currency = '';
				if($row['PurchasedReceipt']['currency_id']=='c8'){
					$currency = strtoupper($row['PurchasedReceipt']['other_name']);
					$rate = $row['PurchasedReceipt']['orig_rate'];
					$amount = $row['PurchasedReceipt']['orig_amount'];
				}else{
					$currency = $row['Currency']['id'];
					$rate = $row['PurchasedReceipt']['rate'];
					$amount = $row['PurchasedReceipt']['amount'];
				}
				$objWorksheet->getCell('B'.$counter)->setValue($amount);
				$objWorksheet->getCell('C'.$counter)->setValue($rate);
				$objWorksheet->getCell('D'.$counter)->setValue($row['PurchasedReceipt']['amount_ugx']);
				$objWorksheet->getCell('E'.$counter)->setValue($currency);
				$objWorksheet->getCell('F'.$counter)->setValue($row['PurchasedReceipt']['date']);
				$objWorksheet->getCell('G'.$counter)->setValue($row['PurchasedReceipt']['t_time']);
				$record_counter++;
			}
			
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPexcel, 'Excel5');
			if (isset($_REQUEST['apiRequest'])) {
				$newFileName = "ExcelFiles/templates/apirequest_purchases" . $_REQUEST['apiRequest'] . ".xls"; 
			} else {
				$newFileName = 'ExcelFiles/templates/purchases'.((isset($u['User']['name']))?$u['User']['name']:$this->Auth->User('name')).".xls"; 
			}
		
			$objWriter->save($newFileName);

			if(isset($_REQUEST['apiRequest'])) {
				echo json_encode([
					'apiRequest'=>$_REQUEST['apiRequest'],
					'filename'=>$newFileName
				]);
				exit();
			}
			$this->redirect('http://'. $this->downloadsIp .'/fx/'.$newFileName);
		}
		echo 'Select a date range';
		exit;
	}
    
    function print_receipt($id){
		if (!$this->PurchasedReceipt->exists($id)) {
			throw new NotFoundException(__('Invalid purchased receipt'));
		}
		
		$this->request->data['PurchasedReceipt']['id']=$id;
		$this->request->data['PurchasedReceipt']['status']=0;
		//Set the remote address for this PC incase the printing will be done from this PC
		if($this->Auth->User('printing_place')==2){
			$this->request->data['PurchasedReceipt']['remote_addr'] = str_replace('::1','127.0.0.1',$_SERVER['REMOTE_ADDR']);
		}
		if ($this->PurchasedReceipt->save($this->request->data)) {
				$this->set('resp','Sent for printing.');
		} else {
			$this->set('resp','Not Sent for printing.');
		}
	}
	
	function should_upload($id,$indicator){
		if (!$this->PurchasedReceipt->exists($id)) {
			throw new NotFoundException(__('Invalid sales receipt'));
		}
		
		$this->request->data['PurchasedReceipt']['id']=$id;
		if($indicator==0 || $indicator==1){
			$this->request->data['PurchasedReceipt']['is_uploaded']=$indicator;
		}
		$this->PurchasedReceipt->save($this->request->data);		
		$this->redirect(array('action'=>'index'));
	}

	public function upload(){
		$options = array('conditions' => array('PurchasedReceipt.is_uploaded'=>0));
		$this->set('receipt_count', $this->PurchasedReceipt->find('count', $options));
	}
	
	public function get_new_receipts_count(){
		$this->set('count_new_receipts',$this->PurchasedReceipt->find('count',array('conditions'=>array('PurchasedReceipt.is_uploaded'=>0))));
	} 
	
	public function send_new_receipts(){	
		$PurchasedReceipts=$this->PurchasedReceipt->find('all',array('recursive'=>-1,'limit'=>1000,'conditions'=>array('PurchasedReceipt.is_uploaded'=>0)));
		$resting=new $this->Resting;
		$_fox=($this->Session->read('fox'));
		$resting->api_username=$_fox['Fox']['un'];
		$resting->api_password=$_fox['Fox']['pwd'];
		$resting->authorisation_key=$_fox['Fox']['k'];
		$resting->url = $_fox['Fox']['url'];
		$response=$resting->XML_fetch_data('/purchased_receipts/fox_add.json','<Receipts>'.(json_encode($PurchasedReceipts)).'</Receipts>');
		echo ($response);
		if($resting->has_response){
			$response_array=json_decode($response);
			if(isset($response_array->data->response->saved_string)){
				if(strlen($response_array->data->response->saved_string)){
					@$this->PurchasedReceipt->query('UPDATE purchased_receipts set is_uploaded=1 where id in ('.($response_array->data->response->saved_string).')');
				}
			}else{
				//echo "Error:Receipt could not be saved online! Access denied";
			}
		}else{
			pr("could not communicate with BOU/ Check your internet connection");
		}
		sleep(1);
	} 
	
	
	//Command to send large_cash
	public function send_purchase_large_cash(){
		ignore_user_abort(1);
		ini_set('max_execution_time', 600);
		
		if(	isset($_REQUEST['date_from']) &&
			isset($_REQUEST['date_to'])){
			$from	=($_REQUEST['date_from']);
			$to	=($_REQUEST['date_to']);
			
			$this->set('from', $from);
			$this->set('to', $to);
			
			$PurchasedReceiptsCount=$this->PurchasedReceipt->find('count',array('conditions'=>array('PurchasedReceipt.is_uploaded'=>0)));
						
			if($PurchasedReceiptsCount){
				$this->Session->setFlash(__("Warning:".($PurchasedReceiptsCount).' purchase receipt(s) not uploaded yet. Please upload to continue.'),'flash_warning');
				return;
			}
			
			
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
			$response=$resting->XML_fetch_data('/purchased_receipts/fox_send_purchases_large_cash.json','<LargeCash>'.(json_encode($LargeCash)).'</LargeCash>');
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
		$this->PurchasedReceipt->recursive = 0;
		$this->paginate=array('order'=>'PurchasedReceipt.date desc');
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
								'PurchasedReceipt.id LIKE' => '%' . $_REQUEST['search_query_string'] . '%',
								'PurchasedReceipt.customer_name LIKE' => '%' . $_REQUEST['search_query_string'] . '%'
							)
						),
						'order' => 'PurchasedReceipt.date desc, PurchasedReceipt.t_time desc',
						'limit'=>200
					);
				}else{
					$this->paginate = array(
						'conditions' => array(
							'OR' => array(
								'PurchasedReceipt.id LIKE' => '%' . $_REQUEST['search_query_string'] . '%',
								'PurchasedReceipt.customer_name LIKE' => '%' . $_REQUEST['search_query_string'] . '%'
							),
							'PurchasedReceipt.user_id'=>$this->Auth->User('id')
						),
						'order' => 'PurchasedReceipt.date desc, PurchasedReceipt.t_time desc',
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
								'PurchasedReceipt.date >='=>$from,
								'PurchasedReceipt.date <='=>$to,
								'PurchasedReceipt.'.$field=>$_REQUEST['currency']
							),
							'order' => 'PurchasedReceipt.date desc, PurchasedReceipt.t_time desc',
							'limit'=>1000
						);
					}else{
						$this->paginate=array(
							'conditions'=>array(
								'PurchasedReceipt.date >='=>$from,
								'PurchasedReceipt.date <='=>$to,
								'PurchasedReceipt.'.$field=>$_REQUEST['currency'],
								'PurchasedReceipt.user_id'=>$this->Auth->User('id')
							),
							'order' => 'PurchasedReceipt.date desc, PurchasedReceipt.t_time desc',
							'limit'=>1000
						);
					}
					$this->set('setCurrency',$_REQUEST['currency']);
				}else{
					if($this->Auth->User('role')=='super_admin'){
						$this->paginate=array(
							'conditions'=>array(
								'PurchasedReceipt.date >='=>$from,
								'PurchasedReceipt.date <='=>$to
							),
							'order' => 'PurchasedReceipt.date desc, PurchasedReceipt.t_time desc',
							'limit'=>1000
						);
					}else{
						$this->paginate=array(
							'conditions'=>array(
								'PurchasedReceipt.date >='=>$from,
								'PurchasedReceipt.date <='=>$to,
								'PurchasedReceipt.user_id'=>$this->Auth->User('id')
							),
							'order' => 'PurchasedReceipt.date desc, PurchasedReceipt.t_time desc',
							'limit'=>1000
						);
					}
				}
			}
			
			if($large_cash){
				
				//get Average Rate for Dollar
				if($this->Auth->User('role')=='super_admin'){
					$dollar_av_rate=$this->PurchasedReceipt->find('all',array(
						'recursive'=>-1,
						'conditions'=>array(
							'PurchasedReceipt.currency_id'=>'USD',
							'PurchasedReceipt.date >='=>$from,
							'PurchasedReceipt.date <='=>$to,
						),
						'fields'=>array(
							'SUM(PurchasedReceipt.amount) as total_amount',
							'SUM(PurchasedReceipt.amount_ugx) as total_amount_ugx'
						)
					));
				}else{
					$dollar_av_rate=$this->PurchasedReceipt->find('all',array(
						'recursive'=>-1,
						'conditions'=>array(
							'PurchasedReceipt.currency_id'=>'USD',
							'PurchasedReceipt.date >='=>$from,
							'PurchasedReceipt.date <='=>$to,
							'PurchasedReceipt.user_id'=>$this->Auth->User('id')
						),
						'fields'=>array(
							'SUM(PurchasedReceipt.amount) as total_amount',
							'SUM(PurchasedReceipt.amount_ugx) as total_amount_ugx'
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
							'PurchasedReceipt.date >='=>$from,
							'PurchasedReceipt.date <='=>$to,
							'PurchasedReceipt.amount_ugx >='=>$max_dollar_ugx,
						),
						'order' => 'PurchasedReceipt.date desc, PurchasedReceipt.t_time desc',
						'limit'=>10000
					);
				}else{
					$this->paginate=array(
						'conditions'=>array(
							'PurchasedReceipt.date >='=>$from,
							'PurchasedReceipt.date <='=>$to,
							'PurchasedReceipt.amount_ugx >='=>$max_dollar_ugx,
							'PurchasedReceipt.user_id'=>$this->Auth->User('id')
						),
						'order' => 'PurchasedReceipt.date desc, PurchasedReceipt.t_time desc',
						'limit'=>10000
					);
				}
				$this->set('large_cash', $large_cash);
				$this->set('dollar_av_rate', $dollar_av_rate);
			}
		}
		$this->set('purchasedReceipts', $this->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->PurchasedReceipt->exists($id)) {
			throw new NotFoundException(__('Invalid purchased receipt'));
		}
		$options = array('conditions' => array('PurchasedReceipt.' . $this->PurchasedReceipt->primaryKey => $id));
		$this->set('purchasedReceipt', $this->PurchasedReceipt->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
 
	public function add() {
		if ($this->request->is('post')) {

			//Check whether there's an opening for today, else redirect user to create an opening
			if(empty($this->request->data['PurchasedReceipt']['date']))
			{
				$this->request->data['PurchasedReceipt']['date'] = date('Y-m-d H:i:s');
			}
			$openingtoday = $this->Opening->find('first',array(
				'conditions'=>array(	
					'Opening.date'=>$this->request->data['PurchasedReceipt']['date'],
					'Opening.user_id'=>(($this->Auth->User('role')=='super_admin')?$this->request->data['PurchasedReceipt']['user_id']:$this->Auth->User('id'))
				),
				'recursive'=>-1
			));
			if(empty($openingtoday) && $this->request->data['PurchasedReceipt']['instrument']=='TT'){
				$this->Session->setFlash(__('Please create an opening for '.$this->request->data['PurchasedReceipt']['date'],true),'flash_error');
				$this->redirect(array('action'=>'add','controller'=>'openings'));
			}
			
			if(($this->request->data['PurchasedReceipt']['currency_id'])=='c00'){
				$this->Session->setFlash(__('Invalid Currency selected.'),'flash_error');
				$this->redirect(array('action' => 'add'));
			}
			
			if(($this->request->data['PurchasedReceipt']['purchased_purpose_id'])=='p000'){
				$this->Session->setFlash(__('Invalid Receipt Source of funds selected.'),'flash_error');
				$this->redirect(array('action' => 'add'));
			}
			
			$tt_currency_id = $this->request->data['PurchasedReceipt']['currency_id'];
			if($this->request->data['PurchasedReceipt']['currency_id']=='c8'){
				$other_currency=$this->PurchasedReceipt->OtherCurrency->find('first',array(
					'conditions'=>array(
						'OtherCurrency.id'=>$this->request->data['PurchasedReceipt']['other_currency_id']
					),'recursive'=>-1
				));
				
				if(isset($other_currency['OtherCurrency']['name'])){					
					$this->request->data['PurchasedReceipt']['other_name']=$other_currency['OtherCurrency']['name'];
					$tt_currency_id = $other_currency['OtherCurrency']['id'];
					$tt_currency_name = $other_currency['OtherCurrency']['name'];
				}else{
					$this->Session->setFlash(__('Other currency not found.'),'flash_error');
					$this->redirect(array('action' => 'add'));
				}
			}else{
				if($this->request->data['PurchasedReceipt']['instrument']=='TT'){
					$_currency=$this->PurchasedReceipt->Currency->find('first',array(
						'conditions'=>array(
							'Currency.id'=>$this->request->data['PurchasedReceipt']['currency_id']
						),'recursive'=>-1
					));
					$tt_currency_id = $_currency['Currency']['id'];
					$tt_currency_name = $_currency['Currency']['description'];
				}
				unset($this->request->data['PurchasedReceipt']['other_currency_id']);
				unset($this->request->data['PurchasedReceipt']['other_name']);
			}
			
			if($this->request->data['PurchasedReceipt']['print']=='dont_print'){
				$this->request->data['PurchasedReceipt']['status']=1;
			}else{
				$this->request->data['PurchasedReceipt']['status']=0;
			}
			
			$ttamount = $this->request->data['PurchasedReceipt']['amount'];
			if($this->request->data['PurchasedReceipt']['currency_id']=='c8'){
				$_amount=$this->request->data['PurchasedReceipt']['amount'];
				$_rate=$this->request->data['PurchasedReceipt']['rate'];
				
				$rate=Configure::read('others');
				$amount=0;
				@$amount=($_amount*$_rate)/$rate;
				
				$this->request->data['PurchasedReceipt']['amount']=$amount;
				$this->request->data['PurchasedReceipt']['rate']=$rate;
				$this->request->data['PurchasedReceipt']['orig_amount']=$_amount;
				$this->request->data['PurchasedReceipt']['orig_rate']=$_rate;
				
			}
			
			if($this->Auth->User('role')!='super_admin'){
				$this->request->data['PurchasedReceipt']['user_id']=$this->Auth->User('id');
				$this->request->data['PurchasedReceipt']['name']=$this->Auth->User('name');
			}else{
				$user=$this->PurchasedReceipt->User->find('first',array(
					'conditions'=>array(
						'User.id'=>$this->request->data['PurchasedReceipt']['user_id']
					),
					'fields'=>array(
						'name'
					)
				));
				$this->request->data['PurchasedReceipt']['name']=$user['User']['name'];
			}			
			$this->request->data['PurchasedReceipt']['fox_id']=Configure::read('foxId');
			
			$_date=date('Y-m-d H:i:s');
			if($this->Auth->User('role')!='super_admin'){
				$this->request->data['PurchasedReceipt']['date']=date('Y-m-d',strtotime($_date));
			}
			$this->request->data['PurchasedReceipt']['t_time']=date('H:i:s',strtotime($_date));
			
			
			$receipt_number = $this->PurchasedReceipt->query("SELECT * FROM receipt_tracks limit 1");
			$this->request->data['PurchasedReceipt']['id'] = $receipt_number[0]['receipt_tracks']['my_count_purchased_receipts'];
			
			//Set the remote address for this PC incase the printing will be done from this PC
			if($this->Auth->User('printing_place')==2){
				$this->request->data['PurchasedReceipt']['remote_addr'] = str_replace('::1','127.0.0.1',$_SERVER['REMOTE_ADDR']);
			}
			
			// Start large cash validations here
			$valid = true;
			$flashMessage = '';
			$isLargeCash = false;

			//Validate USD large cash
			$largeCashUSD = 5000;
			$conversionAmountUGX = $this->request->data['PurchasedReceipt']['amount_ugx'];
			$conversionCurrency = $this->request->data['PurchasedReceipt']['currency_id'];
			$USDEquivalent = 0;
			$defaultUSDRate = 300;

			if ($conversionCurrency=='USD') {
				$USDEquivalent = $this->request->data['PurchasedReceipt']['amount'];
			}else {
				$currenciesDetails = $this->PurchasedReceipt->Currency->find('first',array('conditions'=>array('Currency.id'=>'USD'),'recursive'=>-1));
				$rate = $defaultUSDRate;
				if (!empty($currenciesDetails)) {
					if (!empty($currenciesDetails['Currency']['buy'])) {
						$rate = $currenciesDetails['Currency']['buy'];
					}
				}
				@$USDEquivalent = $conversionAmountUGX/$rate;
			}

            if ($USDEquivalent>=$largeCashUSD) {
            	$x = $this->request->data['PurchasedReceipt'];
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
				$this->PurchasedReceipt->create();
				if ($this->PurchasedReceipt->save($this->request->data)) {
					$this->Session->setFlash(__('The purchase receipt has been saved'),'flash_success');
				
					$this->Session->delete('unused_purchased_receipt_id');
					$this->PurchasedReceipt->query("UPDATE receipt_tracks SET my_count_purchased_receipts=my_count_purchased_receipts+1");
					
					if($this->request->data['PurchasedReceipt']['instrument']=='TT'){
						//Update the opening UGX,
						$this->Opening->save(array(
							'Opening'=>array(
								'id'=>$openingtoday['Opening']['id'],
								'opening_ugx'=>$openingtoday['Opening']['opening_ugx'] - $this->request->data['PurchasedReceipt']['amount_ugx'] 
							)
						));
						//save the amount to the TtAccount
						$ttAcount = $this->TtAccount->find('first',array('conditions'=>array('TtAccount.id'=> $tt_currency_id)));
						@$ttAccountBalance = $ttAcount['TtAccount']['balance'];
						$this->TtAccount->save(array(
							'TtAccount'=>array('id'=>$tt_currency_id,'balance'=>$ttAccountBalance + $ttamount,'currency_name'=>$tt_currency_name)
						));
					}
					
					//Save transaction log
					$func=$this->Func;
					$action_performed=$this->Auth->User('name').' added purchase['.($this->request->data['PurchasedReceipt']['id']).'] receipt of '.(date('Y-m-d',strtotime($this->request->data['PurchasedReceipt']['date']))).' with amount '.(($this->request->data['PurchasedReceipt']['currency_id']!='c8')?$this->request->data['PurchasedReceipt']['amount']:$this->request->data['PurchasedReceipt']['orig_amount']).' at rate '.(($this->request->data['PurchasedReceipt']['currency_id']!='c8')?$this->request->data['PurchasedReceipt']['rate']:$this->request->data['PurchasedReceipt']['orig_rate']).' on '.(date('M d Y h:i:sa',strtotime($_date)));
					
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
				
					
					$this->redirect(array('action' => 'view',$this->request->data['PurchasedReceipt']['id']));
				} else {
					$this->Session->setFlash(__('The purchase receipt could not be saved. Please, try again.'),'flash_error');
				}
			}else{
            	$this->Session->setFlash(__($flashMessage),'flash_error');
            }
		}
		$purchasedPurposes = $this->PurchasedReceipt->PurchasedPurpose->find('list',array(
			'conditions'=>array(
				'NOT'=>array(
					'PurchasedPurpose.id'=>array('p38','p39','p40','p42')
				)
			)
		));
		
		$users = $this->PurchasedReceipt->User->find('list',array(
			'conditions'=>array(
				'NOT'=>array(
					'User.role'=>array(
						'super_admin','customer'
					)
				)
			),
			'recursive'=>-1
		));
		$currencies = $this->PurchasedReceipt->Currency->find('list',[
			'order'=>'Currency.is_other_currency ASC,Currency.arrangement ASC, Currency.id ASC',
		]);
		$other_currencies = $this->PurchasedReceipt->OtherCurrency->find('list');
		$this->set(compact('purchasedPurposes', 'currencies','users','other_currencies'));
		
		$_fox=($this->Session->read('fox'));
		@$use_system_board_rates = $_fox['Fox']['use_system_board_rates'];
		if($use_system_board_rates){
			$currenciesDetails = $this->PurchasedReceipt->Currency->find('all',array('recursive'=>-1));
			$otherCurrenciesDetails = $this->PurchasedReceipt->OtherCurrency->find('all',array('recursive'=>-1));
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

		if (!$this->PurchasedReceipt->exists($id)) {
			throw new NotFoundException(__('Invalid purchase receipt'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			$_date = $this->request->data['PurchasedReceipt']['date']['year'].'-'.$this->request->data['PurchasedReceipt']['date']['month'].'-'.$this->request->data['PurchasedReceipt']['date']['day'];
			//Check whether there's an opening for today, else redirect user to create an opening
			$openingtoday = $this->Opening->find('first',array(
				'conditions'=>array(	
					'Opening.date'=>$this->request->data['PurchasedReceipt']['date'],
					'Opening.user_id'=>(($this->Auth->User('role')=='super_admin')?$this->request->data['PurchasedReceipt']['user_id']:$this->Auth->User('id'))
				),
				'recursive'=>-1
			));
			if(empty($openingtoday) && $this->request->data['PurchasedReceipt']['instrument']=='TT'){
				$this->Session->setFlash(__('Please create an opening for '.$this->request->data['PurchasedReceipt']['date'],true),'flash_error');
				$this->redirect(array('action'=>'add','controller'=>'openings'));
			}
			
			$oldReceipt = $this->PurchasedReceipt->find('first',array('conditions'=>array('PurchasedReceipt.id'=>$id),'recursive'=>-1));
			
			if(strlen($this->request->data['PurchasedReceipt']['id'])<4){
				$this->Session->setFlash(__('Invalid Receipt number.'),'flash_error');
				$this->redirect(array('action' => 'edit',$id));
			}
			
			if(($this->request->data['PurchasedReceipt']['currency_id'])=='c00'){
				$this->Session->setFlash(__('Invalid Currency selected.'),'flash_error');
				$this->redirect(array('action' => 'edit',$id));
			}
			
			if(($this->request->data['PurchasedReceipt']['purchased_purpose_id'])=='p000'){
				$this->Session->setFlash(__('Invalid Receipt Source of funds selected.'),'flash_error');
				$this->redirect(array('action' => 'edit',$id));
			}
			
			$tt_currency_id = $this->request->data['PurchasedReceipt']['currency_id'];
			if($this->request->data['PurchasedReceipt']['currency_id']=='c8'){
				$other_currency=$this->PurchasedReceipt->OtherCurrency->find('first',array(
					'conditions'=>array(
						'OtherCurrency.id'=>(!empty($this->request->data['PurchasedReceipt']['other_currency_id']))?$this->request->data['PurchasedReceipt']['other_currency_id']:$oldReceipt['PurchasedReceipt']['other_currency_id']
					)
				));
				
				if(isset($other_currency['OtherCurrency']['name'])){					
					$this->request->data['PurchasedReceipt']['other_name']=$other_currency['OtherCurrency']['name'];
					$tt_currency_id = $other_currency['OtherCurrency']['id'];
					$tt_currency_name = $other_currency['OtherCurrency']['name'];
				}else{
					$this->Session->setFlash(__('Other currency not found.'),'flash_error');
					$this->redirect(array('action' => 'add'));
				}
			}else{
				if($this->request->data['PurchasedReceipt']['instrument']=='TT'){
					$_currency=$this->PurchasedReceipt->Currency->find('first',array(
						'conditions'=>array(
							'Currency.id'=>$this->request->data['PurchasedReceipt']['currency_id']
						),'recursive'=>-1
					));
					$tt_currency_id = $_currency['Currency']['id'];
					$tt_currency_name = $_currency['Currency']['description'];
				}
				unset($this->request->data['PurchasedReceipt']['other_currency_id']);
				unset($this->request->data['PurchasedReceipt']['other_name']);
			}
			
			if($this->request->data['PurchasedReceipt']['currency_id']!='c8'){
				unset($this->request->data['PurchasedReceipt']['other_name']);
				$this->request->data['PurchasedReceipt']['orig_amount']=0;
				$this->request->data['PurchasedReceipt']['orig_rate']=0;
			}
			
			$ttamount = $this->request->data['PurchasedReceipt']['amount'];
			if($this->request->data['PurchasedReceipt']['currency_id']=='c8'){
				$_amount=$this->request->data['PurchasedReceipt']['amount'];
				$_rate=$this->request->data['PurchasedReceipt']['rate'];
				
				$rate=Configure::read('others');
				$amount=0;
				@$amount=($_amount*$_rate)/$rate;
				
				$this->request->data['PurchasedReceipt']['amount']=$amount;
				$this->request->data['PurchasedReceipt']['rate']=$rate;
				$this->request->data['PurchasedReceipt']['orig_amount']=$_amount;
				$this->request->data['PurchasedReceipt']['orig_rate']=$_rate;
				
			}
			
			$date=$this->request->data['PurchasedReceipt']['date']['year'].'-'.$this->request->data['PurchasedReceipt']['date']['month'].'-'.$this->request->data['PurchasedReceipt']['date']['day'];
			$_date=date('Y-m-d H:i:s');
			
			if($this->Auth->User('role')!='super_admin'){
				$this->request->data['PurchasedReceipt']['user_id']=$this->Auth->User('id');
				$this->request->data['PurchasedReceipt']['name']=$this->Auth->User('name');
			}else{
				$user=$this->PurchasedReceipt->User->find('first',array(
					'conditions'=>array(
						'User.id'=>$this->request->data['PurchasedReceipt']['user_id']
					),
					'fields'=>array(
						'name'
					)
				));
				$this->request->data['PurchasedReceipt']['name']=$user['User']['name'];
			}	


			// Start large cash validations here
			$valid = true;
			$flashMessage = '';
			$isLargeCash = false;

			//Validate USD large cash
			$largeCashUSD = 5000;
			$conversionAmountUGX = $this->request->data['PurchasedReceipt']['amount_ugx'];
			$conversionCurrency = $this->request->data['PurchasedReceipt']['currency_id'];
			$USDEquivalent = 0;
			$defaultUSDRate = 300;

			if ($conversionCurrency=='USD') {
				$USDEquivalent = $this->request->data['PurchasedReceipt']['amount'];
			}else {
				$currenciesDetails = $this->PurchasedReceipt->Currency->find('first',array('conditions'=>array('Currency.id'=>'USD'),'recursive'=>-1));
				$rate = $defaultUSDRate;
				if (!empty($currenciesDetails)) {
					if (!empty($currenciesDetails['Currency']['buy'])) {
						$rate = $currenciesDetails['Currency']['buy'];
					}
				}
				@$USDEquivalent = $conversionAmountUGX/$rate;
			}

            if ($USDEquivalent>=$largeCashUSD) {
            	$x = $this->request->data['PurchasedReceipt'];
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
				if($this->PurchasedReceipt->save($this->request->data)) {
					$this->Session->setFlash(__('The purchase receipt has been saved'),'flash_success');
					
					//Try to work on the old receipt
					if($oldReceipt['PurchasedReceipt']['instrument']=='TT'){
						//Get the old opening
						$oldopeningtoday = $this->Opening->find('first',array(
							'conditions'=>array(	
								'Opening.date'=>$oldReceipt['PurchasedReceipt']['date'],
								'Opening.user_id'=>(($this->Auth->User('role')=='super_admin')?$oldReceipt['PurchasedReceipt']['user_id']:$this->Auth->User('id'))
							),
							'recursive'=>-1
						));
						//Update the opening UGX,
						$this->Opening->save(array(
							'Opening'=>array(
								'id'=>$oldopeningtoday['Opening']['id'],
								'opening_ugx'=>($oldopeningtoday['Opening']['opening_ugx']+$oldReceipt['PurchasedReceipt']['amount_ugx'])
							)
						));
						//save the amount to the TtAccount
						$_ttcurrency_id = ($oldReceipt['PurchasedReceipt']['currency_id']=='c8')?$oldReceipt['PurchasedReceipt']['other_currency_id']:$oldReceipt['PurchasedReceipt']['currency_id'];
						$_ttamount = ($oldReceipt['PurchasedReceipt']['currency_id']=='c8')?$oldReceipt['PurchasedReceipt']['orig_amount']:$oldReceipt['PurchasedReceipt']['amount'];
						$_ttAcount = $this->TtAccount->find('first',array('conditions'=>array('TtAccount.id'=>$_ttcurrency_id)));
						@$_ttAccountBalance = $_ttAcount['TtAccount']['balance'];
						$this->TtAccount->save(array('TtAccount'=>array('id'=>$_ttcurrency_id,'balance'=>($_ttAccountBalance - $_ttamount))));
					}
					
					//Save the tt_acount_balance for the new record/edited record in case its still a TT
					if($this->request->data['PurchasedReceipt']['instrument']=='TT'){
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
								'opening_ugx'=>($openingtoday['Opening']['opening_ugx'] - $this->request->data['PurchasedReceipt']['amount_ugx'])
							)
						));
						//save the amount to the TtAccount
						$ttAcount = $this->TtAccount->find('first',array('conditions'=>array('TtAccount.id'=> $tt_currency_id)));
						@$ttAccountBalance = $ttAcount['TtAccount']['balance'];
						$this->TtAccount->save(array(
							'TtAccount'=>array('id'=>$tt_currency_id,'balance'=>$ttAccountBalance + $ttamount,'currency_name'=>$tt_currency_name)
						));
					}
					
					//Save transaction log
					$func=$this->Func;
					$action_performed=
						$this->Auth->User('name').
						' edited purchase receipt['.($this->request->data['PurchasedReceipt']['id']).'] of '.
						(date('Y-m-d',strtotime($date))).
						' with amount '.
						(($this->request->data['PurchasedReceipt']['currency_id']!='c8')?$this->request->data['PurchasedReceipt']['amount']:$this->request->data['PurchasedReceipt']['orig_amount']).
						' at rate '.
						(($this->request->data['PurchasedReceipt']['currency_id']!='c8')?$this->request->data['PurchasedReceipt']['rate']:$this->request->data['PurchasedReceipt']['orig_rate']).
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
					$this->Session->setFlash(__('The purchase receipt could not be saved. Please, try again.'),'flash_error');
				}
			}else{
            	$this->Session->setFlash(__($flashMessage),'flash_error');
            }
		} else {
			$options = array('conditions' => array('PurchasedReceipt.' . $this->PurchasedReceipt->primaryKey => $id));
			$this->request->data = $this->PurchasedReceipt->find('first', $options);
		}
		$purchasedPurposes = $this->PurchasedReceipt->PurchasedPurpose->find('list',array(
			'conditions'=>array(
				'NOT'=>array(
					'PurchasedPurpose.id'=>array('p38','p39','p40','p42')
				)
			)
		));
		$users = $this->PurchasedReceipt->User->find('list',array(
			'conditions'=>array(
				'NOT'=>array(
					'User.role'=>array(
						'super_admin','customer'
					)
				)
			),
			'recursive'=>-1
		));
		
		$currencies = $this->PurchasedReceipt->Currency->find('list',[
			'order'=>'Currency.is_other_currency ASC,Currency.arrangement ASC, Currency.id ASC',
		]);
		$other_currencies = $this->PurchasedReceipt->OtherCurrency->find('list');
		$this->set(compact('purchasedPurposes', 'currencies','users','other_currencies'));
		
		$_fox=($this->Session->read('fox'));
		@$use_system_board_rates = $_fox['Fox']['use_system_board_rates'];
		if($use_system_board_rates){
			$currenciesDetails = $this->PurchasedReceipt->Currency->find('all',array('recursive'=>-1));
			$otherCurrenciesDetails = $this->PurchasedReceipt->OtherCurrency->find('all',array('recursive'=>-1));
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

		$this->PurchasedReceipt->id = $id;
		if (!$this->PurchasedReceipt->exists()) {
			throw new NotFoundException(__('Invalid purchase receipt'));
		}
		
		$purchased_receipt=$this->PurchasedReceipt->find('first',array(
			'conditions'=>array(
				'PurchasedReceipt.id'=>$id
			),
			'recursive'=>-1
		));		
		$deleted_purchased_receipt['DeletedPurchasedReceipt']=$purchased_receipt['PurchasedReceipt'];
		$_date=date('Y-m-d H:i:s');
		if ($this->PurchasedReceipt->delete()) {
			$this->Session->setFlash(__('Purchase receipt deleted'),'flash_success');
			$this->DeletedPurchasedReceipt->save($deleted_purchased_receipt);		
			//Save transaction log
			$func=$this->Func;
			$action_performed=
				$this->Auth->User('name').
				' deleted purchase receipt['.($purchased_receipt['PurchasedReceipt']['id']).'] of '.
				($purchased_receipt['PurchasedReceipt']['date']).' '.(date('h:i:sa',strtotime($purchased_receipt['PurchasedReceipt']['t_time']))).
				' with amount '.
				(($purchased_receipt['PurchasedReceipt']['currency_id']!='c8')?$purchased_receipt['PurchasedReceipt']['amount']:$purchased_receipt['PurchasedReceipt']['orig_amount']).
				' at rate '.
				(($purchased_receipt['PurchasedReceipt']['currency_id']!='c8')?$purchased_receipt['PurchasedReceipt']['rate']:$purchased_receipt['PurchasedReceipt']['orig_rate']).
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
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Purchase receipt was not deleted'),'flash_error');
		$this->redirect(array('action' => 'index'));
	}
	
	function print_multiple_receipts($receipts){
		$data = array(
			'MultiplePrintReceipt'=>array(
				'id'=>'12345',
				'receipts'=>$receipts,
				'was_printed'=>0,
				'receipt_table'=>'purchased_receipts'
			)
		);
		if($this->Auth->User('printing_place')==2){
			$data['MultiplePrintReceipt']['remote_addr'] = str_replace('::1','127.0.0.1',$_SERVER['REMOTE_ADDR']);
		}
		$this->MultiplePrintReceipt->save($data);	
		echo 'done';
		exit;
	}
}
