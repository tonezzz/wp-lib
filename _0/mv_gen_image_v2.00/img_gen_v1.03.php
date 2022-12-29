<?php //die(__FILE__);
/*
v1.03 - 20180114
	- To fix character rotation with more accurate angle
	- To fix character translation for up/down curve
v1.01 - 20180108:Tony
	- Use $_REQUEST instead of $_GET for long url  issue with apply image.
v1.00 - 20171228:Tony
	- Switch to Imagick.
	- Installation for CentOS http://www.webhostingtalk.com/showthread.php?t=1528610
	- Installation for Debian https://codeplanet.io/install-imagemagick-with-php-on-debian/
	- Then fix the issue with: apt-get install libmagickwand-dev libmagickcore-dev
v0.00 - 20171026:Tony
*/
require_once '../../../../../wp-load.php';

class gen_image extends gz_tpl{
	private $img_dir,$image,$image_format;
	private $draw=null;
	private $special_chars = 'ิีึืุู่้๊๋ำ';
	//private $ch_2nd_level = 'ิีึืำ';

	
	public function __construct(){//die(__FILE__.__FUNCTION__);
		$this->set_resource_limits();
		if(isset($_REQUEST['d_res'])) {$this->show_info(); die();}
		parent::__construct();
		$this->get_url(__FILE__);
		$this->set_id(__CLASS__);
		//$this->init_scripts();
		//$this->init_shortcode();
		$this->img_dir = $this->dir.'img/'; //ob_clean(); echo "<pre>"; var_dump($this); die();
		$this->render_image();
	}
	
	function set_resource_limits(){
		Imagick::setResourceLimit(Imagick::RESOURCETYPE_MEMORY,2575908864);
		Imagick::setResourceLimit(Imagick::RESOURCETYPE_AREA,2575908864);
		//Imagick::setResourceLimit(Imagick::RESOURCETYPE_DISK,-1);
		Imagick::setResourceLimit(Imagick::RESOURCETYPE_FILE,768);
		Imagick::setResourceLimit(Imagick::RESOURCETYPE_MAP,2575908864);
	}
	
	function show_info(){
		$info['Imagick::RESOURCETYPE_MEMORY'] = Imagick::getResourceLimit(Imagick::RESOURCETYPE_MEMORY);
		$info['Imagick::RESOURCETYPE_AREA'] = Imagick::getResourceLimit(Imagick::RESOURCETYPE_AREA);
		$info['Imagick::RESOURCETYPE_DISK'] = Imagick::getResourceLimit(Imagick::RESOURCETYPE_DISK);
		$info['Imagick::RESOURCETYPE_FILE'] = Imagick::getResourceLimit(Imagick::RESOURCETYPE_FILE);
		$info['Imagick::RESOURCETYPE_MAP'] = Imagick::getResourceLimit(Imagick::RESOURCETYPE_MAP);
		//$info['Imagick::RESOURCETYPE_THREAD'] = Imagick::getResourceLimit(Imagick::RESOURCETYPE_THREAD);
		ob_clean(); echo '<pre>'; print_r($info);
	}
	
	function create_image(){
		if(func_num_args()>0){$prm = func_get_arg(0);} //ob_clean(); echo "<pre>"; var_dump($prm); die();
		//wp_parse_args($prm,['width'=>1200 ,'height'=>1200]);
		//Ceate a blank image
		//$image = new stdClass; $image->sx = $prm['width']; $image->sy = $prm['height'];
		//$imagecreatetruecolor($image->sx,$image->sy);
		//if(isset($prm['file']))$this->image = load_image(['file'=>$prm['file']]);
		//$this>image = new Imagick();
		return $this->load_image(['file'=>$prm['file']]);
	}
	
	function create_image_2(){
		$prm = func_num_args()>0?func_get_arg(0):[]; //ob_clean(); echo "<pre>"; var_dump($prm); die();
		$prm = $this->shortcode_atts(['file'=>false,'format'=>'jpeg','w'=>1000,'h'=>1000,'bg_color'=>'rgb(0,0,0)'],$prm); //ob_clean(); echo "<pre>"; print_r($prm); die();
		extract($prm,EXTR_PREFIX_ALL,'prm'); //ob_clean(); echo "<pre>"; print_r($prm_file); die();
		$this->image_format = $prm_format; //die($this->image_format);
		if($prm_file) $img = $this->load_image(['file'=>$prm_file]);
		else{
			$img = new Imagick();
			$img->setformat($this->image_format);
			//$prm_bg_color = new ImagickPixel($prm_bg_color);
			$img->newImage($prm_w,$prm_h,$prm_bg_color);
		}
		return $img;
	}
	
