<?php //die(__FILE__);
/*
* Note:
* 	Check https://github.com/edent/QR-Generator-PHP
* v0.00 - 20171026:Tony
*/
class gz_map extends gz_tpl{
	public function __construct(){//die(__FILE__.__FUNCTION__);
		$config = [
			'enqueue'  => [
				['type'=>'script' ,'load'=>true ,'prm'=>[__CLASS__,'[REL_PATH]_wp_script.js',['gmap3']]]
				,['type'=>'style' ,'load'=>true ,'prm'=>[__CLASS__,'[REL_PATH]_wp_style.scss',['gmap3']]]
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
	static function gz_map(){
		$prm = self::shortcode_atts([
			'style'	=> 'border:1% solid white;width:99%;height:400px;'
			//,'init' => "{address:'bangkok', zoom:16}" //Works
			//,'init' => "{center:[13.8388272,100.7163296], zoom:16}" //Works (P'Nong's office)
			//,'init' => "{center:[48.8620722, 2.352047],zoom:16}"
			//,'init' => "{center:{latlng:'bangkok'}, zoom:16}"
			//,'markers' => "[{address:'bangkok', icon:{url:'/wp/wp-content/uploads/2018/02/logo_png-crop-fit-160x97.png'}}]"
			/*
			,'init' => "{center:[13.8388272,100.7163296], zoom:16}"
			,'markers' => "[{latLng:[13.8388272,100.7163296], icon:{url:'/wp/wp-content/themes/treebox/img/map.png'}}]"
			*/
		],func_num_args()>0?func_get_arg(0):[]); //if($prm['debug']){ob_clean(); echo "<pre>"; print_r($prm); die();}
		//ob_clean(); echo "<pre>"; print_r($prm); die();
		extract($prm,EXTR_PREFIX_ALL,'prm');
		//$prm_markers = "[{address:'bangkok', icon:{url:'/wp/wp-content/themes/treebox/img/map.png'}}]";
		$prm_init = "{center:[13.8388272,100.7163296], zoom:16}"; //Works (P'Nong's office)
		$prm_init = htmlspecialchars(htmlspecialchars_decode($prm_init),ENT_QUOTES);
		//$prm_markers = '[]';
		//$prm_markers = "[{address:'RK Park', icon:{url:'/wp/wp-content/themes/treebox/img/map.png' ,size:new google.maps.Size(69,33)}}]";
		//$prm_markers = "[{position:[13.8388272,100.7163296], icon:{url:'/wp/wp-content/themes/treebox/img/map.png' ,size:new google.maps.Size(69,33)}}]";
		//$prm_markers = "[{position:[13.8388272,100.7163296], icon:{url:'/wp/wp-content/themes/treebox/img/map.png'}}]";
		//$prm_markers = "[{position:[13.8388272,100.7163296], icon:{url:'/wp/wp-content/themes/treebox/img/map-97x100.png' ,size:new google.maps.Size(97,100)}}]";
		//$prm_markers = "[{position:[13.838672,100.7181685]}]";
		//$prm_markers = "[{position:[13.838674,100.718169]} ,{position:[13.838672,100.7181685]} ,{position:[13.838872,100.7181685]}]";
		//$prm_markers = "[{position:[13.8388,100.7186], icon:{url:'/wp/wp-content/themes/treebox/img/map-97x100.png'} ,size:new google.maps.Size(97,100) ,origin:new google.maps.Point(50,0)} ,{position:[13.8388,100.7186]}]";
		//$p1 =  "{position:[13.8388,100.7186]}";
		//$p2 = "{position:[13.8388,100.7186], icon:{url:'/wp/wp-content/themes/treebox/img/map-97x100.png'} ,size:new google.maps.Size(97,100) ,origin:new google.maps.Point(0,0)}";
		//$p2 = "{position:[13.8388,100.7186], icon:{url:'/wp/wp-content/themes/treebox/img/map-97x100.png'}}";
		//$p3 = "{position:[13.8388,100.7186], icon:{url:'/wp/wp-content/themes/treebox/img/map-97x100.png'} ,size:new google.maps.Size(97,100) ,anchor:new google.maps.Point(50,50)}";
		//$p3 = "{position:[13.8388,100.7186], icon:{url:'/wp/wp-content/themes/treebox/img/map-97x100.png'} ,size:[97,100] ,origin:[0,0]}";
		//$p3 = "{position:[13.8388,100.717], icon:{url:'/wp/wp-content/themes/treebox/img/map-97x100.png'} ,scaledSize:[50,50]}";
		//$prm_markers = "[{$p1},{$p2},{$p3}]";
		$m1 = "{position:[13.8388,100.7186], icon:{url:'/wp/wp-content/themes/treebox/img/map-97x100.png'}}";
		$prm_markers = "[{$m1}]";
		$prm_markers = htmlspecialchars($prm_markers,ENT_QUOTES);
		$html = self::render_dom([
			'dom'=>['type'=>'div' ,'class'=>__CLASS__ .""]
			,'attr'=>['style'=>$prm_style ,'data-init'=>$prm_init ,'data-markers'=>$prm_markers]
			//,'attr'=>['src'=>$img_url ,'style'=>'display:inline;']
		]);
		return $html;
	}

}
