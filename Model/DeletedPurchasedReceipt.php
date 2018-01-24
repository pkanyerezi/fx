<?php
App::uses('AppModel', 'Model');
/**
 * PurchasedReceipt Model
 *
 * @property Purpose $Purpose
 * @property Currency $Currency
 */
class DeletedPurchasedReceipt extends AppModel {

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
