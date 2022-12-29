<?php //die(__FILE__);
/*
* v0.00 - 201700510:Tony
* 	- Using menu designed by KK
* 
* https://stackoverflow.com/questions/26660728/embed-activity-feed-of-a-public-facebook-page-without-forcing-user-to-login-allo
* https://graph.facebook.com/oauth/access_token?client_id=" + APP_ID + "&client_secret=" + APP_SECRET + "&grant_type=client_credentials
* https://graph.facebook.com/oauth/access_token?client_id=291680341300463&client_secret=92ae93207bc2c8f186413d02d0b29db7&grant_type=client_credentials
*/
class gz_zoom_mask extends gz_tpl{
	private $transient_prefix = __CLASS__;
	//private $id = __CLASS__;
	//private $url;			//Using relative url here
	
	public function __construct(){//die(__FILE__.__FUNCTION__);
		parent::__construct();
		$this->get_url(__FILE__);
		$this->set_id(__CLASS__);
		$this->init_scripts();
		$this->init_shortcode();
	}
	
	function init_shortcode(){//die(__FILE__.__FUNCTION__);
		add_shortcode('gz_zoom_mask',[$this,'render_cartoon']);
		//add_shortcode('kk_cartoon',array($this,'render_cartoon'));
		//$post = get_post(16); ob_clean(); var_dump($post);
		//add_filter('the_content',[$this,'test_content']);
	}
	
	function get_fb_album($args){//ob_clean(); echo '<pre>'; var_dump($args); die();
		$nocache = isset($_GET['nocache']); //ob_clean(); print_r($nocache); die();
		if(func_num_args()>0) {$prm = func_get_arg(0); extract($prm);} //ob_clean(); print_r($use_cache); die();
		$fb_album_url = $fb_api_url.$fb_album_id;
		$url = add_query_arg(array(
			'access_token'	=> $fb_access_token
			//,'fields'		=> 'name,description'
			,'fields'		=> $fb_fields
		),$fb_album_url); //if(isset($_GET['d'])){ob_clean(); print_r($url); die();}
		//$key = md5($url);
		//$key = $this->transient_prefix.md5($url);
		$key = $this->transient_prefix.'_'.md5($url);
		if($nocache||(false===($rs=get_transient($key)))){
			$rs = file_get_contents($url); //if(isset($_GET['d'])){ob_clean(); var_dump($rs); die();}
			$data = json_decode($rs); //if(isset($_GET['d'])){ob_clean(); echo '<pre>'; var_dump($data); die();}
			//$album = array();
			//foreach($data->data as $item) if($img=$this->get_image($item)) $album[$item->id] = $img;
			//foreach($data->data as $item) if($img=$this->get_image_2($item)) $album[$item->id] = $img;
			$album = $data;
			set_transient($key,$album,60*60);
		}else{
			$album = $rs; //$SYS3->_debug('f',true,true,$rs);
		} //if(isset($_GET['d'])){ob_clean(); var_dump($album); die();}
		return $album;
	}
	
	private function load_fb_images(){
		$nocache = isset($_GET['nocache']); //ob_clean(); print_r($nocache); die();
		if(func_num_args()>0) {$prm = func_get_arg(0); extract($prm);} //ob_clean(); print_r($use_cache); die();
		$fb_gallery_url = $fb_api_url.$fb_album_id.'/photos';
		$url = add_query_arg(array(
			'access_token'	=> $fb_access_token
			,'fields'		=> $fb_fields
		),$fb_gallery_url); //if(isset($_GET['d'])){ob_clean(); print_r($url); die();}
		$key = $this->transient_prefix.'_'.md5($url);
		if($nocache||(false===($rs=get_transient($key)))){
			//$url = urlencode($url); 			if(isset($_GET['d'])){ob_clean(); var_dump($url); die();}
			//$url = str_replace('//','/',$url);  if(isset($_GET['d'])){ob_clean(); var_dump($url); die();}
			//$rs = file_get_contents(urlencode($url)); if(isset($_GET['d'])){ob_clean(); var_dump($rs); die();}
			$rs = file_get_contents($url); //if(isset($_GET['d'])){ob_clean(); var_dump($rs); die();}
			$data = json_decode($rs); //$SYS3->_debug('d',true,true,$data);
			$images = array();
			//foreach($data->data as $item) if($img=$this->get_image($item)) $images[$item->id] = $img;
			foreach($data->data as $item) if($img=$this->get_image_2($item)) $images[$item->id] = $img;
			set_transient($key,$images,60*60);
		}else{
			$images = $rs; //$SYS3->_debug('f',true,true,$rs);
		} //if($this->post->ID==53)$this->SYS3->_debug('d',true,true,$images);
		return $images;
	}

