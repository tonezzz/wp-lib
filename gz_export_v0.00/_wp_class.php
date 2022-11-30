<?php
class gz_export extends gz_tpl{
	private $field_pre='_mm_';
	public function __construct(){
		$config = [
			'enqueue'  => [//@import './fonts/css/fontello.css';
				['type'=>'localize', 'prm'=>[__CLASS__,__CLASS__,['export'=>admin_url('admin-ajax.php')]]]
				//['type'=>'style' ,'load'=>true ,'prm'=>[__CLASS__,'[REL_PATH]_wp_style.scss'],[]]
				//['type'=>'script' ,'load'=>true ,'prm'=>[__CLASS__,'[REL_PATH]_wp_script.js',['jquery-core']]]
			]
		];
		parent::__construct($config); //init_shortcodes
		add_shortcode("gz_export",[$this,'render_'.'export_url']);
		add_action('wp_ajax_nopriv_'.'export',[$this,'render_'.'export']);
		add_action('wp_ajax_'.'export',[$this,'render_'.'export']);
    }

    function render_export_url($atts,$content,$shortcode){
		//ob_clean(); echo '<pre>'; print_r(compact('atts','content','shortcode')); die();
		$pre = $this->field_pre;
		$atts = $this->shortcode_atts([
			'post_type'	=> 'event_regis'
			,'event_id'	=> 15
		],$atts);
        extract($atts,EXTR_PREFIX_ALL,'att');
		$html = '';
        $prm = array_merge($atts,[
			'action'	=> 'export'
		]);
        $url = add_query_arg($prm,site_url().'/wp-admin/admin-ajax.php'); //ob_clean(); echo '<pre>'; print_r($img_url); print_r(site_url()); die();
        $html.= "<a href='{$url}'>";
        $html.= empty($content)?"Download":$content;
		$html.= "</a>";
		return $html;
	}
	
	function render_export()
	{
		//require_once dirname(__FILE__).'/excel_reader2.php';
		$pre = $this->field_pre;
		$atts = $this->shortcode_atts([
			'post_type'	=> 'event_regis'
			,'event_id'	=> 15
		],$_GET); //ob_clean(); echo '<pre>'; print_r($atts); die();
		extract($atts,EXTR_PREFIX_ALL,'att');
		$arg = [
			'post_type'			=> 'event_regis'
			,'posts_per_page'	=> -1
			,'orderby'			=> 'regis_id'
			,'order'			=> 'asc'
			,'meta_query'		=> [
				'relation'		=> 'and'
				//,['key'=>$pre.'event_id','value'=>$att_event_id,'compare'=>'=']
				,['key'=>$pre.'event_id','value'=>$att_event_id,'compare'=>'=']
			]
		]; //ob_clean(); echo '<pre>'; print_r($arg); die();
		$items = get_posts($arg); //ob_clean(); echo '<pre>'; print_r($items); die();
		$data = [];
		foreach($items as $id=>$item){
			$item_id = $item->ID;
			$data[$id]['regis_id'] = $item_id;
			$data[$id]['regis_code'] = get_post_meta($item_id,$pre.'regis_code',true);
			$data[$id]['pay_status'] = get_post_meta($item_id,$pre.'pay_status',true);
			$data[$id]['name'] = get_post_meta($item_id,$pre.'first_name',true).' '.get_post_meta($item_id,$pre.'last_name',true);
			$data[$id]['sex'] = get_post_meta($item_id,$pre.'sex',true);
			$data[$id]['age_group'] = get_post_meta($item_id,$pre.'age_group',true);
			$data[$id]['distance'] = get_post_meta($item_id,$pre.'distance',true);
			$data[$id]['date_of_birth'] = get_post_meta($item_id,$pre.'date_of_birth',true);
			$data[$id]['jersey_color'] = get_post_meta($item_id,$pre.'jersey_color',true);
			$data[$id]['jersey_size'] = get_post_meta($item_id,$pre.'date_of_birth',true);
			$data[$id]['delivery'] = get_post_meta($item_id,$pre.'delivery',true);
			$metas = get_post_meta($item_id);
			foreach($metas as $key=>$val) if(0===strpos($key,$pre)){
				$k1 = str_replace($pre,'',$key);
				$data[$id][$k1] = $val[0];
			}
		} //ob_clean(); echo '<pre>'; print_r($data); die();
		$this->render_file(['data'=>$data]);
		die();
	}

	function render_file($prm){
		extract($prm,EXTR_PREFIX_ALL,'prm');
		require_once __DIR__ .'/../phpspreadsheet_20180818/vendor/autoload.php';
		$spreadsheet = new PhpOffice\PhpSpreadsheet\Spreadsheet();
		$spreadsheet->getProperties()
		->setCreator("Rundee.net")
		->setTitle("Rundee export");
		$sheet = $spreadsheet->getActiveSheet();
		$row = 1;
		//for the header row
		if(is_array($prm_data)){
			$col = 'A';
			foreach(array_keys($prm_data[0]) as $val) $sheet->setCellValue($col++.$row,$val);
		} $row++;
		foreach($prm_data as $row_vals){
			$col = 'A';
			foreach($row_vals as $val){
				$sheet->setCellValue($col++.$row,$val);
			} $row++;
		}
		//$sheet->getDefaultColumnDimension()->setAutoSize(true);
		foreach($sheet->getColumnDimensions() as $colDim) $colDim->setAutoSize(true);
		$writer = new PhpOffice\PhpSpreadsheet\Writer\Xls($spreadsheet);
		header('Content-type: application/vnd.ms-excel');
		header('Content-Disposition: attachment; filename="rundee-export.xls"');
		$writer->save('php://output');
	}

	function render_file_0($prm){
		extract($prm,EXTR_PREFIX_ALL,'prm');
		require_once __DIR__ .'/../excel_writer_v0.9.4/Writer/BIFFwriter.php';
		require_once __DIR__ .'/../excel_writer_v0.9.4/Writer/Format.php';
		require_once __DIR__ .'/../excel_writer_v0.9.4/Writer/Workbook.php';
		require_once __DIR__ .'/../excel_writer_v0.9.4/Writer.php';
		$workbook = new Spredsheet_Excel_Writer();
		$workbook->send('rundee-export.xls');
		$worksheet =& $workbook->addWorksheet('Registrations');
		$worksheet->write(0,0,'Test');
		$workbook->close();
	}
}