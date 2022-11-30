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
			,'enqueue_admin'  => [//@import './fonts/css/fontello.css';
				['type'=>'script' ,'load'=>true ,'prm'=>[__CLASS__,'[REL_PATH]_wp_script.js',['jquery-core']]],
				['type'=>'style','load'=>true,'prm'=>[__CLASS__,'[REL_PATH]_wp_style.scss',[]]]
				//,['type'=>'localize', 'prm'=>[__CLASS__,__CLASS__,['get_poi'=>admin_url('admin-ajax.php')]]]
			]
		];
		parent::__construct($config); //init_shortcodes
		add_action('cmb2_render_'.'gz_password' ,[$this,'render_'.'gz_password'], 10, 5 );
		add_action('cmb2_render_'.'gz_date' ,[$this,'render_'.'gz_date'], 10, 5 );
		add_action('cmb2_render_'.'gz_datetime' ,[$this,'render_'.'gz_datetime'], 10, 5 );
		add_filter('cmb2_sanitize_'.'gz_datetime' ,[$this,'sanitize_'.'gz_datetime'], 10, 2 );
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

	function render_gz_datetime($field,$value,$object_id,$object_type,$field_type){
		//ob_clean(); $test=date("Y-M-d h:m",$value);echo '<pre>'; print_r(compact('value','test')); die();
		//echo $field_type->input(['type'=>'text_medium','class'=>'gz_datetime']);
		//echo $field_type->input(['type'=>'text_medium','class'=>'gz_datetime','value'=>date('d-M-Y h:m'),$value]);
		echo $field_type->input(['type'=>'text_medium','class'=>'gz_datetime','value'=>date('d-M-Y H:i',strtotime($value))]);
		//echo "[{$value}]";
	}

	function sanitize_gz_datetime($ov,$value){
		//$date = strtotime($value);
		//ob_clean(); $test = date("Y-m-d H:i",strtotime('24-Aug-2018')); echo '<pre>'; print_r(compact('ov','value','date','test')); die();
		$date = date("Y-m-d H:i",strtotime($value));
		return $date;
	}

}
 
 //global $gz_ad_panel; $gz_ad_panel = new gz_ad_panel();