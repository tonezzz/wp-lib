<?php //die(__FILE__);
/*
* Note:
* 	Perform Caching
* v0.00 - 20200406:Tony
*/

class gz_cmb2 extends gz_tpl{
	public function __construct(){//die(__FILE__.__FUNCTION__);
		$config = [
			/*
			'enqueue'  => [
				['type'=>'localize', 'prm'=>[__CLASS__,__CLASS__,[
					'ajax'=>admin_url('admin-ajax.php')
				]]]
			]
			,'ajaxes' => [
				['prm'=>['get_geocode',[$this,'get_geocode']]]
			]
			*/
		]; //ob_clean(); echo '<pre>'; print_r($config); die();
		parent::__construct($config); //ob_clean(); echo '<pre>'; print_r($config); die();
		//$this->init_db();
		//$this->get_geocode('AmnatCharoen'); //die();
	}

	/**
	 * $prm = Parameters to pass
	 * $opt	= Options for this call
	 */
	function get_data($func,$prm=[],$cache_opt=[]){
		$opt = shortcode_atts([
			'clear'	=> false
			,'time'	=> 60 		//1 minute
		],$cache_opt); //ob_clean(); $prm['func']=$func[1]; die('<pre>'.print_r(compact('prm','opt'),true));
		extract($opt,EXTR_PREFIX_ALL,'opt');
		$transient_key = __CLASS__.'-'.hash('md5',json_encode($func).json_encode($prm)); //ob_clean(); die($transient_key);
		if($opt_clear || false===($data=get_transient($transient_key))){
			$data = call_user_func_array($func,$prm);
			set_transient($transient_key,$data,$opt_time);
		} //ob_clean(); die('<pre>'.print_r(compact('transient_key','data'),true));
		return $data;
	}
}
