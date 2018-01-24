<?php
App::uses('AppController', 'Controller');
/**
 * Expenses Controller
 *
 * @property Expense $Expense
 */
class ExpensesController extends AppController {
	function beforeFilter() {
        parent::beforeFilter();		
        if ($this->action == 'edit' ||
			$this->action == 'delete') {
			if($this->Auth->user('role')!='super_admin'){
				$this->Session->setFlash(__('Access Denied!!'),'flash_error');
				$this->redirect($this->Auth->logout());
			}
        }
    }
/**
 * index method
 * 
 * @return void
 */
	public function index($item_id=null) {
		$this->Expense->recursive = 0;
		$this->paginate=array('order'=>'CashAtBankForeign.date desc');
		if(	isset($_REQUEST['date_from']) &&
			isset($_REQUEST['date_to'])){
			$from	=($_REQUEST['date_from']);
			$to	=($_REQUEST['date_to']);
			
			$this->set('from', $from);
			$this->set('to', $to);
			
			if($this->Auth->User('role')!='super_admin'){
				if($item_id){
					$this->paginate=array(
						'conditions'=>array('Expense.date >='=>$from,'Expense.date <='=>$to,'Expense.user_id'=>$this->Auth->User('id'),'Expense.item_id'=>$item_id),
						'order'=>'Expense.date desc',
						'limit'=>200
					);
				}else{
					$this->paginate=array(
						'conditions'=>array('Expense.date >='=>$from,'Expense.date <='=>$to,'Expense.user_id'=>$this->Auth->User('id')),
						'order'=>'Expense.date desc',
						'limit'=>200
					);					
				}
			}else{
				if($item_id){
					$this->paginate=array(
						'conditions'=>array('Expense.date >='=>$from,'Expense.date <='=>$to,'Expense.item_id'=>$item_id),
						'order'=>'Expense.date desc',
						'limit'=>200
					);
				}else{
					$this->paginate=array(
						'conditions'=>array('Expense.date >='=>$from,'Expense.date <='=>$to),
						'order'=>'Expense.date desc',
						'limit'=>200
					);
				}
			}
		}
		$this->set('expenses', $this->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->Expense->exists($id)) {
			throw new NotFoundException(__('Invalid expense'));
		}
		$options = array('conditions' => array('Expense.' . $this->Expense->primaryKey => $id));
		$this->set('expense', $this->Expense->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$func=$this->Func;
			$this->request->data['Expense']['id']=$func->getUID1();
			if($this->Auth->User('role')!='super_admin'){
				$this->request->data['Expense']['user_id']=$this->Auth->User('id');
			}
			$this->Expense->create();
			if ($this->Expense->save($this->request->data)) {
				$this->Session->setFlash(__('The expense has been saved'),'flash_success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The expense could not be saved. Please, try again.'),'flash_error');
			}
		}
		$users = $this->Expense->User->find('list',array(
			'conditions'=>array(
				'NOT'=>array(
					'User.role'=>array(
						'super_admin','customer'
					)
				)
			),
			'recursive'=>-1
		));
		$items = $this->Expense->Item->find('list');
		$this->set(compact('users','items'));
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		if (!$this->Expense->exists($id)) {
			throw new NotFoundException(__('Invalid expense'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Expense->save($this->request->data)) {
				$this->Session->setFlash(__('The expense has been saved'),'flash_success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The expense could not be saved. Please, try again.'),'flash_error');
			}
		} else {
			$options = array('conditions' => array('Expense.' . $this->Expense->primaryKey => $id));
			$this->request->data = $this->Expense->find('first', $options);
		}
		$users = $this->Expense->User->find('list',array(
			'conditions'=>array(
				'NOT'=>array(
					'User.role'=>array(
						'super_admin','customer'
					)
				)
			),
			'recursive'=>-1
		));
		$items = $this->Expense->Item->find('list');
		$this->set(compact('users','items'));
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
		$this->Expense->id = $id;
		if (!$this->Expense->exists()) {
			throw new NotFoundException(__('Invalid expense'));
		}
		if ($this->Expense->delete()) {
			$this->Session->setFlash(__('Expense deleted'),'flash_success');
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Expense was not deleted'),'flash_error');
		$this->redirect(array('action' => 'index'));
	}
}
