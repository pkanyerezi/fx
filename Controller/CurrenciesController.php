<?php
App::uses('AppController', 'Controller');
/**
 * Currencies Controller
 *
 * @property Currency $Currency
 */
class CurrenciesController extends AppController {
    
	public $uses = array('Currency','OtherCurrency','PurchasedReceipt','SoldReceipt');
	
	function beforeFilter() {
        parent::beforeFilter();		
        if (in_array($this->action,array('delete','match_currency_to_receipts_step1'))){
			if($this->Auth->user('role')!='super_admin'){
				$this->Session->setFlash(__('Access Denied!!'));
				$this->redirect($this->Auth->logout());
			}
        }
    }

    /**
     * This is the first step 
     *
     * @param $oldCurrency_id - This is the id of the currency in the OtherCurrency table we are going to convert
     */
    public function match_currency_to_receipts_step1($oldCurrency_id){

        if($this->request->is('post')){
            $currency_to = $this->request->data['Currency']['currency_id'];

            $this->Currency->query("
            UPDATE sold_receipts 
            SET 
                `currency_id` = '$currency_to',
                `amount` = `orig_amount`,
                `rate` = `orig_rate`
            WHERE 
                `other_currency_id` = '$oldCurrency_id'
            ");

            $this->Currency->query("
            UPDATE purchased_receipts 
            SET 
                `currency_id` = '$currency_to',
                `amount` = `orig_amount`,
                `rate` = `orig_rate`
            WHERE 
                `other_currency_id` = '$oldCurrency_id'
            ");

            $this->Session->setFlash(__('All Reciepts Updated to ' . $currency_to),'flash_success');
            $this->redirect(['action'=>'index','controller'=>'other_currencies']);
        }

        $oldCurrency = $this->OtherCurrency->find('first',['conditions'=>['OtherCurrency.id'=>$oldCurrency_id]]);
        $currencies = $this->Currency->find('list',[
                'limit'=>0,
                'recursive'=>-1,
                'conditions'=>[
                    'NOT'=>[
                        'id'=>['c00','c8']
                    ]
                ],
                'order'=>'id ASC'
        ]);
        $purchasedReceipts = $this->PurchasedReceipt->find('count',[
            'conditions'=>[
                'PurchasedReceipt.currency_id'=>'c8',
                'PurchasedReceipt.other_currency_id'=>$oldCurrency_id
            ]
        ]);
        $soldReceipts = $this->SoldReceipt->find('count',[
            'conditions'=>[
                'SoldReceipt.currency_id'=>'c8',
                'SoldReceipt.other_currency_id'=>$oldCurrency_id
            ]
        ]);
        $this->set(compact('oldCurrency','currencies','purchasedReceipts','soldReceipts'));
    }
	
	public function match_currency_to_receipts_step1_v2(){
		if($this->request->is('post')){
            //$currency_to = $this->request->data['Currency']['currency_id'];
				
			$indexTracker = -1;
			$testArray = [];
			foreach($this->request->data['Currency']['currency_to'] as $currency_to){
				$indexTracker++;
				$oldCurrency_id = $this->request->data['Currency']['currency_from'][$indexTracker];
				$testArray[] = [
					'From'=>$oldCurrency_id,
					'To'=>$currency_to
				];
				
				//continue;
				
				$this->Currency->query("
				UPDATE sold_receipts 
				SET 
					`currency_id` = '$currency_to',
					`amount` = `orig_amount`,
					`rate` = `orig_rate`
				WHERE 
					`other_currency_id` = '$oldCurrency_id'
				");

				$this->Currency->query("
				UPDATE purchased_receipts 
				SET 
					`currency_id` = '$currency_to',
					`amount` = `orig_amount`,
					`rate` = `orig_rate`
				WHERE 
					`other_currency_id` = '$oldCurrency_id'
				");
			}
	
			//$this->log($testArray,'opps');
			
            $this->Session->setFlash(__('done'),'flash_success');
            $this->redirect(['action'=>'index','controller'=>'other_currencies']);
        }

        //$oldCurrency = $this->OtherCurrency->find('first',['conditions'=>['OtherCurrency.id'=>$oldCurrency_id]]);
        $currencies = $this->Currency->find('list',[
                'limit'=>0,
                'recursive'=>-1,
				'conditions'=>['is_other_currency'=>1],
                // 'conditions'=>[
                    // 'NOT'=>[
                        // 'id'=>['c00','c8']
                    // ]
                // ],
                'order'=>'id ASC'
        ]);
		$otherCurrencies = $this->OtherCurrency->find('list',[
                'limit'=>0,
                'recursive'=>-1,
                'order'=>'id ASC'
        ]);
        $this->set(compact('otherCurrencies','currencies'));
	}
    
    public function currency_board(){
    	if($this->request->is('post')){
            if($this->Auth->user('role')!='super_admin')
            {
                $this->Session->setFlash(__('Access Denied!!'),'flash_error');
                $this->redirect(['action'=>'currency_board']);
            }
    		//Update currencies incase they are submitted by the super admin
    		$rates = array();
    		$this->Currency->recursive=-1;
			//$this->OtherCurrency->recursive=-1;
			$currencies = $this->Currency->find('all');
			//$otherCurrencies = $this->OtherCurrency->find('all');
    		foreach($currencies as $currency){
    			if(in_array($currency['Currency']['id'],array('c00','c8'))) continue;
    			array_push($rates,array('Currency'=>array('id'=>$currency['Currency']['id'],'buy'=>$this->request->data['Currency']['buy'][$currency['Currency']['id']],'sell'=>$this->request->data['Currency']['sell'][$currency['Currency']['id']])));
    		}
    		$this->Currency->saveAll($rates);
    		
    		/*$rates = array();
    		foreach($otherCurrencies as $currency){
    			array_push($rates,array('OtherCurrency'=>array('id'=>$currency['OtherCurrency']['id'],'buy'=>$this->request->data['OtherCurrency']['buy'][$currency['OtherCurrency']['id']],'sell'=>$this->request->data['OtherCurrency']['sell'][$currency['OtherCurrency']['id']])));
    		}
    		$this->OtherCurrency->saveAll($rates);
    		*/
    		$this->Session->setFlash(__('saved',true),'flash_success');
    	}
    	
    	$this->Currency->recursive=-1;
    	//$this->OtherCurrency->recursive=-1;
    	//$currencies = $this->Currency->find('all');
    	$currencies = $this->PurchasedReceipt->Currency->find('all',[
			'order'=>'Currency.is_other_currency ASC,Currency.arrangement ASC, Currency.id ASC',
		]);
		//$otherCurrencies = $this->OtherCurrency->find('all');
    	$this->set(compact('currencies','otherCurrencies'));
    }
	
/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->Currency->recursive = 0;
        $this->paginate = [
            'order'=>'Currency.is_other_currency ASC, Currency.id ASC',
            'conditions'=>['Currency.display'=>1]
        ];
		$this->set('currencies', $this->paginate());
	}

    public function add() {
        if ($this->request->is('post')) {
            $exists = $this->Currency->find('first',[
                'conditions'=>[
                    'OR'=>[
                        'Currency.id'=>$this->request->data['Currency']['id'],
                        'Currency.description'=>$this->request->data['Currency']['id'],
                    ]
                ]
            ]);

            if (!empty($exists)) {
                $this->Session->setFlash(__('Currency Exists as.' . $exists['Currency']['description']),'flash_warnig');
                $this->redirect(array('action' => 'add'));
            }
            $this->request->data['Currency']['description'] = $this->request->data['Currency']['name'];
            $this->request->data['Currency']['is_other_currency'] = 1;
            $this->Currency->create();
            if ($this->Currency->save($this->request->data)) {
                $this->Session->setFlash(__('The currency has been saved'),'flash_success');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The currency could not be saved. Please, try again.'),'flash_error');
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
        if (!$this->Currency->exists($id)) {
            throw new NotFoundException(__('Invalid currency'));
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->Currency->save($this->request->data)) {
                $this->Session->setFlash(__('The currency has been saved'),'flash_success');
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The currency could not be saved. Please, try again.'),'flash_error');
            }
        } else {
            $options = array('conditions' => array('Currency.' . $this->Currency->primaryKey => $id));
            $this->request->data = $this->Currency->find('first', $options);
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
        $this->Currency->id = $id;
        if (!$this->Currency->exists()) {
            throw new NotFoundException(__('Invalid other currency'));
        }
        // $this->request->onlyAllow('post', 'delete');
        if ($this->Currency->delete()) {
            $this->Session->setFlash(__('currency deleted'),'flash_success');
            $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(__('Currency was not deleted'),'flash_error');
        $this->redirect(array('action' => 'index'));
    }
}