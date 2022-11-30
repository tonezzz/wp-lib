<?php //die(__FILE__);
/*
v1.03 - 20180418:Tony
	- Add shotcode parameters price, btn_apply
v1.01 - 20180111:Tony
	- Add mv_gen_sticker shotcode.
v0.00 - 20171026:Tony
*/
class mv_gen_image extends gz_tpl{
	
	public function __construct(){//die(__FILE__.__FUNCTION__);
		$config = [
			'enqueue'  => [
				//['type'=>'localize', 'prm'=>[__CLASS__,__CLASS__,['gen_img_2'=>admin_url('admin-ajax.php')]]]
				//,['type'=>'script' ,'load'=>true ,'prm'=>[__CLASS__,'[REL_PATH]_wp_script.js',['jquery-core']]]
				//,['type'=>'style' ,'load'=>true ,'prm'=>[__CLASS__,'[REL_PATH]_wp_style.scss',[]]]
			]
			,'cmb2' => [//Don't forget to activate CMB2 plugin ;-) //event-regis
				'mv_prod_img' => [
					'prefix'		=> "_mv_"
					,'cmb2_args'		=> [
						'id'			=> 'mv_prod_img'
						,'title'		=> "Extra info"
						,'object_types' => ['product']
						,'fields'	=> [
							['id'=>'prod_img','type'=>'group','close'=>false
								,'repeatable'=>false
								,'options'=>['group_title'=>'Product image','sortable'=>true]
								,'fields'=>[
									['id'=>'raw_img','name'=>'Product image','type'=>'file']
									,['id'=>'name1','name'=>'Name 1','type'=>'text']
									,['id'=>'name2','name'=>'Name 2','type'=>'text']
									,['id'=>'desc1','name'=>'Desc 1','type'=>'text']
									,['id'=>'desc2','name'=>'Desc 2','type'=>'text']
									,['id'=>'size','name'=>'Size','type'=>'text']
								]
							]
						]
					]
				]
			]
		];
		parent::__construct($config);
		$this->get_url(__FILE__);
		$this->set_id(__CLASS__);
		$this->init_scripts();
		$this->init_shortcode(); //ob_clean(); echo "<pre>"; print_r($this); die();
		$this->image_gen_url = $this->url.'img_gen_v1.05.php';
		add_action('wp_ajax_nopriv_'.'render_img_2',[$this,'render_img_2']);
		add_action('wp_ajax_'.'render_img_2',[$this,'render_img_2']);
		add_action('wp_ajax_nopriv_'.'apply_product_image',[$this,'apply_product_image']);
		add_action('wp_ajax_'.'apply_product_image',[$this,'apply_product_image']);
	}
	
	function init_scripts(){
		//add_action('admin_enqueue_scripts',array($this,'register_scripts_admin'));
		//add_action('admin_enqueue_scripts',array($this,'enqueue_scripts_admin'));
		add_action('wp_enqueue_scripts',array($this,'register_scripts'));
		add_action('wp_enqueue_scripts',array($this,'enqueue_scripts'));
	}

	public function register_scripts_admin(){
		//wp_register_script($this->id,$url.'_wp_admin_script.js',array('jquery-core'));
		//wp_register_style($this->id,$tis->url.'_wp_style_admin.css');
	}
	
	public function enqueue_scripts_admin(){
		//wp_enqueue_script($this->id);
		wp_enqueue_style($this->id);
	}
	
	public function register_scripts(){//ob_clean(); var_dump($this,$this->url); die();
		wp_register_script($this->id,$this->url.'_wp_script.js',array('jquery-core'));
		wp_register_style($this->id,$this->url.'_wp_style.scss');
	}
	
	public function enqueue_scripts(){
		wp_enqueue_script($this->id);
		wp_enqueue_style($this->id);
	}
/*
	function shortcode_atts($default,$arr){
		$arr = shortcode_atts($default,$arr);
		foreach($default as $key=>$var) if(is_array($var)) $arr[$key] = shortcode_atts($var,$arr[$key]);
		return $arr;
	}
*/
	function init_shortcode(){//die(__FILE__.__FUNCTION__);
		add_shortcode('mv_gen_image',[$this,'render_mv_gen_image']);
		add_shortcode('mv_gen_sticker',[$this,'render_mv_gen_sticker']);
		add_shortcode('mv_gen_banner',[$this,'render_mv_gen_banner']);
		add_shortcode('mv_gen_image_2',[$this,'render_mv_gen_image_2']);
	}

