<?php
App::uses('AppController', 'Controller');
/**
 * Users Controller
 *
 * @property User $User
 */
class UsersController extends AppController {

	public $uses=array('User','ActionLog','Notifications.Notification','Creditor','Debtor','Receivable','Withdrawal','Currency','OtherCurrency','Opening','Safe','SafeTransaction');
	
	public $components = array(
        'RequestHandler',
      /*  'Rest.Rest' => array(
            'catchredir' => true, // Recommended unless you implement something yourself
            'debug' => 2,
            'actions' => array(
                'fox_login' => array(
                    'extract' => array('response'),
                ),
            ),
        ),*/
    );
	
	function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('reset_password','my_actions','register','logout');
		
        if ($this->action == 'add' || $this->action == 'add_customers' || $this->action == 'safe_deposit' || $this->action == 'safe_withdraw') {
			if($this->Auth->user('role')!='super_admin'){
				$this->Session->setFlash(__('Access Denied!!'),'flash_error');
				$this->redirect($this->Auth->logout());
			}
        }
    } 
    
   
	
	//Done by a cashier and approved by the admin
	public function safe_withdrawal_from_safe($currency_id,$amount){
		$amount=(int)$amount;
		$date_today	=($_REQUEST['date_today']);
		
		if($amount<1){
			$this->Session->setFlash(__('Amount should be greater than 0'),'flash_warning');
			$this->redirect(array('action'=>'view',$this->Auth->User('id')));
		}
		
		$my_field='';//field in which the amount is stored.
		$currency_description='';
		if($currency_id!='ugx'){
			if (!$this->Currency->exists($currency_id)) {
				$this->Session->setFlash(__('Currency not found'),'flash_error');
				$this->redirect(array('action'=>'view',$this->Auth->User('id')));
			}
			$my_field=$currency_id.'a';
			
			$_currency=$this->Currency->find('first',array(
				'recursive'=>-1,
				'conditions'=>array(
					'Currency.id'=>$currency_id
				)
			));
			$currency_description=$_currency['Currency']['description'];
		}else{
			$currency_id = 'UGX';
			$my_field='opening_ugx';
			$currency_description='UGX';
		}
		
		//Get today's opening
		$opening=$this->Opening->find('first',array(
			'conditions'=>array(
				'Opening.status'=>0,
				'Opening.date'=>$date_today,
				'Opening.user_id'=>$this->Auth->User('id')
			),
			'fields'=>array(
				'Opening.'.$my_field,'Opening.id'
			),
			'recursive'=>-1
		));
		
		if(!count($opening)){
			$this->Session->setFlash(__('No opening found for today. Please save the work for the previous working day.'),'flash_warning');
			$this->redirect(array('action'=>'view',$this->Auth->User('id')));
		}
		
		
		$safe=$this->Safe->find('first',array(
			'conditions'=>array('Safe.id'=>1111111111),
			'fields'=>array('Safe.'.$my_field,'Safe.id')
		));
		
		if(!count($safe)){
			$this->Safe->query("INSERT INTO `saves` (`id`, `opening_ugx`, `c1a`, `c2a`, `c3a`, `c4a`, `c5a`, `c6a`, `c7a`, `c8a`, `other_currencies`) VALUES ('1111111111', '0', '0', '0', '0', '0', '0', '0', '0', '0', NULL);");
			$safe=$this->Safe->find('first',array(
				'conditions'=>array('Safe.id'=>1111111111),
				'fields'=>array('Safe.'.$my_field,'Safe.id')
			));
			if(!count($safe)){
				$this->Session->setFlash(__('No safe found.'),'flash_error');
				$this->redirect(array('action'=>'view',$this->Auth->User('id')));
			}
		}

		
		
		if($amount > $safe['Safe'][''.$my_field]){
			$this->Session->setFlash(__('Insufficient amount left in the Safe. '.($safe['Safe'][''.$my_field]).' '.$$currency_description.' is left.'),'flash_warning');
			$this->redirect(array('action'=>'view',$this->Auth->User('id')));
		}
		
		$opening_amount=$opening['Opening'][''.$my_field];
		$new_opening_amount=$opening_amount+$amount;
		
		$this->request->data['Opening']['id']=$opening['Opening']['id'];
		$this->request->data['Opening'][''.$my_field]=$new_opening_amount;
		
		//update the opening
		if($this->Opening->save($this->request->data)){
			//update the safe.
			$this->request->data['Safe']['id']=$safe['Safe']['id'];
			$this->request->data['Safe'][''.$my_field]=($safe['Safe'][''.$my_field]-$amount);
			if($this->Safe->save($this->request->data)){
				$this->Session->setFlash(__('Successfully withdrew '.$amount.' '.$currency_description),'flash_success');
				
				//Save transaction log
				$func=$this->Func;
				$_date=date($date_today.' H:i:s');
				$action_performed=$this->Auth->User('name').' withdrew '.$amount.' '.$currency_description.' from the safe on '.(date('M d Y h:i:sa',strtotime($_date)));
				
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
				
			}else{
				$this->request->data['Opening'][''.$my_field]=$opening_amount;
				$this->Opening->save($this->request->data);
				$this->Session->setFlash(__('Failed to withdraw amount. Please try again.'),'flash_error');
			}
		}else{
			$this->Session->setFlash(__('Failed to update your amount withdrawn. Please try again or contact vendor if it persists.'),'flash_error');
		}
		
		$this->redirect(array('action'=>'view',$this->Auth->User('id')));
		
		//Log:Hillary withdrew 2000 Dollar from the safe on jul 07 2013 8:59am	
		
	
	}
	
	//Done by a cashier
	public function other_safe_withdrawal_from_safe($currency_id,$amount){
		$amount=(int)$amount;
		$date_today	=($_REQUEST['date_today']);
		
		if($amount<1){
			$this->Session->setFlash(__('Amount should be greater than 0'),'flash_warning');
			$this->redirect(array('action'=>'view',$this->Auth->User('id')));
		}
		
		$my_field='';//field in which the amount is stored.
		$currency_description='';
		if (!$this->OtherCurrency->exists($currency_id)) {
			$this->Session->setFlash(__('Currency not found'),'flash_warning');
			$this->redirect(array('action'=>'view',$this->Auth->User('id')));
		}
		$my_field=$currency_id.'a';
		$currency_description=''.$currency_id;
		
		$other_currencies=$this->OtherCurrency->find('all',array(
			'recursive'=>-1,
			'conditions'=>array(
				'OtherCurrency.id'=>$currency_id
			)
		));
		
		//Get today's opening
		$opening=$this->Opening->find('first',array(
			'conditions'=>array(
				'Opening.status'=>0,
				'Opening.date'=>$date_today,
				'Opening.user_id'=>$this->Auth->User('id')
			),
			'fields'=>array(
				'Opening.other_currencies','Opening.id'
			),
			'recursive'=>-1
		));
		
		if(!count($opening)){
			$this->Session->setFlash(__('No opening found for today. Please save the work for the previous working day.'),'flash_warning');
			$this->redirect(array('action'=>'view',$this->Auth->User('id')));
		}
		
		$opening_data=json_decode($opening['Opening']['other_currencies']);
		
		
		$safe=$this->Safe->find('first',array(
			'conditions'=>array('Safe.id'=>1111111111),
			'fields'=>array('Safe.other_currencies','Safe.id')
		));
		
		if(!count($safe)){
			$this->Safe->query("INSERT INTO `saves` (`id`, `opening_ugx`, `c1a`, `c2a`, `c3a`, `c4a`, `c5a`, `c6a`, `c7a`, `c8a`, `other_currencies`) VALUES ('1111111111', '0', '0', '0', '0', '0', '0', '0', '0', '0', NULL);");
			$safe=$this->Safe->find('first',array(
				'conditions'=>array('Safe.id'=>1111111111),
				'fields'=>array('Safe.other_currencies','Safe.id')
			));
			if(!count($safe)){
				$this->Session->setFlash(__('No safe found.'),'flash_warnings');
				$this->redirect(array('action'=>'view',$this->Auth->User('id')));
			}
		}
		
		$safe_data=json_decode($safe['Safe']['other_currencies']);
		
		
		$opening_arr['data']=array();
		
		if(count($opening_data)){
			foreach($other_currencies as $other_currency){
				$_amount=0;
				foreach($opening_data as $_other_currencies){
					foreach($_other_currencies as $_other_currency){	
						if(isset($_other_currency->CID)){
							if($_other_currency->CID==$other_currency['OtherCurrency']['id']){
								$_amount=$_other_currency->CAMOUNT;
							}
						}
					}
				}
				
				if($other_currency['OtherCurrency']['id']==$currency_id){
					$_amount+=$amount;
				}
								
				array_push($opening_arr['data'],array(
					'CID'=>$currency_id,
					''.($currency_id)=>$currency_id,
					'CRATE'=>0,
					'CAMOUNT'=>$_amount,
					'CNAME'=>$currency_id				
				));
			}
		}else{
			//If there has not been any amount saved for any other currency
			array_push($opening_arr['data'],array(
				'CID'=>$currency_id,
				''.($currency_id)=>$currency_id,
				'CRATE'=>0,
				'CAMOUNT'=>$amount,
				'CNAME'=>$currency_id				
			));
		}
		
		
		$safe_arr['data']=array();
		
		if(count($safe_data)){
			foreach($other_currencies as $other_currency){
				$_amount=0;
				foreach($safe_data as $_other_currencies){
					foreach($_other_currencies as $_other_currency){	
						if(isset($_other_currency->CID)){
							if($_other_currency->CID==$other_currency['OtherCurrency']['id']){
								$_amount=$_other_currency->CAMOUNT;
							}
						}
					}
				}
				
				if($other_currency['OtherCurrency']['id']==$currency_id){
					if($amount > $_amount){
						$this->Session->setFlash(__('Insufficient amount of '.($currency_id).' in safe. '.($_amount).' left.'),'flash_warning');
						$this->redirect(array('action'=>'view',$this->Auth->User('id')));
					}
					$_amount-=$amount;
				}
								
				array_push($safe_arr['data'],array(
					'CID'=>$currency_id,
					''.($currency_id)=>$currency_id,
					'CAMOUNT'=>$_amount
				));
			}
		}else{
			//If there has not been any amount saved for any other currency
			$this->Session->setFlash(__('No currency found in the safe.'),'flash_warning');
			$this->redirect(array('action'=>'view',$this->Auth->User('id')));
		}
		
		
		$this->request->data['Opening']['id']=$opening['Opening']['id'];
		$this->request->data['Opening']['other_currencies']=json_encode($opening_arr);
		
		//update the opening
		if($this->Opening->save($this->request->data)){
			//update the safe.
			$this->request->data['Safe']['id']=$safe['Safe']['id'];
			$this->request->data['Safe']['other_currencies']=json_encode($safe_arr);
			if($this->Safe->save($this->request->data)){
				$this->Session->setFlash(__('Successfully withdrew '.$amount.' '.$currency_description),'flash_success');
				
				//Save transaction log
				$func=$this->Func;
				$_date=date($date_today.' H:i:s');
				$action_performed=$this->Auth->User('name').' withdrew '.$amount.' '.$currency_description.' from the safe on '.(date('M d Y h:i:sa',strtotime($_date)));
				
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
				
			}else{
				$this->request->data['Opening'][''.$my_field]=$opening_amount;
				$this->Opening->save($this->request->data);
				$this->Session->setFlash(__('Failed to withdraw amount. Please try again.'),'flash_error');
			}
		}else{
			$this->Session->setFlash(__('Failed to update your amount withdrawn. Please try again or contact vendor if it persists.'),'flash_error');
		}
		
		$this->redirect(array('action'=>'view',$this->Auth->User('id')));
	
	}
	
	//Done by a cashier
	public function safe_return_to_safe($user_id,$currency_id,$amount){

		//$user_id = $this->Auth->User('id');

		$amount=(int)$amount;
		$date_today	=($_REQUEST['date_today']);
		
		if($amount<1){
			$this->Session->setFlash(__('Amount should be greater than 0'),'flash_warning');
			$this->redirect(array('action'=>'view',$user_id));
		}
		
		$my_field='';//field in which the amount is stored.
		$currency_description='';
		if($currency_id!='ugx'){
			if (!$this->Currency->exists($currency_id)) {
				$this->Session->setFlash(__('Currency not found'),'flash_error');
				$this->redirect(array('action'=>'view',$user_id));
			}
			$my_field=$currency_id.'a';
			
			$_currency=$this->Currency->find('first',array(
				'recursive'=>-1,
				'conditions'=>array(
					'Currency.id'=>$currency_id
				)
			));
			$currency_description=$_currency['Currency']['description'];
		}else{
			$currency_id = 'UGX';
			$my_field='opening_ugx';
			$currency_description='UGX';
		}
		
		//Get today's opening
		$opening=$this->Opening->find('first',array(
			'conditions'=>array(
				'Opening.status'=>0,
				'Opening.date'=>$date_today,
				'Opening.user_id'=>$user_id
			),
			'fields'=>array(
				'Opening.'.$my_field,'Opening.id'
			),
			'recursive'=>-1
		));
		
		if(!count($opening)){
			$this->Session->setFlash(__('No opening found for today. Please save the work for the previous working day.'),'flash_warning');
			$this->redirect(array('action'=>'view',$user_id));
		}
		
		
		$safe=$this->Safe->find('first',array(
			'conditions'=>array('Safe.id'=>1111111111),
			'fields'=>array('Safe.'.$my_field,'Safe.id')
		));

		if(!count($safe)){
			$this->Safe->query("INSERT INTO `saves` (`id`, `opening_ugx`, `c1a`, `c2a`, `c3a`, `c4a`, `c5a`, `c6a`, `c7a`, `c8a`, `other_currencies`) VALUES ('1111111111', '0', '0', '0', '0', '0', '0', '0', '0', '0', NULL);");
			$safe=$this->Safe->find('first',array(
				'conditions'=>array('Safe.id'=>1111111111),
				'fields'=>array('Safe.'.$my_field,'Safe.id')
			));
			if(!count($safe)){
				$this->Session->setFlash(__('No safe found.'),'flash_warning');
				$this->redirect(array('action'=>'view',$user_id));
			}
		}
		
		$opening_amount=$opening['Opening'][''.$my_field];
		if($amount > $opening_amount){
			$this->Session->setFlash(__('Insufficient amount on your account. '.($opening_amount).' '.$currency_description.' is left.'),'flash_warning');
			$this->redirect(array('action'=>'view',$user_id));
		}
		$new_opening_amount=$opening_amount-$amount;
		
		$this->request->data['Opening']['id']=$opening['Opening']['id'];
		$this->request->data['Opening'][''.$my_field]=$new_opening_amount;
		
		//update the opening
		if($this->Opening->save($this->request->data)){
			//update the safe.
			$this->request->data['Safe']['id']=$safe['Safe']['id'];
			$this->request->data['Safe'][''.$my_field]=($safe['Safe'][''.$my_field]+$amount);
			if($this->Safe->save($this->request->data)){
				$this->Session->setFlash(__('Successfully returned '.$amount.' '.$currency_description.' to safe.'),'flash_success');
				
				//Save transaction log
				$func=$this->Func;
				$_date=date($date_today.' H:i:s');
				$action_performed=$this->Auth->User('name').' returned '.$amount.' '.$currency_description.' from their account opening back to the safe on '.(date('M d Y h:i:sa',strtotime($_date)));
				
				$action_log=array(
					'ActionLog'=>array(
						'id'=>$func->getUID1(),
						'user_id'=>$user_id,
						'action_performed'=>$action_performed,
						'date_created'=>date('Y-m-d',strtotime($_date)),
						'date_time_created'=>$_date
					)
				);				
				$this->ActionLog->save($action_log);
				
			}else{
				$this->request->data['Opening'][''.$my_field]=$opening_amount;
				$this->Opening->save($this->request->data);
				$this->Session->setFlash(__('Failed to return the cash. Please try again.'),'flash_error');
			}
		}else{
			$this->Session->setFlash(__('Failed to update your amount returned in your account. Please try again or contact vendor if it persists.'),'flash_error');
		}
		
		$this->redirect(array('action'=>'view',$user_id));
	}
	
	//Done by a cashier
	public function other_safe_return_to_safe($user_id,$currency_id,$amount){
		//$user_id = $this->Auth->User('id');
		$amount=(int)$amount;
		$date_today	=($_REQUEST['date_today']);
		
		if($amount<1){
			$this->Session->setFlash(__('Amount should be greater than 0'),'flash_warning');
			$this->redirect(array('action'=>'view',$user_id));
		}
		
		$my_field='';//field in which the amount is stored.
		$currency_description='';
		if (!$this->OtherCurrency->exists($currency_id)) {
			$this->Session->setFlash(__('Currency not found'),'flash_warning');
			$this->redirect(array('action'=>'view',$user_id));
		}
		$my_field=$currency_id.'a';
		$currency_description=''.$currency_id;
		
		$other_currencies=$this->OtherCurrency->find('all',array(
			'recursive'=>-1,
			'conditions'=>array(
				'OtherCurrency.id'=>$currency_id
			)
		));
		
		//Get today's opening
		$opening=$this->Opening->find('first',array(
			'conditions'=>array(
				'Opening.status'=>0,
				'Opening.date'=>$date_today,
				'Opening.user_id'=>$user_id
			),
			'fields'=>array(
				'Opening.other_currencies','Opening.id'
			),
			'recursive'=>-1
		));
			
		
		if(!count($opening)){
			$this->Session->setFlash(__('No opening found for today. Please save the work for the previous working day.'),'flash_warning');
			$this->redirect(array('action'=>'view',$user_id));
		}
		
		$opening_data=json_decode($opening['Opening']['other_currencies']);
		
		
		$safe=$this->Safe->find('first',array(
			'conditions'=>array('Safe.id'=>1111111111),
			'fields'=>array('Safe.other_currencies','Safe.id')
		));
		

		if(!count($safe)){
			$this->Safe->query("INSERT INTO `saves` (`id`, `opening_ugx`, `c1a`, `c2a`, `c3a`, `c4a`, `c5a`, `c6a`, `c7a`, `c8a`, `other_currencies`) VALUES ('1111111111', '0', '0', '0', '0', '0', '0', '0', '0', '0', NULL);");
			$safe=$this->Safe->find('first',array(
				'conditions'=>array('Safe.id'=>1111111111),
				'fields'=>array('Safe.other_currencies','Safe.id')
			));
			if(!count($safe)){
				$this->Session->setFlash(__('No safe found.'),'flash_warning');
				$this->redirect(array('action'=>'view',$user_id));
			}
		}

		$safe_data=json_decode($safe['Safe']['other_currencies']);
		
		
		$opening_arr['data']=array();
		
		if(count($opening_data)){
			foreach($other_currencies as $other_currency){
				$_amount=0;
				foreach($opening_data as $_other_currencies){
					foreach($_other_currencies as $_other_currency){	
						if(isset($_other_currency->CID)){
							if($_other_currency->CID==$other_currency['OtherCurrency']['id']){
								$_amount=$_other_currency->CAMOUNT;
							}
						}
					}
				}
				
				if($other_currency['OtherCurrency']['id']==$currency_id){
					if($amount > $_amount){
						$this->Session->setFlash(__('Insufficient amount of '.($currency_id).' on your account opening. '.($_amount).' left.'),'flash_warning');
						$this->redirect(array('action'=>'view',$user_id));
					}
					$_amount-=$amount;
				}
								
				array_push($opening_arr['data'],array(
					'CID'=>$currency_id,
					''.($currency_id)=>$currency_id,
					'CRATE'=>0,
					'CAMOUNT'=>$_amount,
					'CNAME'=>$currency_id				
				));
			}
		}else{
			//If there has not been any amount saved for any other currency
			array_push($opening_arr['data'],array(
				'CID'=>$currency_id,
				''.($currency_id)=>$currency_id,
				'CRATE'=>0,
				'CAMOUNT'=>$amount,
				'CNAME'=>$currency_id				
			));
		}
		
		
		$safe_arr['data']=array();
		
		if(count($safe_data)){
			foreach($other_currencies as $other_currency){
				$_amount=0;
				foreach($safe_data as $_other_currencies){
					foreach($_other_currencies as $_other_currency){	
						if(isset($_other_currency->CID)){
							if($_other_currency->CID==$other_currency['OtherCurrency']['id']){
								$_amount=$_other_currency->CAMOUNT;
							}
						}
					}
				}
				
				if($other_currency['OtherCurrency']['id']==$currency_id){
					$_amount+=$amount;
				}
								
				array_push($safe_arr['data'],array(
					'CID'=>$currency_id,
					''.($currency_id)=>$currency_id,
					'CAMOUNT'=>$_amount
				));
			}
		}else{
			//If there has not been any amount saved for any other currency
			$this->Session->setFlash(__('No currency found in the safe.'),'flash_warning');
			$this->redirect(array('action'=>'view',$user_id));
		}
		
		
		$this->request->data['Opening']['id']=$opening['Opening']['id'];
		$this->request->data['Opening']['other_currencies']=json_encode($opening_arr);
		
		//update the opening
		if($this->Opening->save($this->request->data)){
			//update the safe.
			$this->request->data['Safe']['id']=$safe['Safe']['id'];
			$this->request->data['Safe']['other_currencies']=json_encode($safe_arr);
			if($this->Safe->save($this->request->data)){
				$this->Session->setFlash(__('Successfully deposited '.$amount.' '.$currency_description),'flash_success');
				
				//Save transaction log
				$func=$this->Func;
				$_date=date($date_today.' H:i:s');
				$action_performed=$this->Auth->User('name').' deposited '.$amount.' '.$currency_description.' from their account opening to the safe on '.(date('M d Y h:i:sa',strtotime($_date)));
				
				$action_log=array(
					'ActionLog'=>array(
						'id'=>$func->getUID1(),
						'user_id'=>$user_id,
						'action_performed'=>$action_performed,
						'date_created'=>date('Y-m-d',strtotime($_date)),
						'date_time_created'=>$_date
					)
				);				
				$this->ActionLog->save($action_log);
				
			}else{
				$this->request->data['Opening'][''.$my_field]=$opening_amount;
				$this->Opening->save($this->request->data);
				$this->Session->setFlash(__('Failed to deposit amount. Please try again.'),'flash_error');
			}
		}else{
			$this->Session->setFlash(__('Failed to update your amount deposited. Please try again or contact vendor if it persists.'),'flash_error');
		}
		
		$this->redirect(array('action'=>'view',$user_id));

	}
	
	//Done by a cashier
	public function safe_deposit_into_safe($currency_id,$amount){
		$amount=(int)$amount;
		$date_today	=($_REQUEST['date_today']);
		
		if($amount<1){
			$this->Session->setFlash(__('Amount should be greater than 0'),'flash_warning');
			$this->redirect(array('action'=>'view',$this->Auth->User('id')));
		}
		
		$my_field='';//field in which the amount is stored.
		$currency_description='';
		if($currency_id!='ugx'){
			if (!$this->Currency->exists($currency_id)) {
				$this->Session->setFlash(__('Currency ' . $currency_id . ' not found'),'flash_warning');
				$this->redirect(array('action'=>'view',$this->Auth->User('id')));
			}
			$my_field=$currency_id.'a';
			
			$_currency=$this->Currency->find('first',array(
				'recursive'=>-1,
				'conditions'=>array(
					'Currency.id'=>$currency_id
				)
			));
			$currency_description=$_currency['Currency']['description'];
		}else{
			$currency_id = 'UGX';
			$my_field='opening_ugx';
			$currency_description='UGX';
		}
		
		//Get today's opening
		$opening=$this->Opening->find('first',array(
			'conditions'=>array(
				'Opening.status'=>0,
				'NOT'=>array(
					'Opening.date'=>$date_today
				),
				'Opening.user_id'=>$this->Auth->User('id')
			),
			'fields'=>array(
				'Opening.'.$my_field,'Opening.id'
			),
			'recursive'=>-1,
			'order'=>'Opening.date DESC'
		));
		
		if(!count($opening)){
			$this->Session->setFlash(__('No opening found for the next working day. Please save your work first.'),'flash_warning');
			$this->redirect(array('action'=>'view',$this->Auth->User('id')));
		}
		
		
		$safe=$this->Safe->find('first',array(
			'conditions'=>array(
				'Safe.id'=>1111111111
			),
			'fields'=>array(
				'Safe.'.$my_field,'Safe.id'
			)
		));
		
		if(!count($safe)){
			$this->Safe->query("INSERT INTO `saves` (`id`, `opening_ugx`, `c1a`, `c2a`, `c3a`, `c4a`, `c5a`, `c6a`, `c7a`, `c8a`, `other_currencies`) VALUES ('1111111111', '0', '0', '0', '0', '0', '0', '0', '0', '0', NULL);");
			$safe=$this->Safe->find('first',array(
				'conditions'=>array('Safe.id'=>1111111111),
				'fields'=>array('Safe.'.$my_field,'Safe.id')
			));
			if(!count($safe)){
				$this->Session->setFlash(__('No safe found.'),'flash_warning');
				$this->redirect(array('action'=>'view',$this->Auth->User('id')));
			}
		}
		
		$opening_amount=$opening['Opening'][''.$my_field];
		if($amount > $opening_amount){
			$this->Session->setFlash(__('Insufficient amount on your account. '.($opening_amount).' '.$currency_description.' is left.'),'flash_warning');
			$this->redirect(array('action'=>'view',$this->Auth->User('id')));
		}
		$new_opening_amount=$opening_amount-$amount;
		
		$this->request->data['Opening']['id']=$opening['Opening']['id'];
		$this->request->data['Opening'][''.$my_field]=$new_opening_amount;
		
		//update the opening
		if($this->Opening->save($this->request->data)){
			//update the safe.
			$this->request->data['Safe']['id']=$safe['Safe']['id'];
			$this->request->data['Safe'][''.$my_field]=($safe['Safe'][''.$my_field]+$amount);
			if($this->Safe->save($this->request->data)){
				$this->Session->setFlash(__('Successfully deposited '.$amount.' '.$currency_description),'flash_success');
				
				//Save transaction log
				$func=$this->Func;
				$_date=date($date_today.' H:i:s');
				$action_performed=$this->Auth->User('name').' deposited '.$amount.' '.$currency_description.' from their account opening to the safe on '.(date('M d Y h:i:sa',strtotime($_date)));
				
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
				
			}else{
				$this->request->data['Opening'][''.$my_field]=$opening_amount;
				$this->Opening->save($this->request->data);
				$this->Session->setFlash(__('Failed to deposit amount. Please try again.'),'flash_error');
			}
		}else{
			$this->Session->setFlash(__('Failed to update your amount deposited. Please try again or contact vendor if it persists.'),'flash_error');
		}
		
		$this->redirect(array('action'=>'view',$this->Auth->User('id')));
	
	}
	
	//Done by a cashier
	public function other_safe_deposit_into_safe($currency_id,$amount){
		$amount=(int)$amount;
		$date_today	=($_REQUEST['date_today']);
		
		if($amount<1){
			$this->Session->setFlash(__('Amount should be greater than 0'),'flash_warning');
			$this->redirect(array('action'=>'view',$this->Auth->User('id')));
		}
		
		$my_field='';//field in which the amount is stored.
		$currency_description='';
		if (!$this->Currency->exists($currency_id)) {
			$this->Session->setFlash(__('Currency ' . $currency_id . ' not found'),'flash_warning');
			$this->redirect(array('action'=>'view',$this->Auth->User('id')));
		}
		$my_field=$currency_id.'a';
		$currency_description=''.$currency_id;
		
		$other_currencies=$this->OtherCurrency->find('all',array(
			'recursive'=>-1,
			'conditions'=>array(
				'OtherCurrency.id'=>$currency_id
			)
		));
		
		//Get today's opening
		$opening=$this->Opening->find('first',array(
			'conditions'=>array(
				'Opening.status'=>0,
				'NOT'=>array(
					'Opening.date'=>$date_today
				),
				'Opening.user_id'=>$this->Auth->User('id')
			),
			'fields'=>array(
				'Opening.other_currencies','Opening.id'
			),
			'recursive'=>-1,
			'order'=>'Opening.date DESC'
		));
			
		
		if(!count($opening)){
			$this->Session->setFlash(__('No opening found for the next working day. Please save your work first.'),'flash_warning');
			$this->redirect(array('action'=>'view',$this->Auth->User('id')));
		}
		
		$opening_data=json_decode($opening['Opening']['other_currencies']);
		
		
		$safe=$this->Safe->find('first',array(
			'conditions'=>array(
				'Safe.id'=>1111111111
			),
			'fields'=>array(
				'Safe.other_currencies','Safe.id'
			)
		));
		
		if(!count($safe)){
			$this->Safe->query("INSERT INTO `saves` (`id`, `opening_ugx`, `c1a`, `c2a`, `c3a`, `c4a`, `c5a`, `c6a`, `c7a`, `c8a`, `other_currencies`) VALUES ('1111111111', '0', '0', '0', '0', '0', '0', '0', '0', '0', NULL);");
			$safe=$this->Safe->find('first',array(
				'conditions'=>array('Safe.id'=>1111111111),
				'fields'=>array('Safe.other_currencies','Safe.id')
			));
			if(!count($safe)){
				$this->Session->setFlash(__('No safe found.'),'flash_warning');
				$this->redirect(array('action'=>'view',$this->Auth->User('id')));
			}
		}
		
		
		$safe_data=json_decode($safe['Safe']['other_currencies'],true);
		$data_other_currencies = [];
		foreach ($safe_data['data'] as $value) {
			$data_other_currencies[$value['CID']] = $value;
		}
		
		$opening_arr['data']=array();
		
		if(count($opening_data)){
			foreach($other_currencies as $other_currency){
				@$_amount=$data_other_currencies[$other_currency['Currency']['id']]['CAMOUNT'];
				
				if($other_currency['OtherCurrency']['id']==$currency_id){
					if($amount > $_amount){
						$this->Session->setFlash(__('Insufficient amount of '.($currency_id).' on your account opening. '.($_amount).' left.'),'flash_warning');
						$this->redirect(array('action'=>'view',$this->Auth->User('id')));
					}
					$_amount-=$amount;
				}
								
				array_push($opening_arr['data'],array(
					'CID'=>$currency_id,
					''.($currency_id)=>$currency_id,
					'CRATE'=>0,
					'CAMOUNT'=>$_amount,
					'CNAME'=>$currency_id				
				));
			}
		}else{
			//If there has not been any amount saved for any other currency
			array_push($opening_arr['data'],array(
				'CID'=>$currency_id,
				''.($currency_id)=>$currency_id,
				'CRATE'=>0,
				'CAMOUNT'=>$amount,
				'CNAME'=>$currency_id				
			));
		}
		
		
		$safe_arr['data']=array();
		
		if(count($safe_data)){
			foreach($other_currencies as $other_currency){
				$_amount=0;
				/*foreach($safe_data as $_other_currencies){
					foreach($_other_currencies as $_other_currency){	
						if(isset($_other_currency->CID)){
							if($_other_currency->CID==$other_currency['OtherCurrency']['id']){
								$_amount=$_other_currency->CAMOUNT;
							}
						}
					}
				}*/

				@$_amount=$data_other_currencies[$other_currency['Currency']['id']]['CAMOUNT'];
				
				if($other_currency['OtherCurrency']['id']==$currency_id){
					$_amount+=$amount;
				}
								
				array_push($safe_arr['data'],array(
					'CID'=>$currency_id,
					''.($currency_id)=>$currency_id,
					'CAMOUNT'=>$_amount
				));
			}
		}else{
			//If there has not been any amount saved for any other currency
			$this->Session->setFlash(__('No currency found in the safe.'),'flash_warning');
			$this->redirect(array('action'=>'view',$this->Auth->User('id')));
		}
		
		
		$this->request->data['Opening']['id']=$opening['Opening']['id'];
		$this->request->data['Opening']['other_currencies']=json_encode($opening_arr);
		
		//update the opening
		if($this->Opening->save($this->request->data)){
			//update the safe.
			$this->request->data['Safe']['id']=$safe['Safe']['id'];
			$this->request->data['Safe']['other_currencies']=json_encode($safe_arr);
			if($this->Safe->save($this->request->data)){
				$this->Session->setFlash(__('Successfully deposited '.$amount.' '.$currency_description),'flash_success');
				
				//Save transaction log
				$func=$this->Func;
				$_date=date($date_today.' H:i:s');
				$action_performed=$this->Auth->User('name').' deposited '.$amount.' '.$currency_description.' from their account opening to the safe on '.(date('M d Y h:i:sa',strtotime($_date)));
				
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
				
			}else{
				$this->request->data['Opening'][''.$my_field]=$opening_amount;
				$this->Opening->save($this->request->data);
				$this->Session->setFlash(__('Failed to deposit amount. Please try again.'),'flash_error');
			}
		}else{
			$this->Session->setFlash(__('Failed to update your amount deposited. Please try again or contact vendor if it persists.'),'flash_error');
		}
		
		$this->redirect(array('action'=>'view',$this->Auth->User('id')));

	}
	
	//Done by a cashier
	public function safe_send_to($user_id,$currency_id,$amount){

		if (!$this->User->exists($user_id)) {
			$this->Session->setFlash(__('User/Cashier not found'),'flash_warning');
			$this->redirect(array('action'=>'view',$this->Auth->User('id')));
		}
		
		//Get destination user/cashier's name for the logs
		$destin_cashier_name='';
		$destin_user=$this->User->find('first',array(
			'conditions'=>array(
				'User.role'=>'regular',
				'User.id'=>$user_id
			),
			'recursive'=>-1,
			'fields'=>array(
				'User.name'
			)
		));
		
		if(!count($destin_user)){
			$this->Session->setFlash(__('Cashier Not found.'),'flash_warning');
			$this->redirect(array('action'=>'view',$this->Auth->User('id')));
		}
		
		$destin_cashier_name=$destin_user['User']['name'];
		
		$amount=(int)$amount;
		$date_today	=($_REQUEST['date_today']);
		
		if($amount<1){
			$this->Session->setFlash(__('Amount should be greater than 0'),'flash_warning');
			$this->redirect(array('action'=>'view',$this->Auth->User('id')));
		}
		
		$my_field='';//field in which the amount is stored.
		$currency_description='';
		if($currency_id!='ugx'){
			if (!$this->Currency->exists($currency_id)) {
				$this->Session->setFlash(__('Currency not found'),'flash_warning');
				$this->redirect(array('action'=>'view',$this->Auth->User('id')));
			}
			$my_field=$currency_id.'a';
			
			$_currency=$this->Currency->find('first',array(
				'recursive'=>-1,
				'conditions'=>array(
					'Currency.id'=>$currency_id
				)
			));
			$currency_description=$_currency['Currency']['description'];
		}else{
			$currency_id = 'UGX';
			$my_field='opening_ugx';
			$currency_description='UGX';
		}

		$data = ['SafeTransaction'=>[
			'user_id'=>$this->Auth->User('id'),
			'amount'=>$amount,
			'currency'=>$currency_id,
			'transaction_from'=>$this->Auth->User('id'),
			'transaction_to'=>$user_id,
			'transaction_type'=>'TRANSFER',
			'comment'=>'Cash transfer',
			'status'=>'PENDING',
			'date'=>date('Y-m-d H:i:s')
		]];
		if($this->SafeTransaction->save($data)){
			//create a notification to super admins
			$this->Session->setFlash(__('Request sent to all admin/supervisors.'),'flash_success');
			$super_admins = $this->User->find('all',[
				'conditions'=>['User.role'=>'super_admin'],
				'limit'=>5,
				'fields'=>['User.id']
			]);
			foreach ($super_admins as  $super_admin) {
				$this->User->Notification->msg(
					$super_admin['User']['id']," requests you to give ".$destin_cashier_name.' '.number_format($amount).' '.$currency_description,
					null,'/safe_transactions/index/'.$this->SafeTransaction->getLastInsertID(),$this->Auth->User('id')
				);
			}
		}else{
			$this->Session->setFlash(__('Error while creating transaction. Please try again later.'),'flash_warning');
		}
		$this->redirect(array('action'=>'view',$this->Auth->User('id')));
	}
	
	//Done by a cashier
	public function other_safe_send_to($user_id,$currency_id,$amount){	
		if (!$this->User->exists($user_id)) {
			$this->Session->setFlash(__('User/Cashier not found'),'flash_warning');
			$this->redirect(array('action'=>'view',$this->Auth->User('id')));
		}
		
		//Get destination user/cashier's name for the logs
		$destin_cashier_name='';
		$destin_user=$this->User->find('first',array(
			'conditions'=>array(
				'User.role'=>'regular',
				'User.id'=>$user_id
			),
			'recursive'=>-1,
			'fields'=>array(
				'User.name'
			)
		));
		
		if(!count($destin_user)){
			$this->Session->setFlash(__('Cashier Not found.'),'flash_warning');
			$this->redirect(array('action'=>'view',$this->Auth->User('id')));
		}
		
		$destin_cashier_name=$destin_user['User']['name'];
		
		$amount=(int)$amount;
		$date_today	=($_REQUEST['date_today']);
		
		if($amount<1){
			$this->Session->setFlash(__('Amount should be greater than 0'),'flash_warning');
			$this->redirect(array('action'=>'view',$this->Auth->User('id')));
		}
		
		$my_field='';//field in which the amount is stored.
		$currency_description='';
		if (!$this->Currency->exists($currency_id)) {
			$this->Session->setFlash(__('Currency ' . $currency_id . ' not found'),'flash_warning');
			$this->redirect(array('action'=>'view',$this->Auth->User('id')));
		}
		$my_field=$currency_id.'a';
		$currency_description=''.$currency_id;
		
		$other_currencies=$this->OtherCurrency->find('all',array(
			'recursive'=>-1,
			'conditions'=>array(
				'OtherCurrency.id'=>$currency_id
			)
		));
		

		$data = ['SafeTransaction'=>[
			'user_id'=>$this->Auth->User('id'),
			'amount'=>$amount,
			'currency'=>$currency_id,
			'transaction_from'=>$this->Auth->User('id'),
			'transaction_to'=>$user_id,
			'transaction_type'=>'TRANSFER',
			'comment'=>'Cash transfer',
			'status'=>'PENDING',
			'date'=>date('Y-m-d H:i:s')
		]];
		if($this->SafeTransaction->save($data)){
			//create a notification to super admins
			$this->Session->setFlash(__('Request sent to all admin/supervisors.'),'flash_success');
			$super_admins = $this->User->find('all',[
				'conditions'=>['User.role'=>'super_admin'],
				'limit'=>5,
				'fields'=>['User.id']
			]);
			foreach ($super_admins as  $super_admin) {
				$this->User->Notification->msg(
					$super_admin['User']['id']," requests you to give ".$destin_cashier_name.' '.number_format($amount).' '.$currency_description,
					null,'/safe_transactions/index/'.$this->SafeTransaction->getLastInsertID(),$this->Auth->User('id')
				);
			}
		}else{
			$this->Session->setFlash(__('Error while creating transaction. Please try again later.'),'flash_error');
		}
		$this->redirect(array('action'=>'view',$this->Auth->User('id')));
		/*
		
		//Get today's opening
		$opening=$this->Opening->find('first',array(
			'conditions'=>array(
				'Opening.status'=>0,
				'Opening.date'=>$date_today,
				'Opening.user_id'=>$this->Auth->User('id')
			),
			'fields'=>array(
				'Opening.other_currencies','Opening.id'
			),
			'recursive'=>-1
		));
		
		if(!count($opening)){
			$this->Session->setFlash(__('No opening found for today. Please save the work for the previous working day.'));
			$this->redirect(array('action'=>'view',$this->Auth->User('id')));
		}
		$opening_data=json_decode($opening['Opening']['other_currencies']);
		
		//Get destination/selected user's opening
		$destin_opening=$this->Opening->find('first',array(
			'conditions'=>array(
				'Opening.status'=>0,
				'Opening.date'=>$date_today,
				'Opening.user_id'=>$user_id
			),
			'fields'=>array(
				'Opening.other_currencies','Opening.id'
			),
			'recursive'=>-1
		));
		
		if(!count($destin_opening)){
			$this->Session->setFlash(__('Failed::Cashier selected has No opening found for today. He/She has to save work of previous working day.'));
			$this->redirect(array('action'=>'view',$this->Auth->User('id')));
		}
		$destin_data=json_decode($destin_opening['Opening']['other_currencies']);
		
		
		$opening_arr['data']=array();
		if(count($opening_data)){
			foreach($other_currencies as $other_currency){
				$_amount=0;
				foreach($opening_data as $_other_currencies){
					foreach($_other_currencies as $_other_currency){	
						if(isset($_other_currency->CID)){
							if($_other_currency->CID==$other_currency['OtherCurrency']['id']){
								$_amount=$_other_currency->CAMOUNT;
							}
						}
					}
				}
				
				if($other_currency['OtherCurrency']['id']==$currency_id){
					if($amount > $_amount){
						$this->Session->setFlash(__('Insufficient amount of '.($currency_id).' on your account opening. '.($_amount).' left.'));
						$this->redirect(array('action'=>'view',$this->Auth->User('id')));
					}
					$_amount-=$amount;
				}
								
				array_push($opening_arr['data'],array(
					'CID'=>$currency_id,
					''.($currency_id)=>$currency_id,
					'CRATE'=>0,
					'CAMOUNT'=>$_amount,
					'CNAME'=>$currency_id				
				));
			}
		}else{
			//If there has not been any amount saved for any other currency
			array_push($opening_arr['data'],array(
				'CID'=>$currency_id,
				''.($currency_id)=>$currency_id,
				'CRATE'=>0,
				'CAMOUNT'=>$amount,
				'CNAME'=>$currency_id				
			));
		}
		
		
		$destin_arr['data']=array();
		if(count($destin_data)){
			foreach($other_currencies as $other_currency){
				$_amount=0;
				foreach($destin_data as $_other_currencies){
					foreach($_other_currencies as $_other_currency){	
						if(isset($_other_currency->CID)){
							if($_other_currency->CID==$other_currency['OtherCurrency']['id']){
								$_amount=$_other_currency->CAMOUNT;
							}
						}
					}
				}
				
				if($other_currency['OtherCurrency']['id']==$currency_id){
					$_amount+=$amount;
				}
								
				array_push($destin_arr['data'],array(
					'CID'=>$currency_id,
					''.($currency_id)=>$currency_id,
					'CRATE'=>0,
					'CAMOUNT'=>$_amount,
					'CNAME'=>$currency_id				
				));
			}
		}else{
			//If there has not been any amount saved for any other currency
			array_push($destin_arr['data'],array(
				'CID'=>$currency_id,
				''.($currency_id)=>$currency_id,
				'CRATE'=>0,
				'CAMOUNT'=>$amount,
				'CNAME'=>$currency_id				
			));
		}
		
		$this->request->data['Opening']['id']=$opening['Opening']['id'];
		$this->request->data['Opening']['other_currencies']=json_encode($opening_arr);
		
		//update the opening
		if($this->Opening->save($this->request->data)){
			//update the destination/selected cashier's opening.
			$this->request->data['Opening']['id']=$destin_opening['Opening']['id'];
			$this->request->data['Opening']['other_currencies']=json_encode($destin_arr);
			if($this->Opening->save($this->request->data)){
				$this->Session->setFlash(__('Successfully sent cash to cashier.'));
				
				//Save transaction log
				$func=$this->Func;
				$_date=date($date_today.' H:i:s');
				$action_performed=$this->Auth->User('name').' sent '.$amount.' '.$currency_description.' to '.$destin_cashier_name.'\'s account opening on '.(date('M d Y h:i:sa',strtotime($_date)));
				
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
				
			}else{
				$this->request->data['Opening'][''.$my_field]=$opening_amount;
				$this->Opening->save($this->request->data);
				$this->Session->setFlash(__('Failed to send the cash. Please try again.'));
			}
		}else{
			$this->Session->setFlash(__('Failed to update your amount sent. Please try again or contact vendor if it persists.'));
		}
		
		$this->redirect(array('action'=>'view',$this->Auth->User('id')));
*/
	}
	//Done by super_admin
	public function safe_deposit($currency_id,$amount){
		$amount=(int)$amount;
		$date_today	=($_REQUEST['date_today']);
		
		if($amount<1){
			$this->Session->setFlash(__('Amount should be greater than 0'),'flash_warning');
			$this->redirect(array('action'=>'view',$this->Auth->User('id')));
		}
		
		$my_field='';//field in which the amount is stored.
		$currency_description='';
		if($currency_id!='ugx'){
			if (!$this->Currency->exists($currency_id)) {
				$this->Session->setFlash(__('Currency not found'),'flash_warning');
				$this->redirect(array('action'=>'view',$this->Auth->User('id')));
			}
			$my_field=$currency_id.'a';
			
			$_currency=$this->Currency->find('first',array(
				'recursive'=>-1,
				'conditions'=>array(
					'Currency.id'=>$currency_id
				)
			));
			$currency_description=$_currency['Currency']['description'];
		}else{
			$currency_id = 'UGX';
			$my_field='opening_ugx';
			$currency_description='UGX';
		}
		
		$safe=$this->Safe->find('first',array(
			'conditions'=>array(
				'Safe.id'=>1111111111
			),
			'fields'=>array(
				'Safe.'.$my_field,'Safe.id'
			)
		));
		
		if(!count($safe)){
			$this->Safe->query("INSERT INTO `saves` (`id`, `opening_ugx`, `c1a`, `c2a`, `c3a`, `c4a`, `c5a`, `c6a`, `c7a`, `c8a`, `other_currencies`) VALUES ('1111111111', '0', '0', '0', '0', '0', '0', '0', '0', '0', NULL);");
			$safe=$this->Safe->find('first',array(
				'conditions'=>array('Safe.id'=>1111111111),
				'fields'=>array('Safe.'.$my_field,'Safe.id')
			));
			if(!count($safe)){
				$this->Session->setFlash(__('No safe found.'),'flash_warning');
				$this->redirect(array('action'=>'view',$this->Auth->User('id')));
			}
		}
		
		$this->request->data['Safe']['id']=$safe['Safe']['id'];
		$this->request->data['Safe'][''.$my_field]=($safe['Safe'][''.$my_field]+$amount);
		
		if($this->Safe->save($this->request->data)){
			$this->Session->setFlash(__('Successfully deposited '.$amount.' '.$currency_description.' to safe.'),'flash_success');
				
			//Save transaction log
			$func=$this->Func;
			$_date=date($date_today.' H:i:s');
			$action_performed=$this->Auth->User('name').' directly deposited '.$amount.' '.$currency_description.' into the safe on '.(date('M d Y h:i:sa',strtotime($_date)));
			
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
		}else{
			$this->Session->setFlash(__('Failed to deposit into safe. Please try again.'),'flash_warning');
		}		
		$this->redirect(array('action'=>'view',$this->Auth->User('id')));
	}
	
	//Done by super_admin
	public function other_safe_deposit($currency_id,$amount){
		$amount=(int)$amount;
		$date_today	=($_REQUEST['date_today']);
		
		if($amount<1){
			$this->Session->setFlash(__('Amount should be greater than 0'),'flash_warning');
			$this->redirect(array('action'=>'view',$this->Auth->User('id')));
		}
		
		$my_field='';//field in which the amount is stored.
		$currency_description='';
		if (!$this->OtherCurrency->exists($currency_id)) {
			$this->Session->setFlash(__('Currency not found'),'flash_warning');
			$this->redirect(array('action'=>'view',$this->Auth->User('id')));
		}
		$my_field=$currency_id.'a';
		$currency_description=''.$currency_id;
		
		$other_currencies=$this->OtherCurrency->find('all',array(
			'recursive'=>-1,
			'conditions'=>array(
				'OtherCurrency.id'=>$currency_id
			)
		));
		
		$safe=$this->Safe->find('first',array(
			'conditions'=>array(
				'Safe.id'=>1111111111
			),
			'fields'=>array(
				'Safe.other_currencies','Safe.id'
			)
		));
		
		
		if(!count($safe)){
			$this->Safe->query("INSERT INTO `saves` (`id`, `opening_ugx`, `c1a`, `c2a`, `c3a`, `c4a`, `c5a`, `c6a`, `c7a`, `c8a`, `other_currencies`) VALUES ('1111111111', '0', '0', '0', '0', '0', '0', '0', '0', '0', NULL);");
			$safe=$this->Safe->find('first',array(
				'conditions'=>array('Safe.id'=>1111111111),
				'fields'=>array('Safe.other_currencies','Safe.id')
			));
			if(!count($safe)){
				$this->Session->setFlash(__('No safe found.'),'flash_warning');
				$this->redirect(array('action'=>'view',$this->Auth->User('id')));
			}
		}
		
		$this->request->data['Safe']['id']=$safe['Safe']['id'];
		
		$data=json_decode($safe['Safe']['other_currencies']);
		
		$arr['data']=array();
		
		if(count($data)){
			foreach($other_currencies as $other_currency){
				$_amount=0;
				foreach($data as $_other_currencies){
					foreach($_other_currencies as $_other_currency){	
						if(isset($_other_currency->CID)){
							if($_other_currency->CID==$other_currency['OtherCurrency']['id']){
								$_amount=$_other_currency->CAMOUNT;
							}
						}
					}
				}
				
				if($other_currency['OtherCurrency']['id']==$currency_id){
					$_amount+=$amount;
				}
				
				array_push($arr['data'],array(
					'CID'=>$currency_id,
					''.($currency_id)=>$currency_id,
					'CAMOUNT'=>$_amount
				));
			}
		}else{
			//If there has not been any amount saved for any other currency
			array_push($arr['data'],array(
				'CID'=>$currency_id,
				''.($currency_id)=>$currency_id,
				'CAMOUNT'=>$amount
			));
		}
		
		$this->request->data['Safe']['other_currencies']=json_encode($arr);
		
		if($this->Safe->save($this->request->data)){
			$this->Session->setFlash(__('Successfully deposited '.$amount.' '.$currency_description.' to safe.'),'flash_success');
				
			//Save transaction log
			$func=$this->Func;
			$_date=date($date_today.' H:i:s');
			$action_performed=$this->Auth->User('name').' directly deposited '.$amount.' '.$currency_description.' into the safe on '.(date('M d Y h:i:sa',strtotime($_date)));
			
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
			
		}else{
			$this->Session->setFlash(__('Failed to deposit into safe. Please try again.'),'flash_warning');
		}	
		
		$this->redirect(array('action'=>'view',$this->Auth->User('id')));
	}
	
	//Done by super_admin
	public function safe_withdraw($currency_id,$amount){
		
		$amount=(int)$amount;
		$date_today	=($_REQUEST['date_today']);
		
		if($amount<1){
			$this->Session->setFlash(__('Amount should be greater than 0'),'flash_warning');
			$this->redirect(array('action'=>'view',$this->Auth->User('id')));
		}
		
		$my_field='';//field in which the amount is stored.
		$currency_description='';
		if($currency_id!='ugx'){
			if (!$this->Currency->exists($currency_id)) {
				$this->Session->setFlash(__('Currency not found'),'flash_warning');
				$this->redirect(array('action'=>'view',$this->Auth->User('id')));
			}
			$my_field=$currency_id.'a';
			
			$_currency=$this->Currency->find('first',array(
				'recursive'=>-1,
				'conditions'=>array(
					'Currency.id'=>$currency_id
				)
			));
			$currency_description=$_currency['Currency']['description'];
		}else{
			$currency_id = 'UGX';
			$my_field='opening_ugx';
			$currency_description='UGX';
		}
		
		$safe=$this->Safe->find('first',array(
			'conditions'=>array(
				'Safe.id'=>1111111111
			),
			'fields'=>array(
				'Safe.'.$my_field,'Safe.id'
			)
		));
		
		if(!count($safe)){
			$this->Safe->query("INSERT INTO `saves` (`id`, `opening_ugx`, `c1a`, `c2a`, `c3a`, `c4a`, `c5a`, `c6a`, `c7a`, `c8a`, `other_currencies`) VALUES ('1111111111', '0', '0', '0', '0', '0', '0', '0', '0', '0', NULL);");
			$safe=$this->Safe->find('first',array(
				'conditions'=>array('Safe.id'=>1111111111),
				'fields'=>array('Safe.'.$my_field,'Safe.id')
			));
			if(!count($safe)){
				$this->Session->setFlash(__('No safe found.'),'flash_warning');
				$this->redirect(array('action'=>'view',$this->Auth->User('id')));
			}
		}
		
		if($safe['Safe'][''.$my_field] < $amount){
			$this->Session->setFlash(__('Insufficient amount in the safe. '.($safe['Safe'][''.$my_field]).' '.$currency_description.' is left.'),'flash_warning');
			$this->redirect(array('action'=>'view',$this->Auth->User('id')));
		}
		
		$this->request->data['Safe']['id']=$safe['Safe']['id'];
		$this->request->data['Safe'][''.$my_field]=($safe['Safe'][''.$my_field]-$amount);
		
		if($this->Safe->save($this->request->data)){
			$this->Session->setFlash(__('Successfully withdrew '.$amount.' '.$currency_description.' from the safe.'),'flash_success');
				
			//Save transaction log
			$func=$this->Func;
			$_date=date($date_today.' H:i:s');
			$action_performed=$this->Auth->User('name').' directly withdrew '.$amount.' '.$currency_description.' from the safe on '.(date('M d Y h:i:sa',strtotime($_date)));
			
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
		}else{
			$this->Session->setFlash(__('Failed to withdraw from the safe. Please try again.'),'flash_warning');
		}
		$this->redirect(array('action'=>'view',$this->Auth->User('id')));
	}
	
	//Done by super_admin
	public function other_safe_withdraw($currency_id,$amount){
		$amount=(int)$amount;
		$date_today	=($_REQUEST['date_today']);
		
		if($amount<1){
			$this->Session->setFlash(__('Amount should be greater than 0'),'flash_warning');
			$this->redirect(array('action'=>'view',$this->Auth->User('id')));
		}
		
		$my_field='';//field in which the amount is stored.
		$currency_description='';
		if (!$this->OtherCurrency->exists($currency_id)) {
			$this->Session->setFlash(__('Currency not found'),'flash_warning');
			$this->redirect(array('action'=>'view',$this->Auth->User('id')));
		}
		$my_field=$currency_id.'a';
		$currency_description=''.$currency_id;
		
		$other_currencies=$this->OtherCurrency->find('all',array(
			'recursive'=>-1,
			'conditions'=>array(
				'OtherCurrency.id'=>$currency_id
			)
		));
		
		$safe=$this->Safe->find('first',array(
			'conditions'=>array(
				'Safe.id'=>1111111111
			),
			'fields'=>array(
				'Safe.other_currencies','Safe.id'
			)
		));
		
		
		if(!count($safe)){
			$this->Safe->query("INSERT INTO `saves` (`id`, `opening_ugx`, `c1a`, `c2a`, `c3a`, `c4a`, `c5a`, `c6a`, `c7a`, `c8a`, `other_currencies`) VALUES ('1111111111', '0', '0', '0', '0', '0', '0', '0', '0', '0', NULL);");
			$safe=$this->Safe->find('first',array(
				'conditions'=>array('Safe.id'=>1111111111),
				'fields'=>array('Safe.other_currencies','Safe.id')
			));
			if(!count($safe)){
				$this->Session->setFlash(__('No safe found.'),'flash_warning');
				$this->redirect(array('action'=>'view',$this->Auth->User('id')));
			}
		}
		
		$this->request->data['Safe']['id']=$safe['Safe']['id'];
		
		$data=json_decode($safe['Safe']['other_currencies']);
		
		$arr['data']=array();
		
		if(count($data)){
			foreach($other_currencies as $other_currency){
				$_amount=0;
				foreach($data as $_other_currencies){
					foreach($_other_currencies as $_other_currency){	
						if(isset($_other_currency->CID)){
							if($_other_currency->CID==$other_currency['OtherCurrency']['id']){
								$_amount=$_other_currency->CAMOUNT;
							}
						}
					}
				}
				
				if($other_currency['OtherCurrency']['id']==$currency_id){
					if($amount > $_amount){
						$this->Session->setFlash(__('Insufficient amount of '.($currency_id).'. '.($_amount).' left.'),'flash_warning');
						$this->redirect(array('action'=>'view',$this->Auth->User('id')));
					}
					$_amount-=$amount;
				}
				
				array_push($arr['data'],array(
					'CID'=>$currency_id,
					''.($currency_id)=>$currency_id,
					'CAMOUNT'=>$_amount
				));
			}
		}else{
			//If there has not been any amount saved for any other currency
			array_push($arr['data'],array(
				'CID'=>$currency_id,
				''.($currency_id)=>$currency_id,
				'CAMOUNT'=>$amount
			));
		}
		
		$this->request->data['Safe']['other_currencies']=json_encode($arr);
		
		if($this->Safe->save($this->request->data)){
			$this->Session->setFlash(__('Successfully withdrew '.$amount.' '.$currency_description.' from the safe.'),'flash_success');
				
			//Save transaction log
			$func=$this->Func;
			$_date=date($date_today.' H:i:s');
			$action_performed=$this->Auth->User('name').' directly withdrew '.$amount.' '.$currency_description.' from the safe on '.(date('M d Y h:i:sa',strtotime($_date)));
			
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
			
		}else{
			$this->Session->setFlash(__('Failed to withdraw into safe. Please try again.'),'flash_error');
		}	
		
		$this->redirect(array('action'=>'view',$this->Auth->User('id')));
	}
	
	public function add_customers() {
		if ($this->request->is('post')) {
			date_default_timezone_set('Africa/Nairobi');
			$func=$this->Func;
			$this->request->data['User']['role']='customer';
			$this->request->data['User']['username']=$func->getUID1();
			$this->request->data['User']['password']=$func->getUID1();
			$this->request->data['User']['password_confirmation']=$this->request->data['User']['password'];
			$this->request->data['User']['created']=date('Y-m-d H:i:s');
			$this->request->data['User']['date']=date('Y-m-d H:i:s');
			$this->User->create();
			if ($this->User->save($this->request->data)) {
				$this->Session->setFlash(__('The Customer has been saved'),'flash_success');
				$this->redirect(array('action' => 'index','customer'));
			} else {
				$this->Session->setFlash(__('The customer could not be saved. Please, try again.'),'flash_error');
			}
		}
	}
	
	public function pong(){}
	
	public function fox_login(){
		$response['resp_string']='Access Denied...';
		if ($this->Auth->login()) {
			$u=$this->User->find('all',array(
				'limit'=>1,
				'recursive'=>-1,
				'fields'=>array(
					'User.name','User.id'
				),
				'conditions'=>array(
					'username'=>$this->Rest->credentials('username')
				)
			));
			if(count($u))
				$response['resp_string']='OK '.strlen($u[0]['User']['id']).' '.($u[0]['User']['id']).' '.(($u[0]['User']['name']));
		}
		$this->set(compact('response'));
	}
	
	public function login() {
		if ($this->request->is('post')) {
			if ($this->Auth->login()) {
				$fox=$this->Fox->find('first');
				$this->Session->write('fox',$fox);
				
				date_default_timezone_set('Africa/Nairobi');
				
				//Date validation
				$ts1 = strtotime(date('Y-m-d'));//today's system date
				$ts2 = strtotime($fox['Fox']['prev_d']);
				$seconds_diff = $ts2 - $ts1;
				if($seconds_diff>0){
					$this->Session->setFlash(__('Invalid system date. Correct it to continue. Thanks.'),'flash_warning');
					$this->redirect($this->Auth->logout());
				}
				
				//Validate for weekends
				$weekends=explode(',',$fox['Fox']['weekends']);
				foreach($weekends as $weekend){
					if($ts1==strtotime($weekend)){
						$this->Session->setFlash(__('Its a weekend.'),'flash_warning');
						$this->redirect($this->Auth->logout());
					}
				}
				
				//update current date
				$this->Fox->id=$fox['Fox']['id'];
				$this->Fox->set('prev_d',date('Y-m-d'));
				$this->Fox->save();
				
				//$this->User->Notification->msg(AuthComponent::user('id'), "You logged in!");
				$this->redirect($this->Auth->redirect());
			} else {
				$this->Session->setFlash(__('Invalid username or password, try again'),'flash_warning');
			}
		}
	}
	
	public function logout() {
		$this->redirect($this->Auth->logout());
	}
	
	public function reset_password(){
	
	}
	
	public function register(){
	
	}
	
	public function settings($id = null) {
		if (!$this->User->exists($id)) {
			throw new NotFoundException(__('Invalid user'));
		}
		
		if($id!=$this->Auth->User('id') and $this->Auth->User('role')!='super_admin'){
        	$this->Session->setFlash(__('Invalid request', true),'flash_warning');
            $this->redirect(array('action' => 'view',$this->Auth->User('id')));
        }
		
		$this->set('user', $this->User->read(null,$id));
		
		if (empty($this->data)) {
            	$this->data = $this->User->read(null, $id);
        }
	}
	
