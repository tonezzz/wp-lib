<?php //die(__FILE__);
/*
v0.00 - 20170510:Tony
*/
class gz_tpl{
	protected $id = __CLASS__;
	protected $url,$dir;			//Using relative url here
	protected $post=false ,$metas=false;
	
	/*
	* Class can be both in dom and attr.
	*/
	public function render_dom($prm){
		$prm_prm = [];
		$prm_content = false;
		extract($prm,EXTR_PREFIX_ALL,'prm'); if(isset($prm__debug)){ob_clean(); echo "<pre>"; var_dump($prm); die();}
		extract($prm_dom,EXTR_PREFIX_ALL,'dom');
		$content = "<{$dom_type} ";
			if(!empty($dom_class)) $content.="class='{$dom_class}'"; 		//Add class from dom array
			if(!empty($prm_attr)) foreach($prm_attr as $key=>$val) $content.="{$key}='{$val}' "; //Add all attr (also class).
		$content.= ">";
		//ob_clean(); echo gettype($prm_content); die();
		if(is_callable($prm_content)) $content.=call_user_func_array($prm_content,$prm_prm);
		elseif(!empty($prm_content)) $content.=$prm_content;
		$content.= "</{$dom_type}>";
		return $content;
	}

	public function __construct(){//die(__FILE__.__FUNCTION__);
		//$this->get_url(__FILE__);
		//$this->init_scripts();
	}
	
	public function set_id($id){
		$this->id = $id;
	}

	public function get_url($f){
		$this->url = substr(dirname($f),strlen(ABSPATH)-1); $this->url = str_replace(array('/','\\'),'/',$this->url).'/'; //die($rel_path);
		$this->dir = ABSPATH.$this->url;
	}
	
	private function get_post(){if(empty($this->post)) {global $post; $this->post = $post;}}
	private function get_metas($id){
		if(empty($this->metas)) $this->metas = get_post_meta($this->post->ID);
		if(isset($this->metas[$id])&&isset($this->metas[$id][0])) return $this->metas[$id][0]; else return false;
	}
	
	function init_scripts(){
		//add_action('admin_enqueue_scripts',array($this,'register_scripts_admin'));
		//add_action('admin_enqueue_scripts',array($this,'enqueue_scripts_admin'));
		//add_action('wp_enqueue_scripts',array($this,'register_scripts'));
		//add_action('wp_enqueue_scripts',array($this,'enqueue_scripts'));
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
	
	public function register_scripts(){
		//wp_register_script($this->id,$this->url.'_wp_script.js',array('jquery-core','jquery_fancybox'));
		//wp_register_style($this->id,$this->url.'_wp_style.scss',array('jquery_fancybox'));
		//wp_register_script('facebook-jssdk','https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.8&appId=339171656464865');
		/*
		$fb_url = 'https://connect.facebook.net/en_US/sdk.js#'.http_build_query([
			'xfbml'	=> 1
			,'version'	=> '2.8'
			,'appId'	=> $this->facebook->app_id
		]); //if(isset($_GET['d_gz_social'])){var_dump($fb_url); die();}
		wp_register_script('facebook-jssdk',$fb_url);
		wp_register_script($this->id,$this->url.'_wp_script.js',array('facebook-jssdk'));
		*/
		wp_register_script($this->id,$this->url.'_wp_script.js');
		wp_register_style($this->id,$this->url.'_wp_style.scss',array('rwh'));
	}
	
	public function enqueue_scripts(){
		//wp_enqueue_script('facebook-jssdk');
		wp_enqueue_script($this->id);
		wp_enqueue_style($this->id);
	}

}
