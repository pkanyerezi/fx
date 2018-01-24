<?php
App::uses('AppController', 'Controller');
/**
 * Purposes Controller
 *
 * @property Purpose $Purpose
 */
class PurposesController extends AppController {
	function beforeFilter() {
        parent::beforeFilter();		
        if ($this->action == 'edit' ||
			$this->action == 'add' ||
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
	public function index() {
		$this->Purpose->recursive = 0;
		$this->set('purposes', $this->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->Purpose->exists($id)) {
			throw new NotFoundException(__('Invalid purpose'));
		}
		$options = array('conditions' => array('Purpose.' . $this->Purpose->primaryKey => $id));
		$this->set('purpose', $this->Purpose->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->Purpose->create();
			if ($this->Purpose->save($this->request->data)) {
				$this->Session->setFlash(__('The purpose has been saved'),'flash_success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The purpose could not be saved. Please, try again.'),'flash_error');
			}
		}
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		if (!$this->Purpose->exists($id)) {
			throw new NotFoundException(__('Invalid purpose'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Purpose->save($this->request->data)) {
				$this->Session->setFlash(__('The purpose has been saved'),'flash_success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The purpose could not be saved. Please, try again.'),'flash_error');
			}
		} else {
			$options = array('conditions' => array('Purpose.' . $this->Purpose->primaryKey => $id));
			$this->request->data = $this->Purpose->find('first', $options);
		}
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
		$this->Purpose->id = $id;
		if (!$this->Purpose->exists()) {
			throw new NotFoundException(__('Invalid purpose'));
		}
		//$this->request->onlyAllow('post', 'delete');
		if ($this->Purpose->delete()) {
			$this->Session->setFlash(__('Purpose deleted'),'flash_success');
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Purpose was not deleted'),'flash_error');
		$this->redirect(array('action' => 'index'));
	}
}
