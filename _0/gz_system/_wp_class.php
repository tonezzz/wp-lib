<?php //die(__FILE__);
/**
 * 
 */

require __DIR__ .'/class/gz_system_ut.php';
require __DIR__ .'/class/mem_info.php';
require __DIR__ .'/class/cpu_info.php';
require __DIR__ .'/class/server_info.php';
require __DIR__ .'/class/disk_info.php';
require __DIR__ .'/class/dir_info.php';

class gz_system extends gz_tpl {
	private $dir_info=false;
	private $wp_dir_info=false;
	public function __construct(){//die(__FILE__.__FUNCTION__);
		//$d = gz_cpuinfo::get_cpu_info(); die('<pre>'.print_r($d,true));
		$config = [
			'enqueue'  => [
				['type'=>'style' ,'load'=>true ,'prm'=>[__CLASS__,'[REL_PATH]_wp_style.scss',[]]]
				//['type'=>'script' ,'load'=>true ,'prm'=>[__CLASS__,'[REL_PATH]_wp_script.js',['jquery-ui-core','jquery-ui-tabs']]]
				//,['type'=>'style' ,'load'=>true ,'prm'=>['jquery-ui',$jquery_ui_path.'/themes/smoothness/jquery-ui.css',[]]]
			]
			,'shortcodes' => [
				['prm'=>['gz_cpu_info',[$this,'render_cpu_info_html']]]
				,['prm'=>['gz_memory_info',[$this,'render_memory_info_html']]]
				,['prm'=>['gz_server_info',[$this,'render_server_info_html']]]
				,['prm'=>['gz_os_info',[$this,'render_os_info_html']]]
				,['prm'=>['gz_php_info',[$this,'render_php_info_html']]]
				,['prm'=>['gz_wp_info',[$this,'render_wp_info_html']]]
				,['prm'=>['gz_disk_info',[$this,'render_disk_info_html']]]
				,['prm'=>['gz_dir_info',[$this,'render_dir_info_html']]]
			]
			,'ajaxes' => [
				['prm'=>['gz_system_info',[$this,'render_gz_system_info']]]
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

	/**
	 * 	'ajaxes' => [
	 *		['prm'=>['gz_info',[$this,'render_gz_info']]]
	 *	]
	 * =IMPORTXML("https://surf-thailand.com.local/v3/wp/wp-admin/admin-ajax.php?action=gz_system_info,"//root")
	 */
	function render_gz_system_info(){
		$cpu = gz_cpu_info::get_info();
		$mem = gz_mem_info::get_info();
		$info = array_merge($cpu,$mem);
		die($this->render_info_table($info));
		/*
		//$html = '';
		//$info = gz_cpu_info::get_info();
		//$html.= '<pre>'.print_r($info,true).'<pre>';
		//echo $html; die(0);
		$xml = new SimpleXMLElement('<root/>');
		//$label = 'x'; $value = 'y';
		self::add_item($xml,'gz_mem_info','mem_total');
		self::add_item($xml,'gz_mem_info','mem_used');
		self::add_item($xml,'gz_mem_info','swap_total');
		self::add_item($xml,'gz_mem_info','swap_used');
		self::add_item($xml,'gz_cpu_info','cpu_usage_avg_1_min');
		self::add_item($xml,'gz_cpu_info','cpu_usage_avg_5_min');
		self::add_item($xml,'gz_cpu_info','cpu_usage_avg_15_min');
		//self::add_item($xml,$label,$value);

		ob_clean(); header("Content-Type:text/xml"); die($xml->asXML());
		//die($xml->asXML());
		*/
	}
	function add_item($xml,$class,$label,$value=''){
		$value = $class::get($label);
		$node = $xml->addChild('item'); $node->addChild('label',$label); $node->addChild('value',$value);
	}

	function render_cpu_info_html(){
		$info = gz_cpu_info::get_info(); return $this->render_info_html($info);
	}

	function render_memory_info_html(){
		$info = gz_mem_info::get_info(); return $this->render_info_html($info);
	}

	function render_disk_info_html(){
		$info = gz_disk_info::get_info(); return $this->render_info_html($info);
	}

	function render_server_info_html(){
		//$info = $this->get_server_info(); return $this->render_info_html($info);
		$info = gz_server_info::get_info();  return $this->render_info_html($info);
	}

	function render_os_info_html(){
		$info = gz_server_info::get_os_info(); return $this->render_info_html($info);
	}

	function render_php_info_html(){
		$info = gz_server_info::get_php_info(); return $this->render_info_html($info);
	}

	function render_wp_info_html($atts,$content=false){
		$info = gz_dir_info::get_wp_info($atts); return $this->render_info_html($info);
	}

	function render_dir_info_html($atts,$content=false){
		$info = gz_dir_info::get_info($atts); return $this->render_info_html($info);
	}

	/**
	 * 
	 */
	function render_info_table($info){
		$html = "<table class='gz_server_info'>";
		foreach($info as $key=>$val){
			$html.= "<tr class='item'>";
			$html.= "<td class='key'>{$key}</td>";
			if(is_array($val)) $val.= implode(', ',$val);
			$html.= "<td class='val'>{$val}</td>";
			$html.= "</tr>";
		}
		$html.= "</table>";
		return $html;
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
		return $html;
	}
}
