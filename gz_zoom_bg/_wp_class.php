<?php //die(__FILE__);
/*
v0.00 - 20170510:Tony
*/
class gz_zoom_bg extends gz_tpl{
	public function __construct(){//die(__FILE__.__FUNCTION__);
		parent::__construct();
		$this->get_url(__FILE__);
		$this->set_id(__CLASS__);
		$this->init_scripts();
	}
	
	function init_scripts(){//die(__FUNCTION__);
		//add_action('admin_enqueue_scripts',array($this,'register_scripts_admin'));
		//add_action('admin_enqueue_scripts',array($this,'enqueue_scripts_admin'));
		add_action('wp_enqueue_scripts',array($this,'register_scripts'));
		add_action('wp_enqueue_scripts',array($this,'enqueue_scripts'));
	}

	public function register_scripts_admin(){
		$url = eval(WP_MISC_RELATIVE_URL);
		wp_register_script($this->id,$url.'_wp_admin_script.js',array('jquery-core'));
		wp_register_style($this->id,$url.'_wp_style_admin.css');
	}
	
	public function enqueue_scripts_admin(){
		wp_enqueue_script($this->id);
		wp_enqueue_style($this->id);
	}
	
	public function register_scripts(){//die(__FILE__);
		wp_register_script($this->id,$this->url.'_wp_script.js',array('jquery-core'));
		wp_register_style($this->id,$this->url.'_wp_style.scss');
	}
	
	public function enqueue_scripts(){
		//wp_enqueue_script('facebook-jssdk');
		wp_enqueue_script($this->id);
		wp_enqueue_style($this->id);
	}

}
