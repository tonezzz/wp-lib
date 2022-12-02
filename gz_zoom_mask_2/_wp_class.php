<?php //die(__FILE__);
/*
* v0.00 - 20171007:Tony
* 	- Add shortcode to render DOM.
*	- Render container, mask & control.
*	- jQuery plugin for control (change img, change mase, zooming, moving).
* 
* https://stackoverflow.com/questions/26660728/embed-activity-feed-of-a-public-facebook-page-without-forcing-user-to-login-allo
* https://graph.facebook.com/oauth/access_token?client_id=" + APP_ID + "&client_secret=" + APP_SECRET + "&grant_type=client_credentials
* https://graph.facebook.com/oauth/access_token?client_id=291680341300463&client_secret=92ae93207bc2c8f186413d02d0b29db7&grant_type=client_credentials
*/
class gz_zoom_mask_2 extends gz_tpl{
	private $transient_prefix = __CLASS__;
	//private $id = __CLASS__;
	//private $url;			//Using relative url here
	private $shortcode_atts=false;
	private $img_groups=false;
	private $gz_facebook=false;
	
	public function __construct(){//die(__FILE__.__FUNCTION__);
		parent::__construct();
		$this->get_url(__FILE__);
		$this->set_id(__CLASS__);
		$this->init_scripts();
		$this->init_shortcode();
		
		global $gz_facebook; $this->gz_facebook = $gz_facebook;
		$this->img_groups = [
			'tmb'	=> ['mode'=>'least','width'=>200,'height'=>200] //Least larger than 200x200
			,'max'	=> ['mode'=>'most','width'=>0,'height'=>0] //Most larger than 0x0
		];
	}
	
	function init_shortcode(){//die(__FILE__.__FUNCTION__);
		add_shortcode('gz_zoom_mask_2',[$this,'render']);
	}
	
	function render($atts){//die(__FILE__.__FUNCTION__);
		$this->shortcode_atts = shortcode_atts([
			'fb_api_url'		=> 'https://graph.facebook.com/v2.10/'
			,'fb_access_token'	=> '291680341300463|2egpDD04iBAsHeYfWmdq70-_K_U'
			,'fb_fields'		=> 'images.order(reverse_chronological)'
			,'fb_album_id'		=> '476208095751555' //KK Cartoon
			//,'fb_album_id'	=> '371660582886332' //KK Folio
			,'mask_image_id'	=> 66
			,'img_width'		=> '100%'
			,'img_height'		=> '100%'
			,'img_margin'		=> 0
		],$atts,'gz_zoom_mask_2'); //ob_clean(); echo '<pre>'; var_dump($atts); die();
		return $this->render_dom([
			'dom'=>['type'=>'div']
			,'attr'=>['class'=>'gz_zoom_mask_2']
			//,'prm'=>null
			,'content'=>function(){//return 'xxxxxx';
				return $this->render_panel_img().$this->render_panel_control().$this->render_panel_tmb();
			}
		]);
		//return $content;
	}
	
	function render_panel_img(){
		return $this->render_dom([
			'dom'=>['type'=>'div']
			,'attr'=>['class'=>'panel_img']
			//,'prm'=>null
			,'content'=>function(){//return 'xxxxxx';
				extract($this->shortcode_atts,EXTR_PREFIX_ALL,'att'); //return $att_fb_album_id;
				$bg_img_url = wp_get_attachment_url($att_mask_image_id); //ob_clean(); die($bg_img_url);
				return $this->render_dom([
					'dom'	=> ['type'=>'div']
					,'attr'	=> ['class'=>'img']
				]).$this->render_dom([
					'dom'	=> ['type'=>'div']
					,'attr'	=> ['class'=>'mask' ,'style'=>"background-image:url({$bg_img_url});"]
				]);
			}
		]);
	}
	
