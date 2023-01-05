<?php //die(__FILE__);
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
*/
class gz_multilang extends gz_tpl{
	public function __construct(){
		$config = [
			'cmb2v2' => [
				['prm'=>[
					'id'			=> 'en',
					'title' 		=> __('English','woocommerce'),
					'object_types'	=> ['product'],
					'context'		=> 'normal',
					'fields' => [
						['id'=>'_post_title_en' ,'name'=>__('Title (English)','woocommerce') ,'type'=>'text'],
						['id'=>'_post_sub_th' ,'name'=>__('Sub title (Thai)','woocommerce') ,'type'=>'textarea_small'],
						['id'=>'_post_sub_en' ,'name'=>__('Sub title (English)','woocommerce') ,'type'=>'textarea_small'],
						['id'=>'_post_content_en' ,'name'=>__('Product description (English)','woocommerce') ,'type'=>'wysiwyg'],
						['id'=>'_post_excerpt_en' ,'name'=>__('Product short description (English)','woocommerce') ,'type'=>'wysiwyg'],
					]
				]],
			],
  		   	'ajaxes' => [
				['prm'=>['set_lang',[$this,'set_lang']]]
			],
			'filters' => [
				//For displaying content in multi language
				['prm'=>['the_title',[$this,'the_title'],10,2]],
				['prm'=>['get_the_excerpt',[$this,'get_the_excerpt'],10,2]],
				['prm'=>['the_content',[$this,'the_content'],10,1]],
				//For language switching
				['prm'=>['determine_locale',[$this,'determine_locale']]],
				//Old
				//['prm'=>['woocommerce_get_breadcrumb',[$this,'woocommerce_get_breadcrumb'],10,2]],
				//['prm'=>['woocommerce_before_main_content',[$this,'woocommerce_breadcrumb'],20,2]],
				//['prm'=>['woocommerce_before_main_content',[$this,'woocommerce_breadcrumb'],20]],
			],
			'xremove_actions' => [
				//['prm'=>['woocommerce_shop_loop_item_title','woocommerce_template_loop_product_title',10]],
				//['prm'=>['woocommerce_single_product_summary','woocommerce_template_single_title',5]],
				//['prm'=>['woocommerce_before_main_content','woocommerce_breadcrumb',20]],
			],
			'xactions' => [
				//['prm'=>['woocommerce_shop_loop_item_title',[$this,'woocommerce_shop_loop_item_title'],10]],
				//['prm'=>['woocommerce_single_product_summary',[$this,'woocommerce_single_product_summary'],5]],
			],
		];
		parent::__construct($config);
	}

	/*
	 * 	/wp/wp-admin/admin-ajax.php
	*/
	function set_lang(){
		$lang = isset($_GET['lang'])?$_GET['lang']:'th';
		echo setcookie('gz_lang',$lang).','.$_COOKIE['gz_lang'];
		die(0);
	}

	/*
	* Setup language base on user's preference.
	*	?lang=xx > cookie:gz_lang
	*/
	function determine_locale(){
		//return 'en_US';
		$lc = [
			'th'	=> 'th_TH',
			'en'	=> 'en_US',
		];
		$lang = isset($_GET['lang'])?$_GET['lang']:false;
		if(!$lang) $lang = isset($_COOKIE['gz_lang'])?$_COOKIE['gz_lang']:false;
		$locale = isset($lc[$lang])?$lc[$lang]:$lc['th'];
		if(isset($_GET['show_lang'])) die($locale);
		if(isset($_GET['show_lang_cookie'])) die($_COOKIE['gz_lang']);
		return $locale;
	}

	/*
	* If there's meta _post_title_en then return it, otherwise return the original title;
	* https://developer.wordpress.org/reference/functions/get_the_title/
	*/
	function the_title($title,$post_id){
		if($new_title=get_post_meta($post_id,'_post_title_'.$this->get_current_lang(),true)) $title = $new_title;
		return $title;
	}

	/*
	* If there's meta _post_excerpt_en then return it, otherwise return the original title;
	* https://developer.wordpress.org/reference/functions/get_the_excerpt/
	*/
	function get_the_excerpt($excerpt,$post){
		if($new_excerpt=get_post_meta($post->ID,'_post_excerpt_'.$this->get_current_lang(),true)) return $new_excerpt;
		else return $excerpt;
	}

	/*
	* If there's meta _post_content_en then return it, otherwise return the original title;
	* https://developer.wordpress.org/reference/functions/the_content/
	*/
	function the_content($content){
		global $post;
		if($new_content=get_post_meta($post->ID,'_post_content_'.$this->get_current_lang(),true)) return $new_content;
		else return $content;
	}

	function woocommerce_get_breadcrumb($crumbs,$breadcrumb){
		$lang = $this->get_current_lang();
		foreach($crumbs as &$item){
			$arr = $this->split_langs($item[0]);
			$item[0] = isset($arr[$lang])?$arr[$lang]:$item[0];
		}
		return $crumbs;
		//die('<pre>'.print_r($lang,true).print_r($arr,true).print_r($crumbs,true).print_r($breadcrumb,true));
	}

	function woocommerce_breadcrumb(){
		global $crumbs,$breadcrumb;
		die('<pre>'.print_r($crumbs,true).print_r($breadcrumb,true));
		//echo "Breadcrumb";
	}

	function split_langs($st,$langs=['th','en']){
		$arr = explode('[:',$st);
		$rs = [];
		foreach($arr as $item){
			$ss = substr($item,3);
			if(strlen($ss)>0) $rs[substr($item,0,2)] = $ss;
		}
		return $rs;
	}

	function get_product_title(){
		//global $product; ob_clean(); die('<pre>'.print_r($product,true));
		//return get_meta($product->id,'name_th',get_the_title( $post:integer|WP_Post ))
		//$txt = preg_split("/[(]/",get_the_title());
		$titles = $this->split_langs(get_the_title()); //ob_clean(); die('<pre>'.print_r($titles,true));
		$lang = $this->get_current_lang();
		return isset($titles[$lang])?$titles[$lang]:get_the_title();
	}

	//Get specific language from Valexar string
	function valexar_text_lang($txt,$lang,$default){
		$ts = $this->split_langs($txt); //ob_clean(); die('<pre>'.print_r($titles,true));
		return isset($ts[$lang])?$ts[$lang]:$default;
	}

	function woocommerce_shop_loop_item_title(){
		echo $this->get_product_title();
	}

	function woocommerce_single_product_summary(){
		echo $this->get_product_title();
	}

	//
	//Support functions
	//
	function get_current_lang(){return substr(get_bloginfo('language'),0,2);}

	function get_field_lang($fn,$lang,$default=''){
		global $product;
		if($val=get_post_meta($product->id,$fn.'_'.$lang)) return $val; else return $default;
	}

}