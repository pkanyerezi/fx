<?php
App::uses('AppController', 'Controller');
/**
 * OtherCurrencies Controller
 *
 * @property OtherCurrency $OtherCurrency
 */
class OtherCurrenciesController extends AppController {

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->OtherCurrency->recursive = 0;
		$this->set('otherCurrencies', $this->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->OtherCurrency->exists($id)) {
			throw new NotFoundException(__('Invalid other currency'));
		}
		$options = array('conditions' => array('OtherCurrency.' . $this->OtherCurrency->primaryKey => $id));
		$this->set('otherCurrency', $this->OtherCurrency->find('first', $options));
	}
/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$OtherCurrency_ID=trim($this->request->data['OtherCurrency']['other_currency']);
			if ($this->OtherCurrency->exists($OtherCurrency_ID)) {
				$this->Session->setFlash(__('Currency ID Exists Already.'),'flash_warning');
				$this->redirect(array('action' => 'index'));
			}
			$this->request->data['OtherCurrency']['id']=strtoupper($OtherCurrency_ID);
			$this->OtherCurrency->create();
			if ($this->OtherCurrency->save($this->request->data)) {
				$this->Session->setFlash(__('The other currency has been saved'),'flash_success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The other currency could not be saved. Please, try again.'),'flash_error');
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
		if (!$this->OtherCurrency->exists($id)) {
			throw new NotFoundException(__('Invalid other currency'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->OtherCurrency->save($this->request->data)) {
				$this->Session->setFlash(__('The other currency has been saved'),'flash_success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The other currency could not be saved. Please, try again.'),'flash_error');
			}
		} else {
			$options = array('conditions' => array('OtherCurrency.' . $this->OtherCurrency->primaryKey => $id));
			$this->request->data = $this->OtherCurrency->find('first', $options);
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
		$this->OtherCurrency->id = $id;
		if (!$this->OtherCurrency->exists()) {
			throw new NotFoundException(__('Invalid other currency'));
		}
		/* $this->request->onlyAllow('post', 'delete'); */
		if ($this->OtherCurrency->delete()) {
			$this->Session->setFlash(__('Other currency deleted'),'flash_success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Other currency was not deleted'),'flash_error');
		$this->redirect(array('action' => 'index'));
	}
}
