<?php
App::uses('AppController', 'Controller');
/**
 * Openings Controller
 *
 * @property Opening $Opening
 */
class OpeningsController extends AppController {
	public $uses=array('Opening','User','OtherCurrency','Currency');
	function beforeFilter() {
        parent::beforeFilter();		
        if ($this->action == 'edit' ||
			$this->action == 'add' ||
			$this->action == 'upgrade_openings' ||
			$this->action == 'delete') {
			if($this->Auth->user('role')!='super_admin'){
				$this->Session->setFlash(__('Access Denied!!'),'flash_error');
				$this->redirect($this->Auth->logout());
			}
        }
    }

    public function upgrade_openings($page=0){

    	$limit = 50;
		$next = 'http://localhost/fx/openings/upgrade_openings/1';
    	$finish = 'http://localhost/fx/openings/upgrade_openings/2';
    	$step3 = 'http://localhost/fx/openings/upgrade_openings/3';
    	$step4 = 'http://localhost/fx/openings/upgrade_openings/4';
    	$step5 = 'http://localhost/fx/openings/upgrade_openings/5';

    	$openingsCount = $this->Opening->find('count',array(
    		'conditions'=>['Opening.opening_detail_id'=>0]
		));

    	if (empty($page)) {
    		Configure::write('debug',2);

    		echo "1. Make sure the <b>Other Currencies</b> have already been migrated<br>";
    		echo "2. $openingsCount Records to fix!";
    		echo "<br>";
    		echo "<a href=\"$next\">Start upgrading</a>";
    		exit();
    	}

    	
    	
		$openings = $this->Opening->find('all',array(
			'conditions'=>['Opening.opening_detail_id'=>0],
			'page'=>$page,
			'limit'=>$limit,
			'recursive'=>0,
			'order'=>'Opening.date DESC'
		));

		// pr($openings[0]['OpeningDetail']);

		// exit();

		$currencies=$this->Currency->find('all',array(
			'conditions'=>array(
				'Currency.is_other_currency'=>0,
				'NOT'=>[
					'Currency.id'=>['c00','c8']
				]
			),
			'order'=>'Currency.is_other_currency ASC, Currency.id ASC',
			'limit'=>0,
			'recursive'=>-1
		));

		$other_currencies=$this->Currency->find('all',array(
			'conditions'=>array(
				'Currency.is_other_currency'=>1,
			),
			'order'=>'Currency.is_other_currency ASC, Currency.id ASC',
			'limit'=>0,
			'recursive'=>-1
		));

		$upgradedCount = 0;
		$upgradedCountSuccess = 0;
		$upgradedCountFail = 0;

		// pr(count($openings));
		// pr(count($openingsCount));
        foreach ($openings as $opening) {

        	// pr($opening);
        	// exit();
        	//if (empty($opening['OpeningDetail'])) {
        		$data = [];
        		$data['opening_id'] = $opening['Opening']['id'];
        		
        		$details = [];
        		foreach($currencies as $currency){
        			$detail = [];
        			$detail['CID'] = $this->getNewCurrencyID($currency['Currency']['id']);
	        		@$detail['CAMOUNT'] = $opening['Opening'][$currency['Currency']['id'].'a'];
	        		@$detail['CRATE'] = $opening['Opening'][$currency['Currency']['id'].'r'];

	        		$detail['CAMOUNT'] = empty($detail['CAMOUNT'])?0:$detail['CAMOUNT'];
	        		$detail['CRATE'] = empty($detail['CRATE'])?0:$detail['CRATE'];

	        		$details[$detail['CID']] = $detail;
        		}

        		$_data_other_currencies=json_decode($opening['Opening']['other_currencies'],true);
				$currencies_added = [];
        		foreach ($_data_other_currencies['data'] as $value) {
        			$currencies_added[] = $value['CID'];
        			$detail = [];
					$detail['CID'] = $value['CID'];
	        		@$detail['CAMOUNT'] = $value['CAMOUNT'];
	        		@$detail['CRATE'] = $value['CRATE'];
	        		@$detail['CAMOUNT'] = $value['CAMOUNT'];
	        		@$detail['CRATE'] = $value['CRATE'];
	        		
	        		@$detail['SENT'] = $value['SENT'];
	        		@$detail['RECEIVED'] = $value['RECEIVED'];

	        		$detail['CAMOUNT'] = empty($detail['CAMOUNT'])?0:$detail['CAMOUNT'];
	        		$detail['CRATE'] = empty($detail['CRATE'])?0:$detail['CRATE'];
					
	        		$details[$value['CID']] = $detail;
				}
					
				// $this->log($details,'ops');
				// exit;
				
				foreach($other_currencies as $currency){
					if (!in_array($currency['Currency']['id'], $currencies_added)) {
						$detail = [];
						$detail['CID'] = $currency['Currency']['id'];
		        		$detail['CAMOUNT'] = 0;
		        		$detail['CRATE'] = 0;
		        		$details[$currency['Currency']['id']] = $detail;
					}
				}

				$data['currency_details'] = json_encode($details);

				if (!empty($opening['OpeningDetail']['id'])) {
					$data['id'] = $opening['OpeningDetail']['id'];
				}else{
					$lastRecord = $this->Opening->OpeningDetail->find('first',['order'=> 'OpeningDetail.id DESC']);
					if (empty($lastRecord)) {
						$data['id'] = 1;
					}else{
						$data['id'] = $lastRecord['OpeningDetail']['id'] + 1;
					}
				}
				
				//$this->log(json_decode($data['currency_details'],true),'ops');
				//exit;

				// Save OpeningDetail
				if ($this->Opening->OpeningDetail->save(['OpeningDetail'=>$data])) {
					$update = [
    					'Opening'=>[
    						'id'=>$opening['Opening']['id'],
    						'opening_detail_id'=>$data['id']
    					]
    				];

        			// Mark Opening as upgraded
        			$this->Opening->save($update);

        			$upgradedCountSuccess++;
        		}else{
        			$upgradedCountFail++;
        		}
        		$upgradedCount++;
        	//}
        }

        if ($openingsCount) {
    		echo "<b>".($openingsCount-$upgradedCountSuccess)."</b> Records Remaining!  <b>$upgradedCount</b> Processed";
    		echo "<br><br>";
    		echo "<b>$upgradedCountSuccess</b> - Succeessful, <b>$upgradedCountFail</b> failed";
    		echo "<br><br>";
    		echo "<a href=\"$next\">Continue</a>";

    		echo '<script>setTimeout(function(){window.location="'.$next.'";},2000);</script>';
    		exit();
    	}else{
    		Configure::write('debug',2);
    		if ($page==2) {
    			try{
    				$this->Opening->query("ALTER TABLE `openings` DROP `c1a` , DROP `c1r` , DROP `c2a`, DROP `c2r`, DROP `c3a`, DROP `c3r`, DROP `c4a`, DROP `c4r`, DROP `c5a`, DROP `c5r`, DROP `c6a`, DROP `c6r`, DROP `c7a`, DROP `c7r`, DROP `c8a`, DROP `c8r`, DROP `other_currencies`;");

    				
    			}catch(Exception $e){
 					// ignore
    			}
    			echo "Congs!! Please clean the <b>tmp/cache</b> files for DB consistency";
    			echo "<br><br>";
    			echo "<a href=\"$step3\">Convert Major Currencies</a>";
    			echo "<br><br>";
    			echo "UPDATE `currencies` SET `id` = 'USD' WHERE `currencies`.`id` = 'c1';<br>
					UPDATE `currencies` SET `id` = 'EUR' WHERE `currencies`.`id` = 'c2';<br>
					UPDATE `currencies` SET `id` = 'GBP' WHERE `currencies`.`id` = 'c3';<br>
					UPDATE `currencies` SET `id` = 'KES' WHERE `currencies`.`id` = 'c4';<br>
					UPDATE `currencies` SET `id` = 'TZS' WHERE `currencies`.`id` = 'c5';<br>
					UPDATE `currencies` SET `id` = 'ZAR' WHERE `currencies`.`id` = 'c6';<br>
					UPDATE `currencies` SET `id` = 'SP' WHERE `currencies`.`id` = 'c7';<br><br><br>
					DELETE FROM `currencies` WHERE id='c8';<br>
					DELETE FROM `currencies` WHERE id='c00';<br>";
    		}elseif ($page==3){ 
    			$this->Opening->query("
    				UPDATE `currencies` SET `id` = 'USD' WHERE `currencies`.`id` = 'c1';
					UPDATE `currencies` SET `id` = 'EUR' WHERE `currencies`.`id` = 'c2';
					UPDATE `currencies` SET `id` = 'GBP' WHERE `currencies`.`id` = 'c3';
					UPDATE `currencies` SET `id` = 'KES' WHERE `currencies`.`id` = 'c4';
					UPDATE `currencies` SET `id` = 'TZS' WHERE `currencies`.`id` = 'c5';
					UPDATE `currencies` SET `id` = 'ZAR' WHERE `currencies`.`id` = 'c6';
					UPDATE `currencies` SET `id` = 'SP' WHERE `currencies`.`id` = 'c7';
					DELETE FROM `currencies` WHERE id='c8';
					DELETE FROM `currencies` WHERE id='c00';
				");
				echo "---Done---";
				echo "<br>";
				echo "<a href=\"$step4\">Clean Transactions to match new currencies</a>";
				
    		}elseif ($page==4){

				// safe_transactions***

				$tables = [
					'cash_at_bank_foreigns',
					'deleted_purchased_receipts',
					'deleted_sold_receipts',
					'purchased_receipts',
					'sold_receipts'
				];
				foreach ($currencies as $currency) {
					foreach ($tables as $table) {
						$this->Opening->query("
		    				UPDATE `$table` SET `currency_id` = '" . $currency['Currency']['id'] . "' 
		    				WHERE `currency_id` = '" . $this->getOldCurrencyID($currency['Currency']['id']) . "';
						");
					}

					$this->Opening->query("
						UPDATE safe_transactions
						SET currency = REPLACE(currency, '-" . $this->getOldCurrencyID($currency['Currency']['id']) . "', '-" . $currency['Currency']['id'] . "')
						WHERE currency LIKE '%-" . $this->getOldCurrencyID($currency['Currency']['id']) . "%'
					");
				}
				echo "Finally Currency <b>SP</b> should be converted to <b>OtherCurrencies</b><br>";
				echo "Reason BOU nolonger considers it under major currencies in Excel files<br><br>";
				echo "<a href=\"$step5\">Convert <b>SP</b></a>";
    		}elseif($page==5){
    			$this->Opening->query("
    				UPDATE `currencies` SET `is_other_currency` = 1
    				WHERE `id` = 'SP';
				");
    			echo "---End---";
    		}else{
    			echo "Finished!!";
    			echo "<br><br>";
    			echo "<a href=\"$finish\">Removed Unnecessay Fields</a>";
    		}	
    	}
        exit();
    }

    private function getNewCurrencyID($oldID){
    	switch ($oldID) {
    		case 'c1':
    			return 'USD';
    			break;
    		case 'c2':
    			return 'EUR';
    			break;
    		case 'c3':
    			return 'GBP';
    			break;
    		case 'c4':
    			return 'KES';
    			break;
    		case 'c5':
    			return 'TZS';
    			break;
    		case 'c6':
    			return 'ZAR';
    			break;
    		case 'c7':
    			return 'SP';
    			break;
    	}
    }

    private function getOldCurrencyID($newID){
    	switch ($newID) {
    		case 'USD':
    			return 'c1';
    			break;
    		case 'EUR':
    			return 'c2';
    			break;
    		case 'GBP':
    			return 'c3';
    			break;
    		case 'KES':
    			return 'c4';
    			break;
    		case 'TZS':
    			return 'c5';
    			break;
    		case 'ZAR':
    			return 'c6';
    			break;
    		case 'SP':
    			return 'c7';
    			break;
    	}
    }

/**
 * index method
 *
 * @return void
 */
	public function index($user_id = null) {
		$this->Opening->recursive = 0;
		$conditions = [];
		
		if(!empty($user_id))
			$conditions = ['Opening.user_id'=>$user_id];

		$this->paginate=array('order'=>'Opening.date desc','conditions'=>$conditions);
		if(	isset($_REQUEST['date_from']) &&
			isset($_REQUEST['date_to'])){
			
			$from	=($_REQUEST['date_from']);
			$to	=($_REQUEST['date_to']);
			
			$this->set('from', $from);
			$this->set('to', $to);
			if(empty($user_id)){
				$conditions['Opening.date >='] = $from;
				$conditions['Opening.date <='] = $to;
			}
			$this->paginate=array(
				'conditions'=>$conditions,
				'order'=>'Opening.date desc',
				// 'limit'=>3
			);
		}

		$currencies=$this->Currency->find('all',array(
			'conditions'=>array(
				'NOT'=>[
					'Currency.id'=>['c00','c8']
				]
			),
			'order'=>'Currency.is_other_currency ASC, Currency.id ASC',
			'limit'=>0,
			'recursive'=>-1,
			'fields'=>[
				'Currency.id'
			]
		));
		$this->set('currencies',$currencies);
		$this->set('openings', $this->paginate());
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->Opening->exists($id)) {
			throw new NotFoundException(__('Invalid opening'));
		}
		$options = array('conditions' => array('Opening.' . $this->Opening->primaryKey => $id));
		$this->set('opening', $this->Opening->find('first', $options));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {

		$currencies=$this->Currency->find('all',array('recursive'=>-1,
			'conditions'=>array(
				'NOT'=>[
					'Currency.id'=>['c00','c8']
				]
			),
			'order'=>'Currency.is_other_currency ASC, Currency.id ASC',
			'limit'=>0
		));

		if ($this->request->is('post')) {
			$func=$this->Func;
			$this->request->data['Opening']['id']=$func->getUID1();
			
			$data = [];
    		$currency_details = [];
    		foreach($currencies as $currency){
    			$detail['CID'] = $currency['Currency']['id'];
        		$detail['CAMOUNT'] = $this->request->data['Opening'][($currency['Currency']['id']).'a'];
        		$detail['CRATE'] = $this->request->data['Opening'][($currency['Currency']['id']).'r'];

        		$currency_details[$currency['Currency']['id']] = $detail;
    		}

    		// set OpeningDetail details
			$data['currency_details'] = json_encode($currency_details);
			$data['id'] = $opening['OpeningDetail']['id'];

			// Save OpeningDetail
			if ($this->Opening->OpeningDetail->save(['OpeningDetail'=>$data])) {
				$this->request->data['Opening']['opening_detail_id'] = $this->Opening->OpeningDetail->getLastInsertID();
				$this->Opening->create();
				if ($this->Opening->save($this->request->data)) {
					$this->Session->setFlash(__('The opening has been saved'),'flash_success');
					$this->redirect(array('action' => 'index'));
				} else {
					$this->Session->setFlash(__('The opening could not be saved. Please, try again.'),'flash_error');
				}
			}else{
				$this->Session->setFlash(__('Error saving Currency details. Please, try again.'),'flash_error');
			}
		}
		$users = $this->Opening->User->find('list',array(
			'conditions'=>array(
				'NOT'=>array(
					'User.role'=>array('customer','super_admin')
				)
			)
		));

		$this->set(compact('users','currencies'));
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id,$user_id) {

		$opening = $this->Opening->find('first',[
			'conditions'=>['Opening.id'=>$id]
		]);

		if (!$this->Opening->exists($id)) {
			throw new NotFoundException(__('Invalid opening'));
		}

		$currencies=$this->Currency->find('all',array('recursive'=>-1,
			'conditions'=>array(
				'NOT'=>[
					'Currency.id'=>['c00','c8']
				]
			),
			'order'=>'Currency.is_other_currency ASC, Currency.id ASC',
			'limit'=>0
		));

		if ($this->request->is('post') || $this->request->is('put')) {
			
			if (!empty($opening['OpeningDetail']['id'])) {

				$data = [];
	    		$currency_details = [];
	    		foreach($currencies as $currency){
	    			$detail['CID'] = $currency['Currency']['id'];
	        		$detail['CAMOUNT'] = $this->request->data['Opening'][($currency['Currency']['id']).'a'];
	        		$detail['CRATE'] = $this->request->data['Opening'][($currency['Currency']['id']).'r'];

	        		$currency_details[$currency['Currency']['id']] = $detail;
	    		}

	    		// set OpeningDetail details
				$data['currency_details'] = json_encode($currency_details);
				$data['id'] = $opening['OpeningDetail']['id'];

				// Save OpeningDetail
				if ($this->Opening->OpeningDetail->save(['OpeningDetail'=>$data])) {
					if ($this->Opening->save($this->request->data)) {
						$this->Session->setFlash(__('The opening has been saved'),'flash_success');
						$this->redirect(array('action' => 'index'));
					} else {
						$this->Session->setFlash(__('The opening could not be saved. Try again.'),'flash_error');
					}
				}
			}else{
				$this->Session->setFlash(__('Opening Currency Details missing. Try upgrading openings.'),'flash_error');
			}
		} else {
			$options = array('conditions' => array('Opening.' . $this->Opening->primaryKey => $id));
			$this->request->data = $this->Opening->find('first', $options);
		}
		$users = $this->Opening->User->find('list',array(
			'conditions'=>array(
				'User.id'=>$user_id
			),'limit'=>1
		));
		
		$this->set(compact('users','currencies','opening'));
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
		$this->Opening->id = $id;
		if (!$this->Opening->exists()) {
			throw new NotFoundException(__('Invalid opening'));
		}
		//$this->request->onlyAllow('post', 'delete');
		if ($this->Opening->delete()) {
			$this->Session->setFlash(__('Opening deleted'),'flash_success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash(__('Opening was not deleted'),'flash_error');
		$this->redirect(array('action' => 'index'));
	}
}
