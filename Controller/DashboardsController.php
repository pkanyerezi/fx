<?php
App::uses('AppController', 'Controller');
App::uses('CakeSchema', 'Model');
App::uses('ConnectionManager', 'Model');
App::uses('Inflector', 'Utility');
App::uses('Folder', 'Utility');
App::uses('File', 'Utility');
App::uses('HttpSocket', 'Network/Http');
class DashboardsController extends AppController {
	public $name = 'Dashboards';
	public $uses = array('Fox','User','PurchasedReceipt','AdditionalProfit','Opening');
	
	public function beforeFilter(){
		parent::beforeFilter();
		$this->Auth->allow('get_files','get_cf','backup_data','show_tables','sql_updates','test_save_all','backup');
	}

	//Required by super admin to confirm the backup
	public function confirm_backup(){}

	public function chats()
	{
		$this->Opening->virtualFields = [
			'total_profits'=>0,
			'total_expenses'=>0
		];
		$this->dateFrom = date('Y-m-d',strtotime($this->dateTo . ' -3months'));
		$closings = $this->Opening->find('all',array(
			'recursive'=>0,'Limit'=>1,
			'order'=>'Opening.date desc',
			'conditions'=>array(
				'DATE(Opening.date) >='=>$this->dateFrom,
				'DATE(Opening.date) <='=>$this->dateTo,
				'Opening.status'=>1
			),
			'fields'=>array(
				'SUM(Opening.total_profit) as Opening__total_profits',
				// 'SUM(Opening.total_gross_profit) as total_gross_profit',
				'SUM(Opening.total_expenses) as Opening__total_expenses',
				'Opening.date',
				// 'SUM(Opening.receivable_cash) as receivable_cash',
				// 'SUM(Opening.withdrawal_cash) as withdrawal_cash',
				// 'SUM(Opening.additional_profits) as additional_profits',
				// 'SUM(Opening.total_sales_ugx) as total_sales_ugx',
				// 'SUM(Opening.total_purchases_ugx) as total_purchases_ugx',
				// 'SUM(Opening.debtors) as debtors',
				// 'SUM(Opening.creditors) as creditors'
			),
			'group'=>'Opening.date'
		));
		$this->set(compact('closings'));
	}
	
	public function test_save_all(){
		$data = array(
			'0'=>array('AdditionalProfit'=>array('amount'=>10,'additional_info'=>'none','date'=>date('Y-m-d H:i:s'),'user_id'=>2)),
			'1'=>array('AdditionalProfit'=>array('amount'=>10,'additional_info'=>'none','date'=>date('Y-m-d H:i:s'),'user_id'=>2)),
			'2'=>array('AdditionalProfit'=>array('amount'=>10,'additional_info'=>'none','date'=>date('Y-m-d H:i:s'),'user_id'=>2)),
			'3'=>array('AdditionalProfit'=>array('amount'=>10,'additional_info'=>'none','date'=>date('Y-m-d H:i:s'),'user_id'=>2)),
			'4'=>array('AdditionalProfit'=>array('amount'=>10,'additional_info'=>'none','date'=>date('Y-m-d H:i:s'),'user_id'=>2)),
			'5'=>array('AdditionalProfit'=>array('amount'=>10,'additional_info'=>'none','date'=>date('Y-m-d H:i:s'),'user_id'=>2)),
		);
		echo '<pre>'; 
			// print_r($data);
			echo '</pre>';
		if($this->AdditionalProfit->saveAll($data)){
			echo '<pre>...'; 
			 print_r($this->AdditionalProfit->inserted_ids);
			echo '</pre>';
		}else{
			echo 'failed';
		}
		exit;
	}
	
