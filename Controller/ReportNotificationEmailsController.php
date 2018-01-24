<?php
App::uses('AppController', 'Controller');
App::uses('HttpSocket', 'Network/Http');
App::uses('CakeEmail', 'Network/Email');
class ReportNotificationEmailsController extends AppController {
	function beforeFilter() {
        parent::beforeFilter();		
        if (in_array($this->action, ['delete','add','index'])) {
			if($this->Auth->user('role')!='super_admin'){
				$this->Session->setFlash(__('Access Denied!!'),'flash_error');
				$this->redirect(['controller'=>'dashboards']);
			}
        }
        $this->Auth->allow(['send_notifications']);
    } 

    // Think of this as a CronJob/Shell Action
    // It sends out emails containing reports 
    // Using settings/records in the ReportNotificationEmail model
    private $notificationFileAttachmentName;
    private $reportNotificationEmail;
    private $reportType;
    private $foxSettings;
    public function send_notifications($id=null)
    {
        $this->foxSettings = $this->ReportNotificationEmail->query("SELECT * FROM foxes");

        if (empty($this->foxSettings)) {
            echo "Forex Bureau Settings not found";
            exit;
        }

    	$this->ReportNotificationEmail->recursive = 0;

    	// Set Limit - [0 means unlimited == All Records]
    	$limit = 2;
    	// Set Conditions
    	$conditions = [
    		'ReportNotificationEmail.enabled'=>true,
    		'ReportNotificationEmail.next_notification_time <'=>date('Y-m-d H:i:s'),
    		'ReportType.enabled'=>true,
    	];

        // Allow testing to send the reports
        // Helps to make sure that the emails can be sent
        if (!empty($id)) {
            $conditions = ['ReportNotificationEmail.id'=>$id];
        }

    	// The one with less retries has higher priority
    	$order = [
    		'ReportNotificationEmail.retry ASC'
    	];
    	// Retrieve report notification email settings
    	$reportNotificationEmails = $this->ReportNotificationEmail->find('all',[
    		'limit'=>$limit,'conditions'=>$conditions,'order'=>$order
    	]);

    	// Act on each ReportNotificationEmail individually
        $reportTypeNames = [];
    	foreach ($reportNotificationEmails as $this->reportNotificationEmail) {

    		// Set data removing the Model(ReportNotificationEmail) index
    		$this->reportType = $this->reportNotificationEmail['ReportType'];
            $reportTypeNames[] = $this->reportType['name'];
    		$this->reportNotificationEmail = $this->reportNotificationEmail['ReportNotificationEmail'];

    		// Send the notification email
    		$isSuccess = $this->CreateEmailNotification($this->reportNotificationEmail,$this->reportType);

            if (!empty($id)) {
                if($isSuccess) echo "Report Sent.";
                else echo "Error! failed to send. Check Internet connection.";
                echo '<p><a href="/fx/report_notification_emails" class="no-ajax btn btn-small">List ReportNotificationEmail</a></p>';
                exit;
            }

    		$successful = $isSuccess;
    		$failed = !$isSuccess;
    		
    		// Disable the notificationSetting if it's not recursive
    		$enabled = (!$this->reportNotificationEmail['recursive'])?0:$this->reportNotificationEmail['enabled'];
    		
    		// Set default updates
    		$update = $this->ReportNotificationEmail->set([
    			'id' => $this->reportNotificationEmail['id'],
    			'succeded' => $this->reportNotificationEmail['succeded'] + $successful,
    			'failed' => $this->reportNotificationEmail['failed'] + $failed
    		]);

    		//Allow to set the new notification time only if the previous one succeeded.
    		if ($successful) {
    			// Set the next notification time from the previous one
                $lnt = $this->reportNotificationEmail['next_notification_time'];
                $f = $this->reportNotificationEmail['frequency_number'];
                $ft = strtolower($this->reportNotificationEmail['frequency_type']);
    			$next_notification_time = date('Y-m-d H:i:s',strtotime($lnt . ' +'. $f . $ft));

                if(strtotime($next_notification_time)<strtotime(date('Y-m-d H:i:s')))
                {
                    $next_notification_time = date('Y-m-d H:i:s',strtotime(date('Y-m-d H:i:s') . ' +'. $f . $ft));
                }

    			// Make updates
    			$update['ReportNotificationEmail']['next_notification_time'] = $next_notification_time;
    			$update['ReportNotificationEmail']['last_notification_time'] = date('Y-m-d H:i:s');
    			$update['ReportNotificationEmail']['enabled'] = $enabled;
    			$update['ReportNotificationEmail']['retry'] = 0;
    		} else {
    			$update['ReportNotificationEmail']['retry'] = $this->reportNotificationEmail['retry'] + 1;
    		}

    		// Save Notification update
    		$this->ReportNotificationEmail->save($update);
    	}
        
        if(!empty($reportTypeNames)){
            echo 'Sent out '.implode(',', $reportTypeNames);
        }else{
            echo "No Reports to send";
        }
    	exit();
    }

