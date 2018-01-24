<?php
App::uses('AppModel', 'Model');
/**
 * SoldReceipt Model
 *
 * @property Purpose $Purpose
 * @property Currency $Currency
 */
class DeletedSoldReceipt extends AppModel {

	public $belongsTo = array(
		'Purpose' => array(
			'className' => 'Purpose',
			'foreignKey' => 'purpose_id',
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