	public function backup_data(){
		
		$other_fields = array('fox_id','customer_name','amount','purchased_purpose_id','rate','amount_ugx','currency_id','date','t_time','status','is_uploaded','nationality','address','passport_number','user_id','name','other_name','orig_amount','orig_rate','other_currency_id');
		$this->send_data('purchased_receipts',$other_fields);
		
		$other_fields = array('fox_id','customer_name','amount','purpose_id','rate','amount_ugx','currency_id','instrument','date','t_time','status','is_uploaded','nationality','address','passport_number','user_id','name','other_name','orig_amount','orig_rate','other_currency_id');
		$this->send_data('sold_receipts',$other_fields);
		
		$other_fields = array('amount','additional_info','date','user_id');
		$this->send_data('additional_profits',$other_fields);
		
		$other_fields = array('amount','bank_name','currency_id','date','user_id');
		$this->send_data('cash_at_bank_foreigns',$other_fields);
		
		$other_fields = array('amount','bank_name','date','user_id');
		$this->send_data('cash_at_bank_ugxes',$other_fields);
		
		$other_fields = array('contact_list_id','name','phone_number','email','date');
		$this->send_data('contacts',$other_fields);
		
		$other_fields = array('name','date');
		$this->send_data('contact_lists',$other_fields);
		
		$other_fields = array('customer','customer_id','date','user_id','amount');
		$this->send_data('creditors',$other_fields);
		
		$other_fields = array('fox_id','date','daily_buying_return_id','daily_selling_return_id','user_id','name');
		$this->send_data('daily_returns',$other_fields);
		
		$other_fields = array('name');
		$this->send_data('items',$other_fields);
		
		$other_fields = array('name','description');
		$this->send_data('other_currencies',$other_fields);
		
		
		
		$other_fields = array('fox_id','daily_return_id','c1','c2','c3','c4','c5','c6','c7','c8','date');
		$this->send_data('daily_selling_returns',$other_fields);
		
		$other_fields = array('customer','customer_id','amount','date','user_id');
		$this->send_data('debtors',$other_fields);
		
		$other_fields = array('item_id','description','amount','date','user_id');
		$this->send_data('expenses',$other_fields);
		
		$other_fields = array('user_id','opening_ugx','c1a','c1r','c2a','c2r','c3a','c3r','c4a','c4r','c5a','c5r','c6a','c6r','c7a','c7r','c8a','c8r','date','other_currencies','total_profit','total_gross_profit','total_expenses','receivable_cash','withdrawal_cash','additional_profits','total_sales_ugx','total_purchases_ugx','status','cash_at_bank_foreign','cash_at_bank_ugx','debtors','creditors','close_ugx');
		$this->send_data('openings',$other_fields);
		
		$other_fields = array('my_count_sold_receipts','my_count_purchased_receipts','year');
		$this->send_data('receipt_tracks',$other_fields);
		
		$other_fields = array('customer','customer_id','amount','date','user_id');
		$this->send_data('receivables',$other_fields);
		
		$other_fields = array('name','username','slug','password','password_token','email','email_verified','email_token','email_token_expires','tos','active','last_login','last_action','is_admin','profile_image','role','date','officer_name','officer_title','officer_phone');
		$this->send_data('users',$other_fields);
		
		$other_fields = array('customer_id','customer','amount','additional_info','date','user_id');
		$this->send_data('withdrawals',$other_fields);
		
		exit;
	}
	
	private function send_data($table,$other_fields){
		$HASH = 'NFIW47FWIEUHF9QNASVNABFOQ395RGHEWABCI9GHW9GKAH9';
		$URL = 'http://localhost/eforexbackup/api/save_backup/';
		
		$HttpSocket = new HttpSocket();
		$ModelName = Inflector::classify($table);$Model = ClassRegistry::init($ModelName);
		$SALT = $HASH.$ModelName;$Model->virtualFields['id']='';
		$fields = array_merge(array('id as data_record_id',"CONCAT('$SALT',id) as ".$ModelName."__id"),$other_fields);
		$data = $Model->find('all',array('recursive'=>-1,'fields'=>$fields,'limit'=>3000,'conditions'=>array('backedup'=>0)));
		$count=0;
		while(!empty($data) && $count<10){
			$count++;
			try{
				$results = $HttpSocket->post($URL.$HASH.'/'.$table.'.json', $data);
				$results = str_replace("[","",$results->body);$results = str_replace("]","",$results);
				$Model->query("update `".$table."` set `backedup`=1 where CONCAT('$SALT',id) IN(".$results.")");
				$data = $Model->find('all',array('recursive'=>-1,'fields'=>$fields,'limit'=>3000,'conditions'=>array('backedup'=>0)));
			}catch(Exception $e){
				break;
			}
		}
		return;
	}
	
	
	public function sql_updates(){
		$dataSourceName = 'default';
		$db = ConnectionManager::getDataSource($dataSourceName);
		$config = $db->config;
		$this->connection = "default";
		
		//Add field "backedup" to track the fields that have been backed up online such that they are not repeated
		$skip_tables = array('action_logs','rest_logs','currencies','deleted_sold_receipts','deleted_purchased_receipts','foxes','saves','purchased_purposes','purposes','notifications');
		foreach ($db->listSources() as $table) {
			if(in_array($table,$skip_tables)) continue;
			try{
				$ModelName = Inflector::classify($table);$Model = ClassRegistry::init($ModelName);
				$query = "ALTER TABLE  `".$table."` ADD  `backedup` SMALLINT( 1 ) NOT NULL DEFAULT  '0'";
				$Model->query($query);
				echo $table.' - done <br/>';
			}catch(Exception $e){
				echo $table.' - failed: '.$query.' <br/>';
			}
		}
		exit;
	}
	
