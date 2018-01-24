<?php
class FuncComponent extends Component {
	
	public function getUID1(){
		return md5(date('Y-m-dh:i:s').($this->getMicrotime()));
	}
	
	private function getMicrotime()	{
		if (version_compare(PHP_VERSION, '5.0.0', '<'))
		{
			return array_sum(explode(' ', microtime()));
		}

		return microtime(true);
	}
	
}
  
?>