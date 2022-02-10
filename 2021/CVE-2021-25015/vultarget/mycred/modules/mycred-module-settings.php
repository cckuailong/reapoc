<?php
if ( ! defined( 'myCRED_VERSION' ) ) exit;

define( 'myCRED_Settings',              __FILE__ );

define( 'myCRED_Settings_VERSION',      '1.3' );

/**
 * myCRED_Settings_Module class
 * @since 0.1
 * @version 1.5
 */
if ( ! class_exists( 'myCRED_Settings_Module' ) ) :
	class myCRED_Settings_Module extends myCRED_Module {

		/**
		 * Construct
		 */
		public function __construct( $type = MYCRED_DEFAULT_TYPE_KEY ) {

			parent::__construct( 'myCRED_Settings_Module', array(
				'module_name' => 'general',
				'option_id'   => 'mycred_pref_core',
				'labels'      => array(
					'menu'        => __( 'Settings', 'mycred' ),
					'page_title'  => __( 'Settings', 'mycred' ),
					'page_header' => __( 'Settings', 'mycred' )
				),
				'screen_id'   => MYCRED_SLUG . '-settings',
				'accordion'   => true,
				'menu_pos'    => 100
			), $type );

		}

		/**
		 * Init
		 * @since 1.7
		 * @version 1.0
		 */
		public function module_init() {

			// Delete users log entries when the user is deleted
			if ( isset( $this->core->delete_user ) && $this->core->delete_user )
				add_action( 'delete_user', array( $this, 'action_delete_users_log_entries' ) );

			add_action( 'wp_ajax_mycred-action-empty-log',       array( $this, 'action_empty_log' ) );
			add_action( 'wp_ajax_mycred-action-reset-accounts',  array( $this, 'action_reset_balance' ) );
			add_action( 'wp_ajax_mycred-action-export-balances', array( $this, 'action_export_balances' ) );
			add_action( 'wp_ajax_mycred-action-generate-key',    array( $this, 'action_generate_key' ) );
			add_action( 'wp_ajax_mycred-action-max-decimals',    array( $this, 'action_update_log_cred_format' ) );

		}

		/**
		 * Admin Init
		 * @since 1.3
		 * @version 1.0.1
		 */
		public function module_admin_init() {

			
			
			if ( isset( $_GET['do'] ) && $_GET['do'] == 'export' )
				$this->load_export();

		}

		/**
		 * Empty Log Action
		 * @since 1.3
		 * @version 1.4
		 */
		public function action_empty_log() {

			// Security
			check_ajax_referer( 'mycred-management-actions', 'token' );

			// Access
			if ( ! is_user_logged_in() || ! $this->core->user_is_point_admin() )
				wp_send_json_error( 'Access denied' );

			// Type
			if ( ! isset( $_POST['type'] ) )
				wp_send_json_error( 'Missing point type' );

			$type = sanitize_key( $_POST['type'] );

			global $wpdb, $mycred_log_table;

			// If we only have one point type we truncate the log
			if ( count( $this->point_types ) == 1 && $type == MYCRED_DEFAULT_TYPE_KEY )
				$wpdb->query( "TRUNCATE TABLE {$mycred_log_table};" );

			// Else we want to delete the selected point types only
			else
				$wpdb->delete(
					$mycred_log_table,
					array( 'ctype' => $type ),
					array( '%s' )
				);

			// Count results
			$total_rows = $wpdb->get_var( "SELECT COUNT(*) FROM {$mycred_log_table} WHERE ctype = '{$type}';" );
			$wpdb->flush();

			// Response
			wp_send_json_success( $total_rows );

		}

		/**
		 * Reset All Balances Action
		 * @since 1.3
		 * @version 1.4.1
		 */
		public function action_reset_balance() {

			// Type
			if ( ! isset( $_POST['type'] ) )
				wp_send_json_error( 'Missing point type' );

			$type = sanitize_key( $_POST['type'] );
			if ( $type != $this->mycred_type ) return;

			// Security
			check_ajax_referer( 'mycred-management-actions', 'token' );

			// Access
			if ( ! is_user_logged_in() || ! $this->core->user_is_point_admin() )
				wp_send_json_error( 'Access denied' );

			global $wpdb;

			$wpdb->delete(
				$wpdb->usermeta,
				array( 'meta_key' => mycred_get_meta_key( $type, '' ) ),
				array( '%s' )
			);

			$wpdb->delete(
				$wpdb->usermeta,
				array( 'meta_key' => mycred_get_meta_key( $type, '_total' ) ),
				array( '%s' )
			);

			do_action( 'mycred_zero_balances', $type );

			// Response
			wp_send_json_success( __( 'Accounts successfully reset', 'mycred' ) );

		}

		/**
		 * Export User Balances
		 * @filter mycred_export_raw
		 * @since 1.3
		 * @version 1.2
		 */
		public function action_export_balances() {

			// Security
			check_ajax_referer( 'mycred-management-actions', 'token' );

			global $wpdb;

			// Log Template
			$log  = sanitize_text_field( $_POST['log_temp'] );

			// Type
			if ( ! isset( $_POST['type'] ) )
				wp_send_json_error( 'Missing point type' );

			$type = sanitize_text_field( $_POST['type'] );

			// Identify users by
			switch ( $_POST['identify'] ) {

				case 'ID' :

					$SQL = "SELECT user_id AS user, meta_value AS balance FROM {$wpdb->usermeta} WHERE meta_key = %s;";

				break;

				case 'email' :

					$SQL = "SELECT user_email AS user, meta_value AS balance FROM {$wpdb->usermeta} LEFT JOIN {$wpdb->users} ON {$wpdb->usermeta}.user_id = {$wpdb->users}.ID WHERE {$wpdb->usermeta}.meta_key = %s;";

				break;

				case 'login' :

					$SQL = "SELECT user_login AS user, meta_value AS balance FROM {$wpdb->usermeta} LEFT JOIN {$wpdb->users} ON {$wpdb->usermeta}.user_id = {$wpdb->users}.ID WHERE {$wpdb->usermeta}.meta_key = %s;";

				break;

			}

			$query = $wpdb->get_results( $wpdb->prepare( $SQL, $type ) );

			if ( empty( $query ) )
				wp_send_json_error( __( 'No users found to export', 'mycred' ) );

			$array = array();
			foreach ( $query as $result ) {
				$data = array(
					'mycred_user'   => $result->user,
					'mycred_amount' => $this->core->number( $result->balance ),
					'mycred_ctype'  => $type
				);

				if ( ! empty( $log ) )
					$data = array_merge_recursive( $data, array( 'mycred_log' => $log ) );

				$array[] = $data;
			}

			set_transient( 'mycred-export-raw', apply_filters( 'mycred_export_raw', $array ), 3000 );

			// Response
			wp_send_json_success( admin_url( 'admin.php?page=' . MYCRED_SLUG . '-settings&do=export' ) );

		}

		/**
		 * Generate Key Action
		 * @since 1.3
		 * @version 1.1
		 */
		public function action_generate_key() {

			// Security
			check_ajax_referer( 'mycred-management-actions', 'token' );

			// Response
			wp_send_json_success( wp_generate_password( 16, true, true ) );

		}


		/**
         * Get Point Image
         * @since 2.2
         * @version 1.0
         */
        public function get_point_image( $attachment_id, $point_type_field ) {

            $image = false;

            if ( $attachment_id > 0 ) {

                $_image = wp_get_attachment_url( $attachment_id );
		
                if ( strlen( $_image ) > 5 )
				{
					$image = "<img src='$_image' alt='Point Type image' /><input type='hidden' name='$point_type_field' value='{$attachment_id}' />";
				}

            }

            return $image;

        }

		/**
		 * Update Log Cred Format Action
		 * Will attempt to modify the myCRED log's cred column format.
		 * @since 1.6
		 * @version 1.0.1
		 */
		public function action_update_log_cred_format() {

			// Security
			check_ajax_referer( 'mycred-management-actions', 'token' );

			if ( ! isset( $_POST['decimals'] ) || $_POST['decimals'] == '' || $_POST['decimals'] > 20 )
				wp_send_json_error( __( 'Invalid decimal value.', 'mycred' ) );

			if ( ! $this->is_main_type )
				wp_send_json_error( 'Invalid Use' );

			$decimals = absint( $_POST['decimals'] );

			global $wpdb, $mycred_log_table;

			if ( $decimals > 0 ) {
				$format = 'decimal';
				if ( $decimals > 4 )
					$cred_format = "decimal(32,$decimals)";
				else
					$cred_format = "decimal(22,$decimals)";
			}
			else {
				$format      = 'bigint';
				$cred_format = 'bigint(22)';
			}

			// Alter table
			$results = $wpdb->query( "ALTER TABLE {$mycred_log_table} MODIFY creds {$cred_format} DEFAULT NULL;" );

			// If we selected no decimals and we have multiple point types, we need to update
			// their settings to also use no decimals.
			if ( $decimals == 0 && count( $this->point_types ) > 1 ) {
				foreach ( $this->point_types as $type_id => $label ) {

					$new_type_core = mycred_get_option( 'mycred_pref_core_' . $type_id );
					if ( ! isset( $new_type_core['format']['decimals'] ) ) continue;

					$new_type_core['format']['type']     = $format;
					$new_type_core['format']['decimals'] = 0;
					mycred_update_option( 'mycred_pref_core_' . $type_id, $new_type_core );

				}
			}

			// Save settings
			$new_core                       = $this->core->core;
			$new_core['format']['type']     = $format;
			$new_core['format']['decimals'] = $decimals;
			mycred_update_option( 'mycred_pref_core', $new_core );

			// Send the good news
			wp_send_json_success( array(
				'url'   => esc_url( add_query_arg( array( 'page' => MYCRED_SLUG . '-settings', 'open-tab' => 0 ), admin_url( 'admin.php' ) ) ),
				'label' => __( 'Log Updated', 'mycred' )
			) );

		}

		/**
		 * Load Export
		 * Creates a CSV export file of the 'mycred-export-raw' transient.
		 * @since 1.3
		 * @version 1.1
		 */
		public function load_export() {

			// Security
			if ( $this->core->user_is_point_editor() ) {

				$export = get_transient( 'mycred-export-raw' );
				if ( $export === false ) return;

				if ( isset( $export[0]['mycred_log'] ) )
					$headers = array( 'mycred_user', 'mycred_amount', 'mycred_ctype', 'mycred_log' );
				else
					$headers = array( 'mycred_user', 'mycred_amount', 'mycred_ctype' );	

				require_once myCRED_ASSETS_DIR . 'libs/parsecsv.lib.php';
				$csv = new parseCSV();

				delete_transient( 'mycred-export-raw' );
				$csv->output( true, 'mycred-balance-export.csv', $export, $headers );

				die;

			}

		}

		/**
		 * Delete Users Log Entries
		 * Will remove a given users log entries.
		 * @since 1.4
		 * @version 1.1
		 */
		public function action_delete_users_log_entries( $user_id ) {

			global $wpdb, $mycred_log_table;

			$wpdb->delete(
				$mycred_log_table,
				array( 'user_id' => $user_id, 'ctype' => $this->mycred_type ),
				array( '%d', '%s' )
			);

		}

		/**
		 * Scripts & Styles
		 * @since 1.7
		 * @version 1.1
		 */
		public function scripts_and_styles() {


			wp_enqueue_media();

			wp_register_script(
				'mycred-type-management',
				plugins_url( 'assets/js/mycred-type-management.js', myCRED_THIS ),
				array( 'jquery', 'jquery-ui-core', 'jquery-ui-dialog', 'jquery-effects-core', 'jquery-effects-slide' ),
				myCRED_VERSION . '.1'
			);

			wp_enqueue_style( MYCRED_SLUG . '-select2-style' );

			wp_enqueue_script( MYCRED_SLUG . '-select2-script' );

		}

		/**
		 * Settings Header
		 * Inserts the export styling
		 * @since 1.3
		 * @version 1.2.3
		 */
		public function settings_header() {

			global $wp_filter, $mycred;

			// Allows to link to the settings page with a defined module to be opened
			// in the accordion. Request must be made under the "open-tab" key and should
			// be the module name in lowercase with the myCRED_ removed.
			$this->accordion_tabs = array( 'core' => 0, 'management' => 1, 'point-types' => 2, 'exports_module' => 3 );

			// Check if there are registered action hooks for mycred_after_core_prefs
			$count = 3;
			if ( isset( $wp_filter['mycred_after_core_prefs'] ) ) {

				// If remove access is enabled
				$settings = mycred_get_remote();
				if ( $settings['enabled'] )
					$this->accordion_tabs['remote'] = $count++;

				foreach ( $wp_filter['mycred_after_core_prefs'] as $priority ) {

					foreach ( $priority as $key => $data ) {

						if ( ! isset( $data['function'] ) ) continue;

						if ( ! is_array( $data['function'] ) )
							$this->accordion_tabs[ $data['function'] ] = $count++;

						else {

							foreach ( $data['function'] as $id => $object ) {

								if ( isset( $object->module_id ) ) {
									$module_id = str_replace( 'myCRED_', '', $object->module_id );
									$module_id = strtolower( $module_id );
									$this->accordion_tabs[ $module_id ] = $count++;
								}

							}

						}

					}

				}

			}

			// If the requested tab exists, localize the accordion script to open this tab.
			// For this to work, the variable "active" must be set to the position of the
			// tab starting with zero for "Core".
			if ( isset( $_REQUEST['open-tab'] ) && array_key_exists( $_REQUEST['open-tab'], $this->accordion_tabs ) )
				wp_localize_script( 'mycred-accordion', 'myCRED', array( 'active' => $this->accordion_tabs[ $_REQUEST['open-tab'] ] ) );

			wp_localize_script(
				'mycred-type-management',
				'myCREDmanage',
				array(
					'ajaxurl'       	 => admin_url( 'admin-ajax.php' ),
					'token'         	 => wp_create_nonce( 'mycred-management-actions' ),
					'cache'         	 => wp_create_nonce( 'mycred-clear-cache' ),
					'working'       	 => esc_attr__( 'Processing...', 'mycred' ),
					'confirm_log'        => esc_attr__( 'Warning! All entries in your log will be permanently removed! This can not be undone!', 'mycred' ),
					'confirm_clean' 	 => esc_attr__( 'All log entries belonging to deleted users will be permanently deleted! This can not be undone!', 'mycred' ),
					'confirm_reset' 	 => esc_attr__( 'Warning! All user balances will be set to zero! This can not be undone!', 'mycred' ),
					'imagelabel'   		 => esc_js( sprintf( '%s {{image}}', __( 'Level', 'mycred' ) ) ),
					'setImage'     		 => esc_js( __( 'Set Image', 'mycred' ) ),
					'set_featured_image' => __( 'Set Default Point Type image', 'mycred' ),
					'changeImage'        => esc_js( __( 'Change Image', 'mycred' ) ),
					'uploadtitle'  		 => esc_js( esc_attr__( 'Point Type Image', 'mycred' ) ),
					'uploadbutton' 		 => esc_js( esc_attr__( 'Use as Image', 'mycred' ) ),
					'done'          	 => esc_attr__( 'Done!', 'mycred' ),
					'export_close'  	 => esc_attr__( 'Close', 'mycred' ),
					'export_title' 		 => $mycred->template_tags_general( esc_attr__( 'Export %singular% Balances', 'mycred' ) ),
					'decimals'      	 => esc_attr__( 'In order to adjust the number of decimal places you want to use we must update your log. It is highly recommended that you backup your current log before continuing!', 'mycred' ),
					'fieldName'		 	 => $this->field_name()
					)
			);
			wp_enqueue_script( 'mycred-type-management' );

			wp_enqueue_style( 'mycred-admin' );
			wp_enqueue_style( 'mycred-bootstrap-grid' );
			wp_enqueue_style( 'mycred-forms' );

			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_script( 'wp-color-picker' );

		}

		/**
		 * Adjust Decimal Places Settings
		 * @since 1.6
		 * @version 1.0.2
		 */
		public function adjust_decimal_places() {

			// Main point type = allow db adjustment
			if ( $this->is_main_type ) {

?>
<div><input type="number" min="0" max="20" id="mycred-adjust-decimal-places" class="form-control" value="<?php echo esc_attr( $this->core->format['decimals'] ); ?>" data-org="<?php echo $this->core->format['decimals']; ?>" size="8" /> <input type="button" style="display:none;" id="mycred-update-log-decimals" class="button button-primary button-large" value="<?php _e( 'Update Database', 'mycred' ); ?>" /></div>
<?php

			}
			// Other point type.
			else {

				$default = mycred();
				if ( $default->format['decimals'] == 0 ) {

?>
<div><?php _e( 'No decimals', 'mycred' ); ?></div>
<?php

				}
				else {

?>
<select name="<?php echo $this->field_name( array( 'format' => 'decimals' ) ); ?>" id="<?php echo $this->field_id( array( 'format' => 'decimals' ) ); ?>" class="form-control">
<?php

					echo '<option value="0"';
					if ( $this->core->format['decimals'] == 0 ) echo ' selected="selected"';
					echo '>' . __( 'No decimals', 'mycred' ) . '</option>';

					for ( $i = 1 ; $i <= $default->format['decimals'] ; $i ++ ) {
						echo '<option value="' . $i . '"';
						if ( $this->core->format['decimals'] == $i ) echo ' selected="selected"';
						echo '>' . $i . ' - 0.' . str_pad( '0', $i, '0' ) . '</option>';
					}

					$url = add_query_arg( array( 'page' => MYCRED_SLUG . '-settings', 'open-tab' => 0 ), admin_url( 'admin.php' ) );

?>
</select>
<p><span class="description"><?php printf( __( '<a href="%s">Click here</a> to change your default point types setup.', 'mycred' ), esc_url( $url ) ); ?></span></p>
<?php

				}

			}

		}

		/**
		 * Admin Page
		 * @since 0.1
		 * @since 2.3 Added select2, Exclude User by ID and Role 
		 * @version 1.6
		 */
		public function admin_page() {

			// Security
			if ( ! $this->core->user_is_point_admin() ) wp_die( 'Access Denied' );

			// General Settings
			$general     = $this->general;
			$action_hook = ( ! $this->is_main_type ) ? $this->mycred_type : '';
			$delete_user = ( isset( $this->core->delete_user ) ) ? $this->core->delete_user : 0;
			$main_screen = ( get_current_screen()->base == 'toplevel_page_mycred-main' );

			// Social Media Links
			$social      = array();
			$social[]    = '<a href="https://www.facebook.com/myCRED" class="facebook" target="_blank">Facebook</a>';
			$social[]    = '<a href="https://plus.google.com/+MycredMe/posts" class="googleplus" target="_blank">Google+</a>';
			$social[]    = '<a href="https://twitter.com/my_cred" class="twitter" target="_blank">Twitter</a>';
			
			// Exclude Users by ID
			$excluded_ids = explode( ',', esc_attr( $this->core->exclude['list'] ) );
			$all_users = $this->get_all_users();
			$excluded_ids_args = array(
				'name'		=>	$this->field_name( array( 'exclude' => 'list' ) ) . '[]',
				'id'		=>	$this->field_id( array( 'exclude' => 'list' ) ),
				'class'		=>	'form-control',
				'multiple'	=>	'multiple'
			);

			//Exclude Users by Role
			$excluded_roles = explode( ',', esc_attr( $this->core->exclude['by_roles'] ) );
			$wp_roles = wp_roles();
			$roles = array();
			foreach( $wp_roles->roles as $role => $name )
				$roles[$role] = $name['name'];
			$roles_args = array(
				'name'		=>	$this->field_name( array( 'exclude' => 'by_roles' ) ) . '[]',
				'id'		=>	$this->field_id( array( 'exclude' => 'by_roles' ) ),
				'class'		=>	'form-control',
				'multiple'	=>	'multiple'
			);

?>
<div class="wrap mycred-metabox" id="myCRED-wrap">
	<h1><?php _e( 'Settings', 'mycred' ); if ( MYCRED_DEFAULT_LABEL === 'myCRED' ) : ?> <a href="http://codex.mycred.me/" target="_blank" class="page-title-action"><?php _e( 'Documentation', 'mycred' ); ?></a><?php endif; ?></h1>

	<?php $this->update_notice(); ?>

	<?php if ( MYCRED_DEFAULT_LABEL === 'myCRED' ) : ?>
	<p id="mycred-thank-you-text"><?php printf( __( 'Thank you for using %s. If you have a moment, please leave a %s.', 'mycred' ), mycred_label(), sprintf( '<a href="https://wordpress.org/support/plugin/mycred/reviews/?rate=5#new-post" target="_blank">%s</a>', __( 'review', 'mycred' ) ) ); ?><span id="mycred-social-media"><?php echo implode( ' ', $social ); ?></span></p>
	<?php endif; ?>

	<form method="post" action="options.php" class="form" name="mycred-core-settings-form" novalidate>

		<?php settings_fields( $this->settings_name ); ?>

		<div class="list-items expandable-li" id="accordion">
			<h4 <?php echo !$main_screen ? '' : 'style="display:none"';?>><span class="dashicons dashicons-admin-settings static"></span><label><?php _e( 'Core Settings', 'mycred' ); ?></label></h4>
			<div class="body" style="display:none;">

				<div class="row">
					<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
						<h3><?php _e( 'Labels', 'mycred' ); ?></h3>
						<div class="row">
							<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
								<div class="form-group">
									<label for="<?php echo $this->field_id( array( 'name' => 'singular' ) ); ?>"><?php _e( 'Singular', 'mycred' ); ?></label>
									<input type="text" name="<?php echo $this->field_name( array( 'name' => 'singular' ) ); ?>" id="<?php echo $this->field_id( array( 'name' => 'singular' ) ); ?>" class="form-control" placeholder="<?php _e( 'Required', 'mycred' ); ?>" value="<?php echo esc_attr( $this->core->name['singular'] ); ?>" />
								</div>
							</div>
							<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
								<div class="form-group">
									<label for="<?php echo $this->field_id( array( 'name' => 'plural' ) ); ?>"><?php _e( 'Plural', 'mycred' ); ?></label>
									<input type="text" name="<?php echo $this->field_name( array( 'name' => 'plural' ) ); ?>" id="<?php echo $this->field_id( array( 'name' => 'plural' ) ); ?>" class="form-control" placeholder="<?php _e( 'Required', 'mycred' ); ?>" value="<?php echo esc_attr( $this->core->name['plural'] ); ?>" />
								</div>
							</div>
						</div>
						<p><span class="description"><?php _e( 'These labels are used throughout the admin area and when presenting points to your users.', 'mycred' ); ?></span></p>
					</div>
					<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
						<h3><?php _e( 'Format', 'mycred' ); ?></h3>
						<div class="row">
							<div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
								<div class="form-group">
									<label for="<?php echo $this->field_id( 'before' ); ?>"><?php _e( 'Prefix', 'mycred' ); ?></label>
									<input type="text" name="<?php echo $this->field_name( 'before' ); ?>" id="<?php echo $this->field_id( 'before' ); ?>" class="form-control" value="<?php echo esc_attr( $this->core->before ); ?>" />
								</div>
							</div>
							<div class="col-lg-5 col-md-5 col-sm-12 col-xs-12">
								<div class="form-group">
									<label for="<?php echo $this->field_id( array( 'format' => 'separators' ) ); ?>-thousand"><?php _e( 'Separators', 'mycred' ); ?></label>
									<div class="form-inline">
										<label>1</label> <input type="text" name="<?php echo $this->field_name( array( 'format' => 'separators' ) ); ?>[thousand]" id="<?php echo $this->field_id( array( 'format' => 'separators' ) ); ?>-thousand" placeholder="," class="form-control" size="2" value="<?php echo esc_attr( $this->core->format['separators']['thousand'] ); ?>" /> <label>000</label> <input type="text" name="<?php echo $this->field_name( array( 'format' => 'separators' ) ); ?>[decimal]" id="<?php echo $this->field_id( array( 'format' => 'separators' ) ); ?>-decimal" placeholder="." class="form-control" size="2" value="<?php echo esc_attr( $this->core->format['separators']['decimal'] ); ?>" /> <label>00</label>
									</div>
								</div>
							</div>
							<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
								<div class="form-group">
									<label for=""><?php _e( 'Decimals', 'mycred' ); ?></label>
									<?php $this->adjust_decimal_places(); ?>
								</div>
							</div>
							<div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
								<div class="form-group">
									<label for="<?php echo $this->field_id( 'after' ); ?>"><?php _e( 'Suffix', 'mycred' ); ?></label>
									<input type="text" name="<?php echo $this->field_name( 'after' ); ?>" id="<?php echo $this->field_id( 'after' ); ?>" class="form-control" value="<?php echo esc_attr( $this->core->after ); ?>" />
								</div>
							</div>
						</div>
						<p><span class="description"><?php _e( 'Set decimals to zero if you prefer to use whole numbers.', 'mycred' ); ?></span></p>
						<?php if ( $this->is_main_type ) : ?>
						<p><strong><?php _e( 'Tip', 'mycred' ); ?>:</strong> <?php _e( 'As this is your main point type, the value you select here will be the largest number of decimals your installation will support.', 'mycred' ); ?></span></p>
						<?php endif; ?>
					</div>
				</div>
				
				<div class="row">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
						<h3><?php _e( 'Security', 'mycred' ); ?></h3>
						<div class="row">
							<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
								<div class="form-group">
									<label for="<?php echo $this->field_id( array( 'caps' => 'creds' ) ); ?>"><?php _e( 'Point Editors', 'mycred' ); ?></label>
									<input type="text" name="<?php echo $this->field_name( array( 'caps' => 'creds' ) ); ?>" id="<?php echo $this->field_id( array( 'caps' => 'creds' ) ); ?>" class="form-control" placeholder="<?php _e( 'Required', 'mycred' ); ?>" value="<?php echo esc_attr( $this->core->caps['creds'] ); ?>" />
									<p><span class="description"><?php _e( 'The capability of users who can edit balances.', 'mycred' ); ?></span></p>
								</div>
							</div>
							<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
								<div class="form-group">
									<label for="<?php echo $this->field_id( array( 'caps' => 'plugin' ) ); ?>"><?php _e( 'Point Administrators', 'mycred' ); ?></label>
									<input type="text" name="<?php echo $this->field_name( array( 'caps' => 'plugin' ) ); ?>" id="<?php echo $this->field_id( array( 'caps' => 'plugin' ) ); ?>" class="form-control" placeholder="<?php _e( 'Required', 'mycred' ); ?>" value="<?php echo esc_attr( $this->core->caps['plugin'] ); ?>" />
									<p><span class="description"><?php _e( 'The capability of users who can edit settings.', 'mycred' ); ?></span></p>
								</div>
							</div>
							<div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
								<div class="form-group">
									<?php if ( ! isset( $this->core->max ) ) $this->core->max(); ?>
									<label for="<?php echo $this->field_id( 'max' ); ?>"><?php _e( 'Max. Amount', 'mycred' ); ?></label>
									<input type="text" name="<?php echo $this->field_name( 'max' ); ?>" id="<?php echo $this->field_id( 'max' ); ?>" class="form-control" value="<?php echo esc_attr( $this->core->max ); ?>" />
									<p><span class="description"><?php _e( 'The maximum amount allowed to be paid out in a single instance.', 'mycred' ); ?></span></p>
								</div>
							</div>
							<div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
								<div class="form-group">
									<label for="<?php echo $excluded_ids_args['id']; ?>"><?php _e( 'Exclude by User ID', 'mycred' ); ?></label>
									<?php echo mycred_create_select2( $all_users, $excluded_ids_args, $excluded_ids ); ?>
								</div>
								<div class="form-group">
									<div class="checkbox">
										<label for="<?php echo $this->field_id( array( 'exclude' => 'cred_editors' ) ); ?>"><input type="checkbox" name="<?php echo $this->field_name( array( 'exclude' => 'cred_editors' ) ); ?>" id="<?php echo $this->field_id( array( 'exclude' => 'cred_editors' ) ); ?>"<?php checked( $this->core->exclude['cred_editors'], 1 ); ?> value="1" /> <?php _e( 'Exclude point editors', 'mycred' ); ?></label>
									</div>
									<div class="checkbox">
										<label for="<?php echo $this->field_id( array( 'exclude' => 'plugin_editors' ) ); ?>"><input type="checkbox" name="<?php echo $this->field_name( array( 'exclude' => 'plugin_editors' ) ); ?>" id="<?php echo $this->field_id( array( 'exclude' => 'plugin_editors' ) ); ?>"<?php checked( $this->core->exclude['plugin_editors'], 1 ); ?> value="1" /> <?php _e( 'Exclude point administrators', 'mycred' ); ?></label>
									</div>
								</div>
							</div>
							<div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
								<div class="form-group">
									<label for="<?php echo $roles_args['id']; ?>"><?php _e( 'Exclude by User Role', 'mycred' ); ?></label>
									<?php echo mycred_create_select2( $roles, $roles_args, $excluded_roles ); ?>
								</div>
							</div>
						</div>
					</div>
				</div>
				

				<div class="row mycred-image-level">
							
					<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
						<div id="mycred-image-setup" class="default-image-wrapper">

								<h3><?php _e( 'Point Type Image', 'mycred' ); ?></h3>

							
								<div class="point-type-image">
									<div class="point-type-image-wrapper image-wrapper">
										<?php

										$attachment_id = mycred_get_default_point_image_id();

										$image_url = wp_get_attachment_url( $attachment_id );

										if( property_exists( $this->core, 'attachment_id' ) && $this->get_point_image( $this->core->attachment_id , $this->field_name( 'attachment_id' )) )
											echo $this->get_point_image( $this->core->attachment_id , $this->field_name( 'attachment_id' ));
										elseif( !$attachment_id )
										{
											?>
											<div class="default-image-wrapper image-wrapper empty dashicons">
											</div>
											<?php
										}
										else
										{
											echo "<img src='{$image_url}' />";
											echo "<input type='hidden' value='{$attachment_id}' name='".$this->field_name( 'attachment_id' )."' />";
										}
										?>
									</div>
									<div class="point-image-buttons">
										<button type="button" class="button button-secondary" id="point-type-change-default-image"><?php _e( 'Change Image', 'mycred' ) ?></button>
									</div>
								</div>
						</div>	
					</div>
				
					<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
						<h3><?php _e( 'Other Settings', 'mycred' ); ?></h3>
						<div class="form-group">
							<label for="<?php echo $this->field_id( 'delete_user' ); ?>"><input type="checkbox" name="<?php echo $this->field_name( 'delete_user' ); ?>" id="<?php echo $this->field_id( 'delete_user' ); ?>" <?php checked( $delete_user, 1 ); ?> value="1" /> <?php _e( 'Delete log entries when user is deleted.', 'mycred' ); ?></label>
						</div>
					</div>
					<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
						<?php do_action( 'mycred_core_prefs' . $action_hook, $this ); ?>
					</div>
				</div>
			</div>
<?php

			global $wpdb, $mycred_log_table;

			$total_rows  = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$mycred_log_table} WHERE ctype = %s;", $this->mycred_type ) );
			$reset_block = false;

			if ( get_transient( 'mycred-accounts-reset' ) !== false )
				$reset_block = true;

?>
			<h4 <?php echo !$main_screen ? '' : 'style="display:none"';?>><span class="dashicons dashicons-dashboard static"></span><label><?php _e( 'Management', 'mycred' ); ?></label></h4>
			<div class="body" style="display:none;">

				<div class="row">
					<div class="col-lg-5 col-md-5 col-sm-12 col-xs-12">
						<div class="form-group">
							<label>Log Table</label>
							<h1><?php echo esc_attr( $mycred_log_table ); ?></h1>
						</div>
					</div>
					<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
						<div class="form-group">
							<label><?php _e( 'Entries', 'mycred' ); ?></label>
							<h1><?php echo $total_rows; ?></h1>
						</div>
					</div>
					<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
						<div class="form-group">
							<label><?php _e( 'Actions', 'mycred' ); ?></label>
							<div>
								<?php if ( ( ! mycred_centralize_log() ) || ( mycred_centralize_log() && $GLOBALS['blog_id'] == 1 ) ) : ?>
								<button type="button" id="mycred-manage-action-empty-log" data-type="<?php echo $this->mycred_type; ?>" class="button button-large large <?php if ( $total_rows == 0 ) echo '"disabled="disabled'; else echo 'button-primary'; ?>"><?php _e( 'Empty Log', 'mycred' ); ?></button>
								<?php endif; ?>
							</div>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-lg-5 col-md-5 col-sm-12 col-xs-12">
						<div class="form-group">
							<label><?php _e( 'Balance Meta Key', 'mycred' ); ?></label>
							<h1><?php echo $this->core->cred_id; ?></h1>
						</div>
					</div>
					<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
						<div class="form-group">
							<label><?php _e( 'Users', 'mycred' ); ?></label>
							<h1><?php echo $this->core->count_members(); ?></h1>
						</div>
					</div>
					<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
						<div class="form-group">
							<label><?php _e( 'Actions', 'mycred' ); ?></label>
							<div>
								<button type="button" id="mycred-manage-action-reset-accounts" data-type="<?php echo $this->mycred_type; ?>" class="button button-large large <?php if ( $reset_block ) echo '" disabled="disabled'; else echo 'button-primary'; ?>"><?php _e( 'Set all to zero', 'mycred' ); ?></button> 
								<button type="button" id="mycred-export-users-points" data-type="<?php echo $this->mycred_type; ?>" class="button button-large large"><?php _e( 'Export Balances', 'mycred' ); ?></button>
							</div>
						</div>
					</div>
				</div>

				<?php do_action( 'mycred_management_prefs' . $action_hook, $this ); ?>

			</div>

			<?php do_action( 'mycred_after_management_prefs' . $action_hook, $this ); ?>

<?php

			if ( $main_screen ) :

?>
			<h4><span class="dashicons dashicons-star-filled static"></span><label><?php _e( 'Point Types', 'mycred' ); ?></label></h4>
			<div class="body" style="display:none;">
<?php

				if ( ! empty( $this->point_types ) ) {

					foreach ( $this->point_types as $type => $label ) {

						if ( $type == MYCRED_DEFAULT_TYPE_KEY ) {

?>
				<div class="row">
					<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
						<div class="form-group">
							<label><?php _e( 'Meta Key', 'mycred' ); ?></label>
							<input type="text" disabled="disabled" class="form-control" value="<?php echo esc_attr( $type ); ?>" class="readonly" />
						</div>
					</div>
					<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
						<div class="form-group">
							<label><?php _e( 'Label', 'mycred' ); ?></label>
							<input type="text" disabled="disabled" class="form-control" value="<?php echo strip_tags( $label ); ?>" class="readonly" />
						</div>
					</div>
					<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
						<div class="form-group">
							<label>&nbsp;</label>
							<label><input type="checkbox" disabled="disabled" class="disabled" value="<?php echo esc_attr( $type ); ?>" /> <?php _e( 'Delete', 'mycred' ); ?></label>
						</div>
					</div>
				</div>
<?php

						}
						else {

?>
				<div class="row">
					<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
						<div class="form-group">
							<label><?php _e( 'Meta Key', 'mycred' ); ?></label>
							<input type="text" name="mycred_pref_core[types][<?php echo esc_attr( $type ); ?>][key]" value="<?php echo esc_attr( $type ); ?>" class="form-control" />
						</div>
					</div>
					<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
						<div class="form-group">
							<label><?php _e( 'Label', 'mycred' ); ?></label>
							<input type="text" name="mycred_pref_core[types][<?php echo esc_attr( $type ); ?>][label]" value="<?php echo strip_tags( $label ); ?>" class="form-control" />
						</div>
					</div>
					<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
						<div class="form-group">
							<label>&nbsp;</label>
							<label for="mycred-point-type-<?php echo esc_attr( $type ); ?>"><input type="checkbox" name="mycred_pref_core[delete_types][]" id="mycred-point-type-<?php echo esc_attr( $type ); ?>" value="<?php echo esc_attr( $type ); ?>" /> <?php _e( 'Delete', 'mycred' ); ?></label>
						</div>
					</div>
				</div>
<?php

						}

					}

				}

?>
				<h3><?php _e( 'Add New Type', 'mycred' ); ?></h3>
				<div class="row">
					<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
						<div class="form-group">
							<label for="mycred-new-ctype-key-value"><?php _e( 'Meta Key', 'mycred' ); ?></label>
							<input type="text" id="mycred-new-ctype-key-value" name="mycred_pref_core[types][new][key]" placeholder="<?php _e( 'Required', 'mycred' ); ?>" value="" class="form-control" />
						</div>
					</div>
					<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
						<div class="form-group">
							<label for="mycred-new-ctype-key-label"><?php _e( 'Singular', 'mycred' ); ?></label>
							<input type="text" id="mycred-new-ctype-key-singular" name="mycred_pref_core[types][new][singular]" placeholder="<?php _e( 'Required', 'mycred' ); ?>" value="" class="form-control" />
						</div>
					</div>
					<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
						<div class="form-group">
							<label for="mycred-new-ctype-key-label"><?php _e( 'Plural', 'mycred' ); ?></label>
							<input type="text" id="mycred-new-ctype-key-label" name="mycred_pref_core[types][new][label]" placeholder="<?php _e( 'Required', 'mycred' ); ?>" value="" class="form-control" />
						</div>
					</div>
				</div>
				<p id="mycred-ctype-warning">
					<strong><?php _e( 'Note This meta key must be in lowercase and only contain letters or underscore. All other characters will be deleted! make sure to add some unique prefix to this meta key to avoid any conflicts in database.', 'mycred' ); ?> <a href="https://codex.mycred.me/chapter-i/points/"><?php _e( 'Read More', 'mycred' )?></a></strong>
				</p>
			</div>
<?php

			endif;

?>

			<div class="mycred-after-core-prefs" <?php echo $main_screen ? '' : 'style="display:none"';?> >
				<?php do_action( 'mycred_after_core_prefs' . $action_hook, $this ); ?>
			</div>

			<div class="mycred-type-prefs" <?php echo !$main_screen ? '' : 'style="display:none"';?>>
				<?php do_action( 'mycred_type_prefs' . $action_hook, $this ); ?>
			</div>

		</div>

		<?php submit_button( __( 'Update Settings', 'mycred' ), 'primary large', 'submit' ); ?>

	</form>

	<?php do_action( 'mycred_bottom_settings_page' . $action_hook, $this ); ?>

	<div id="export-points" style="display:none;">
		<div class="mycred-container">

			<div class="form mycred-metabox">
				<div class="row">
					<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
						<div class="form-group">
							<label><?php _e( 'Identify users by', 'mycred' ); ?></label>
							<select id="mycred-export-identify-by" class="form-control">
<?php

			// Identify users by...
			$identify = apply_filters( 'mycred_export_by', array(
				'ID'    => __( 'User ID', 'mycred' ),
				'email' => __( 'User Email', 'mycred' ),
				'login' => __( 'User Login', 'mycred' )
			) );

			foreach ( $identify as $id => $label )
				echo '<option value="' . $id . '">' . $label . '</option>';

?>
							</select>
							<span class="description"><?php _e( 'Use ID if you intend to use this export as a backup of your current site while Email is recommended if you want to export to a different site.', 'mycred' ); ?></span>
						</div>
					</div>
					<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
						<div class="form-group">
							<label><?php _e( 'Import Log Entry', 'mycred' ); ?></label>
							<input type="text" id="mycred-export-log-template" value="" class="regular-text form-control" />
							<span class="description"><?php echo sprintf( __( 'Optional log entry to use if you intend to import this file in a different %s installation.', 'mycred' ), mycred_label() ); ?></span>
						</div>
					</div>
				</div>	

				<div class="row last">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-right">
						<input type="button" id="mycred-run-exporter" value="<?php _e( 'Export', 'mycred' ); ?>" data-type="<?php echo $this->mycred_type; ?>" class="button button-large button-primary" />
					</div>
				</div>
			</div>

		</div>
	</div>

</div>
<?php

		}

		/**
		 * Maybe Whitespace
		 * Since we want to allow a single whitespace in the string and sanitize_text_field() removes this whitespace
		 * this little method will make sure that whitespace is still there and that we still can sanitize the field.
		 * @since 0.1
		 * @version 1.0
		 */
		public function maybe_whitespace( $string ) {

			if ( strlen( $string ) > 1 )
				return '';

			return $string;

		}

		/**
		 * Sanititze Settings
		 * @filter 'mycred_save_core_prefs'
		 * @since 0.1
		 * @since 2.3 Added `by_role` Exclude user by role
		 * @version 1.5.2
		 */
		public function sanitize_settings( $post ) {

			$new_data = array();

			if ( $this->mycred_type == MYCRED_DEFAULT_TYPE_KEY ) {
				if ( isset( $post['types'] ) ) {

					$types = array( MYCRED_DEFAULT_TYPE_KEY => mycred_label() );
					foreach ( $post['types'] as $item => $data ) {

						// Make sure it is not marked as deleted
						if ( isset( $post['delete_types'] ) && in_array( $item, $post['delete_types'] ) ) {

							do_action( 'mycred_delete_point_type', $data['key'] );
							do_action( 'mycred_delete_point_type_' . $data['key'] );
							continue;

						}

						// Skip if empty
						if ( empty( $data['key'] ) || empty( $data['label'] ) ) continue;

						// Add if not in array already
						if ( ! array_key_exists( $data['key'], $types ) ) {

							$key           = str_replace( array( ' ', '-' ), '_', $data['key'] );
							$key           = sanitize_key( $key );

							$types[ $key ] = sanitize_text_field( $data['label'] );

							$type_settings = mycred_get_option( 'mycred_pref_core_' . $key );

							if ( $key !== MYCRED_DEFAULT_TYPE_KEY && empty( $type_settings  ) ) {

								if ( empty( $data['singular'] ) )
									$data['singular'] = $types[ $key ];

								$mycred = mycred();
								$new_type_defaults = $mycred->defaults();
								$new_type_defaults['cred_id'] = $key;
								$new_type_defaults['name']['singular'] = sanitize_text_field( $data['singular'] );
								$new_type_defaults['name']['plural']   = $types[ $key ];
							
								mycred_update_option( 'mycred_pref_core_' . $key , $new_type_defaults );

								mycred_upload_default_point_image();
							}

						}

					}

					mycred_update_option( 'mycred_types', $types );
					unset( $post['types'] );

					if ( isset( $post['delete_types'] ) )
						unset( $post['delete_types'] );

				}

				$new_data['format'] = $this->core->core['format'];

				if ( isset( $post['format']['type'] ) && $post['format']['type'] != '' )
					$new_data['format']['type'] = absint( $post['format']['type'] );

				if ( isset( $post['format']['decimals'] ) )
					$new_data['format']['decimals'] = absint( $post['format']['decimals'] );

			}
			else {

				$main_settings      = mycred_get_option( 'mycred_pref_core' );
				$new_data['format'] = $main_settings['format'];

				if ( isset( $post['format']['decimals'] ) ) {

					$new_decimals = absint( $post['format']['decimals'] );
					if ( $new_decimals <= $main_settings['format']['decimals'] )
						$new_data['format']['decimals'] = $new_decimals;

				}

			}

			// Format
			$new_data['cred_id'] = $this->mycred_type;

			$new_data['format']['separators']['decimal']  = $this->maybe_whitespace( $post['format']['separators']['decimal'] );
			$new_data['format']['separators']['thousand'] = $this->maybe_whitespace( $post['format']['separators']['thousand'] );

			// Name
			$new_data['name']    = array(
				'singular'          => sanitize_text_field( $post['name']['singular'] ),
				'plural'            => sanitize_text_field( $post['name']['plural'] )
			);

			// Look
			$new_data['before']  = sanitize_text_field( $post['before'] );
			$new_data['after']   = sanitize_text_field( $post['after'] );

			// Capabilities
			$new_data['caps']    = array(
				'plugin'            => sanitize_text_field( $post['caps']['plugin'] ),
				'creds'             => sanitize_text_field( $post['caps']['creds'] )
			);

			// Max
			$new_data['max']     = $this->core->number( $post['max'] );

			// Make sure multisites uses capabilities that exists
			if ( in_array( $new_data['caps']['plugin'], array( 'create_users', 'delete_themes', 'edit_plugins', 'edit_themes', 'edit_users' ) ) && is_multisite() )
				$new_data['caps']['plugin'] = 'edit_theme_options';

			//Exclude Users by roles and ID
			$sanitized_exclude_ids = !empty( $post['exclude']['list'] ) ? sanitize_text_field( implode( ',', $post['exclude']['list'] ) ) : '';
			$sanitized_exclude_roles = !empty( $post['exclude']['by_roles'] ) ? sanitize_text_field( implode( ',', $post['exclude']['by_roles'] ) ) : '';

			// Excludes
			$new_data['exclude'] = array(
				'plugin_editors'    =>	( isset( $post['exclude']['plugin_editors'] ) ) ? $post['exclude']['plugin_editors'] : 0,
				'cred_editors'      =>	( isset( $post['exclude']['cred_editors'] ) ) ? $post['exclude']['cred_editors'] : 0,
				'list'              =>	$sanitized_exclude_ids,
				'by_roles'			=>	$sanitized_exclude_roles
			);

			// Remove Exclude users balances
			if ( $new_data['exclude']['list'] != '' || $new_data['exclude']['by_roles'] != '' ) {

				$excluded_ids = wp_parse_id_list( $new_data['exclude']['list'] );

				//Exclude by User Role
				$excluded_roles = $post['exclude']['by_roles'];

				if( !empty( $excluded_roles ) )
				{
					$users_by_role = $this->get_users_by_role( $excluded_roles );
					$excluded_ids = array_merge( $excluded_ids, $users_by_role );
					$excluded_ids = array_unique( $excluded_ids );
				}

	
				if ( ! empty( $excluded_ids ) ) {
					foreach ( $excluded_ids as $user_id ) {

						$user_id = absint( $user_id );
						if ( $user_id == 0 ) continue;

						mycred_delete_user_meta( $user_id, $this->mycred_type );
						mycred_delete_user_meta( $user_id, $this->mycred_type, '_total' );

					}
				}

			}
			
			
			// User deletions
			$new_data['delete_user'] = ( isset( $post['delete_user'] ) ) ? $post['delete_user'] : 0;

			//Point type image
			$new_data['attachment_id'] = isset( $post['attachment_id'] ) ? $post['attachment_id'] : 0;

			$action_hook             = '';
			if ( ! $this->is_main_type )
				$action_hook = $this->mycred_type;

			return apply_filters( 'mycred_save_core_prefs' . $action_hook, $new_data, $post, $this );

		}

		/**
		 * @since 2.3
		 * @version 1.0
		 */
		public function get_all_users()
		{
			$users = array();
	
			$wp_users = get_users();
	
			foreach( $wp_users as $user )
				$users[$user->ID] = $user->display_name;
	
			return $users;
		} 

		/**
		 * @since 2.3
		 * @version 1.0
		 */
		public function get_users_by_role( $roles )
		{
			$user_ids = array();

			foreach( $roles as $role )
			{
				$args = array(
					'role'	=>	$role
				);

				$user_query = new WP_User_Query( $args );

				if ( ! empty( $user_query->get_results() ) ) 
				{
					foreach ( $user_query->get_results() as $user ) 
						$user_ids[] = $user->ID;
				}
			}

			return $user_ids;
		}

	}
endif;