	function load_image(){//die(__FUNCTION__);
		if(func_num_args()>0){$prm = func_get_arg(0);} //ob_clean(); echo "<pre>"; var_dump($prm); die();
		$img = new Imagick(); //die('xx');
		$img->readImage($prm['file']); //ob_clean(); echo "<pre>"; var_dump($img); die();
		//$fh = fopen($prm['file'],'r'); $img->readImageFile($fh); fclose($fh); //ob_clean(); echo "<pre>"; var_dump($img); die();
		return $img;
	}
	
	/*
	*/
	function parse_paste_param($prm){//ob_clean(); echo "<pre>"; var_dump($prm); die();
		$rs = new stdClass;
		if(!empty($prm['file'])) $rs->img = $this->load_image(['file'=>$prm['file']]); else $rs->img = $prm['img']; //ob_clean(); echo "<pre>"; var_dump($rs); die();
		if(!empty($prm['px'])) $rs->x = $prm['img']->sx*$prm['px']; elseif(isset($prm['x'])) $rs->x = $prm['x']; else $rs->x = 0;
		if(!empty($prm['py'])) $rs->y = $prm['img']->sy*$prm['py']; elseif(isset($prm['y'])) $rs->y = $prm['y']; else $rs->y = 0;
		if(!empty($prm['psx'])) $rs->sx = $rs->img->sx*$prm['psx']; elseif(isset($prm['sx'])) $rs->sx = $prm['sx']; else $rs->sx = $prm['img']->sx;
		if(!empty($prm['psy'])) $rs->sy = $rs->img->sy*$prm['psy']; elseif(isset($prm['sy'])) $rs->sy = $prm['sy']; else $rs->sy = $prm['img']->sy;
		//ob_clean(); echo "<pre>"; var_dump($prm,$rs); die();
		return $rs;
	}
	
	function paste_image(){
		$prm = func_num_args()>0?func_get_arg(0):[]; //ob_clean(); echo "<pre>"; var_dump($prm); die();
		$prm = $this->shortcode_atts([
			'src'=>['file'=>false,'img'=>false ,'x'=>0 ,'y'=>0 ,'psx'=>1 ,'psy'=>1]
			,'dst'=>['img'=>false ,'x'=>0 ,'y'=>0 ,'psx'=>.1 ,'psy'=>.1]
		],$prm); //ob_clean(); echo "<pre>"; var_dump($prm); die();
		$src = $this->parse_paste_param($prm['src']); //ob_clean(); echo "<pre>"; var_dump($src); die();
		$dst = $this->parse_paste_param($prm['dst']); //ob_clean(); echo "<pre>"; var_dump($src,$dst); die();
		//$this->image->compositeImage($src->img,Imagick::COMPOSITE_DEFAULT, $dst->x, $dst->y);
		$dst->img->compositeImage($src->img,Imagick::COMPOSITE_DEFAULT, $dst->x, $dst->y);
		unset($src->img); unset($src);
	}
	
