<?php
App::uses('AppController', 'Controller');
class AssetNamesController extends AppController {
	function beforeFilter() {
        parent::beforeFilter();		
		if($this->Auth->user('role')!='super_admin'){
			$this->Session->setFlash(__('Access Denied!!'));
			$this->redirect($this->Auth->logout());
		}
    }
/**
 * index method
 * 
 * @return void
 */
	public function index($item_id=null) {
		$this->AssetName->recursive = 0;
		$this->set('assetNames', $this->paginate());
	}

	public function view($id = null) {
		if (!$this->AssetName->exists($id)) {
			throw new NotFoundException(__('Invalid AssetName'));
		}
		$options = array('conditions' => array('AssetName.' . $this->AssetName->primaryKey => $id));
		$this->set('assetName', $this->AssetName->find('first', $options));
	}

	public function add() {
		if ($this->request->is('post')) {
			$this->AssetName->create();
			if ($this->AssetName->save($this->request->data)) {
				$this->Session->setFlash(__('The AssetName has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The AssetName could not be saved. Please, try again.'));
			}
		}
	}
	
	public function edit($id = null) {
		if (!$this->AssetName->exists($id)) {
			throw new NotFoundException(__('Invalid AssetName'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->AssetName->save($this->request->data)) {
				$this->Session->setFlash(__('The AssetName has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The AssetName could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('AssetName.' . $this->AssetName->primaryKey => $id));
			$this->request->data = $this->AssetName->find('first', $options);
		}
	}

	public function delete($id = null) {
		$this->AssetName->id = $id;
		if (!$this->AssetName->exists()) {
			throw new NotFoundException(__('Invalid AssetName'));
		}
		if ($this->AssetName->delete()) {
			$this->Session->setFlash(__('AssetName deleted'));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('AssetName was not deleted'));
		$this->redirect(array('action' => 'index'));
	}
}
