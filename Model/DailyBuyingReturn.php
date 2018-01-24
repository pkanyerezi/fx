<?php
App::uses('AppModel', 'Model');
/**
 * DailyBuyingReturn Model
 *
 * @property Fox $Fox
 * @property DailyReturn $DailyReturn
 */
class DailyBuyingReturn extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'fox_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'daily_return_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
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

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Fox' => array(
			'className' => 'Fox',
			'foreignKey' => 'fox_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'DailyReturn' => array(
			'className' => 'DailyReturn',
			'foreignKey' => 'daily_return_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}
