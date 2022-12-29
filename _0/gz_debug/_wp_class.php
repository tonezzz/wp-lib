<?php //die(__FILE__);
/*
* Note:
* 	Perform Caching
* v0.00 - 20200406:Tony
*/



class gz_debug extends gz_tpl{
	private $log_keep_duration = 24; //Hours to keep
	public function __construct(){//die(__FILE__.__FUNCTION__);
		$this->config = [
			'actions' => [
				['prm'	=> [__CLASS__.'_clear_log',[$this,'clear_log']]]
			]
			,'ajaxes' => [
				['prm'=>[__CLASS__.'_clear_log',[$this,'clear_log']]]
				,['prm'=>[__CLASS__.'_enable_autoclean',[$this,'enable_autoclean']]]
				,['prm'=>[__CLASS__.'_disable_autoclean',[$this,'disable_autoclean']]]
			]
		]; //
		parent::__construct($this->config);
		//$this->init_db();
		//$this->get_geocode('AmnatCharoen'); //die();
	}

	static function clear_log(){//die(__CLASS__.__FUNCTION__);
		global $wpdb;
		$rs = $wpdb->get_results("DELETE FROM wp_gz_debug WHERE _created < (NOW() - INTERVAL {$this->log_keep_duration} DAY_HOUR)");
		print_r($rs); die(0);
	}

	static function log($prm){ //ob_clean(); die ('<pre>'.print_r($prm['msg'],true));
		extract($prm,EXTR_PREFIX_ALL,'prm');
		global $wpdb;
		$wpdb->insert('wp_gz_debug',['msg'=>$prm['msg']]);
	}

	/**
	 * function enable_autoload() - Set wp cronjob
	 * https://surf-thailand.com.local/v3/wp/wp-admin/admin-ajax.php?action=enable_autoload
	 */
	function enable_autoclean($die=true){
		$time = intdiv(strtotime('NOW'),3600)*3600;
		$act=__CLASS__.'_clear_log'; if(!wp_next_scheduled($act)) $rs = wp_schedule_event($time,'hourly',$act); //die(print_r($rs,true));
		gz_debug::log(['msg'=>"Add schedule: ".__CLASS__.'->'.__FUNCTION__]);
		if($die) {echo $rs; die(0);}
	}

	/**
	 * https://surf-thailand.com.local/v3/wp/wp-admin/admin-ajax.php?action=disable_autoload
	 */
	function disable_autoclean($die=true){
		$rs = wp_clear_scheduled_hook(__CLASS__.'_clear_log');
		gz_debug::log(['msg'=>"Del schedule: ".__CLASS__.'->'.__FUNCTION__]);
		if($die) {echo $rs; die(0);}
	}

}