	function render_mv_gen_banner($atts){ //if(isset($_GET['d'])){ob_clean(); echo '<pre>'; var_dump($atts); die();}
		$atts = $this->shortcode_atts([
			'bg_img_id'			=> 704
			,'logo_round_id'	=> 703
			,'logo_text_id'		=> 702
			,'area_title'		=> false
		],$atts,'mv_gen_image');  //if(isset($_GET['d'])){ob_clean(); echo '<pre>'; var_dump($atts); die();}
		extract($atts,EXTR_PREFIX_ALL,'att');
		$bg_image = wp_get_attachment_image_src($atts['bg_img_id'],'full'); //ob_clean(); print_r($bg_image); die();
		$font_normal = "BoonTook-Ultra.ttf";
		$area_title = $att_area_title?"มอเตอร์วีทาล  ".$att_area_title:'';
		$img = [
			['c2','prm'=>['format'=>'jpeg','file'=>$bg_image[0],'w'=>$bg_image[1],'h'=>$bg_image[2]]] //Create from template file
			,['t3','prm'=>['text'=>$area_title,'pos'=>['x'=>1920/2,'y'=>340,'align'=>Imagick::ALIGN_CENTER],'font'=>['name'=>$font_normal,'size'=>70,'angle'=>0,'color'=>'rgb(255,255,255)'] ,'shadow'=>['ox'=>4,'oy'=>4,'color'=>'rgba(0,0,0,.5)']]]
			,['img','prm'=>['img_id'=>$att_logo_text_id,'pos'=>['x'=>960,'y'=>100,'deg'=>0,'align'=>['x'=>'center','y'=>'top']]]]
			,['img','prm'=>['img_id'=>$att_logo_round_id,'pos'=>['x'=>-70,'y'=>70,'deg'=>0,'align'=>['x'=>'right','y'=>'top']],'size'=>['x'=>200,'y'=>200]]]
			
		];
		$img_s = serialize($img); //ob_clean(); echo "<pre>"; print_r($img); die();
		$prm = ['seq' => $img_s ,'filename' => 'fb_cover.jpg'];
		$img_url = add_query_arg($prm,$this->image_gen_url); $img_url = site_url($img_url); //ob_clean(); echo "<a href='{$img_url}'><img src='{$img_url}'/></a>"; die();
		//return $img_url;
		return $this->render_dom([
			'dom'		=> ['type'=>'div' ,'class'=>'banner_img']
			,'attr'=>[
				//'data-prod-id'		=> $att_product_id
				//,'data-site-url'	=> site_url()
				//,'data-tpl-url'			=> $this->url
				//,'data-img-name'	=> $img_name
			]
			,'content'	=> $this->render_dom([
				'dom'	=> ['type'=>'a' ,'class'=>'img']
				,'attr'	=> ['href'=>$img_url]
				,'content'	=> $this->render_dom([
					'dom'=>['type'=>'img' ,'class'=>"mv_gen_banner"]
					//,'attr'=>['src'=>$img_url ,'style'=>"width:600px; height:600px;"]
					,'attr'=>['src'=>$img_url]
				])
			])//.$this->render_dom(['dom'=>['type'=>'a' ,'class'=>'button apply'] ,'attr'=>['href'=>'#'] ,'content'=>'Apply'])
		]);
	}
		
