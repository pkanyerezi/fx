<?php
App::uses('AppModel', 'Model');
/**
 * CashAtBankUgx Model
 *
 */
class AdditionalProfit extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	
	public $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
	
}
