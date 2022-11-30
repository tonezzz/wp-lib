<?php
/*
	if($hostname==$config['domains']){
		gz_enable_plugins($config['plugins_own']['enable']);
		gz_disable_plugins($config['plugins_own']['enable']);
		gz_update_options($config['update_options_own']);
	}else{
		gz_enable_plugins($config['plugins_other']['enable']);
		gz_disable_plugins($config['plugins_other']['enable']
		gz_update_options($config['update_options_others']);
	}
*/

/**
 * class gz_wp_init - To help automate Wordpress for Live and Staging websites.
 */
class gz_wp_init extends gz_tpl{
	protected $config_wp;
	protected $config;
	protected $hostname;
	protected $is_own_domains;

	public function __construct(){//die(__FILE__.__FUNCTION__);
		$this->config_wp = [
			'domains' => [
				'surfsupwarehouse.com.au'
				,'surf-thailand.com.local'
			]
			,'plugins_own' => [
				'disable' => []
				,'enable' => []
			]
			,'plugins_other' => [
				'disable' => [
					'duracelltomi-google-tag-manager/duracelltomi-google-tag-manager-for-wordpress.php'
					,'advanced-cf7-db/advanced-cf7-db.php'
				]
				,'enable' => []
			]
			,'update_options_own' => ['blog_public' => '1']
			,'update_options_others' => ['blog_public' => '0']
		];

		$this->config = [
			'filters' => [
				['prm'=>['option_active_plugins',[$this,'option_active_plugins']]]
			]
			,'actions'	=> [
				['prm'=>['init',[$this,'update_options']]]
			]
		]; //ob_clean(); echo '<pre>'; print_r($config); die();
		
		parent::__construct($this->config); //ob_clean(); echo '<pre>'; print_r($config); die();
		$hostname = $_SERVER['HTTP_HOST']; //die('<br>'.print_r($config_only[$hostname],true));
		$this->is_own_domains = (in_array($hostname,$this->config_wp['domains']))?true:false; //die('<pre>'.print_r($this->config_wp,true));
		$this->gz_update_options();
	}
	
	public function gz_update_options(){
		if($this->is_own_domains) $options = 'update_options_own'; else $options = 'update_options_others';
		foreach($this->config_wp[$options] as $key=>$val) update_option($key,$val);
	}
	
	public function option_active_plugins(){
		if(isset($_GET['list_plugins'])) die('<pre>'.print_r($plugins,true)); //Just list it all to see.
	}
}