	function render_mv_gen_image($atts){ //if(isset($_GET['d'])){ob_clean(); echo '<pre>'; var_dump($atts); die();}
		$atts = $this->shortcode_atts([
			'product_id'	=> 190
			,'image_id'		=> 388
			,'name1'		=> 'BENZINEX'
			,'name2'		=> 'Premium Additive'
			,'desc1'		=> 'หัวเชื้อน้ำมัน'
			,'desc2'		=> 'เชื้อเพลิงเบนซิน'
			,'size'			=> 'ขนาด 250ml'
			,'qty'			=> ''
			,'note'			=> ''
			,'price'		=> false
			,'btn_apply'	=> true
		],$atts,'mv_gen_image'); //if(isset($_GET['d'])){ob_clean(); echo '<pre>'; var_dump($atts); die();}
		extract($atts,EXTR_PREFIX_ALL,'att');
		//Get product info
		if($att_price){
			if(empty($att_price)) $price_st = "X,XXX.-"; else $price_st = number_format($att_price,0).'.-';
		}elseif(0!=$att_product_id){
			$product = wc_get_product( $att_product_id ); //if(isset($_GET['d'])){ob_clean(); echo '<pre>'; var_dump(get_post_meta( $att_product_id, '_regular_price', true),$product->price,$product->get_price(),$product); die();}
			//$price = $product->get_price(); $price.='.-';
			//$price = get_post_meta( $att_product_id, '_regular_price', true);
			$price = get_post_meta( $att_product_id, '_price', true);
			if(empty($price)) $price_st = "X,XXX.-"; else $price_st = number_format($price,0).'.-';
		}else{
			$price_st = 'X,XXX.-';
		}
		$product_image = wp_get_attachment_image_src($atts['image_id'],[1200,1200]); //print_r($product_image); die();
		$product_image = urlencode($product_image[0]);
		//$product_image = 'https://www.motorvital.co.th/wp/wp-content/uploads/2017/12/Motor-Vital-Benzine-Premium-Additive-250ml.jpg';
		//$product_image = '6.jpg';
		//
		$circle_sx = 431; $circle_sy = 437; $circle_x = 1200-$circle_sx-100; $circle_y = 80; $circle_cx = $circle_x+$circle_sx/2; $circle_cy = $circle_y+$circle_sy/2;
		$font_name1 = "BoonTook-Ultra.ttf";
		$font_name2 = "BoonTook-Ultra.ttf";
		$font_price = "BoonTook-Ultra.ttf";
		$font_normal = "Chonburi-Regular.ttf";
		$font_qty = "BoonTook-Ultra.ttf";
		$font_note = "BoonTook-Ultra.ttf";
		$img = [
				//['c1',$product_image,1200,1200] //Create from template file
				['c3','prm'=>['bg_color'=>'rgb(238,238,238)','file'=>$product_image,'w'=>1200,'h'=>1200]] //Create from template file
				,['p1','logo.png',0,0,213,45,25,25,213,45] //Past from template file (entire file onto pos(10,10), size(20%,20%) of destination image size
				,['p1','curve.png',0,0,1200,238,0,1200-238,1200,238] //Past from template file (entire file onto pos(10,10), size(20%,20%) of destination image size
				,['p1','circle.png',0,0,$circle_sx,$circle_sy,$circle_x,$circle_y,$circle_sx,$circle_sy] //Past from template file (entire file onto pos(10,10), size(20%,20%) of destination image size
				//,['t3','prm'=>['text'=>$att_name1,'pos'=>['x'=>$circle_cx,'y'=>$circle_y+110,'align'=>Imagick::ALIGN_CENTER],'font'=>['name'=>$font_name1,'size'=>70,'angle'=>0,'color'=>'rgb(29,28,86)'],'stroke'=>['color'=>'rgba(255,255,255,1)','width'=>4],'shadow'=>['ox'=>4,'oy'=>4,'color'=>'rgba(0,0,0,.5)'],'debug'=>false]]
				,['t3','prm'=>['text'=>$att_name1,'pos'=>['x'=>$circle_cx,'y'=>$circle_y+110,'align'=>Imagick::ALIGN_CENTER],'font'=>['name'=>$font_name1,'size'=>35,'angle'=>0,'color'=>'rgb(29,28,86)'],'stroke'=>['color'=>'rgba(255,255,255,1)','width'=>3],'shadow'=>['ox'=>4,'oy'=>4,'color'=>'rgba(0,0,0,.5)'],'debug'=>false]]
				//,['t3','prm'=>['text'=>$att_name2,'pos'=>['x'=>$circle_cx,'y'=>$circle_y+160,'align'=>Imagick::ALIGN_CENTER],'font'=>['name'=>$font_name2,'size'=>40,'angle'=>0,'color'=>'rgb(29,28,86)'],'stroke'=>['color'=>'rgb(255,255,255)','width'=>2],'shadow'=>['ox'=>3,'oy'=>3,'color'=>'rgba(0,0,0,.5)']]]
				,['t3','prm'=>['text'=>$att_name2,'pos'=>['x'=>$circle_cx,'y'=>$circle_y+160,'align'=>Imagick::ALIGN_CENTER],'font'=>['name'=>$font_name2,'size'=>32,'angle'=>0,'color'=>'rgb(29,28,86)'],'shadow'=>['ox'=>3,'oy'=>3,'color'=>'rgba(0,0,0,.5)']]]
				//,['t3','prm'=>['text'=>$att_name2,'pos'=>['x'=>$circle_cx,'y'=>$circle_y+160,'align'=>Imagick::ALIGN_CENTER],'font'=>['name'=>$font_name2,'size'=>40,'angle'=>0,'color'=>'rgb(29,28,86)'],'shadow'=>['ox'=>2,'oy'=>2,'color'=>'rgba(0,0,0,.5)']]]
				,['t3','prm'=>['text'=>$price_st,'pos'=>['x'=>$circle_cx+10,'y'=>$circle_y+270,'align'=>Imagick::ALIGN_CENTER],'font'=>['name'=>$font_price,'size'=>130,'angle'=>0,'color'=>'rgb(29,28,86)'],'stroke'=>['color'=>'rgb(255,255,255)','width'=>5],'shadow'=>['ox'=>4,'oy'=>4,'color'=>'rgba(0,0,0,.5)']]]
				,['t3','prm'=>['text'=>$att_desc1,'pos'=>['x'=>$circle_cx,'y'=>$circle_y+320,'align'=>Imagick::ALIGN_CENTER],'font'=>['name'=>$font_normal,'size'=>30,'angle'=>0,'color'=>'rgb(29,28,86)']]]
				,['t3','prm'=>['text'=>$att_desc2,'pos'=>['x'=>$circle_cx,'y'=>$circle_y+350,'align'=>Imagick::ALIGN_CENTER],'font'=>['name'=>$font_normal,'size'=>30,'angle'=>0,'color'=>'rgb(29,28,86)']]]
				,['t3','prm'=>['text'=>$att_size,'pos'=>['x'=>$circle_cx,'y'=>$circle_y+$circle_sy+40,'align'=>Imagick::ALIGN_CENTER],'font'=>['name'=>$font_normal,'size'=>40,'angle'=>0,'color'=>'rgb(29,28,86)']]]
		];
		//if($att_qty!='') $img[] = ['t3','prm'=>['text'=>$att_qty,'pos'=>['x'=>550,'y'=>1100,'align'=>Imagick::ALIGN_LEFT],'font'=>['name'=>$font_qty,'size'=>300,'angle'=>0,'color'=>'rgb(29,28,86)'],'stroke'=>['color'=>'rgba(255,255,255,1)','width'=>8]]];
		if($att_qty!='') $img[] = ['t3','prm'=>['text'=>$att_qty,'pos'=>['x'=>$circle_cx+20,'y'=>900,'align'=>Imagick::ALIGN_CENTER],'font'=>['name'=>$font_qty,'size'=>260,'angle'=>0,'color'=>'rgb(29,28,86)'],'stroke'=>['color'=>'rgba(255,255,0,1)','width'=>8]]];
		if($att_note!='') $img[] = ['t3','prm'=>['text'=>$att_note,'pos'=>['x'=>$circle_cx+20,'y'=>950,'align'=>Imagick::ALIGN_CENTER],'font'=>['name'=>$font_qty,'size'=>40,'angle'=>0,'color'=>'rgb(255,0,0)']]];
		$img_s = serialize($img); //ob_clean(); echo "<pre>"; print_r($img); die();
		$prm = ['seq' => $img_s];
		//$site_url = site_url();
		$img_title = "Motor Vital - {$att_name1} {$att_name2} {$att_size} - {$price_st}"; $img_name = sanitize_title($img_title).'.jpg';
		$prm['filename'] = $img_title.'.jpg';
		//$prm['btn_apply']	= $att_btn_apply;
		$img_url = add_query_arg($prm,$this->image_gen_url); $img_url = site_url($img_url); //ob_clean(); echo "<a href='{$img_url}'><img src='{$img_url}'/></a>"; die();
		//administrator, manage_options
		$btn_apply_html = (current_user_can('administrator')&&$att_btn_apply)?$this->render_dom(['dom'=>['type'=>'a' ,'class'=>'button apply'] ,'attr'=>['href'=>'#'] ,'content'=>'Apply']):'';
		return $this->render_dom([
			'dom'		=> ['type'=>'div' ,'class'=>'prod_img']
			,'attr'=>[
				'data-prod-id'		=> $att_product_id
				,'data-site-url'	=> site_url()
				,'data-tpl-url'		=> $this->url
				,'data-img-name'	=> $img_name
				,'data-btn-apply'	=> $att_btn_apply
			]
			,'content'	=> $this->render_dom([
				'dom'	=> ['type'=>'a' ,'class'=>'img']
				,'attr'	=> ['href'=>$img_url]
				,'content'	=> $this->render_dom([
					'dom'=>['type'=>'img' ,'class'=>"mv_gen_image"]
					,'attr'=>['src'=>$img_url ,'style'=>"width:600px; height:600px;"]
				])
			]).$btn_apply_html
		]);
	}

