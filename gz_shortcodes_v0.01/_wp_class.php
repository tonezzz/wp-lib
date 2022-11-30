<?php //die(__FILE__);
/*
* Note:
* 	Perform Caching
* v0.00 - 20200406:Tony
*/

class gz_shortcodes extends gz_tpl{
	public function __construct(){//die(__FILE__.__FUNCTION__);
		$config = [
			'filters' => [
				['prm'=>['script_loader_tag',[$this,'script_loader_tag'],10,3]]
				,['prm'=>['upload_mimes',[$this,'upload_mimes'],1,1]]
			]
			,'actions' => [
				['prm'=>['admin_init',[$this,'init_vc']]]
				,['prm'=>['vc_before_init',[$this,'init_gz_3d_vc']]]
			]
			,'enqueue'  => [
				//For 3D
				['type'=>'script' ,'load'=>true
					,'prm'=>['model_viewer','https://unpkg.com/@google/model-viewer/dist/model-viewer.min.js']
					,'tags'=>['type'=>'module']
					//,'debug'=>true
				]
				//['type'=>'style' ,'load'=>true ,'prm'=>[__CLASS__,'[REL_PATH]wp_style.scss']]
				//,['type'=>'script' ,'load'=>true ,'prm'=>[__CLASS__,'[REL_PATH]wp_script_5.js']]
			]
			,'shortcodes' => [
				['prm'=>['nsp_tech_dropdown',[$this,'render_nsp_tech_dropdown']]]
				,['prm'=>['gz_3d',[$this,'render_gz_3d']]]
			]
		]; //ob_clean(); echo '<pre>'; print_r($config); die();
		parent::__construct($config); //ob_clean(); echo '<pre>'; print_r($config); die();
		//$this->init_vc();
		//die('<pre>'.print_r($GLOBALS['wp_filter'],true));
		//$this->init_nsp_3d_vc(); //This goes into _wp_init.php for now.
		//add_action('vc_before_init',[$this,'init_gz_3d_vc']);
	}

	/**
	 * Add file picker shartcode param.
	 *
	 * @param array $settings   Array of param seetings.
	 * @param int   $value      Param value.
	 * https://stackoverflow.com/questions/36195200/visual-composer-attach-media-file-needed/40903883
	 */
	function init_vc(){//die(__FUNCTION__);
		//$this->init_gz_3d_vc();
		//add_action('vc_before_init',[$this,'init_gz_3d_vc']);
		if(function_exists('vc_add_shortcode_param')) vc_add_shortcode_param( 'file_picker', [$this,'file_picker_settings_field'], site_url().$this->url.'vc_file_picker.js' );
	}
	/**
	 * Moved to _wp_init() until I can solve this loading puzzle.
	 */
	public function init_gz_3d_vc(){//die(__FUNCTION__);
		if(!function_exists('vc_map')) return;
		$map = [
			'category'	=> 'Gizmo'
			,'name' 	=> 'GZ 3D'
			,'base'		=> 'gz_3d'
			,'description'=> 'gz 3D'
			,'class'	=> 'gz_3d'
			,'front_enqueue_js'	=> 'model_viewer'
			,'params'	=> [
				//['param_name'=>'src' ,'type'=>'attach_image' ,'heading'=>'src' ,'value'=>'' ,'description'=>'3D File']
				['type'=>'file_picker' ,'class'=>'' ,'heading'=>'Select 3D file' ,'param_name'=>'media_id' ,'value'=>'' ,'description'=>'3D File']
				,['type'=>'textfield' ,'class'=>'' ,'heading'=>'Width' ,'param_name'=>'width' ,'value'=>'' ,'description'=>'In px (default), %, vh, vw']
				,['type'=>'textfield' ,'class'=>'' ,'heading'=>'Height' ,'param_name'=>'height' ,'value'=>'' ,'description'=>'In px (default), %, vh, vw']
			]
		]; //ob_clean(); die('<pre>'.print_r(compact('map'),true));
		vc_map($map);
	}
	function file_picker_settings_field( $settings, $value ) {
		$output = '';
		$select_file_class = '';
		$remove_file_class = ' hidden';
		$attachment_url = wp_get_attachment_url( $value );
		if ( $attachment_url ) {
			$select_file_class = ' hidden';
			$remove_file_class = '';
		}
		$output .= '<div class="file_picker_block">
					<div class="' . esc_attr( $settings['type'] ) . '_display">' .
					  $attachment_url .
					'</div>
					<input type="hidden" name="' . esc_attr( $settings['param_name'] ) . '" class="wpb_vc_param_value wpb-textinput ' .
					 esc_attr( $settings['param_name'] ) . ' ' .
					 esc_attr( $settings['type'] ) . '_field" value="' . esc_attr( $value ) . '" />
					<button class="button file-picker-button' . $select_file_class . '">Select File</button>
					<button class="button file-remover-button' . $remove_file_class . '">Remove File</button>
				  </div>
				  ';
		return $output;
	}
	
