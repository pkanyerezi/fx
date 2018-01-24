<?php
/**
 * Dashboard controller.
 */
App::uses('AppController', 'Controller');
class MySearchController extends AppController {
	public $uses = array('User');
	
	public function beforeFilter(){
		parent::beforeFilter();
		$this->Auth->allow('get_data');
	}
	
	public function get_data($q=null) {
		$Response=array(
			'User'=>array(),
			'Group'=>array(),
			'Destination'=>array(),
		);
		if($q){
			//Users
			$Response['User']=$this->User->find('all', array(
				'conditions' => array(
					 'OR' => array(
						  'User.name LIKE ' => '%'.h($q).'%'
					  ),
					  'User.role'=>array(
							'customer'
					  )
				 ),
				 'limit'=>20,
				 'fields'=>array(
					'id','name','profile_image'
				 ),
				 'recursive'=>-1,
				 'order'=>'name desc'
			));
		}
		
		$this->set('data',$Response);
	}
}
