<?php
App::uses('AppController', 'Controller');
/**
 * CashAtBankForeigns Controller
 *
 * @property CashAtBankForeign $CashAtBankForeign
 */
class CashAtBankForeignsController extends AppController {
	
	function beforeFilter() {
        parent::beforeFilter();		
        if($this->Auth->user('role')!='super_admin'){
			$this->Session->setFlash(__('Access Denied!!'),'flash_error');
			$this->redirect($this->Auth->logout());
		}
    } 

	public function index($type=null,$bank_id=null) {
		$this->CashAtBankForeign->recursive = 0;
		$this->paginate=array('order'=>'CashAtBankForeign.date desc');
		if(	isset($_REQUEST['date_from']) &&
			isset($_REQUEST['date_to'])){
			$from	=($_REQUEST['date_from']);
			$to	=($_REQUEST['date_to']);
			
			$this->set('from', $from);
			$this->set('to', $to);
			
			if($type=='deposite'){
				$this->paginate=array(
					'conditions'=>array(
						'CashAtBankForeign.date >='=>$from,
						'CashAtBankForeign.date <='=>$to,
						'CashAtBankForeign.amount >'=>0
					),
					'order'=>'CashAtBankForeign.date desc'
				);
				if($bank_id){
					$this->paginate=array(
						'conditions'=>array(
							'CashAtBankForeign.date >='=>$from,
							'CashAtBankForeign.date <='=>$to,
							'CashAtBankForeign.amount >'=>0,
							'CashAtBankForeign.bank_id'=>$bank_id,
						),
						'order'=>'CashAtBankForeign.date desc'
					);	
				}	
			}else{//Withdraw
				$this->paginate=array(
					'conditions'=>array(
						'CashAtBankForeign.date >='=>$from,
						'CashAtBankForeign.date <='=>$to,
						'CashAtBankForeign.amount <'=>0
					),
					'order'=>'CashAtBankForeign.date desc'
				);		
				if($bank_id){
					$this->paginate=array(
						'conditions'=>array(
							'CashAtBankForeign.date >='=>$from,
							'CashAtBankForeign.date <='=>$to,
							'CashAtBankForeign.amount <'=>0,
							'CashAtBankForeign.bank_id'=>$bank_id,
						),
						'order'=>'CashAtBankForeign.date desc'
					);	
				}	
			}
		}
		$this->set('cashAtBankForeigns', $this->paginate());
		$this->set('type',$type);
	}

	public function view($id = null) {
		if (!$this->CashAtBankForeign->exists($id)) {
			throw new NotFoundException(__('Invalid cash at bank foreign'));
		}
		$options = array('conditions' => array('CashAtBankForeign.' . $this->CashAtBankForeign->primaryKey => $id));
		$this->set('cashAtBankForeign', $this->CashAtBankForeign->find('first', $options));
	}

	//The amount should be +ve for system compatibility
	public function deposited_to_bank() {
		if ($this->request->is('post')) {
			if($this->Auth->User('role')!='super_admin'){
				$this->request->data['CashAtBankForeign']['user_id']=$this->Auth->User('id');
			}
			
			$this->CashAtBankForeign->create();
			if ($this->CashAtBankForeign->save($this->request->data)) {
				$this->Session->setFlash(__('The cash at bank foreign has been saved'),'flash_success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The cash at bank foreign could not be saved. Please, try again.'),'flash_error');
			}
		}
		$users = $this->CashAtBankForeign->User->find('list',array(
			'conditions'=>array(
				'NOT'=>array(
					'User.role'=>array(
						'super_admin','customer'
					)
				)
			),
			'recursive'=>-1
		));
		$currencies = $this->CashAtBankForeign->Currency->find('list');
		$banks = $this->CashAtBankForeign->Bank->find('list');
		$this->set(compact('users','currencies','banks'));
	}
	
	//The amount here should be -ve for system compaitbility
	public function withdrawn_from_bank() {
		if ($this->request->is('post')) {
			if($this->Auth->User('role')!='super_admin'){
				$this->request->data['CashAtBankForeign']['user_id']=$this->Auth->User('id');
			}
			
			$this->CashAtBankForeign->create();
			$this->request->data['CashAtBankForeign']['amount'] =-1 * $this->request->data['CashAtBankForeign']['amount'];
			if ($this->CashAtBankForeign->save($this->request->data)) {
				$this->Session->setFlash(__('The cash at bank foreign has been saved'),'flash_success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The cash at bank foreign could not be saved. Please, try again.'),'flash_error');
			}
		}
		$users = $this->CashAtBankForeign->User->find('list',array(
			'conditions'=>array(
				'NOT'=>array(
					'User.role'=>array(
						'super_admin','customer'
					)
				)
			),
			'recursive'=>-1
		));
		$currencies = $this->CashAtBankForeign->Currency->find('list');
		$banks = $this->CashAtBankForeign->Bank->find('list');
		$this->set(compact('users','currencies','banks'));
	}

	public function edit($id = null) {
		if (!$this->CashAtBankForeign->exists($id)) {
			throw new NotFoundException(__('Invalid cash at bank foreign'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if($this->Auth->User('role')!='super_admin'){
				$this->request->data['CashAtBankForeign']['user_id']=$this->Auth->User('id');
			}
			if ($this->CashAtBankForeign->save($this->request->data)) {
				$this->Session->setFlash(__('The cash at bank foreign has been saved'),'flash_success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The cash at bank foreign could not be saved. Please, try again.'),'flash_error');
			}
		} else {
			$options = array('conditions' => array('CashAtBankForeign.' . $this->CashAtBankForeign->primaryKey => $id));
			$this->request->data = $this->CashAtBankForeign->find('first', $options);
		}
		$users = $this->CashAtBankForeign->User->find('list',array(
			'conditions'=>array(
				'NOT'=>array(
					'User.role'=>array(
						'super_admin','customer'
					)
				)
			),
			'recursive'=>-1
		));
		$currencies = $this->CashAtBankForeign->Currency->find('list');
		$this->set(compact('users','currencies'));
	}

	public function delete($id = null) {
		$this->CashAtBankForeign->id = $id;
		if (!$this->CashAtBankForeign->exists()) {
			throw new NotFoundException(__('Invalid cash at bank foreign'));
		}
		if ($this->CashAtBankForeign->delete()) {
			$this->Session->setFlash(__('Cash at bank foreign deleted'),'flash_success');
			$this->redirect(array('controller' => 'dashboards','action'=>'index'));
		}
		$this->Session->setFlash(__('Cash at bank foreign was not deleted'),'flash_error');
		$this->redirect(array('action' => 'index'));
	}
}