	/**
	 * Currently using define('ALLOW_UNFILTERED_UPLOADS',true);
	 */
	function upload_mimes($mime_types){
		$mime_types['glb'] = 'model/gltf-binary'; //die('<pre>'.print_r($mime_types,true));
		return $mime_types;
	}
	function script_loader_tag($tag,$handle,$source){ //die($handle);
		if('model_viewer'==$handle) {
			//$tag = "<script type='module' id='{$handle}-js' src='{$source}'></script>\n";
			$tag = "<script type='module' src='".esc_url($source)."' id='{$handle}'></script>\n";
			//die("\n".$tag);
		} 
		return $tag;
	}
	 
	/**
	 */
	function render_gz_3d($atts ,$content=null){ //return "xxx"; //die(__FUNCTION__);
		$atts = $this->shortcode_atts([
			'media_id'			=> false
			,'loading'			=> false
			,'camera_controls'	=> true
			,'auto_rotate'		=> true
			,'poster'			=> false
			,'src'				=> false
			,'alt'				=> false
			,'class'			=> ''
			,'width'			=> '100%'
			,'height'			=> '50vh' //Use this as default, because if missing it shows 0 height.
		],$atts,'gz_3d'); //ob_clean(); echo '<pre>'; var_dump($atts); die();
		$menu_data = $content;
		extract($atts,EXTR_PREFIX_ALL,'att'); //ob_clean(); die('<pre>'.print_r(compact('att_id'),true));
		$html = '';
		$html.= "<model-viewer";
		//Class
		$class_name = "gz-3d-{$att_media_id}";
		$html.=" class='{$att_class} {$class_name}'";
		//
		if($att_media_id)	$html.=" id='gz_3d_{$att_media_id}'";
		$att_src = wp_get_attachment_url($att_media_id);
		if($att_src) 		$html.=" src='{$att_src}'";
		if($att_alt) 		$html.=" alt='{$att_alt}'";
		if($att_loading) 	$html.=" loading='{$att_loading}'";
		if($att_camera_controls)$html.=" camera-controls";
		if($att_auto_rotate)$html.=" auto-rotate";
		if($att_poster) 	$html.=" class='{$att_class}'";
		$html.= " touch-action='pan-y'";
		$html.= "></model-viewer>";  //return $html; //ob_clean(); echo '<pre>'; echo $html; die(__FUNCTION__);
		//Style
		$size = '';
		if(!empty($att_height)) $size.= 'height:'.$att_height.((strpos('px',$att_height)||strpos('%',$att_height)||strpos('vh',$att_height)||strpos('vw',$att_height))?'px;':';');
		if(!empty($att_width)) 	$size.= 'width:'.$att_width.((strpos('px',$att_width)||strpos('%',$att_width)||strpos('vh',$att_width)||strpos('vw',$att_width))?'px;':';');
		//ob_clean(); die('<pre>'.print_r(compact('att_height','att_width','size'),true));
		if(!empty($size)) $html.="<style>.{$class_name} {{$size}}</style>";
		return "\n".$html."\n";
	}

/*
[nsp_tech_dropdown scroll_duration=1500]
SURF>Surf(PU,CSE,Protech>Protech,ElementsHD>Elements HD,CFX,E+,P2)
,SUP(HIT,Elements,Cocoflax>Coco Flax,Cocomat,SLX,P2Soft>P2 Soft,O2)
,Race (PRO)
,Foil(SLXSURFFOIL>SLX Surf Foil,PROTECHSURFFOIL>Protech Surf Foil,SLXSUPFOIL>SLX SUP Foil,PROTECHSUPFOIL>Protech SUP Foil,SLXFOILFLAX>SLX Foil FLAX)
[/nsp_tech_dropdown]
*/
	function render_nsp_tech_dropdown($atts ,$content=null){ //die(__FUNCTION__);
		$atts = $this->shortcode_atts([
			'scroll_duration' => 2000
		],$atts,'nsp_tech_dropdown');
		$menu_data = $content;
		extract($atts,EXTR_PREFIX_ALL,'att'); //$att_menu_data = $menu_data;
		//$arg = ['post_type'=>'poi' ,'numberposts'=>-1];
		//$posts = $this->get_poi($arg);
		//$html.= print_r($posts,true);
		$html = '';
		$html.= "<div class='nsp_tech_dropdown' data-menu='{$menu_data}' data-scroll_duration='{$att_scroll_duration}'></div>";
		return $html;
	}

}
