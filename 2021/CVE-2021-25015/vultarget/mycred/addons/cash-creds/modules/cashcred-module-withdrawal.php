<?php
if ( ! defined( 'MYCRED_CASHCRED' ) ) exit;

/**
 * cashCRED_Pending_Payments class
 * @since 1.7
 * @version 1.2
 */
if ( ! class_exists( 'cashCRED_Pending_Payments' ) ) :
	class cashCRED_Pending_Payments extends myCRED_Module {

		/**
		 * Construct
		 */
		function __construct( $type = MYCRED_DEFAULT_TYPE_KEY ) {

			parent::__construct( 'cashCRED_Payments', array(
				'module_name' => 'cashCRED_Payments',
				'option_id'   => '',
				'defaults'    => array(),
				'screen_id'   => '',
				'accordion'   => false,
				'add_to_core' => false,
				'menu_pos'    => 81
			), $type );	
			
		}
		
		/**
		 * Load
		 * @version 1.0.1
		 */
		public function load() {

			add_action( 'mycred_init',       array( $this, 'module_init' ), $this->menu_pos );
			add_action( 'mycred_admin_init', array( $this, 'module_admin_init' ), $this->menu_pos );

		}

		/**
		 * Module Init
		 * @since 1.7
		 * @version 1.2
		 */
		public function module_init() {

			$this->register_cashcred_payments();

			add_action( 'mycred_add_menu', array( $this, 'add_to_menu' ), $this->menu_pos );

		}

		/**
		 * Module Admin Init
		 * @since 1.7
		 * @version 1.1
		 */
		public function module_admin_init() {

			add_filter( 'parent_file',           array( $this, 'parent_file' ) );
			add_filter( 'submenu_file',          array( $this, 'subparent_file' ), 10, 2 );

			add_action( 'admin_notices',         array( $this, 'admin_notices' ) );
 
			add_action( 'admin_head-post.php',   array( $this, 'edit_pending_payment_style' ) );
			add_filter( 'post_updated_messages', array( $this, 'post_updated_messages' ) );

			add_filter( 'manage_' . MYCRED_CASHCRED_KEY . '_posts_columns',       array( $this, 'adjust_column_headers' ) );
			add_action( 'manage_' . MYCRED_CASHCRED_KEY . '_posts_custom_column', array( $this, 'adjust_column_content' ), 10, 2 );
			add_filter( 'bulk_actions-edit-' . MYCRED_CASHCRED_KEY,               array( $this, 'bulk_actions' ) );
			add_action( 'save_post_' . MYCRED_CASHCRED_KEY,                       array( $this, 'save_pending_payment' ), 10, 2 );

			add_action( 'restrict_manage_posts', array( $this,  'cashcred_filter_html' ) );
			add_action( 'parse_query',		     array( $this,  'cashcred_filter_query' ) );
			
			add_action( 'admin_enqueue_scripts', array( $this, 'cashcred_admin_assets' ) );

		}
		
		public function cashcred_admin_assets() { 

			global $post_type;

			if ( $post_type == MYCRED_CASHCRED_KEY ) {
				
				wp_register_style( 'cashcred-admin', plugins_url( 'assets/css/admin-style.css', MYCRED_CASHCRED ), array(), MYCRED_CASHCRED_VERSION, 'all' );
	        	wp_enqueue_style( 'cashcred-admin' );

	        	wp_register_script( 'cashcred-admin-script', plugins_url( 'assets/js/admin-script.js', MYCRED_CASHCRED ), array( 'jquery' ), MYCRED_CASHCRED_VERSION, 'all' );
				wp_enqueue_script( 'cashcred-admin-script' );
			
			}

		}
			
		public function cashcred_filter_html() {

			global $wp_query, $mycred_modules, $post_type;

			if ( $post_type == MYCRED_CASHCRED_KEY ) {  

				$status = array( 'Approved', 'Pending', 'Cancelled' );

				$current_plugin = '';
				if( isset( $_GET['Status'] ) ) {
					$current_plugin = $_GET['Status'];  
				} 
				?>
				<select name="Status" id="Status">
					<option value="all" <?php selected( 'all', $current_plugin ); ?>>
						<?php _e( 'All Status', 'mycred' ); ?>
					</option>
					<?php foreach( $status as $key => $value ) { ?>
					<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $value, $current_plugin ); ?>>
						<?php echo esc_attr( $value ); ?>
					</option>
					<?php } ?>
				</select>
				<?php 
				$current_selected = '';
				if( isset( $_GET['gateway'] ) ) {
					$current_selected = $_GET['gateway']; // Check if option has been selected
				}
				?>
				<select name="gateway" id="gateway">
					<option value="all" <?php selected( 'all', $current_selected ); ?>>
						<?php _e( 'All Gateway', 'mycred' ); ?>
					</option>
					<?php 
					foreach ( $mycred_modules['solo']['cashcred']->get() as $gateway_id => $info ) { ?> 
					<option value="<?php echo esc_attr( $gateway_id ); ?>" <?php selected( $gateway_id, $current_selected ); ?>>
						<?php echo esc_attr( $info['title'] ); ?>
					</option>
					<?php } ?>
				</select>
			   <?php 
				$current_user = '';
				if( isset( $_GET['user_id'] ) ) {
					$current_user = $_GET['user_id']; // Check if option has been selected
				}
				$users = get_users( array( 'fields' => array( 'ID' , 'user_nicename')  ) );
				?>
				<select name="user_id" id="user_id">
					<option value="all" <?php selected( 'all', $current_user ); ?>>
						<?php _e( 'All Users', 'mycred' ); ?>
					</option>
					<?php 
					foreach ( $users as $user ) { ?> 
					<option value="<?php echo esc_attr( $user->ID ); ?>" <?php selected( $user->ID, $current_user ); ?>>
						<?php echo esc_attr( $user->user_nicename ); ?>
					</option>
					<?php } ?>
				</select>
			<?php 
			}
		}

		public function cashcred_filter_query( $query ) {
		  
		  	global $pagenow;
		  	$meta_query = array();
		 
		  	$post_type = isset( $_GET['post_type'] ) ? $_GET['post_type'] : '';
		  
		    /* Gateway filter */
			if ( is_admin() && $pagenow=='edit.php' && $post_type == 'cashcred_withdrawal' && isset( $_GET['gateway'] ) && $_GET['gateway'] !='all' ) {

				$meta_query[] = array(
				'key'     => 'gateway',
				'value'   => $_GET['gateway'],
				'compare' => '='
				);  
			}
		  
			/* Payment status filter */
			if ( is_admin() && $pagenow=='edit.php' && $post_type == 'cashcred_withdrawal' && isset( $_GET['Status'] ) && $_GET['Status'] !='all' ) {

				$meta_query[] = array(
				'key'     => 'Status',
				'value'   => $_GET['Status'],
				'compare' => '='
				);  
			 
			}

			/* User filter */
			if ( is_admin() && $post_type == 'cashcred_withdrawal' && isset( $_GET['user_id'] ) && $_GET['user_id'] !='all') {

				$meta_query[] = array(
				'key'     => 'from',
				'value'   => $_GET['user_id'],
				'compare' => '='
				);  
			}
					  
			if( ! empty( $meta_query ) )
				$query->set( 'meta_query', $meta_query );  
		  
		}
		
		 /**
		 * Register Pending Payments
		 * @since 1.5
		 * @version 1.1
		 */
		protected function register_cashcred_payments() {

			$labels = array(
				'name'                => _x( 'cashCred Withdrawal', 'Post Type General Name', 'mycred' ),
				'singular_name'       => _x( 'cashCred Withdrawal', 'Post Type Singular Name', 'mycred' ),
				'menu_name'           => __( 'cashCred Withdrawal', 'mycred' ),
				'parent_item_colon'   => '',
				'all_items'           => __( 'cashCred Withdrawal', 'mycred' ),
				'view_item'           => '',
				'add_new_item'        => '',
				'add_new'             => '',
				'edit_item'           => __( 'Edit Withdrawal Request', 'mycred' ),
				'update_item'         => '',
				'search_items'        => '',
				'not_found'           => __( 'Not found in Trash', 'mycred' ),
				'not_found_in_trash'  => __( 'Not found in Trash', 'mycred' ),
			);
			$args = array(
				'labels'               => $labels,
				'supports'             => array( 'title', 'comments' ),
				'hierarchical'         => false,
				'public'               => false,
				'show_ui'              => true,
				'show_in_menu'         => false,
				'show_in_nav_menus'    => false,
				'show_in_admin_bar'    => false,
				'can_export'           => true,
				'has_archive'          => false,
				'exclude_from_search'  => true,
				'publicly_queryable'   => false,
				'register_meta_box_cb' => array( $this, 'add_metaboxes' )
			);
			register_post_type( MYCRED_CASHCRED_KEY, apply_filters( 'mycred_setup_cashcred_payment', $args ) );

		}

		/**
		 * Adjust Post Updated Messages
		 * @since 1.7
		 * @version 1.1
		 */
		public function post_updated_messages( $messages ) {

			$messages[ MYCRED_CASHCRED_KEY ] = array(
				0 => '',
				1 => __( 'Payment Updated.', 'mycred' ),
				2 => __( 'Payment Updated.', 'mycred' ),
				3 => __( 'Payment Updated.', 'mycred' ),
				4 => __( 'Payment Updated.', 'mycred' ),
				5 => __( 'Payment Updated.', 'mycred' ),
				6 => __( 'Payment Updated.', 'mycred' ),
				7 => __( 'Payment Updated.', 'mycred' ),
				8 => __( 'Payment Updated.', 'mycred' ),
				9 => __( 'Payment Updated.', 'mycred' ),
				10 => ''
			);

			return $messages;

		}

		/**
		 * Add Comment
		 * @since 1.7
		 * @version 1.0
		 */
		public function add_comment( $post_id, $event = '', $time = NULL ) {

			return cashcred_add_comment( $post_id, $event, $time );

		}

		/**
		 * Admin Notices
		 * @since 1.7
		 * @version 1.1
		 */
		public function admin_notices() {

			if ( isset( $_GET['post_type'] ) && $_GET['post_type'] == MYCRED_CASHCRED_KEY && isset( $_GET['credited'] ) ) {

				if ( $_GET['credited'] == 1 )
					echo '<div id="message" class="updated notice is-dismissible"><p>' . __( 'Pending payment successfully credited to account.', 'mycred' ) . '</p><button type="button" class="notice-dismiss"></button></div>';

				elseif ( $_GET['credited'] == 0 )
					echo '<div id="message" class="error notice is-dismissible"><p>' . __( 'Failed to credit the pending payment to account.', 'mycred' ) . '</p><button type="button" class="notice-dismiss"></button></div>';

			}

		}

		/**
		 * Add Admin Menu Item
		 * @since 1.7
		 * @version 1.1
		 */
		public function add_to_menu() {

			// In case we are using the Master Template feautre on multisites, and this is not the main
			// site in the network, bail.
			//if ( mycred_override_settings() && ! mycred_is_main_site() ) return;

			mycred_add_main_submenu(
				__( 'cashCred Withdrawal', 'mycred' ),
				__( 'cashCred Withdrawal', 'mycred' ),
				$this->core->get_point_editor_capability(),
				'edit.php?post_type=' . MYCRED_CASHCRED_KEY
			);

		}

		/**
		 * Parent File
		 * @since 1.7
		 * @version 1.0.1
		 */
		public function parent_file( $parent = '' ) {

			global $pagenow;

			if ( isset( $_GET['post'] ) && mycred_get_post_type( $_GET['post'] ) == MYCRED_CASHCRED_KEY && isset( $_GET['action'] ) && $_GET['action'] == 'edit' )
				return MYCRED_MAIN_SLUG;

			return $parent;

		}

		/**
		 * Sub Parent File
		 * @since 1.7.8
		 * @version 1.0
		 */
		public function subparent_file( $subparent = '', $parent = '' ) {

			global $pagenow;

			if ( ( $pagenow == 'edit.php' || $pagenow == 'post-new.php' ) && isset( $_GET['post_type'] ) && $_GET['post_type'] == MYCRED_CASHCRED_KEY ) {

				return 'edit.php?post_type=' . MYCRED_CASHCRED_KEY;
			
			}

			elseif ( $pagenow == 'post.php' && isset( $_GET['post'] ) && mycred_get_post_type( $_GET['post'] ) == MYCRED_CASHCRED_KEY ) {

				return 'edit.php?post_type=' . MYCRED_CASHCRED_KEY;

			}

			return $subparent;

		}

		/**
		 * Pending Payment Column Headers
		 * @since 1.5
		 * @version 1.0
		 */
		public function adjust_column_headers( $columns ) {
			 
			return array(
				'cb'       => $columns['cb'],
			 	'title'    => __( 'Request ID', 'mycred' ),
				'User'     => __( 'User', 'mycred' ),
				'Points'   => __( 'Points Withdrawal', 'mycred' ),
				'cost'     => __( 'Cost', 'mycred' ),
				'amount'   => __( 'Amount', 'mycred' ),
			 	'gateway'  => __( 'Gateway', 'mycred' ),
				'ctype'    => __( 'Point Type', 'mycred' ),
				'status'   => __( 'Status', 'mycred' ),
				'date'     => $columns['date'],
			);

		}
	

		/**
		 * Pending Payment Column Content
		 * @since 1.5
		 * @version 1.0
		 */
		public function adjust_column_content( $column_name, $post_id ) {

			global $mycred_modules;
			switch ( $column_name ) {
				case 'User' :
					
					$from = (int) check_site_get_post_meta( $post_id, 'from', true );
					$from = (int) check_site_get_post_meta( $post_id, 'from', true );
					$user = get_userdata( $from );

					if ( isset( $user->display_name ) )
					echo '<a href="' . esc_url( admin_url( add_query_arg( array( 'post_type' => MYCRED_CASHCRED_KEY . '&user_id='.$user->ID  ), 'edit.php' ) ) ) . '">' . $user->display_name . '</a>';
					else
						echo 'ID: ' . $from;

				break;
				case 'Points';

					$type   = check_site_get_post_meta( $post_id, 'point_type', true );
					$points = check_site_get_post_meta( $post_id, 'points', true );
					$mycred = mycred( $type );

					echo $mycred->format_creds( $points );

				break;
				case 'cost';

					$cost     = check_site_get_post_meta( $post_id, 'cost', true );
					$currency = check_site_get_post_meta( $post_id, 'currency', true );
					echo $cost . ' ' . $currency;

				break;
				case 'amount';
					
					$points = check_site_get_post_meta( $post_id, 'points', true );
					$cost     = check_site_get_post_meta( $post_id, 'cost', true );
					$currency = check_site_get_post_meta( $post_id, 'currency', true );
					echo $currency .' ' . $points * $cost;

				break;
				case 'gateway';

					$gateway   = check_site_get_post_meta( $post_id, 'gateway', true );
					$installed = $mycred_modules['solo']['cashcred']->get();

					if ( isset( $installed[ $gateway ] ) )
						echo $installed[ $gateway ]['title'];
					else
						echo $gateway;

				break;
				case 'ctype';

					$type = check_site_get_post_meta( $post_id, 'point_type', true );
					
					if ( isset( $this->point_types[ $type ] ) )
						echo $this->point_types[ $type ];
					else
						echo $type;

				break;
					case 'status';
					$status = check_site_get_post_meta( $post_id, 'status', true );
					echo "<div class='cashcred_bages'><span class='cashcred_" . $status . "'>" . $status . "</span></div>";

				break;
				
			}

		}

		/**
		 * Adjust Bulk Actions
		 * @since 1.5
		 * @version 1.0
		 */
		public function bulk_actions( $actions ) {

			unset( $actions['edit'] );
			return $actions;

		}

		 

		/**
		 * Edit Pending Payment Style
		 * @since 1.7
		 * @version 1.0.1
		 */
		public function edit_pending_payment_style() {

			global $post_type;

			if ( $post_type !== MYCRED_CASHCRED_KEY ) return;

			wp_enqueue_style( 'mycred-bootstrap-grid' );
			wp_enqueue_style( 'mycred-forms' );

			add_filter( 'postbox_classes_buycred_payment_buycred-pending-payment',  array( $this, 'metabox_classes' ) );
			add_filter( 'postbox_classes_buycred_payment_cashcred-comments', array( $this, 'metabox_classes' ) );

		}

		/**
		 * Add Metaboxes
		 * @since 1.7
		 * @version 1.1
		 */
		public function add_metaboxes() {

			add_meta_box(
				'cashcred_withdrawal_request',
				__( 'Withdrawal Request', 'mycred' ),
				array( $this, 'metabox_pending_payment' ),
				MYCRED_CASHCRED_KEY,
				'normal',
				'high'
			);
			
			add_meta_box(
				'cashcred-user-info',
				__( 'User Information', 'mycred' ),
				array( $this, 'cashcred_user_info' ),
				MYCRED_CASHCRED_KEY,
				'normal',
				'high'
			);
			
			add_meta_box(
				'payment_gateway_detail',
				__( 'Payment Gateway Detail', 'mycred' ),
				array( $this, 'payment_gateway_detail' ),
				MYCRED_CASHCRED_KEY,
				'side',
				'high'
			);

			add_meta_box(
				'cashcred-payment-status',
				__( 'Withdrawal Payment Status', 'mycred' ),
				array( $this, 'cashcred_payment_pay' ),
				MYCRED_CASHCRED_KEY,
				'side',
				'high'
			);
	 
			add_meta_box(
				'cashcred-comments',
				__( 'History', 'mycred' ),
				array( $this, 'metabox_cashcred_comments' ),
				MYCRED_CASHCRED_KEY,
				'normal',
				'default'
			);
			
			$mycred_pref_cashcreds = mycred_get_cashcred_settings();
			
			if( isset( $mycred_pref_cashcreds["debugging"] ) && $mycred_pref_cashcreds["debugging"] == 'enable' ) {
				
				add_meta_box(
					'cashcred-developer-log',
					__( 'Debugging Log', 'mycred' ),
					array( $this, 'cashcred_developer_log' ),
					MYCRED_CASHCRED_KEY,
					'normal',
					'default'
				);		
			
			}
					
			remove_meta_box( 'commentstatusdiv', MYCRED_CASHCRED_KEY, 'normal' );
			remove_meta_box( 'commentsdiv', MYCRED_CASHCRED_KEY, 'normal' );

			remove_meta_box( 'submitdiv', MYCRED_CASHCRED_KEY, 'side' );

			add_meta_box(
				'submitdiv',
				__( 'Actions', 'mycred' ),
				array( $this, 'metabox_pending_actions' ),
				MYCRED_CASHCRED_KEY,
				'side',
				'high'
			);

		}
		
		
		public function cashcred_developer_log () {
		
			$counter = (int) mycred_get_post_meta( get_the_ID(), 'cashcred_log_counter', true );
			$orderdesc = $counter;
			
			for ( $log = 1; $log <= $counter; $log++ ) {
				
				$payment_log = ''; 
					
				$payment_log = mycred_get_post_meta( get_the_ID(), 'cashcred_log_' . $orderdesc, true );
				 
				echo "<pre>";	
				echo "<b>Date Time: </b>".$payment_log['datetime']."<br>";  
				echo "<b>Payment Gateway: </b>".$payment_log['payment_gateway']."<br>";
				print_r( json_decode( $payment_log["response"] ) );	
				echo "</pre>";
				
				$orderdesc = $counter - 1; 
		
			}
			
		}
		
		
		public function payment_gateway_detail(){
		
			global $mycred_modules;	

			foreach ( $mycred_modules['solo']['cashcred']->get() as $gateway_id => $info ) {
				
				if(!$mycred_modules['solo']['cashcred']->is_active( $gateway_id )) continue ;
				
				$MyCred_payment_setting_call = new $info['callback'][0]($gateway_id);
				$MyCred_payment_setting_call->cashcred_payment_settings($gateway_id) ;
				  
			}
		}
		
		public function cashcred_payment_pay(){
			
			global $mycred_modules;	

			$user_id = get_post_meta( get_the_ID(), 'from', true );
			$status = get_post_meta( get_the_ID(), 'status', true );
			$transfer_date = get_post_meta( get_the_ID(), 'cashcred_payment_transfer_date', true );

			$manual = get_post_meta( get_the_ID(), 'manual', true );
			 
			$cashcred_user_settings = get_user_meta( $user_id, 'cashcred_user_settings', true );
			 
			$get_payment_settings = cashcred_get_payment_settings( get_the_ID() );
			
			?>
			 
			<div class="row">
				<div class="col-md-5 col-sm-12">
					<div class="form-group"><strong>Amount Transfer :</strong></div>
				</div>
				<div class="type-cashcred_withdrawal col-md-4 col-sm-12">
					<div class="form-group"><span class="cashcred_<?php echo $status; ?>"><?php echo $status; ?></span></div>
				</div>
			</div>
			
			<div class="row">
				<div class="col-md-5 col-sm-12">
					<div class="form-group"> <strong>Payment Method :</strong> </div>
				</div>
				<div class="col-md-4 col-sm-12">
					<div class="form-group">
						<?php 
						foreach ( $mycred_modules['solo']['cashcred']->get() as $gateway_id => $info ) {
							if($get_payment_settings->gateway_id == $gateway_id ){
								echo $info['title'];
								?><input type="hidden" name="cashcred_pay_method" value="<?php echo $gateway_id;?>"><?php
							}
						}
						?>
					</div>
				</div>
			</div>
			
			<div class="row">
				<div class="col-md-5 col-sm-12">
					<div class="form-group"> <strong>Amount :</strong> </div>
				</div>

				<div class="col-md-4 col-sm-12">
					<div class="form-group"><?php echo $get_payment_settings->currency ." ". $get_payment_settings->points * $get_payment_settings->cost;?></div>
				</div>
			</div>
			
			<div class="row">
				<div class="col-md-5 col-sm-12">
					<div class="form-group"><strong>Transfer Date :</strong></div>
				</div>

				<div class="col-md-4 col-sm-12">
					<div class="form-group"> 
						<span class="entry-date">
							<?php
								if($transfer_date){
									echo date( 'Y-m-d H:i:s', strtotime( $transfer_date ) ); 
								}else{
									echo "-";
								}
							?>
						</span> 
					</div>
				</div>
			</div>
			
			<div class="row">
				<div class="col-md-5 col-sm-12">
					<div class="form-group"> <strong>Transfer Mode :</strong> </div>
				</div>

				<div class="col-md-4 col-sm-12">
					<div class="form-group"><?php echo $manual; ?></div>
				</div>
			</div>
			<br>
			<?php 
				
				$disabled = '';

				if( $status == 'Approved' || $this->is_paid_request( $user_id, get_the_ID() ) ) 
					$disabled =  'disabled';

			?>
			<input type="hidden" name="cashcred_create_nonce" value="<?php echo wp_create_nonce( 'cashcred_create_nonce' ); ?>">
			<button type="button" id="cashcred_paynow" <?php echo $disabled; ?> class="button button-secondary btn-lg btn-block">
				<div class="spinner"></div>
				<span class="cashcred_paynow_text">Pay Now</span>
			</button>
			<div id="placeholder"></div>
			<div id="payment_response"></div>
					 
			<script type="text/javascript">
			
				jQuery( "#cashcred_paynow" ).click(function() {
					
				var confirm_payment = confirm("Are you sure you want to process now");
				if ( confirm_payment == false ) {
					return false;
				}
			 
				form 		= 	jQuery('#post');
				btn_paynow	=	jQuery(this);
				placeholder =	jQuery('#placeholder');
				spinner		=	jQuery('#cashcred-payment-status .spinner');
				
				var data = jQuery(form).serialize() + "&action=cashcred_pay_now";

				jQuery.ajax({
					type: 'POST',
					url: "<?php echo admin_url( 'admin-ajax.php' );?>",
					data: data,
					dataType: "json",
					beforeSend: function() {
						
						// setting a timeout
						jQuery(spinner).addClass('is-active');
						jQuery('.cashcred_paynow_text').text('Loading...');
						jQuery('#payment_response').slideUp();
						jQuery(btn_paynow).prop('disabled', true);
						jQuery( '#payment_response' ).removeClass();
						
					},
					success: function( response ) {
						 
						jQuery( '.cashcred_paynow_text' ).text('Pay Now');
						jQuery( '#payment_response' ).html(response.message);
						jQuery( '#payment_response' ).addClass(""+ response.status +"");
						jQuery( '#payment_response' ).slideDown();
						
						if(response.status == true){
							jQuery( '.disabled_fields' ).prop('disabled', true);
							jQuery( '.readonly_fields' ).prop('readonly', true);
							jQuery( btn_paynow ).prop('disabled', true);
							jQuery('.cashcred_Approved').remove();
							
							html_approved = "<span class='cashcred_Approved'>Approved</span>";
							comments = "<li><time>"+response.date+"</time><p>"+response.comments+"</p></li>";
							
							jQuery( '#cashcred-comments .history').prepend(comments);
							jQuery('.type-cashcred_withdrawal .form-group').html(html_approved);
							jQuery( '#cashcred-payment-status .entry-date' ).html(response.date); 
							jQuery('#cashcred_post_ststus select').get(0).selectedIndex = 1;
							jQuery( '#user_total_cashcred' ).html(response.total); 

						}else{
							jQuery( btn_paynow ).prop('disabled', false);
						}
						
						
						if( jQuery('#cashcred-developer-log').length && response.log != null ) {
						
							jQuery( '#cashcred-developer-log .inside' ).html( response.log ); 
						
						}
						
					},
					error: function(xhr) {
					
						// if error occured
						alert("Error occured.please try again");

						jQuery( '#payment_response' ).html(xhr.responseText);
						jQuery( '#payment_response' ).addClass('false');					
						jQuery( btn_paynow ).prop('disabled', false);
						jQuery( '.cashcred_paynow_text' ).text('Pay Now');
					},
					complete: function() {
						
						jQuery(spinner).removeClass('is-active');
						
					}
				});

			});
			</script> 
			<?php 
		}
		
		
		public function cashcred_user_info() {
			
			$user_id  			  = get_post_meta( get_the_ID(), 'from', true );
			$user_obj 			  = get_user_by( 'id', $user_id );
			$get_payment_settings = cashcred_get_payment_settings( get_the_ID() );
		
			?>
			<div class="row">
			
				<div class="col-md-4 col-sm-12">
					<div class="form-group"> <strong>User ID :</strong> </div>
				</div>
				
				<div class="col-md-4 col-sm-12">
					<div class="form-group"> <?php echo $user_id ?> </div>
				</div>
			
			</div>
			
			<div class="row">
			
				<div class="col-md-4 col-sm-12">
					<div class="form-group"> <strong>User Name :</strong> </div>
				</div>
				<div class="col-md-4 col-sm-12">
					<div class="form-group"> <?php echo $user_obj->data->display_name;?></div>
				</div>
			
			</div>
			
			<div class="row">
			
				<div class="col-md-4 col-sm-12">
					<div class="form-group"> <strong>User Email :</strong> </div>
				</div>
				<div class="col-md-4 col-sm-12">
					<div class="form-group"> <?php echo $user_obj->data->user_email;?></div>
				</div>
			
			</div>
			
			<div class="row">
			
				<div class="col-md-4 col-sm-12">
					<div class="form-group"> <strong>User IP :</strong> </div>
				</div>
				<div class="col-md-4 col-sm-12">
					<div class="form-group"> <?php echo get_post_meta( get_the_ID(), 'user_ip', true );?></div>
				</div>
			
			</div>
			
			
			<div class="row">
			
				<div class="col-md-4 col-sm-12">
					<div class="form-group"> <strong>User withdraw total amount:</strong> </div>
				</div>
				<div class="col-md-4 col-sm-12">
					<div class="form-group" id="user_total_cashcred"><?php 
					if(get_user_meta( $user_id, 'cashcred_total', true )){
						echo $get_payment_settings->currency .' '. get_user_meta( $user_id, 'cashcred_total', true );
					}else{
						echo 0;
					}
					?>
					</div>
				</div>
			
			</div>
			<div class="row">
			
				<div class="col-md-12 col-sm-12">
					<div class="form-group"> <strong><a target="_blank" href="edit.php?post_type=cashcred_withdrawal&user_id=<?php echo $user_id ?>">View user all withdrawal request.</a></strong> </div>
				</div>
			 
			
			</div>
			<input type="hidden" name="user_id" value="<?php echo $user_id ?>">
			
			<style>
				#cashcred-user-info .row,#cashcred-payment-status .row{
					border-bottom: 1px solid #e5e5e5;
					padding-top: 4px;
					padding-bottom: 6px;
				}
				
				#cashcred_post_ststus select, .btn-block {
					width:100%;
				}
			</style>
			<?php 
		
		}
		
		

		/**
		 * Metabox: Pending Actions
		 * @since 1.7
		 * @version 1.0
		 */
		public function metabox_pending_actions( $post ) {

			$payout_url = add_query_arg( array(
				'post_type' => $post->post_type,
				'credit'    => $post->ID,
				'token'     => wp_create_nonce( 'buycred-payout-pending' )
			), admin_url( 'edit.php' ) );

			$delete_url = get_delete_post_link( $post->ID );
			
			$status = mycred_get_post_meta( $post->ID, 'status', true );

?>
<div class="submitbox mycred-metabox" id="submitpost">
	<div id="minor-publishing">
		<div style="display:none;">
		<?php submit_button( __( 'Save', 'mycred' ), 'button', 'save' ); ?>
		</div>

		<div id="minor-publishing-actions">

			<div id="cashcred_post_ststus">	 
		
				<select name="status">
					<option value="Pending" <?php echo $status  == "Pending" ? "selected" : "" ?>>
						<?php _e('Pending', 'cashcred'); ?>
					</option>		
					<option value="Approved" <?php echo $status  == "Approved" ? "selected" : "" ?>>
						<?php _e('Approved', 'cashcred'); ?>
					</option>
					<option value="Cancelled" <?php echo $status  == "Cancelled" ? "selected" : "" ?>>
						<?php _e('Cancelled', 'cashcred'); ?>
					</option>
				</select>
				
			</div>	 
		 
			<div>
				<a href="<?php echo $delete_url; ?>" class="button button-secondary button-block"><?php _e( 'Trash', 'mycred' ); ?></a>
			</div>

		</div>

		<div class="clear"></div>
	</div>
	<div id="major-publishing-actions">

		<div id="publishing-action">
			<span class="spinner"></span>

			<input type="submit" id="publish" class="button button-primary primary button-large" value="<?php _e( 'Save Changes', 'mycred' ); ?>" />

		</div>
		<div class="clear"></div>
	</div>
</div>
<?php

		}

		/**
		 * Metabox: Pending Payment
		 * @since 1.7
		 * @version 1.0.1
		 */
		public function metabox_pending_payment( $post ) {

			global $mycred_modules;
			
			$readonly=$disabled='';

			$payment_status = get_post_meta( get_the_ID(), 'status', true );

			$user_id = mycred_get_post_meta( get_the_ID(), 'from', true );
			
			if($payment_status == 'Approved' || $this->is_paid_request( $user_id, get_the_ID() ) ) 
				$readonly =  'readonly';
		
			if( $payment_status == 'Approved' || $this->is_paid_request( $user_id, get_the_ID() ) ) 
				$disabled =  'disabled';
			
			$pending_payment = cashcred_get_payment_settings( $post->ID );

			if ( $pending_payment->point_type == $this->core->cred_id )
				$mycred = $this->core;
			else
				$mycred = mycred( $pending_payment->point_type );			
?>
<div class="form">
	<div class="row">
		<div class="col-md-2 col-sm-6">
			<div class="form-group">
				<label for="cashcred-pending-payment-point_type"><?php _e( 'Point Type', 'mycred' ); ?></label>
 
				<select name="cashcred_pending_payment[point_type]" <?php echo $disabled; ?> id="cashcred-pending-payment-point_type" class="form-control disabled_fields">
				<?php

				foreach ( mycred_get_types() as $key => $point_type  ) {
					
					echo '<option value="' . $key . '"';
					if ( $pending_payment->point_type == $key ) echo ' selected="selected"';
					echo '>' . mycred_get_point_type_name( $key, false ) . '</option>';

				}

				?>
				</select>
 
			</div>
		</div>
		<div class="col-md-2 col-sm-6">
			<div class="form-group">
				<label for="cashcred-pending-payment-gateway"><?php _e( 'Gateway', 'mycred' ); ?></label>
				<select name="cashcred_pending_payment[gateway]" <?php echo $disabled; ?> id="cashcred-pending-payment-gateway" class="form-control disabled_fields">
<?php

			foreach ( $mycred_modules['solo']['cashcred']->get() as $gateway_id => $info ) {

				echo '<option value="' . $gateway_id . '"';
				if ( $pending_payment->gateway_id == $gateway_id ) echo ' selected="selected"';
				if ( ! $mycred_modules['solo']['cashcred']->is_active( $gateway_id ) ) echo ' disabled="disabled"';
				echo '>' . $info['title'] . '</option>';

			}

?>
				</select>
			</div>
		</div>
		<div class="col-md-2 col-sm-6">
			<div class="form-group">
				<label for="cashcred-pending-payment-points"><?php _e( 'Points', 'mycred' ); ?></label>
				<input type="text" <?php echo $readonly; ?> name="cashcred_pending_payment[points]" id="cashcred-pending-payment-points" class="form-control readonly_fields" value="<?php echo $mycred->number( $pending_payment->points ); ?>" />
			</div>
		</div>
		<div class="col-md-2 col-sm-6">
			<div class="form-group">
				<label for="cashcred-pending-payment-cost"><?php _e( 'Cost', 'mycred' ); ?></label>
				<input type="text" <?php echo $readonly; ?> name="cashcred_pending_payment[cost]" id="cashcred-pending-payment-cost" class="form-control readonly_fields" value="<?php echo esc_attr( $pending_payment->cost ); ?>" />
			</div>
		</div>
		<div class="col-md-2 col-sm-6">
			<div class="form-group">
				<label for="cashcred-pending-payment-currency"><?php _e( 'Currency', 'mycred' ); ?></label>
				<input type="text" <?php echo $readonly; ?> name="cashcred_pending_payment[currency]" id="cashcred-pending-payment-currency" class="form-control readonly_fields" value="<?php echo esc_attr( $pending_payment->currency ); ?>" />
			</div>
		</div>
	</div>
</div>
<?php
		}

		/**
		 * Metabox: Pending Payment Comments
		 * @since 1.7
		 * @version 1.0
		 */
		public function metabox_cashcred_comments( $post ) {

			$comments = get_comments( array( 'post_id' => $post->ID ) );

			echo '<ul class="history">';

			if ( empty( $comments ) ) {

				$c                  = new StdClass();
				$c->comment_date    = $post->post_date;
				$c->comment_content = __( 'Withdrawal request created.', 'mycred' );

				$event = $this->add_comment( $post->ID, $c->comment_content, $c->comment_date );
				if ( $event === false )
					$c->comment_content .= ' Unsaved';

				$comments[] = $c;

			}

			foreach ( $comments as $comment ) {

				$comment_date = isset( $comment->comment_date_gmt ) ? $comment->comment_date_gmt : $post->post_date;

				echo '<li><time>' . $comment_date . '</time><p>' . $comment->comment_content . '</p></li>';

			}

			echo '</ul>';

		}

		/**
		 * Save Pending Payment
		 * @since 1.7
		 * @version 1.0
		 */
		public function save_pending_payment( $post_id, $post ) {
 
		 	if ( ! $this->core->user_is_point_editor() || ! isset( $_POST['cashcred_pending_payment'] ) ) return;

			$pending_payment = $_POST['cashcred_pending_payment'];

			$old_status = mycred_get_post_meta( $post_id, 'status', true );
			$new_status = sanitize_text_field( $_POST['status'] );

			$user_settings = mycred_get_user_meta( $_POST['user_id'], cashcred_get_user_settings(), '', true );
			$updated_user_settings = $_POST['cashcred_user_settings'];
	 
			$changed_fields  = array();

			$withdraw_request_messages = array(
				'point_type' => __( 'Point type', 'mycred' ),
				'gateway'    => __( 'Gateway', 'mycred' ),
				'points'     => __( 'Points', 'mycred' ),
				'cost'       => __( 'Cost', 'mycred' ),
				'currency'   => __( 'Currency', 'mycred' )
			);

			mycred_cashcred_update_status( $post_id, 'status', $new_status );

			mycred_update_user_meta( $_POST['user_id'], cashcred_get_user_settings(), '', $updated_user_settings );

			foreach ( $pending_payment as $meta_key => $meta_value ) {

				$new_value = sanitize_text_field( $meta_value );
				$old_value = check_site_get_post_meta( $post_id, $meta_key, true );

				if ( $new_value != $old_value ) {
					mycred_cashcred_update_status( $post_id, $meta_key, $new_value );
					$changed_fields[] = $withdraw_request_messages[ $meta_key ];
				}
				
			}

			$changes = join( ", ", $changed_fields );
			
			$user = wp_get_current_user();

			if ( serialize( $user_settings ) != serialize( $updated_user_settings ) ) {
				$this->add_comment( $post_id, sprintf( __( 'User\'s detail updated by %s', 'mycred' ), $user->user_login ) );
			}

			if ( ! empty( $changed_fields ) ) {
				$this->add_comment( $post_id, sprintf( __( '%s updated by %s', 'mycred' ), $changes, $user->user_login ) );
			}

			if ( $old_status != $new_status ) {
				$this->add_comment( $post_id, sprintf( __( 'Status changed from %s to %s updated by %s', 'mycred' ), $old_status, $new_status, $user->user_login ) );
			}

		}
		
		/**
		 * Withdraw Request Paid
		 * @version 1.0
		 */
		public function is_paid_request( $user_id, $post_id ) {

			$args = array(
				'ref'     => 'cashcred_withdrawal',
				'user_id' => $user_id,
				'ref_id'  => $post_id
			);

			$log = new myCRED_Query_Log( $args );

			return $log->have_entries();

		}

	}
endif;

/**
 * Load buyCRED Pending Module
 * @since 1.7
 * @version 1.0
 */
if ( ! function_exists( 'mycred_load_cashcred_pending_addon' ) ) :
	function mycred_load_cashcred_pending_addon( $modules, $point_types ) {

		$modules['solo']['cashcred-pending'] = new cashCRED_Pending_Payments();
		$modules['solo']['cashcred-pending']->load();

		return $modules;

	}
endif;
add_filter( 'mycred_load_modules', 'mycred_load_cashcred_pending_addon', 40, 2 );
