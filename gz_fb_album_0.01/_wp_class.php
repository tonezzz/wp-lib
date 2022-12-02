<?php //die(__FILE__);
/*
* v0.00 - 201700510:Tony
* 	- Using menu designed by KK
* 
* https://stackoverflow.com/questions/26660728/embed-activity-feed-of-a-public-facebook-page-without-forcing-user-to-login-allo
* https://graph.facebook.com/oauth/access_token?client_id=" + APP_ID + "&client_secret=" + APP_SECRET + "&grant_type=client_credentials
* https://graph.facebook.com/oauth/access_token?client_id=291680341300463&client_secret=92ae93207bc2c8f186413d02d0b29db7&grant_type=client_credentials
*/
//class gz_fb_album extends gz_tpl{
class gz_fb_album extends gz_facebook{
	private $transient_prefix = __CLASS__;
	//private $id = __CLASS__;
	//private $url;			//Using relative url here
	private $fb_fields_img	= 'images.order(reverse_chronological)';
	private $fb_access_token = '132741334090106|Ks7ErRnocuAFdhAkoKTv7u7W15g';
	
	public function __construct(){//die(__FILE__.__FUNCTION__);
		parent::__construct();
		$this->get_url(__FILE__);
		$this->set_id(__CLASS__);
		$this->init_scripts();
		$this->init_shortcode();
	}
	
	function init_shortcode(){//die(__FILE__.__FUNCTION__);
		add_shortcode('gz_fb_album',[$this,'render_fb_album']);
		//add_shortcode('kk_cartoon',array($this,'render_cartoon'));
		//$post = get_post(16); ob_clean(); var_dump($post);
		//add_filter('the_content',[$this,'test_content']);
	}
	
	function render_single_img($prm){
		extract($prm,EXTR_PREFIX_ALL,'prm'); //ob_clean(); echo '<pre>'; var_dump($bg); die();
		//$bg = ''; foreach($prm_bg as $item) $bg.="url({$item})"; //ob_clean(); echo '<pre>'; var_dump($bg); die();
		//$bg = implode(',',$prm_bg); //ob_clean(); echo '<pre>'; var_dump($bg); die();
		//$bg = "background-image:url({$img->tmb->source})";
		//$bg.= ",url(/images/preload.gif);"; //Preload background
		array_walk($prm_bg,function(&$v,$k){$v="url({$v})";}); $bg = 'background-image:'.implode(',',$prm_bg).';';
		//$bg .= "background-size:cover;";
		$img_info = json_encode($prm_info);
		$html = '';
		$html.="<li class='item' data-img='{$img_info}'>";
			$tmb ="<span class='tmb' style='{$bg}'></span>";
			if(!empty($prm_description)) $tmb = "<a target='_blank' href='{$prm_description}'>{$tmb}</a>";
			$html.=$tmb;
		$html.="</li>";
		return $html;
	}
	
	function render_fb_album($atts){ //if(isset($_GET['d'])){ob_clean(); die(__FILE__.__FUNCTION__);}
		global $kk_control;
		$atts = shortcode_atts([
			'fb_api_url'		=> 'https://graph.facebook.com/v2.10/'
			//,'fb_access_token'	=> '291680341300463|2egpDD04iBAsHeYfWmdq70-_K_U'
			,'fb_access_token'	=> $this->fb_access_token
			//,'fb_fields'		=> 'images.order(reverse_chronological)'
			,'fb_fields'		=> $this->fb_fields_img
			,'fb_album_id'		=> '476208095751555' //KK Cartoon
			,'item_bg_image_id'	=> '' //Motor vital logo
		],$atts,'gz_fb_album'); //if(isset($_GET['d'])){ob_clean(); echo '<pre>'; var_dump($atts); die();}
		$img_groups	= [
			'tmb'	=> ['mode'=>'least','width'=>200,'height'=>200] //Least larger than 200x200
			,'max'	=> ['mode'=>'most','width'=>0,'height'=>0] //Most larger than 0x0
		];
		extract($atts);
		//Get base background
		$bg_0_url = '';
		if(!empty($item_bg_image_id)) $bg_0 = wp_get_attachment_image_src($item_bg_image_id); //ob_clean(); echo '<pre>'; var_dump($bg); die();
		if(!empty($bg_0)) $bg_0_url = $bg_0[0];
		//
		$content = '';
		if(strpos($fb_album_id,',')===false){
			//For single album
			$prm = [
				'fb_api_url'		=> $fb_api_url
				,'fb_access_token'	=> $fb_access_token
				,'fb_album_id'		=> $fb_album_id
				,'groups'			=> $img_groups
			];
			$images = $this->load_fb_images($prm); //if(isset($_GET['d'])){ob_clean(); echo '<pre>'; var_dump($prm,$images); die();}
			$content.= "<div class='gz_fb_album'>";
				$content.= "<ul class='items'>";
				foreach($images as $img){//ob_clean(); echo "<pre>"; print_r($img); die();
					$content.=$this->render_single_img([
						//'bg' => [$img->tmb->source,$trans_white_90,$bg_0_url]
						'bg' => [$img->tmb->source]
						//'bg' => [$trans_white_90,$bg_0_url]
						,'info'	=> json_encode($img)
						,'desc'	=> $img->description
					]);
				}
				$content.= "</ul>";
			$content.= "</div>";
		}else{//For multi album
			//$control_prm = ['obj'=>'.gz_zoom_mask' ,'items'=>'.gz_img_list' ,'tabs'=>'gz_img_tab'];
			$albums = explode(',',$atts['fb_album_id']); //ob_clean(); echo "<pre>"; var_dump($albums); die();
			$html_tab = '';
			$html_content = '';
			$css_label = '';
			foreach($albums as $album_id){
				$album = $this->get_fb_album([
					'fb_api_url' 		=> $atts['fb_api_url']
					,'fb_access_token' 	=> $atts['fb_access_token']
					,'fb_album_id' 		=> $album_id
					,'groups'			=> $img_groups
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
					foreach($images as $img){//ob_clean(); echo "<pre>"; var_dump($img); die();
						//$bg = "background-image:url({$img->url});";
						//$img_info = json_encode($img);
						//$html_content.="<li class='item' style='{$bg}' data-img='{$img_info}'>";
						//$html_content.="</li>";
						$html_content.=$this->render_single_img([
							'bg' => [$img->tmb->source]
							,'info'	=> json_encode($img)
							,'desc'	=> $img->description
						]);
					}
					$html_content.= "</ul>";
				$html_content.= "</div>";
			}
			//$content.= $kk_control->render($control_prm);
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