	function render_panel_control(){
		return $this->render_dom([
			'dom'=>['type'=>'div' ,'class'=>'panel_control']
			//,'attr'=>['class'=>'panel_control']
			//,'prm'=>null
			,'content'=>
				$this->render_dom(['dom'=>['type'=>'div' ,'class'=>'control album_prev']])
				.$this->render_dom(['dom'=>['type'=>'div' ,'class'=>'control img_prev']])
				.$this->render_dom(['dom'=>['type'=>'div' ,'class'=>'control zoom_out']])
				.$this->render_dom([
					'dom'=>['type'=>'div' ,'class'=>'control img_pan']
					,'content'=>
						$this->render_dom(['dom'=>['type'=>'div' ,'class'=>'button up']])
						.$this->render_dom(['dom'=>['type'=>'div' ,'class'=>'button right']])
						.$this->render_dom(['dom'=>['type'=>'div' ,'class'=>'button down']])
						.$this->render_dom(['dom'=>['type'=>'div' ,'class'=>'button left']])
						.$this->render_dom(['dom'=>['type'=>'div' ,'class'=>'button center']])
				])
				.$this->render_dom(['dom'=>['type'=>'div' ,'class'=>'control zoom_in']])
				.$this->render_dom(['dom'=>['type'=>'div' ,'class'=>'control img_next']])
				.$this->render_dom(['dom'=>['type'=>'div' ,'class'=>'control album_next']])
		]);
	}

	function render_panel_tmb(){ //gz_img_tab
		return $this->render_dom([
			'dom'=>['type'=>'div' ,'class'=>'panel_tmb']
			//,'attr'=>['class'=>'panel_tmb']
			//,'prm'=>null
			,'content'=>function(){//return 'zzzzzzz';
				extract($this->shortcode_atts,EXTR_PREFIX_ALL,'att'); //return $att_fb_album_id;
				$albums = explode(',',$att_fb_album_id); //ob_clean(); var_dump($albums); die();
				if(count($albums)>=1) return $this->render_panel_tmb_multi(['albums'=>$albums]);
				else return $this->render_panel_tmb_single([$albums[0]]);
			}
		]);
	}
	
	function render_panel_tmb_multi($prm){
		extract($this->shortcode_atts,EXTR_PREFIX_ALL,'shortcode_att'); //return $att_fb_album_id;
		extract($prm,EXTR_PREFIX_ALL,'prm'); //return $att_fb_album_id;
		$html_tab = '';
		$html_content = '';
		$css_label = '';
		$fb_album_prm = [
			'fb_api_url'		=> $shortcode_att_fb_api_url
			,'fb_access_token'	=> $shortcode_att_fb_access_token
			,'json_decode'		=> true
			//,'fb_album_id'		=> $album_id
		]; //ob_clean(); var_dump($fb_prm); die();
		$fb_img_prm = [
			'fb_api_url'		=> $shortcode_att_fb_api_url
			,'fb_access_token'	=> $shortcode_att_fb_access_token
			,'groups'			=> $this->img_groups
			,'json_decode'		=> true
			//,'fb_album_id'		=> $album_id
		];
		/*
		* Here will prepare html for tabs and content for each tab.
		*/
		foreach($prm_albums as $album_id){
			$fb_album_prm['fb_album_id'] = $album_id;
			$album = $this->gz_facebook->get_fb_album($fb_album_prm); //ob_clean(); echo "<pre>"; var_dump($album); die();
			if(is_object($album)){
				$album_title = $album->name; $album_title_slug = sanitize_title($album_title);
				$html_tab.= $this->render_dom([//'_debug'=>true,'xx'=>'x',
					'dom'=>['type'=>'li']
					,'attr'=>['class'=>'tab_label']
					,'prm'=>[['album_title_slug'=>$album_title_slug ,'album_id'=>$album_id]]
					,'content'=>function($prm){//ob_clean(); echo "<pre>"; var_dump($prm); die();
						extract($prm); //return $att_fb_album_id;
						return $this->render_dom([//'_debug'=>true,'xx'=>'x',
							'dom'=>['type'=>'a']
							,'attr'=>['class'=>'tab_label' ,'href'=>"#{$album_title_slug}"]
							,'prm'=>[['album_title_slug'=>$album_title_slug ,'album_id'=>$album_id]]
							,'content'=>function($prm){
								extract($prm); //return $att_fb_album_id;
								return $this->render_dom([
									'dom'=>['type'=>'span']
									,'attr'=>['class'=>"tab_label {$album_title_slug}"]
									,'prm'=>[['album_id'=>$album_id]]
									,'content'=>function($prm){
										extract($prm); //return $att_fb_album_id;
										$url="https://www.facebook.com/pg/KKGT-ADAY-Cartoon-Character-126395397399495/photos/?tab=album&album_id={$album_id}";
										return $this->render_dom([
											'dom'=>['type'=>'a']
											,'attr'=>['class'=>"more" ,'target'=>'_blank' ,'href'=>$url]
											,'content'=>'More...'
										]);
									}//return 'zzzzzzz';
								]);
							}
						]);
					}
				]);
				//
				$fn = "/images/btn_ports/btn_{$album_title_slug}";
				$css_label.= ".tab_label.{$album_title_slug} {background-image:url({$fn}_off.png)}\n";
				$css_label.= ".tab_label.{$album_title_slug}:hover {background-image:url({$fn}_on.png)}\n";
				//
				$html_content.= $this->render_panel_tmb_single([
					'album_title_slug'	=> $album_title_slug
					,'fb_img_prm'		=> $fb_img_prm
					,'album_id'			=> $album_id
				]);
			}
		}
		/*
		* Here putting it together
		*/
		$content = $this->render_dom([
			'dom'=>['type'=>'div']
			,'attr'=>['class'=>"gz_img_tabs"]
			,'prm'=>[['html_tab'=>$html_tab ,'html_content'=>$html_content ,'css_label'=>$css_label]]
			,'content'=>function($prm){
				extract($prm,EXTR_PREFIX_ALL,'prm'); //return $att_fb_album_id;
				return $this->render_dom([
					'dom'=>['type'=>'ul']
					,'attr'=>['class'=>"tabs_nav"]
					//,'prm'=>[['html_tab'=>$prm_html_tab ,'html_content'=>$prm_html_content ,'css_label'=>$prm_css_label]]
					,'content'=>$prm_html_tab
				]).$prm_html_content."\n<style>/*label_style*/\n{$prm_css_label}</style>\n";
			}
		]);
		return $content;
	}
	