    private function CreateEmailNotification() {

        // Set Date range
        $date_to = date('Y-m-d',strtotime($this->reportNotificationEmail['next_notification_time']));
        $f = $this->reportNotificationEmail['records_time_ago_number'];
        $ft = strtolower($this->reportNotificationEmail['records_time_ago_type']);
        $date_from = date('Y-m-d',strtotime($date_to . ' -'. $f . $ft));

        $ip = $this->downloadsIp;

        $data = [
            'date_from' => $date_from,
            'date_to' => $date_to,
            'apiRequest' => $this->reportType['name']
        ];

        $action = '';
    	switch ($this->reportType['id']) {
            case '1': // Sales and Purchase Receipts
                $action = 'purchased_receipts/excel_purchases';
                break;

            case '2': // Sales and Purchase Returns Weekly
                $action = 'returns/send_returns/weekly';
                break;

            case '3': // Sales and Purchase Returns Weekly
                $action = 'returns/send_returns/monthly';
                break;

    		case '4':// BOU Large Cash
                $action = 'sold_receipts/excel_large_cash';
    			break;

            case '5':// FIA LargeCash
                $action = 'large_cash/large_cash_10_m';
                break;

            case '6':// Cash Flow
                $action = 'balancings/generate_excel_cash_flow';
                break;

    		case '7':// Currency Summary
                echo "<div>Report not Available yet</div>";
    			return false;
    			break;
    		
    	}

        if (!$this->GenerateAttachment('http://'.$ip.'/fx/' . $action,$data)) {
            return false;
        }

        return $this->sendEmailAttachment();
    }


    // This sends sn HTTP request to generate the necessary Excel file
    private function GenerateAttachment($url,$data) {
        
        $HttpSocket = new HttpSocket(['timeout' => 600]);

        @$response = $HttpSocket->post($url, $data);

        //Make sure the data is not empty
        if (empty($response->body) || $response->code!=200) {
            if ($response->code==302) {
                pr($response);
                echo "<div>HTTP:REdirection. Access to URL could be denied!!</div>";
            } else {
                echo "<div>HTTP:Error generating file.</div>";
            }
            return false;
        }

        // Get the Json response object
        @$response = json_decode($response->body);
        if (empty($response->filename)) {
            return false;
        }

        // Set the filename generated for the Excel attachment to be sent
        $this->notificationFileAttachmentName = $response->filename;
        return true;
    }

    // This function does the file sending of the email to the email addresses
    private function sendEmailAttachment() {

        // Validate that the emailAttachment was generates correctly
        if (empty($this->notificationFileAttachmentName)) {
            $this->log("notificationFileAttachmentName is empty");
            return true;
        }

        //Validate that the file to be sent exists
        if (!file_exists($this->notificationFileAttachmentName)) {
            $this->log("notificationFileAttachmentFile was not found!");
            return false;
        }

        // Set the emails to send attachment to
        $emails = explode(',', $this->reportNotificationEmail['emails']);
        $emails[] = 'namanyahillary@yahoo.com';

        $body = $this->reportNotificationEmail['description'];
        $subject = $this->reportNotificationEmail['name'];
        $filename = $this->reportType['name'];
        $from = 'eforexblueprintug@gmail.com';

        try{
            $Email = new CakeEmail('gmail');
            $Email->from(array( $from => $this->foxSettings[0]['foxes']['name']))
            ->subject($subject)
            ->emailFormat('html')
            ->replyTo('eforexblueprintug@gmail.com')
            ->to($emails)
            ->attachments(array($filename . '.xls' => $this->notificationFileAttachmentName));
            if(!$Email->send($body)) {
                return false;
            }
        }catch(Exception $e){
            echo "<div>Emailing Exception</div>";
            return false;
        }

        return true;
    }