	function paste_image_0(){
		$prm = wp_parse_args(func_num_args()>0?func_get_arg(0):[],[
			'src'=>['img'=>false ,'x'=>0 ,'y'=>0 ,'psx'=>1 ,'psy'=>1]
			,'dst'=>['img'=>false ,'x'=>0 ,'y'=>0 ,'psx'=>.1 ,'psy'=>.1]
		]); ob_clean(); echo "<pre>"; var_dump($prm); die();
		$src = $this->parse_paste_param($prm['src']); //ob_clean(); echo "<pre>"; var_dump($src); die();
		$dst = $this->parse_paste_param($prm['dst']); //ob_clean(); echo "<pre>"; var_dump($src,$dst); die();
		$this->image->compositeImage($src->img,Imagick::COMPOSITE_DEFAULT, $dst->x, $dst->y);
		unset($src->img); unset($src);
	}
	/*
	*	
	*/
	function draw_text_3(){
		$prm = wp_parse_args(func_num_args()>0?func_get_arg(0):[],[
			'text'	=> 'TEST'
			,'font'	=> ['name'=>'BoonTook-Ultra.ttf' ,'size'=>10 ,'angle'=>0 ,'color'=>'rgb(29,28,86)']
			,'pos'	=> ['x'=>0 ,'y'=>0 ,'align'=>Imagick::ALIGN_CENTER]
			,'stroke'	=> null
			,'shadow'	=> null
			,'debug'	=> false
		]); if($prm['debug']){ob_clean(); echo "<pre>"; print_r($prm); die();}
		$prm['font_file'] = $this->dir.'fonts/'.$prm['font']['name']; //ob_clean(); var_dump($font_file); die();
		//Set parameters
		//if($this->draw==null) $this->draw = new ImagickDraw(); $draw = $this->draw; //Reducing memory usage
		$draw = new ImagickDraw();
		$draw->setFont($prm['font_file']);
		$draw->setFontSize($prm['font']['size']);
		//extract($prm['rgb'],EXTR_PREFIX_ALL,'rgb'); //var_dump('xx',$rgb_0); die();
		$draw->setTextAlignment($prm['pos']['align']); //ob_clean(); echo "<pre>"; var_dump($draw); die();
		$draw->setTextAntialias(true);
		//Drop shadow
		if(!empty($prm['shadow'])){
			$shadow_color = new \ImagickPixel(); $shadow_color->setColor($prm['shadow']['color']);
			$draw->setFillColor($shadow_color);
			$this->image->annotateImage($draw,$prm['pos']['x']+$prm['shadow']['ox'],$prm['pos']['y']+$prm['shadow']['oy'],0,$prm['text']); //die();
		}
		$text_color = new \ImagickPixel(); $text_color->setColor($prm['font']['color']);
		$draw->setFillColor($text_color);
		//Stroke
		if(!empty($prm['stroke'])){
			$draw->setStrokeColor($prm['stroke']['color']);
			$draw->setStrokeWidth($prm['stroke']['width']);
			$draw->setStrokeAntialias(true);
		} //else $draw->setStrokeWidth(0);
		try{
			$this->image->annotateImage($draw,$prm['pos']['x'],$prm['pos']['y'],0,$prm['text']); //var_dump($e); die();
		//}catch(ImagickException $e){
		} catch (Exception $e) {
			var_dump($e); die();
		}
	}

	/*
	*	http://php.net/manual/en/function.imagecopymerge.php
	*	http://php.net/manual/en/function.imagecopyresampled.php
	*/
	function render_image(){//die(__FUNCTION__);
		$seq = $_REQUEST['seq']; //ob_clean(); echo $seq; die();
		$seq = stripslashes($seq);
		$seq = unserialize($seq); //ob_clean(); echo "<pre>"; print_r($seq); die();
		foreach($seq as $v){
			switch($v[0]){
				case 'c1': //ob_clean(); echo "<pre>"; var_dump($v); die();
					if(filter_var($v[1],FILTER_VALIDATE_URL)){
						$this->image = $this->create_image(['file'=>$v[1]]); //ob_clean(); echo "<pre>"; var_dump($this); die();
					}else{
						$this->image = $this->create_image(['file'=>$this->img_dir.$v[1]]); //ob_clean(); echo "<pre>"; var_dump($this); die();
					}
					break;
				case 'c2'://ob_clean(); echo "<pre>"; print_r($v); die();
					$this->image = $this->create_image_2($v['prm']);
					break;
				case 'c3'://ob_clean(); echo "<pre>"; print_r($v); die();
					$this->image = $this->do_c3($v['prm']);
					break;
				case 'p1':
					$prm = [
						'src'=>[ //Source image and location to pick
							'file'	=> $this->img_dir.$v[1]
							,'x'	=> $v[2]
							,'y'	=> $v[3]
							,'sx'	=> $v[4]
							,'sy'	=> $v[5] //Paste the entire image
						]
						,'dst'=>[ //Destination image and location to paste
							'img'	=> $this->image
							,'x'	=> $v[6]
							,'y'	=> $v[7]
							,'sx'	=> empty($v[8])?null:$v[8]
							,'sy'	=> empty($v[9])?null:$v[9]
						]
					]; //ob_clean(); echo "<pre>"; var_dump($prm); die();
					$this->paste_image($prm);
					break;
				case 't3':
					$prm = $v['prm']; //ob_clean(); echo "<pre>"; var_dump($prm); die();
					$this->draw_text_3($prm);
					break;
				case 'tc1': //Text curve 1
					$prm = $v['prm']; //ob_clean(); echo "<pre>"; var_dump($v); die();
					//$this->draw_text_curve_1($prm);
					$this->draw_text_curve_1a($prm);					
					break;
				case 'circle': //Text curve 1
					$prm = $v['prm']; //ob_clean(); echo "<pre>"; var_dump($v); die();
					$this->draw_circle_1($prm);
					break;
				case 'line': //Text curve 1
					$prm = $v['prm']; //ob_clean(); echo "<pre>"; var_dump($v); die();
					$this->draw_line_1($prm);
					break;
				case 'img': //Text curve 1
					$prm = $v['prm']; //ob_clean(); echo "<pre>"; var_dump($v); die();
					$this->draw_img($prm);
					break;
			}
		} //die('xx');
		//if(!isset($_GET['e1'])) header("Content-Type: image/jpeg");	imagejpeg($this->image->img,null,90); //imagedestroy($img->img);
		if(!isset($_REQUEST['e1'])){
			$img_type = "image/{$this->image_format}"; //die($img_type);
			header("Content-Type: {$img_type}");
			$filename = empty($_GET['filename'])?'product_image.jpg':$_GET['filename'];
			header("content-disposition: attachment; filename=\"{$filename}\"");
			$img_data = $this->image->getImageBlob();
			echo $img_data; //die('xx');
			//imagejpeg($this->image->img,null,90); //imagedestroy($img->img);
		}
	}

