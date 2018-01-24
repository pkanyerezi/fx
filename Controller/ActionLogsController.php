<?php
App::uses('AppController', 'Controller');
/**
 * ActionLogs Controller
 *
 * @property ActionLog $ActionLog
 */
class ActionLogsController extends AppController {
	function beforeFilter() {
        parent::beforeFilter();		
        if ($this->action == 'delete') {
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
	public function index($user_id) {
		$this->ActionLog->recursive = 0;
		$this->paginate=array('order'=>'ActionLog.date_time_created ASC');
		if(	isset($_REQUEST['date_from']) &&
			isset($_REQUEST['date_to'])){
			$from	=($_REQUEST['date_from']);
			$to	=($_REQUEST['date_to']);
			
			$this->set('from', $from);
			$this->set('to', $to);
			
			$this->paginate=array(
				'conditions'=>array(
					'ActionLog.date_created >='=>$from,
					'ActionLog.date_created <='=>$to,
					'ActionLog.user_id'=>$user_id
				),
				'order'=>'ActionLog.date_time_created ASC'
			);
		}
		$this->set('actionLogs', $this->paginate());
		$this->set('user_id', $user_id);
	}
	
	public function delete($id = null,$user_id=null) {
		$this->ActionLog->id = $id;
		if (!$this->ActionLog->exists()) {
			throw new NotFoundException(__('Invalid ActionLog'));
		}
		if ($this->ActionLog->delete()) {
			$this->Session->setFlash(__('ActionLog deleted'),'flash_success');
			$this->redirect(array('action'=>'index',($user_id)?$user_id:$this->Auth->User('id')));
		}
		$this->Session->setFlash(__('ActionLog was not deleted'),'flash_error');
		$this->redirect(array('action' => 'index',($user_id)?$user_id:$this->Auth->User('id')));
	}
}
