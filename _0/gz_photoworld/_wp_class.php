<?php //die(__FILE__); 
/* poi
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
 class gz_photoworld extends gz_tpl{
	//protected $cmb2;
	//protected $post, $metas;
	static $icons;
	
	public function __construct(){
		$config = [
			'enqueue'  => [//@import './fonts/css/fontello.css';
				['type'=>'script' ,'load'=>true ,'prm'=>[__CLASS__,'[REL_PATH]_wp_script.js',['jquery-core']]]
				#,['type'=>'localize', 'prm'=>[__CLASS__,__CLASS__,['get_markers'=>admin_url('admin-ajax.php')]]]
				//,['type'=>'style' ,'prm'=>[__CLASS__,'[REL_PATH]_wp_style.scss'],['jquery_slick']]
			]
			,'shortcodes' => [
				['prm'=>['gz_photoworld',[$this,'render_photoworld']]]
			]
		];
		parent::__construct($config); //init_shortcodes
	}

	function render_photoworld(){
		$atts = $this->shortcode_atts([
			'cat'	=> 'surfskate'
		],$atts,'gz_poi'); //ob_clean(); echo '<pre>'; var_dump($atts); die();
		extract($atts,EXTR_PREFIX_ALL,'att');
		$html = "<div id='st_poi_map' data-poi_cat='{$att_cat}'></div>";
		//$html.= "<div id='gz_poi_content_wrap'>{$this->render_place_html(8622)}</div>";
		return $html;
	}

}
 
 //global $gz_ad_panel; $gz_ad_panel = new gz_ad_panel();