	function render_mv_gen_image_2($atts,$content,$shortcode){//echo '<pre>'; print_r(compact('atts','content','shortcode')); die();
		$pre = '_mv_';
		$atts = $this->shortcode_atts([
			'product_id'	=> 0
			//,'image_id'		=> 388
			,'name1'		=> 'BENZINEX'
			,'name2'		=> 'Premium Additive'
			,'desc1'		=> 'หัวเชื้อน้ำมัน'
			,'desc2'		=> 'เชื้อเพลิงเบนซิน'
			,'size'			=> 'ขนาด 250ml'
			,'qty'			=> ''
			,'note'			=> ''
			,'price'		=> false
			,'btn_apply'	=> true
		],$atts,'mv_gen_image'); //if(isset($_GET['d'])){ob_clean(); echo '<pre>'; var_dump($atts); die();}
		extract($atts,EXTR_PREFIX_ALL,'att');
		//Get list of products
		$arg = [
			//'author'		=> $user->ID
			'post_type'	=> 'product'
			,'orderby'		=> 'post_date'
			,'order'		=> 'desc'
			,'numberposts'	=> -1
			,'meta_query'	=> [['key'=>$pre.'prod_img','compare'=>'EXISTS']]
		];
		$posts = get_posts($arg); //ob_clean(); echo '<pre>'; print_r(compact('arg','posts')); die();
		$html = '';
		$html.="<table class='gen_img_2'>";
		foreach($posts as $post){
			$html.= "<tr>";
				//Current product image
				$img = wp_get_attachment_image_src(get_post_thumbnail_id( $post->ID ),[1200,1200]);
				$img_url = $img[0];
				$html.= "<td class='img1'><span>Current image</span><img class='prod_img' src='{$img_url}'></td>";
				//$new_img_url = get_post_meta($post->ID,$pre.'prod_img',true);
				if($att_price){
					if(empty($att_price)) $price_st = "X,XXX.-"; else $price_st = number_format($att_price,0).'.-';
				}elseif(0==$att_product_id){
					$product = wc_get_product( $post->ID ); //if(isset($_GET['d'])){ob_clean(); echo '<pre>'; var_dump(get_post_meta( $att_product_id, '_regular_price', true),$product->price,$product->get_price(),$product); die();}
-					$price = $product->get_price();
					if(empty($price)) $price_st = "X,XXX.-"; else $price_st = number_format($price,0).'.-';
				}else{
					$price_st = 'X,XXX.-';
				}
				$prm = ['action'=>'render_img_2'];
				$circle_sx = 431; $circle_sy = 437; $circle_x = 1200-$circle_sx-100; $circle_y = 80; $circle_cx = $circle_x+$circle_sx/2; $circle_cy = $circle_y+$circle_sy/2;
				$font_name1 = "BoonTook-Ultra.ttf";
				$font_name2 = "BoonTook-Ultra.ttf";
				$font_price = "BoonTook-Ultra.ttf";
				$font_normal = "Chonburi-Regular.ttf";
				$font_qty = "BoonTook-Ultra.ttf";
				$font_note = "BoonTook-Ultra.ttf";
				$prod_images = get_post_meta($post->ID,$pre.'prod_img',true); //ob_clean(); echo '<pre>'; var_dump($prod_img); die();
				$prod_image = $prod_images[0];
				extract($prod_image,EXTR_PREFIX_ALL,'att');
				$new_img = wp_get_attachment_image_src($att_raw_img_id,'original');
				$new_img_url = $new_img[0];
				$html.= "<td class='img2'><span>New image</span><img class='prod_img' src='{$new_img_url}'></td>";
				$img = [
					//['c1',$product_image,1200,1200] //Create from template file
					['c3','prm'=>['bg_color'=>'rgb(238,238,238)','file'=>$new_img_url,'w'=>1200,'h'=>1200,'fit'=>true]] //Create from template file
					,['p1','logo.png',0,0,213,45,25,25,213,45] //Past from template file (entire file onto pos(10,10), size(20%,20%) of destination image size
					,['p1','curve.png',0,0,1200,238,0,1200-238,1200,238] //Past from template file (entire file onto pos(10,10), size(20%,20%) of destination image size
					,['p1','circle.png',0,0,$circle_sx,$circle_sy,$circle_x,$circle_y,$circle_sx,$circle_sy] //Past from template file (entire file onto pos(10,10), size(20%,20%) of destination image size
					,['t3','prm'=>['text'=>$att_name1,'pos'=>['x'=>$circle_cx,'y'=>$circle_y+110,'align'=>Imagick::ALIGN_CENTER],'font'=>['name'=>$font_name1,'size'=>35,'angle'=>0,'color'=>'rgb(29,28,86)'],'stroke'=>['color'=>'rgba(255,255,255,1)','width'=>3],'shadow'=>['ox'=>4,'oy'=>4,'color'=>'rgba(0,0,0,.5)'],'debug'=>false]]
					,['t3','prm'=>['text'=>$att_name2,'pos'=>['x'=>$circle_cx,'y'=>$circle_y+160,'align'=>Imagick::ALIGN_CENTER],'font'=>['name'=>$font_name2,'size'=>32,'angle'=>0,'color'=>'rgb(29,28,86)'],'shadow'=>['ox'=>3,'oy'=>3,'color'=>'rgba(0,0,0,.5)']]]
					,['t3','prm'=>['text'=>$price_st,'pos'=>['x'=>$circle_cx+10,'y'=>$circle_y+270,'align'=>Imagick::ALIGN_CENTER],'font'=>['name'=>$font_price,'size'=>130,'angle'=>0,'color'=>'rgb(29,28,86)'],'stroke'=>['color'=>'rgb(255,255,255)','width'=>5],'shadow'=>['ox'=>4,'oy'=>4,'color'=>'rgba(0,0,0,.5)']]]
					,['t3','prm'=>['text'=>$att_desc1,'pos'=>['x'=>$circle_cx,'y'=>$circle_y+320,'align'=>Imagick::ALIGN_CENTER],'font'=>['name'=>$font_normal,'size'=>30,'angle'=>0,'color'=>'rgb(29,28,86)']]]
					,['t3','prm'=>['text'=>$att_desc2,'pos'=>['x'=>$circle_cx,'y'=>$circle_y+350,'align'=>Imagick::ALIGN_CENTER],'font'=>['name'=>$font_normal,'size'=>30,'angle'=>0,'color'=>'rgb(29,28,86)']]]
					,['t3','prm'=>['text'=>$att_size,'pos'=>['x'=>$circle_cx,'y'=>$circle_y+$circle_sy+40,'align'=>Imagick::ALIGN_CENTER],'font'=>['name'=>$font_normal,'size'=>40,'angle'=>0,'color'=>'rgb(29,28,86)']]]
				];
				$img_s = serialize($img); //ob_clean(); echo "<pre>"; print_r($img); die();
				//$prm = ['seq' => $img_s];
				$prm = ['seq' => urlencode($img_s)];
				//$site_url = site_url();
				//$img_title = "Motor Vital - {$att_name1} {$att_name2} {$att_size} - {$price_st}"; $img_name = sanitize_title($img_title).'.jpg';
				//$prm['filename'] = $img_title.'.jpg';
				//$prm['btn_apply']	= $att_btn_apply;
				$img_url = add_query_arg($prm,$this->image_gen_url); $img_url = site_url($img_url); //ob_clean(); echo "<a href='{$img_url}'><img src='{$img_url}'/></a>"; die();
				$btn_html = $this->render_btn([
					'title'	=> 'Apply'
					,'prod_id'	=> $post->ID
					,'img_url'	=> $img_url
				]);
				$html.= "<td class='img3' rowspan='2'><span>Test image</span><img class='prod_img' src='{$img_url}'>{$btn_html}</td>";
				$html.= "</tr><tr><td class='txt' colspan='2'>{$post->post_title}</td>";
			$html.= "</tr>";
		}
		$html.="</table>";
		return $html;
	}

