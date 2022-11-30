<?php //die(__FILE__);
/*
v0.06 - 20200401:Tony
	- Add examples to ajaxes, scripts, shortcodes, scripts_login
v0.05 - 20200107:Tony
	- Add shortcodes,actions,filters,ajaxes in config
v0.02 - 20180426:Tony
	- Add post_type init
v0.01 - 20180216:Tony
	- Add $this->url_full
	- Add $this->auto_load
v0.00 - 20170510:Tony
*/
class gz_tpl{
	protected $id = __CLASS__;
	protected $url,$dir,$full_url;			//Using relative url here
	protected $post=false ,$metas=false;
	protected $scripts = false;
	protected $admin_scripts = false;
	protected $config = false;

	public function __construct($config=false){//if(get_class($this)=='tb_page'){ob_clean(); echo '<pre>'.__CLASS__; print_r($this); die();}
		$this->id = $class_name = static::class;

		$this->config = $config; //Will make it merge in case there're more than one $config
		 //Use ?action=<classname> to get all the available AJAX Call
		$this->config['ajaxes'][] = ['prm'=>[static::class,[$this,'info']]];

		//$this->id = $class_name; //if($this->id=='tb_page') {ob_clean(); echo '<pre>'; print_r($this->config); die();}	
		//Init path
		$reflector = new ReflectionClass($class_name);
		$file_name = $reflector->getFileName();
		$this->url = substr(dirname($file_name),strlen(ABSPATH)-1); $this->url = str_replace(array('/','\\'),'/',$this->url).'/'; //die($rel_path);
		$this->url_full = site_url($this->url);
		$this->dir = ABSPATH.substr($this->url,1);
		unset($reflector);
		$this->init();
		//if(get_class($this)=='tb_page'){ob_clean(); echo '<pre>'.__CLASS__; print_r($this); die();}
	}

	/**
	 * function info() - Show list of available ajaxes.
	 */
	static function info(){//die('xxx');
		global $GZ;
		if(isset($_GET['action']) && $_GET['action']=='gz_tpl'){
			foreach($GZ->modules as $item){//die('<pre>'.print_r($item,true));
				//$item->_info($item->config['ajaxes']);
			}
		}
		else echo self::_info(['url'=>$this->config['ajaxes']]);
		die(0);
	}
	static function _info($info=[]){//die('<pre>'.print_r($info['url'],true));
		$html = '';
		if(is_array($info)) foreach($info['url'] as $item){//die('<pre>'.print_r($item['prm'][0],true));
			$url = add_query_arg(['action'=>$item['prm'][0]]);
			$html.= "<a href='{$url}'>{$url}</a><br/>";
		}
		return $html;
	}

	function init_module($prm){
		//$prm = $this->
		//$mod_type = $prm['type'];
		//$mod_name = $prm['name'];
		//$mod_
		//require $prm['']
	}
	
	function dump_hook(){
		$hook_name = 'wp_enqueue_scripts';
		global $wp_filter; //ob_clean(); echo '<pre>'; print_r(array_keys((array)$wp_filter['wp_enqueue_scripts']['callbacks'])); die();
		//ob_clean(); echo '<pre>'; print_r($wp_filter['wp_enqueue_scripts']['callbacks']); die();
		foreach($wp_filter['wp_enqueue_scripts']['callbacks'] as $hook){ print_r($hook);
		}
		//echo '<pre>'; print_r($hook);
	}
	
	private function init(){//die('<pre>'.print_r($this->config,true));
		if(isset($this->config['enqueue'])) 		add_action('wp_enqueue_scripts',[$this,'init_scripts']);
		if(isset($this->config['enqueue_admin'])) 	add_action('admin_enqueue_scripts',[$this,'init_scripts_admin']);
		if(isset($this->config['enqueue_login'])) 	add_action('login_enqueue_scripts',[$this,'init_scripts_login']);
		if(isset($this->config['post_types'])) 		add_action('wp_loaded',[$this,'init_post_types']);
		if(isset($this->config['cmb2'])) 			add_action('cmb2_init',[$this,'init_cmb2']);
		if(isset($this->config['meta_tag'])) 		add_action('wp_head',[$this,'init_meta_tag']);
		if(isset($this->config['image_sizes']))		add_action('wp_loaded',[$this,'init_image_sizes']);
		if(isset($this->config['shortcodes']))		add_action('wp_loaded',[$this,'init_shortcodes']);
		if(isset($this->config['filters']))			add_action('wp_loaded',[$this,'init_filters']);
		if(isset($this->config['actions']))			add_action('wp_loaded',[$this,'init_actions']);
		if(isset($this->config['ajaxes']))			add_action('wp_loaded',[$this,'init_ajaxes']);
	}

