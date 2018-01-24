<?php
App::uses('AppController', 'Controller');
class TtAccountsController extends AppController {
	function beforeFilter() {
        parent::beforeFilter();		
        if($this->Auth->user('role')!='super_admin'){
			$this->Session->setFlash(__('Access Denied!!'),'flash_error');
			$this->redirect($this->Auth->logout());
		}
    }
	
	
	public function index() {
		$this->TtAccount->recursive = 0; 
		$this->paginate = array(
			'limit'=>100
		);
		$this->set('tTAccounts', $this->paginate());
	}
	
	public function edit($id = null) {
		if (!$this->TtAccount->exists($id)) {
			throw new NotFoundException(__('Invalid ttaccount'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->TtAccount->save($this->request->data)) {
				$this->Session->setFlash(__('The TtAccount has been saved'),'flash_success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The TtAccount could not be saved. Please, try again.'),'flash_error');
			}
		}
		$options = array('conditions' => array('TtAccount.' . $this->TtAccount->primaryKey => $id));
		$this->request->data = $this->TtAccount->find('first', $options);
	}

}
