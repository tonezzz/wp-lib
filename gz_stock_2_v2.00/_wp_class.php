<?php //die(__FILE__);
/*
v1.01 - 20220505:Tony:.gz_val is now for update, use data-type to determind the data type.
v1.00 - 20220422:Tony:Implement automatic js update fields
v0.00 - 20220416:Tony:Prepare panel & import module
*/
class gz_stock_2 extends gz_tpl{
	public function __construct(){//die(__FILE__.__FUNCTION__);
		$config = [
			'enqueue'  => [
				['type'=>'style' ,'prm'=>['jquery-ui','//code.jquery.com/ui/1.13.1/themes/base/jquery-ui.css',]],
				['type'=>'style' ,'load'=>true ,'prm'=>[__CLASS__,'[REL_PATH]wp_style.scss',['jquery-ui']]],
				['type'=>'script' ,'load'=>true ,'prm'=>[__CLASS__,'[REL_PATH]_wp_script.js',['gz_panel']]],
				['type'=>'script' ,'load'=>true ,'prm'=>['gz_panel','[REL_PATH]gz_panel.js',['jquery-core','jquery-ui-core','jquery-ui-tabs']]],
				['type'=>'localize', 'prm'=>[__CLASS__,__CLASS__,['ajax_url'=>admin_url('admin-ajax.php')]]],
			]
			,'shortcodes' => [
				['prm'=>['gz_stock_2_panel',[$this,'gz_stock_2_panel']]],
			],
		  	'ajaxes' => [
		 		['prm'=>['get_stock_2_panel',[$this,'get_stock_2_panel']]],
		 		['prm'=>['get_db_status',[$this,'get_db_status']]],
		 		['prm'=>['get_db_control',[$this,'get_db_control']]],
			]
		];
		$this->wpdb = $GLOBALS['wpdb'];
		$this->table_name = $this->wpdb->prefix.'stock_price';
		parent::__construct($config); //ob_clean(); echo '<pre>'; print_r($config); die();
	}

	function get_db_status(){
		$sql = "SELECT COUNT(*) total_records FROM {$this->table_name}";
		$rs = $this->wpdb->get_results($sql,OBJECT); //die('<pre>'.print_r(compact('sql','rs'),true).'</pre>');

		$html = "";
		$html.= "<table class='gz_info'>";
		$html.= "<tr><td>Total records</td><td>{$rs[0]->total_records}</td></tr>";
		$html.= "</table>";
		//$html.= "<table class='gz_debug'>";
		//$html.= "<tr><td>sql</td><td>{$sql}</td></tr>";
		//$html.= "<tr><td>rs</td><td>".print_r($rs,true)."</td></tr>";
		//$html.= "</table>";
		$rs = (object)['html' => $html];
		echo json_encode($rs); die(0);
	}

	function gz_db_view(){
		$html = "";
		$html.= "<div class='display'><span class='label'>Table name</span><span class='data' data-type='text' data-index='table_name'>?</span></div>";
		$html.= "<div class='display'><span class='label'>Total records</span><span class='data' data-type='int' data-index='total_records'>?</span></div>";
		return $html;
	}

	function gz_db_control(){
		$html = "";
		$html.= "<span class='btn refresh cell gz_panel' data-default='debug=1' data-event='click' data-type='refresh' ><i class='fa-solid fa-arrows-rotate rotate'></i> Refresh</span>";
		return $html;
	}
	function get_db_control(){
		$rs = (object)['html' => $this->gz_db_control()];
		echo json_encode($rs); die(0);
	}

	function gz_db_status(){
		$html = "";
		$html.= "<div class='db_view'>{$this->gz_db_view()}</div>";
		$html.= "<div class='db_control db_menu menu row'>{$this->gz_db_control()}</div>";
		return $html;
	}
	
	function gz_stock_2_panel_html(){
		$html = "";
		$html.= "<div class='db_status sub_panel gz_panel' data-event='init' data-type='ajax' data-action='get_db_status'>{$this->gz_db_status()}</div>";
		$html.= "<div class='db_import sub_panel'>DB Import...</div>";
		$rs = (object)[
			'html'	=> $html,
		];
		echo json_encode($rs); die(0);
	}

	function gz_stock_2_panel($atts,$content=false){
		$html = "";
		$html.= "<div class='gz_stock_2_panel gz_panel' data-event='init' data-type='panel' data-action='get_stock_2_panel' data-default=''>";
		$html.= "Stock panel...";
		$html.= "</div>";
		return $html;
	}

}
