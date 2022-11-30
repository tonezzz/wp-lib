<?php //die(__FILE__);
/*
v1.00 - 20220422:Tony:Implement automatic js update fields
v0.00 - 20220416:Tony:Prepare panel & import module
*/
class gz_stock_chart extends gz_tpl{
	
	public function __construct(){//die(__FILE__.__FUNCTION__);
		$this->test_data = [ 
			["x" => 1514485800000, "y" => [54.15 ,54.55 ,53.65 ,53.85]],
			["x" => 1514399400000, "y" => [54.6 ,54.7 ,53.75 ,54.15]],
			["x" => 1514313000000, "y" => [55.4 ,55.5 ,54.05 ,54.85]],
			["x" => 1513881000000, "y" => [56 ,56.2 ,54.9 ,55.4]],
			["x" => 1513794600000, "y" => [54.85 ,56.15 ,54.6 ,56.05]],
			["x" => 1513708200000, "y" => [55.8 ,56 ,54.45 ,54.75]],
			["x" => 1513621800000, "y" => [56.5 ,56.5 ,55.65 ,55.75]],
			["x" => 1513535400000, "y" => [55.15 ,56.8 ,55.1 ,56.55]],
			["x" => 1513276200000, "y" => [55.35 ,55.4 ,54.75 ,55.1]],
			["x" => 1513189800000, "y" => [55.95 ,56.2 ,54.2 ,55.45]],
			["x" => 1513103400000, "y" => [53.75 ,56.5 ,53.7 ,55.9]],
			["x" => 1513017000000, "y" => [53.5 ,53.95 ,53 ,53.8]],
			["x" => 1512930600000, "y" => [53 ,53.1 ,52.15 ,52.65]],
			["x" => 1512671400000, "y" => [53.15 ,53.5 ,52.7 ,52.9]],
			["x" => 1512585000000, "y" => [52.7 ,53.45 ,52.6 ,52.85]],
			["x" => 1512498600000, "y" => [52.85 ,52.85 ,51.6 ,52.4]],
			["x" => 1512412200000, "y" => [52.45 ,53.45 ,52.1 ,53.25]],
			["x" => 1512325800000, "y" => [52.4 ,53.8 ,52.2 ,52.65]],
			["x" => 1512066600000, "y" => [52.5 ,52.95 ,51.85 ,51.95]]
		];
		
		$config = [
			'enqueue'  => [
				['type'=>'script' ,'load'=>true ,'prm'=>['canvasjs','https://canvasjs.com/assets/script/canvasjs.min.js']],
				['type'=>'style' ,'prm'=>['jquery-ui','//code.jquery.com/ui/1.13.1/themes/base/jquery-ui.css']],
				['type'=>'style' ,'load'=>true ,'prm'=>[__CLASS__,'[REL_PATH]wp_style.scss',['jquery-ui','canvasjs']]],
				['type'=>'script' ,'load'=>true ,'prm'=>[__CLASS__,'[REL_PATH]_wp_script.js',['jquery-core','jquery-ui-core','jquery-ui-tabs']]],
				['type'=>'localize', 'prm'=>[__CLASS__,__CLASS__,['ajax_url'=>admin_url('admin-ajax.php')]]],
			]
			,'shortcodes' => [
				['prm'=>['gz_stock_chart',[$this,'gz_stock_chart']]],
			],
		  	'ajaxes' => [
		 		['prm'=>['get_chart_data',[$this,'get_chart_data']]],
		 	]
		];
		$this->wpdb = $GLOBALS['wpdb'];
		$this->table_name = $this->wpdb->prefix.'stock_price';
		parent::__construct($config); //ob_clean(); echo '<pre>'; print_r($config); die();
	}
	
	function get_chart_data($atts=[],$content=false){
		$atts = shortcode_atts([
			'ticker'=> isset($_GET['ticker'])?	$_GET['ticker']		:'AAV',
			'd0'	=> isset($_GET['d0'])?		$_GET['d0']			:'2019-01-01',
			'd1'	=> isset($_GET['d1'])?		$_GET['d1']			:'2019-12-31',
			'prms'		=> isset($_GET['prms'])	?$_GET['prms']	:false,
			'output'	=> isset($_GET['output'])?	$_GET['output']	:'json',
			'echo'		=> isset($_GET['echo'])?	$_GET['echo']	:true,
		],$atts); //die('<pre>'.print_r(compact('atts'),true));
		if($atts['prms']) $atts = array_merge($atts,unserialize(urldecode($atts['prms'])));
		//if($atts['prms']) $atts['prms1'] = (urldecode($atts['prms']));
		extract($atts, EXTR_PREFIX_ALL,'att');
		$sql = $this->wpdb->prepare("
			SELECT UNIX_TIMESTAMP(date),open,high,low,close FROM {$this->table_name} WHERE ticker=%s AND (date >= %s and date <= %s)
		",$att_ticker,$att_d0,$att_d1);
		$rs = $this->wpdb->get_results($sql,ARRAY_N); //die('<pre>'.print_r(compact('sql','atts','rs'),true));
		$data = [];
		foreach($rs as $item){
			$data[] = ['x'=>$item[0] ,'y'=>[$item[1],$item[2],$item[3],$item[4]]];
		}
		
		$info = [];
		$info['success'] = 1;
		//$info['data']['chart_data'] = $this->test_data;
		$info['data']['chart_data'] = $data;
		//die('<pre>'.print_r(compact('info','sql','atts','rs'),true));
		if($att_output=='json'){
			$html = json_encode($info,JSON_NUMERIC_CHECK );
		} else {
			$html = '<pre>'.print_r($info,true).'</pre>';
		}

		if($att_echo) {echo $html; die(0);} else return $html;
	}

	function render_stock_chart_panel($atts){
		$html = '';
		$html.= "<div id='gz_chart' class='gz_ajax' data-action='get_chart_data'>";
		$html.= "<div id='chart_canvas' class='gz_val gz_chart' data-id='chart_data' data-type='chart'></div>";
		$html.= "</div>";
		return $html;
	}

	function gz_stock_chart($atts,$content=false){
		$atts = shortcode_atts([
			'd0'	=> isset($_GET['d0'])?		$_GET['d0']		:'2021-01-01',
			'd1'	=> isset($_GET['d1'])?		$_GET['d1']		:'2021-12-31',
			'prms'		=> isset($_GET['prms'])	?$_GET['prms']	:false,
			'output'=> isset($_GET['output'])?	$_GET['output']	:'json',
			'echo'	=> isset($_GET['echo'])?	$_GET['echo']	:true,
		],$atts,'gz_stock_panel');
		if(is_array($atts['prms'])) $atts = array_merge($atts,unserialize(urldecode($atts['prms'])));
		extract($atts, EXTR_PREFIX_ALL,'att'); //print_r(compact('atts','att_test','atttest'),true); die();
		$html ='';
		$html.="<div class='gz_stock_chart' data-id='gz_stock_chart'>";
		$html.=$this->render_stock_chart_panel($atts);
		$html.= "</div>";
		return $html;
	}

}
