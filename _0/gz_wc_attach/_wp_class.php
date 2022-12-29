<?php //die(__FILE__);
/*
* Note:
* 	Check https://github.com/edent/QR-Generator-PHP
*	- Include gz_location to show poi.
* 	- [gz_map] short_code
*		- style=''
*		- poi='location,get_poi,all
*		- icon=''
* v0.02 - 20181201:Tony
*	- 
* v0.00 - 20171026:Tony
*/
class gz_wc_attach extends gz_tpl{
	//static $icon = [
	//];
	public function __construct(){//die(__FILE__.__FUNCTION__);
		$config = [
			'enqueue'  => [
				['type'=>'script' ,'load'=>true ,'prm'=>[__CLASS__,'[REL_PATH]_wp_script.js',['jquery-core']]]
				,['type'=>'style' ,'load'=>true ,'prm'=>[__CLASS__,'[REL_PATH]wp_style.scss',[]]]
				,['type'=>'localize', 'prm'=>[__CLASS__,__CLASS__,['ajax_url'=>admin_url('admin-ajax.php')]]]
				//,['type'=>'style' ,'load'=>true ,'prm'=>['google_fonts','https://fonts.googleapis.com/css?family=Kanit',[]]]
			]
			,'cmb2' =>[
				//This will add multiple file upload to admin edit order panel
				'admin_order'	=> [
					'prefix'	=> '_gz_wc_attach_'
					,'cmb2_args'	=> [
						'id'	=> 'file_attachment'
						,'title'	=> "File attachments"
						,'closed'	=> false
						,'object_types'	=> ['shop_order']
						,'fields'	=> [
							['id'=>'files','name'=>'Files','type'=>'file_list']
						]
					]
				]
				
				,'manage_order' => [
					'prefix' 	=> '_gz_wc_attach_'
					,'cmb2_args'	=> [
						'id'		=> 'manage_order'
						,'title'	=> "Manage Order"
						,'hookup'	=> true
						//,'enqueue_js'=>false
						//,'object_types'	=> ['shop_order']
						//,'show_names' => false
						,'fields'	=> [
							['id'=>'files','name'=>'Files','type'=>'file_list']
						]
					]
				]
				
			]
		]; //ob_clean(); echo '<pre>'; print_r($config); die();
		parent::__construct($config); //ob_clean(); echo '<pre>'; print_r($config); die();
		//wp_enqueue_media(); //This caused the problem attaching product image
		//add_action('admin_enqueue_scripts',[$this,'enqueue_media']);
		//if(is_page('manage-orders')) add_action('wp_enqueue_scripts',[$this,'enqueue_media']);
		add_action('wp_enqueue_scripts',[$this,'enqueue_media']);
		/*
		//add_filter( 'wp_nav_menu_items',[$this,'add_login_to_menu'], 199, 2 );
		//add_action('woocommerce_after_order_itemmeta',[$this,'test_order'],10,3);
		//add_action('woocommerce_before_order_notes',[$this,'test'],10,3);
		//add_action('woocommerce_admin_order_data_after_order_details',[$this,'test'],10,3);
		//add_action('woocommerce_order_details_before_order_table',[$this,'woocommerce_admin_order_data_after_order_details']);
		//add_action('woocommerce_admin_order_data_after_order_details',[$this,'woocommerce_admin_order_data_after_order_details']);
		*/
		add_filter('woocommerce_my_account_my_orders_columns',[$this,'woocommerce_my_account_my_orders_columns']);
		add_action('woocommerce_my_account_my_orders_column_attachment',[$this,'woocommerce_my_account_my_orders_column_order_attachment']);
		add_shortcode('gz_wc_manage_orders',[$this,'gz_wc_manage_orders']);
		add_action('wp_ajax_nopriv_'.'get_attach',[$this,'get_attach']); add_action('wp_ajax_'.'get_attach',[$this,'get_attach']);
		add_action('wp_ajax_nopriv_'.'add_attach',[$this,'add_attach']); add_action('wp_ajax_'.'add_attach',[$this,'add_attach']);
		add_action('wp_ajax_nopriv_'.'del_attach',[$this,'del_attach']); add_action('wp_ajax_'.'del_attach',[$this,'del_attach']);
		add_action('wp_ajax_nopriv_'.'send_mail',[$this,'send_mail']); add_action('wp_ajax_'.'send_mail',[$this,'send_mail']);
	}

