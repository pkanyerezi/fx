$(document).ready(function(){
	setInterval(function(){
		$('.notifier-status').html('About to check for reports to send');
		send_notifications();
	},30000);
});
var lock=0;
function send_notifications(){

	if(lock==0){lock=1;}
	else {return;}
	
	//load new receipts-These are receipts that are to be printed
	var data = {};
	if(lock==1){
		$.ajax({
			url: 'http://localhost/fx/report_notification_emails/send_notifications',
			dataType: 'html',
			data: data,
			beforeSend: function(){$('.notifier-status').html('Checking for reports to send!');},
			success: succeeded,
			error: errored,
			complete:function(){lock=0;}
		});
	}
}

function befored(){$('.notifier-status').html('Checking for reports to send!');}

function succeeded(data) {
	$('.notifier-status').html(data);
	setTimeout(function(){$('.notifier-status').html('About to check for reports to send');}, 20000);//Sleep for 20 Secs
}

function errored() {$('.notifier-status').html('An Error Occurred!');}