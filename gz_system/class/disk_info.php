<?php //die(__FILE__);
/**
 * Class gz_disk_info - Get disk & partition information
 * - Unit is Bytes
 */
class gz_disk_info {
	private static $data=false;

	static public function get_php_info(){
		ob_clean(); die('<pre>'.print_r(self::test(),true));
		return [
			'disk_total' 				=> self::get('php','version')
		];
	}

	static public function test(){
		die('xxx');
	}

	static public function get_os_info(){
		return [
			'os_type' 			=> self::get('os','type')
			,'os_release' 		=> self::get('os','release')
			,'os_version' 		=> self::get('os','version')
			,'os_machine_type' 	=> self::get('os','machine_type')
		];
	}

	static public function get_info(){
		return [
			'os_type' 			=> self::get('os','type')
			,'os_release' 		=> self::get('os','release')
			,'os_version' 		=> self::get('os','version')
			,'os_machine_type' 	=> self::get('os','machine_type')
			,'php_version' 				=> self::get('php','version')
			,'php_sapi_name' 			=> self::get('php','sapi_name')
			,'php_memory_usage' 		=> self::get('php','memory_usage')
			,'php_memory_peak_usage' 	=> self::get('php','memory_peak_usage')
			,'php_loaded_extensions' 	=> implode(' ',self::get('php','loaded_extensions'))
		];
	}

	static public function get($group,$key){
		$rs = false;
		if(false==self::$data) self::$data = (object)['os'=>false ,'php'=>false];
		if(false==self::$data->$group) self::load_data($group);
		return self::$data->$group[$key];
	}

	static public function load_data($group){
		switch($group){
			case 'os':self::load_os_info(); break;
			case 'php':self::load_php_info(); break;
		}
	}

	/**
	 * 
	 */
	static public function load_php_info(){
		self::$data->php['version'] 			= phpversion();	//Still hard to read
		self::$data->php['sapi_name'] 			= php_sapi_name();
		self::$data->php['memory_usage'] 		= memory_get_usage(true);
		self::$data->php['memory_peak_usage'] 	= memory_get_peak_usage(true);
		self::$data->php['loaded_extensions'] 	= get_loaded_extensions();
	}

	/**
	 * 
	 * PHP_OS			- Return the OS PHP was compiled for
	 * php_uname('s') 	- Return the runtime OS name
	 */
	static public function load_os_info(){
		//$v = php_uname('n'); ob_clean(); die('<pre>'.print_r($v,true));
		self::$data->os['type'] 		= php_uname('s');
		self::$data->os['host_name'] 	= php_uname('n');
		self::$data->os['release'] 		= php_uname('r');
		self::$data->os['version'] 		= php_uname('v');
		self::$data->os['machine_type'] = php_uname('m');
		//$items = php_uname(); ob_clean(); die('<pre>'.print_r($items,true));
		//$items = getrusage(); ob_clean(); die('<pre>'.print_r($items,true));
		/*
		foreach($items as $item){
			$a = explode(':',$item);
			$key = $a[0];
			$val = str_replace([' ','kB'],'',$a[1]);
			self::$data[$key] = 1024*$val;
		} //die('<pre>'.print_r(self::$data,true));
		*/
	}
}
