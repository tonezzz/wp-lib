<?php //die(__FILE__);
/**
 * Class gz_meminfo - Get memory information from /proc/meminfo
 * - Unit is Bytes
 */
class gz_mem_info {
	private static $data=false;

	static public function get_info($prm=[]){
		$items = [
			'Total memory'		=> 'MemTotal'
			,'Available memory'	=> 'MemAvailable'
			,'Swap total'		=> 'SwapTotal'
			,'Swap free'		=> 'SwapFree'
		];
		$prm_exc = false; $prm_inc = false;
		extract($prm,EXTR_PREFIX_ALL,'prm');
		if($prm_exc=='all') $items=[]; elseif(is_array($prm_exc)) $items = array_diff($items,$prm_exc);
		if(is_array($prm_inc)) $items = array_merge($items,$prm_inc);
		$ret = [];
		foreach($items as $label=>$item){
			$ret[$label] = self::get($item);
		}
		return $ret;
	}
	static public function get_info_0(){
		return [
			'mem_total' => self::get('MemTotal')
			,'mem_used' => number_format(100.0*(1-self::get('MemAvailable')/self::get('MemTotal')),2).'%'
			,'swap_total' => self::get('SwapTotal')
			,'swap_used' => number_format(100.0*(1-self::get('SwapFree')/self::get('SwapTotal')),2).'%'
		];
	}
	static public function get_mem_total(){return self::get('MemTotal');}
	static public function get_mem_available(){return self::get('MemAvailable');}
	static public function get_swap_total(){return self::get('SwapTotal');}
	static public function get_swap_free(){return self::get('SwapFree');}

	static public function get($key){
		if(false==self::$data) self::load_data();
		return self::$data[$key];
	}

	static public function load_data(){
		$res = exec('cat /proc/meminfo',$items);
		foreach($items as $item){
			$a = explode(':',$item);
			$key = $a[0];
			$val = str_replace([' ','kB'],'',$a[1]);
			self::$data[$key] = 1024*$val;
		} //die('<pre>'.print_r(self::$data,true));
	}
}
