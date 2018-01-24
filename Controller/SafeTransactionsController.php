<?php
App::uses('AppController', 'Controller');
class SafeTransactionsController extends AppController {

	function beforeFilter() {
        parent::beforeFilter();
    }
	
	public function index($transactionId=null) {
		$conditions = [];
		if(!empty($transactionId)){
			$conditions['SafeTransaction.id'] = $transactionId;
		}
		if(!in_array($this->Auth->User('role'), ['super_admin'])){
			$conditions['OR']['SafeTransaction.user_id'] = $this->Auth->User('id');
			$conditions['OR']['SafeTransaction.transaction_to'] = $this->Auth->User('id');
		}

		if(	isset($_REQUEST['date_from']) &&
			isset($_REQUEST['date_to'])){
			
			$from	=($_REQUEST['date_from']);
			$to	=($_REQUEST['date_to']);
			
			$this->set('from', $from);
			$this->set('to', $to);
			$conditions['DATE(SafeTransaction.date) >='] = $from;
			$conditions['DATE(SafeTransaction.date) <='] = $to;
		}

		// Other filters by user ids
		if (isset($_REQUEST['transaction_from'])) {
			$conditions['SafeTransaction.transaction_from'] = $_REQUEST['transaction_from'];
		}

		if (isset($_REQUEST['transaction_to'])) {
			$conditions['SafeTransaction.transaction_to'] = $_REQUEST['transaction_to'];
		}	

		if (isset($_REQUEST['approved_by'])) {
			$conditions['SafeTransaction.approved_by'] = $_REQUEST['approved_by'];
		}		
		

		$this->SafeTransaction->recursive = 0;
		$this->paginate = [
			'conditions'=>$conditions,
			'order'=>'SafeTransaction.status DESC,SafeTransaction.date DESC'
		];
		$this->set('safeTransactions', $this->paginate());
		$this->set('transactionId',$transactionId);
	}

