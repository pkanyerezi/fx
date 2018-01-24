<?php
App::uses('AppController', 'Controller');
/**
 * Banks Controller
 *
 * @property Bank $Bank
 */
class BanksController extends AppController {
	function beforeFilter() {
        parent::beforeFilter();		
        if($this->Auth->user('role')!='super_admin'){
			$this->Session->setFlash(__('Access Denied!!'),'flash_error');
			$this->redirect($this->Auth->logout());
		}
    }
    
	public function index() {
		$this->Bank->recursive = 0;
		$this->set('banks', $this->paginate());
	}
	
	public function view($id = null) {
		if (!$this->Bank->exists($id)) {
			throw new NotFoundException(__('Invalid bank'));
		}
		$options = array('conditions' => array('Bank.' . $this->Bank->primaryKey => $id));
		$this->set('bank', $this->Bank->find('first', $options));
	}
	
	public function add() {
		if ($this->request->is('post')) {
			$this->Bank->create();
			if ($this->Bank->save($this->request->data)) {
				$this->Session->setFlash(__('The bank has been saved'),'flash_success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The bank could not be saved. Please, try again.'),'flash_error');
			}
		}
	}

	public function edit($id = null) {
		if (!$this->Bank->exists($id)) {
			throw new NotFoundException(__('Invalid bank'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Bank->save($this->request->data)) {
				$this->Session->setFlash(__('The bank has been saved'),'flash_success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The bank could not be saved. Please, try again.'),'flash_error');
			}
		} else {
			$options = array('conditions' => array('Bank.' . $this->Bank->primaryKey => $id));
			$this->request->data = $this->Bank->find('first', $options);
		}
	}

	public function delete($id = null) {
		$this->Bank->id = $id;
		if (!$this->Bank->exists()) {
			throw new NotFoundException(__('Invalid bank'));
		}
		if ($this->Bank->delete()) {
			$this->Session->setFlash(__('Bank deleted'),'flash_success');
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Bank was not deleted'),'flash_error');
		$this->redirect(array('action' => 'index'));
	}
}
