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
			'style'		=> 'width:100%;height:100%;'
			,'init' 	=> "{address:'bangkok', zoom:16}" //Works
			//,'init' 	=> "{center:[13.8388272,100.7163296], zoom:16}" //Works (P'Nong's office)
			,'icon'		=> false
			,'markers' 	=> false
			,'poi'		=> false
		],func_num_args()>0?func_get_arg(0):[]); //if($prm['debug']){ob_clean(); echo "<pre>"; print_r($prm); die();}
		//ob_clean(); echo "<pre>"; print_r($prm); die();
		extract($prm,EXTR_PREFIX_ALL,'prm');
		$attr = [];
		if($prm_icon){
			$attr['data-icon'] = htmlspecialchars(htmlspecialchars_decode($prm_icon),ENT_QUOTES);
		}
		if($prm_style){
			$attr['style'] = htmlspecialchars(htmlspecialchars_decode($prm_style),ENT_QUOTES);
		}
		//$prm_init = "{center:[13.8388272,100.7163296], zoom:16}"; //Works (P'Nong's office)
		if($prm_init){
			$attr['data-init'] = htmlspecialchars(htmlspecialchars_decode($prm_init),ENT_QUOTES);
		}
		if($prm_markers){
			$attr['data-markers'] = htmlspecialchars($prm_markers,ENT_QUOTES);
		}
		//Process poi
		$prm_poi = 'gz_location,get_poi,all';
		if($prm_poi){
			//$prm = explode(',',$prm_poi);
			//$prm_st = $prm[0].'::'.get_poi($prm[1]);
			//$prm_st = $prm[0].'::'.get_poi($prm[1]);
			$attr['data-poi'] = htmlspecialchars($prm_poi,ENT_QUOTES);
		}
		//ob_clean(); echo "<pre>"; print_r($attr); die();
		//Render html code
		$html = self::render_dom([
			'dom'=>['type'=>'div' ,'class'=>__CLASS__ .""]
			,'attr'=>$attr
		]);
		return $html;
	}

}
