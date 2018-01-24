<?php
App::uses('AppModel', 'Model');
class Fox extends AppModel {
	public $validate = array(
		'name' => array(
			'notempty' => array(
				'rule' => array('notempty'),
			),
		),
		'location' => array(
			'notempty' => array(
				'rule' => array('notempty'),
			),
		),
	);
}