	function render_btn($prm){
		extract($prm,EXTR_PREFIX_ALL,'prm');
		$html = '';
		$prm = ['action'=>'apply_product_image','prod_id'=>$prm_prod_id,'img_url'=>$prm_img_url];
		$url = add_query_arg($prm,site_url().'/wp-admin/admin-ajax.php'); //ob_clean(); echo '<pre>'; print_r($img_url); print_r(site_url()); die();
		$html.= "<a class='gen_image button apply' href='{$url}'>{$prm_title}</a>";
		return $html;
	}

	function apply_product_image(){//ob_clean(); echo '<pre>'; print_r($_GET); die();
		extract($_GET,EXTR_PREFIX_ALL,'get');
		$post_id = $get_prod_id;
		$image_url = $get_img_url;
		$image_name = empty($get_img_name)?'product-image.jpg':$get_img_name;
		$upload_dir = wp_upload_dir(); // Set upload folder
		
		$req = parse_url($image_url); //echo "<pre>"; print_r($req); die();
		$ch = curl_init();
		$url = $req['scheme'].'://'.$req['host'].$req['path']; //echo $url.'?'.$req['query']; die();
		//$url = $req['scheme'].'://'.$req['host'].$req['path'].'?'.$req['query']; ob_clean(); echo $url; die();
		//$req = stripslashes($req['query']);
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_POST, 1);
		//curl_setopt($ch, CURLOPT_POSTFIELDS,$req['query']);
		curl_setopt($ch, CURLOPT_POSTFIELDS,stripslashes($req['query']));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$image_data = curl_exec($ch);
		$info = curl_getinfo($ch); $errno = curl_errno($ch); //ob_clean(); echo '<pre>'; print_r(compact('info','errno','image_data')); die();
		curl_close($ch);
		
