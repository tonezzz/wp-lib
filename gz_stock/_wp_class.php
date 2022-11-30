<?php //die(__FILE__);
/*
v0.00 - 20220416:Tony:Prepare panel & import module
*/
class gz_stock extends gz_tpl{
	public function __construct(){//die(__FILE__.__FUNCTION__);
		$config = [
			'enqueue'  => [
				['type'=>'style' ,'prm'=>['jquery-ui','//code.jquery.com/ui/1.13.1/themes/base/jquery-ui.css',]],
				['type'=>'style' ,'load'=>true ,'prm'=>[__CLASS__,'[REL_PATH]wp_style.scss',['jquery-ui']]],
				['type'=>'script' ,'load'=>true ,'prm'=>[__CLASS__,'[REL_PATH]_wp_script.js',['jquery-core','jquery-ui-core','jquery-ui-tabs']]],
				['type'=>'localize', 'prm'=>[__CLASS__,__CLASS__,['ajax_url'=>admin_url('admin-ajax.php')]]],
			]
			,'shortcodes' => [
				['prm'=>['gz_stock_panel',[$this,'render_panel']]],
			],
		  	'ajaxes' => [
		 		['prm'=>['create_db',[$this,'create_db']]],
		 		['prm'=>['clear_db',[$this,'clear_db']]],
		 		['prm'=>['render_file_panel',[$this,'render_file_panel']]],
		 		['prm'=>['import_date',[$this,'import_date']]],
		 		['prm'=>['clear_date',[$this,'clear_date']]],
		 		['prm'=>['get_db_status',[$this,'get_db_status']]],
		 		['prm'=>['get_date_status',[$this,'get_date_status']]],
		 	]
		];
		$this->wpdb = $GLOBALS['wpdb'];
		$this->table_name = $this->wpdb->prefix.'stock_price';
		parent::__construct($config); //ob_clean(); echo '<pre>'; print_r($config); die();
	}
	
	function get_date_status($atts=[]){
		$atts = shortcode_atts([
			'output'	=> 'html',
			'date'		=> isset($_GET['date'])?$_GET['date']:false,
		],$atts);
		extract($atts, EXTR_PREFIX_ALL,'att'); //die('<pre>'.print_r(compact('sql'),true).'</pre>');
		
		if($att_date==false) $html = 'n/a';
		else {
			$sql = "SELECT COUNT(*) total_records FROM {$this->table_name} where date='{$att_date}'";
			$rs = $this->wpdb->get_results($sql);
			if($att_output=='json'){
				$html = json_encode(['success'=>1]);
			} else {
				$html = $rs[0]->total_records;
			}
		}
		echo $html; die(0);
	}
	
	function get_db_status($atts=[]){
		$atts = shortcode_atts([
			'output'	=> 'html'
		],$atts);
		extract($atts, EXTR_PREFIX_ALL,'att'); //print_r(compact('atts','att_test','atttest'),true); die();
		$sql = "SELECT COUNT(*) total_records FROM {$this->table_name}";
		$rs = $this->wpdb->get_results($sql);
		if($att_output=='json'){
		} else {
			$html = '';
			$html.= "<span class='row'><span class='label cell'>Total records:</span><span class='cell'>{$rs[0]->total_records}</span></span>";
			$html.= "<span class='row'><span class='label cell'>Raw:</span><span class='cell'><pre>".print_r($rs,true)."</pre></span></span>";
		}
		echo $html; die(0);
	}
	
