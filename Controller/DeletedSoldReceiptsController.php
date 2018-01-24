<?php
App::uses('AppController', 'Controller');
/**
 * DeletedSoldReceipts Controller
 *
 * @property DeletedSoldReceipt $DeletedSoldReceipt
 */
class DeletedSoldReceiptsController extends AppController {
	public $uses = array('DeletedSoldReceipt','SoldReceipt');
	
	function beforeFilter() {
        parent::beforeFilter();
        if ($this->action == 'index' || $this->action == 'view' || $this->action == 'delete') {
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
		$this->DeletedSoldReceipt->recursive = 0;
		$this->paginate=array('order'=>'DeletedSoldReceipt.date desc');
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
							'DeletedSoldReceipt.id LIKE' => '%' . $_REQUEST['search_query_string'] . '%',
							'DeletedSoldReceipt.customer_name LIKE' => '%' . $_REQUEST['search_query_string'] . '%'
						)
					),
					'order' => array('DeletedSoldReceipt.date' => 'desc'),
					'limit'=>200
				);
				
			}else{
				$this->paginate=array('conditions'=>array('DeletedSoldReceipt.date >='=>$from,'DeletedSoldReceipt.date <='=>$to),'order'=>'DeletedSoldReceipt.date desc','limit'=>200);
			}
			
			if($large_cash){
				
				//get Average Rate for Dollar
				$dollar_av_rate=$this->DeletedSoldReceipt->find('all',array(
					'recursive'=>-1,
					'conditions'=>array(
						'DeletedSoldReceipt.currency_id'=>'c1',
						'DeletedSoldReceipt.date >='=>$from,
						'DeletedSoldReceipt.date <='=>$to
					),
					'fields'=>array(
						'SUM(DeletedSoldReceipt.amount) as total_amount',
						'SUM(DeletedSoldReceipt.amount_ugx) as total_amount_ugx'
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
						'DeletedSoldReceipt.date >='=>$from,
						'DeletedSoldReceipt.date <='=>$to,
						'DeletedSoldReceipt.amount_ugx >='=>$max_dollar_ugx
					),
					'order'=>'DeletedSoldReceipt.date desc',
					'limit'=>0
				);
				$this->set('dollar_av_rate', $dollar_av_rate);
				$this->set('large_cash', $large_cash);
				
			}
		}
		$this->set('deletedSoldReceipts', $this->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->DeletedSoldReceipt->exists($id)) {
			throw new NotFoundException(__('Invalid sales receipt'));
		}
		$options = array('conditions' => array('DeletedSoldReceipt.' . $this->DeletedSoldReceipt->primaryKey => $id));
		$this->set('deletedSoldReceipt', $this->DeletedSoldReceipt->find('first', $options));
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
		$this->DeletedSoldReceipt->id = $id;
		if (!$this->DeletedSoldReceipt->exists()) {
			throw new NotFoundException(__('Invalid sales receipt'));
		}
		
		if ($this->DeletedSoldReceipt->delete()) {
			$this->Session->setFlash(__('Sales receipt permanently deleted'),'flash_success');			
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Sales receipt was not permanently deleted. Please try again.'),'flash_error');
		$this->redirect(array('action' => 'index'));
	}
	
	public function return_back($id = null) {
		$this->DeletedSoldReceipt->id = $id;
		if (!$this->DeletedSoldReceipt->exists()) {
			throw new NotFoundException(__('Invalid sales receipt'));
		}
		$_DeletedSoldReceipt = $this->DeletedSoldReceipt->find('first',array('recursive'=>-1,'conditions'=>array('DeletedSoldReceipt.id'=>$id)));
		if (!empty($_DeletedSoldReceipt) && $this->SoldReceipt->save(array(
			'SoldReceipt'=>$_DeletedSoldReceipt['DeletedSoldReceipt']
		))) {
			$this->DeletedSoldReceipt->delete();
			$this->Session->setFlash(__('Returned receipt.'),'flash_success');			
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Sales receipt was not returned. Make sure it exists. Please try again.'),'flash_error');
		$this->redirect(array('action' => 'index'));
	}
}