/**
 * index method
 *
 * @return void
 */
	public function index($role=null) {
		$this->set('userrole',$role);
		if($role){
			$this->User->virtualFields=array(
				'total_credited'=>0,
				'total_debited'=>0,
				'total_deposited'=>0,
				'total_withdrawn'=>0,
			);
			$this->paginate=array(
				'conditions'=>array(
					'User.role'=>'customer'
				),
				'fields'=>array(
					'User.id','User.name','User.username','User.email','User.role','User.id as the_id',
					'(select SUM(`amount`) as total_credit from `creditors` where `customer_id`=the_id) as User__total_credited',
					'(select SUM(`amount`) as total_debt from `debtors` where `customer_id`=the_id) as User__total_debited',
					'(select SUM(`amount`) as total_deposit from `receivables` where `customer_id`=the_id) as User__total_deposited',
					'(select SUM(`amount`) as total_withdraw from `withdrawals` where `customer_id`=the_id) as User__total_withdrawn',
//					'User__total_debit','User__total_withdraw','User__total_deposit'
				)
			);
		}else{
			$this->paginate=array(
				'conditions'=>array(
					'User.role'=>array('super_admin','regular')
				)
			);
		}
		$this->User->recursive = 0;
		$this->set('users', $this->paginate());
		$this->set('the_role',$role);
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	
	public function transfer($id){
		if (!$this->User->exists($id)) {
			throw new NotFoundException(__('Invalid user'));
		}
		
		if($this->request->is('post')){
			$withdrawer = $this->User->find('first',array(
				'conditions'=>array(
					'User.id'=>$id
				)
			));
			
			$receiver = $this->User->find('first',array(
				'conditions'=>array(
					'User.id'=>$this->request->data['User']['user_id']
				)
			));
			
			//Receivable/Deposit
			if(!$this->Receivable->save(
				array(
					'Receivable'=>array(
						'customer'=>$receiver['User']['name'],
						'customer_id'=>$receiver['User']['id'],
						'amount'=>$this->request->data['User']['amount'],
						'date'=>date('Y-m-d'),
						'additional_info'=>'Transfer from '.$withdrawer['User']['name'].' to '.$receiver['User']['name'],
						'user_id'=>$this->Auth->User('id')
					)
				)
			)){
				$this->Session->setFlash(__('Failed Receivable'),'flash_error');
				$this->redirect(array('action' => 'view',$id));
			}
			
			//Withdraw
			if(!$this->Withdrawal->save(
				array(
					'Withdrawal'=>array(
						'customer'=>$withdrawer['User']['name'],
						'customer_id'=>$withdrawer['User']['id'],
						'amount'=>$this->request->data['User']['amount'],
						'date'=>date('Y-m-d'),
						'additional_info'=>'Transfer from '.$withdrawer['User']['name'].' to '.$receiver['User']['name'],
						'user_id'=>$this->Auth->User('id')
					)
				)
			)){
				$this->Session->setFlash(__('Failed Withdrawal'),'flash_error');
				$this->redirect(array('action' => 'view',$id));
			}
			
			$this->Session->setFlash(__('Transfered'),'flash_success');
			$this->redirect(array('action' => 'view',$id));
		}
		
		$users = $this->User->find('all',array(
			'limit'=>1000,
			'conditions'=>array(
				'User.role'=>'customer',
				'NOT'=>array(
					'User.id'=>$id
				)
			)
		));
		$this->set(compact('users'));
	}
	
	public function view($id = null,$dateRage=null){
		if (!$this->User->exists($id)) {
			throw new NotFoundException(__('Invalid user'));
		}
		
		$extraFilterReceivables = [];
		$extraFilterWithdrawals = [];
		if(	isset($_REQUEST['date_from']) &&
			isset($_REQUEST['date_to']) &&
			$dateRage
			){
			$from	=($_REQUEST['date_from']);
			$to	=($_REQUEST['date_to']);
			
			$extraFilterReceivables['Receivable.date >='] = $from;
			$extraFilterReceivables['Receivable.date <='] = $to;
			
			$extraFilterWithdrawals['Withdrawal.date >='] = $from;
			$extraFilterWithdrawals['Withdrawal.date <='] = $to;
		}
		
		if($id!=$this->Auth->User('id') and $this->Auth->User('role')!='super_admin' and $this->Auth->User('role')!='regular'){
        	$this->Session->setFlash(__('Invalid request', true),'flash_warning');
            $this->redirect(array('action' => 'view',$this->Auth->User('id')));
        }
		$user = $this->User->read(null, $id);
		$this->set('user', $user);
		
		if (empty($this->data)) {
            $this->data = $user;
        }
		
		
		$debt=$this->Debtor->find('all',array(
			'conditions'=>array(
				'Debtor.customer_id'=>$id
			),
			'recursive'=>-1,
			'fields'=>array(
				'SUM(Debtor.amount) as total_amount'
			)
		));
		$receivable=$this->Receivable->find('all',array(
			'conditions'=>array(
				'Receivable.customer_id'=>$id,
				$extraFilterReceivables
			),
			'recursive'=>-1,
			'fields'=>array(
				'SUM(Receivable.amount) as total_amount'
			),
			'group'=>'Receivable.reason'
		));
		
		$credit=$this->Creditor->find('all',array(
			'conditions'=>array(
				'Creditor.customer_id'=>$id
			),
			'recursive'=>-1,
			'fields'=>array(
				'SUM(Creditor.amount) as total_amount'
			)
		));
		
		$withdrawal=$this->Withdrawal->find('all',array(
			'conditions'=>array(
				'Withdrawal.customer_id'=>$id,
				$extraFilterWithdrawals
			),
			'recursive'=>-1,
			'fields'=>array(
				'SUM(Withdrawal.amount) as total_amount',
				'Withdrawal.reason'
			),
			'group'=>['Withdrawal.reason']
		));
		
		//echo '<pre>';
		//print_r($withdrawal);
		//exit;
		
		/*$currencies=$this->Currency->find('all',array(
			'recursive'=>-1,
			'conditions'=>array(
				'NOT'=>array(
					'Currency.id'=>'c00'
				)
			)
		));*/

		$currencies = $this->Currency->find('all',[
                'limit'=>0,
                'recursive'=>-1,
                'conditions'=>[
                    'NOT'=>[
                        'id'=>['c00','c8']
                    ],
                    'is_other_currency'=>0
                ],
                'order'=>'Currency.is_other_currency ASC,Currency.arrangement ASC, Currency.id ASC',
        ]);

		//Include UGX opening
		array_unshift($currencies,array(
				'Currency'=>array(
					'id'=>'ugx',
					'description'=>'UGX',
					'is_other_currency'=>0
				)
			)
		);

		$otherCurrencies = $this->Currency->find('all',[
            'limit'=>0,
            'recursive'=>-1,
            'conditions'=>[
                'is_other_currency'=>1
            ],
            'order'=>'id ASC'
        ]);

        // $otherCurrencies = [];
		
		$users=$this->User->find('all',array(
			'recursive'=>-1,
			'conditions'=>array(
				'User.role'=>'regular',
				'NOT'=>array(
					'User.id'=>$this->Auth->User('id')
				)
			)
		));
		$opening=array();
		if(isset($_REQUEST['date_today'])){
			$date_today	=($_REQUEST['date_today']);
			//Today's opening
			$opening=$this->Opening->find('first',array(
				'conditions'=>array(
					'Opening.status'=>0,
					'Opening.date'=>$date_today,
					'Opening.user_id'=>$id
				),
				'recursive'=>-1
			));
			
			//Next opening
			$next_opening=$this->Opening->find('first',array(
				'conditions'=>array(
					//'Opening.status'=>0,
					'Opening.date >'=>$date_today,
					'Opening.user_id'=>$id
				),
				'recursive'=>-1,
				'order'=>'Opening.date asc'
			));
		}
		
		$safe=$this->Safe->find('first');
		$this->set(compact('date_today','credit','debt','receivable','withdrawal','currencies','otherCurrencies','users','opening','next_opening','safe'));
		if($user['User']['is_bank']){
			$this->render('bank_view');
		}
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			date_default_timezone_set('Africa/Nairobi');
			$this->request->data['User']['date']=date('Y-m-d H:i:s');
			$this->User->create();			
			if ($this->User->save($this->request->data)) {
				$this->Session->setFlash(__('The user has been saved'),'flash_success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The user could not be saved. Please, try again.'),'flash_error');
			}
		}
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit_customers($id = null) {
		if (!$this->User->exists($id)) {
			throw new NotFoundException(__('Invalid user'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->User->save($this->request->data)) {
				$this->Session->setFlash(__('The user has been saved'),'flash_success');
				$this->redirect(array('action' => 'index','customer'));
			} else {
				$this->Session->setFlash(__('The user could not be saved. Please, try again.'),'flash_warning');
				$this->redirect(array('action' => 'index','customer'));
			}
		} else {
			$options = array('conditions' => array('User.' . $this->User->primaryKey => $id));
			$this->request->data = $this->User->find('first', $options);
		}
	}
	
	public function edit($id = null,$role=null) {
		if (!$this->User->exists($id)) {
			throw new NotFoundException(__('Invalid user'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->User->save($this->request->data)) {
				if(!empty($this->request->data['User']['is_safe'])){
					$is_safes = $this->User->find('count',[
						'conditions'=>[
							'User.is_safe'=>1
						]
					]);
					$user_id = $this->request->data['User']['id'];
					if ($is_safes>1) {
						$this->Session->setFlash(__('Saved, Other "Safe" accounts have been unmarked'),'flash_warning');
						$this->User->query("UPDATE users set is_safe=0 where id NOT IN ('$user_id')");
					}else{
						$this->Session->setFlash(__('The user has been saved'),'flash_success');
					}
				}else{
					$this->Session->setFlash(__('The user has been saved'),'flash_success');
				}

				if(!empty($this->request->data['User']['password']))
				{
					$this->User->save([
						'User'=>[
							'id'=>$id,
							'password_last_changed_on'=>date('Y-m-d')
						]
					]);
				}
				
				$this->Session->write('Auth', $this->User->read(null, $this->Auth->User('id')));
				$this->redirect(array('action' => 'settings',$id));
			} else {
				$this->Session->setFlash(__('The user could not be saved. Please, try again.'),'flash_warning');
				$this->redirect(array('action' => 'settings'));
			}
		} else {
			$options = array('conditions' => array('User.' . $this->User->primaryKey => $id));
			$this->request->data = $this->User->find('first', $options);
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
		$this->User->id = $id;
		if (!$this->User->exists()) {
			throw new NotFoundException(__('Invalid user'));
		}
		
		if($this->Auth->user('role')!='super_admin'){
			$this->request->onlyAllow('post', 'delete');
		}
		
		if ($this->User->delete()) {
			$this->Session->setFlash(__('User deleted'),'flash_success');
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('User was not deleted'),'flash_warning');
		$this->redirect(array('action' => 'index'));
	}
	
	//Currently works for only user accounts where is_bank==1
	public function transaction_summary($customer_id,$page=1)
	{
		$customer = $this->Withdrawal->Customer->find('first',['conditions'=>['Customer.id'=>$customer_id],'recursive'=>-1]);
		$this->set('customer',$customer);
		
		$limit = 50;
		$page = $page;
		$transactions = [];
		$dateFilter = '';
		if(	isset($_REQUEST['date_from']) &&
			isset($_REQUEST['date_to'])){
			$from	=($_REQUEST['date_from']);
			$to	=($_REQUEST['date_to']);
			
			$this->set('from', $from);
			$this->set('to', $to);
			
			$dateFilter = "AND DATE(`date`) >= '".$from."' AND DATE(`date`) <= '".$to."'";
		}
		
		if($customer['Customer']['is_bank']){
			$transactions = $this->User->query("
				SELECT 'receivables' as my_table_name,
					`id`,`customer`,`customer_id`,`amount`,`additional_info`,`date`,`user_id`,`reason` 
				FROM `receivables` where `customer_id`='".$customer_id."' ".$dateFilter."
				UNION ALL
				SELECT 'withdrawals' as my_table_name,
					`id`,`customer`,`customer_id`,`amount`,`additional_info`,`date`,`user_id`,`reason` 
				FROM `withdrawals` where `customer_id`='".$customer_id."' ".$dateFilter."
				ORDER BY date DESC
				LIMIT ".$limit."
				OFFSET ".$limit * ($page-1)."
			");
		}else{
			$transactions = $this->User->query("
				SELECT 'receivables' as my_table_name,
					`id`,`customer`,`customer_id`,`amount`,`additional_info`,`date`,`user_id`, 'Credit' AS reason 
				FROM `receivables` where `customer_id`='".$customer_id."' ".$dateFilter."
				UNION ALL
				SELECT 'withdrawals' as my_table_name,
					`id`,`customer`,`customer_id`,`amount`,`additional_info`,`date`,`user_id`,'Debt' as reason 
				FROM `withdrawals` where `customer_id`='".$customer_id."' ".$dateFilter."
				ORDER BY date DESC
				LIMIT ".$limit."
				OFFSET ".$limit * ($page-1)."
			");
		}
		
		$this->set('transactions',$transactions);
		$this->set('limit',$limit);
		$this->set('page',$page);
		$this->set('customer_id', $customer_id);
	}
}
