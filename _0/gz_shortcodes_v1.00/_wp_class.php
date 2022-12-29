<?php //die(__FILE__);
/*
v0.00 - 20220519:Tony:Add gz_highlight (a video and a simple post list by cat)
*/
class cn_site extends gz_tpl{
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
			],
		];
		parent::__construct($config); //ob_clean(); echo '<pre>'; print_r($config); die();
	}
	
	function gz_highlight($atts=[],$content=false){ //die('<pre>'.print_r(compact('html','atts'),true));
		$atts = shortcode_atts([
			'padding'	=> isset($_GET['padding'])	?$_GET['padding']	:'10% 5% 20% 5%',
			'video'		=> isset($_GET['video'])	?$_GET['video']		:false,
			'img'		=> isset($_GET['img'])		?$_GET['img']		:false,
			'post_cat'	=> isset($_GET['post_cat'])	?$_GET['post_cat']	:false,
			'header'	=> isset($_GET['header'])	?$_GET['header']	:'Header',
			'button'	=> isset($_GET['button'])	?$_GET['button']	:'LEARN MORE',
			'url' 		=> isset($_GET['url'])		?$_GET['url']		:'/learn-more',
			'prms'		=> isset($_GET['prms'])		?$_GET['prms']		:false,
			'output'	=> isset($_GET['output'])	?$_GET['output']	:'html',
			'echo'		=> isset($_GET['echo'])		?$_GET['echo']		:false,
		],$atts);
		if(is_array($atts['prms'])) $atts = array_merge($atts,unserialize(urldecode($atts['prms'])));
		extract($atts, EXTR_PREFIX_ALL,'att'); //die('<pre>'.print_r(compact('atts'),true).'</pre>');
		$html = '';
		$html.= "<div class='gz_highlight'>";
			if($att_img) $html.= "<img src='{$att_img}'>";
			if($att_video){
				$html.= "<video autoplay loop muted playbackRate='.5' >";
					$html.= "<source src='{$att_video}' type='video/mp4'>Your browser doesn not support HTML5 video.";
				$html.= "</video>";
			}
			$html.= "<div class='overlay' style='padding:{$att_padding};'>";
				$html.= "<h2 class='header'>{$att_header}</h2>";
				if($content) $html.= "<span class='content'>{$content}</span>";
				$html.= "<span><a class='button' href='{$att_url}'>{$att_button}</a></span>";
			$html.= "</div>";
			if($att_post_cat){
				$html.= "<div class='posts'>";
				$html.= $this->render_post_list(['cat'=>$att_post_cat]);
				$html.= "</div>";
			}
		$html.= "</div>"; //die('<pre>'); //die('<pre>'.print_r(compact('html','atts'),true));
		return $html;
	}
	
	function render_post_list($atts=[]){
		$atts = shortcode_atts([
			'cat'		=> isset($_GET['button'])	?$_GET['button'] 	:'home-highlight',
			'max' 		=> isset($_GET['max'])		?$_GET['max']		:4,
			'prms'		=> isset($_GET['prms'])		?$_GET['prms']		:false,
			'output'	=> isset($_GET['output'])	?$_GET['output']	:'html',
			'echo'		=> isset($_GET['echo'])		?$_GET['echo']		:false,
		],$atts);
		if(is_array($atts['prms'])) $atts = array_merge($atts,unserialize(urldecode($atts['prms'])));
		extract($atts, EXTR_PREFIX_ALL,'att');
		$posts = get_posts(['numberposts'=>$att_max ,'category_name'=>$att_cat]);
		$html = '';
		foreach($posts as $post){
			$img = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'single-post-thumbnail' ); //die('<pre>'.print_r(compact('img'),true).'</pre>');
			$html.= "<div class='post' style='background-image:url({$img[0]})'>";
			$html.= "</div>";
		}
		return $html;
	}

}