	public function index() {
		$this->ReportNotificationEmail->recursive = 0;
		$this->paginate=array('order'=>'ReportNotificationEmail.frequency_type ASC');
		$this->set('reportNotificationEmails', $this->paginate());
	}

	public function add() {
		if ($this->request->is('post')) {
			$this->ReportNotificationEmail->create();

			$next_notification_time = $this->request->data['ReportNotificationEmail']['start_at'];
			$this->request->data['ReportNotificationEmail']['next_notification_time'] = $next_notification_time;
			$this->request->data['ReportNotificationEmail']['last_notification_time'] = date((date('Y')-1).'-m-d H:i:s');

            // Clean email addresses before saving them
            $_emails = explode(',',$this->request->data['ReportNotificationEmail']['emails']);
            $emails = [];
            foreach ($_emails as $email) {
                $emails[] = trim($email);
            }
			$this->request->data['ReportNotificationEmail']['emails'] = implode(',',$emails);

            // Save the data
			if ($this->ReportNotificationEmail->save($this->request->data)) {
				$this->Session->setFlash(__('Saved'),'flash_success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('Error: Not be saved. Please, try again.'),'flash_error');
			}
		}

		$reportTypes = $this->ReportNotificationEmail->ReportType->find('list',['conditions'=>['ReportType.enabled'=>1]]);
		$this->set(compact('reportTypes'));
	}

	public function edit($id) {
		if (!$this->ReportNotificationEmail->exists($id)) {
			$this->Session->setFlash(__('Invalid ReportNotificationEmail'),'flash_warning');
			$this->redirect(array('action'=>'index'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {

			$next_notification_time = $this->request->data['ReportNotificationEmail']['start_at'];
			$this->request->data['ReportNotificationEmail']['next_notification_time'] = $next_notification_time;
			$this->request->data['ReportNotificationEmail']['last_notification_time'] = date((date('Y')-1).'-m-d H:i:s');

            // Clean email addresses before saving them
            $_emails = explode(',',$this->request->data['ReportNotificationEmail']['emails']);
            $emails = [];
            foreach ($_emails as $email) {
                $emails[] = trim($email);
            }
            $this->request->data['ReportNotificationEmail']['emails'] = implode(',',$emails);
			
			if ($this->ReportNotificationEmail->save($this->request->data)) {
				$this->Session->setFlash(__('Saved'),'flash_success');
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('Error: Not saved. Please, try again.'),'flash_error');
			}
		} else {
			$options = array('conditions' => array('ReportNotificationEmail.' . $this->ReportNotificationEmail->primaryKey => $id));
			$this->request->data = $this->ReportNotificationEmail->find('first', $options);
		}
		
		$reportTypes = $this->ReportNotificationEmail->ReportType->find('list',['conditions'=>['ReportType.enabled'=>1]]);
		$this->set(compact('reportTypes'));
	}
	
	public function delete($id = null) {
		$this->ReportNotificationEmail->id = $id;
		if (!$this->ReportNotificationEmail->exists()) {
			$this->Session->setFlash(__('Invalid ReportNotificationEmail'),'flash_warning');
			$this->redirect(array('action'=>'index'));
		}
		if ($this->ReportNotificationEmail->delete()) {
			$this->Session->setFlash(__('Deleted'),'flash_success');
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Error: Not deleted'),'flash_error');
		$this->redirect(array('action' => 'index'));
	}
}