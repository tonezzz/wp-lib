<?php //die(__FILE__);
/*
 */
 class post_search_ajax extends gz_tpl{
	public function __construct(){//die(__FUNCTION__);
		//$config = [];
		//parent::__construct($config);
		//add_filter( 'cmb2_render_pw_map', array( $this, 'render_pw_map' ), 10, 5 );
		//add_filter( 'cmb2_sanitize_pw_map', array( $this, 'sanitize_pw_map' ), 10, 4 );
		require_once dirname(__FILE__).'/cmb-field-post-search-ajax.php';
	}
}
