<?php //die(__FILE__);
/*
* gz_facebook() class.
* 	- Retrieve photos from FB album.
* v0.01 - 20180223:Tony
*	- Use gz_tpl v0.01 style
*	- Add shortcode for FB box
* v0.00 - 20171002:Tony
* 	- Add feature, retrieve photos from FB album.
*		- Choose group by resolutions.
*/
class gz_facebook extends gz_tpl{
	private $transient_prefix = __CLASS__;
	//private $id = __CLASS__;
	//private $url;			//Using relative url here
	
	public function __construct(){//die(__FILE__.__FUNCTION__);
		parent::__construct();
		add_shortcode('gz_fb_iframe',[$this,'render_fb_iframe']);
	}
	
	//https://developers.facebook.com/docs/plugins/page-plugin/
	static function render_fb_iframe($atts, $content = null){
		//Prepare shortcode attributes
		//$atts = $this->shortcode_atts([
		$atts = self::shortcode_atts([
			'fb_iframe_url'	=> 'https://www.facebook.com/plugins/page.php'
			,'fb_page_name'	=> '' //'motorvital.co.th' //https%3A%2F%2Fwww.facebook.com%2Fmotorvital.co.th%2F
			,'fb_url'		=> 'https://www.facebook.com'
			,'tabs'			=> 'none' //timeline
			,'width'		=> 500
			,'height'		=> 400
			,'small_header'	=> 'true'
			,'border'		=> 'none'
			,'overflow'		=> 'hidden'
			,'scrolling'	=> 'no'
			,'frameborder'	=> '0'
			,'hide_cover'	=> 'false'
			,'show_facepile'=> 'true'
			,'app_id'		=> '500668826616925'
			,'allowtransparency'	=> 'true'
			,'adapt_container_width'=> 'true'
		],$atts,'gz_fb_iframe'); //ob_clean(); echo '<pre>'; var_dump($atts); die();
		extract($atts,EXTR_PREFIX_ALL,'att');
		//$html = "<iframe src='{$att_fb_iframe_url}?href=https://{$att_page_name}&tabs={$att_tabs}&width={$att_width}&height={$att_height}&small_header=true&adapt_container_width=true&hide_cover=true&show_facepile=true&appId=500668826616925' width='500' height='400' style='border:none;overflow:hidden' scrolling='no' frameborder='0' allowTransparency='true'></iframe>";
		//style='border:none;overflow:hidden' scrolling='no' frameborder='0' allowTransparency='true'
		//"{$att_fb_iframe_url}?href=https://{$att_page_name}&tabs={$att_tabs}&width={$att_width}&height={$att_height}&small_header=true&adapt_container_width=true&hide_cover=true&show_facepile=true&appId=500668826616925"
		//$fb_url = "https://www.facebook.com/{$att_fb_page_name}";
		$fb_url = "{$att_fb_url}/{$att_fb_page_name}";
		$page_url = add_query_arg([
			//'tabs'		=> 'none'
			'width'	=> $att_width
			,'height'	=> $att_height
			,'small_header'		=> $att_small_header
			,'hide_cover'		=> $att_hide_cover
			,'show_facepile'	=> $att_show_facepile
			,'app_id'			=> $att_app_id
			,'adapt_container_width'	=> $att_adapt_container_width
		],$fb_url); //ob_clean(); echo '<pre>'; print_r($atts); print_r($url); die();
		$src = add_query_arg([
			'href'	=> $page_url
		],$att_fb_iframe_url);
		$html = self::render_dom([
			'dom'	=> ['type'=>'iframe' ,'class'=>"gz_fb_iframe"]
			,'attr'	=> [
				'src'	=> $src
				,'width'	=> $att_width
				,'height'	=> $att_height
				,'style'	=> "border:{$att_border};overflow:{$att_overflow};"
				,'scrolling' 			=> $att_scrolling
				,'framborder' 			=> $att_frameborder
				,'allowTransparency'	=> $att_allowtransparency
			]
			,'content'	=> ''
		]); //else $html_top_slides='';
		return $html;
	}
	
