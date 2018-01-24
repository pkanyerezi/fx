<?php
App::uses('AppModel', 'Model');
class AssetName extends AppModel {
	public $hasMany = array(
		'Asset' => array(
			'className' => 'Asset',
			'foreignKey' => 'asset_name_id',
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
