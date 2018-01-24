<?php
App::uses('AppModel', 'Model');
/**
 * User Model
 *
 */
class User extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'name' => array(
			'notempty' => array(
				'rule' => array('notempty')
			),
		),
		'username' => array(
			'notempty' => array(
				'rule' => array('notempty'),
			),
			'unique' =>array(
				'rule' => array('isUnique'),
				'message' => 'Username already taken',
			)
		),
		'email' => array(
			'notempty' => array(
				'rule' => array('notempty'),
			),
			'unique' =>array(
				'rule' => array('isUnique'),
				'message' => 'Phone exists',
			)
		),
		'password' => array(
			'notempty' => array(
				'rule' => array('notempty'),
			),
		),
		'password' => array(
			'The passwords do not match'=>array(
				'rule'=>'matchPasswords',
				'message'=>'The passwords do not match.'
			)
		),
	);
	
	function matchPasswords($data){
		if($data['password'] == $this->data['User']['password_confirmation']){
			return TRUE;
		}
		$this->invalidate('password_confirmation','The passwords do not match');
	}
	function hashPasswords($data){
		if (isset($this->data['User']['password'])){
			$this->data['User']['password']=Security::hash($this->data['User']['password'],NULL,TRUE);
			return $data;
		}
		return $data;
	}
	function beforeSave($options = []){
		$this->hashPasswords(NULL,TRUE);
		return TRUE;
	}
	
	public $hasMany = array(
		'Notification' => array(
			'className' => 'Notifications.Notification',
			'foreignKey' => 'user_id',
		),
		'ActionLog' => array(
			'className' => 'ActionLog',
			'foreignKey' => 'user_id',
		),
	);
}
