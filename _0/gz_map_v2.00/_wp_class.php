<?php //die(__FILE__);
/*
v0.00 - 20220519:Tony:Add gz_highlight (a video and a simple post list by cat)
*/
class gz_map extends gz_tpl{
	public function __construct(){//die(__FILE__.__FUNCTION__);
		$config = [
			'enqueue'  => [
				['type'=>'style' ,'load'=>true ,'prm'=>['animate_css','https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.0.0/animate.min.css']],
				['type'=>'style' ,'load'=>false ,'prm'=>['leaflet_c','https://unpkg.com/leaflet@1.8.0/dist/leaflet.css'] ,'tags'=>['crossorigin'=>'dev00.surf-thailand.com' ,'integrity'=>'sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A==']],
				['type'=>'script' ,'load'=>false ,'prm'=>['leaflet_s','https://unpkg.com/leaflet@1.8.0/dist/leaflet.js'] ,'tags'=>['crossorigin'=>'dev00.surf-thailand.com' ,'integrity'=>'sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA==']],

				['type'=>'style' ,'load'=>true ,'prm'=>[__CLASS__,'[REL_PATH]wp_style.scss',['leaflet_c','animate_css']]],
				['type'=>'script' ,'load'=>true ,'prm'=>[__CLASS__,'[REL_PATH]_wp_script.js',['jquery-core','leaflet_s']]],
				['type'=>'localize', 'prm'=>[__CLASS__,__CLASS__,['ajax'=>admin_url('admin-ajax.php')]]],
			]
			,'shortcodes' => [
				['prm'=>['gz_map',[$this,'gz_map']]],
			]
			,'ajaxes' => [
				['prm'=>['get_pois',[$this,'get_pois']]]
				,['prm'=>['get_poi',[$this,'get_poi']]]
			]
			,'post_types'=>[
				'poi' => [
					'label'			=> 'Poi'
					,'description'	=> 'Point of interest'
					,'supports'		=> ['title' ,'editor' ,'thumbnail' ,'revisions']
					,'public'		=> true
					,'taxonomies'		=> ['category']
					//,'heirachical'	=> false
					//,'show_ui'		=> true
					//,'show_in_menu'	=> true
					//,'show_in_nav_menus'	=> true
					//,'show_in_admin_bar'	=> true
					//,'has_archive'	=> true
					//,'can-export'	=> true
					//,'exclude_from_search'	=> false
					//,'yarpp_support'	=> true
					//,'publicly_queryable'	=> true
					//,'capability_type'	=> 'post'
				]
			]
			,'cmb2' =>[
				'poi' => [
					'prefix'			=> 'info'
					,'cmb2_args'		=> [
						//'id'	=> '_smf_ticket'
						'title'			=> "General info"
						,'closed'		=> false
						,'object_types'	=> ['poi']
						//,'show_names'	=> true
						,'fields'	=> [
							['id'=>'cover_img' ,'name'=>'Cover image' ,'type'=>'file']
							,['id'=>'latlng' ,'name'=>'Location' ,'type'=>'pw_map']
							,['id'=>'line' ,'name'=>'Line' ,'type'=>'text_medium']
							,['id'=>'email' ,'name'=>'Email' ,'type'=>'text_email']
							,['id'=>'website' ,'name'=>'Website' ,'type'=>'text_url']
							,['id'=>'facebook' ,'name'=>'Facebook' ,'type'=>'text_url']
							,['id'=>'tel' ,'name'=>'Tel' ,'type'=>'text_medium']
						]
					]
				]
			]
		];
		parent::__construct($config); //ob_clean(); echo '<pre>'; print_r($config); die();
	}
	
	function gz_map($atts=[],$content=false){ //die('<pre>'.print_r(compact('html','atts'),true));
		$atts = shortcode_atts([
			'id'		=> isset($_GET['id'])		?$_GET['id']		:false,
			'class'		=> isset($_GET['class'])	?$_GET['class']		:'',
			'css'		=> isset($_GET['css'])		?$_GET['css']		:'',
			'poi_cat'	=> isset($_GET['poi_cat'])	?$_GET['poi_cat']	:'',
			'prms'		=> isset($_GET['prms'])		?$_GET['prms']		:false,
			'output'	=> isset($_GET['output'])	?$_GET['output']	:'html',
			'echo'		=> isset($_GET['echo'])		?$_GET['echo']		:false,
		],$atts);
		if(is_array($atts['prms'])) $atts = array_merge($atts,unserialize(urldecode($atts['prms'])));
		extract($atts, EXTR_PREFIX_ALL,'att');
		if($att_id) $id = "id='{$att_id}'"; else $id='';
		$html = "<div {$id} class='gz_map {$att_class}' data-poi_cat='{$att_poi_cat}' style='{$att_css}'>";
		$html.= "</div>";
		return $html;
	}


	function get_poi($atts=false ,$echo='html'){
		if($atts==false) $atts = $_REQUEST;
		$atts = shortcode_atts([
			'id' => false
		],$atts,'get_poi'); //print_r($atts);
		extract($atts,EXTR_PREFIX_ALL,'att');

		die($this->render_place_html($att_id));
	}

	function get_pois($atts ,$echo='json'){
		//die('[{"title":"Park Skateboard","lat":"13.804563645212726","lng":"100.8105301357544","data":{"id":8636}},{"title":"Nokhook Skate Park II","lat":"13.9699841","lng":"100.5522337","data":{"id":8634}},{"title":"Surflab","lat":"13.8297227","lng":"100.4867591","data":{"id":8632}}]');
		$poi_cat = isset($_GET['cat'])?$_GET['cat']:false;
		$atts = shortcode_atts([
			'post_type' 		=> 'poi'
			,'cat'				=> $poi_cat
			,'numberposts'		=> -1
		], $atts, 'gz_poi'); //die('<pre>'.print_r(compact('atts'),true));
		extract($atts,EXTR_PREFIX_ALL,'att');
		$args = ['post_type'=>$att_post_type ,'category_name'=>'windsurf' ,'numberposts'=>$att_numberposts];
		$rs = new WP_Query($args); //die('<pre>'.print_r(compact('args','posts'),true));
		$pois = [];
		foreach($rs->posts as $post){//die('<pre>'.print_r($post,true));
			$metas = get_post_meta($post->ID);
			if(isset($metas['infolatlng'][0])){
				$latlng = unserialize($metas['infolatlng'][0]); //die('<pre>'.print_r(compact('latlng','metas'),true));
				$poi = (object)[
					'title'	=> $post->post_title
					,'lat'	=> $latlng['latitude']
					,'lng'	=> $latlng['longitude']
					,'data'	=> [
						'id'	=> $post->ID
						//,'div_icon'	=> $this->render_div_icon($post,$metas)
					]
				];
				$pois[] = $poi;
			}
		}
		echo json_encode($pois);
		die();
	}
}
