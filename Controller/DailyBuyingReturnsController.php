<?php
App::uses('AppController', 'Controller');
/**
 * DailyBuyingReturns Controller
 *
 * @property DailyBuyingReturn $DailyBuyingReturn
 */
class DailyBuyingReturnsController extends AppController {
	
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
		$this->DailyBuyingReturn->recursive = 0;
		$this->set('dailyBuyingReturns', $this->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->DailyBuyingReturn->exists($id)) {
			throw new NotFoundException(__('Invalid daily buying return'));
		}
		$options = array('conditions' => array('DailyBuyingReturn.' . $this->DailyBuyingReturn->primaryKey => $id));
		$this->set('dailyBuyingReturn', $this->DailyBuyingReturn->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->DailyBuyingReturn->create();
			if ($this->DailyBuyingReturn->save($this->request->data)) {
				$this->Session->setFlash(__('The daily buying return has been saved'),'flash_success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The daily buying return could not be saved. Please, try again.'),'flash_error');
			}
		}
		$foxes = $this->DailyBuyingReturn->Fox->find('list');
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
		if (!$this->DailyBuyingReturn->exists($id)) {
			throw new NotFoundException(__('Invalid daily buying return'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->DailyBuyingReturn->save($this->request->data)) {
				$this->Session->setFlash(__('The daily buying return has been saved'),'flash_success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The daily buying return could not be saved. Please, try again.'),'flash_error');
			}
		} else {
			$options = array('conditions' => array('DailyBuyingReturn.' . $this->DailyBuyingReturn->primaryKey => $id));
			$this->request->data = $this->DailyBuyingReturn->find('first', $options);
		}
		$foxes = $this->DailyBuyingReturn->Fox->find('list');
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
		$this->DailyBuyingReturn->id = $id;
		if (!$this->DailyBuyingReturn->exists()) {
			throw new NotFoundException(__('Invalid daily buying return'));
		}
		
		if ($this->DailyBuyingReturn->delete()) {
			$this->Session->setFlash(__('Daily buying return deleted'),'flash_success');
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Daily buying return was not deleted'),'flash_error');
		$this->redirect(array('action' => 'index'));
	}
}
