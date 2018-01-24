<?php
App::uses('AppModel', 'Model');
/**
 * PurchasedReceipt Model
 *
 * @property Purpose $Purpose
 * @property Currency $Currency
 */
class PurchasedReceipt extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'purpose_id' => array(
			'notempty' => array(
				'rule' => array('notempty'),
			),
			'Invalid purpose selected' => array(
				'rule' => array('maxLength', 3),
				'message' => 'Invalid purpose selected'
			),
		),
		'currency_id' => array(
			'notempty' => array(
				'rule' => array('notempty'),
			)
		),
		'rate' => array(
			'numeric' => array(
				'rule' => array('numeric'),
			),
		),
		'amount' => array(
			'numeric' => array(
				'rule' => array('numeric'),
			),
		),
		'amount_ugx' => array(
			'numeric' => array(
				'rule' => array('numeric'),
			),
		),
	);

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'PurchasedPurpose' => array(
			'className' => 'PurchasedPurpose',
			'foreignKey' => 'purchased_purpose_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Currency' => array(
			'className' => 'Currency',
			'foreignKey' => 'currency_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'OtherCurrency' => array(
			'className' => 'OtherCurrency',
			'foreignKey' => 'other_currency_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}
