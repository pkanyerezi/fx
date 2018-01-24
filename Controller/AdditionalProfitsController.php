<?php
App::uses('AppController', 'Controller');
/**
 * AdditionalProfits Controller
 *
 * @property AdditionalProfit $AdditionalProfit
 */
class AdditionalProfitsController extends AppController {
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
	public function index() {
		$this->AdditionalProfit->recursive = 0;
		$this->paginate=array('order'=>'AdditionalProfit.date desc');
		if(	isset($_REQUEST['date_from']) &&
			isset($_REQUEST['date_to'])){
			$from	=($_REQUEST['date_from']);
			$to	=($_REQUEST['date_to']);
			
			$this->set('from', $from);
			$this->set('to', $to);
			
			$this->paginate=array(
				'conditions'=>array(
					'AdditionalProfit.date >='=>$from,
					'AdditionalProfit.date <='=>$to
				),
				'order'=>'AdditionalProfit.date desc'
			);
		}
		$this->set('additionalProfits', $this->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->AdditionalProfit->exists($id)) {
			throw new NotFoundException(__('Invalid additionalProfit'));
		}
		$options = array('conditions' => array('AdditionalProfit.' . $this->AdditionalProfit->primaryKey => $id));
		$this->set('additionalProfit', $this->AdditionalProfit->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			if($this->Auth->User('role')!='super_admin'){
				$this->request->data['AdditionalProfit']['user_id']=$this->Auth->User('id');
			}
			$this->AdditionalProfit->create();
			if ($this->AdditionalProfit->save($this->request->data)) {
				$this->Session->setFlash(__('The additionalProfit has been saved'),'flash_success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The additionalProfit could not be saved. Please, try again.'),'flash_error');
			}
		}
		$users = $this->AdditionalProfit->User->find('list',array(
			'conditions'=>array(
				'NOT'=>array(
					'User.role'=>array(
						'super_admin','customer'
					)
				)
			),
			'recursive'=>-1
		));
		$this->set(compact('users'));
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		if (!$this->AdditionalProfit->exists($id)) {
			throw new NotFoundException(__('Invalid additionalProfit'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if($this->Auth->User('role')!='super_admin'){
				$this->request->data['AdditionalProfit']['user_id']=$this->Auth->User('id');
			}
			if ($this->AdditionalProfit->save($this->request->data)) {
				$this->Session->setFlash(__('The additionalProfit has been saved'),'flash_success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The additionalProfit could not be saved. Please, try again.'),'flash_error');
			}
		} else {
			$options = array('conditions' => array('AdditionalProfit.' . $this->AdditionalProfit->primaryKey => $id));
			$this->request->data = $this->AdditionalProfit->find('first', $options);
		}
		$users = $this->AdditionalProfit->User->find('list',array(
			'conditions'=>array(
				'NOT'=>array(
					'User.role'=>array(
						'super_admin','customer'
					)
				)
			),
			'recursive'=>-1
		));
		$this->set(compact('users'));
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
		$this->AdditionalProfit->id = $id;
		if (!$this->AdditionalProfit->exists()) {
			throw new NotFoundException(__('Invalid additionalProfit'));
		}
		if ($this->AdditionalProfit->delete()) {
			$this->Session->setFlash(__('AdditionalProfit deleted'),'flash_success');
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('AdditionalProfit was not deleted'),'flash_error');
		$this->redirect(array('action' => 'index'));
	}
}
