<?php //die(__FILE__); 
/*
 v2.01:20180417:Tony
	- Fix "global $post;" in WooCommerce shop page.
 v2.00:20170206:Tony
	- Extend gz_tpl for better DOM rendering command.
 v1.03:20170526:Tony
	- Remove col_2 column and expand col_1 to cover the space

 * Source: http://www.remicorson.com/woocommerce-list-products-by-attribute-with-multiple-values/
 * Plugin Name: WooCommerce - List Products by Multiple Categories, Attributes, Etc.
 * Description: [gz_products attrs='color(red,black):brand(adidas,asics)' cats='a,b,c' tags='a,b,c']
 * Version: 1.0
 */
/**
 * class gz_cmb2_fieldtype - Add custom field types to CMB2
 */
 class gz_cmb2_fieldtype extends gz_tpl{
	
	public function __construct(){
		$config = [
			'enqueue_admin'  => [//@import './fonts/css/fontello.css';
				['type'=>'style','load'=>true,'prm'=>[__CLASS__.'_admin','[REL_PATH]_wp_style.scss',[]]]
			]
			,'enqueue'  => [//@import './fonts/css/fontello.css';
				['type'=>'script' ,'load'=>true ,'prm'=>[__CLASS__,'[REL_PATH]_wp_script.js',['jquery-core']]],
				['type'=>'style','load'=>true,'prm'=>[__CLASS__,'[REL_PATH]_wp_style.scss',[]]]
				//,['type'=>'localize', 'prm'=>[__CLASS__,__CLASS__,['get_poi'=>admin_url('admin-ajax.php')]]]
			]
		];
		parent::__construct($config); //init_shortcodes
		add_action('cmb2_render_'.'gz_password' ,[$this,'render_'.'gz_password'], 10, 5 );
		add_action('cmb2_render_'.'gz_date' ,[$this,'render_'.'gz_date'], 10, 5 );
		add_action('cmb2_render_'.'gz_date_picker' ,[$this,'render_'.'gz_date_picker'], 10, 5 );
		add_filter('cmb2_sanitize_'.'gz_date_picker' ,[$this,'sanitize_'.'gz_date_picker'], 10, 2 );
		//add_action('cmb2_save_'.'gz_date_picker'.'_fields',[$this,'save_'.'gz_date_picker'], 10, 4);
		//add_filter('cmb2_sanitize_'.'gz_date_picker' ,[$this,'sanitize_'.'gz_date_picker'], 10, 4 );
	}

	function render_gz_password($field,$value,$object_id,$object_type,$field_type){
		//ob_clean(); echo '<pre>'; print_r(compact('field','value')); die();
		//ob_clean(); echo '<pre>'; print_r($field->args['id']); die();
		$html = '';
		//$field_id = $field->args['id'];
		//Not sure what are they doing, just empty prm gives me what I want
		$name = $field_type->_name();
		$id = $field_type->_id();
		//ob_clean(); echo '<pre>'; print_r(compact('field_id','name','id')); die();
		$html.= "<input type='password' name='{$name}' id='{$id}'/>";
		echo $html;
	}

	function render_gz_date($field,$value,$object_id,$object_type,$field_type){
		echo $field_type->input(['type'=>'text_medium','class'=>'gz_date']);
	}

	function sanitize_gz_date_picker($override_value, $value){//ob_clean(); echo '<pre>'; print_r(compact('override_value','value')); die();
		extract($value,EXTR_PREFIX_ALL,'prm');
		$date = new DateTime();
		$date->setDate($prm_y,$prm_m,$prm_d); //ob_clean(); echo '<pre>'; print_r(compact('prm_d','prm_m','prm_y','date')); die();
		//$ret = "{$prm_y} {$prm_m} {$prm_d}";
		$ret = $date->format("Y-M-d"); //ob_clean(); echo '<pre>'; print_r(compact('ret','date','value')); die();
		return $ret;
	}

	function render_gz_date_picker($field,$value,$object_id,$object_type,$field_type){
		//ob_clean(); echo '<pre>'; print_r(compact('value','object_id','field','object_type','field_type')); die();
		//Trying with multiple date format
		if(is_string($value)){//Prevent array from entering
			$date = DateTime::createFromFormat("Y-M-d",$value);
			if(empty($date)) $date = DateTime::createFromFormat("d-M-Y",$value);
		}
		if(empty($date)) $date = new DateTime('now');
		$d = $date->format('d');
		$m = $date->format('m');
		$y = $date->format('y');
		//ob_clean(); echo '<pre>'; print_r(compact('d','m','y','value','date','date1')); die();

		$value = compact('value','date','d','m','y'); //ob_clean(); echo '<pre>'; print_r($value); die();
		$html = '';
		$value = wp_parse_args($value,['d'=>'','m'=>'','y'=>'']);
		//
		$html.="<div><p><label for='".$field_type->_id('day')."'>Day</label></p>"
		.$field_type->select([
			//'class'	=> 'cmb_text_small',
			'name'  => $field_type->_name( '[d]' ),
			'id'    => $field_type->_id( '_d' ),
			'value' => $value['d'],
			'options'	=> $this->gz_date_picker_get_day_options($value),
			'desc'  => '',
		])."</div>";
		//
		$html.="<div><p><label for='".$field_type->_id('m')."'>Month</label></p>"
		.$field_type->select([
			//'class'	=> 'cmb_text_small',
			'name'  => $field_type->_name( '[m]' ),
			'id'    => $field_type->_id( '_m' ),
			'value' => $value['m'],
			'options'	=> $this->gz_date_picker_get_month_options($value),
			'desc'  => '',
		])."</div>";
		//
		$html.="<div><p><label for='".$field_type->_id('y')."'>Year</label></p>"
		.$field_type->select([
			//'class'	=> 'cmb_text_small',
			'name'  => $field_type->_name('[y]'),
			'id'    => $field_type->_id('_y'),
			'value' => $value['y'],
			'options'	=> $this->gz_date_picker_get_year_options($value),
			'desc'  => '',
		])."</div>";
		//
		$html.= "<br class='clear'/>".$field_type->_desc(true);
		echo $html; //return true;
	}
	/**
	 * 
	 */
	function gz_date_picker_get_day_options($val){//ob_clean(); echo '<pre>'; print_r($val); die();
		$html = '';
		$ndays = cal_days_in_month(CAL_GREGORIAN,$val['m'],$val['y']);
		for($i=1;$i<=$ndays;$i++){
			$selected = selected($i,$val['d'],false);
			$html.= "<option value='{$i}' {$selected}>{$i}</option>";
		}
		return $html;
	}
	/**
	 * 
	 */
	function gz_date_picker_get_month_options($val){
		$html = '';
		//$m_th = ['มกราคม','กุมภาพัน','มีนาคม','เมษายน','พฤษภาคม','มิถุนายน','กรกฏาคม','สิงหาคม','กันยายน','ตุลาคม','พฤศจิกายน','ธันวาคม'];
		//$m_en = ['January','February','March','April','May','June','July','August','September','October','November','December'];
		$m_th = ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];
		$m_en = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'June', 'July', 'Aug', 'Sept', 'Oct', 'Nov', 'Dec'];
		for($i=1;$i<=12;$i++){
			$selected = selected($i,$val['m'],false);
			$txt = $m_th[$i-1].' / '.$m_en[$i-1];
			$html.= "<option value='{$i}' {$selected}>{$txt}</option>";
		}
		return $html;
	}
	/**
	 * 
	 */
	function gz_date_picker_get_year_options($val){
		$html = '';
		$y1 = date('Y');
		$y2 = $y1-100;
		for($i=$y1;$i>$y2;$i--){
			$selected = selected($i,$val['y'],false);
			$txt = ($i+543).' / '.($i);
			$html.= "<option value='{$i}' {$selected}>{$txt}</option>";
		}
		return $html;
	}
}
 
 //global $gz_ad_panel; $gz_ad_panel = new gz_ad_panel();