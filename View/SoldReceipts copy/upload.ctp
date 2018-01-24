<?php echo $this->Html->script(array('script_dynamic_content'));?>
<div class="flash-message"><?php echo $this->Session->flash(); ?></div>
<div class="well">
	Only new Sales receipts will be uploaded. Click start to start the upload. <hr/>
	Receipts to upload:<span class="receipts_to_upload"><?php echo $receipt_count; ?></span>
</div>
<center>
<div class="btn-group">
<span class="btn btn-primary" id="start-upload"><i class="icon-white icon-play"></i> Start</span>
</div><br/><br/>
<span></span>
<div class="progress progress-striped active">
  <div class="initial_count" initial_count=<?php echo $receipt_count; ?> style="display:none;"></div>
  <div class="current_count" current_count=-1 style="display:none;"></div>
  <div class="bar" style="width: 0%;"></div>
</div>
</center>

<div class="well">
<h2>Error Logs</h2>
	<div class="remote-error"></div>
	<div style="max-height:200px;overflow:auto;" class="process-logs">
	</div>
</div>

<script>
	$(document).ready(function(){
		setInterval(function(){
			if(started){ 
				doUpload();
				get_remaining_number();
				calculate_progress();
			}
		},1000);
		
		$('#start-upload').click(function(){
			started=true;
		});
	});	
	function calculate_progress(){
		var ini_val=$('.initial_count').attr('initial_count');
		var cur_val=$('.current_count').attr('current_count');
		if(cur_val>0 && cur_val<=ini_val){
			var _progress=(1-(cur_val/ini_val))*100;
			$('.bar').css('width',_progress+'%');
		}else if(cur_val==0){
			$('.bar').css('width','100%');
		}
	}
	
	var started=false;
	var upload_lock=0;
	var remainder_lock=0;
	function get_remaining_number(){
		if(remainder_lock==0)remainder_lock=1;
		else return;
		
		var cur_val=$('.current_count').attr('current_count');
		if(remainder_lock==1 && cur_val!=0){
			$.ajax({
				url: '<?php echo $this->webroot;?>sold_receipts/get_new_receipts_count',
				data: {},					
				success: function(data) {
					remainder_lock=0;
					$('.current_count').attr('current_count',data);
				},
				error: function() {remainder_lock=0;}
			});
		}else{
			remainder_lock=0;
		}
	}
	function doUpload(){
		if(upload_lock==0)upload_lock=1;
		else return;
		
		var cur_val=$('.current_count').attr('current_count');
		if(upload_lock==1 && cur_val>0){
			$.ajax({
				url: '<?php echo $this->webroot;?>sold_receipts/send_new_receipts',
				data: {},					
				success: function(data) {
					upload_lock=0;
					if(data.data.response.unsaved_string!='undefined')
						$('.remote-error').html('<p><b>Remote(BOU)-'+(data.meta.feedback[0].level)+': '+(data.meta.feedback[0].message)+'</b></p>');
					if(data.meta.feedback[0].level!='undefined'){
						$('.remote-error').html("<p>Unsaved("+(data.data.response.unsaved_string).length+"):"+(data.data.response.unsaved_string)+"</p>");
					}
				},
				statusCode: {
				  200: function (response) {},
				  500: function (response) {$('.process-logs').html('<p style="color:red;">Internal error occured on your local server.</p>');},
				  400: function (response) {$('.process-logs').prepend('<p style="color:red;">Bad Request to your local server.</p>');},
				  401: function (response) {$('.process-logs').prepend('<p style="color:red;">Unauthorised request to your server. Please login and try again.</p>');},
				  403: function (response) {$('.process-logs').html('<p style="color:red;"></b>Forbidden</b> from the server. You need to <b>login</b> first.</p>');},
				  404: function (response) {$('.process-logs').prepend('<p style="color:red;">The server has not found anything matching the Request-URI.</p>');}
			   },
				error: function() {upload_lock=0;$('.process-logs').prepend('<p style="color:orange">Server connection failed...</p>');}
			});
		}else{
			upload_lock=0;
		}	
	}
</script>