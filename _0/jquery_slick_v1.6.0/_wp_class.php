<?php //die(__FILE__);
class jquery_slick extends gz_tpl{

	public function __construct(){//die(__FILE__.__FUNCTION__);
		$config = [
			'meta_tag' => [
				['name'=>'mobile-web-app-capable' ,'content'=>'yes']
				,['name'=>'apple-mobile-web-app-capable' ,'content'=>'yes']
			]
			,'enqueue'  => [//@import './fonts/css/fontello.css';
				['type'=>'style' ,'prm'=>['slick_css','[REL_PATH]slick/slick.scss']]
				,['type'=>'style' ,'prm'=>['slick_theme','[REL_PATH]slick/slick-theme.scss'],['slick_css']]
				,['type'=>'script' ,'load'=>true ,'prm'=>[__CLASS__,'[REL_PATH]slick/slick.min.js'],['jquery-core']]
				,['type'=>'style' ,'load'=>true ,'prm'=>[__CLASS__,'[REL_PATH]wp_style.scss'],['slick_theme']]
			]
		];
		parent::__construct($config); //init_shortcodes
	}

}