	function enqueue_media(){
		wp_enqueue_media();
	}

	/**
	 * get_attach() - Get attachment list in ul/li format
	 *
	 * @return void
	 */
	function send_mail(){
		$post_id = $_REQUEST['post_id']; if(empty($post_id)) die(); //echo '<pre>'; print_r(compact('post_id'));
		$files = get_post_meta($post_id,'_gz_wc_attach_files',true); //ob_clean(); echo '<pre>'; print_r(compact('files')); die();
		$email_attach=[]; foreach($files as $id_media=>$url) $email_attach[]=get_attached_file($id_media);
		//$html_file = $this->arr2list($files,['post_id'=>$post_id]); //ob_clean(); echo '<pre>'; print_r(compact('h')); die();
		//die($html_file);
		$order = wc_get_order($post_id); if(empty($order)) die();
		$billing_name = "{$order->get_billing_first_name()} {$order->get_billing_last_name()}";
		$email_subject = "Order #{$post_id} {$billing_name} (documents)";
		$email_to = $order->get_billing_email();
		$email_body = "Test sending order attachment via email.";
		$email_header = ['Content-Type: text/html; charset=UTF-8'];
		wp_mail($email_to,$email_subject,$email_body,$email_header,$email_attach);
		die();
	}

	/**
	 * get_attach() - Get attachment list in ul/li format
	 *
	 * @return void
	 */
	function get_attach(){
		$post_id = $_REQUEST['post_id']; if(empty($post_id)) die(); //echo '<pre>'; print_r(compact('post_id'));
		$files = get_post_meta($post_id,'_gz_wc_attach_files',true); 
		$html_file = $this->arr2list($files,['post_id'=>$post_id]); //ob_clean(); echo '<pre>'; print_r(compact('h')); die();
		die($html_file);
	}

	/**
	 * get_attach() - Add attachment
	 *
	 * @return void
	 */
	function add_attach(){
		$post_id = $_REQUEST['post_id']; if(empty($post_id)) die(); //echo '<pre>'; print_r(compact('post_id'));
		$metas = get_post_meta($post_id,'_gz_wc_attach_files',true); if(!is_array($metas)) $metas = [];
		$data = $_REQUEST['data']; if(empty($data)&&!is_array($data)) die(); //echo '<pre>'; print_r(compact('metas','data'));
		foreach($data as $item){ //echo '<pre>'; print_r(compact('metas','data','item')); die();
			$metas[$item['id']] = $item['url']; //echo '<pre>'; print_r(compact('metas','data','item')); print_r($metas[1741]); die();
		} //echo '<pre>'; print_r(compact('metas','data')); die();
		//$json_st = json_encode($metas);
		//header('Content-Type: application/json'); die($json_st);
		update_post_meta($post_id,'_gz_wc_attach_files',$metas);
		$this->get_attach();
	}

	/**
	 * del_attach() - Delete attachment
	 *
	 * @return void
	 */
	function del_attach(){
		$post_id = $_REQUEST['post_id']; if(empty($post_id)) die('Error: Missing post id.'); //echo '<pre>'; print_r(compact('post_id'));
		$data = $_REQUEST['data']; if(empty($data)) die('Error: Missing data.');
		$files = get_post_meta($post_id,'_gz_wc_attach_files',true); //echo '<pre>'; print_r(compact('post_id','data','files')); //die();
		foreach($data as $val){unset($files[$val]);} //echo '<pre>'; print_r(compact('files')); die();
		update_post_meta($post_id,'_gz_wc_attach_files',$files);
		$this->get_attach();
	}

	function arr2list($items,$opt=[]){
		$opt = $this->shortcode_atts([
			'post_id'		=> false
			,'ul_class' 	=> 'file_list'
			,'li_class' 	=> ''
			,'button_class'	=> 'button gz_wc_attach add_files'
			,'button_text'	=> 'Add Files'
		],$opt);
		extract($opt,EXTR_PREFIX_ALL,'opt');
		$html = '';
		$html.="<ul class='{$opt_ul_class}' data-post_id='{$opt_post_id}'>";
		if(!empty($items) && is_array($items)) foreach($items as $id=>$file){
			$file_name = basename($file);
			$html.="<li class='item'><span class='remove' data-id='{$id}'>X</span><a href='{$file}'>{$file_name}</a></li>";
		}
		//Add button
		$html.= $this->render_dom([
			'dom'	=> ['type'=>'li' ,'class'=>$opt_button_class]
			,'attr'	=> [
				'data-title'	=> "Attachment ({$opt_post_id})"
				,'data-post_id'	=> $opt_post_id
				,'data-button_text'	=> $opt_button_text
			]
			,'content'	=> $opt_button_text
		]);
		$html.="</ul>";
		return $html;
	}

