<?php
/*
* gz_facebook() class.
*	- Render page badge in iFrame.
* 	- Retrieve photos from FB album.
* v0.02/20180405:Tony
*	- Fix width doesn't apply on badge rendering (caused by wrong url param rendering).
* v0.01/20180223:Tony
*	- Use gz_tpl v0.01 style
*	- Add shortcode for FB box
* v0.00/20171002:Tony
* 	- Add feature, retrieve photos from FB album.
*		- Choose group by resolutions.
die
*/
class gz_multilang extends gz_tpl{
	public $the_content_off=false;
	private $is_load_theme_textdomain=false;
	private $text_domain='mafoil';

	public function __construct($text_domain=false){
		if($text_domain) $this->text_domain = $text_domain;
		$config = [
			'enqueue'  => [
				//['type'=>'style' ,'load'=>true ,'prm'=>['font-awesome','//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css']],
				//['type'=>'style' ,'load'=>true ,'prm'=>[__CLASS__,'[REL_PATH]wp_style.scss'],['font-awesome']],
				['type'=>'script' ,'load'=>true ,'prm'=>[__CLASS__,'[REL_PATH]wp_script.js',['jquery-core']]],
				['type'=>'localize', 'prm'=>[__CLASS__,__CLASS__,[
					//'menu_lang'	=> $this->render_menu_lang(),
				]]]
			],
			'cmb2v2' => [
				['prm'=>[
					'id'					=> 'en',
					'title' 				=> __('English','woocommerce'),
					'object_types'			=> ['post','page','product','nav_menu_item'],
					'context'				=> 'normal',
					'fields' 				=> [
						['id'=>'_post_title_en' ,'name'=>__('Title (English)','woocommerce') ,'type'=>'text'],
						//['id'=>'_post_sub_th' ,'name'=>__('Sub title (Thai)','woocommerce') ,'type'=>'textarea_small'],
						//['id'=>'_post_sub_en' ,'name'=>__('Sub title (English)','woocommerce') ,'type'=>'textarea_small'],
						['id'=>'_post_content_en' ,'name'=>__('Post content (English)','woocommerce') ,'type'=>'wysiwyg'],
						['id'=>'_post_excerpt_en' ,'name'=>__('Post excerpt (English)','woocommerce') ,'type'=>'wysiwyg'],
					]
				]],
			],
  		   	'ajaxes' => [
			],
			'filters' => [
				['prm'=>['locale',[$this,'get_locale']]],
				//For displaying content in multi language
				['prm'=>['the_title',[$this,'the_title'],10,2]],
				['prm'=>['get_term',[$this,'get_term'],10,2]],

				//['prm'=>['woocommerce_cart_shipping_method_full_label',[$this,'woocommerce_cart_shipping_method_full_label'],10,2]],
				['prm'=>['woocommerce_shipping_rate_label',[$this,'woocommerce_shipping_rate_label'],10,2]],
				['prm'=>['woocommerce_gateway_title',[$this,'woocommerce_gateway_title'],10,2]],
				['prm'=>['woocommerce_gateway_description',[$this,'woocommerce_gateway_description'],10,2]],
				['prm'=>['woocommerce_cart_item_name',[$this,'woocommerce_cart_item_name'],10,3]],

				//['prm'=>['wp_setup_nav_menu_item',[$this,'filter_menu_lang'],1]],
				//['prm'=>['the_excerpt',[$this,'get_the_excerpt'],21,2]],
				//['prm'=>['get_the_excerpt',[$this,'get_the_excerpt'],21,2]],
				//For language switching
				//['prm'=>['locale',[$this,'get_locale_2'],50,1]],
				//['prm'=>['determine_locale',[$this,'get_locale']]],
				//Old
				//['prm'=>['woocommerce_get_breadcrumb',[$this,'woocommerce_get_breadcrumb'],10,2]],
				//['prm'=>['woocommerce_before_main_content',[$this,'woocommerce_breadcrumb'],20,2]],
				//['prm'=>['woocommerce_before_main_content',[$this,'woocommerce_breadcrumb'],20]],
			],
			'remove_actions' => [
				//['prm'=>['storefront_page','storefront_page_content',20]],
				//['prm'=>['woocommerce_shop_loop_item_title','woocommerce_template_loop_product_title',10]],
				//['prm'=>['woocommerce_single_product_summary','woocommerce_template_single_title',5]],
				//['prm'=>['woocommerce_before_main_content','woocommerce_breadcrumb',20]],
			],
			'actions' => [
				//['prm'=>['init',[$this,'init_lang'],0]],
				['prm'=>['template_redirect',[$this,'template_redirect'],0]],
				['prm'=>['after_setup_theme',[$this,'load_translations']]],
				//['prm'=>['admin_init',[$this,'ajax_pre_process'],10,2]],
				//['prm'=>['storefront_page',[$this,'storefront_page_content'],20]],
				//['prm'=>['woocommerce_shop_loop_item_title',[$this,'woocommerce_shop_loop_item_title'],10]],
				//['prm'=>['woocommerce_single_product_summary',[$this,'woocommerce_single_product_summary'],5]],
			],
			'shortcodes' => [
				['prm'=>['mv_text',[$this,'mv_text']]],
			],
		];
		parent::__construct($config);
		$this->init_locale();
		//$this->load_translations();
		//remove_action( 'storefront_page', 'storefront_page_content', 20);
	}