	/**
	 * 	'ajaxes' => [
	 *		['prm'=>['get_provinces',[$this,'get_provinces']]]
	 *	]
	 */
	public function init_ajaxes(){
		if(is_array($ajaxes=$this->config['ajaxes'])){
			foreach($ajaxes as $item){
				$prm = $item['prm']; $prm[0] = "wp_ajax_{$prm[0]}"; 		call_user_func_array('add_action',$prm);
				$prm = $item['prm']; $prm[0] = "wp_ajax_nopriv_{$prm[0]}"; 	call_user_func_array('add_action',$prm);
			}
		} //ob_clean(); echo '<pre>'; print_r($image_sizes); die();
	}

	public function init_filters(){
		if(is_array($filters=$this->config['filters'])){
			foreach($filters as $item){
				call_user_func_array('add_filter',$item['prm']);
			}
		} //ob_clean(); echo '<pre>'; print_r($image_sizes); die();
	}

	/**
	 * function init_actions() - Add WP actions
	 */
	public function init_actions(){
		if(is_array($actions=$this->config['actions'])){
			foreach($actions as $item){
				call_user_func_array('add_action',$item['prm']);
			}
		} //ob_clean(); echo '<pre>'; print_r($image_sizes); die();
	}

	/**
	 *	'shortcodes' => [
	 *		['prm'=>['test_app',[$this,'render_test_app']]]
	 *	]
	 */
	public function init_shortcodes(){//die('<pre>'.print_r($this,true));
		if(is_array($shortcodes=$this->config['shortcodes'])){
			foreach($shortcodes as $item){ //die('<pre>'.print_r($item,true));
				call_user_func_array('add_shortcode',$item['prm']);
			}
		} //ob_clean(); echo '<pre>'; print_r($image_sizes); die();
	}

	public function init_image_sizes(){
		if(is_array($image_sizes=$this->config['image_sizes'])){
			foreach($image_sizes as $key=>$val){
				add_image_size('$key',$val[0],$val[1],$val[2]);
			}
		} //ob_clean(); echo '<pre>'; print_r($image_sizes); die();
		//ob_clean(); echo '<pre>'; print_r(get_intermediate_image_sizes()); die();
	}

	/***
	'post_types'=>[
		'poi' => [
			'label'			=> 'POI'
			,'description'	=> 'Point of interest'
			,'supports'		=> ['title' ,'editor' ,'excerpt' ,'thumbnail' ,'revisions' ,'custom-fields']
			,'public'		=> true
			//,'heirachical'	=> false
			//,'show_ui'		=> true
			//,'show_in_menu'	=> true
			//,'show_in_nav_menus'	=> true
			//,'show_in_admin_bar'	=> true
			//,'has_archive'	=> true
			//,'can-export'	=> true
			//,'exclude_from_search'	=> false
			//,'yarpp_support'	=> true
			//,'taxonomies'		=> ['post_tag']
			//,'publicly_queryable'	=> true
			//,'capability_type'	=> 'post'
		]
	]
	 */
	public function init_post_types(){//die(__FUNCTION__);
		$post_types = $this->config['post_types'];
		foreach($post_types as $id=>$prm){//ob_clean(); echo '<pre>'; print_r($prm); die();
			register_post_type($id,$prm);
		}
	}
	
	public function init_meta_tag(){
		echo "\n";
		if(isset($this->config['meta_tag']))foreach($this->config['meta_tag'] as $item){
			extract($item,EXTR_PREFIX_ALL,'item');
			echo "<meta name='{$item_name}' content='{$item_content}'>\n";
		}
	}
	
	/***
	'cmb2' =>[
		'poi' => [
			'prefix'	=> 'poi'
			,'cmb2_args'	=> [
				//'id'	=> '_smf_ticket'
				'title'	=> "POI"
				,'closed'	=> false
				,'object_types'	=> ['post']
				,'fields'	=> [
					['id'=>'name','name'=>'Name','type'=>'text']
				]
			]
		]
	]
	*/
	public function init_cmb2(){//die(__FUNCTION__);
		if(isset($this->config['cmb2']))foreach($this->config['cmb2'] as $item){//ob_clean(); echo "<pre>"; print_r($item); die();
			$this->init_cmb2_do($item); //ob_clean(); die();
		}
	}

	function init_cmb2_do($prm){//ob_clean(); echo "<pre>"; print_r($prm); die();
		if(!empty($prm['prefix'])){
			//Apply prefix to all field ID
			foreach($prm['cmb2_args']['fields'] as &$field) $field['id'] = $prm['prefix'].$field['id'];
		}
		//{ob_clean(); echo "<pre>"; print_r($prm); die();}
		extract($prm,EXTR_PREFIX_ALL,'prm');
		//if(!empty($prm_cmb2_args['prefix']))foreach($prm_cmb2_args['fields'] as &$field) $field['id']=$prm_cmb2_args['prefix'].$field['id'];
		if(!isset($prm_cmb2_args['id'])) $prm_cmb2_args['id'] = $prm_prefix;
		 
		$cmb2 = new_cmb2_box($prm_cmb2_args);
		$form_id = 'form_'.$prm_cmb2_args['id']; $this->$form_id = $cmb2; //Make it available for use in class
		return $cmb2;
	}

