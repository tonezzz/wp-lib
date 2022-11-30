<?php //die(__FILE__);
/*
v0.00 - 20220519:Tony:Add gz_highlight (a video and a simple post list by cat)
*/
class gz_shortcodes extends gz_tpl{
	public function __construct(){//die(__FILE__.__FUNCTION__);
		$config = [
			'enqueue'  => [
				//['type'=>'style' ,'prm'=>['jquery-ui','//code.jquery.com/ui/1.13.1/themes/base/jquery-ui.css',]],
				['type'=>'style' ,'load'=>true ,'prm'=>[__CLASS__,'[REL_PATH]wp_style.scss']],
				['type'=>'script' ,'load'=>true ,'prm'=>[__CLASS__,'[REL_PATH]_wp_script.js',['jquery-core','jquery-ui-core','jquery-ui-tabs']]],
				//['type'=>'localize', 'prm'=>[__CLASS__,__CLASS__,['ajax_url'=>admin_url('admin-ajax.php')]]],
			]
			,'shortcodes' => [
				['prm'=>['gz_highlight',[$this,'gz_highlight']]],
				['prm'=>['gz_posts',[$this,'gz_posts']]],
			],
		];
		parent::__construct($config); //ob_clean(); echo '<pre>'; print_r($config); die();
	}
	
	function gz_posts($atts=[],$content=false){ //die('<pre>'.print_r(compact('html','atts'),true));
		$atts = shortcode_atts([
			'row'		=> isset($_GET['row'])		?$_GET['row'] 		:2,
			'col'		=> isset($_GET['col'])		?$_GET['col'] 		:4,
			'padding'	=> isset($_GET['padding'])	?$_GET['padding']	:'10% 5% 20% 5%',
			'video'		=> isset($_GET['video'])	?$_GET['video']		:false,
			'img'		=> isset($_GET['img'])		?$_GET['img']		:false,
			'posts_cat'	=> isset($_GET['posts_cat'])?$_GET['posts_cat']	:false,
			'max' 		=> isset($_GET['max'])		?$_GET['max']		:7,
			'header'	=> isset($_GET['header'])	?$_GET['header']	:false,
			'button'	=> isset($_GET['button'])	?$_GET['button']	:'LEARN MORE',
			'url' 		=> isset($_GET['url'])		?$_GET['url']		:'/learn-more',
			'prms'		=> isset($_GET['prms'])		?$_GET['prms']		:false,
			'output'	=> isset($_GET['output'])	?$_GET['output']	:'html',
			'echo'		=> isset($_GET['echo'])		?$_GET['echo']		:false,
		],$atts);
		if(is_array($atts['prms'])) $atts = array_merge($atts,unserialize(urldecode($atts['prms'])));
		extract($atts, EXTR_PREFIX_ALL,'att'); //die('<pre>'.print_r(compact('atts'),true).'</pre>');
		$html = '';
		$html.= $this->render_post_list([
			//'header'		=> $att_header,
			'cat'			=> $att_posts_cat,
			'wrapper_class' => 'gz_posts',
			'max'			=> $att_max,
			'row'			=> $att_row,
			'col'			=> $att_col,
		]);
		return $html;
	}
	
