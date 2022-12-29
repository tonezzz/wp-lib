<?php //die(__FILE__);
/*
v0.00 - 20180105:Tony
	- Apply a feature image from url to product
*/
require_once '../../../../../wp-load.php';

class image_apply extends gz_tpl{
	private $img_dir,$image;
	private $draw=null;
	
	public function __construct(){//die(__FILE__.__FUNCTION__);
		//$this->set_resource_limits();
		//if(isset($_REQUEST['d_res'])) {$this->show_info(); die();}
		parent::__construct();
		$this->get_url(__FILE__);
		$this->set_id(__CLASS__);
		//$this->init_scripts();
		//$this->init_shortcode();
		//$this->img_dir = $this->dir.'img/'; //ob_clean(); echo "<pre>"; var_dump($this); die();
		//$this->render_image();
		$this->apply();
	}
	
	function apply(){ //print_r($_REQUEST);
		//die(json_encode($_REQUEST));
		$prod_id = $_REQUEST['prod_id'];
		$site_url = $_REQUEST['site_url']; $site_url = stripcslashes($site_url); //echo (json_encode(['url'=>$img_url])); die();
		$tpl_url = $_REQUEST['tpl_url']; $tpl_url = stripcslashes($tpl_url); //echo (json_encode(['url'=>$img_url])); die();
		$img_url = $_REQUEST['img_url']; $img_url = stripcslashes($img_url); //echo (json_encode(['url'=>$img_url])); die();
		$img_name = $_REQUEST['img_name'];
		$this->do_apply($prod_id,$img_url,$img_name);
	}
	
	function do_apply($post_id,$image_url,$image_name){ //die($image_url);
		// Add Featured Image to Post
		//$image_url        = 'http://s.wordpress.org/style/images/wp-header-logo.png'; // Define the image URL here
		//$image_name       = 'wp-header-logo.png';
		$upload_dir       = wp_upload_dir(); // Set upload folder
		//
		//$image_data       = file_get_contents($image_url); var_dump($image_data); die();// Get image data
		//
		$req = parse_url($image_url); //echo "<pre>"; print_r($req['query']); die();
		$ch = curl_init();
		$url = $req['scheme'].'://'.$req['host'].$req['path']; //echo $url; die();
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS,$req['query']);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$image_data = curl_exec($ch);
		curl_close($ch);
		//
		$unique_file_name = wp_unique_filename( $upload_dir['path'], $image_name ); // Generate unique name
		$filename         = basename( $unique_file_name ); // Create image file name

		// Check folder permission and define file location
		if( wp_mkdir_p( $upload_dir['path'] ) ) {
			$file = $upload_dir['path'] . '/' . $filename;
		} else {
			$file = $upload_dir['basedir'] . '/' . $filename;
		}

		// Create the image  file on the server
		file_put_contents( $file, $image_data );

		// Check image file type
		$wp_filetype = wp_check_filetype( $filename, null );

		// Set attachment data
		$attachment = array(
			'post_mime_type' => $wp_filetype['type'],
			'post_title'     => sanitize_file_name( $filename ),
			'post_content'   => '',
			'post_status'    => 'inherit'
		);

		// Create the attachment
		$attach_id = wp_insert_attachment( $attachment, $file, $post_id );

		// Include image.php
		require_once(ABSPATH . 'wp-admin/includes/image.php');

		// Define attachment metadata
		$attach_data = wp_generate_attachment_metadata( $attach_id, $file );

		// Assign metadata to attachment
		wp_update_attachment_metadata( $attach_id, $attach_data );

		// And finally assign featured image to post
		set_post_thumbnail( $post_id, $attach_id );
		
		echo json_encode(['result'=>'ok']);
	}
}

$res = new image_apply();