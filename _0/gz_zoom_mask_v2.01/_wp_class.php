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
		$config = [
			'enqueue'  => [
				['type'=>'style' ,'load'=>true ,'prm'=>[__CLASS__,'[REL_PATH]_wp_style.scss']],
				['type'=>'script' ,'load'=>true ,'prm'=>[__CLASS__,'[REL_PATH]_wp_script.js',['jquery-core']]]
			]
			,'shortcodes' => [
				['prm'=>['gz_zoom_mask',[$this,'render_zoom_mask']]]
			]
		];
		parent::__construct($config); //ob_clean(); echo '<pre>'; print_r($config); die();
	}
	
	function render_zoom_mask($atts){//die(__FILE__.__FUNCTION__);
		global $kk_control_2022; 
		$img_groups	= [
			'tmb'	=> ['mode'=>'least','width'=>200,'height'=>200] //Least larger than 200x200
			,'max'	=> ['mode'=>'most','width'=>0,'height'=>0] //Most larger than 0x0
		];
		$atts = shortcode_atts([
			'mask_image_id'	=> 3520,
			'img_width'		=> '100%',
			'img_height'	=> '100%',
			'img_margin'	=> 0,
			'album'			=> false,
			'bg_color'		=> 'black'
		],$atts,'gz_zoom_mask'); //ob_clean(); echo '<pre>'; var_dump($atts); die();
		extract($atts);
		$rs = '';
		$rs.= "<div class='gz_zoom_mask'>";
			$url = wp_get_attachment_url($atts['mask_image_id']); $rs.= "<img class='gz_mask' src='{$url}'>";
			$rs.= $this->render_album($atts);
			$control_prm = ['album'=>$album, 'obj'=>'.gz_zoom_mask', 'items'=>'.gz_img_list'];
			$rs.= $kk_control_2022->render_control_bar($control_prm);
			//$rs.= $kk_control_2022->render_linkbar();
		$rs.= "</div>";
		//
		global $kk_control_2022;
		$rs.= "<div class='link_bar {$album}'>".$kk_control_2022->render_linkbar($album)."</div>";
		return $rs;
	}
	
	function render_album($atts){
		$rs = '';
		if($atts['album']){
			//For single album
			$images = $this->get_portfolio_images($atts['album']);
			$rs.= "<div class='gz_img_list'>";
				foreach($images as $image){
					$rs.="<img src='{$image}' class='item'>";
				}
			$rs.= "</div>";
		}
		return $rs;
	}
	
	function get_portfolio_images($album){
		$doc_root = $_SERVER['DOCUMENT_ROOT'];
		$dir = $doc_root.'/portfolios/'.$album; //die($dir);
		$url = '/portfolios/'.$album;
		$files = glob($dir."/*.{jpg,png,gif,jfif}",GLOB_BRACE); //ob_clean(); echo "<pre>"; var_dump($files); die();
		foreach($files as &$file) $file = str_replace($doc_root,'',$file); //ob_clean(); echo "<pre>"; var_dump($files); die();
		return $files;
	}
	
	function render_content($prm){ //ob_clean(); echo "<pre>"; var_dump($prm); die();
		extract($prm);
		$res = '';
		//$res.= "<img class='item' src='/images/home/cartoon01.jpg'/>";
		//$res.= "<div class='item gz_zoom_bg' data-control='.gz_control' data-mask='.gz_mask' style='background-image:url(/images/home/cartoon01.jpg);'></div>";
		$res.= "<div class='item gz_zoom_bg' data-control='.kk_control' data-mask='.gz_mask' style='{$style}'></div>";
		return $res;
	}

}