	public function edit($id) {
		if (!$this->SafeTransaction->exists($id)) {
			$this->Session->setFlash(__('Transaction Not Found!'),'flash_warning');
			$this->redirect(array('action'=>'index'));
		}

		if(!in_array($this->Auth->User('role'), ['super_admin'])){
			$this->Session->setFlash(__('Access denied!'),'flash_error');
			$this->redirect(array('action'=>'index'));
		}
		
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->SafeTransaction->save($this->request->data)) {
				$this->Session->setFlash(__('Saved'),'flash_success');
				$this->redirect(array('action' => 'index',$id));
			} else {
				$this->Session->setFlash(__('Error updating transaction. Please, try again.'),'flash_error');
				$this->redirect(array('action' => 'index',$id));
			}
		} else {
			$options = array('recursive'=>-1,'conditions' => array('SafeTransaction.' . $this->SafeTransaction->primaryKey => $id));
			$this->request->data = $this->SafeTransaction->find('first', $options);
		}
	}

	public function view($transactionId = null)
	{
		$safeTransaction = $this->SafeTransaction->find('first',[
			'conditions'=>[
				'SafeTransaction.id'=>$transactionId
			],
			'recursive'=>0,
		]);
		$this->set('safeTransaction',$safeTransaction);
	}

	public function accept($transactionId = null)
	{
		$safeTransaction = $this->SafeTransaction->find('first',[
			'conditions'=>[
				'SafeTransaction.id'=>$transactionId
			],
			'recursive'=>0,
			'fields'=>[
				'SafeTransaction.id',
				'SafeTransaction.amount',
				'SafeTransaction.currency',
				'SafeTransaction.transaction_from',
				'SafeTransaction.transaction_to',
				'User.name','User.id',
				'To.name','To.id'
			]
		]);
		
		if($safeTransaction['SafeTransaction']['transaction_to']!=$this->Auth->User('id'))
		{
			$this->Session->setFlash(__('Access denied!'),'flash_error');
			$this->redirect(array('action'=>'index'));
		}

		if(!empty($safeTransaction)){

			if($this->SafeTransaction->save(['SafeTransaction'=>[
					'id'=>$safeTransaction['SafeTransaction']['id'],
					'status'=>'ACCEPTED',
					'accepted_at'=>date('Y-m-d H:i:s')
			]]))
			{
				$this->Session->setFlash(__('Transaction accepted.'));
				$cur = $safeTransaction['SafeTransaction']['currency'];
				$this->SafeTransaction->User->Notification->msg(
					$safeTransaction['SafeTransaction']['transaction_from']," accepted " . number_format($safeTransaction['SafeTransaction']['amount']) . ' ' . $cur[0] . ' from you',
					null,'/safe_transactions/index/'.$safeTransaction['SafeTransaction']['id'],$safeTransaction['SafeTransaction']['transaction_to']
				);
			}else{
				$this->Session->setFlash(__('Error approving transaction.'),'flash_error');
			}
		}else{
			$this->Session->setFlash(__('Transaction not found'),'flash_error');
		}
		$this->redirect(array('action'=>'index'));
	}

	
	public function approve($transactionId = null)
	{
		if(!in_array($this->Auth->User('role'), ['super_admin']))
		{
			$this->Session->setFlash(__('Access denied!'),'flash_error');
			$this->redirect(array('action'=>'index'));
		}

		$safeTransaction = $this->SafeTransaction->find('first',[
			'conditions'=>[
				'SafeTransaction.id'=>$transactionId
			],
			'recursive'=>0,
			'fields'=>[
				'SafeTransaction.id',
				'SafeTransaction.amount',
				'SafeTransaction.currency',
				'SafeTransaction.transaction_from',
				'SafeTransaction.transaction_to',
				'User.name','User.id',
				'To.name','To.id'
			]
		]);
		
		if(!empty($safeTransaction)){

			if($this->SafeTransaction->save(['SafeTransaction'=>[
					'id'=>$safeTransaction['SafeTransaction']['id'],
					'status'=>'APPROVED',
					'approved_at'=>date('Y-m-d H:i:s'),
					'approved_by'=>$this->Auth->User('id')
			]]))
			{
				$this->Session->setFlash(__('Transaction approved.'),'flash_success');
				$cur = explode('-',$safeTransaction['SafeTransaction']['currency']);
				$this->SafeTransaction->User->Notification->msg(
					$safeTransaction['SafeTransaction']['transaction_to']," sent you " . number_format($safeTransaction['SafeTransaction']['amount']) . ' ' . $cur[0],
					null,'/safe_transactions/index/'.$safeTransaction['SafeTransaction']['id'],$safeTransaction['SafeTransaction']['transaction_from']
				);
			}else{
				$this->Session->setFlash(__('Error approving transaction.'),'flash_error');
			}
		}else{
			$this->Session->setFlash(__('Transaction not found'),'flash_error');
		}
		$this->redirect(array('action'=>'index'));
	}

	public function cancel($transactionId = null)
	{
		if(!in_array($this->Auth->User('role'), ['super_admin']))
		{
			$this->Session->setFlash(__('Access denied!'),'flash_error');
			$this->redirect(array('action'=>'index'));
		}

		$safeTransaction = $this->SafeTransaction->find('first',[
			'conditions'=>[
				'SafeTransaction.id'=>$transactionId
			],
			'recursive'=>0,
			'fields'=>[
				'SafeTransaction.id',
				'SafeTransaction.amount',
				'SafeTransaction.currency',
				'SafeTransaction.transaction_from',
				'SafeTransaction.transaction_to',
				'To.name'
			]
		]);

		if(!empty($safeTransaction)){
			$this->SafeTransaction->id = $safeTransaction['SafeTransaction']['id'];
			if($this->SafeTransaction->delete())
			{
				$cur = explode('-',$safeTransaction['SafeTransaction']['currency']);
				$this->Session->setFlash(__('Transaction removed'),'flash_success');
				$this->SafeTransaction->User->Notification->msg(
					$safeTransaction['SafeTransaction']['transaction_from']," dis-approved sending " . number_format($safeTransaction['SafeTransaction']['amount']) . ' ' . $cur[0] . ' to '.$safeTransaction['To']['name'],
					null,'/safe_transactions/index',$this->Auth->User('id')
				);
			}else{
				$this->Session->setFlash(__('Error while removing transaction.'),'flash_error');
			}
		}else{
			$this->Session->setFlash(__('Transaction not found'),'flash_error');
		}

		$this->redirect(array('action'=>'index'));
	}
}
