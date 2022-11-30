<?php //die(__FILE__);
/**
 * 
 */
class gz_system extends gz_tpl {	
	public function __construct(){//die(__FILE__.__FUNCTION__);
		$config = [
			'enqueue'  => [
				['type'=>'style' ,'load'=>true ,'prm'=>[__CLASS__,'[REL_PATH]_wp_style.scss',[]]]
				//['type'=>'script' ,'load'=>true ,'prm'=>[__CLASS__,'[REL_PATH]_wp_script.js',['jquery-ui-core','jquery-ui-tabs']]]
				//,['type'=>'style' ,'load'=>true ,'prm'=>['jquery-ui',$jquery_ui_path.'/themes/smoothness/jquery-ui.css',[]]]
			]
			,'shortcodes' => [
				['prm'=>['gz_server_info',[$this,'render_server_info_html']]]
				,['prm'=>['gz_php_info',[$this,'render_php_info_html']]]
				,['prm'=>['gz_wp_info',[$this,'render_wp_info_html']]]
			]
			//,'ajaxes' => [
			//	['prm'=>['gz_user_login',[$this,'do_gz_user_login']]]
			//]
			/*
			,'enqueue_login'  => [
				['type'=>'style' ,'load'=>true ,'prm'=>[__CLASS__,'[REL_PATH]_wp_style.scss',[]]]
			]
			,'debug'=>true
			*/
		]; //ob_clean(); echo '<pre>'; print_r($config); die();
		parent::__construct($config); //ob_clean(); echo '<pre>'; print_r($config); die();
	}

	function render_server_info_html(){
		$info = $this->get_server_info(); return $this->render_info_html($info);
	}

	function render_php_info_html(){
		$info = $this->get_php_info(); return $this->render_info_html($info);
	}

	function render_wp_info_html(){
		$info = $this->get_wp_info(); return $this->render_info_html($info);
	}

	/**
	 * 
	 */
	function render_info_html($info){
		$html = "<ul class='gz_server_info'>";
		foreach($info as $key=>$val){
			$html.= "<li class='item'>";
			$html.= "<span class='key'>{$key}</span>";
			if(is_array($val)) $val = "<span class='block'>".implode(', ',$val)."</span>";
			$html.= "<span class='val'>{$val}</span>";
			$html.= "</li>";
		}
		$html.= "</ul>";
		$html.= "<pre>".print_r($GLOBALS,true)."</pre>";
		//$html.= "<pre>".print_r(get_defined_vars(),true)."</pre>";
		//ob_clean(); die("<pre>".print_r($GLOBALS,true)."</pre>");
		//ob_clean(); die("<pre>".print_r($_ENV,true)."</pre>");
		//ob_clean(); die("<pre>".print_r(getenv(),true)."</pre>");
		//ob_clean(); die("<pre>".print_r(foldersize('.'),true)."</pre>");
		//$this->test();
		return $html;
	}

	function test(){
		$f = '.';
		$io = popen('/usr/bin/du -sk'.$f,'r');
		$rs = fgets($io,4096);
		ob_clean(); die("<pre>".print_r(compact('f','io','rs'),true)."</pre>");
	}

	function get_server_info(){
		$res = (object)[];
		$res->uname = php_uname(); 			//Still very hard to read
		$res->get_loadavg = sys_getloadavg();
		$res->getrusage = getrusage();
		//$res->phpinfo = phpinfo();
		//$res->CPU = system("cat /proc/cpuinfo | grep \"model name\\|processor\"");
		//$res->env = getenv();
		//$res->disk_total_space = disk_total_space('/');
		//$kernel = explode(' ', file_get_contents('/proc/version'));
		//$res->kernel_version = $kernel;
		return $res;
	}

	function get_php_info(){
		$res = (object)[];
		$res->php_version = phpversion();	//Still hard to read
		$res->php_sapi_name = php_sapi_name();
		$res->memory_usage = memory_get_usage(true);
		$res->memory_peak_usage = memory_get_peak_usage(true);
		$res->loaded_extensions = get_loaded_extensions();
		//$res->get_defined_vars = get_defined_vars();
		//$res->get_extension_funcs = get_extension_funcs();
		return $res;
	}

	function get_wp_info(){//die(ABSPATH);
		$res = (object)[];
		//$path = ABSPATH.'wp-content/uploads/2017/';
		$path = ABSPATH.'wp-content/plugins/gz-surf-thailand/_sys/tpl/gz_system/';
		$res->dir_size = $this->get_dir_size($path,true);
		return $res;
	}

	function get_dir_size($dir='.',$get_files=false){//die($get_files);
		$i=0;
		$path = realpath($dir); //die($path);
		if($path!==false && $path!='' && file_exists($path)){
			$res = $this->get_path_info($path,$get_files);
		}
		ob_clean(); die("<pre>".print_r(compact('bytestotal','path','res'),true)."</pre>");
		return $res;
	}

	function get_path_info($path,$get_files=false){
		$res = [];
		$iterators = new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS); //ob_clean(); die("<pre>".print_r(compact('path','iterators'),true)."</pre>");
		$name = basename($path);
		$res[$name]['size'] = 0;
		$res[$name]['total_size'] = 0;
		foreach($iterators as $object){
			if($object->isFile()){
				if($get_files){
					$file_size = $object->getSize();
					$res[$name]['files'][$object->getFilename()] =[
						'size' => $file_size
					];
				}
				$res[$name]['size']+=$file_size;
			}else{
				$res[$name]['dirs'] = $this->get_path_info($path.'/'.$object->getFilename());
			}
		}
		//die("<pre>".print_r($res[$path]['dirs'],true)."</pre>");
		//if(is_array($res[$name]['dirs'])){
		//	foreach($res[$name]['dirs'] as $key=>&$dir){//die("<pre>".print_r(compact('path','key','dir'),true)."</pre>");
		//		$dir = $this->get_path_info($path.'/'.$key);
		//	}
		//}
		return $res;
	}
}
