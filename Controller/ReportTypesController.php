<?php
App::uses('AppController', 'Controller');
App::uses('HttpSocket', 'Network/Http');
App::uses('CakeEmail', 'Network/Email');
class ReportTypesController extends AppController {
	function beforeFilter() {
        parent::beforeFilter();		
        if (in_array($this->action, ['delete','add','index'])) {
			if($this->Auth->user('role')!='super_admin'){
				$this->Session->setFlash(__('Access Denied!!'),'flash_error');
				$this->redirect(['controller'=>'dashboards']);
			}
        }
        $this->Auth->allow(['send_notifications']);
    }

	public function index() {
		$this->ReportType->recursive = -1;
		$this->paginate=array('order'=>'ReportType.sort_order ASC');
		$this->set('reportTypes', $this->paginate());
	}

	public function edit($id) {
		if (!$this->ReportType->exists($id)) {
			$this->Session->setFlash(__('Invalid ReportType'),'flash_warning');
			$this->redirect(array('action'=>'index'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {

			if ($this->ReportType->save($this->request->data)) {
				$this->Session->setFlash(__('Saved'),'flash_success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('Error: Not saved. Please, try again.'),'flash_error');
			}
		} else {
			$options = array('conditions' => array('ReportType.' . $this->ReportType->primaryKey => $id));
			$this->request->data = $this->ReportType->find('first', $options);
		}
	}
}