	function do_c3(){//die(__FUNCTION__);
		$prm = func_num_args()>0?func_get_arg(0):[]; //ob_clean(); echo "<pre>"; var_dump($prm); die();
		$prm = $this->shortcode_atts(['file'=>false,'format'=>'jpeg','w'=>1000,'h'=>1000,'bg_color'=>false],$prm); //ob_clean(); echo "<pre>"; print_r($prm); die();
		extract($prm,EXTR_PREFIX_ALL,'prm'); //ob_clean(); echo "<pre>"; print_r($prm_file); die();
		$this->image_format = $prm_format; //die($this->image_format);

		$img = new Imagick(); $img->setformat($this->image_format);
		
		if($prm_bg_color) $img->newImage($prm_w,$prm_h,$prm_bg_color);
		if($prm_file){
			//$img_file = $this->load_image(['file'=>$prm_file]);
			$this->paste_image(['src'=>['file'=>$prm_file],'dst'=>['img'=>$img]]);
		}
		//$this->output_jpeg($img); die();
		return $img;
	}
	
	function output_jpeg($img){
		header("Content-Type: image/jpeg");
		imagejpeg($img,null,90); //imagedestroy($img->img);
	}
	
	function draw_line_1($prm){
		$prm = func_num_args()>0?func_get_arg(0):[]; //ob_clean(); echo "<pre>"; var_dump($prm); die();
		$prm = $this->shortcode_atts([
			'x1'=>500-400
			,'y1'=>500
			,'x2'=>500+400
			,'y2'=>500
			,'stroke'=>['color'=>'rgba(255,255,255,1)','width'=>4,'antiAlias'=>true]
		],$prm); //ob_clean(); echo "<pre>"; print_r($prm); die();
		extract($prm,EXTR_PREFIX_ALL,'prm');
		$draw = new \ImagickDraw();
		$draw->setStrokeColor($prm_stroke['color']); $draw->setSTrokeWidth($prm_stroke['width']); $draw->setStrokeAntialias($prm_stroke['antiAlias']);
		//$draw->setFillColor($prm_fillColor);
		$draw->line($prm_x1,$prm_y1,$prm_x2+$prm_x2,$prm_y2);
		$this->image->drawImage($draw);
	}

	function draw_rect_1($prm){
		$prm = func_num_args()>0?func_get_arg(0):[]; //ob_clean(); echo "<pre>"; var_dump($prm); die();
		$prm = $this->shortcode_atts([
			'origin'		=> ['x'=>0,'y'=>0]
			,'bounding'		=> ['x1'=>250,'y1'=>250,'x2'=>750,'y2'=>750]
			,'stroke'	=> ['color'=>'rgba(255,0,0,1)','width'=>1,'antiAlias'=>true]
			,'fillColor'=> 'rgba(0,0,0,0)'
		],$prm); //ob_clean(); echo "<pre>"; print_r($prm); die();
		extract($prm,EXTR_PREFIX_ALL,'prm');
		$draw = new \ImagickDraw();
		$draw->setStrokeColor($prm_stroke['color']); $draw->setSTrokeWidth($prm_stroke['width']); $draw->setStrokeAntialias($prm_stroke['antiAlias']);
		$draw->setFillColor($prm_fillColor);
		$draw->rectangle($prm_origin['x']+$prm_bounding['x1'],$prm_origin['y']+$prm_bounding['y1'],$prm_origin['x']+$prm_bounding['x2'],$prm_origin['y']+$prm_bounding['y2']);
		$this->image->drawImage($draw);
	}

	function shortcode_atts($default,$arr){
		$arr = shortcode_atts($default,$arr);
		foreach($default as $key=>$var) if(is_array($var)) $arr[$key] = $this->shortcode_atts($var,$arr[$key]);
		return $arr;
	}
	