	function woocommerce_cart_item_name($product_name, $cart_item, $cart_item_key){
		//ob_clean(); die('<pre>'.print_r($product_name,true));
		return $this->the_title($product_name,$cart_item['product_id']);
	}

	//function ajax_pre_process(){
	//	die('<pre>'.print_r($_REQUEST,true).'</pre>');
	//}

	function get_term($_term, $taxonomy){
		$this->load_translations();
		$_term->name = __($_term->name,$this->text_domain);
		$_term->description = __($_term->description,$this->text_domain);
		//die('<pre>'.print_r($_term,true).print_r($taxonomy,true));
		return $_term;
	}

	//function filter_menu_lang($menu){
	//	//if('th'!=$this->get_current_lang()) $menu->title = __($menu->title,'gz_multilang');
	//	return $menu;
	//}

	//Replace with correct language if exists.
	function template_redirect(){
		global $post;
		//if ($data=get_post_meta($post->ID,'_post_content_'.$this->get_current_lang(),true)) $post->post_content = $data;
		//if ($data=get_post_meta($post->ID,'_post_excerpt_'.$this->get_current_lang(),true)) $post->post_excerpt = $data;
		if ($data = get_field_lang($post,'_post_content_',$this->get_current_lang(),false); $post->post_content = $data;
		if ($data = get_field_lang($post,'_post_excerpt_',$this->get_current_lang(),false); $post->post_excerpt = $data;
	}

	/*
	* If there's meta _post_title_en then return it, otherwise return the original title;
	* https://developer.wordpress.org/reference/functions/get_the_title/
	*/
	function the_title($title,$post_id){ if(isset($_GET['d'])) //die($this->get_current_lang());
		if (is_admin() && !wp_doing_ajax()) return $title;
		if (!empty($suffix=$this->get_suffix())){ //die($suffix);
			if ($new_title=get_post_meta($post_id,'_post_title'.$suffix,true)) $title = $new_title; //die($title);
		}
		return $title;
	}

	/*
	* If there's meta _post_excerpt_en then return it, otherwise return the original title;
	* https://developer.wordpress.org/reference/functions/get_the_excerpt/
	*/
	function get_the_excerpt($excerpt,$post){ if(isset($_GET['d'])) die($this->get_current_lang());
		global $post;
		$excerpt = get_field_lang($post,'_post_excerpt_',$this->get_current_lang(),$excerpt); //die($excerpt);
		//if($new_excerpt=get_post_meta($post->ID,'_post_excerpt_'.$this->get_current_lang(),true)) return $new_excerpt;
		//else return $excerpt;
	}

	///////////////////////
	// Woocommerce
	//

	//function woocommerce_cart_shipping_method_full_label($label, $method){//ob_clean(); die($label);
	//	if(strpos($label,'COD')) {ob_clean(); die(__($label,$this->text_domain));}
	//	return __($label,$this->text_domain);
	//}

	/**
	 * Translate payment gateway title (e.g. 'Direct Bank Transfer')
	 *
	 * @param [type] $title
	 * @param [type] $id
	 * @return translated title
	 */
	function woocommerce_gateway_title($title,$id){
		$this->load_translations();
		return __($title,$this->text_domain);
	}

	/**
	 * Translate payment gateway description (e.g. 'mv_text_direct_bank_transfer_description')
	 *
	 * @param [type] $description
	 * @param [type] $id
	 * @return translatre description
	 */
	function woocommerce_gateway_description($description,$id){ //return $description;
		//ob_clean(); die($this->get_current_lang()); die($description);
		$this->load_translations();
		return __($description,$this->text_domain);
	}

	function woocommerce_shipping_rate_label($label,$obj){
		$this->load_translations();
		return __($label,$this->text_domain);
	}

	function woocommerce_shop_loop_item_title(){
		echo $this->get_product_title();
	}

	function woocommerce_single_product_summary(){
		echo $this->get_product_title();
	}
	  
	///////////////////////
	//Initializing functions
	//

	/*
	 * 1. Use locale param if availabe.  Also update user_lang_pref if login.
	 * 2. Use loale from user_lang_pref if login.
	*/
	function init_locale(){ //return;
		if(isset($_GET['lang'])){ //"lang=th or lang=en"
			$lang = $_GET['lang'];
			//if($user_id=get_current_user_id()) update_user_option($user_id,'user_lang_pref',$locale);
			//$_COOKIE['gz_lang'] = $lang; //echo $lang.' '.$_COOKIE['gz_lang'];
			//$this->is_load_theme_textdomain = false; //Clear theme textdomain flag for reloading
		}else{
			//$lang = "th";
			//if($user_id=get_current_user_id()) $locale = get_user_option('user_lang_pref',$user_id );
			$lang = isset($_COOKIE['gz_lang'])?$_COOKIE['gz_lang']:"";
			//if($locale=='en') $locale='en_US';
		}
		$this->set_lang($lang);
	}

	function set_lang($lang=''){
		switch($lang){
			case 'en': $lang='en'; $locale='en_US'; break;
			case 'th': $lang='th'; $locale='th_TH'; break;
			default: $lang='th'; $locale='th_TH';
		}
		setcookie('gz_lang',$locale,time()+60*60*24*355); $_COOKIE['gz_lang'] = $lang;
		setcookie('gz_locale',$locale,time()+60*60*24*355); $_COOKIE['gz_locale'] = $locale;
		switch_to_locale($locale);

		if(isset($_GET['d'])) {
			//$this->show_debug_lang(); die();
			$locale = get_locale(); //die($locale);
		}
		//if(empty($locale)) $locale = 'en_US';
		//switch_to_locale($locale);
		$this->load_translations();
	}

	function get_suffix(){
		if($_COOKIE['gz_lang']=='th') return '';
		else return '_'.$_COOKIE['gz_lang'];
	}
	/*
	* Setup language base on user's preference.
	*	?lang=xx > cookie:gz_lang
	*/
	//function get_locale_2($locale='en_US'){$locale='en_US';
	//	return $locale;
	//}
	function get_locale(){ //die($_COOKIE['gz_locale']);
		$locale = isset($_COOKIE['gz_locale'])?$_COOKIE['gz_locale']:'th_TH'; //if(isset($_GET['d'])) die($locale);
		return $locale;
	}

	/**
	 * load_child_theme_textdomain($this->text_domain,get_stylesheet_directory().'/languages');
	 */
	function load_translations($force=false){
		if($force || !$this->is_load_theme_textdomain){
			update_option('template',$this->text_domain);
			load_theme_textdomain($this->text_domain,get_stylesheet_directory().'/languages');
		}
	}

	//
	//Support functions
	//
	function mv_text($atts,$content=null){//die('='.__($content,$this->text_domain).'=');
		$atts = shortcode_atts([
			'domain' 	=> $this->text_domain
		],$atts,'mv_text');
		extract($atts, EXTR_PREFIX_ALL,'att');
		$this->load_translations();
		$html = '';
		$html.= __($content,$att_domain);
		return $html;
	}

	function get_current_lang(){
		if (isset($_GET['lang'])) $lang = $_GET['lang'];
		elseif (isset($_COOKIE['lang'])) $lang = $_COOKIE['lang'];
		else $lang = "th_TH";
		return substr($lang,0,2);
	}

	function get_field_lang($post,$fn,$lang,$default=''){
		if(is_admin() && !wp_doing_ajax()) return $excerpt;
		//global $post;
		if($val=get_post_meta($post->ID,$fn.'_'.$lang)) return $val; else return $default;
	}

	function show_debug_lang(){
		echo '<pre>'.print_r([
			'is_admin()'		=> is_admin(),
			'wp_doing_ajax()'	=> wp_doing_ajax(),
			'$_GET["lang"]'		=> isset($_GET['lang'])?$_GET['lang']:'n/a',
			'$_COOKIE["gz_locale"]'	=> $_COOKIE["gz_locale"],
			'$_COOKIE["gz_lang"]'	=> $_COOKIE["gz_lang"],
			//'get_current_user_id()'	=> get_current_user_id(),
			//'get_user_option()'		=> get_user_option( 'user_lang_pref', 1 ),
			//'$this->get_locale()'	=> $this->get_locale(),
			//'get_locale()'			=> get_locale(),
			//'get_current_lang()'	=> $this->get_current_lang(),
		],true).'</pre>';
	}
	///////////////////////////////////////////
	// Unused
	/*
	//Get specific language from Valexar string
	function valexar_text_lang($txt,$lang,$default){
		$ts = $this->split_langs($txt); //ob_clean(); die('<pre>'.print_r($titles,true));
		return isset($ts[$lang])?$ts[$lang]:$default;
	}
	*/

	/*
	function woocommerce_get_breadcrumb($crumbs,$breadcrumb){
		//$lang = $this->get_current_lang();
		//foreach($crumbs as &$item){
		//	$arr = $this->split_langs($item[0]);
		//	$item[0] = isset($arr[$lang])?$arr[$lang]:$item[0];
		//}
		return $crumbs;
		//die('<pre>'.print_r($crumbs,true).print_r($breadcrumb,true));
	}
	*/

	/*
	function woocommerce_breadcrumb(){
		global $crumbs,$breadcrumb;
		die('<pre>'.print_r($crumbs,true).print_r($breadcrumb,true));
	}
	*/

	/*
	function get_product_title(){
		$titles = $this->split_langs(get_the_title()); //ob_clean(); die('<pre>'.print_r($titles,true));
		$lang = $this->get_current_lang();
		return isset($titles[$lang])?$titles[$lang]:get_the_title();
	}
	*/

	/*
	function split_langs($st,$langs=['th','en']){
		$arr = explode('[:',$st);
		$rs = [];
		foreach($arr as $item){
			$ss = substr($item,3);
			if(strlen($ss)>0) $rs[substr($item,0,2)] = $ss;
		}
		return $rs;
	}
	*/

	/*
  	function render_menu_lang(){
		$langs = [
			'th' 	=> 'ไทย',
			'en'	=> 'English',
		];
		$cl = $this->get_current_lang();
		$html = '';
		$html.= "<a cl='{$cl}'>".$langs[$cl]."</a>";
		$langs[$cl] = null;
		$html.= "<ul class='sub-menu'>";
		foreach($langs as $k=>$item){
			$html.= "<li class='menu-item'>";
			#$url = admin_url('admin-ajax.php').'?lang='.$k;
			$url = '?lang='.$k;
			$html.= "<a href='{$url}'>".$item."</a>";
			$html.= "</li>";
		}
		$html.= "</ul>";
		return $html;
	}
	*/

}


