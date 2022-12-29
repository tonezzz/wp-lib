<?php //die(__FILE__);
/**
 * Class gz_dir_info - Get path & file information
 * - Unit is Bytes
 */
class gz_dir_info {
	private static $data=false;

	static public function get_info(){
		//ob_clean(); die('<pre>'.print_r(self::test(),true));
		return [
			'/' 	=> self::get_dir_size('/',false)
		];
	}

	static public function get_wp_info($prm=[]){//die(ABSPATH);
		$paths = [
			''
			,'wp-content'
			,'wp-content/themes'
			,'wp-content/plugins'
			,'wp-content/updraft'
			,'wp-content/uploads'
		];
		extract($prm,EXTR_PREFIX_ALL,'prm');
		if(is_array($prm_exc)) $paths = array_diff($paths,$prm_exc);
		if(is_array($prm_inc)) $paths = array_merge($paths,$prm_inc);
		$res = [];
		$res['/'] = self::get_dir_size();
		foreach($paths as $path){
			$key = 'wp/'.$path;
			$wp_path = ABSPATH.$path;
			$res[$key] = self::get_dir_size($wp_path);
		}
		return $res;
	}

	static public function test(){
		die('xxx');
	}

	/**
	 * function dir_info::get - Get info of dir
	 * 	- exc => Array of dir to exclue
	 * 	- inc => Array of dir to include
	 */
	static public function get($prm=[]){//die(ABSPATH);
		$paths = [
		];
		extract($prm,EXTR_PREFIX_ALL,'prm');
		if(is_array($prm_exc)) $paths = array_diff($paths,$prm_exc);
		if(is_array($prm_inc)) $paths = array_merge($paths,$prm_inc);
		$res = [];
		$res['/'] = self::get_dir_size();
		foreach($paths as $path){
			$key = $path;
			$res[$key] = self::get_dir_size($path);
		}
		return $res;
	}

	/**
	 * function get_dir_size() - Get size of dir
	 */
	static public function get_dir_size($dir='',$unit=true){
		$units = ['Bytes','KB','MB','GB','TB','PB'];
		$object = self::load_data($dir);
		$size = isset($object['total_size'])?$object['total_size']:false;
		if($unit){
			for($i=0;($i<count($units))&&($size>1000);$i++) {$size = $size/1024;}
			$ret = number_format($size,2).$units[$i];
		}else{
			$ret = $size;
		}
		return $ret;
	}

	/**
	 * function load_data() - Load size info of dir
	 */
	static public function load_data($dir=''){
		if(self::$data===false) self::$data = self::do_get_dir_info(); //ob_clean(); die("<pre>".print_r($this->dir_info,true)."</pre>");
		$dir1 = realpath($dir);
		$dir2 = str_replace(self::$data['root'],'',$dir1);
		$items = explode('/',$dir2); //die("<pre>".print_r(compact('path0','path1','path2','items'),true)."</pre>");
		$object = self::$data;
		foreach($items as $item) if(!empty($item)){//ob_clean(); die("<pre>".print_r($object['dirs'][$item],true)."</pre>");
			if(isset($object['dirs'][$item])) $object = $object['dirs'][$item]; else {$object = false; break;}
		}
		//ob_clean(); die("<pre>".print_r($object,true)."</pre>");
		return $object;
	}

	static public function do_get_dir_info($dir=false,$get_files=false){//die($get_files);
		if(false===$dir) $dir = $_SERVER['DOCUMENT_ROOT'];
		$transient_key = 'gz_dir_info'; //ob_clean(); die("<pre>".print_r(get_transient($transient_key),true));
		if(isset($_GET['clear_'.$transient_key])) delete_transient($transient_key); //die("<pre>".print_r(get_transient($transient_key),true));
		if(false===($res=get_transient($transient_key))){
			$i=0;
			$path = realpath($dir); //die($path);
			if($path!==false && $path!='' && file_exists($path)){
				$res = self::do_get_dir_info($path,$get_files);
				$res['root'] = $path;
				set_transient($transient_key,$res,10*60*60);
			}
		}
		//ob_clean(); ob_clean(); die("<pre>".print_r(compact('dir','path','res'),true)."</pre>");
		return $res;
	}

}