	function draw_circle_1($prm){
		$prm = func_num_args()>0?func_get_arg(0):[]; //ob_clean(); echo "<pre>"; var_dump($prm); die();
		$prm = $this->shortcode_atts([
			'pos'		=> ['x'=>500,'y'=>500]
			,'radius'	=> 500
			,'stroke'	=> ['color'=>'rgba(255,255,255,1)','width'=>2,'antiAlias'=>true]
			,'fillColor'=> 'rgba(0,0,255,0)' //Default is no fill
		],$prm); //ob_clean(); echo "<pre>"; print_r($prm); die();
		//$prm['stroke'] = shortcode_atts(['color'=>'rgba(255,255,255,1)','width'=>1,'antiAlias'=>true],$prm['stroke']); ob_clean(); echo "<pre>"; print_r($prm); die();
		extract($prm,EXTR_PREFIX_ALL,'prm');
		$draw = new \ImagickDraw();
		$draw->setStrokeColor($prm_stroke['color']); $draw->setSTrokeWidth($prm_stroke['width']); $draw->setStrokeAntialias($prm_stroke['antiAlias']);
		$draw->setFillColor($prm_fillColor);
		$draw->circle($prm_pos['x'],$prm_pos['y'],$prm_pos['x']+$prm_radius,$prm_pos['y']);
		$this->image->drawImage($draw);
	}
	
	/*
		20180116:Tony
			Concept:
			- Merge multi level text and draw once.
			Issues:
			- Problem with multi levels.
	*/
	function draw_text_curve_1(){
		$prm = func_num_args()>0?func_get_arg(0):[]; //ob_clean(); echo "<pre>"; var_dump($prm); die();
		$prm = $this->shortcode_atts([
			'text'		=> 'TESTTESTTESTTESTTESTTESTTESTTEST'
			,'pos'		=> ['x'=>500,'y'=>500,'deg'=>-90]
			,'radius'	=> 425
			,'flip' 	=> 1
			,'font'		=> ['name'=>'BoonTook-Ultra.ttf','size'=>50,'color'=>'rgb(29,28,86)']
			,'stroke'	=> ['color'=>'rgba(255,255,255,1)','width'=>4]
			,'shadow'	=> ['ox'=>4,'oy'=>4,'color'=>'rgba(0,0,0,.5)']
			,'debug'	=> false
		],$prm); //ob_clean(); echo "<pre>"; print_r($prm); die();
		extract($prm,EXTR_PREFIX_ALL,'prm'); //$prm_text = 'น้ำมั่นดี่เซล DIESEL VITAL';
		$prm_font_file = $prm['font_file'] = $this->dir.'fonts/'.$prm_font['name']; //ob_clean(); var_dump($font_file); die();
		//Set parameters
		$draw = new ImagickDraw();
		$draw->setFont($prm_font_file);
		$draw->setFontSize($prm_font['size']);
		$draw->setTextAlignment(Imagick::ALIGN_LEFT); //ob_clean(); echo "<pre>"; var_dump($draw); die();
		//$draw->setGravity (Imagick::GRAVITY_CENTER);
		//$draw->setTextAlignment(Imagick::ALIGN_CENTER); //ob_clean(); echo "<pre>"; var_dump($draw); die();
		$draw->setTextAntialias(true);
		$draw->setFillColor($prm_font['color']);
		
		$ch_arr = [];
		$text = new \stdClass; $text->width = 0; $text->y = 0; $text_prev = '';
		$text->circle_circum = pi()*2*$prm_radius;
		$start_deg = 0;
		$start_x = 0;
		for($i=0;$i<mb_strlen($prm_text);$i++){
			$ch_arr[] = $ch = new \stdClass; //$ch_arr[] = $ch;
			$ch->text = mb_substr($prm_text,$i,1); //ob_clean(); echo "<pre>"; print_r($ch); die();
			//echo mb_substr($prm_text,$i+1,1); die();
			$yy = 0;
			while(mb_strpos($this->special_chars,mb_substr($prm_text,$i+1,1))!==false){
				//if(mb_strpos('ิีึื',mb_substr($prm_text,$i+1,1))!==false) $yy = $ch->metrics['boundingBox']['y2'] - $ch->metrics['boundingBox']['y1']
				$i++;
				$ch->text.=mb_substr($prm_text,$i,1);
			}
			$ch->metrics = $this->image->queryFontMetrics($draw,$ch->text,false); //ob_clean(); echo "<pre>"; print_r($ch->text_metrics); die();
			$ch->width = $ch->metrics['textWidth']; $ch->width_deg = $ch->width/$text->circle_circum*360;
			$ch->origin = new \stdClass;
			$ch->origin->x = $text->width+$ch->metrics['boundingBox']['x1'];
			$ch->origin->y = $text->y + $ch->metrics['boundingBox']['y1']; //if($ch->text=='่') $ch->origin->y+=10;
			$text->y = $ch->origin->y;
			$ch->pos = new \stdClass;
			$ch->pos->x = $text->width;// - $ch->metrics['boundingBox']['x1'];
			$ch->pos->y = $ch->metrics['boundingBox']['y1'];
			$ch->start_deg = ($ch->pos->x)/$text->circle_circum*360; //Angular location of text
			$ch->mid_deg = $ch->start_deg + $ch->width_deg/2; //Angular location of text center
			$text->width+=$ch->metrics['originX']; //Add to text width the newxt text position
			//if() $text->y = 
			$text->prev = $ch->text;
		}
		$text->width_ratio = $text->width/$text->circle_circum;
		$text->start_deg = $prm_pos['deg'] - $text->width_ratio*360/2 *$prm_flip;
		$text->start_rad = deg2rad($text->start_deg);
		foreach($ch_arr as $ch){ //ob_clean(); echo "<pre>"; print_r($ch); die();
			$ch->rad = deg2rad($text->start_deg + $ch->start_deg *$prm_flip);
			//$ch->x = cos($ch->rad)*$prm_radius + $prm_pos['x']; $ch->y = sin($ch->rad)*$prm_radius + $prm_pos['y'];
			$ch->x = cos($ch->rad)*($prm_radius) + $prm_pos['x']; $ch->y = sin($ch->rad)*($prm_radius) + $prm_pos['y'];
			$ch->deg = (90 + $ch->mid_deg)*$prm_flip + $text->start_deg;
			$this->image->annotateImage($draw,$ch->x,$ch->y,$ch->deg,$ch->text); //$cc->circle($x,$y,$x+5,$y); $this->image->drawImage($cc);
			unset($ch->metrics);
		}
		if(isset($_GET['d'])){ob_clean(); echo "<pre>"; print_r($text); print_r($ch_arr); die();}
	}
	
