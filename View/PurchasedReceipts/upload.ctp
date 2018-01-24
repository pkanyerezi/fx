<?php echo $this->Html->script(array('script_dynamic_content'));?>
<div class="flash-message"><?php echo $this->Session->flash(); ?></div>
<div class="well">
	Only new Sales receipts will be uploaded. Click start to start the upload. <hr/>
	Receipts to upload:<span class="receipts_to_upload"><?php echo $receipt_count; ?></span>
</div>
<center>
<div class="btn-group">
<span class="btn btn-primary" id="p_start-upload"><i class="icon-white icon-play"></i> Start</span>
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
			if(p_started){ 
				p_doUpload();
				p_get_remaining_number();
				p_calculate_progress();
			}
		},1000);		
		$('#p_start-upload').click(function(){
			p_started=true;
		});
	});	
	
	function p_calculate_progress(){
		var ini_val=$('.initial_count').attr('initial_count');
		var cur_val=$('.current_count').attr('current_count');
		if(cur_val>0 && cur_val<=ini_val){
			var _progress=(1-(cur_val/ini_val))*100;
			$('.bar').css('width',_progress+'%');
		}else if(cur_val==0){
			$('.bar').css('width','100%');
		}
	}
	
	var p_started=false;
	var p_upload_lock=0;
	var p_remainder_lock=0;
	function p_get_remaining_number(){
		if(p_remainder_lock==0)p_remainder_lock=1;
		else return;
		
		var cur_val=$('.current_count').attr('current_count');
		if(p_remainder_lock==1 && cur_val!=0){
			$.ajax({
				url: '<?php echo $this->webroot;?>purchased_receipts/get_new_receipts_count',
				data: {},					
				success: function(data) {
					p_remainder_lock=0;
					$('.current_count').attr('current_count',data);
				},
				error: function() {p_remainder_lock=0;}
			});
		}else{
			p_remainder_lock=0;
		}
	}
	function p_doUpload(){
		if(p_upload_lock==0)p_upload_lock=1;
		else return;
		
		var cur_val=$('.current_count').attr('current_count');
		if(p_upload_lock==1 && cur_val>0){
			$.ajax({
				url: '<?php echo $this->webroot;?>purchased_receipts/send_new_receipts',
				data: {},
				dataType: "json",				
				success: function(data) {
					p_upload_lock=0;
					if(data.data.response.unsaved_string!='undefined')
						$('.remote-error').html('<p><b>Remote(BOU)-'+(data.meta.feedback[0].level)+': '+(data.meta.feedback[0].message)+'</b></p>');
					if(data.meta.feedback[0].level!='undefined'){
						if(data.meta.feedback[0].message!='undefined'){						$('.remote-error').html("<p> <b>Error from Remote server</b>  -<span style='color:red;'>"+(data.meta.feedback[0].message)+"</span></p>");
						}
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
				error: function() {p_upload_lock=0;$('.process-logs').prepend('<p style="color:orange">Server connection failed...</p>');}
			});
		}else{
			p_upload_lock=0;
		}	
	}
</script>