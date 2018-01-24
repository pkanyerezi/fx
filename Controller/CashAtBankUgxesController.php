<?php
App::uses('AppController', 'Controller');
class CashAtBankUgxesController extends AppController {
	function beforeFilter() {
        parent::beforeFilter();		
        if($this->Auth->user('role')!='super_admin'){
			$this->Session->setFlash(__('Access Denied!!'),'flash_error');
			$this->redirect($this->Auth->logout());
		}
    } 

	public function index($type=null,$bank_id=null) {
		$this->CashAtBankUgx->recursive = 0;
		$this->paginate=array('order'=>'CashAtBankUgx.date desc');
		if(	isset($_REQUEST['date_from']) &&
			isset($_REQUEST['date_to'])){
			$from	=($_REQUEST['date_from']);
			$to	=($_REQUEST['date_to']);
			
			$this->set('from', $from);
			$this->set('to', $to);
			
			if($type=='deposit'){
				$this->paginate=array(
					'conditions'=>array(
						'CashAtBankUgx.date >='=>$from,
						'CashAtBankUgx.date <='=>$to,
						'CashAtBankUgx.amount >'=>0
					),
					'order'=>'CashAtBankUgx.date desc'
				);
				
				if($bank_id){
					$this->paginate=array(
					'conditions'=>array(
							'CashAtBankUgx.date >='=>$from,
							'CashAtBankUgx.date <='=>$to,
							'CashAtBankUgx.amount >'=>0,
							'CashAtBankUgx.bank_id'=>$bank_id,
						),
						'order'=>'CashAtBankUgx.date desc'
					);
				}
			}else{//Withdraw
				$this->paginate=array(
					'conditions'=>array(
						'CashAtBankUgx.date >='=>$from,
						'CashAtBankUgx.date <='=>$to,
						'CashAtBankUgx.amount <'=>0
					),
					'order'=>'CashAtBankUgx.date desc'
				);	
				
				if($bank_id){
					$this->paginate=array(
						'conditions'=>array(
							'CashAtBankUgx.date >='=>$from,
							'CashAtBankUgx.date <='=>$to,
							'CashAtBankUgx.amount <'=>0,
							'CashAtBankUgx.bank_id'=>$bank_id,
						),
						'order'=>'CashAtBankUgx.date desc'
					);
				}
						
			}
		}
		$this->set('cashAtBankUgxes', $this->paginate());
		$this->set('type',$type);
	}

	public function view($id = null) {
		if (!$this->CashAtBankUgx->exists($id)) {
			throw new NotFoundException(__('Invalid cash at bank ugx'));
		}
		$options = array('conditions' => array('CashAtBankUgx.' . $this->CashAtBankUgx->primaryKey => $id));
		$this->set('cashAtBankUgx', $this->CashAtBankUgx->find('first', $options));
	}
	
	//The amount should be +ve for system compatibility
	public function deposited_to_bank() {
		if ($this->request->is('post')) {
			if($this->Auth->User('role')!='super_admin'){
				$this->request->data['CashAtBankUgx']['user_id']=$this->Auth->User('id');
			}
			$this->CashAtBankUgx->create();
			 
			if ($this->CashAtBankUgx->save($this->request->data)) {
				$this->Session->setFlash(__('The ugx cash deposited to bank has been saved'),'flash_success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The ugx cash deposited to bank counld not be saved. Please, try again.'),'flash_error');
			}
		}
		$users = $this->CashAtBankUgx->User->find('list',array(
			'conditions'=>array(
				'NOT'=>array(
					'User.role'=>array(
						'super_admin','customer'
					)
				)
			),
			'recursive'=>-1
		));
		$banks = $this->CashAtBankUgx->Bank->find('list');
		$this->set(compact('users','banks'));
	}

	//The amount here should be -ve for system compaitbility
	public function withdrawn_from_bank() {
		if ($this->request->is('post')) {
			if($this->Auth->User('role')!='super_admin'){
				$this->request->data['CashAtBankUgx']['user_id']=$this->Auth->User('id');
			}
			$this->CashAtBankUgx->create();
			$this->request->data['CashAtBankUgx']['amount'] =$this->request->data['CashAtBankUgx']['amount'] * -1;
			if ($this->CashAtBankUgx->save($this->request->data)) {
				$this->Session->setFlash(__('The ugx cash withdrawn from bank ugx has been saved'),'flash_success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The ugx cash withdrawn could not be saved. Please, try again.'),'flash_error');
			}
		}
		$users = $this->CashAtBankUgx->User->find('list',array(
			'conditions'=>array(
				'NOT'=>array(
					'User.role'=>array(
						'super_admin','customer'
					)
				)
			),
			'recursive'=>-1
		));
		$banks = $this->CashAtBankUgx->Bank->find('list');
		$this->set(compact('users','banks'));
	}

	public function edit($id = null) {
		if (!$this->CashAtBankUgx->exists($id)) {
			throw new NotFoundException(__('Invalid cash at bank ugx'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if($this->Auth->User('role')!='super_admin'){
				$this->request->data['CashAtBankUgx']['user_id']=$this->Auth->User('id');
			}
			if ($this->CashAtBankUgx->save($this->request->data)) {
				$this->Session->setFlash(__('The cash at bank ugx has been saved'),'flash_success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The cash at bank ugx could not be saved. Please, try again.'),'flash_error');
			}
		} else {
			$options = array('conditions' => array('CashAtBankUgx.' . $this->CashAtBankUgx->primaryKey => $id));
			$this->request->data = $this->CashAtBankUgx->find('first', $options);
		}
		$users = $this->CashAtBankUgx->User->find('list',array(
			'conditions'=>array(
				'NOT'=>array(
					'User.role'=>array(
						'super_admin','customer'
					)
				)
			),
			'recursive'=>-1
		));
		$banks = $this->CashAtBankUgx->Bank->find('list');
		$this->set(compact('users','banks'));
	}

	public function delete($id = null) {
		$this->CashAtBankUgx->id = $id;
		if (!$this->CashAtBankUgx->exists()) {
			throw new NotFoundException(__('Invalid cash at bank ugx'));
		}
		if ($this->CashAtBankUgx->delete()) {
			$this->Session->setFlash(__('Cash at bank ugx deleted'),'flash_success');
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Cash at bank ugx was not deleted'),'flash_error');
		$this->redirect(array('action' => 'index'));
	}
}