	function render_html_file($post_id,$meta_key){
		$html = '';
		$files = get_post_meta($post_id,$meta_key,true); //ob_clean(); echo '<pre>'; print_r($files); die();
		$html.="<ul class='file_list'>";
		if(!empty($files)) foreach($files as $file){
			$file_name = basename($file);
			$html.="<li class='item'><a href='{$file}'>{$file_name}</a></li>";
		}
		//$html.="<span class='button gz_wc_attach add_files'>Add files</span>";
		$html.= $this->render_dom([
			'dom'	=> ['type'=>'li' ,'class'=>'button gz_wc_attach add_files']
			,'attr'	=> [
				'data-title'	=> "Attachment ({$post_id})"
				,'data-post_id'	=> $post_id
				,'data-button_text'	=> 'Add attachment'
			]
			,'content'	=> 'Add files'
		]);
		$html.="</ul>";
		return $html;
	}

	function gz_wc_manage_orders(){//echo __FUNCTION__;
		$args = [];
		$html_debug = '';
		$html = '';
		$html.= "<table class='gz_wc_attach list_orders'>";
		$html.= "<tr><td>Order ID</td><td>Billing Name</td><td>Amount</td><td>Attach</td></tr>";
		$orders = wc_get_orders($args); //ob_clean(); echo '<pre>'; print_r($orders); die();
		foreach($orders as $order){
			$order_id = $order->get_id(); //ob_clean(); echo '<pre>'; var_dump($order_id); die();
			$html_mail_button = "<span class='button send_mail' data-post_id='{$order_id}'>Send Mail</span>";
			$html.= "<tr>";
			$html.= "<td>{$order_id}</td>";
			$html.= "<td>{$order->get_billing_first_name()} {$order->get_billing_last_name()} {$html_mail_button}</td>";
			$total = number_format($order->get_total(),2);
			$html.= "<td>{$total}</td>";
			//
			//$html_file = $this->render_html_file($order_id,'_gz_wc_attach_files');
			//$html.= "<td>{$html_file}</td>";
			$files = get_post_meta($order_id,'_gz_wc_attach_files',true); //ob_clean(); echo '<pre>'; print_r($files); die();
			$html_file = $this->arr2list($files,['post_id'=>$order_id]); //ob_clean(); echo '<pre>'; print_r(compact('h')); die();
			$html.= "<td class='list_files'>{$html_file}</td>";
			$html.= "</tr>";
			//Old code for CMB2
			/*
			$opt = [];
			$html_form = cmb2_get_metabox_form('manage_order',$order_id,$opt);
			$html.= "<tr><td colspan='4'>{$html_form}</td></tr>";
			$metas = get_post_meta($order_id,'_gz_wc_attach_files',true); //ob_clean(); echo '<pre>'; print_r($files); die();
			$html_debug.="<tr><td colspan='4'><pre>".print_r($metas,true)."</pre></td></tr>";
			*/
		}
		$html.= "</table>";
		$html.= "<table>{$html_debug}</table>";
		return $html;
	}

	function gz_wc_manage_orders_2(){//echo __FUNCTION__;
		$args = [];
		$html_debug = '';
		$html = '';
		$html.= "<table class='orders'>";
		$html.= "<tr><td>Order ID</td><td>Billing Name</td><td>Amount</td><td>Attach</td></tr>";
		$orders = wc_get_orders($args); //ob_clean(); echo '<pre>'; print_r($orders); die();
		foreach($orders as $order){
			$order_id = $order->get_id(); //ob_clean(); echo '<pre>'; var_dump($order_id); die();
			$html.= "<tr>";
			$html.= "<td>{$order_id}</td>";
			$html.= "<td>{$order->get_billing_first_name()} {$order->get_billing_last_name()}";
			$total = number_format($order->get_total(),2);
			$html.= "<td>{$total}</td>";
			//$form = cmb2_get_metabox_form('manage_order',$order_id);
			$opt = [];
			//$cmb = cmb2_get_metabox('manage_order');
			$form_html = cmb2_get_metabox_form('manage_order',$order_id,$opt);
			//$form_html = cmb2_get_metabox_form($cmb,$order_id,$opt);
			//$form_html = $this->create_form($order_id,$opt);
			//$form_html = do_shortcode("[cmb-form id='manage_order' post_id='{$order_id}']");
			//$form = cmb2_get_metabox_form($form,$order_id,$opt);
			//$form_html = str_replace('id=\"manage_order\"','id=\"manage_order_'.$key.'\"',$form_html);
			//$form_html = str_replace('id="manage_order"','id="manage_order_'.$key.'"',$form_html);
			$html.= "<td>{$form_html}</td>";
			$html.= "</tr>";
			$metas = get_post_meta($order_id,'_gz_wc_attach_files'); 
			//$html_debug.="<tr><td colspan='4'><pre>".print_r($metas,true).print_r($cmb,true)."</pre></td></tr>";
		}
		$html.= "</table>";
		$html.= "<table>{$html_debug}</table>";
		return $html;
	}

