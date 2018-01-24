<?php
App::uses('AppModel', 'Model');
class ReportType extends AppModel {
	public $hasMany = array(
		'ReportNotificationEmail' => array(
			'className' => 'ReportNotificationEmail',
			'foreignKey' => 'report_type_id',
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