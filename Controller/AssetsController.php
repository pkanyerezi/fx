<?php
App::uses('AppController', 'Controller');
class AssetsController extends AppController {
	function beforeFilter() {
        parent::beforeFilter();		
        if($this->Auth->user('role')!='super_admin'){
			$this->Session->setFlash(__('Access Denied!!'));
			$this->redirect($this->Auth->logout());
		}
    }
	
	public function xx(){
		
		echo $_SERVER['REMOTE_ADDR'];
		exit();
	}
	
	public function list_items($item_id=null) {
		$this->Asset->recursive = 0;
		if(	isset($_REQUEST['date_from']) &&
			isset($_REQUEST['date_to'])){
			$from=($_REQUEST['date_from']);
			$to	=($_REQUEST['date_to']);
			
			$this->set('from', $from);
			$this->set('to', $to);
			
			if($item_id){
				$this->paginate=array(
					'conditions'=>array('Asset.date >='=>$from,'Asset.date <='=>$to,'Asset.asset_name_id'=>$item_id),
					'order'=>'Asset.date desc',
					'limit'=>200
				);
			}else{
				$this->paginate=array(
					'conditions'=>array('Asset.date >='=>$from,'Asset.date <='=>$to),
					'order'=>'Asset.date desc',
					'limit'=>200
				);
			}
		}else{
			$this->paginate=array(
				'order'=>'Asset.date desc',
				'limit'=>200
			);
		}
		$this->set('assets', $this->paginate());
	}

	public function view($id = null) {
		if (!$this->Asset->exists($id)) {
			throw new NotFoundException(__('Invalid asset'));
		}
		$options = array('conditions' => array('Asset.' . $this->Asset->primaryKey => $id));
		$this->set('asset', $this->Asset->find('first', $options));
	}

	public function add() {
		if ($this->request->is('post')) {
			$this->Asset->create();
			if ($this->Asset->save($this->request->data)) {
				$this->Session->setFlash(__('The asset has been saved'));
				$this->redirect(array('action' => 'list_items'));
			} else {
				$this->Session->setFlash(__('The asset could not be saved. Please, try again.'));
			}
		}
		$assetNames = $this->Asset->AssetName->find('list');
		$this->set(compact('assetNames'));
	}

	public function edit($id = null) {
		if (!$this->Asset->exists($id)) {
			throw new NotFoundException(__('Invalid asset'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Asset->save($this->request->data)) {
				$this->Session->setFlash(__('The asset has been saved'));
				$this->redirect(array('action' => 'list_items'));
			} else {
				$this->Session->setFlash(__('The asset could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('Asset.' . $this->Asset->primaryKey => $id));
			$this->request->data = $this->Asset->find('first', $options);
		}
		$assetNames = $this->Asset->AssetName->find('list');
		$this->set(compact('users','assetNames'));
	}

	public function delete($id = null) {
		$this->Asset->id = $id;
		if (!$this->Asset->exists()) {
			throw new NotFoundException(__('Invalid asset'));
		}
		if ($this->Asset->delete()) {
			$this->Session->setFlash(__('Asset deleted'));
			$this->redirect(array('action'=>'list_items'));
		}
		$this->Session->setFlash(__('Asset was not deleted'));
		$this->redirect(array('action' => 'list_items'));
	}
}
