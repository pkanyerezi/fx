<?php
App::uses('Controller', 'Controller');
class AppController extends Controller {
	public $uses = array('User','Notification.Notification','Fox','ActionLog','DeletedPurchasedReceipt','DeletedSoldReceipt');
	public $components = array(
		'Auth','Session',
		'RequestHandler',
		'Func',
    );

    public $dateToday = null;
    public $dateFrom = null;
    public $dateTo = null;
    public $fox = null;
    public $downloadsIp = 'localhost';

	function beforeFilter(){

		parent::beforeFilter();
		
		$this->Auth->authError = 'Please login to continue.';
        $this->Auth->loginError = 'Incorrect username/password combination';
        $this->Auth->loginRedirect = array('controller' => 'dashboards');
        $this->Auth->logoutRedirect = array('controller' => 'users', 'action' => 'login');
		
        //Get Company details.
        $this->fox = $this->Session->read('fox');
        if(empty($this->fox) || empty($this->fox['Fox']['server_public_ip'])){
            $this->fox = $this->Fox->find('first');
            $this->Session->write('fox',$this->fox);
        }
        

        // Set Ip/domain for downloading files from the server via network PC
        if (!isset($this->fox['Fox']['server_public_ip'])) {
            echo 'field server_public_ip missing in settings!';exit;
        }
        @$this->downloadsIp = $this->fox['Fox']['server_public_ip'];
        if (empty($this->downloadsIp)) {
             $this->downloadsIp = 'localhost';
        }
        $this->set('downloadsIp',$this->downloadsIp);

        $this->set('super_admin', $this->_is('super_admin'));
        $this->set('cashier', $this->_is('regular'));
		$this->set('admin', $this->_isAdmin());
        $this->set('logged_in', $this->_loggedIn());
        $this->set('users_username', $this->setField('username'));
        $this->set('users_Id', $this->setField('id'));
        $this->set('approval_position', $this->setField('approval_position'));
        $this->set('name_of_user', $this->setField('name'));
        $this->set('role_of_user', $this->setField('role'));
        $this->set('other_role_of_user', $this->setField('other_role'));
        $this->set('email_of_user', $this->setField('email'));
        $this->set('profile_image', $this->setField('profile_image')); 
        $this->set('store', $this->_isStore()); 
        
        $authUser = $this->_getAuthUser();

        //Require change of password every after a month
        if($this->request->params['controller']!='users' && !empty($authUser['id'])){
            if(empty($authUser['password_last_changed_on']) || strtotime(date('Y-m-d'))>strtotime($authUser['password_last_changed_on'].' +1month'))
            {
                $this->Session->setFlash(__('You are required to change your password.'),'flash_warning');
                $this->redirect(array('action' => 'settings','controller'=>'users',$authUser['id']));
            }
        }

        //Require super_admin to make a daily bakup by force
        if(in_array($this->request->params['controller'], ['dashboards','sold_receipts','purchased_receipts']) && $authUser['role']=='super_admin')
        {
            if(!in_array($this->request->params['action'], ['confirm_backup','backup'])) {
            
                $fox = $this->Session->read('fox');
                if(empty($fox))
                {
                    $fox = $this->Fox->find('first');
                    $this->Session->write('fox',$fox);
                }

                if(strtotime($fox['Fox']['last_backup']) < strtotime(date('Y-m-d',strtotime(date('Y-m-d').' -3days'))))
                {
                    $this->Session->setFlash(__('You need to create a Data backup today.'),'flash_info');
                    $this->redirect(array('action' => 'confirm_backup','controller'=>'dashboards'));
                }
            }
        }

        //Require Updates when we enter a new year
        if(in_array($this->request->params['controller'], ['returns']))
        {
            $updateReminderDate = '2025-01-03';
            $updateDate = '2025-01-10';

            //Remind the person
            if (strtotime(date('Y-m-d')) >= strtotime($updateReminderDate)) {
                //Force them to upgrade
                if (strtotime(date('Y-m-d')) >= strtotime($updateDate)) {
                    echo "<style>.flash-message{z-index: 2147483647;position: absolute;}</style>";
                    $this->Session->setFlash(__('System need to be upgraded for the new financial year. Please contact us.'),'flash_error');
                   $this->redirect(array('action' => 'index','controller'=>'dashboards'));
                }else{
                    echo "<style>.flash-message{z-index: 2147483647;position: absolute;}</style>";
                    $this->Session->setFlash(__('System need to be upgraded for the new financial year. Please contact us.'),'flash_warning');
                }
            }
        }
        $this->set('authUser',$authUser);


        date_default_timezone_set('Africa/Nairobi');
        $this->dateToday=date('Y-m-d');
        if(isset($_REQUEST['date_today'])){
            $this->dateToday =($_REQUEST['date_today']);
        }
        $this->dateFrom = (empty($_REQUEST['date_from']))?date('Y-m-d'):$_REQUEST['date_from'];
        $this->dateTo   = (empty($_REQUEST['date_from']))?date('Y-m-d'):$_REQUEST['date_to'];

        $this->set('from',$this->dateFrom);
        $this->set('to',$this->dateTo);
	}

    

    function _getAuthUser(){
        return $this->Auth->User();
    }

	function _is($role) {
        $fits_role = FALSE;
        if ($this->Auth->user('role') == $role) {
            $fits_role = TRUE;
        }
        return $fits_role;
    }
	
	function _isStore() {
        $admin = FALSE;
        if ($this->Auth->user('role') == 'store') {
            $this->Session->write('user_id', $this->Auth->user('id'));
            $admin = TRUE;
        }else
            $admin = FALSE;
        return $admin;
    }

    function setField($field) {
        return $this->Auth->User($field);
    }

    function _isAdmin() {
        $admin = FALSE;
        if ($this->Auth->user('role') == 'admin') {
            $admin = TRUE;
        }
        return $admin;
    }

    function _loggedIn() {
        $logged_in = FALSE;
        if ($this->Auth->user()) {
            $logged_in = TRUE;
        }
        return $logged_in;
    }
}
