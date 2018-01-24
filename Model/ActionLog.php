<?php
App::uses('AppModel', 'Model');
/**
 * Item Model
 *
 * @property Expense $Expense
 */
class ActionLog extends AppModel {
	public $hasMany = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
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
