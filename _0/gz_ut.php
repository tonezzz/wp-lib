<?php //die(__FILE__);
global $GZ; $GZ = (object)['modules'=>[]];

add_action('init','gz_init');
function gz_init(){
    do_action('gz_init_before');
    //date_default_timezone_set('Asia/Bangkok');
    do_action('gz_init_after');
}

function load_module($prm){ //die('<pre>'.print_r($prm,true));
    extract($prm,EXTR_PREFIX_ALL,'prm'); //ob_clean(); echo "<pre>"; print_r($prm); die();
    global $GZ;
	$sys_dir = realpath(dirname(__FILE__).'/..').'/';
    $prm_version = empty($prm_version)?'':'_'.$prm_version;
    switch($prm_type){
        case 'lib0': $path = 'lib/'.$prm_name.$prm_version.'.php'; break;
        default: $path = $prm_type.'/'.$prm_name.$prm_version.'/_wp_class.php';
    }
    //if(empty($prm_action)){
        require $sys_dir.$path;
        if($prm_init){
            global $$prm_name;
            $GZ->modules[] = $$prm_name = new $prm_name(); //if($prm_name=='wq') {ob_clean(); echo "<pre>"; print_r(compact($prm_name)); die();}
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