	function gz_highlight($atts=[],$content=false){ //die('<pre>'.print_r(compact('html','atts'),true));
		$atts = shortcode_atts([
			'padding'	=> isset($_GET['padding'])	?$_GET['padding']	:'0',
			'height'	=> isset($_GET['height'])	?$_GET['height']	:'auto',
			'video'		=> isset($_GET['video'])	?$_GET['video']		:false,
			'img'		=> isset($_GET['img'])		?$_GET['img']		:false,
			'img_fit'	=> isset($_GET['img_fit'])	?$_GET['img_fit']	:'cover',
			'img_css'	=> isset($_GET['img_css'])	?$_GET['img_css']	:'',
			'header'	=> isset($_GET['header'])	?$_GET['header']	:false,
			'button'	=> isset($_GET['button'])	?$_GET['button']	:false, //'LEARN MORE',
			'url' 		=> isset($_GET['url'])		?$_GET['url']		:'/learn-more',
			'prms'		=> isset($_GET['prms'])		?$_GET['prms']		:false,
			'max' 		=> isset($_GET['max'])		?$_GET['max']		:4,
			'overlay_style'	=>isset($_GET['overlay_style'])	?$_GET['overlay_style']	:'flex',
			'posts_cat'	 	=> isset($_GET['posts_cat'])	?$_GET['posts_cat']		:false,
			'posts_style'	 => isset($_GET['posts_style'])	?$_GET['posts_style']	:'',
			'posts_class'=>isset($_GET['posts_class'])	?$_GET['posts_class']	:'row',
			'output'	=> isset($_GET['output'])	?$_GET['output']	:'html',
			'echo'		=> isset($_GET['echo'])		?$_GET['echo']		:false,
		],$atts);
		if(is_array($atts['prms'])) $atts = array_merge($atts,unserialize(urldecode($atts['prms'])));
		extract($atts, EXTR_PREFIX_ALL,'att'); //die('<pre>'.print_r(compact('atts'),true).'</pre>');
		$html = '';
		$html.= "<div class='gz_highlight' style='height:{$att_height};padding:{$att_padding};'>";
			if($att_img) $html.= "<img src='{$att_img}' style='{$att_img_css};'>";
			//if($att_img) $html.= "<img src='{$att_img}' style='object-fit:{$att_img_fit};'>";
			if($att_video){
				$html.= "<video autoplay loop muted playbackRate='.5' >";
					$html.= "<source src='{$att_video}' type='video/mp4'>Your browser doesn not support HTML5 video.";
				$html.= "</video>";
			}
			$html.= "<div class='overlay' style='{$att_overlay_style}'>";
				if($att_header) $html.= "<h2 class='header'>{$att_header}</h2>";
				if($content)    $html.= "<span class='content'>{$content}</span>";
				if($att_button) $html.= "<span><a class='button' href='{$att_url}'>{$att_button}</a></span>";
			$html.= "</div>";
			if($att_posts_cat) $html.= $this->render_post_list([
				'cat'			=> $att_posts_cat,
				'max'			=> $att_max,
				'wrapper_class'	=> "posts {$att_posts_class}",
				'wrapper_style'	=> "{$att_posts_style}",
			]);
		$html.= "</div>"; //die('<pre>'); //die('<pre>'.print_r(compact('html','atts'),true));
		return $html;
	}
	
	function render_post_list($atts=[]){
		$atts = shortcode_atts([
			'wrapper_class'		=> isset($_GET['wrapper_class'])		?$_GET['wrapper_class'] 	:false,
			'wrapper_style'		=> isset($_GET['wrapper_style'])		?$_GET['wrapper_style'] 	:'',
			'row'		=> isset($_GET['row'])		?$_GET['row'] 		:1,
			'col'		=> isset($_GET['col'])		?$_GET['col'] 		:4,
			'cat'		=> isset($_GET['button'])	?$_GET['button'] 	:false,
			'max' 		=> isset($_GET['max'])		?$_GET['max']		:4,
			'prms'		=> isset($_GET['prms'])		?$_GET['prms']		:false,
			'output'	=> isset($_GET['output'])	?$_GET['output']	:'html',
			'echo'		=> isset($_GET['echo'])		?$_GET['echo']		:false,
		],$atts);
		if(is_array($atts['prms'])) $atts = array_merge($atts,unserialize(urldecode($atts['prms'])));
		extract($atts, EXTR_PREFIX_ALL,'att'); if(!$att_cat) return '';
		$posts = get_posts(['numberposts'=>$att_max ,'category_name'=>$att_cat]); if(empty($posts)) return '';
		//if($atts['cat']=='expertise') die('<pre>'.print_r(compact('posts'),true).'</pre>');
		$html = '';
		foreach($posts as $post){
			$img = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'single-post-thumbnail' ); //die('<pre>'.print_r(compact('img'),true).'</pre>');
			$style = "width:calc(100% / {$att_col}); height:calc(100% / {$att_row});";
			$html.= "<a class='post' style='{$style}' href='/'>";
				$html.= "<img src='$img[0]'>";
			$html.= "</a>";
		}
		if($att_wrapper_class) $html = "<div class='{$att_wrapper_class}' style='{$att_wrapper_style}'>{$html}</div>"; //Wrap it
		return $html;
	}

}
