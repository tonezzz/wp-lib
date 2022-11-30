<?php //die(__FILE__);
/*
* Note:
* 	Perform Geocoding and cache into Google Sheet
* v0.00 - 20200403:Tony
*/
//		wp_enqueue_script('googlemapsapi3', 'https://maps.google.com/maps/api/js?libraries=places,visualization&key=AIzaSyBHECdeG3VyMDPGrAKYPRUWg972BQzIfUo', false, '3', false); ;

class gz_geocode extends gz_tpl{
	public function __construct(){//die(__FILE__.__FUNCTION__);
		$config = [
			'enqueue'  => [
				['type'=>'localize', 'prm'=>[__CLASS__,__CLASS__,[
					'ajax'=>admin_url('admin-ajax.php')
				]]]
			]
			,'ajaxes' => [
				['prm'=>['get_geocode',[$this,'get_geocode']]]
			]
		]; //ob_clean(); echo '<pre>'; print_r($config); die();
		parent::__construct($config); //ob_clean(); echo '<pre>'; print_r($config); die();
		//$this->init_db();
		//$this->get_geocode('AmnatCharoen'); //die();
	}

	/*
	function init_db(){
		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE {$wpdb->base_prefix}gz_geocode (";
		$sql.= "	address varchar(255) NOT NULL";
		//$sql.= "	,lat double";
		//$sql.= "	,lng double";
		$sql.= " ,PRIMARY KEY  (address)";
		$sql.= ") {$charset_collate};";
		
		//require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		//dbDelta($sql);
	}
	*/

	function get_geocode($address){//die(print_r([$address,$this->get_geocode_db($address)],true));
		$ret = false;
		if(false==($ret=$this->get_geocode_db($address))){ //die('<pre>'.print_r(compact('address','rs'),true));
			if(true==($ret=$this->get_geocode_gmap($address))) $ret = $this->add_geocode_to_db($address,$ret);
		} //ob_clean(); die('<pre>'.print_r(compact('address','ret'),true));
		return ($ret)?(object)$ret:false;
	}

	function add_geocode_to_db($address,$rs){ //ob_clean(); die('<pre>'.print_r(compact('address','rs'),true));
		if(!(is_object($rs) && isset($rs->lat) && isset($rs->lng) && is_numeric($rs->lat) && is_numeric($rs->lng))) return;
		global $wpdb;
		$table_name = $wpdb->base_prefix.'gz_geocode';
		//$wpdb->delete($table_name,['address'=>$address]);
		return $wpdb->replace($table_name,['address'=>$address ,'lat'=>$rs->lat ,'lng'=>$rs->lng]);
	}

	function get_geocode_gmap($address){
		$api_key = 'AIzaSyBHECdeG3VyMDPGrAKYPRUWg972BQzIfUo';
		//?address=1600+Amphitheatre+Parkway,+Mountain+View,+CA&key=YOUR_API_KEY
		$service_url = "https://maps.googleapis.com/maps/api/geocode/json";
		$prm = ['address'=>urlencode($address) ,'key'=>$api_key];
		$url = add_query_arg($prm,$service_url); //die($url);
		$rs = file_get_contents($url); //ob_clean(); die('<pre>'.print_r(compact('address','url','rs','prm'),true));
		$data = json_decode($rs);;
		if(isset($data->results[0]->geometry->location)) $ret = $data->results[0]->geometry->location; else $ret = false;
		//ob_clean(); die('<pre>'.print_r($ret,true));
		return $ret;
	}

	function get_geocode_db($address){
		global $wpdb;
		$sql = "SELECT * FROM {$wpdb->base_prefix}gz_geocode WHERE address='{$address}' LIMIT 1";
		$rs = $wpdb->get_results($sql); //ob_clean(); die('<pre>'.print_r($rs,true));
		if(empty($rs)) return false; else return ['lat'=>$rs[0]->lat ,'lng'=>$rs[0]->lng];
	}

}