	/*
		20180116:Tony
			Concept:
			- Calculate exact position of each char then draw one by one.
			Issues:
			- ?
	*/
	function draw_text_curve_1a(){
		$prm = func_num_args()>0?func_get_arg(0):[]; //ob_clean(); echo "<pre>"; var_dump($prm); die();
		$prm = $this->shortcode_atts([
			'text'		=> 'TESTTESTTESTTESTTESTTESTTESTTEST'
			,'pos'		=> ['x'=>500,'y'=>500,'deg'=>-90]
			,'radius'	=> 425
			,'flip' 	=> 1
			,'font'		=> ['name'=>'BoonTook-Ultra.ttf','size'=>50,'color'=>'rgb(29,28,86)','antiAlias'=>true]
			,'stroke'	=> ['color'=>'rgba(255,255,255,1)','width'=>4]
			,'shadow'	=> ['ox'=>4,'oy'=>4,'color'=>'rgba(0,0,0,.5)']
			,'debug'	=> false
		],$prm); //ob_clean(); echo "<pre>"; print_r($prm); die();
		extract($prm,EXTR_PREFIX_ALL,'prm'); $prm_text = 'น้ำ';// 'นี่น้ำ';
		$prm_font_file = $prm['font_file'] = $this->dir.'fonts/'.$prm_font['name']; //ob_clean(); var_dump($font_file); die();
		//Set parameters
		$draw = new ImagickDraw();
		$draw->setFont($prm_font_file);
		$draw->setFontSize($prm_font['size']);
		$draw->setTextAlignment(Imagick::ALIGN_LEFT); //ob_clean(); echo "<pre>"; var_dump($draw); die();
		$draw->setTextAntialias($prm_font['antiAlias']);
		$draw->setFillColor($prm_font['color']);
		
		$ch_arr = [];
		$text = new \stdClass; $text->width = 0; $text->circle_circum = pi()*2*$prm_radius;
		$start_x = 50; $start_deg = 0;
		for($i=0;$i<mb_strlen($prm_text);$i++){
			$ch_arr[] = $ch = new \stdClass; //$ch_arr[] = $ch;
			$ch->text = mb_substr($prm_text,$i,1); //ob_clean(); echo "<pre>"; print_r($ch); die();
			$ch->metrics = $this->image->queryFontMetrics($draw,$ch->text,false); //ob_clean(); echo "<pre>"; print_r($ch->text_metrics); die();
			$ch->pos = new \stdClass; $ch->pos->x = $start_x+$text->width; $ch->pos->y = 500;
			$ch->pos1 = new \stdClass; $ch->pos1->x = $text->width; $ch->pos1->y = 500 + $ch->metrics['boundingBox']['x1'];
			$ch->width = $ch->metrics['textWidth'];
			$text->width+=$ch->metrics['originX']; //Add to text width the next text position
			$ch->bounding = new \stdClass;
			$ch->bounding->x1 = $ch->metrics['boundingBox']['x1'];  $ch->bounding->y1 = -$ch->metrics['boundingBox']['y1'];
			$ch->bounding->x2 = $ch->metrics['boundingBox']['x2'];  $ch->bounding->y2 = -$ch->metrics['boundingBox']['y2'];
			$ch->pos2 = $ch->pos;
			if(($i>0)&&($ch->text=='ำ')&&(mb_strpos('่้๊๋',$ch_arr[$i-1]->text)!==false)){//die('xx');
				$ch_arr[$i-1]->pos2->x += ($ch->bounding->x1 - $ch_arr[$i-1]->bounding->x1);
				$ch_arr[$i-1]->pos2->y += ($ch->bounding->y2*1.05 - $ch_arr[$i-1]->bounding->y1);
			}
			if(($i>0)&&(mb_strpos('่้๊๋',$ch->text)!==false)&&(mb_strpos('ิีึื',$ch_arr[$i-1]->text))){//die('xx');
				$ch->pos2->y += ($ch_arr[$i-1]->bounding->y2-$ch_arr[$i-1]->bounding->y1)*1.1;
			}
			//Center point for rotating calculation
			$ch->pos_center = new \stdClass;
			$ch->pos_center->x = $ch->pos2->x + ($ch->bounding->x2 + $ch->bounding->x1)/2;
			$ch->pos_center->y = $ch->pos2->y + ($ch->bounding->y2 + $ch->bounding->y1)/2;
			//if($i==2){echo "<pre>"; $ch->xx = mb_strpos('่้๊๋','่'); print_r($ch); die();}
		}
		foreach($ch_arr as $ch){ //ob_clean(); echo "<pre>"; print_r($ch); die();
			$x = $ch->pos2->x; $y = $ch->pos2->y; $deg = 0;
			$this->image->annotateImage($draw,$x,$y,$deg,$ch->text);
			//
			//$this->draw_circle_1(['pos'=>$ch->pos,'radius'=>4,'stroke'=>['color'=>'rgba(255,0,0,.8)','width'=>2,'antiAlias'=>true]]);
			//$this->draw_rect_1(['origin'=>$ch->pos,'bounding'=>$ch->bounding,'stroke'=>['color'=>'rgba(255,0,0,1)','width'=>1]]);
			//$this->draw_rect_1(['origin'=>$ch->pos1,'bounding'=>$ch->bounding,'stroke'=>['color'=>'rgba(255,0,0,.9)','width'=>2]]);
			//$this->draw_rect_1(['origin'=>$ch->pos,'bounding'=>$ch->bounding,'stroke'=>['color'=>'rgba(0,255,255,.9)','width'=>2]]);
			$this->draw_rect_1(['origin'=>$ch->pos2,'bounding'=>$ch->bounding,'stroke'=>['color'=>'rgba(0,255,0,.9)','width'=>2]]);
			$this->draw_circle_1(['pos'=>$ch->pos2,'radius'=>4,'stroke'=>['color'=>'rgba(0,255,0,.8)','width'=>2]]);
			$this->draw_circle_1(['pos'=>$ch->pos_center,'radius'=>4,'stroke'=>['color'=>'rgba(255,0,0,.8)','width'=>2]]);
			//unset($ch->metrics);
			//echo "<pre>"; print_r($ch); //die();
		}
		/*
		for($i=0;$i<mb_strlen($prm_text);$i++){
			$ch_arr[] = $ch = new \stdClass; //$ch_arr[] = $ch;
			$ch->text = mb_substr($prm_text,$i,1); //ob_clean(); echo "<pre>"; print_r($ch); die();
			//echo mb_substr($prm_text,$i+1,1); die();
			$yy = 0;
			while(mb_strpos($this->special_chars,mb_substr($prm_text,$i+1,1))!==false){
				//if(mb_strpos('ิีึื',mb_substr($prm_text,$i+1,1))!==false) $yy = $ch->metrics['boundingBox']['y2'] - $ch->metrics['boundingBox']['y1']
				$i++;
				$ch->text.=mb_substr($prm_text,$i,1);
			}
			$ch->metrics = $this->image->queryFontMetrics($draw,$ch->text,false); //ob_clean(); echo "<pre>"; print_r($ch->text_metrics); die();
			$ch->width = $ch->metrics['textWidth']; $ch->width_deg = $ch->width/$text->circle_circum*360;
			$ch->origin = new \stdClass;
			$ch->origin->x = $text->width+$ch->metrics['boundingBox']['x1'];
			$ch->origin->y = $text->y + $ch->metrics['boundingBox']['y1']; //if($ch->text=='่') $ch->origin->y+=10;
			$text->y = $ch->origin->y;
			$ch->pos = new \stdClass;
			$ch->pos->x = $text->width;// - $ch->metrics['boundingBox']['x1'];
			$ch->pos->y = $ch->metrics['boundingBox']['y1'];
			$ch->start_deg = ($ch->pos->x)/$text->circle_circum*360; //Angular location of text
			$ch->mid_deg = $ch->start_deg + $ch->width_deg/2; //Angular location of text center
			$text->width+=$ch->metrics['originX']; //Add to text width the newxt text position
			//if() $text->y = 
			$text->prev = $ch->text;
		}
		$text->width_ratio = $text->width/$text->circle_circum;
		$text->start_deg = $prm_pos['deg'] - $text->width_ratio*360/2 *$prm_flip;
		$text->start_rad = deg2rad($text->start_deg);
		foreach($ch_arr as $ch){ //ob_clean(); echo "<pre>"; print_r($ch); die();
			$ch->rad = deg2rad($text->start_deg + $ch->start_deg *$prm_flip);
			//$ch->x = cos($ch->rad)*$prm_radius + $prm_pos['x']; $ch->y = sin($ch->rad)*$prm_radius + $prm_pos['y'];
			$ch->x = cos($ch->rad)*($prm_radius) + $prm_pos['x']; $ch->y = sin($ch->rad)*($prm_radius) + $prm_pos['y'];
			$ch->deg = (90 + $ch->mid_deg)*$prm_flip + $text->start_deg;
			$this->image->annotateImage($draw,$ch->x,$ch->y,$ch->deg,$ch->text); //$cc->circle($x,$y,$x+5,$y); $this->image->drawImage($cc);
			unset($ch->metrics);
		}
		*/
		if(isset($_GET['d'])){ob_clean(); echo "<pre>"; print_r($text); print_r($ch_arr); die();}
	}