	//Return image url
	public function get_image($data){
		//eval(MISC_ALL_GLOBAL); //$SYS3->_debug('d',true,true,$data);
		if(isset($data->images)){
			$sel_img = (object)['source'=>'' ,'width'=>640 ,'height'=>480];
			foreach($data->images as $image) if($image->width>=$sel_img->width && $image->height>=$sel_img->height) $sel_img = $image;
			$img_url = $sel_img->source;
		} //$SYS3->_debug('d',true,true,$sel_img);
		if(empty($img_url)){
			if(!empty($data->source)) $img_url = $data->source;
			elseif(!empty($data->picture)) $img_url = $data->picture;
		}
		return empty($img_url)?false:$img_url;
	}


	//Return array of (url, width, height)
	public function get_image_2($data){
		if(isset($data->images)){
			$sel_img = (object)['source'=>'' ,'width'=>0 ,'height'=>0]; //Minimum width & height
			foreach($data->images as $image) if($image->width>=$sel_img->width && $image->height>=$sel_img->height) $sel_img = $image;
			$img = (object)['width'=>$sel_img->width ,'height'=>$sel_img->height ,'url'=>$sel_img->source]; //ob_clean(); echo '<pre>'; var_dump($img); die();
		} //$SYS3->_debug('d',true,true,$sel_img);
		if(empty($img_url)){
			if(!empty($data->source)) $img = (object)['url'=>$data->source];
			elseif(!empty($data->picture)) $img = (object)['url'=>$data->picture];
		}
		return empty($img)?false:$img;
	}