	function render_panel_tmb_single($prm){//ob_clean(); echo "<pre>"; var_dump($prm); die();
		extract($prm,EXTR_PREFIX_ALL,'prm'); //return $att_fb_album_id;
		$html_content = $this->render_dom([
			'dom'=>['type'=>'div']
			,'attr'=>['id'=>$prm_album_title_slug ,'class'=>"gz_img_list"]
			,'prm'=>[['album_id'=>$prm_album_id ,'fb_img_prm'=>$prm_fb_img_prm]]
			,'content'=>function($prm){
				extract($prm); //ob_clean(); echo "<pre>"; var_dump($fb_img_prm); die();
				return $this->render_dom([
					'dom'=>['type'=>'ul']
					,'attr'=>['class'=>"items"]
					,'prm'=>[['album_id'=>$album_id ,'fb_img_prm'=>$fb_img_prm]]
					,'content'=>function($prm){
						extract($prm);
						$fb_img_prm['fb_album_id'] = $album_id; //ob_clean(); echo "<pre>"; var_dump($fb_img_prm); die();
						$images = $this->gz_facebook->load_fb_images($fb_img_prm); //ob_clean(); echo "<pre>"; var_dump($images); die();
						$html = '';
						foreach($images as $img){
							//$bg = "background-image:url({$img->url});";
							$bg = "background-image:url({$img->tmb->source})";
							$bg.= ",url(/images/preload.gif);"; //Preload background
							//$img_info = json_encode($img->max);
							//$html_content.="<li class='item' style='{$bg}' data-img='{$img_info}'>";
							//$html_content.="</li>";
							
							$html.=$this->render_dom([
								'dom'=>['type'=>'li']
								,'attr'=>['class'=>"item" ,'style'=>$bg ,'data-img'=>json_encode($img->max)]
							]);
						}
						return $html;
					}
				]);
			}//return 'zzzzzzz';
		]);
		return $html_content;
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
		wp_register_script($this->id,$this->url.'_wp_script.js',array('jquery-core','jquery-loop_nextprev'));
		wp_register_style($this->id,$this->url.'_wp_style.scss');
	}
	
	public function enqueue_scripts(){
		wp_enqueue_script($this->id);
		wp_enqueue_style($this->id);
	}

}
