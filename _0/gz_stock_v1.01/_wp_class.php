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
				['type'=>'script' ,'load'=>true ,'prm'=>[__CLASS__,'[REL_PATH]_wp_script.js',['jquery-core','jquery-ui-core','jquery-ui-tabs']]],
				['type'=>'localize', 'prm'=>[__CLASS__,__CLASS__,['ajax_url'=>admin_url('admin-ajax.php')]]],
			]
			,'shortcodes' => [
				['prm'=>['gz_stock_panel',[$this,'render_panel']]],
			],
		  	'ajaxes' => [
		 		['prm'=>['get_db_status',[$this,'get_db_status']]],
				['prm'=>['get_file_list',[$this,'get_file_list']]],
		 		['prm'=>['get_date_status',[$this,'get_date_status']]], //get_date_status
		 		['prm'=>['import_date',[$this,'import_date']]],
		 		['prm'=>['clear_date',[$this,'clear_date']]],
				
		 		['prm'=>['create_db',[$this,'create_db']]],
		 		['prm'=>['clear_db',[$this,'clear_db']]],
		 		['prm'=>['render_file_panel',[$this,'render_file_panel']]],
		 		['prm'=>['clear_date',[$this,'clear_date']]],
		 	]
		];
		$this->wpdb = $GLOBALS['wpdb'];
		$this->table_name = $this->wpdb->prefix.'stock_price';
		parent::__construct($config); //ob_clean(); echo '<pre>'; print_r($config); die();
	}
	
	function get_date_status($atts=[],$content=false){
		$atts = shortcode_atts([
			'prms'		=> isset($_GET['prms'])		?$_GET['prms']		:false,
			'output'	=> isset($_GET['output'])	?$_GET['output']	:'json',
			'echo'		=> isset($_GET['echo'])		?$_GET['echo']		:true,
			'year'		=> isset($_GET['year'])		?$_GET['year']		:false,
		],$atts);
		if(is_array($atts['prms'])) $atts = array_merge($atts,unserialize(urldecode($atts['prms'])));
		extract($atts, EXTR_PREFIX_ALL,'att'); //die('<pre>'.print_r(compact('atts'),true).'</pre>');
		if($att_year==false) $html = 'n/a';
		else {
			//Generate day stats
			$sql = "SELECT date,COUNT(*) as count FROM {$this->table_name} where YEAR(date)={$att_year} GROUP BY date"; //die($sql);
			$date_rs = $this->wpdb->get_results($sql,ARRAY_N);
			//$date_data = array_combine(array_column($date_rs,0),array_column($date_rs,1)); 
			$date_data = [];
			foreach($date_rs as $item){
				$date_data[$item[0]] 		= $item[1];
				$date_data[$item[0].'_t'] 	= 'd';
			} //die('<pre>'.print_r(compact('sql','date_rs','date_data'),true));
			//
			$info = [];
			$info['success'] = 1;
			$info['data'] = $date_data;
		}
		if($att_output=='json'){
			$html = json_encode($info);
		} else {
			$html = '<pre>'.print_r($info,true).'</pre>';
		}

		if($att_echo) {echo $html; die(0);} else return $html;
	}
	function get_db_status($atts=[],$content=false){
		$atts = shortcode_atts([
			'output'	=> isset($_GET['output'])?$_GET['output']:'html',
			'echo'		=> true,
			'year'		=> '2021',//false,
		],$atts);
		extract($atts, EXTR_PREFIX_ALL,'att'); //die('<pre>'.print_r(compact('sql'),true).'</pre>');
		
		$sql = "SELECT COUNT(*) total_records FROM {$this->table_name}";
		if($year) $sql.= " where YEAR(date)='{$att_year}'";
		//
		$info = [];
		$info['sql']	= $sql;
		//
		$rs = $this->wpdb->get_results($sql,ARRAY_A); //die('<pre>'.print_r(compact('sql','rs'),true).'</pre>');
		$data['table_name']	= $this->table_name;
		$data['total_records'] = $rs[0]['total_records'];
		$data['total_records_t'] = 'd';
		$data['raw'] = '<pre>'.print_r($rs,true).'</pre>';
		//
		$info['data'] = $data;
		$info['success'] = 1;
		if($att_output=='json'){
			$html = json_encode($info);
		} else {
			$html = '<pre>'.print_r($info,true).'</pre>';
			$html = '';
			$html.= "<span class='row'><span class='label cell'>Total records:</span><span class='cell'>{$rs[0]->total_records}</span></span>";
			$html.= "<span class='row'><span class='label cell'>Raw:</span><span class='cell'><pre>".print_r($rs,true)."</pre></span></span>";
		}

		if($att_echo) {echo $html; die(0);} else return $html;
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
	
	function clear_date($atts=[],$content=false){
		$atts = shortcode_atts([
			'date'		=> isset($_GET['date'])		?$_GET['date']		:false,
			'echo'		=> isset($_GET['echo'])		?$_GET['echo']		:true,
		],$atts);
		extract($atts, EXTR_PREFIX_ALL,'att'); //die('<pre>'.print_r(compact('atts','att_year','att_src_path'),true));
		
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
			'path'		=> isset($_GET['path'])		?$_GET['path']		:'_stock/price_raw',
			'year'		=> isset($_GET['year'])		?$_GET['year']		:false,
			'file'		=> isset($_GET['file'])		?$_GET['file']		:false,
			'echo'		=> isset($_GET['echo'])		?$_GET['echo']		:true,
		],$atts);
		extract($atts, EXTR_PREFIX_ALL,'att'); //die('<pre>'.print_r(compact('atts','att_year','att_src_path'),true));
		$html = '';
		//$root_src = '/var/www/dev00.surf-thailand.com/htdocs';
		//if($src){//$html.=print_r(compact('src_file'),true);
			$sql = "LOAD DATA LOCAL INFILE '{$att_root}/{$att_path}/{$att_year}/{$att_file}'
				INTO TABLE {$this->table_name}
				FIELDS TERMINATED BY ',' 
				ENCLOSED BY '\"'
				LINES TERMINATED BY '\\n'
				IGNORE 1 ROWS
				(ticker,date,open,high,low,close,vol)
				SET ID = NULL
			"; //die($sql);
			$rs = $this->wpdb->get_results($sql); //die('<pre>'.print_r(compact('sql','atts','rs'),true));
			$html.= json_encode(['success'=>1 ,'msg'=>print_r(compact('rs'),true)]);
		//} else $html.= json_encode(['success'=>0]);
		echo $html; die(0);
	}
	
	function get_file_list($atts=[],$content=false){
		$atts = shortcode_atts([
			'src_path'	=> isset($_GET['src_path'])?$_GET['src_path']	:'_stock/price_raw',
			'year'		=> isset($_GET['year'])?	$_GET['year']		:false,
			'prms'		=> isset($_GET['prms'])?	$_GET['prms']		:false,
			'select'	=> isset($_GET['select'])?	$_GET['select']		:'*.csv',
			'output'	=> isset($_GET['output'])?	$_GET['output']		:'json',
			'echo'		=> isset($_GET['echo'])?	$_GET['echo']		:true,
		],$atts); //die('<pre>'.print_r(compact('atts'),true));
		if($atts['prms']) $atts = array_merge($atts,unserialize(urldecode($atts['prms'])));
		//if($atts['prms']) $atts['prms1'] = (urldecode($atts['prms']));
		extract($atts, EXTR_PREFIX_ALL,'att');
		//Generate file list
		$doc_root = $_SERVER["DOCUMENT_ROOT"];
		$path = $doc_root.'/'.$att_src_path.'/'.$att_year.'/'.$att_select; //die('<pre>'.print_r(compact('path','atts','att_year','att_src_path'),true));
		$files = glob($path); //die('<pre>'.print_r(compact('path','files','x'),true));
		$data = ''; foreach($files as $file){$data.="<li>".basename($file)."</li>";}
		//Generate day stats
		//$sql = "SELECT date,COUNT(*) as count FROM {$this->table_name} where YEAR(date)={$att_year} GROUP BY date";
		//$date_rs = $this->wpdb->get_results($sql,ARRAY_N);
		//$date_data = array_combine(array_column($date_rs,0),array_column($date_rs,1)); //die('<pre>'.print_r(compact('date_data'),true));
		//Generate return date
		$info = []; //$info['$_GET'] = $_GET; //$info['atts'] = $atts;
		$info['success'] = 1;
		//$info['data'] = $date_data;
		$info['data']['file_list'] = $data;
		$info['data']['file_list_t'] = 's'; //String
		if($att_output=='json'){
			$html = json_encode($info);
		} else {
			$html = '<pre>'.print_r($info,true).'</pre>';
		}

		if($att_echo) {echo $html; die(0);} else return $html;
	}
	
	function render_file_panel($atts=[],$content=false){
		$atts = shortcode_atts([
			'src_path'	=> isset($_GET['src_path'])?$_GET['src_path']	:'_stock/price_raw',
			'year'		=> isset($_GET['year'])?	$_GET['year']		:false,
			'select'	=> isset($_GET['select'])?	$_GET['select']		:'*.csv',
			'panel'		=> isset($_GET['panel'])?	$_GET['panel']		:false,
			'echo'		=> isset($_GET['echo'])?	$_GET['echo']		:true,
		],$atts);
		extract($atts, EXTR_PREFIX_ALL,'att'); //die('<pre>'.print_r(compact('atts','att_year','att_src_path'),true));
		if($att_year===false) return;
		$html = '';
		if($att_panel){
			$prms = ['year'=>$att_year]; $prm_st = urlencode(serialize($prms));
			$html.= "<div class='file_panel gz_ajax' data-action='get_file_list' data-prms='{$prm_st}'>";
				$html.= "<span class='info status row'>";
					$html.= "<span class='label'>Total records:</span><span class='value' data-from='status_year' data-key='total_records'>?</span>";
				$html.= "</span>";
				$html.= "<div class='file_menu menu row'>";
					$html.= "<span class='btn refresh cell'><i class='fa-solid fa-arrows-rotate rotate'></i> Refresh</span>";
					$html.= "<span class='btn clear_all cell'>Clear all</span>";
					$html.= "<span class='btn import_all cell'>Import all</span>";
				$html.= "</div>";
			$prm = ['year'=>$att_year]; $prm_st = serialize($prm);
			$html.= "<ul class='file_list table gz_val' data-type='ul' data-id='file_list' data-prm='{$prm_st}'>";
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
		$html.= "<div class='db_panel gz_ajax' data-action='get_db_status'>";
			$html.= "<label for='total_records'>Total records:</label><div id='total_records' class='gz_val' data-type='d' data-id='total_records'>?</div>";
		$html.= "<div class='menu_db menu row'>";
			$html.= "<span class='btn refresh cell gz_act' 	 data-event='click' data-type='trigger' data-trigger='refresh'>Refresh</span>";
			$html.= "<span class='btn create_db cell gz_act' data-event='click' data-type='ajax'    data-action='create_db'>CreateDB</span>";
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
			$html_tabs.= "<li rel='{$year}'><a href='{$url}{$year}'>{$year}</a></li>";
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
