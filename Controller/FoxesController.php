<?php
App::uses('AppController', 'Controller');
class FoxesController extends AppController {
	
	function beforeFilter() {
        parent::beforeFilter();		
        if ($this->action == 'edit') {
			if($this->Auth->user('role')!='super_admin'){
				$this->Session->setFlash(__('Access Denied!!'),'flash_error');
				$this->redirect($this->Auth->logout());
			}
        }
    }
	
	public function edit($id = 9265236542) {
		if (!$this->Fox->exists($id)) {
			throw new NotFoundException(__('Invalid fox'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Fox->save($this->request->data)) {
				$this->Session->write('fox',$this->Fox->find('first'));
				$this->Session->setFlash(__('Details have been updated.'),'flash_success');
				$this->redirect(array('action' => 'index','controller'=>'dashboards'));
			} else {
				$this->Session->setFlash(__('The details could not be updated. Please, try again.'),'flash_error');
			}
		} else {
			$options = array('conditions' => array('Fox.' . $this->Fox->primaryKey => $id));
			$this->request->data = $this->Fox->find('first', $options);
		}
	}
}