	/*
	* get_data()
	* 	- Get data from FB with cache
	* Parameter:
	*	- url.
	* Return:
	*	- Data (raw).
	*
	*/
	public function get_data(){
		$nocache = isset($_GET['nocache']); //ob_clean(); print_r($nocache); die();
		$clearcache = isset($_GET['clearcache']); //ob_clean(); print_r($nocache); die();
		if(func_num_args()>0) {$prm = func_get_arg(0); extract($prm);} //ob_clean(); echo '<pre>'; print_r($prm); die();
		$key = $this->transient_prefix.'_'.md5($url);
		if($clearcache) delete_transient($key);
		if($nocache||(false===($rs=get_transient($key)))){
			$rs = file_get_contents($url); //if(isset($_GET['d'])){ob_clean(); var_dump($rs); die();}
			set_transient($key,$rs,60*60);
		}
		return $rs;
	}
	
	function get_fb_album($args){//ob_clean(); echo '<pre>'; var_dump($args); die();
		$nocache = isset($_GET['nocache']); //ob_clean(); print_r($nocache); die();
		$fb_fields = '';
		if(func_num_args()>0) {$prm = func_get_arg(0); extract($prm);} //ob_clean(); print_r($use_cache); die();
		$fb_album_url = $fb_api_url.$fb_album_id;
		$url = add_query_arg(array(
			'access_token'	=> $fb_access_token
			//,'fields'		=> 'name,description'
			,'fields'		=> $fb_fields
		),$fb_album_url); //if(isset($_GET['d'])){ob_clean(); print_r($url); die();}
		//$key = md5($url);
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
	
	/*
	* - Load images from FB album.
	* 	- Group by resolutions.
	* - Album description too.
	*/
	public function load_fb_images(){
		$nocache = isset($_GET['nocache']); //ob_clean(); print_r($nocache); die();
		if(func_num_args()>0) {$prm = func_get_arg(0); extract($prm);} //ob_clean(); echo '<pre>'; print_r($prm); die();
		$fb_gallery_url = $fb_api_url.$fb_album_id.'/photos';
		$fb_fields = 'name,images.order(reverse_chronological)'; //FB put description in name field.
		$url = add_query_arg(array(
			'fields'		=> $fb_fields
			,'access_token'	=> $fb_access_token
		),$fb_gallery_url); //if(isset($_GET['d'])){ob_clean(); print_r($url); die();}
		$key = $this->transient_prefix.'_'.md5($url);
		if($nocache||(false===($rs=get_transient($key)))){
			$rs = file_get_contents($url); //if(isset($_GET['d'])){ob_clean(); var_dump($rs); die();}
			$data = json_decode($rs); //if(isset($_GET['d'])){ob_clean(); echo '<pre>'; var_dump($data); die();}
			$images = array();
			foreach($data->data as $item){//ob_clean(); echo '<pre>'; var_dump($item); die();
				$image = new \stdClass;
				foreach($groups as $gn=>$gc){//ob_clean(); echo '<pre>'; var_dump($gn,$gc); die();}
					$img = $this->get_image([
						'images'	=> $item->images
						,'cond'		=> $gc //['mode'=>'min','w'=>200,'h'=>200,'smaller'=>true] //Smallest img that's bigger than 200x200px (smaller ok if not found)
					]); //ob_clean(); echo '<pre>'; var_dump($item->images,$img); die();
					$image->$gn = $img;
				}//ob_clean(); echo '<pre>'; var_dump($item); die();
				$image->description = isset($item->name)?$item->name:'';
				$images[$item->id] = $image;
			}
			set_transient($key,$images,60*60);
		}else{
			$images = $rs; //$SYS3->_debug('f',true,true,$rs);
		}
		//if(isset($_GET['d'])){ob_clean(); echo '<pre>'; var_dump($images); die();}
		return $images;
	}

	/*
	* Receive: images(FB list), condition(mode,w,h,smaller)
	* Return (object)$img_sel={image,width,height}
	*/
	public function get_image_3($prm){ //ob_clean(); echo '<pre>'; print_r($prm); die();
		if(func_num_args()>0) {$prm = func_get_arg(0); extract($prm);}
		extract($cond);
		switch($mode){
			case 'least'://Equal or least larger dimension
				$sel_close = (object)['source'=>'' ,'width'=>0 ,'height'=>0 ,'df'=>10000]; //Minimum width & height
				$img_sel = false;
				foreach($images as $img){//ob_clean(); echo '<pre>'; print_r($img); die();
					$df = sqrt(pow($img->width - $width,2) + pow($img->height - $height,2));
					if($sel_close->df>=$df){
						$sel_close->source = $img->source;
						$sel_close->width = $img->width;
						$sel_close->height = $img->height;
						$sel_close->df = $df;
					}
					if($img->width>$width && $img->height>$height) $img_sel = $sel_close;
					//$dbg[] = ['df'=>$df,'img'=>$img];
				}
				if(!$img_sel) $img_sel = $sel_close; //If no least bigger then take the closest instead.
			break;
			case 'most'://Equal or least larger dimension
				$sel_close = (object)['source'=>'' ,'width'=>0 ,'height'=>0 ,'df'=>0]; //Minimum width & height
				$img_sel = false;
				foreach($images as $img){//ob_clean(); echo '<pre>'; print_r($img); die();
					$df = sqrt(pow($img->width - $width,2) + pow($img->height - $height,2));
					if($sel_close->df<=$df){
						$sel_close->source = $img->source;
						$sel_close->width = $img->width;
						$sel_close->height = $img->height;
						$sel_close->df = $df;
					}
					if($img->width>$width && $img->height>$height) $img_sel = $sel_close;
					$dbg[] = ['df'=>$df,'img'=>$img];
				}
				if(!$img_sel) $img_sel = $sel_close; //If no least bigger then take the closest instead.
				//ob_clean(); echo '<pre>'; var_dump($prm,$img_sel,$dbg); die();
			break;
			case 'close'://Closest match (square diff)
				$img_sel = (object)['source'=>'' ,'width'=>0 ,'height'=>0 ,'df'=>10000]; //Minimum width & height
				foreach($images as $img){//ob_clean(); echo '<pre>'; print_r($img); die();
					$df = sqrt(pow($img->width - $sel_close->width,2) + pow($img->height - $sel_close->height,2));
					if($img_sel->df>=$df){
						$img_sel->source = $img->source;
						$img_sel->width = $img->width;
						$img_sel->height = $img->height;
						$img_sel->df = $df;
					}
				}
			break;
		}
		unset($img_sel->df);
		return $img_sel;
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

	/*
	* Receive: images(FB list), condition(mode,w,h,smaller)
	* Return (object)$img_sel={image,width,height}
	*/
	public function get_image($prm){ //ob_clean(); echo '<pre>'; print_r($prm); die();
		if(func_num_args()>0) {$prm = func_get_arg(0); extract($prm);}
		extract($cond);
		switch($mode){
			case 'least'://Equal or least larger dimension
				$sel_close = (object)['source'=>'' ,'width'=>0 ,'height'=>0 ,'df'=>10000]; //Minimum width & height
				$img_sel = false;
				foreach($images as $img){//ob_clean(); echo '<pre>'; print_r($img); die();
					$df = sqrt(pow($img->width - $width,2) + pow($img->height - $height,2));
					if($sel_close->df>=$df){
						$sel_close->source = $img->source;
						$sel_close->width = $img->width;
						$sel_close->height = $img->height;
						$sel_close->df = $df;
					}
					if($img->width>$width && $img->height>$height) $img_sel = $sel_close;
					//$dbg[] = ['df'=>$df,'img'=>$img];
				}
				if(!$img_sel) $img_sel = $sel_close; //If no least bigger then take the closest instead.
			break;
			case 'most'://Equal or least larger dimension
				$sel_close = (object)['source'=>'' ,'width'=>0 ,'height'=>0 ,'df'=>0]; //Minimum width & height
				$img_sel = false;
				foreach($images as $img){//ob_clean(); echo '<pre>'; print_r($img); die();
					$df = sqrt(pow($img->width - $width,2) + pow($img->height - $height,2));
					if($sel_close->df<=$df){
						$sel_close->source = $img->source;
						$sel_close->width = $img->width;
						$sel_close->height = $img->height;
						$sel_close->df = $df;
					}
					if($img->width>$width && $img->height>$height) $img_sel = $sel_close;
					$dbg[] = ['df'=>$df,'img'=>$img];
				}
				if(!$img_sel) $img_sel = $sel_close; //If no least bigger then take the closest instead.
				//ob_clean(); echo '<pre>'; var_dump($prm,$img_sel,$dbg); die();
			break;
			case 'close'://Closest match (square diff)
				$img_sel = (object)['source'=>'' ,'width'=>0 ,'height'=>0 ,'df'=>10000]; //Minimum width & height
				foreach($images as $img){//ob_clean(); echo '<pre>'; print_r($img); die();
					$df = sqrt(pow($img->width - $sel_close->width,2) + pow($img->height - $sel_close->height,2));
					if($img_sel->df>=$df){
						$img_sel->source = $img->source;
						$img_sel->width = $img->width;
						$img_sel->height = $img->height;
						$img_sel->df = $df;
					}
				}
			break;
		}
		unset($img_sel->df);
		return $img_sel;
	}
}
