<?php
App::uses('AppController', 'Controller');
/**
 * PurchasedPurposes Controller
 *
 * @property PurchasedPurpose $PurchasedPurpose
 */
class PurchasedPurposesController extends AppController {
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
		$this->PurchasedPurpose->recursive = 0;
		$this->set('purchasedPurposes', $this->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->PurchasedPurpose->exists($id)) {
			throw new NotFoundException(__('Invalid purchased purpose'));
		}
		$options = array('conditions' => array('PurchasedPurpose.' . $this->PurchasedPurpose->primaryKey => $id));
		$this->set('purchasedPurpose', $this->PurchasedPurpose->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->PurchasedPurpose->create();
			if ($this->PurchasedPurpose->save($this->request->data)) {
				$this->Session->setFlash(__('The purchased purpose has been saved'),'flash_success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The purchased purpose could not be saved. Please, try again.'),'flash_error');
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
		if (!$this->PurchasedPurpose->exists($id)) {
			throw new NotFoundException(__('Invalid purchased purpose'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->PurchasedPurpose->save($this->request->data)) {
				$this->Session->setFlash(__('The purchased purpose has been saved'),'flash_success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The purchased purpose could not be saved. Please, try again.'),'flash_error');
			}
		} else {
			$options = array('conditions' => array('PurchasedPurpose.' . $this->PurchasedPurpose->primaryKey => $id));
			$this->request->data = $this->PurchasedPurpose->find('first', $options);
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
		$this->PurchasedPurpose->id = $id;
		if (!$this->PurchasedPurpose->exists()) {
			throw new NotFoundException(__('Invalid purchased purpose'));
		}
		//$this->request->onlyAllow('post', 'delete');
		if ($this->PurchasedPurpose->delete()) {
			$this->Session->setFlash(__('Purchased purpose deleted'),'flash_success');
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Purchased purpose was not deleted'),'flash_error');
		$this->redirect(array('action' => 'index'));
	}
}
