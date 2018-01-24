<?php
App::uses('AppController', 'Controller');
/**
 * Contacts Controller
 *
 * @property Contact $Contact
 */
class ContactsController extends AppController {
	
	function beforeFilter() {
        parent::beforeFilter();		
        if ($this->action == 'send_sms' || 
			$this->action == 'send_backup' ||
			$this->action == 'edit' ||
			$this->action == 'delete') {
			if($this->Auth->user('role')!='super_admin'){
				$this->Session->setFlash(__('Access Denied!!'));
				$this->redirect($this->Auth->logout());
			}
        }
    } 
	
	public function send_sms($id){				
		$response_array=array();
		if ($this->request->is('post')) {
			$contacts['Contacts']=$this->Contact->find('all',array('conditions'=>array('Contact.contact_list_id'=>$id),'recursive'=>-1));
			//send all the contacts to the service provider of the bulk SMS.
			if(count($contacts) && isset($this->request->data['SMS']['msg']) && !empty($this->request->data['SMS']['msg'])){
				$contacts['msg']=$this->request->data['SMS']['msg'];
				$resting=new $this->Resting;
				$_fox=($this->Session->read('fox'));
				$resting->api_username=$_fox['Fox']['un'];
				$resting->api_password=$_fox['Fox']['pwd'];
				$resting->authorisation_key=$_fox['Fox']['k'];
				$resting->url = $_fox['Fox']['url'];
				$response=$resting->XML_fetch_data('/sms_services/sms_me.json','<Contacts>'.(json_encode($contacts)).'</Contacts>');
				if($resting->has_response){
					$response_array_full=json_decode($response);
					$response_array=array();
					if(isset($response_array_full->data->response)){
						array_push($response_array,$response_array_full->data->response);
					}else{
						array_push($response_array," Remote is server under maintenance, or it could not log you in.");
					}
	
					if(isset($response_array_full->data->response->msgs)){
						array_push($response_array,$response_array_full->data->response->msgs);
					}
				}else{
					$response_array=array("Request failed. Check your internet connection!! Thanks.");
				}
			}else{
				$response_array=array("No contacts found for the contact list. Or no message provided.");
			}
			
			if(count($response_array)){
				$result='';
				foreach($response_array as $resp){				
					if(isset($resp->msgs)){
						foreach($resp->msgs as $msg){
							$result.=$msg.'.';
						}
					}
				}
				if(!strlen($result))
					$this->Session->setFlash(__("Not sent."),'flash_error');
				else
					$this->Session->setFlash(__($result),'flash_error');
			}else{
				$this->Session->setFlash(__("Not sent."),'flash_error');
			}
			
		}else{
			$this->Session->setFlash(__("Compose your message."),'flash_info');
		}
		
		$contacts_found=$this->Contact->find('count',array('conditions'=>array('Contact.contact_list_id'=>$id)));
		$this->set('contacts_found', $contacts_found);
		
	}
	
	public function send_backup($id){
		
	}
	
/**
 * index method
 *
 * @return void
 */
	public function index($id=null) {
		$this->Contact->recursive = 0;
		if($id)
			$this->paginate=array('conditions'=>array('Contact.contact_list_id'=>$id));
		$this->set('contacts', $this->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->Contact->exists($id)) {
			throw new NotFoundException(__('Invalid contact'));
		}
		$options = array('conditions' => array('Contact.' . $this->Contact->primaryKey => $id));
		$this->set('contact', $this->Contact->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function add($id=null) {
		if ($this->request->is('post')) {
			if(!isset($this->request->data['Contact']['contact_list_id']) || empty($this->request->data['Contact']['contact_list_id'])){
				$this->Session->setFlash(__('Select or create a contact list first and try again.'),'flash_warning');
				$this->redirect(array('action' => 'add'));
			}
			$func=$this->Func;
			$this->request->data['Contact']['id']=$func->getUID1();
			date_default_timezone_set('Africa/Nairobi');
			$this->request->data['Contact']['date']=date('Y-m-d H:i:s');
			
			$this->Contact->create();
			if ($this->Contact->save($this->request->data)) {
				$this->Session->setFlash(__('The contact has been saved'),'flash_success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The contact could not be saved. Please, try again.'),'flash_error');
			}
		}
	
		if($id){
			$contactLists = $this->Contact->ContactList->find('list',array('conditions'=>array('ContactList.id'=>$id),'limit'=>1));
		}else{
			$contactLists = $this->Contact->ContactList->find('list');
		}
		$this->set(compact('contactLists'));
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		if (!$this->Contact->exists($id)) {
			throw new NotFoundException(__('Invalid contact'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Contact->save($this->request->data)) {
				$this->Session->setFlash(__('The contact has been saved'),'flash_success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The contact could not be saved. Please, try again.'),'flash_error');
			}
		} else {
			$options = array('conditions' => array('Contact.' . $this->Contact->primaryKey => $id));
			$this->request->data = $this->Contact->find('first', $options);
		}
		$contactLists = $this->Contact->ContactList->find('list');
		$this->set(compact('contactLists'));
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
		$this->Contact->id = $id;
		if (!$this->Contact->exists()) {
			throw new NotFoundException(__('Invalid contact'));
		}
		if ($this->Contact->delete()) {
			$this->Session->setFlash(__('Contact deleted'),'flash_success');
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Contact was not deleted'),'flash_error');
		$this->redirect(array('action' => 'index'));
	}
}
