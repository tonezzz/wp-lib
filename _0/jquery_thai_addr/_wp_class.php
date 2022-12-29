<?php
/*
	20180214:Tony:Update to current technique
*/

class jquery_thai_addr extends gz_tpl {
	//private $id, $full_url, $rel_url;

	function __construct($enqueue=false){
		$config = [
			'enqueue'  => [//@import './fonts/css/fontello.css';
				['type'=>'style' ,'prm'=>['jquery-thai-addr','https://earthchie.github.io/jquery.Thailand.js/jquery.Thailand.js/dist/jquery.Thailand.min.css',[]]]
				,['type'=>'script' ,'prm'=>['jql','https://earthchie.github.io/jquery.Thailand.js/jquery.Thailand.js/dependencies/JQL.min.js',[]]]
				,['type'=>'script' ,'prm'=>['type-ahead','https://earthchie.github.io/jquery.Thailand.js/jquery.Thailand.js/dependencies/typeahead.bundle.js',[]]]
				,['type'=>'script' ,'prm'=>['jquery-thai-addr','https://earthchie.github.io/jquery.Thailand.js/jquery.Thailand.js/dist/jquery.Thailand.min.js',['jquery-core','jql','type-ahead']]]
				//,['type'=>'script' ,'prm'=>['jquery-thai-addr','https://earthchie.github.io/jquery.Thailand.js/jquery.Thailand.js/dist/jquery.Thailand.min.js',['jquery-core']]]
				,['type'=>'script' ,'prm'=>[__CLASS__,'[REL_PATH]_wp_script.js',['jquery-core','jquery-thai-addr']]]
				//,['type'=>'localize', 'prm'=>[__CLASS__,__CLASS__,['get_poi'=>admin_url('admin-ajax.php')]]]
				]
		];
		parent::__construct($config);
	}
}