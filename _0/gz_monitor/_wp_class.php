<?php //die(__FILE__);
/*
 * gz_monitor: Read json data and display info
 * - v0.00-20220624-Tony:
 *   - $atts is now used as object
 *	 - Display multiple hosts (via tabs)

Example:
[gz_monitor path="../../gz_monitor/"]
	- path	: path to data (uses ABSPATH.$path)
	
 //files
*/
class gz_monitor extends gz_tpl{
	public function __construct(){//die(__FILE__.__FUNCTION__);
		$config = [
			'enqueue'  => [
				['type'=>'script' ,'load'=>true ,'prm'=>['chartjs','https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.8.0/chart.min.js']],
				['type'=>'style' ,'prm'=>['jquery-ui','//code.jquery.com/ui/1.13.1/themes/base/jquery-ui.css',]],
				['type'=>'style' ,'load'=>true ,'prm'=>[__CLASS__,'[REL_PATH]wp_style.scss',['jquery-ui']]],
				['type'=>'script' ,'load'=>true ,'prm'=>[__CLASS__,'[REL_PATH]wp_script.js',['chartjs','jquery-core','jquery-ui-core','jquery-ui-tabs']]],
				['type'=>'localize', 'prm'=>[__CLASS__,__CLASS__,['ajax_url'=>admin_url('admin-ajax.php')]]],
			],
			'shortcodes' => [
				['prm'=>['gz_monitor',[$this,'gz_monitor']]],
			],
			'ajaxes' => [
				['prm'=>['get_host_info',[$this,'get_host_info']]]
			]
		];
		parent::__construct($config); //ob_clean(); echo '<pre>'; print_r($config); die();
	}
	
	function gz_monitor($atts=[],$content=false){ //die('<pre>'.print_r(compact('html','atts'),true));
		$atts = shortcode_atts([
			//'host'		=> isset($_GET['host'])		?$_GET['host']		:'wsl_09',	//Data path (from web root)
			'path'		=> isset($_GET['path'])		?$_GET['path']		:'/gz_monitor',	//Data path (from web root)
			'class'		=> isset($_GET['class'])	?$_GET['class']		:'',	//Container class
			'prms'		=> isset($_GET['prms'])		?$_GET['prms']		:false,
			'output'	=> isset($_GET['output'])	?$_GET['output']	:'html',
			'echo'		=> isset($_GET['echo'])		?$_GET['echo']		:false,
		],$atts);
		if(is_array($atts['prms'])) $atts = array_merge($atts,unserialize(urldecode($atts['prms']))); $atts = (object)$atts;
		$atts->full_path = ABSPATH.$atts->path;
		$html = "<div class='gz_monitor {$atts->class}'>";
			$html.= $this->render_monitor_info($atts);
		$html.= "</div>";
		//Debug info
		$html.='<pre>'.print_r($atts,true).'</pre>';
		//$atts->fn = $atts->full_path.$atts->host.'.json';
		return $html;
	}
	
	function render_monitor_info($atts){
		$fp = ABSPATH.$atts->path;
		$files = glob($fp.'*.json'); $atts->files = $files;
		return $this->render_info_tabs($files);
	}
	
	function render_info_tabs($files){
		$tabs = '';
		$panels = '';
		foreach($files as $file){
			$host_id = basename($file,'.json');
			$info_url = admin_url("admin-ajax.php?action=get_host_info&host={$host_id}");
			$tabs.= "<li rel='#{$host_id}'><a href='{$info_url}'><span>{$host_id}</span></a></li>";
			//$panels.= "<div id='{$host_id}'></div>";
		}
		$html = "<div class='info_tabs'><ul class='info'>{$tabs}</ul>{$panels}</div>";
		return $html;
	}
	
	function get_host_info($atts=[],$content=false){ //die('<pre>'.print_r(compact('atts',true)));
		$atts = shortcode_atts([
			'host'		=> isset($_GET['host'])		?$_GET['host']		:'wsl_09',	//Data path (from web root)
			'path'		=> isset($_GET['path'])		?$_GET['path']		:'../../gz_monitor/',	//Data path (from web root)
			'class'		=> isset($_GET['class'])	?$_GET['class']		:'',	//Container class
			'prms'		=> isset($_GET['prms'])		?$_GET['prms']		:false,
			'output'	=> isset($_GET['output'])	?$_GET['output']	:'html',
			'echo'		=> isset($_GET['echo'])		?$_GET['echo']		:true,
		],$atts); //die('<pre>'.print_r($atts,true));
		if(is_array($atts['prms'])) $atts = array_merge($atts,unserialize(urldecode($atts['prms']))); $atts = (object)$atts;
		$atts->full_path = ABSPATH.$atts->path;
		$atts->fn = $atts->full_path.$atts->host.'.json';
		$html = "<div class='gz_monitor {$atts->class}'>";
			$host_info = json_decode(file_get_contents($atts->fn));
			//$host_info = json_decode(file_get_contents($atts->path.$atts->host.'.json'));
			$html.= $this->render_host_info($host_info);
			//Debug info
			$atts->host_info=$host_info; $html.='<pre>'.print_r($atts,true).'</pre>';
		$html.= "</div>";
		if($atts->echo) {echo $html; return false;} else return $html;
	}
	
	function render_host_info($info){
		$html = "<div class='host_info'>";
		//$html.= $this->render_host_info_sub($this->gen_disk_info($info));
		$html.= $this->gen_chart($info);
		//$html.= "<hr style='margin:40px 0;'>";
		$html.= $this->render_host_info_sub($info);
		$html.= "</div>";
		return $html;
	}
/*	
				'labels' => [
					'RAM Use',
					'RAM Free',
					'Swap Use',
					'Swap Free',
				],
				'datasets' => [
				    'label' => 'Memory',
					//['data' => [$info->memory->total, $info->memory->free, $info->memory->total, $info->memory->free]]
					['data' => [50, 40, 60, 50]],
				],
*/
	function gen_chart($info){
		$chart = (object)[
			'type' => 'pie',
			'data' => [
				'labels' => [
					'Use',
					'Free',
				],
				'datasets' => [
					['label'=>'RAM' ,'data'=>[40, 20] ,'backgroundColor'=>['Blue','Yellow']],
					['label'=>'SWAP' ,'data'=>[40, 60] ,'backgroundColor'=>['Red','Green']],
				],
			]
		];
		$chart_st = json_encode($chart);
		$html.= "<div class='graphs'>";
		$html.= "<div class='gz_graph pie' data-graph='{$chart_st}'></div>";
		$html.= "<div class='gz_graph pie' data-graph='{$chart_st}'></div>";
		$html.= "</div>";
		return $html;
	}
	
	function render_host_info_sub($info){
		$html = '';
		foreach($info as $k=>$v){
			if( is_object($v) || is_array($v) ){
				$html.= "<div class='group {$k}'>";
				$html.= "<span class='title'>{$k}</span>";
				$html.= "<ul class='items'>";
				$html.= $this->render_host_info_sub($v);;
				$html.= "</ul>";
				$html.= "</div>";
			}else{
				$html.= "<li class='item'><span class='label'>{$k}</span><span>{$v}</span></li>";
			}
		}
		return $html;
	}

}
