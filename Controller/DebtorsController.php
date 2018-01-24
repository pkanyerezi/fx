<?php
App::uses('AppController', 'Controller');
/**
 * Debtors Controller
 *
 * @property Debtor $Debtor
 */
class DebtorsController extends AppController {

	function beforeFilter() {
        parent::beforeFilter();		
        /* if($this->Auth->user('role')!='super_admin'){
			$this->Session->setFlash(__('Access Denied!!'));
			$this->redirect($this->Auth->logout());
		} */
    } 
/**
 * index method
 *
 * @return void
 */
	public function index($customer_id=null) {
		$this->Debtor->recursive = 0;
		$this->paginate=array('Debtor.order'=>'date desc');
		if(	isset($_REQUEST['date_from']) &&
			isset($_REQUEST['date_to'])){
			$from	=($_REQUEST['date_from']);
			$to	=($_REQUEST['date_to']);
			
			$this->set('from', $from);
			$this->set('to', $to);
			
			if($customer_id){
				$this->paginate=array(
					'conditions'=>array(
						'Debtor.customer_id'=>$customer_id
					),
					'order'=>'Debtor.date desc',
					'limit'=>200
				);
			}else{
				$this->paginate=array(
					'conditions'=>array(
						'Debtor.date >='=>$from,
						'Debtor.date <='=>$to
					),
					'order'=>'Debtor.date desc',
					'limit'=>200
				);
			}
		}
		
		if($customer_id)	$this->set('customer_id', $customer_id);
		
		$this->set('debtors', $this->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->Debtor->exists($id)) {
			throw new NotFoundException(__('Invalid debtor'));
		}
		$options = array('conditions' => array('Debtor.' . $this->Debtor->primaryKey => $id));
		$this->set('debtor', $this->Debtor->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function add($customer_id=null) {
		if ($this->request->is('post')) {
			if($this->Auth->User('role')!='super_admin'){
				$this->request->data['Debtor']['user_id']=$this->Auth->User('id');
			}
			
			$customer=$this->Debtor->Customer->find('first',array(
				'conditions'=>array(
					'Customer.id'=>$this->request->data['Debtor']['customer_id']
				)
			));
			$this->request->data['Debtor']['customer']=$customer['Customer']['name'];
			$this->Debtor->create();
			if ($this->Debtor->save($this->request->data)) {
				$this->Session->setFlash(__('The debtor has been saved'),'flash_success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The debtor could not be saved. Please, try again.'),'flash_error');
			}
		}
		$users = $this->Debtor->User->find('list',array(
			'conditions'=>array(
				'NOT'=>array(
					'User.role'=>array(
						'super_admin','customer'
					),
					'User.is_director'=>1,
					'User.is_bank'=>1
				)
			),
			'recursive'=>-1
		));
		
		if($customer_id){
			$customers=$this->Debtor->Customer->find('list',array(
				'conditions'=>array(
					'OR'=>array(
						'Customer.role'=>'customer',
						'Customer.is_director'=>1
					),
					'Customer.id'=>$customer_id,
				)
			));
		}else{
			$customers=$this->Debtor->Customer->find('list',array(
				'conditions'=>array(
					'OR'=>array(
						'Customer.role'=>'customer',
						'Customer.is_director'=>1
					)
				)
			));
		}
		$this->set(compact('users','customers'));
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		if (!$this->Debtor->exists($id)) {
			throw new NotFoundException(__('Invalid debtor'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			$customer=$this->Debtor->Customer->find('first',array(
				'conditions'=>array(
					'Customer.id'=>$this->request->data['Debtor']['customer_id']
				)
			));
			$this->request->data['Debtor']['customer']=$customer['Customer']['name'];
			if ($this->Debtor->save($this->request->data)) {
				$this->Session->setFlash(__('The debtor has been saved'),'flash_success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The debtor could not be saved. Please, try again.'),'flash_error');
			}
		} else {
			$options = array('conditions' => array('Debtor.' . $this->Debtor->primaryKey => $id));
			$this->request->data = $this->Debtor->find('first', $options);
		}
		$users = $this->Debtor->User->find('list',array(
			'conditions'=>array(
				'NOT'=>array(
					'User.role'=>array(
						'super_admin','customer'
					),
					'User.is_director'=>1,
					'User.is_bank'=>1
				)
			),
			'recursive'=>-1
		));
		
		if($customer_id){
			$customers=$this->Debtor->Customer->find('list',array(
				'conditions'=>array(
					'OR'=>array(
						'Customer.role'=>'customer',
						'Customer.is_director'=>1
					),
					'Customer.id'=>$customer_id,
				)
			));
		}else{
			$customers=$this->Debtor->Customer->find('list',array(
				'conditions'=>array(
					'OR'=>array(
						'Customer.role'=>'customer',
						'Customer.is_director'=>1
					)
				)
			));
		}
		$this->set(compact('users','customers'));
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
		$this->Debtor->id = $id;
		if (!$this->Debtor->exists()) {
			throw new NotFoundException(__('Invalid debtor'));
		}
		if ($this->Debtor->delete()) {
			$this->Session->setFlash(__('Debtor deleted'),'flash_success');
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Debtor was not deleted'),'flash_error');
		$this->redirect(array('action' => 'index'));
	}
}
