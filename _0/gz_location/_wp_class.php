<?php //die(__FILE__); 
/* poi
 v2.01:20180417:Tony
	- Fix "global $post;" in WooCommerce shop page.
 v2.00:20170206:Tony
	- Extend gz_tpl for better DOM rendering command.
 v1.03:20170526:Tony
	- Remove col_2 column and expand col_1 to cover the space

 * Source: http://www.remicorson.com/woocommerce-list-products-by-attribute-with-multiple-values/
 * Plugin Name: WooCommerce - List Products by Multiple Categories, Attributes, Etc.
 * Description: [gz_products attrs='color(red,black):brand(adidas,asics)' cats='a,b,c' tags='a,b,c']
 * Version: 1.0
 */
 class gz_location extends gz_tpl{
	//protected $cmb2;
	//protected $post, $metas;
	static $icons;
	
	public function __construct(){
		$config = [
			'image_sizes'	=> ['gmap_marker'=>[20,32,true]]
			,'enqueue'  => [//@import './fonts/css/fontello.css';
				['type'=>'script' ,'load'=>true ,'prm'=>[__CLASS__,'[REL_PATH]_wp_script.js',['jquery-core']]]
				,['type'=>'localize', 'prm'=>[__CLASS__,__CLASS__,['get_markers'=>admin_url('admin-ajax.php')]]]
				//,['type'=>'style' ,'prm'=>[__CLASS__,'[REL_PATH]_wp_style.scss'],['jquery_slick']]
			]
			,'post_types'=>[
				'location'=>[
					'labels'	=> ['name'=>'Locations']
					,'public'	=> true
					,'menu_position'	=> null
					,'taxonomies'	=> ['category']
				]
			]
			,'cmb2' => [
				'location'=>[
					'prefix'			=> '_stv3_location_'
					,'cmb2_args'		=> [
						'title'         => __('Location' ,'cmb2')
						,'object_types' => ['location','page']
						//,'show_on' 	=> array( 'key' => 'page-template', 'value' => array('page-sbw2017-section-map.php'))
						,'closed'     	=> true // Keep the metabox closed by default
						,'fields'		=> [
							//['id'=>'group' ,'type'=>'group' ,'options'=>['sortable'=>true] //, 'description'=>'Icons'
							//	,'fields' => [
							//		['id'=>'group_name' ,'name'=>'Group name' ,'type'=>'text_small' ,'default'=>'default']
							//		,['id'=>'img' ,'name'=>"Gallery images", 'type'=>'file_list']
							//	]
							//]
							['id'=>'icon_name','name'=>'Icon','type'=>'select','options'=>[
								'townhouse' => 'Townhouse'
								,'condo'	=> 'Condominium'
								,'house'	=> 'House'
								,'house_twin'=> 'Twin House'
							]]
							,['id'=>'desc_short' ,'name'=>'Short description', 'type'=>'textarea']
							,['id'=>'loc','name'=>'Location','type'=>'pw_map']
						]
					] //,'_debug'		=> true
				]
			]
		];
		parent::__construct($config); //init_shortcodes
		//$this->rel_path = substr(dirname(__FILE__),strlen(ABSPATH)-1); $this->rel_path = str_replace(array('/','\\'),'/',$this->rel_path); //die($rel_path);
		//add_action('cmb2_admin_init',array($this,'init_cmb'));
		//add_shortcode("gz_ad_panel",array($this,'render_shortcode'));
		//add_action('wp_enqueue_scripts',array($this,'register_scripts'));
		add_action('wp_ajax_nopriv_'.'get_markers',array($this,'get_markers'));
		add_action('wp_ajax_'.'get_markers',array($this,'get_markers'));
		self::init_icons();
	}

	static function init_icons(){
		self::$icons = [
			'townhouse' => ['id'=>120] //'/v3/wp/wp-content/uploads/2017/08/logo_surfthai_150.png'
			,'condo'	=> ['id'=>120] //'/v3/wp/wp-content/uploads/2017/08/logo_surfthai_150.png'
			,'house'	=> ['id'=>120] //'/v3/wp/wp-content/uploads/2017/08/logo_surfthai_150.png'
			,'house_twin'=> ['id'=>120] //'/v3/wp/wp-content/uploads/2017/08/logo_surfthai_150.png'
		];
	}
	 
	static function get_markers_0(){
		$poi1 = "{position:[13.9,100.8]}";
		$pois = [$poi1];
		//if($prm_markers){
		//	$attr['data-markers'] = htmlspecialchars($prm_markers,ENT_QUOTES);
		//}
		$pois_st = '['.implode(',',$pois).']';
		echo $pois_st;
		die();
	}

	static function get_markers(){
		/*
		$test_icon = [
			'path' 		=> "M-20,0a20,20 0 1,0 40,0a20,20 0 1,0 -40,0"
			,'fillColor' => '#FF0000'
			,'fillOpacity'=> .6
			,'anchor' 	=> [0,0] //new google.maps.Point(0,0)
			,'strokeWeight'=> 0
			,'scale' 	=> 1
		];
		*/
		$posts = get_posts([
			'number_posts'	=> 10
			,'post_type'	=> 'location'
			,'post_status'	=> 'publish'
			//,'category_name'=> 'surf-club'
		]); //ob_clean(); echo '<pre>'; print_r($posts); die();
		if(is_array($posts)){
			$pois = [];
			//$label = new \stdClass; $label->fontFamily = 'Fontawesome'; $label->text = '\xf299';
			$pre = '_stv3_location_';
			$label = [];
			foreach($posts as $post){
				$metas = get_post_meta($post->ID); //ob_clean(); echo '<pre>'; print_r($metas); die();
				if(isset($metas[$pre.'loc'][0])){
					$loc_st = $metas[$pre.'loc'][0];
					$loc = unserialize($loc_st); //ob_clean(); echo '<pre>'; print_r($loc); die();
					if(isset($loc['latitude'])&&isset($loc['longitude'])){
						$poi = new \stdClass;
						$latlng = [$loc['latitude'],$loc['longitude']]; $poi->position = $latlng;
						//if(isset($metas['name'][0])) $poi->label = do_shortcode('[wp-svg-icons icon="home-3" wrap="i"]').$metas['name'][0];
						//if(isset($metas['name'][0])) $poi->title = $metas['name'][0];
						//if(isset($metas['name'][0])) $poi->label.text = $post->post_title;
						$label['text'] = $post->post_title;
						$poi->label = $label;
						//$poi->label = $label;
						//$poi->icon = "https://maps.google.com/mapfiles/kml/shapes/library_maps.png";
						//$poi->icon = ['type'=>'google.maps.SymbolPath','name'=>'CIRCLE','scale'=>10];
						//$poi->icon = ['type'=>'google.maps.SymbolPath','name'=>'CIRCLE','scale'=>10];
						if(!empty($metas[$pre.'icon_name']) && !empty($metas[$pre.'icon_name'][0])){
							$icon_name = $metas[$pre.'icon_name'][0];
							if(isset(self::$icons[$icon_name])){
								$icon = self::$icons[$icon_name];
								//$img = wp_get_attachment_image_src($icon['id'],'gmap_marker'); ob_clean(); echo '<pre>'; print_r($img); die();
								//$img_url = $img[0];
								//ob_clean(); echo '<pre>'; print_r(compact('icon_name','icon_id')); die();
								//if($img_url) $poi->icon = $img_url;
								//$poi->icon = $test_icon;
							}
						}
						$pois[] = $poi;
					}
					//$poi->icon = 
				}
			}
			$pois_st = json_encode($pois,JSON_UNESCAPED_UNICODE);
			//ob_clean(); echo '<pre>'; print_r($pois_st); die();
		} else $pois_st = '[]';
		echo $pois_st;
		die();
	}
}
 
 //global $gz_ad_panel; $gz_ad_panel = new gz_ad_panel();