	public function init_scripts(){//echo '<pre>'; print_r($this->config); die();
		if(is_array($this->config['enqueue'])) foreach($this->config['enqueue'] as $item) $this->do_init_scripts($item);
	}
	
	public function init_scripts_admin(){//echo '<pre>'; print_r($this->config); die();
		if(is_array($this->config['enqueue_admin'])) foreach($this->config['enqueue_admin'] as $item) $this->do_init_scripts($item);
	}
	
	/**
	 *	'enqueue_login'  => [
	 *		['type'=>'style' ,'load'=>true ,'prm'=>[__CLASS__,'[REL_PATH]_wp_style.scss',[]]]
	 *	]
	 */
	public function init_scripts_login(){//echo '<pre>'; print_r($this->config); die();
		if(is_array($this->config['enqueue_login'])) foreach($this->config['enqueue_login'] as $item) $this->do_init_scripts($item);
	}

	/**
	 *	'enqueue'  => [
	 *		['type'=>'style' ,'load'=>true ,'prm'=>[__CLASS__,'[REL_PATH]_wp_style.scss',[]]]
	 *		,['type'=>'script' ,'load'=>true ,'prm'=>[__CLASS__,'[REL_PATH]_wp_script.js',['jquery-ui-core','jquery-ui-tabs']]]
	 *		,['type'=>'localize', 'prm'=>[__CLASS__,__CLASS__,[
	 *			'ajax'=>admin_url('admin-ajax.php')
	 *			,'provinces'=>$this->provinces
	 *		]]]
	 *	]
	 * 
	 */
	public function do_init_scripts($item){
		if(isset($item['prm'][1])) $item['prm'][1] = str_replace('[REL_PATH]',$this->url,$item['prm'][1]); //ob_clean(); echo '<pre/>'; print_r($item['prm'][1]); print_r($item); die();
		switch($item['type']){
			case 'descript':
				wp_deregister_script($item['prm'][0]);
				break;
			case 'destyle':
				wp_deregister_style($item['prm'][0]);
				break;
			case 'script': //if($item['prm'][0]=='googlemapsapi3') ob_clean(); echo '<pre/>'; print_r($item); die();
				call_user_func_array('wp_register_script',$item['prm']);
				if(isset($item['load'])&&(true==$item['load'])) wp_enqueue_script($item['prm'][0]);
				break;
			case 'style': //die($item['prm'][1]);
				call_user_func_array('wp_register_style',$item['prm']);
				if(isset($item['load'])&&(true==$item['load'])) wp_enqueue_style($item['prm'][0]);
				break;
			case 'localize': //ob_clean(); echo '<pre>'; print_r($item['prm']); die();
				call_user_func_array('wp_localize_script',$item['prm']);
				//wp_enqueue_script($item['prm'][0]);
				break;
		}
	}

	/*
	* Class can be both in dom and attr.
	*/
	static function render_dom($prm){ //ob_clean(); echo '<pre>'; print_r($prm); die();
		$prm_prm = [];
		$prm_content = false;
		extract($prm,EXTR_PREFIX_ALL,'prm'); //if(isset($prm__debug)){ob_clean(); echo "<pre>"; var_dump($prm); die();}
		extract($prm_dom,EXTR_PREFIX_ALL,'dom');
		$content = "<{$dom_type} ";
			if(!empty($dom_class)) $content.="class='{$dom_class}'"; 		//Add class from dom array
			if(!empty($prm_attr)) foreach($prm_attr as $key=>$val){ //if(is_null($val)) die($key);
				if(is_null($val)) $content.="{$key} "; else $content.="{$key}='{$val}' "; //Add all attr (also class).
			}
		$content.= ">";
		//ob_clean(); echo gettype($prm_content); die();
		if(is_callable($prm_content)) $content.=call_user_func_array($prm_content,$prm_prm);
		elseif(!empty($prm_content)) $content.=$prm_content;
		$content.= "</{$dom_type}>";
		return $content;
	}
	
	protected function get_post(){if(empty($this->post)) {global $post; $this->post = $post;}}
	protected function get_metas($id=false){
		if(empty($this->metas)) $this->metas = get_post_meta($this->post->ID);
		if($id&&isset($this->metas[$id])&&isset($this->metas[$id][0])) return $this->metas[$id][0]; else return false;
	}
	
	static function shortcode_atts($default,$arr){
		$arr = shortcode_atts($default,$arr);
		foreach($default as $key=>$var) if(is_array($var)) $arr[$key] = shortcode_atts($var,$arr[$key]);
		return $arr;
	}
	
	/*Keep for compatibility*/
 	protected function set_id($id){
		$this->id = $id;
	}

	/*Keep for compatibility*/
	protected function get_url($f){
		$this->url = substr(dirname($f),strlen(ABSPATH)-1); $this->url = str_replace(array('/','\\'),'/',$this->url).'/'; //die($rel_path);
		$this->dir = ABSPATH.$this->url;
	}
}

function test(){ob_clean(); die('test');
}
