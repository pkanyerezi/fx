<?php
App::uses('AppController', 'Controller');
/**
 * Creditors Controller
 *
 * @property Creditor $Creditor
 */
class CreditorsController extends AppController {
	function beforeFilter() {
        parent::beforeFilter();		
        /* if($this->Auth->user('role')!='super_admin'){
			$this->Session->setFlash(__('Access Denied!!'));
			$this->redirect($this->Auth->logout());
		} */
    } 
    
	
	
	public function index($customer_id=null) {
		$this->Creditor->recursive = 0;
		$this->paginate=array('Creditor.order'=>'date desc');
		if(	isset($_REQUEST['date_from']) &&
			isset($_REQUEST['date_to'])){
			$from	=($_REQUEST['date_from']);
			$to	=($_REQUEST['date_to']);
			
			$this->set('from', $from);
			$this->set('to', $to);
			
			if($customer_id){
				$this->paginate=array(
					'conditions'=>array(
						'Creditor.customer_id'=>$customer_id
					),
					'order'=>'Creditor.date desc',
					'limit'=>200
				);
			}else{
				$this->paginate=array(
					'conditions'=>array(
						'Creditor.date >='=>$from,
						'Creditor.date <='=>$to
					),
					'order'=>'Creditor.date desc',
					'limit'=>200
				);
			}
		}
		
		if($customer_id)	$this->set('customer_id', $customer_id);
		
		$this->set('creditors', $this->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->Creditor->exists($id)) {
			throw new NotFoundException(__('Invalid creditor'));
		}
		$options = array('conditions' => array('Creditor.' . $this->Creditor->primaryKey => $id));
		$this->set('creditor', $this->Creditor->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function add($customer_id=null) {
		if ($this->request->is('post')) {
			$_date=date('Y-m-d H:i:s');
			if($this->Auth->User('role')!='super_admin'){
				$this->request->data['Creditor']['user_id']=$this->Auth->User('id');
				$this->request->data['Creditor']['date']=date('Y-m-d',strtotime($_date));
			}
			
			$customer=$this->Creditor->Customer->find('first',array(
				'conditions'=>array(
					'Customer.id'=>$this->request->data['Creditor']['customer_id']
				)
			));
			$this->request->data['Creditor']['customer']=$customer['Customer']['name'];
			$this->Creditor->create();
			if ($this->Creditor->save($this->request->data)) {
				$this->Session->setFlash(__('The creditor has been saved'),'flash_success');
				//Save transaction log
				$func=$this->Func;
				$action_performed=$this->Auth->User('name').' added creditor of '.(date('Y-m-d',strtotime($this->request->data['Creditor']['date']))).' with amount '.($this->request->data['Creditor']['amount']).' on '.(date('M d Y h:i:sa',strtotime($_date)));
				
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
			} else {
				$this->Session->setFlash(__('The creditor could not be saved. Please, try again.'),'flash_error');
			}
		}
		
		if($customer_id){
			$customers=$this->Creditor->Customer->find('list',array(
				'conditions'=>array(
					'OR'=>array(
						'Customer.role'=>'customer',
						'Customer.is_director'=>1
					),
					'Customer.id'=>$customer_id,
				)
			));
		}else{
			$customers=$this->Creditor->Customer->find('list',array(
				'conditions'=>array(
					'OR'=>array(
						'Customer.role'=>'customer',
						'Customer.is_director'=>1
					)
				)
			));
		}
		$users = $this->Creditor->User->find('list',array(
			'conditions'=>array(
				'NOT'=>array(
					'User.role'=>array(
						'super_admin','customer'
					),
					'User.is_bank'=>1,
					'User.is_director'=>1
				)
			),
			'recursive'=>-1
		));
		
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
		if (!$this->Creditor->exists($id)) {
			throw new NotFoundException(__('Invalid creditor'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			$customer=$this->Creditor->Customer->find('first',array(
				'conditions'=>array(
					'Customer.id'=>$this->request->data['Creditor']['customer_id']
				)
			));
			$this->request->data['Creditor']['customer']=$customer['Customer']['name'];
			if ($this->Creditor->save($this->request->data)) {
				$this->Session->setFlash(__('The creditor has been saved'),'flash_success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The creditor could not be saved. Please, try again.'),'flash_error');
			}
		} else {
			$options = array('conditions' => array('Creditor.' . $this->Creditor->primaryKey => $id));
			$this->request->data = $this->Creditor->find('first', $options);
		}
		if($customer_id){
			$customers=$this->Creditor->Customer->find('list',array(
				'conditions'=>array(
					'OR'=>array(
						'Customer.role'=>'customer',
						'Customer.is_director'=>1
					),
					'Customer.id'=>$customer_id,
				)
			));
		}else{
			$customers=$this->Creditor->Customer->find('list',array(
				'conditions'=>array(
					'OR'=>array(
						'Customer.role'=>'customer',
						'Customer.is_director'=>1
					)
				)
			));
		}
		$users = $this->Creditor->User->find('list',array(
			'conditions'=>array(
				'NOT'=>array(
					'User.role'=>array(
						'super_admin','customer'
					),
					'User.is_bank'=>1,
					'User.is_director'=>1
				)
			),
			'recursive'=>-1
		));
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
		$this->Creditor->id = $id;
		if (!$this->Creditor->exists()) {
			throw new NotFoundException(__('Invalid creditor'));
		}
		if ($this->Creditor->delete()) {
			$this->Session->setFlash(__('Creditor deleted'),'flash_success');
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Creditor was not deleted'),'flash_error');
		$this->redirect(array('action' => 'index'));
	}
}
