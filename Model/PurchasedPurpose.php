<?php
App::uses('AppModel', 'Model');
/**
 * PurchasedPurpose Model
 *
 * @property PurchasedReceipt $PurchasedReceipt
 */
class PurchasedPurpose extends AppModel {
	
	public $displayField='description';
	
/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'description' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'PurchasedReceipt' => array(
			'className' => 'PurchasedReceipt',
			'foreignKey' => 'purchased_purpose_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		)
	);

}
