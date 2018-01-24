<?php
App::uses('AppModel', 'Model');
class Asset extends AppModel {

	public $belongsTo = array(
		'AssetName' => array(
			'className' => 'AssetName',
			'foreignKey' => 'asset_name_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}
