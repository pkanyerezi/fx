<?php
App::uses('AppController', 'Controller');
/**
 * DeletedPurchasedReceipts Controller
 *
 * @property DeletedPurchasedReceipt $DeletedPurchasedReceipt
 */
class DeletedPurchasedReceiptsController extends AppController {
	public $uses = array('DeletedPurchasedReceipt','PurchasedReceipt');
	
	function beforeFilter() {
        parent::beforeFilter();
        if ($this->action == 'view' || $this->action == 'index' || $this->action == 'delete') {
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
	public function index($large_cash=null) {
		$this->DeletedPurchasedReceipt->recursive = 0;
		$this->paginate=array('order'=>'DeletedPurchasedReceipt.date desc');
		if(	isset($_REQUEST['date_from']) &&
			isset($_REQUEST['date_to'])){
			
			$from	=($_REQUEST['date_from']);
			$to	=($_REQUEST['date_to']);
			
			$this->set('from', $from);
			$this->set('to', $to);
			
			if(isset($_REQUEST['search_query_string']) && !empty($_REQUEST['search_query_string'])){					
				$this->paginate = array(
					'conditions' => array(
						'OR' => array(
							'DeletedPurchasedReceipt.id LIKE' => '%' . $_REQUEST['search_query_string'] . '%',
							'DeletedPurchasedReceipt.customer_name LIKE' => '%' . $_REQUEST['search_query_string'] . '%'
						)
					),
					'order' => array('DeletedPurchasedReceipt.date' => 'desc'),
					'limit'=>200
				);
				
			}else{
				$this->paginate=array('conditions'=>array('DeletedPurchasedReceipt.date >='=>$from,'DeletedPurchasedReceipt.date <='=>$to),'order'=>'DeletedPurchasedReceipt.date desc','limit'=>200);
			}
			
			if($large_cash){
				
				//get Average Rate for Dollar
				$dollar_av_rate=$this->DeletedPurchasedReceipt->find('all',array(
					'recursive'=>-1,
					'conditions'=>array(
						'DeletedPurchasedReceipt.currency_id'=>'c1',
						'DeletedPurchasedReceipt.date >='=>$from,
						'DeletedPurchasedReceipt.date <='=>$to
					),
					'fields'=>array(
						'SUM(DeletedPurchasedReceipt.amount) as total_amount',
						'SUM(DeletedPurchasedReceipt.amount_ugx) as total_amount_ugx'
					)
				));
				
				
				if(isset($dollar_av_rate[0][0]['total_amount_ugx'])){
					$dollar_av_rate=($dollar_av_rate[0][0]['total_amount_ugx']!=0)?($dollar_av_rate[0][0]['total_amount_ugx']/$dollar_av_rate[0][0]['total_amount']):2400;
				}else{
					$dollar_av_rate=2400;
				}
				
				
				
				$max_dollar_ugx=5000*$dollar_av_rate;
				
				$this->paginate=array(
					'conditions'=>array(
						'DeletedPurchasedReceipt.date >='=>$from,
						'DeletedPurchasedReceipt.date <='=>$to,
						'DeletedPurchasedReceipt.amount_ugx >='=>$max_dollar_ugx					
					),
					'order'=>'DeletedPurchasedReceipt.date desc',
					'limit'=>0
				);
				$this->set('large_cash', $large_cash);
				$this->set('dollar_av_rate', $dollar_av_rate);
			}
		}
		$this->set('deletedPurchasedReceipts', $this->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->DeletedPurchasedReceipt->exists($id)) {
			throw new NotFoundException(__('Invalid purchased receipt'));
		}
		$options = array('conditions' => array('DeletedPurchasedReceipt.' . $this->DeletedPurchasedReceipt->primaryKey => $id));
		$this->set('deletedPurchasedReceipt', $this->DeletedPurchasedReceipt->find('first', $options));
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
		$this->DeletedPurchasedReceipt->id = $id;
		if (!$this->DeletedPurchasedReceipt->exists()) {
			throw new NotFoundException(__('Invalid purchase receipt'));
		}
		
		if ($this->DeletedPurchasedReceipt->delete()) {
			$this->Session->setFlash(__('Deleted permanently'),'flash_success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Deleted Purchase receipt was not deleted permanently. Please try again.'),'flash_error');
		$this->redirect(array('action' => 'index'));
	}
	
	public function return_back($id = null) {
		$this->DeletedPurchasedReceipt->id = $id;
		if (!$this->DeletedPurchasedReceipt->exists()) {
			throw new NotFoundException(__('Invalid sales receipt'));
		}
		$_DeletedPurchasedReceipt = $this->DeletedPurchasedReceipt->find('first',array('recursive'=>-1,'conditions'=>array('DeletedPurchasedReceipt.id'=>$id)));
		if (!empty($_DeletedPurchasedReceipt) && $this->PurchasedReceipt->save(array(
			'PurchasedReceipt'=>$_DeletedPurchasedReceipt['DeletedPurchasedReceipt']
		))) {
			$this->DeletedPurchasedReceipt->delete();
			$this->Session->setFlash(__('Returned receipt.'),'flash_success');			
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Purchase receipt was not returned. Make sure it exists. Please try again.'),'flash_error');
		$this->redirect(array('action' => 'index'));
	}
}