	function woocommerce_my_account_my_orders_columns($columns){//ob_clean(); echo '<pre>'; print_r($columns); die();
		$columns["attachment"] = "Attachment";
		return $columns;
	}
	function woocommerce_my_account_my_orders_column_order_attachment($order){//ob_clean(); echo '<pre>'; print_r($order); die();
		//$formatted_shipping = $order->get_formatted_shipping_address();
		//echo ! empty( $formatted_shipping ) ? $formatted_shipping : '–';
		$meta = get_post_meta($order->get_id(),'_gz_wc_attach_files',true); //ob_clean(); echo '<pre>'; print_r($meta); die();
		if($meta && is_array($meta)){
			$html = '';
			foreach($meta as $id=>$url){
				$html.= "<ul class='gz_wc_attach_files'>";
					$html.= "<li class='item'>";
					$fname = basename($url);
					$html.= "<a href={$url}>{$fname}</a>";
					$html.= "</li>";
				$html.= "</ul>";
			}
		} else $html = '-';
		echo $html;
	}

	function render_b2b_dashboard(){
		//$prm = self::shortcode_atts([
		//],func_num_args()>0?func_get_arg(0):[]); //if($prm['debug']){ob_clean(); echo "<pre>"; print_r($prm); die();}
		//extract($prm,EXTR_PREFIX_ALL,'prm');
		$links = [
			['Dashboard','/my-account']
			,['Orders','/my-account/orders']
			,['Downloads','/my-account/downloads']
			,['Addresses','/my-account/edit-address']
			,['Account details','/my-account/edit-account']
			,['Logout','/my-account/customer-logout']
		];
		$html = ''; //[woocommerce_my_account]
		$html.= "<div class='b2b_dashboard'>";
		foreach($links as $item){
			$label = $item[0]; $label_slug = sanitize_title($label);
			$link = $item[1];
			$html.="<a class='item {$label_slug}' href='{$link}'>{$label}</a>";
		}
		$html.= "</div>";
		return $html;
	}

	function render_b2b_myaccount(){
		//$prm = self::shortcode_atts([
		//],func_num_args()>0?func_get_arg(0):[]); //if($prm['debug']){ob_clean(); echo "<pre>"; print_r($prm); die();}
		//extract($prm,EXTR_PREFIX_ALL,'prm');
		$html = ''; //[woocommerce_my_account]
		$html.= do_shortcode("[woocommerce_my_account]");
		return $html;
	}

}

	/*
	function create_form($obj_id=null,$opt=[]){
		$cmb = new_cmb2_box([
			'id' 			=> 'manage_order_'.$obj_id
			,'title'		=> 'Manage order'
			,'show_names'	=> false
			,'fields'		=> [
				['id'=>'_gz_wc_attach_files','name'=>'Files','type'=>'file_list']
			]
		]);
		$opt['save_button']	= $obj_id;
		return cmb2_get_metabox_form($cmb,$obj_id,$opt);
	}
	*/
	/*
	//add_action('add_meta_boxes',[$this,'add_meta_box']);
	function add_meta_box(){
		add_meta_box( 'mv_other_fields', __('Extra','woocommerce'),[$this,'add_meta_box_fields'], 'shop_order', 'side', 'core' );
	}
	function add_meta_box_fields(){echo "Extra documents here!!!";
		echo "<input type='file' id='order_attachment' name='order_attachment'>";

	}
	*/