	public function show_tables(){
		$dataSourceName = 'default';
		$db = ConnectionManager::getDataSource($dataSourceName);

		$config = $db->config;
		$this->connection = "default";
		$skip_tables = array('action_logs','rest_logs','currencies','deleted_sold_receipts','deleted_purchased_receipts','foxes','saves','purchased_purposes','purposes','notifications');
		foreach ($db->listSources() as $table) {
			if(in_array($table,$skip_tables)) continue;
			
			echo $table.'<br/>';
			
		}
		
		$SALT = 'SALT';
		
		//additional_profits
		$table = 'additional_profits';
		$ModelName = Inflector::classify($table);$Model = ClassRegistry::init($ModelName);
		$SALT = $SALT.$ModelName;$Model->virtualFields['_objectID']='';
		$fields = array("CONCAT('$SALT',id) as ".$ModelName."___objectID",'amount','additional_info','date','user_id');
		$data = $Model->find('first',array('recursive'=>-1,'fields'=>$fields,'limit'=>1000,'conditions'=>array()));
			
		echo '<pre>';
		print_r($data);
		echo '</pre>';
		exit;
	}
	
	public function index() {	
	
		//$this->get_notifications();
		
		$_fox=($this->Session->read('fox'));
		$date2 = date('Y-m-d');//today's system date
		$date1 = $_fox['Fox']['last_backup'];
		$diff = abs(strtotime($date2) - strtotime($date1));

		$years = floor($diff / (365*60*60*24));
		$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
		$days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
		
		if($days>4){
			$this->Session->setFlash(__("Its over $days days since you last backedup your Data. ").'flash_warning');
			
			$this->set('days',$days);
			$this->set('fox_id',$_fox['Fox']['id']);
		}
	}
	
	function re_get_notifications(){
		$this->get_notifications();
	}
	
	function get_notifications(){
	exit;
		if($this->Auth->User('role')=='super_admin'){
			$resting=new $this->Resting;			
			$_fox=($this->Session->read('fox'));
			$resting->api_username=$_fox['Fox']['un'];
			$resting->api_password=$_fox['Fox']['pwd'];
			$resting->authorisation_key=$_fox['Fox']['k'];
			$resting->url = $_fox['Fox']['url'];
			$response=$resting->XML_fetch_data('/notifs/my_notifications.json','<Notifications></Notifications>');
			if($resting->has_response){
				$response_array=json_decode($response);
				if(isset($response_array->data->response->notifications)){
					$notifications=(json_decode($response_array->data->response->notifications[0]));
					foreach($notifications as $notification){
						$this->User->Notification->msg($this->Auth->User('id'), $notification->Notification->message);
					}
				}
			}
		}
	}
	
