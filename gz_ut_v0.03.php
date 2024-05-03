<?php //die(__FILE__);
/**
	$GZ global variable for all GZ's modules & settings.
	GZ() global function for easy access of $GZ variable.
	Example:
		GZ()['modules']
		if('nsp_shortcodes'==$prm['name']) {ob_clean(); die('<pre>'.print_r($prm,true));}
 */
global $GZ; $GZ = (object)['modules'=>[] ,'script_loader_tags'=>[]];
function GZ(){global $GZ; return $GZ;}

/**
 *	Function gz_load_module_2(): Attach each module to its coresponding init event.
 *		Default is 'init' for backward compatibility.
 */
 //Save module parameters in $GZ array and add action to init module.
function gz_load_module_2($prm){
	//$mod_name = $prm['name'];
	//Action 'load' means load immediately.
	
	//Action not specify = use default (init)
	if(empty($prm['action'])) $prm['action']='init';
    if('load'==$prm['action']) load_module($prm); else{
        add_action($prm['action'],function() use($prm){
            load_module($prm);
        });
    }
}

/********************/

add_action('init','gz_init');
function gz_init(){
    do_action('gz_init_before');
    //date_default_timezone_set('Asia/Bangkok');
    do_action('gz_init_after');
}

add_action('admin_init','gz_admin_init');
function gz_admin_init(){
    do_action('gz_admin_init_before');
    //date_default_timezone_set('Asia/Bangkok');
    do_action('gz_admin_init_after');
}

function load_module($prm){ //die('<pre>'.print_r($prm,true));
    extract($prm,EXTR_PREFIX_ALL,'prm'); //ob_clean(); echo "<pre>"; print_r($prm); die();
    global $GZ;
	$sys_dir 		= realpath(dirname(__FILE__).'/..').'/';
    $prm_version 	= empty($prm_version)?'':'_'.$prm_version;
    $prm_init 		= isset($prm_init)?$prm_init:false;
    switch($prm_type){
        case 'lib0': $path = 'lib/'.$prm_name.$prm_version.'.php'; break;
        default: $path = $prm_type.'/'.$prm_name.$prm_version.'/_wp_class.php';
    }
    //if(empty($prm_action)){
        require $sys_dir.$path;
        if($prm_init){
            global $$prm_name;
            $GZ->modules[$prm_name] = $$prm_name = new $prm_name(); //if($prm_name=='wq') {ob_clean(); echo "<pre>"; print_r(compact($prm_name)); die();}
        }
    //}
    /*
    //Trying to load with specific action, doesn't work yet.
    else{//ob_clean(); echo "<pre>"; print_r($prm); die();
        add_action($prm_action,function(){ob_clean(); echo "<pre>"; print_r($prm); die();
            require $path;
            if($prm_init){
                global $$prm_name;
                $$prm_name = new $prm_name(); //if($prm_name=='wq') {ob_clean(); echo "<pre>"; print_r(compact($prm_name)); die();}
            }
        });
    }
    */
}

	/**
	 */
	//if(isset(GZ()->script_loader_tags)) add_filter('script_loader_tag','do_script_loader_tag',999,3);
	if(!is_admin()) add_filter('script_loader_tag','do_script_loader_tag',999,3);
	function do_script_loader_tag($tag,$handle,$src){ //return false;
		//if(isset($this->script_loader_tags[$handle])&&$this->script_loader_tags[$handle]['debug']) die('<pre>'.print_r(compact('tag','handle','src'),true).print_r($this->script_loader_tags[$handle],true));
		$atts = array_merge([
			'type'	=> 'text/javascript'
			,'id'	=> $handle
			,'src'	=> $src
		],isset(GZ()->script_loader_tags[$handle])?GZ()->script_loader_tags[$handle]:[]);
		$atts_st = '';
		foreach($atts as $key=>$val) $atts_st.= " {$key}='{$val}'";
		$tag_1 = "<script {$atts_st}></script>\n"; //ob_clean(); die($tag_1);
		//if(isset($this->script_loader_tags[$handle])&&$this->script_loader_tags[$handle]['debug'])
		//	die('<pre>'.print_r(compact('atts','atts_st','tag','tag_1'),true).print_r($this->script_loader_tags[$handle],true));
		return $tag_1;
		//return true;
	}
