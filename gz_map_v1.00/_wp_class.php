<?php //die(__FILE__);
/*
* Note:
* 	Check https://github.com/edent/QR-Generator-PHP
*	- Include gz_location to show poi.
* 	- [gz_map] short_code
*		- style=''
*		- poi='location,get_poi,all
*		- icon=''
* v0.02 - 20181201:Tony
*	- 
* v0.00 - 20171026:Tony
*/
//		wp_enqueue_script('googlemapsapi3', 'https://maps.google.com/maps/api/js?libraries=places,visualization&key=AIzaSyBHECdeG3VyMDPGrAKYPRUWg972BQzIfUo', false, '3', false); ;

class gz_map extends gz_tpl{
	static $icon = [

	];
	public function __construct(){//die(__FILE__.__FUNCTION__);
		$config = [
			'enqueue'  => [
				['type'=>'script' ,'load'=>false ,'prm'=>['googlemapsapi3','https://maps.google.com/maps/api/js?libraries=places,visualization&key=AIzaSyBHECdeG3VyMDPGrAKYPRUWg972BQzIfUo']]
				,['type'=>'script' ,'load'=>false ,'prm'=>['gz_map_jquery','[REL_PATH]gz_map.jquery.js',['googlemapsapi3']]]
				,['type'=>'script' ,'load'=>true ,'prm'=>[__CLASS__,'[REL_PATH]_wp_script.js',['gz_map_jquery']]]
				,['type'=>'style' ,'load'=>true ,'prm'=>[__CLASS__,'[REL_PATH]wp_style.scss',[]]]
				//['type'=>'localize', 'prm'=>[__CLASS__,__CLASS__,['render_qrcode'=>admin_url('admin-ajax.php')]]]
			]
		]; //ob_clean(); echo '<pre>'; print_r($config); die();
		parent::__construct($config); //ob_clean(); echo '<pre>'; print_r($config); die();
		add_shortcode('gz_map',__CLASS__.'::gz_map');
		//add_shortcode('gz_qrcode',[$this,'gz_qrcode']);
		//add_action('wp_ajax_nopriv_'.'render_qrcode',array($this,'render_qrcode'));
		//add_action('wp_ajax_'.'render_qrcode',array($this,'render_qrcode'));
		//add_action('wp_ajax_nopriv_'.'render_qrcode','gz_qrcode::render_qrcode');
		//add_action('wp_ajax_'.'render_qrcode','gz_qrcode::render_qrcode');
	}

	//13.8388272,100.7163296
	static function gz_map($atts=[]){
		$prm = self::shortcode_atts([
			'id'		=> false
			,'style'	=> 'width:100%;height:100%;'
			,'init' 	=> false //"{center:{address:'bangkok', zoom:16}}" //Works
			//,'init' 	=> "{center:[13.8388272,100.7163296], zoom:16}" //Works (P'Nong's office)
			,'icon'		=> false
			,'markers' 	=> false
			,'map_type'	=> 'normal'
			,'poi'		=> false
			,'dom_init'	=> false //JS callback after init dom
			,'map_init'	=> false //JS callback after init map
			,'poi_init'	=> false //JS callback after init each poi
			,'init_script'	=> false //Callback after init map (deprecated)
			,'legend_url'	=> ''
			,'_debug'	=> false
		],$atts,'gz_map'); //if($prm['_debug']){ob_clean(); echo "<pre>"; print_r($prm); die();}
		//ob_clean(); echo "<pre>"; print_r($prm); die();
		extract($prm,EXTR_PREFIX_ALL,'prm');
		$attr = [];
		if($prm_id)	$attr['id']	= $prm_id;
		if($prm_icon) $attr['data-icon'] = htmlspecialchars(htmlspecialchars_decode($prm_icon),ENT_QUOTES);
		//if($prm_style) $attr['style'] = htmlspecialchars(htmlspecialchars_decode($prm_style),ENT_QUOTES);
		//$prm_init = "{center:[13.8388272,100.7163296], zoom:16}"; //Works (P'Nong's office)
		if($prm_init) $attr['data-init'] = htmlspecialchars(htmlspecialchars_decode($prm_init),ENT_QUOTES);
		if($prm_dom_init) $attr['data-dom_init'] = htmlspecialchars(htmlspecialchars_decode($prm_dom_init),ENT_QUOTES);
		if($prm_map_init) $attr['data-map_init'] = htmlspecialchars(htmlspecialchars_decode($prm_map_init),ENT_QUOTES);
		if($prm_poi_init) $attr['data-poi_init'] = htmlspecialchars(htmlspecialchars_decode($prm_poi_init),ENT_QUOTES);
		if($prm_markers) $attr['data-markers'] = htmlspecialchars(htmlspecialchars_decode($prm_markers),ENT_QUOTES);
		$attr['data-map_type'] = $prm_map_type;
		//Process poi
		//$prm_poi = 'gz_location,get_markers,all';
		if($prm['poi']){
			$prm_poi = $prm['poi'];
			if(!empty($prm_poi)){
				$attr['data-markers'] = htmlspecialchars(htmlspecialchars_decode($prm_poi),ENT_QUOTES);
			}
		} //ob_clean(); echo "<pre>"; print_r($attr); die();
		//Render html code
		$attr['data-legend_url'] = '/home/value-color-scales/';
		$html = self::render_dom([
			'dom'=>['type'=>'div' ,'class'=>__CLASS__ .""]
			,'attr'=>$attr
			,'content'	=> self::render_dom([
				'dom'=>['type'=>'div' ,'class'=>"map"]
				,'attr'=>['style'=>$prm_style]
			])
		]);
		return $html;
	}

}