		/*
		$image_data = file_get_contents($image_url);
		*/
		//ob_clean(); echo $image_data; die();
		//
		$unique_file_name = wp_unique_filename( $upload_dir['path'], $image_name ); // Generate unique name
		$filename         = basename( $unique_file_name ); // Create image file name
		// Check folder permission and define file location
		if( wp_mkdir_p( $upload_dir['path'] ) ) {
			$file = $upload_dir['path'] . '/' . $filename;
		} else {
			$file = $upload_dir['basedir'] . '/' . $filename;
		}
		// Create the image  file on the server
		file_put_contents( $file, $image_data );
		// Check image file type
		$wp_filetype = wp_check_filetype( $filename, null );
		// Set attachment data
		$attachment = array(
			'post_mime_type' => $wp_filetype['type'],
			'post_title'     => sanitize_file_name( $filename ),
			'post_content'   => '',
			'post_status'    => 'inherit'
		);
		// Create the attachment
		$attach_id = wp_insert_attachment( $attachment, $file, $post_id );
		// Include image.php
		require_once(ABSPATH . 'wp-admin/includes/image.php');
		// Define attachment metadata
		$attach_data = wp_generate_attachment_metadata( $attach_id, $file );
		// Assign metadata to attachment
		wp_update_attachment_metadata( $attach_id, $attach_data );
		// And finally assign featured image to post
		set_post_thumbnail( $post_id, $attach_id );
		echo json_encode(['result'=>'ok']);
		die();
	}

	function render_img_2(){
		//$img_type = 'image/jpeg'; header("Content-Type: {$img_type}");
		return file_get_contents('http://www.motorvital.co.th.local/wp/wp-content/uploads/2017/11/2537197.png');
	}

	function render_mv_gen_sticker($atts){ //if(isset($_GET['d'])){ob_clean(); echo '<pre>'; var_dump($atts); die();}
		$atts = $this->shortcode_atts([
			'format'	=> 'png'
			,'bg_color'	=> 'rgb(0,0,255)'
			,'color'	=> 'rgb(255,255,255)'
			,'ct1'		=> 'DIESEL VITAL 100'
			//,'ct2'		=> 'MIX WITH DIESEL FUEL'
			,'ct2'		=> 'ใช้ร่วมกับน้ำมันเชื้อเพลิง ดีเซล'
			,'t1'		=> 'FOR'
			,'t2'		=> 'DIESEL'
			,'t3'		=> 'ดีเซล'
		],$atts,'mv_gen_sticker');// if(isset($_GET['d'])){ob_clean(); echo '<pre>'; var_dump($atts); die();}
		extract($atts,EXTR_PREFIX_ALL,'att');

		//$font = "BoonTook-Ultra.ttf";
		$font = "Kanit-Regular.ttf";
		//$font = "Mitr-Bold.ttf";
		$img = [
				['c2','prm'=>['format'=>$att_format,'w'=>1000,'h'=>1000]] //Create from template file
				,['circle','prm'=>['pos'=>['x'=>500,'y'=>500],'radius'=>496],'stroke'=> ['color'=>'rgba(255,255,255,1)','width'=>4,'antiAlias'=>true]]
				,['circle','prm'=>['pos'=>['x'=>500,'y'=>500],'radius'=>400],'stroke'=> ['color'=>'rgba(255,255,255,1)','width'=>4,'antiAlias'=>true]]
				,['tc1','prm'=>['text'=>$att_ct1,'pos'=>['x'=>500,'y'=>500,'deg'=>-90],'radius'=>415,'font'=>['name'=>$font,'size'=>100,'color'=>'rgb(255,255,255)']]]
				//,['tc1','prm'=>['text'=>$att_ct2,'pos'=>['x'=>500,'y'=>500,'deg'=>90],'radius'=>480,'font'=>['name'=>$font,'size'=>80,'color'=>'rgb(255,255,255)'],'flip'=>-1]]
				//,['t3','prm'=>['text'=>$att_t1,'pos'=>['x'=>500,'y'=>500-160,'align'=>Imagick::ALIGN_CENTER],'font'=>['name'=>$font,'size'=>210,'angle'=>0,'color'=>'rgb(255,255,255)']]]
				//,['t3','prm'=>['text'=>$att_t2,'pos'=>['x'=>500,'y'=>500-10,'align'=>Imagick::ALIGN_CENTER],'font'=>['name'=>$font,'size'=>180,'angle'=>0,'color'=>'rgb(255,255,255)']]]
				//,['t3','prm'=>['text'=>$att_t3,'pos'=>['x'=>500,'y'=>500+250,'align'=>Imagick::ALIGN_CENTER],'font'=>['name'=>$font,'size'=>180,'angle'=>0,'color'=>'rgb(255,255,255)']]]
				//,['line','prm'=>['x1'=>500-380,'y1'=>550,'x2'=>500+380,'y2'=>550,'stroke'=>['color'=>'rgba(255,255,255,1)','width'=>4,'antiAlias'=>true]]]
		];
		//if($att_qty!='') $img[] = ['t3','prm'=>['text'=>$att_qty,'pos'=>['x'=>$circle_cx+20,'y'=>900,'align'=>Imagick::ALIGN_CENTER],'font'=>['name'=>$font_qty,'size'=>260,'angle'=>0,'color'=>'rgb(29,28,86)'],'stroke'=>['color'=>'rgba(255,255,0,1)','width'=>8]]];
		//if($att_note!='') $img[] = ['t3','prm'=>['text'=>$att_note,'pos'=>['x'=>$circle_cx+20,'y'=>950,'align'=>Imagick::ALIGN_CENTER],'font'=>['name'=>$font_qty,'size'=>40,'angle'=>0,'color'=>'rgb(255,0,0)']]];
		$img_s = serialize($img); //ob_clean(); echo "<pre>"; print_r($img); die();
		$img_title = $att_ct1; $img_name = sanitize_title($img_title).'.'.$att_format;
		$prm = ['seq'=>$img_s ,'name'=>$img_name];
		$img_url = add_query_arg($prm,$this->image_gen_url); $img_url = site_url($img_url); //ob_clean(); echo "<a href='{$img_url}'><img src='{$img_url}'/></a>"; die();
		return $this->render_dom([
			'dom'		=> ['type'=>'div' ,'class'=>'prod_img']
			,'attr'=>[
				//'data-prod-id'		=> $att_product_id
				'data-site-url'	=> site_url()
				,'data-tpl-url'			=> $this->url
				,'data-img-name'	=> $img_name
			]
			,'content'	=> $this->render_dom([
				'dom'	=> ['type'=>'a' ,'class'=>'img']
				,'attr'	=> ['href'=>$img_url]
				,'content'	=> $this->render_dom([
					'dom'=>['type'=>'img' ,'class'=>"mv_gen_image"]
					,'attr'=>['src'=>$img_url ,'style'=>"width:600px; height:600px;"]
				])
			]).$this->render_dom(['dom'=>['type'=>'a' ,'class'=>'button apply'] ,'attr'=>['href'=>'#'] ,'content'=>'Apply'])
		]);
	}
}
