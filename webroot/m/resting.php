<?php
class Resting {
   /*
	* Resting component helps to communicate with remote server providing data 
	*=========================================================================
	*
	*	Example of usage
	*	================
	*	$api=new Resting;
	*	$data=$api->XML_fetch_data('buying_closings/index.json','<company>BOU</company>');
	*	if(!$api->has_response)
	*		echo $data;
	*
	*
	*/
	
	
	public $api_username='';
	public $api_password='';
	public $api_key='my_key';
	public $authorisation_key='';
	public $url = '';				//The business url
	public 	$endpoint='';			//The actually request <controller>/<action>.<extention>
	public	$has_response=false;

	public function __construct(){}
	
	function XML_fetch_data($endpoint='',$xml_request_params=''){	
		$this->endpoint=$endpoint;
		$xml_request='<?xml version="1.0" encoding="UTF-8"?>';
		$xml_request.='<request>'.$xml_request_params.'</request>';
		
		return $this->send_xml_request($xml_request);
	}


	/**
	* Implement cURL request to Servers
	*
	* ------------------------------------------------------------------------------------------------------------------
	*/
	function send_xml_request($xml_request){	
		ini_set('max_execution_time', 300);
		$ch = curl_init(); //initiate the curl session
		
		curl_setopt($ch, CURLOPT_URL, $this->url.$this->endpoint); //set to API endpoint
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		//curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // tell curl to return data in a variable
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: text/xml", 
													"Content-length: ".strlen($xml_request),
													'Authorize: '.$this->authorisation_key.' username='.$this->api_username.'&password='.$this->api_password.'&apikey='.$this->api_key.'&class=Customer'
											));
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_request); // post the xml
		curl_setopt($ch, CURLOPT_TIMEOUT, (int)120); // set timeout in seconds

		$xml_response=false;
		$xml_response = curl_exec($ch);

		if($xml_response === FALSE)
			$this->has_response=false;
		else
			$this->has_response=true;
			
		curl_close ($ch);
		// Get Status
		//$status_code = get_status($xml_response);
		return $xml_response;
	}

	/**
	* Get the Status Message for the Request
	*
	* ------------------------------------------------------------------------------------------------------------------
	*/
	function get_status($xml_response){
		$xml = simplexml_load_string($xml_response);
		if(!$xml)
		{
			return 'false';
		}
		else
		{
			return $xml->Response->StatusCode;
		}
	}


	/**
	* Log any Errors encountered
	*
	* ------------------------------------------------------------------------------------------------------------------
	*/
	function log_errors($xml_response){
				// TODO: log errors to file / database
	}
}
  
?>