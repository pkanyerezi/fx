<?php
App::uses('AppController', 'Controller');
/**
 * Receivables Controller
 *
 * @property Receivable $Receivable
 */
class ReceivablesController extends AppController {
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
		$this->Receivable->recursive = 0;
		$this->paginate=array('Receivable.order'=>'date desc');
		if(	isset($_REQUEST['date_from']) &&
			isset($_REQUEST['date_to'])){
			$from	=($_REQUEST['date_from']);
			$to	=($_REQUEST['date_to']);
			
			$this->set('from', $from);
			$this->set('to', $to);
			
			if($customer_id){
				$this->paginate=array(
					'conditions'=>array(
						'Receivable.customer_id'=>$customer_id
					),
					'order'=>'Receivable.date desc',
					'limit'=>200
				);
			}else{
				$this->paginate=array(
					'conditions'=>array(
						'Receivable.date >='=>$from,
						'Receivable.date <='=>$to
					),
					'order'=>'Receivable.date desc',
					'limit'=>200
				);
			}
		}
		
		if($customer_id)
		{	
			$this->set('customer', $this->Receivable->Customer->find('first',['conditions'=>['Customer.id'=>$customer_id],'recursive'=>-1]));
			$this->set('customer_id', $customer_id);
		}
		
		$this->set('receivables', $this->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->Receivable->exists($id)) {
			throw new NotFoundException(__('Invalid deposit'));
		}
		$options = array('conditions' => array('Receivable.' . $this->Receivable->primaryKey => $id));
		$this->set('receivable', $this->Receivable->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function add($customer_id=null) {
		if ($this->request->is('post')) {
			if($this->Auth->User('role')!='super_admin'){
				$this->request->data['Receivable']['user_id']=$this->Auth->User('id');
			}
			$customer=$this->Receivable->Customer->find('first',array(
				'conditions'=>array(
					'Customer.id'=>$this->request->data['Receivable']['customer_id']
				)
			));
			$this->request->data['Receivable']['customer']=$customer['Customer']['name'];
			$this->Receivable->create();
			if ($this->Receivable->save($this->request->data)) {
				$this->Session->setFlash(__('The deposit has been saved'),'flash_success');
				$this->redirect(array('action' => 'index',$this->request->data['Receivable']['customer_id']));
			} else {
				$this->Session->setFlash(__('The deposit could not be saved. Please, try again.'),'flash_error');
			}
		}
		$users = $this->Receivable->User->find('list',array(
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
			$customers=$this->Receivable->Customer->find('list',array(
				'conditions'=>array(
					'Customer.role'=>'customer',
					'Customer.id'=>$customer_id,
				)
			));
			$customer_details = $this->Receivable->Customer->find('first',['conditions'=>['Customer.id'=>$customer_id]]);
		}else{
			$customers=$this->Receivable->Customer->find('list',array(
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
		$receivable = $this->Receivable->find('first',['conditions'=>['Receivable.id'=>$id]]);
		if (empty($receivable)) {
			throw new NotFoundException(__('Invalid deposit'));
		}
		
		$customer_id = $receivable['Receivable']['customer_id'];
		
		if ($this->request->is('post') || $this->request->is('put')) {
			$customer=$this->Receivable->Customer->find('first',array(
				'conditions'=>array(
					'Customer.id'=>$this->request->data['Receivable']['customer_id']
				)
			));
			$this->request->data['Receivable']['customer']=$customer['Customer']['name'];
			if ($this->Receivable->save($this->request->data)) {
				$this->Session->setFlash(__('Saved'),'flash_success');
				$this->redirect(array('action' => 'index',$customer_id));
			} else {
				$this->Session->setFlash(__('Error while saving. Please, try again.'),'flash_error');
			}
		} else {
			$options = array('conditions' => array('Receivable.' . $this->Receivable->primaryKey => $id));
			$this->request->data = $this->Receivable->find('first', $options);
		}
		$users = $this->Receivable->User->find('list',array(
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
			$customers=$this->Receivable->Customer->find('list',array(
				'conditions'=>array(
					'Customer.role'=>'customer',
					'Customer.id'=>$customer_id,
				)
			));
			$customer_details = $this->Receivable->Customer->find('first',['conditions'=>['Customer.id'=>$customer_id]]);
		}else{
			$customers=$this->Receivable->Customer->find('list',array(
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
		$this->Receivable->id = $id;
		if (!$this->Receivable->exists()) {
			throw new NotFoundException(__('Invalid deposit'));
		}
		if ($this->Receivable->delete()) {
			$this->Session->setFlash(__('deposit deleted'),'flash_success');
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('deposit was not deleted'),'flash_error');
		$this->redirect(array('action' => 'index'));
	}
}
