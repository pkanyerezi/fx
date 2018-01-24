<?php
App::uses('AppModel', 'Model');
class SafeTransaction extends AppModel {
	public $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
			'conditions' => '',
			'fields' => ['id','name','role'],
			'order' => ''
		),
		'To' => array(
			'className' => 'User',
			'foreignKey' => 'transaction_to',
			'conditions' => '',
			'fields' => ['id','name','role'],
			'order' => ''
		),
		'From' => array(
			'className' => 'User',
			'foreignKey' => 'transaction_from',
			'conditions' => '',
			'fields' => ['id','name','role'],
			'order' => ''
		),
		'Approver' => array(
			'className' => 'User',
			'foreignKey' => 'approved_by',
			'conditions' => '',
			'fields' => ['id','name','role'],
			'order' => ''
		),
	);
}
