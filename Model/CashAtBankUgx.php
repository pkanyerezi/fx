<?php
App::uses('AppModel', 'Model');
/**
 * CashAtBankUgx Model
 *
 */
class CashAtBankUgx extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'date' => array(
			'date' => array(
				'rule' => array('date'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);
	
	public $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Bank' => array(
			'className' => 'bank',
			'foreignKey' => 'bank_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}