	function draw_img(){
		$prm = func_num_args()>0?func_get_arg(0):[]; //ob_clean(); echo "<pre>"; var_dump($prm); die();
		$prm = $this->shortcode_atts([
			'img_id'	=> 467
			,'pos'		=> ['x'=>0,'y'=>0,'deg'=>0,'align'=>['x'=>'center','y'=>'center']]
			,'size'		=> false
		],$prm); //ob_clean(); echo "<pre>"; print_r($prm); die();
		extract($prm,EXTR_PREFIX_ALL,'prm');
		$img = wp_get_attachment_image_src($prm_img_id,'full'); //ob_clean(); print_r($img); die();
		if($img===false) return;
		$src_img = $this->load_image(['file'=>$img[0]]); //ob_clean(); echo "<pre>"; var_dump($src); die();
		if($prm_size) $src_img->resizeImage($prm_size['x'],$prm_size['y'],Imagick::FILTER_LANCZOS,1);
		//Calculate relate width/height
		if($prm_pos['x']<0) $prm_pos['x']+=$this->image->getImageWidth();
		if($prm_pos['y']<0) $prm_pos['y']+=$this->image->getImageHeight();
		//Calculate alignment
		switch($prm_pos['align']['x']){
			case 'right':$pos_x = $prm_pos['x'] - $src_img->getImageWidth(); break;
			case 'center':$pos_x = $prm_pos['x'] - $src_img->getImageWidth()/2; break;
			default:$pos_x = $prm_pos['x'];
		}
		switch($prm_pos['align']['y']){
			case 'center':$pos_y = $prm_pos['y'] - $src_img->getImageHeight()/2; break;
			case 'bottom':$pos_y = $prm_pos['y'] - $src_img->getImageHeight(); break;
			default:$pos_y = $prm_pos['y'];
		} //ob_clean(); echo "<pre>"; print_r([$pos_x,$pos_y]); die();
		$this->image->compositeImage($src_img,Imagick::COMPOSITE_DEFAULT, $pos_x ,$pos_y);
		//unset($src->img); unset($src);
	}
}

//ob_clean(); var_dump($_GET); die();
$img = new gen_image();