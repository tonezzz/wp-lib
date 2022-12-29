<?php //die(__FILE__);
/**
 * Class gz_meminfo - Get memory information from /proc/meminfo
 * - Unit is Bytes
 */
class gz_cpu_info {
	private static $data=false;

	static public function get_info(){
		//if(false==self::$data) self::load_data();
		return [
			'cpu_model' => self::get('model name')
			,'cpu_freq'	=> (self::get('cpu MHz')/1000).'GHz'
			,'cpu_cores' => self::get('cpu cores')
			,'cpu_usage_avg_15_min' => self::get('cpu_usage_avg_15_min')
		];
	}

	static public function get($key){
		if(false==self::$data) self::load_data();
		return self::$data[$key];
	}

	static public function load_data(){
		$res = exec('cat /proc/cpuinfo',$items);
		foreach($items as $item){
			$a = explode(':',$item);
			$key = trim($a[0]);
			$val = @$a[1];
			self::$data[$key] = $val;
		} //die('<pre>'.print_r(self::$data,true));
		//self::$data['cpu_use'] = self::get_cpu_use();
		self::get_cpu_stat();
	}

	static public function get_cpu_stat(){
		//exec('cat /proc/stat',$stat1); ob_clean(); die('<pre>'.print_r($stat1,true));
		$stat = sys_getloadavg(); ob_clean(); //die('<pre>'.print_r($stat,true));
		self::$data['cpu_usage_avg_1_min'] = $stat[0];
		self::$data['cpu_usage_avg_5_min'] = $stat[1];
		self::$data['cpu_usage_avg_15_min'] = $stat[2];
		//$loads = sys_getloadavg();
		//$core_nums = trim(exec("grep -P '^processor' /proc/cpuinfo|wc -l"));
		//$load = round($loads[0]/($core_nums + 1)*100, 2);
		//return $load;
	}

	/**
	 * http://www.geekysolution.com/how-to-get-server-memory-and-cpu-usage-in-php/
	 */
}