	function render_cartoon($atts){//die(__FILE__.__FUNCTION__);
		global $kk_control;
		$atts = shortcode_atts([
			'fb_api_url'		=> 'https://graph.facebook.com/v2.10/'
			,'fb_access_token'	=> '291680341300463|2egpDD04iBAsHeYfWmdq70-_K_U'
			,'fb_fields'		=> 'images.order(reverse_chronological)'
			,'fb_album_id'		=> '476208095751555' //KK Cartoon
			//,'fb_album_id'	=> '371660582886332' //KK Folio
			,'mask_image_id'	=> 66
			,'img_width'		=> '100%'
			,'img_height'		=> '100%'
			,'img_margin'		=> 0
		],$atts,'kk_cartoon'); //ob_clean(); echo '<pre>'; var_dump($atts); die();
		extract($atts);
		$content = '';
		$content.= "<div class='gz_zoom_mask'>";
			$content.= "<div class='gz_container'>";
				$content.= $this->render_content(['style'=>"width:{$img_width}; height:{$img_height}; margin:{$img_margin}"]);
			$content.= "</div>";
			$url = wp_get_attachment_url($atts['mask_image_id']); //ob_clean(); die($url);
				$bg = "background-image:url({$url});";
				$style = "style={$bg}";
			$content.= "<div class='gz_mask' {$style}></div>";
		$content.= "</div>";
		//Move to below so I can add tabs
		//$content.= $kk_control->render(['obj'=>'.gz_zoom_mask', 'items'=>'.gz_img_list']);

		$atts['nocache'] = true;
		$fb_album_id = $atts['fb_album_id'];
		if(strpos($fb_album_id,',')===false){
			//For single album
			$control_prm = ['obj'=>'.gz_zoom_mask', 'items'=>'.gz_img_list'];
			$content.= $kk_control->render($control_prm);
			$images = $this->load_fb_images($atts); //ob_clean(); echo "<pre>"; print_r($images); die();
			$content.= "<div class='gz_img_list'>";
				$content.= "<ul class='items'>";
				foreach($images as $img){
					//$bg = "background-image:url({$img->url});";
					$bg = "background-image:url({$img->url})";
					$bg.= ",url(/images/preload.gif);"; //Preload background
					$img_info = json_encode($img);
					$content.="<li class='item' style='{$bg}' data-img='{$img_info}'>";
					$content.="</li>";
				}
				$content.= "</ul>";
			$content.= "</div>";
		}else{//For multi album
			$control_prm = ['obj'=>'.gz_zoom_mask' ,'items'=>'.gz_img_list' ,'tabs'=>'gz_img_tab'];
			$albums = explode(',',$atts['fb_album_id']); //ob_clean(); echo "<pre>"; var_dump($albums); die();
			$html_tab = '';
			$html_content = '';
			$css_label = '';
			foreach($albums as $album_id){
				$album = $this->get_fb_album([
					'fb_api_url' 		=> $atts['fb_api_url']
					,'fb_access_token' 	=> $atts['fb_access_token']
					,'fb_album_id' 		=> $album_id
					,'fb_fields'		=> 'name,description'
				]); //ob_clean(); echo "<pre>"; var_dump($album); die();
				$album_title = $album->name; $album_title_slug = sanitize_title($album_title);
				//Text labels
				//$html_tab.= "<li><a href='#{$album_title_slug}'>{$album_title}</a></li>";
				//Img labels
				$html_tab.= "<li><a href='#{$album_title_slug}'><span class='tab_label {$album_title_slug}'></span></a></li>";
				$fn = "/images/btn_ports/btn_{$album_title_slug}";
				$css_label.= ".tab_label.{$album_title_slug} {background-image:url({$fn}_off.png)}\n";
				$css_label.= ".tab_label.{$album_title_slug}:hover {background-image:url({$fn}_on.png)}\n";
				//
				$html_content.= "<div id='{$album_title_slug}' class='gz_img_list'>";
				$images = $this->load_fb_images([
					'fb_api_url' 		=> $atts['fb_api_url']
					,'fb_access_token' 	=> $atts['fb_access_token']
					,'fb_album_id' 		=> $album_id
					,'fb_fields'		=> 'images.order(reverse_chronological)'
				]); //ob_clean(); echo "<pre>"; print_r($images); die();
					$html_content.= "<ul class='items'>";
					foreach($images as $img){
						//$bg = "background-image:url({$img->url});";
						$bg = "background-image:url({$img->url})";
						$bg.= ",url(/images/preload.gif);"; //Preload background
						$img_info = json_encode($img);
						$html_content.="<li class='item' style='{$bg}' data-img='{$img_info}'>";
						$html_content.="</li>";
					}
					$html_content.= "</ul>";
				$html_content.= "</div>";
			}
			$content.= $kk_control->render($control_prm);
			$content.= "<div class='gz_img_tab'>";
			$content.= "<ul>{$html_tab}</ul>";
			$content.= "{$html_content}";
			$content.="</div>";
			echo "\n<style>/*label_style*/\n{$css_label}</style>\n";
		}
		return $content;
	}
	
	function render_content($prm){ //ob_clean(); echo "<pre>"; var_dump($prm); die();
		extract($prm);
		$res = '';
		//$res.= "<img class='item' src='/images/home/cartoon01.jpg'/>";
		//$res.= "<div class='item gz_zoom_bg' data-control='.gz_control' data-mask='.gz_mask' style='background-image:url(/images/home/cartoon01.jpg);'></div>";
		$res.= "<div class='item gz_zoom_bg' data-control='.kk_control' data-mask='.gz_mask' style='{$style}'></div>";
		return $res;
	}
	
	function init_scripts(){
		//add_action('admin_enqueue_scripts',array($this,'register_scripts_admin'));
		//add_action('admin_enqueue_scripts',array($this,'enqueue_scripts_admin'));
		add_action('wp_enqueue_scripts',array($this,'register_scripts'));
		add_action('wp_enqueue_scripts',array($this,'enqueue_scripts'));
	}

	public function register_scripts_admin(){
		//wp_register_script($this->id,$url.'_wp_admin_script.js',array('jquery-core'));
		//wp_register_style($this->id,$this->url.'_wp_style_admin.css');
	}
	
	public function enqueue_scripts_admin(){
		//wp_enqueue_script($this->id);
		//wp_enqueue_style($this->id);
	}
	
	public function register_scripts(){//ob_clean(); var_dump($this,$this->url); die();
		//wp_register_script($this->id,$this->url.'_wp_script.js');
		wp_register_style($this->id,$this->url.'_wp_style.scss');
	}
	
	public function enqueue_scripts(){
		//wp_enqueue_script($this->id);
		wp_enqueue_style($this->id);
	}

}
