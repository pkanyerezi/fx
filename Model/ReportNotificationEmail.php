<?php
App::uses('AppModel', 'Model');
class ReportNotificationEmail extends AppModel {
	public $belongsTo = array(
		'ReportType' => array(
			'className' => 'ReportType',
			'foreignKey' => 'report_type_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}
