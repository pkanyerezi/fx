<?php
App::uses('AppController', 'Controller');
/**
 * DailySellingReturns Controller
 *
 * @property DailySellingReturn $DailySellingReturn
 */
class DailySellingReturnsController extends AppController {
	
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
	public function index() {
		$this->DailySellingReturn->recursive = 0;
		$this->set('dailySellingReturns', $this->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->DailySellingReturn->exists($id)) {
			throw new NotFoundException(__('Invalid daily selling return'));
		}
		$options = array('conditions' => array('DailySellingReturn.' . $this->DailySellingReturn->primaryKey => $id));
		$this->set('dailySellingReturn', $this->DailySellingReturn->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->DailySellingReturn->create();
			if ($this->DailySellingReturn->save($this->request->data)) {
				$this->Session->setFlash(__('The daily selling return has been saved'),'flash_success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The daily selling return could not be saved. Please, try again.'),'flash_error');
			}
		}
		$foxes = $this->DailySellingReturn->Fox->find('list');
		$this->set(compact('foxes'));
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		if (!$this->DailySellingReturn->exists($id)) {
			throw new NotFoundException(__('Invalid daily selling return'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->DailySellingReturn->save($this->request->data)) {
				$this->Session->setFlash(__('The daily selling return has been saved'),'flash_success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The daily selling return could not be saved. Please, try again.'),'flash_error');
			}
		} else {
			$options = array('conditions' => array('DailySellingReturn.' . $this->DailySellingReturn->primaryKey => $id));
			$this->request->data = $this->DailySellingReturn->find('first', $options);
		}
		$foxes = $this->DailySellingReturn->Fox->find('list');
		$this->set(compact('foxes'));
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
		$this->DailySellingReturn->id = $id;
		if (!$this->DailySellingReturn->exists()) {
			throw new NotFoundException(__('Invalid daily selling return'));
		}
		if ($this->DailySellingReturn->delete()) {
			$this->Session->setFlash(__('Daily selling return deleted'),'flash_success');
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Daily selling return was not deleted'),'flash_error');
		$this->redirect(array('action' => 'index'));
	}
}
