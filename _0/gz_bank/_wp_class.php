<?php //die(__FILE__);
/*
* Class r2p_site - Control site appearance.
* v0.00 - 20171026:Tony
*/
class gz_bank extends gz_tpl{
	//static $icon = [
	//];
	public function __construct(){//die(__FILE__.__FUNCTION__);
		$config = [
			'enqueue'  => [
				['type'=>'style' ,'load'=>true ,'prm'=>[__CLASS__,'[REL_PATH]wp_style.scss',['jquery_slick','slick_theme','slick_css']]]
				,['type'=>'script' ,'load'=>true ,'prm'=>[__CLASS__,'[REL_PATH]wp_script.js',['jquery_slick']]]
			/*
				,['type'=>'localize', 'prm'=>[__CLASS__,__CLASS__,[
					'ajax'=>admin_url('admin-ajax.php')
				]]]
			*/
			]
		//,['type'=>'style' ,'load'=>true ,'prm'=>['google_fonts','https://fonts.googleapis.com/css?family=Kanit',[]]]
		]; //ob_clean(); echo '<pre>'; print_r($config); die();
		parent::__construct($config); //ob_clean(); echo '<pre>'; print_r($config); die();
		//$this->init_sidebar();
		//$this->init_sidebar_shop_page();
	}

	/*
	//////
	function init_sidebar_shop_page(){//die(__FUNCTION__);
		//add_action('woocommerce_after_shop_loop',[$this,'render_sidebar']);
		add_action('woocommerce_before_main_content',[$this,'render_sidebar']);
	}
	function render_sidebar(){//die(__FUNCTION__);
		dynamic_sidebar('r2p-sidebar');
	}
	//////
	*/
}