	function backup($fox_id=null){
		
		set_time_limit(0);
		$dataSource = ConnectionManager::getDataSource('default');
		
		/*$dbhost =   '188.166.9.123';
		$dbuser =   'root';
		$dbpass =   'Q1a2t3t4check';
		$dbname =   'finance';*/
		
		$dbhost =   $dataSource->config['host'];
		$dbuser =   $dataSource->config['login'];
		$dbpass =   $dataSource->config['password'];
		$dbname =   $dataSource->config['database'];
		$dir =  dirname(__FILE__); 
		$serverDir =  str_replace(DS . 'fx' . DS . 'Controller','',$dir);
		
		$path = APP_DIR . DS .'Backups' . DS;
		$Folder = new Folder($path, true);
		
		$fileSufix = 'db.sql';
		$file = $path . $fileSufix;

		//Check if directory is writable
		if (!is_writable($path)) 
		{
			trigger_error('The path "' . $path . '" isn\'t writable!', E_USER_ERROR);
		}
		
		//Create the file name with full path to be executed in the commandline
		//$commandFullFilePath = $serverDir . DS . $file;
		$commandFullFilePath = $serverDir . DS . 'fx'. DS . 'webroot' . DS . $file;
		$command = "";
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') 
		{
			//Get Mysql version
			$result = $this->User->query('SELECT version() as v');
			$mysqlVersion = $result[0][0]['v'];
			$mysqlDumpProgram = "C:\wamp\bin\mysql\mysql".$mysqlVersion."\bin\mysqldump";
			
			$dbLogin = "--user=$dbuser --host=$dbhost $dbname > ";
			$command = $mysqlDumpProgram." ".$dbLogin.$commandFullFilePath;			
		} 
		elseif(PHP_OS == 'Linux')
		{
			$command = "/usr/bin/mysqldump --opt --user=$dbuser --password='$dbpass' --host=$dbhost $dbname > ".$commandFullFilePath;
		}
		else 
		{
			$command = "/usr/local/mysql/bin/mysqldump --user=$dbuser --password='$dbpass' --host=$dbhost $dbname > ".$commandFullFilePath;
		}
		shell_exec($command);
		
		$file = $commandFullFilePath;
		if (class_exists('ZipArchive') && file_exists($file)) 
		{
			$zip = new ZipArchive();
			$zip->open($file . '.zip', ZIPARCHIVE::CREATE);
			$zip->addFile($file, $fileSufix);
			$zip->close();
			if (file_exists($file . '.zip')) 
			{
				unlink($file);
			}
			//update current date
			$fox = $this->Fox->find('first');
			$this->Fox->id=$fox['Fox']['id'];
			$this->Fox->set('last_backup',date('Y-m-d'));
			$this->Fox->save();
			
			$fox = $this->Fox->find('first');
            $this->Session->write('fox',$fox);
		}
	}
	
	
	function backupv1($fox_id){
		$dataSourceName = 'default';
		date_default_timezone_set('Africa/Nairobi');
		$path = APP_DIR . DS .'Backups' . DS;

		$Folder = new Folder($path, true);
		
		//$fileSufix = date('Ymd\_His') . '.sql';
		$fileSufix = 'db.sql';
		$file = $path . $fileSufix;
		if (!is_writable($path)) {
			trigger_error('The path "' . $path . '" isn\'t writable!', E_USER_ERROR);
		}
		
		//$this->out("Backuping...\n");
		$File = new File($file);

		$db = ConnectionManager::getDataSource($dataSourceName);

		$config = $db->config;
		$this->connection = "default";
		
		foreach ($db->listSources() as $table) {
			$table = str_replace($config['prefix'], '', $table);
			
			
			// $table = str_replace($config['prefix'], '', 'dinings');
			$ModelName = Inflector::classify($table);
			$Model = ClassRegistry::init($ModelName);
			$DataSource = $Model->getDataSource();
			$this->Schema = new CakeSchema(array('connection' => $this->connection));
			
			$cakeSchema = $db->describe($table);
			// $CakeSchema = new CakeSchema();
			$this->Schema->tables = array($table => $cakeSchema);
			date_default_timezone_set ( "Africa/Nairobi" );
			$_Date=date('Y-m-d H:i:s');
			$File->write("\n/* Date {$_Date} */\n");
			$File->write("\n/* Drop statement for {$table} */\n");
			$File->write("SET foreign_key_checks = 0;");
			// $File->write($DataSource->dropSchema($this->Schema, $table) . "\n");
			$File->write($DataSource->dropSchema($this->Schema, $table));
			$File->write("SET foreign_key_checks = 1;\n");

			$File->write("\n/* Backuping table schema {$table} */\n");

			$File->write($DataSource->createSchema($this->Schema, $table) . "\n");

			$File->write("\n/* Backuping table data {$table} */\n");

		
			unset($valueInsert, $fieldInsert);
			
			if(in_array($table,array('action_logs'))) continue;
			
			$rows = $Model->find('all', array('recursive' => -1));
			$quantity = 0;
			
			if (sizeOf($rows) > 0) {
				$fields = array_keys($rows[0][$ModelName]);
				$values = array_values($rows);	
				$count = count($fields);

				for ($i = 0; $i < $count; $i++) {
					$fieldInsert[] = $DataSource->name($fields[$i]);
				}
				$fieldsInsertComma = implode(', ', $fieldInsert);

				foreach ($rows as $k => $row) {
					unset($valueInsert);
					for ($i = 0; $i < $count; $i++) {
						$valueInsert[] = $DataSource->value(utf8_encode($row[$ModelName][$fields[$i]]), $Model->getColumnType($fields[$i]), false);
					}

					$query = array(
						'table' => $DataSource->fullTableName($table),
						'fields' => $fieldsInsertComma,
						'values' => implode(', ', $valueInsert)
					);		
					$File->write($DataSource->renderStatement('create', $query) . ";\n");
					$quantity++;
				}

			}
			
			//$this->out('Model "' . $ModelName . '" (' . $quantity . ')');
		}
		$File->close();
		//$this->out("\nFile \"" . $file . "\" saved (" . filesize($file) . " bytes)\n");

		if (class_exists('ZipArchive') && filesize($file) > 100) {
			//$this->out('Zipping...');
			$zip = new ZipArchive();
			$zip->open($file . '.zip', ZIPARCHIVE::CREATE);
			$zip->addFile($file, $fileSufix);
			$zip->close();
			//$this->out("Zip \"" . $file . ".zip\" Saved (" . filesize($file . '.zip') . " bytes)\n");
			//$this->out("Zipping Done!");
			if (file_exists($file . '.zip') && filesize($file) > 10) {
				unlink($file);
			}
			//update current date
			$this->Fox->id=$fox_id;
			$this->Fox->set('last_backup',date('Y-m-d'));
			$this->Fox->save();
			$this->Session->write('fox',$this->Fox->find('first'));
			//$this->out("Database Backup Successful.\n");
		}
	}
	
}
