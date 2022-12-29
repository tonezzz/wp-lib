<?php //die(__FILE__);
/*
 */
 class pw_map extends gz_tpl{
	public function __construct(){//die(__FUNCTION__);
		$config = [
			'enqueue_admin'  => [//@import './fonts/css/fontello.css';
				['type'=>'script' ,'load'=>true ,'prm'=>[__CLASS__,'[REL_PATH]js/script.js',['jquery-core']]]
				,['type'=>'style' ,'load'=>true ,'prm'=>[__CLASS__,'[REL_PATH]css/style.css',[]]]
				,['type'=>'descript' ,'prm'=>['googlemapsapi3']]
				,['type'=>'script' ,'load'=>true ,'prm'=>['googlemapsapi3', 'http://maps.google.com/maps/api/js?libraries=places&key=AIzaSyBHECdeG3VyMDPGrAKYPRUWg972BQzIfUo']]
				,['type'=>'descript' ,'prm'=>['gmap3']]
				,['type'=>'script' ,'load'=>true ,'prm'=>['gmap3', '[REL_PATH]js/gmap3.js', ['jquery-core','googlemapsapi3'],'7.1.0']]
			]
		];
		parent::__construct($config);
		add_filter( 'cmb2_render_pw_map', array( $this, 'render_pw_map' ), 10, 5 );
		add_filter( 'cmb2_sanitize_pw_map', array( $this, 'sanitize_pw_map' ), 10, 4 );
	}

	/**
	 * Render field
	 */
	public function render_pw_map( $field, $field_escaped_value, $field_object_id, $field_object_type, $field_type_object ) {
		//$this->setup_admin_scripts();

		echo '<input type="text" class="large-text pw-map-search" id="' . $field->args( 'id' ) . '" />';

		echo '<div class="pw-map"></div>';

		$field_type_object->_desc( true, true );

		echo $field_type_object->input( array(
			'type'       => 'hidden',
			'name'       => $field->args('_name') . '[latitude]',
			'value'      => isset( $field_escaped_value['latitude'] ) ? $field_escaped_value['latitude'] : '',
			'class'      => 'pw-map-latitude',
			'desc'       => '',
		) );
		echo $field_type_object->input( array(
			'type'       => 'hidden',
			'name'       => $field->args('_name') . '[longitude]',
			'value'      => isset( $field_escaped_value['longitude'] ) ? $field_escaped_value['longitude'] : '',
			'class'      => 'pw-map-longitude',
			'desc'       => '',
		) );
	}

	/**
	 * Optionally save the latitude/longitude values into two custom fields
	 */
	public function sanitize_pw_map( $override_value, $value, $object_id, $field_args ) {
		if ( isset( $field_args['split_values'] ) && $field_args['split_values'] ) {
			if ( ! empty( $value['latitude'] ) ) {
				update_post_meta( $object_id, $field_args['id'] . '_latitude', $value['latitude'] );
			}

			if ( ! empty( $value['longitude'] ) ) {
				update_post_meta( $object_id, $field_args['id'] . '_longitude', $value['longitude'] );
			}
		}

		return $value;
	}
}
