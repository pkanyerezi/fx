<?php
App::uses('AppController', 'Controller');
/**
 * ContactLists Controller
 *
 * @property ContactList $ContactList
 */
class ContactListsController extends AppController {
	
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
		$this->ContactList->recursive = 0;
		$this->set('contactLists', $this->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->ContactList->exists($id)) {
			throw new NotFoundException(__('Invalid contact list'));
		}
		$options = array('conditions' => array('ContactList.' . $this->ContactList->primaryKey => $id));
		$this->set('contactList', $this->ContactList->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$func=$this->Func;
			$this->request->data['ContactList']['id']=$func->getUID1();
			date_default_timezone_set('Africa/Nairobi');
			$this->request->data['ContactList']['date']=date('Y-m-d H:i:s');
			$this->ContactList->create();
			if ($this->ContactList->save($this->request->data)) {
				$this->Session->setFlash(__('The contact list has been saved'),'flash_success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The contact list could not be saved. Please, try again.'),'flash_error');
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
		if (!$this->ContactList->exists($id)) {
			throw new NotFoundException(__('Invalid contact list'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->ContactList->save($this->request->data)) {
				$this->Session->setFlash(__('The contact list has been saved'),'flash_success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The contact list could not be saved. Please, try again.'),'flash_error');
			}
		} else {
			$options = array('conditions' => array('ContactList.' . $this->ContactList->primaryKey => $id));
			$this->request->data = $this->ContactList->find('first', $options);
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
		$this->ContactList->id = $id;
		if (!$this->ContactList->exists()) {
			throw new NotFoundException(__('Invalid contact list'));
		}
		if ($this->ContactList->delete()) {
			$this->Session->setFlash(__('Contact list deleted'),'flash_success');
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Contact list was not deleted'),'flash_error');
		$this->redirect(array('action' => 'index'));
	}
}
