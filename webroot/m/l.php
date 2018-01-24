<?php
	$result="Error:Error occured.";
	if(	isset($_REQUEST['un']) &&
		isset($_REQUEST['pw']) &&
		isset($_REQUEST['ak'])
		){
		
		include_once('resting.php');
		$resting=new Resting();
		$resting->api_username=$_REQUEST['un'];
		$resting->api_password=$_REQUEST['pw'];
		$resting->authorisation_key=$_REQUEST['ak'];
		$resting->url='http://127.0.0.1:80/fx/';
		
		$response=$resting->XML_fetch_data('users/fox_login.json','<Receipts></Receipts>');
		//echo 'NO 1 2 Namanya Hillary';
		
		if($resting->has_response){
			$response_array=json_decode($response);
			if(isset($response_array->data->response->resp_string)){
				echo $response_array->data->response->resp_string;
			}else{
				echo "NO Access denied";
			}
		}else{
			$result="Error:An internal error occured online!";
		}
		
	}else{
		echo "Some required fields are missing.";
	}
	
	
	
	function handleShutdown(){
		echo $GLOBALS['result'];
	}
	
	function cSQL($data){
		return (strip_tags($data));
	}
?>