	//https://medium.com/enekochan/using-dbdelta-with-wordpress-to-create-and-alter-tables-73883f1db57
	function create_db(){
		$sql = "CREATE TABLE {$this->table_name} (
			id int(10) NOT NULL AUTO_INCREMENT,
			ticker varchar(10) CHARACTER SET ascii NOT NULL,
			date date NOT NULL,
			open float NOT NULL,
			high float NOT NULL,
			low float NOT NULL,
			close float NOT NULL,
			vol int NOT NULL,
			PRIMARY KEY  (id),
			KEY ticker (ticker),
			KEY date (date),
			KEY ticker_date (ticker,date)
		) CHARSET=ascii;
		"; echo '<pre>'; print_r($sql);
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		$rs = dbDelta($sql); print_r($rs);
		die(0);
	}
	
	function clear_db(){
		$html = '';
		$sql = "DELETE FROM {$this->table_name}";
		$rs = $this->wpdb->get_results($sql);
		$html.= json_encode(['success'=>1 ,'msg'=>print_r($rs,true)]);
	}
	
	function clear_date(){
		$atts = shortcode_atts([
			'output'	=> 'html',
			'date'		=> isset($_GET['date'])?$_GET['date']:false,
		],$atts);
		extract($atts, EXTR_PREFIX_ALL,'att'); //die('<pre>'.print_r(compact('sql'),true).'</pre>');
		
		if($att_date==false) $html = 'n/a';
		else {
			$sql = "DELETE FROM {$this->table_name} where date='{$att_date}'";
			$rs = $this->wpdb->get_results($sql);
			if($att_output=='json'){
				$html = json_encode(['success'=>1]);
			} else {
				$html = $rs[0]->total_records;
			}
		}
		echo $html; die(0);
	}
	
	#https://stackoverflow.com/questions/55818568/load-data-local-infile-forbidden-after-php-mariadb-update
	function import_date($atts=[],$content=false){
		$atts = shortcode_atts([
			'root'		=> isset($_GET['root'])		?$_GET['root']		:'/var/www/dev00.surf-thailand.com/htdocs',
			'src'		=> isset($_GET['src'])		?$_GET['src']		:false,
			'year'		=> isset($_GET['year'])		?$_GET['year']		:false,
			'select'	=> isset($_GET['select'])	?$_GET['select']	:'*.csv',
			'echo'		=> isset($_GET['echo'])		?$_GET['echo']		:true,
			'panel'		=> isset($_GET['panel'])	?$_GET['panel']		:false,
		],$atts);
		extract($atts, EXTR_PREFIX_ALL,'att'); //die('<pre>'.print_r(compact('atts','att_year','att_src_path'),true));
		$html = '';
		$root_src = '/var/www/dev00.surf-thailand.com/htdocs';
		if($src){//$html.=print_r(compact('src_file'),true);
			$sql = "LOAD DATA LOCAL INFILE '{$att_root}/{$att_src}'
				INTO TABLE {$this->table_name}
				FIELDS TERMINATED BY ',' 
				ENCLOSED BY '\"'
				LINES TERMINATED BY '\\n'
				IGNORE 1 ROWS
				(ticker,date,open,high,low,close,vol)
				SET ID = NULL
			";
			$rs = $this->wpdb->get_results($sql); //$html.=print_r($rs,true)."\n";
			$html.= json_encode(['success'=>1 ,'msg'=>print_r(compact('rs'),true)]);
		} else $html.= json_encode(['success'=>0]);
		echo $html; die(0);
	}
	
	function render_file_panel($atts=[],$content=false){
		$atts = shortcode_atts([
			'src_path'	=> isset($_GET['src_path'])?$_GET['src_path']	:'_stock/price_raw',
			'year'		=> isset($_GET['year'])?	$_GET['year']		:false,
			'select'	=> isset($_GET['select'])?	$_GET['select']		:'*.csv',
			'echo'		=> isset($_GET['echo'])?	$_GET['echo']		:true,
			'panel'		=> isset($_GET['panel'])?	$_GET['panel']		:false,
		],$atts);
		extract($atts, EXTR_PREFIX_ALL,'att'); //die('<pre>'.print_r(compact('atts','att_year','att_src_path'),true));
		if($att_year===false) return;
		$html = '';
		if($att_panel){
			$html.= "<div class='file_panel'>";
			$html.= "<span class='info status row'>";
				$html.= "<span class='label'>Total records:</span><span class='value' data-from='status_year' data-key='total_records'>?</span>";
			$html.= "</span>";
			$html.= "<div class='file_menu menu row'>";
				$html.= "<span class='btn refresh cell'><i class='fa-solid fa-arrows-rotate rotate'></i> Refresh</span>";
				$html.= "<span class='btn import cell'>Import</span>";
			$html.= "</div>";
			$html.= "<ul class='file_list table'>";
		}
		$doc_root = $_SERVER["DOCUMENT_ROOT"];
		$path = $doc_root.'/'.$att_src_path.'/'.$att_year.'/'.$att_select; //die('<pre>'.print_r(compact('path','atts','att_year','att_src_path'),true));
		$files = glob($path); //die('<pre>'.print_r(compact('path','files','x'),true));
		foreach($files as $file){
			$date = substr($file,-14,10);
			$html.="<li data-path='{$file}' data-date='{$date}' class='row'>";
			$html.="<span class='cell'>".basename($file)."</span>";
			$html.="<span class='btn status cell' data-id='{$date}'>(?)</span>";
			$html.="<span class='btn clear cell'>Clear</span>";
			$html.="<span class='btn import_all cell'>Import all</span>";
			$html.='</li>';
		}
		if($att_panel){
			$html.= "</ul></div>";
		}
		if($att_echo){echo $html; die(0);} else return $html;
	}

	function render_db_panel($atts=[],$content=false){
		$atts = shortcode_atts([
			'db_table'	=> 'gz_stock'
		],$atts,'gz_db_panel');
		extract($atts, EXTR_PREFIX_ALL,'att'); //print_r(compact('atts','att_test','atttest'),true); die();
		$html = '';
		$html.= "<div class='db_panel'>";
		$html.= "<div class='menu_db menu row'>";
			$html.= "<span class='btn status cell'>Status</span>";
			$html.= "<span class='btn create_db cell'>CreateDB</span>";
			$html.= "<span class='btn delete_db cell'>DeleteDB</span>";
			$html.= "<span class='btn clear_db cell'>ClearDB</span>";
		$html.= "</div>";
		$html.= "<div class='db_status table'></div>";
		$html.= "</div>";
		return $html;
	}

	function render_import_tabs($atts=[],$echo=false){
		$atts = shortcode_atts([
			'select'	=> '*.csv'
			,'src_path'	=> '_stock/price_raw'
			,'years'	=> '2021'
		],$atts,'gz_stock_panel');
		extract($atts, EXTR_PREFIX_ALL,'att'); //print_r(compact('atts','att_test','atttest'),true); die();
		$html_tabs = '';
		$html_pans = '';
		$years = explode(',',$att_years);
		$url = site_url('/wp-admin/admin-ajax.php?action=render_file_panel&panel=1&year=');
		foreach($years as $year){
			$html_tabs.= "<li><a href='{$url}{$year}#{$year}'>{$year}</a></li>";
			$html_pans.= ''; //"<div id='{$year}'>{$year},{$url}</div>";
		}
		$html = '';
		//$html.= $att_years;
		$html.= "<div class='gz_stock_tabs'><ul>{$html_tabs}</ul>{$html_pans}</div>";
		if($echo){echo $html; die(0);} else return $html;
	}

	function render_panel($atts,$content=false){
		$atts = shortcode_atts([
			'src_path'	=> isset($_GET['src_path'])?$_GET['src_path']	:'_stock/price_raw',
			'select'	=> isset($_GET['select'])?	$_GET['select']		:'*.csv',
			'years'	=> isset($_GET['years'])?	$_GET['years']		:'2018,2019,2020,2021',
		],$atts,'gz_stock_panel');
		extract($atts, EXTR_PREFIX_ALL,'att'); //print_r(compact('atts','att_test','atttest'),true); die();
		$html ='';
		$html.="<div class='gz_stock_panel'>";
		$html.=$this->render_db_panel([]);
		$html.=$this->render_import_tabs(['years'=>$att_years]);
		//$html.=$this->render_file_panel(['select'=> $att_select ,'src_path'=>$att_src_path ,'years'=>$att_years,'echo'=>true ,'panel'=>true]);
		$html.= "</div>";
		return $html;
	}

}
