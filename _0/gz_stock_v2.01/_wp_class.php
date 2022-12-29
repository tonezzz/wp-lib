<?php //die(__FILE__);
/*
v1.01 - 20220505:Tony:.gz_val is now for update, use data-type to determind the data type.
v1.00 - 20220422:Tony:Implement automatic js update fields
v0.00 - 20220416:Tony:Prepare panel & import module
*/
class gz_stock extends gz_tpl{
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
				['prm'=>['gz_stock_panel',[$this,'gz_stock_panel']]],
			],
		  	'ajaxes' => [
		 		['prm'=>['get_stock_panel',[$this,'get_stock_panel']]],
		 		['prm'=>['get_db_status',[$this,'get_db_status']]],
		 		['prm'=>['get_db_control',[$this,'get_db_control']]],
		 		['prm'=>['get_db_import_tab',[$this,'get_db_import']]],
		 		['prm'=>['get_db_import_panel',[$this,'get_db_import_panel']]],
			]
		];
		$this->wpdb = $GLOBALS['wpdb'];
		$this->table_name = $this->wpdb->prefix.'stock_price';
		parent::__construct($config); //ob_clean(); echo '<pre>'; print_r($config); die();
	}

	function get_db_status(){
		$sql = "SELECT COUNT(*) total_records FROM {$this->table_name}";
		$rs = $this->wpdb->get_results($sql,OBJECT); //die('<pre>'.print_r(compact('sql','rs'),true).'</pre>');

		$data = [];
		$data['total_records'] = $rs[0]->total_records;
		$data['table_name'] = $this->table_name;
		$data['sql'] = $sql;
		$data['rs'] = '<pre>'.print_r($rs,true).'</pre>';
		$ret = (object)['data' => $data];
		echo json_encode($ret); die(0);
	}

	function gz_db_view(){
		$html = "";
		$html = "";
		$html.= "<table class='gz_info'>";
		$html.= "<tr><td>Total records</td>	<td data-id='total_records' class='td gz_panel' data-event='refresh' data-type='text' data-default=''></td></tr>";
		$html.= "<tr><td>Table name</td>	<td data-id='table_name' class='td gz_panel' data-event='refresh' data-type='text' data-default=''></td></tr>";
		$html.= "<tr><td>SQL</td>			<td data-id='sql' class='td gz_panel' data-event='refresh' data-type='text' data-default=''></td></tr>";
		$html.= "<tr><td>rs</td>			<td data-id='rs' class='td gz_panel' data-event='refresh' data-type='html' data-default=''></td></tr>";
		$html.= "</table>";
		return $html;
	}

	function gz_db_control(){
		$html = "";
		//$html.= "<span class='btn refresh cell gz_panel' data-default='xdebug=1' data-event='click' data-type='refresh' ><i class='fa-solid fa-arrows-rotate rotate'></i> Refresh</span>";
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
	
	function get_dataset_list(){
		$doc_root = $_SERVER["DOCUMENT_ROOT"];
		$att_src_path = '_stock/price_raw/';
		$att_select = '*/';
		$path = $doc_root.$att_src_path.$att_select; //die('<pre>'.print_r(compact('path','atts','att_year','att_src_path'),true));
		$files = glob($path); //die('<pre>'.print_r(compact('path','files','x'),true));
		return($files);
	}

	function gz_db_import_tabs(){
		$html_tabs = '';
		$html_pans = '';
		$datasets = $this->get_dataset_list();
		$html.= "<div class='db_import_tabs gz_panel' data-type='tabs'>";
		$url = site_url('/wp-admin/admin-ajax.php?action=render_file_panel&panel=1&year=');
		foreach($datasets as $dataset){
			$html_tabs.= "<li rel='{$dataset}'><a href='{$url}{$dataset}'>{$dataset}</a></li>";
			$html_pans.= ''; //"<div id='{$dataset}'>{$dataset},{$url}</div>";
		}
		$html.= "<ul>{$html_tabs}</ul>{$html_pans}";
		$html.= "</div>";
		$html.= '<pre>'.print_r($datasets,true).'</pre>';
		return $html;
	}

	function gz_db_import_control(){
		$html.= "<div class='db_import_control menu row'>";
			$html.= "<span class='refresh btn cell'>Refresh</span>";
		$html.= "</div>";
		return $html;
	}
	
	function gz_db_import_panel(){
		$html = "";
		$html.= $this->gz_db_import_tabs();
		$html.= $this->gz_db_import_control();
		//$html.= "<div class='import_tab xgz_panel' data-event='refresh' data-type='tabs' data-id='import_tabs'>Import tabs...</div>";
		return $html;
	}
	
	function get_db_import_panel(){
		$rs = (object)[
			'html'	=> $this->gz_db_import_panel(),
		];
		echo json_encode($rs); die(0);
	}
	
	function get_stock_panel(){
		$html = "";
		$html.= "<div class='db_status sub_panel gz_panel' data-event='init' data-type='data' data-action='get_db_status' data-default=''>{$this->gz_db_status()}</div>";
		$html.= "<div class='db_import sub_panel gz_panel' data-event='init' data-type='panel' data-action='get_db_import_panel' data-default=''>DB Import ...</div>";
		$rs = (object)[
			'html'	=> $html,
		];
		echo json_encode($rs); die(0);
	}

	function gz_stock_panel($atts,$content=false){
		$html = "";
		$html.= "<div class='gz_stock_panel gz_panel' data-events='init,ajax' data-type='panel' data-action='get_stock_panel' data-default='debug=1'>";
		$html.= "Stock panel...";
		$html.= "</div>";
		return $html;
	}
/*
gz_panel:
  gz_ajax: Load data (html,data)
  gz_view: View data from store
*/
}
