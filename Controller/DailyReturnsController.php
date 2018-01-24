<?php
App::uses('AppController', 'Controller');
/**
 * DailyReturns Controller
 *
 * @property DailyReturn $DailyReturn
 */
class DailyReturnsController extends AppController {
	
	public $uses=array('DailyReturn','Fox','Currency','DailyBuyingReturn','DailySellingReturn');
	
	function beforeFilter() {
        parent::beforeFilter();
        if ($this->action == 'edit' || $this->action == 'delete') {
			if($this->Auth->user('role')!='super_admin'){
				$this->Session->setFlash(__('Access Denied!!'),'flash_error');
				$this->redirect($this->Auth->logout());
			}
        }
    }

	public function send($id){
		ini_set('max_execution_time', 120);
		if (!$this->DailyReturn->exists($id)) {
			throw new NotFoundException(__('Invalid daily return'));
		}
		$response_array=array();
		$DailyReturns=$this->DailyReturn->find('all',array('limit'=>1,'conditions'=>array('DailyReturn.id'=>$id)));
		if(isset($DailyReturns[0])){
			$resting=new $this->Resting;
			$_fox=($this->Session->read('fox'));
			$resting->api_username=$_fox['Fox']['un'];
			$resting->api_password=$_fox['Fox']['pwd'];
			$resting->authorisation_key=$_fox['Fox']['k'];
			$resting->url = $_fox['Fox']['url'];
			$response=$resting->XML_fetch_data('/daily_returns/fox_add_daily_returns.json','<Returns>'.(json_encode($DailyReturns[0])).'</Returns>');
			if($resting->has_response){
				$response_array_full=json_decode($response);
				$response_array=array();
				if(isset($response_array_full->data->response->msgs)){
					$response_array=$response_array_full->data->response->msgs;
				}
			}else{
				$response_array=array("could not communicate with BOU/ Check your internet connection");
			}
		}else{
			throw new NotFoundException(__('No record found'));
			$response_array=array("No record found");
		}	
		$msgs='';$counter =0;
		foreach($response_array as $msg){			
			($counter==0)?$msgs=$msg:$msgs.=$msg;
			$counter++;
		}
		
		if(!strlen($msgs))
			$msgs='No response.';
		
		$this->Session->setFlash(__($msgs),'flash_warning');
		$this->redirect(array('action' => 'index'));
	}

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->DailyReturn->recursive = 0;
		$this->paginate=array('order'=>'DailyReturn.date desc');
		if(	isset($_REQUEST['date_from']) &&
			isset($_REQUEST['date_to'])){
			
			$from	=($_REQUEST['date_from']);
			$to	=($_REQUEST['date_to']);	
			
			$this->set('from', $from);
			$this->set('to', $to);
			
			$this->paginate=array('order'=>'DailyReturn.date desc','conditions'=>array('DailyReturn.date >= '=>$from,'DailyReturn.date <= '=>$to));
		}else{
			$this->Session->setFlash(__('Date range required.'),'flash_warning');
		}
		$this->set('dailyReturns', $this->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->DailyReturn->exists($id)) {
			throw new NotFoundException(__('Invalid daily return'));
		}
		$options = array('conditions' => array('DailyReturn.' . $this->DailyReturn->primaryKey => $id));
		$this->set('dailyReturn', $this->DailyReturn->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {	
			
			$daily_return_id=(((string)Configure::read('foxId')).''.((string)date('Ymd')));			
			$this->request->data['DailyReturn']['id'] = $daily_return_id;
			$this->request->data['DailyReturn']['daily_buying_return_id'] = $daily_return_id;
			$this->request->data['DailyReturn']['daily_selling_return_id'] = $daily_return_id;
			$this->request->data['DailyReturn']['fox_id'] = Configure::read('foxId');
			$this->request->data['DailyReturn']['date'] = date('Y-m-d');
			$this->request->data['DailyReturn']['user_id'] = $this->Auth->User('id');
			$this->request->data['DailyReturn']['name'] = $this->Auth->User('name');			
			$this->request->data['DailyBuyingReturn']['id'] = $daily_return_id;
			$this->request->data['DailyBuyingReturn']['date'] = date('Y-m-d');
			$this->request->data['DailyBuyingReturn']['fox_id'] = Configure::read('foxId');
			$this->request->data['DailyBuyingReturn']['daily_return_id'] = $daily_return_id;
			$this->request->data['DailySellingReturn']['id'] = $daily_return_id;
			$this->request->data['DailySellingReturn']['date'] = date('Y-m-d');
			$this->request->data['DailySellingReturn']['fox_id'] = Configure::read('foxId');
			$this->request->data['DailySellingReturn']['daily_return_id'] = $daily_return_id;
			
			$this->DailyReturn->create();
			if ($this->DailyReturn->save($this->request->data)) {
				$this->DailyBuyingReturn->save($this->request->data);
				$this->DailySellingReturn->save($this->request->data);
				$this->Session->setFlash(__('Saved'),'flash_success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The could not be saved. Please, try again.'),'flash_error');
			}
		}
		$foxes = $this->DailyReturn->Fox->find('list');
		$currencies=$this->Currency->find('all',array(
			'recursive'=>-1,
			'conditions'=>array(
				'NOT'=>[
					'Currency.id'=>['c00','c8']
				]
			),
			'order'=>'Currency.is_other_currency ASC, Currency.id ASC'
		));
		$this->set(compact('foxes','currencies'));
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		if (!$this->DailyReturn->exists($id)) {
			throw new NotFoundException(__('Invalid daily return'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->DailyReturn->save($this->request->data)) {
				$this->Session->setFlash(__('The daily return has been saved'),'flash_success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The daily return could not be saved. Please, try again.'),'flash_error');
			}
		} else {
			$options = array('conditions' => array('DailyReturn.' . $this->DailyReturn->primaryKey => $id));
			$this->request->data = $this->DailyReturn->find('first', $options);
		}
		$foxes = $this->DailyReturn->Fox->find('list');
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
		$this->DailyReturn->id = $id;
		if (!$this->DailyReturn->exists()) {
			throw new NotFoundException(__('Invalid daily return'));
		}
		if ($this->DailyReturn->delete()) {
			$this->Session->setFlash(__('Daily return deleted'),'flash_success');
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Daily return was not deleted'),'flash_error');
		$this->redirect(array('action' => 'index'));
	}
}
