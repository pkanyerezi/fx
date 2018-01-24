<?php
App::uses('AppController', 'Controller');
/**
 * Withdrawals Controller
 *
 * @property Withdrawal $Withdrawal
 */
class WithdrawalsController extends AppController {
	function beforeFilter() {
        parent::beforeFilter();		
        /*if($this->Auth->user('role')!='super_admin'){
			$this->Session->setFlash(__('Access Denied!!'),'flash_error');
			$this->redirect($this->Auth->logout());
		}*/
    } 
/**
 * index method
 *
 * @return void
 */
	public function index($customer_id=null){
		$this->Withdrawal->recursive = 0;
		$this->paginate=array('order'=>'Withdrawal.date desc');
		if(	isset($_REQUEST['date_from']) &&
			isset($_REQUEST['date_to'])){
			$from	=($_REQUEST['date_from']);
			$to	=($_REQUEST['date_to']);
			
			$this->set('from', $from);
			$this->set('to', $to);
			
			if($customer_id){
				$this->paginate=array(
					'conditions'=>array(
						'Withdrawal.customer_id'=>$customer_id
					),
					'order'=>'Withdrawal.date desc',
					'limit'=>200
				);
			}else{
				$this->paginate=array(
					'conditions'=>array(
						'Withdrawal.date >='=>$from,
						'Withdrawal.date <='=>$to
					),
					'order'=>'Withdrawal.date desc',
					'limit'=>200
				);
			}
		}else{
			$this->Session->setFlash(__('The withdrawal could not be saved. Please, try again.'),'flash_error');
		}
		
		if($customer_id)
		{	
			$this->set('customer', $this->Withdrawal->Customer->find('first',['conditions'=>['Customer.id'=>$customer_id],'recursive'=>-1]));
			$this->set('customer_id', $customer_id);
		}
		
		$this->set('withdrawals', $this->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->Withdrawal->exists($id)) {
			throw new NotFoundException(__('Invalid withdrawal'));
		}
		$options = array('conditions' => array('Withdrawal.' . $this->Withdrawal->primaryKey => $id));
		$this->set('withdrawal', $this->Withdrawal->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function add($customer_id=null) {
		if ($this->request->is('post')) {
			if($this->Auth->User('role')!='super_admin'){
				$this->request->data['Withdrawal']['user_id']=$this->Auth->User('id');
			}
			$customer=$this->Withdrawal->Customer->find('first',array(
				'conditions'=>array(
					'Customer.id'=>$this->request->data['Withdrawal']['customer_id']
				)
			));
			$this->request->data['Withdrawal']['customer']=$customer['Customer']['name'];
			$this->Withdrawal->create();
			if ($this->Withdrawal->save($this->request->data)) {
				$this->Session->setFlash(__('The withdrawal has been saved'),'flash_success');
				$this->redirect(array('action' => 'index',$this->request->data['Withdrawal']['customer_id']));
			} else {
				$this->Session->setFlash(__('The withdrawal could not be saved. Please, try again.'),'flash_error');
			}
		}
		$users = $this->Withdrawal->User->find('list',array(
			'conditions'=>array(
				'NOT'=>array(
					'User.role'=>array(
						'super_admin','customer'
					)
				)
			),
			'recursive'=>-1
		));
		
		if($customer_id){
			$customers=$this->Withdrawal->Customer->find('list',array(
				'conditions'=>array(
					'Customer.role'=>'customer',
					'Customer.id'=>$customer_id,
				)
			));
			$customer_details = $this->Withdrawal->Customer->find('first',['conditions'=>['Customer.id'=>$customer_id]]);
		}else{
			$customers=$this->Withdrawal->Customer->find('list',array(
				'conditions'=>array(
					'Customer.role'=>'customer'
				)
			));
			$customer_details = ['Customer'=>['name'=>'Customer','is_bank'=>0]];
		}
		$this->set(compact('users','customers','customer_details'));
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		$withdrawal = $this->Withdrawal->find('first',['conditions'=>['Withdrawal.id'=>$id]]);
		if (empty($withdrawal)) {
			throw new NotFoundException(__('Invalid withdrawal'));
		}
		
		$customer_id = $withdrawal['Withdrawal']['customer_id'];
		
		if ($this->request->is('post') || $this->request->is('put')) {
			if($this->Auth->User('role')!='super_admin'){
				$this->request->data['Withdrawal']['user_id']=$this->Auth->User('id');
			}
			$customer=$this->Withdrawal->Customer->find('first',array(
				'conditions'=>array(
					'Customer.id'=>$this->request->data['Withdrawal']['customer_id']
				)
			));
			$this->request->data['Withdrawal']['customer']=$customer['Customer']['name'];
			if ($this->Withdrawal->save($this->request->data)) {
				$this->Session->setFlash(__('Saved'),'flash_success');
				$this->redirect(array('action' => 'index',$customer_id));
			} else {
				$this->Session->setFlash(__('Error while saving. Please, try again.'),'flash_error');
			}
		} else {
			$options = array('conditions' => array('Withdrawal.' . $this->Withdrawal->primaryKey => $id));
			$this->request->data = $this->Withdrawal->find('first', $options);
		}
		$users = $this->Withdrawal->User->find('list',array(
			'conditions'=>array(
				'NOT'=>array(
					'User.role'=>array(
						'super_admin','customer'
					)
				)
			),
			'recursive'=>-1
		));
		
		if($customer_id){
			$customers=$this->Withdrawal->Customer->find('list',array(
				'conditions'=>array(
					'Customer.role'=>'customer',
					'Customer.id'=>$customer_id,
				)
			));
			$customer_details = $this->Withdrawal->Customer->find('first',['conditions'=>['Customer.id'=>$customer_id]]);
		}else{
			$customers=$this->Withdrawal->Customer->find('list',array(
				'conditions'=>array(
					'Customer.role'=>'customer'
				)
			));
			$customer_details = ['Customer'=>['name'=>'Customer','is_bank'=>0]];
		}
		$this->set(compact('users','customers','customer_details'));
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
		$this->Withdrawal->id = $id;
		if (!$this->Withdrawal->exists()) {
			throw new NotFoundException(__('Invalid withdrawal'));
		}
		if ($this->Withdrawal->delete()) {
			$this->Session->setFlash(__('Withdrawal deleted'),'flash_success');
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Withdrawal was not deleted'),'flash_error');
		$this->redirect(array('action' => 'index'));
	}
}
