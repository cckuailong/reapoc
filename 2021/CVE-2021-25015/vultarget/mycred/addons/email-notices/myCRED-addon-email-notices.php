<?php
/**
 * Addon: Email Notices
 * Addon URI: http://codex.mycred.me/chapter-iii/email-notice/
 * Version: 1.4
 */
if ( ! defined( 'myCRED_VERSION' ) ) exit;

// Define constants
define( 'myCRED_EMAIL',         __FILE__ );
define( 'myCRED_EMAIL_DIR',     myCRED_ADDONS_DIR . 'email-notices/' );
define( 'myCRED_EMAIL_VERSION', '1.4' );

// Coupon Key
if ( ! defined( 'MYCRED_EMAIL_KEY' ) )
	define( 'MYCRED_EMAIL_KEY', 'mycred_email_notice' );

// Includes
require_once myCRED_EMAIL_DIR . 'includes/mycred-email-functions.php';
require_once myCRED_EMAIL_DIR . 'includes/mycred-email-object.php';
require_once myCRED_EMAIL_DIR . 'includes/mycred-email-shortcodes.php';

/**
 * myCRED_Email_Notice_Module class
 * @since 1.1
 * @version 2.0
 */
if ( ! class_exists( 'myCRED_Email_Notice_Module' ) ) :
	class myCRED_Email_Notice_Module extends myCRED_Module {

		/**
		 * Construct
		 */
		function __construct() {

			parent::__construct( 'myCRED_Email_Notice_Module', array(
				'module_name' => 'emailnotices',
				'defaults'    => mycred_get_addon_defaults( 'emailnotices' ),
				'register'    => false,
				'add_to_core' => true,
				'menu_pos'    => 90
			) );

		}

		/**
		 * Hook into Init
		 * @since 1.1
		 * @version 1.3
		 */
		public function module_init() {

			$this->register_email_notices();
			$this->setup_cron_jobs();

			add_action( 'mycred_set_current_account',    array( $this, 'populate_current_account' ) );
			add_action( 'mycred_get_account',            array( $this, 'populate_account' ) );

			add_filter( 'mycred_add_finished',           array( $this, 'email_check' ), 80, 3 );

			add_action( 'mycred_badge_level_reached',    array( $this, 'badge_check' ), 10, 3 );
			add_action( 'mycred_user_got_promoted',      array( $this, 'rank_promotion' ), 10, 4 );
			add_action( 'mycred_user_got_demoted',       array( $this, 'rank_demotion' ), 10, 4 );		
            
            add_action( 'mycred_send_email_notices',     'mycred_email_notice_cron_job' );

			add_shortcode( MYCRED_SLUG . '_email_subscriptions', 'mycred_render_email_subscriptions' );

			add_action( 'mycred_admin_enqueue',          array( $this, 'enqueue_scripts' ), $this->menu_pos );
			add_action( 'mycred_add_menu',               array( $this, 'add_to_menu' ), $this->menu_pos );
			
            add_action( 'mycred_after_payment_request',  array( $this, 'after_payment_request' ), 10, 2);	

		}

		/**
		 * Hook into Admin Init
		 * @since 1.1
		 * @version 1.1
		 */
		public function module_admin_init() {

			add_filter( 'post_updated_messages', array( $this, 'post_updated_messages' ) );

			add_filter( 'parent_file',           array( $this, 'parent_file' ) );
			add_filter( 'submenu_file',          array( $this, 'subparent_file' ), 10, 2 );

			add_action( 'admin_head',            array( $this, 'admin_header' ) );

			add_filter( 'enter_title_here',      array( $this, 'enter_title_here' ) );
			add_filter( 'page_row_actions',      array( $this, 'adjust_row_actions' ), 10, 2 );

			add_filter( 'user_can_richedit',     array( $this, 'disable_richedit' ) );
			add_filter( 'default_content',       array( $this, 'default_content' ) );

			add_filter( 'manage_' . MYCRED_EMAIL_KEY . '_posts_columns',       array( $this, 'adjust_column_headers' ), 50 );
			add_action( 'manage_' . MYCRED_EMAIL_KEY . '_posts_custom_column', array( $this, 'adjust_column_content' ), 10, 2 );
			add_action( 'save_post_' . MYCRED_EMAIL_KEY,                       array( $this, 'save_email_notice' ), 10, 2 );

		}

		/**
		 * Setup Cron Jobs
		 * @since 1.8
		 * @version 1.0
		 */
		public function setup_cron_jobs() {

			// Schedule Cron
			if ( ! isset( $this->emailnotices['send'] ) ) return;

			if ( $this->emailnotices['send'] == 'hourly' && wp_next_scheduled( 'mycred_send_email_notices' ) === false )
				wp_schedule_event( time(), 'hourly', 'mycred_send_email_notices' );

			elseif ( $this->emailnotices['send'] == 'daily' && wp_next_scheduled( 'mycred_send_email_notices' ) === false )
				wp_schedule_event( time(), 'daily', 'mycred_send_email_notices' );

			elseif ( $this->emailnotices['send'] == '' && wp_next_scheduled( 'mycred_send_email_notices' ) !== false )
				wp_clear_scheduled_hook( 'mycred_send_email_notices' );

		}

		/**
		 * Register Email Notice Post Type
		 * @since 1.1
		 * @version 1.1
		 */
		protected function register_email_notices() {

			$labels = array(
				'name'               => __( 'Email Notifications', 'mycred' ),
				'singular_name'      => __( 'Email Notification', 'mycred' ),
				'add_new'            => __( 'Add New', 'mycred' ),
				'add_new_item'       => __( 'Add New', 'mycred' ),
				'edit_item'          => __( 'Edit Email Notification', 'mycred' ),
				'new_item'           => __( 'New Email Notification', 'mycred' ),
				'all_items'          => __( 'Email Notifications', 'mycred' ),
				'view_item'          => '',
				'search_items'       => __( 'Search Email Notifications', 'mycred' ),
				'not_found'          => __( 'No email notifications found', 'mycred' ),
				'not_found_in_trash' => __( 'No email notifications found in Trash', 'mycred' ), 
				'parent_item_colon'  => '',
				'menu_name'          => __( 'Email Notifications', 'mycred' )
			);
			$args = array(
				'labels'               => $labels,
				'supports'             => array( 'title', 'editor' ),
				'hierarchical'         => true,
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

			register_post_type( MYCRED_EMAIL_KEY, apply_filters( 'mycred_register_emailnotices', $args ) );

		}

		/**
		 * Register Scripts & Styles
		 * @since 1.7
		 * @version 1.0
		 */
		public function scripts_and_styles() {

			// Register Email List Styling
			wp_register_style(
				'mycred-email-notices',
				plugins_url( 'assets/css/email-notice.css', myCRED_EMAIL ),
				false,
				myCRED_EMAIL_VERSION . '.1',
				'all'
			);

			// Register Edit Email Notice Styling
			wp_register_style(
				'mycred-email-edit-notice',
				plugins_url( 'assets/css/edit-email-notice.css', myCRED_EMAIL ),
				false,
				myCRED_EMAIL_VERSION . '.1',
				'all'
			);

		}

		/**
		 * Populate Current Account
		 * @since 1.8
		 * @version 1.0
		 */
		public function populate_current_account() {

			global $mycred_current_account;

			if ( isset( $mycred_current_account )
				&& ( $mycred_current_account instanceof myCRED_Account )
				&& ( isset( $mycred_current_account->email_block ) )
			) return;

			$mycred_current_account->email_block = (array) mycred_get_user_meta( $mycred_current_account->user_id, 'mycred_email_unsubscriptions', '', true );

		}

		/**
		 * Populate Account
		 * @since 1.8
		 * @version 1.0
		 */
		public function populate_account() {

			global $mycred_account;

			if ( isset( $mycred_account )
				&& ( $mycred_account instanceof myCRED_Account )
				&& ( isset( $mycred_account->email_block ) )
			) return;

			$mycred_account->email_block = (array) mycred_get_user_meta( $mycred_account->user_id, 'mycred_email_unsubscriptions', '', true );

		}

		/**
		 * Adjust Post Updated Messages
		 * @since 1.1
		 * @version 1.1
		 */
		public function post_updated_messages( $messages ) {

			$messages[ MYCRED_EMAIL_KEY ] = array(
				0  => '',
				1  => __( 'Email Notice Updated.', 'mycred' ),
				2  => __( 'Email Notice Updated.', 'mycred' ),
				3  => __( 'Email Notice Updated.', 'mycred' ),
				4  => __( 'Email Notice Updated.', 'mycred' ),
				5  => false,
				6  => __( 'Email Notice Activated.', 'mycred' ),
				7  => __( 'Email Notice Updated.', 'mycred' ),
				8  => __( 'Email Notice Updated.', 'mycred' ),
				9  => __( 'Email Notice Updated.', 'mycred' ),
				10 => __( 'Email Notice Updated.', 'mycred' )
			);

			return $messages;

		}

		/**
		 * Add Admin Menu Item
		 * @since 1.7
		 * @version 1.1
		 */
		public function add_to_menu() {

			// In case we are using the Master Template feautre on multisites, and this is not the main
			// site in the network, bail.
			if ( mycred_override_settings() && ! mycred_is_main_site() ) return;

			mycred_add_main_submenu(
				__( 'Email Notifications', 'mycred' ),
				__( 'Email Notifications', 'mycred' ),
				$this->core->get_point_editor_capability(),
				'edit.php?post_type=' . MYCRED_EMAIL_KEY
			);

		}

		/**
		 * Parent File
		 * @since 1.7
		 * @version 1.1
		 */
		public function parent_file( $parent = '' ) {

			global $pagenow;

			if ( isset( $_GET['post'] ) && mycred_get_post_type( $_GET['post'] ) == MYCRED_EMAIL_KEY && isset( $_GET['action'] ) && $_GET['action'] == 'edit' )
				return MYCRED_MAIN_SLUG;

			if ( $pagenow == 'post-new.php' && isset( $_GET['post_type'] ) && $_GET['post_type'] == MYCRED_EMAIL_KEY )
				return MYCRED_MAIN_SLUG;

			return $parent;

		}

		/**
		 * Sub Parent File
		 * @since 1.7
		 * @version 1.0
		 */
		public function subparent_file( $subparent = '', $parent = '' ) {

			global $pagenow;

			if ( ( $pagenow == 'edit.php' || $pagenow == 'post-new.php' ) && isset( $_GET['post_type'] ) && $_GET['post_type'] == MYCRED_EMAIL_KEY ) {

				return 'edit.php?post_type=' . MYCRED_EMAIL_KEY;
			
			}

			elseif ( $pagenow == 'post.php' && isset( $_GET['post'] ) && mycred_get_post_type( $_GET['post'] ) == MYCRED_EMAIL_KEY ) {

				return 'edit.php?post_type=' . MYCRED_EMAIL_KEY;

			}

			return $subparent;

		}

		/**
		 * Adjust Enter Title Here
		 * @since 1.1
		 * @version 1.0
		 */
		public function enter_title_here( $title ) {

			global $post_type;

			if ( $post_type == MYCRED_EMAIL_KEY )
				return __( 'Email Subject', 'mycred' );

			return $title;

		}

		/**
		 * Adjust Column Header
		 * @since 1.1
		 * @version 1.1
		 */
		public function adjust_column_headers( $defaults ) {

			$columns       = array();
			$columns['cb'] = $defaults['cb'];

			// Add / Adjust
			$columns['title']                  = __( 'Email Subject', 'mycred' );
			$columns['mycred-email-status']    = __( 'Status', 'mycred' );
			$columns['mycred-email-reference'] = __( 'Setup', 'mycred' );

			if ( count( $this->point_types ) > 1 )
				$columns['mycred-email-ctype'] = __( 'Point Type', 'mycred' );

			// Return
			return $columns;

		}

		/**
		 * Adjust Column Content
		 * @since 1.1
		 * @version 1.1
		 */
		public function adjust_column_content( $column_name, $post_id ) {

			// Get the post
			if ( in_array( $column_name, array( 'mycred-email-status', 'mycred-email-reference', 'mycred-email-ctype' ) ) )
				$email = mycred_get_email_notice( $post_id );

			// Email Status Column
			if ( $column_name == 'mycred-email-status' ) {

				if ( $email->post->post_status != 'publish' && $email->post->post_status != 'future' )
					echo '<p>' . __( 'Not Active', 'mycred' ) . '</p>';

				elseif ( $email->post->post_status == 'future' )
					echo '<p>' . sprintf( '<strong>%s</strong> %s', __( 'Scheduled', 'mycred' ), date( get_option( 'date_format' ) . ' @ ' . get_option( 'time_format' ), strtotime( $email->post->post_date ) ) ) . '</p>';

				else {

					if ( empty( $email->last_run ) )
						echo '<p><strong>' . __( 'Active', 'mycred' ) . '</strong></p>';
					else
						echo '<p>' . sprintf( '<strong>%s</strong> %s', __( 'Active - Last run', 'mycred' ), date( get_option( 'date_format' ) . ' @ ' . get_option( 'time_format' ), $email->last_run ) ) . '</p>';

				}

			}

			// Email Setup Column
			elseif ( $column_name == 'mycred-email-reference' ) {

				$instances  = mycred_get_email_instances();
				$references = mycred_get_all_references();

				$trigger     = $email->get_trigger();
				$description = array();

				if ( $trigger == '' )
					$description[] = sprintf( '<strong>%s:</strong> %s', __( 'Sent when', 'mycred' ), __( 'Not set', 'mycred' ) );

				elseif ( array_key_exists( $trigger, $instances ) )
					$description[] = sprintf( '<strong>%s:</strong> %s', __( 'Sent when', 'mycred' ), $instances[ $trigger ] );

				elseif( array_key_exists( $trigger, $references ) )
					$description[] = sprintf( '<strong>%s:</strong> %s', __( 'Sent when', 'mycred' ), $references[ $trigger ] );

				else
					$description[] = sprintf( '<strong>%s:</strong> %s', __( 'Sent at custom events', 'mycred' ), str_replace( ',', ', ', $trigger ) );

				if ( $email->settings['recipient'] == 'user' )
					$description[] = sprintf( '<strong>%s:</strong> %s', __( 'Recipient', 'mycred' ), __( 'User', 'mycred' ) );

				elseif ( $email->settings['recipient'] == 'admin' )
					$description[] = sprintf( '<strong>%s:</strong> %s', __( 'Recipient', 'mycred' ), __( 'Administrator', 'mycred' ) );

				else
					$description[] = sprintf( '<strong>%s:</strong> %s', __( 'Recipient', 'mycred' ), __( 'Both', 'mycred' ) );

				echo '<p>' . implode( '<br />', $description ) . '</p>';

			}

			// Email Setup Column
			elseif ( $column_name == 'mycred-email-ctype' ) {

				echo '<p>';
				if ( empty( $email->point_types ) )
					_e( 'No point types selected', 'mycred' );

				else {
					$types = array();
					foreach ( $email->point_types as $type_key ) {
						$types[] = $this->point_types[ $type_key ];
					}
					echo implode( ', ', $types );
				}
				echo '</p>';

			}

		}

		/**
		 * Adjust Row Actions
		 * @since 1.1
		 * @version 1.0.1
		 */
		public function adjust_row_actions( $actions, $post ) {

			if ( $post->post_type == MYCRED_EMAIL_KEY ) {
				unset( $actions['inline hide-if-no-js'] );
				unset( $actions['view'] );
			}

			return $actions;

		}

		/**
		 * Add Meta Boxes
		 * @since 1.1
		 * @version 1.1
		 */
		public function add_metaboxes() {

			add_meta_box(
				'mycred-email-setup',
				__( 'Email Trigger', 'mycred' ),
				array( $this, 'metabox_email_setup' ),
				MYCRED_EMAIL_KEY,
				'side',
				'high'
			);

			add_meta_box(
				'mycred-email-tags',
				__( 'Available Template Tags', 'mycred' ),
				array( $this, 'metabox_template_tags' ),
				MYCRED_EMAIL_KEY,
				'normal',
				'core'
			);

			add_meta_box(
				'mycred-email-details',
				__( 'Email Details', 'mycred' ),
				array( $this, 'metabox_email_details' ),
				MYCRED_EMAIL_KEY,
				'normal',
				'high'
			);

		}

		/**
		 * Enqueue Scripts & Styles
		 * @since 1.1
		 * @version 1.1
		 */
		public function enqueue_scripts() {

			$screen = get_current_screen();
			// Commonly used
			if ( $screen->id == 'edit-' . MYCRED_EMAIL_KEY || $screen->id == MYCRED_EMAIL_KEY )
				wp_enqueue_style( 'mycred-admin' );

			// Edit Email Notice Styling
			if ( $screen->id == MYCRED_EMAIL_KEY ) {

				wp_enqueue_style( 'mycred-email-edit-notice' );
				wp_enqueue_style( 'mycred-bootstrap-grid' );
				wp_enqueue_style( 'mycred-forms' );

				wp_enqueue_script( 'mycred-edit-email', plugins_url( 'assets/js/edit-email.js', myCRED_EMAIL ), array( 'jquery' ), myCRED_EMAIL_VERSION, true );

				add_filter( 'postbox_classes_' . MYCRED_EMAIL_KEY . '_mycred-email-setup',   array( $this, 'metabox_classes' ) );
				add_filter( 'postbox_classes_' . MYCRED_EMAIL_KEY . '_mycred-email-tags',    array( $this, 'metabox_classes' ) );
				add_filter( 'postbox_classes_' . MYCRED_EMAIL_KEY . '_mycred-email-details', array( $this, 'metabox_classes' ) );

			}

			// Email Notice List Styling
			elseif ( $screen->id == 'edit-' . MYCRED_EMAIL_KEY )
				wp_enqueue_style( 'mycred-email-notices' );

		}

		/**
		 * Admin Header
		 * @since 1.1
		 * @version 1.0
		 */
		public function admin_header() {

			$screen = get_current_screen();
			if ( $screen->id == MYCRED_EMAIL_KEY && $this->emailnotices['use_html'] === false ) {
				remove_action( 'media_buttons', 'media_buttons' );
				echo '<style type="text/css">#ed_toolbar { display: none !important; }</style>';
			}

		}

		/**
		 * Disable WYSIWYG Editor
		 * @since 1.1
		 * @version 1.0.1
		 */
		public function disable_richedit( $default ) {

			global $post;

			if ( isset( $post->post_type ) && $post->post_type == MYCRED_EMAIL_KEY && $this->emailnotices['use_html'] === false )
				return false;

			return $default;

		}

		/**
		 * Apply Default Content
		 * @since 1.1
		 * @version 1.0
		 */
		public function default_content( $content ) {

			global $post_type;

			if ( $post_type == MYCRED_EMAIL_KEY && !empty( $this->emailnotices['content'] ) )
				$content = $this->emailnotices['content'];

			return $content;

		}

		/**
		 * Email Settings Metabox
		 * @since 1.1
		 * @version 1.1
		 */
		public function metabox_email_setup( $post ) {

			// Get trigger
			$email         = mycred_get_email_notice( $post->ID );
			$trigger       = $email->get_trigger();

			$instances     = mycred_get_email_instances();
			$references    = mycred_get_all_references();

			$uses_generic  = ( $trigger == '' || array_key_exists( $trigger, $instances ) ) ? true : false;
			$uses_specific = ( ! $uses_generic && array_key_exists( $trigger, $references ) ) ? true : false;
			$uses_custom   = ( ! $uses_generic && ! $uses_specific ) ? true : false;

?>
<div class="form">
	<div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<div class="form-group">
				<label for="mycred-email-instance"<?php if ( $post->post_status == 'publish' && empty( $trigger ) ) echo ' style="color:red;font-weight:bold;"'; ?>><?php _e( 'Send this email notice when...', 'mycred' ); ?></label>
				<select name="mycred_email[instance]" id="mycred-email-instance" class="form-control">
<?php

			// Loop though instances
			foreach ( $instances as $instance => $event ) {

				echo '<option value="' . $instance . '"';
				if ( $instance == $trigger || ( $instance == 'any' && $trigger == '' ) || ( $instance == 'custom' && ( $uses_specific || $uses_custom ) ) ) echo ' selected="selected"';
				echo '>... ' . esc_html( $event ) . '</option>';

			}

?>
				</select>
			</div>
			<div id="reference-selection" style="display: <?php if ( $uses_specific || $uses_custom ) echo 'block'; else echo 'none'; ?>;">
				<div class="form-group">
					<label for="mycred-email-ctype"><?php _e( 'Reference', 'mycred' ); ?></label>
					<select name="mycred_email[reference]" id="mycred-email-reference" class="form-control">
<?php

			$references['mycred_custom'] = __( 'Custom Reference', 'mycred' );

			foreach ( $references as $ref_id => $ref_description ) {

				echo '<option value="' . esc_attr( $ref_id ) . '"';
				if ( $uses_specific && $trigger == $ref_id ) echo ' selected="selected"';
				elseif ( $ref_id == 'mycred_custom' && $uses_custom ) echo ' selected="selected"';
				echo '>' . esc_html( $ref_description ) . '</option>';

			}

?>
					</select>
				</div>
				<div id="custom-reference-selection" style="display: <?php if ( $uses_custom ) echo 'block'; else echo 'none'; ?>;">
					<div class="form-group">
						<label for="mycred-email-custom-ref"><?php _e( 'Custom Reference', 'mycred' ); ?></label>
						<input type="text" name="mycred_email[custom_reference]" placeholder="<?php _e( 'required', 'mycred' ); ?>" id="mycred-email-custom-ref" class="form-control" value="<?php echo esc_attr( $trigger ); ?>" />
					</div>
					<p class="description" style="line-height: 16px;"><?php _e( 'This can be either a single reference or a comma separated list of references.', 'mycred' ); ?></p>
				</div>
			</div>
			<hr />

			<div class="form-group">
				<label for="mycred-email-ctype"><?php _e( 'Point Types', 'mycred' ); ?></label>
<?php

			if ( count( $this->point_types ) > 1 ) {

				mycred_types_select_from_checkboxes( 'mycred_email[ctype][]', 'mycred-email-ctype', $email->point_types );

			}

			else {

?>

				<p class="form-control-static"><?php echo $this->core->plural(); ?></p>
				<input type="hidden" name="mycred_email[ctype][]" id="mycred-email-ctype" value="<?php echo MYCRED_DEFAULT_TYPE_KEY; ?>" />
<?php

			}

?>

			</div>
			<hr />

			<div class="form-group" style="margin-bottom: 0;">
				<label for="mycred-email-recipient-user"><?php _e( 'Recipient:', 'mycred' ); ?></label>
				<div class="inline-radio">
					<label for="mycred-email-recipient-user"><input type="radio" name="mycred_email[recipient]" id="mycred-email-recipient-user" value="user" <?php checked( $email->settings['recipient'], 'user' ); ?> /> <?php _e( 'User', 'mycred' ); ?></label>
				</div>
				<div class="inline-radio">
					<label for="mycred-email-recipient-admin"><input type="radio" name="mycred_email[recipient]" id="mycred-email-recipient-admin" value="admin" <?php checked( $email->settings['recipient'], 'admin' ); ?> /> <?php _e( 'Administrator', 'mycred' ); ?></label>
				</div>
				<div class="inline-radio">
					<label for="mycred-email-recipient-both"><input type="radio" name="mycred_email[recipient]" id="mycred-email-recipient-both" value="both" <?php checked( $email->settings['recipient'], 'both' ); ?> /> <?php _e( 'Both', 'mycred' ); ?></label>
				</div>
			</div>
		</div>
	</div>

	<?php do_action( 'mycred_email_settings_box', $this ); ?>

</div>
<?php

		}

		/**
		 * Email Details Metabox
		 * @since 1.8
		 * @version 1.0
		 */
		public function metabox_email_details( $post ) {

			$email = mycred_get_email_notice( $post->ID );

?>
<div class="form">
	<div class="row">
		<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
			<div class="form-group">
				<label for="mycred-email-senders-name"><?php _e( 'Senders Name:', 'mycred' ); ?></label>
				<input type="text" name="mycred_email[senders_name]" id="mycred-email-senders-name" class="form-control" value="<?php echo esc_attr( $email->settings['senders_name'] ); ?>" />
			</div>
		</div>
		<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
			<div class="form-group">
				<label for="mycred-email-senders-email"><?php _e( 'Senders Email:', 'mycred' ); ?></label>
				<input type="text" name="mycred_email[senders_email]" id="mycred-email-senders-email" class="form-control" value="<?php echo esc_attr( $email->settings['senders_email'] ); ?>" />
			</div>
		</div>
		<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
			<div class="form-group">
				<label for="mycred-email-reply-to"><?php _e( 'Reply-To Email:', 'mycred' ); ?></label>
				<input type="text" name="mycred_email[reply_to]" id="mycred-email-reply-to" class="form-control" value="<?php echo esc_attr( $email->settings['reply_to'] ); ?>" />
			</div>
		</div>
	</div>
</div>
<?php

			if ( $this->emailnotices['use_html'] !== false ) {

?>
<div class="form">
	<div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<div class="form-group">
				<label for="mycred-email-styling"><?php _e( 'CSS Styling', 'mycred' ); ?></label>
				<textarea name="mycred_email[styling]" class="form-control code" rows="10" cols="30" id="mycred-email-styling"><?php echo esc_html( $email->get_email_styling() ); ?></textarea>
			</div>
		</div>
	</div>
</div>
<?php

			}

			do_action( 'mycred_email_details_box', $this );

		}

		/**
		 * Template Tags Metabox
		 * @since 1.1
		 * @version 1.2
		 */
		public function metabox_template_tags( $post ) {

?>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
		<h3><?php _e( 'Site Related', 'mycred' ); ?></h3>
		<div class="row">
			<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
				<strong>%blog_name%</strong>
			</div>
			<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
				<div><?php _e( 'Your websites title', 'mycred' ); ?></div>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
				<strong>%blog_url%</strong>
			</div>
			<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
				<div><?php _e( 'Your websites address', 'mycred' ); ?></div>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
				<strong>%blog_info%</strong>
			</div>
			<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
				<div><?php _e( 'Your websites tagline (description)', 'mycred' ); ?></div>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
				<strong>%admin_email%</strong>
			</div>
			<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
				<div><?php _e( 'Your websites admin email', 'mycred' ); ?></div>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
				<strong>%num_members%</strong>
			</div>
			<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
				<div><?php _e( 'Total number of blog members', 'mycred' ); ?></div>
			</div>
		</div>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
		<h3><?php _e( 'Instance Related', 'mycred' ); ?></h3>
		<div class="row">
			<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
				<strong>%new_balance%</strong>
			</div>
			<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
				<div><?php _e( 'The users new balance', 'mycred' ); ?></div>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
				<strong>%old_balance%</strong>
			</div>
			<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
				<div><?php _e( 'The users old balance', 'mycred' ); ?></div>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
				<strong>%amount%</strong>
			</div>
			<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
				<div><?php _e( 'The amount of points gained or lost in this instance', 'mycred' ); ?></div>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
				<strong>%entry%</strong>
			</div>
			<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
				<div><?php _e( 'The log entry', 'mycred' ); ?></div>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
				<div><?php printf( __( 'You can also use %s.', 'mycred' ), '<a href="http://codex.mycred.me/category/template-tags/temp-user/" target="_blank">' . __( 'user related template tags', 'mycred' ) . '</a>' ); ?></div>
			</div>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
		<h3><?php _e( 'Badge Related', 'mycred' ); ?></h3>
		<div class="row">
			<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
				<strong>%badge_title%</strong>
			</div>
			<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
				<div><?php _e( 'Gained badge title', 'mycred' ); ?></div>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
				<strong>%badge_image%</strong>
			</div>
			<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
				<div><?php _e( 'Gained badge image', 'mycred' ); ?></div>
			</div>
		</div>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
		<h3><?php _e( 'Rank Related', 'mycred' ); ?></h3>
		<div class="row">
			<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
				<strong>%rank_title%</strong>
			</div>
			<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
				<div><?php _e( 'Users rank title', 'mycred' ); ?></div>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
				<strong>%rank_image%</strong>
			</div>
			<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
				<div><?php _e( 'Users rank image', 'mycred' ); ?></div>
			</div>
		</div>
	</div>
</div>
<?php

		}

		/**
		 * Save Email Notice Details
		 * @since 1.1
		 * @version 1.2
		 */
		public function save_email_notice( $post_id, $post = NULL ) {

			if ( $post === NULL || ! $this->core->user_is_point_editor() || ! isset( $_POST['mycred_email'] ) ) return $post_id;

			global $mycred_types;

			$email       = mycred_get_email_notice( $post_id );
			$current     = $email->get_trigger();

			// Update Instance
			$instances   = mycred_get_email_instances();
			$references  = mycred_get_all_references();
			$instance    = '';
			$event       = '';
			$settings    = array();
			$point_types = array();

			// Generic
			if ( $_POST['mycred_email']['instance'] != '' && $_POST['mycred_email']['instance'] != 'custom' ) {

				$instance = sanitize_key( $_POST['mycred_email']['instance'] );
				if ( ! array_key_exists( $instance, $instances ) )
					$instance = '';

				else {
					$event = 'generic';
				}

			}

			// Specific
			elseif ( $_POST['mycred_email']['instance'] != '' ) {

				$event     = 'specific';
				$reference = sanitize_key( $_POST['mycred_email']['reference'] );

				// Based on built-in reference
				if ( array_key_exists( $reference, $references ) )
					$instance = $reference;

				// Based on custom reference
				else {

					$reference_list   = array();
					$custom_reference = explode( ',', $_POST['mycred_email']['custom_reference'] );

					foreach ( $custom_reference as $reference_id ) {

						$reference_id = sanitize_key( $reference_id );
						if ( ! empty( $reference_id ) && ! array_key_exists( $reference_id, $instances ) )
							$reference_list[] = $reference_id;

					}

					if ( ! empty( $reference_list ) )
						$instance = implode( ',', $reference_list );

				}

			}

			$email->set_trigger( $instance );

			// Construct new settings
			if ( ! empty( $_POST['mycred_email']['recipient'] ) )
				$settings['recipient']     = sanitize_text_field( $_POST['mycred_email']['recipient'] );

			if ( ! empty( $_POST['mycred_email']['senders_name'] ) )
				$settings['senders_name']  = sanitize_text_field( $_POST['mycred_email']['senders_name'] );

			if ( ! empty( $_POST['mycred_email']['senders_email'] ) )
				$settings['senders_email'] = sanitize_text_field( $_POST['mycred_email']['senders_email'] );

			if ( ! empty( $_POST['mycred_email']['reply_to'] ) )
				$settings['reply_to']      = sanitize_text_field( $_POST['mycred_email']['reply_to'] );

			$email->save_settings( $settings );

			// Point Types
			if ( array_key_exists( 'ctype', $_POST['mycred_email'] ) && ! empty( $_POST['mycred_email']['ctype'] ) ) {

				$checked_types = ( isset( $_POST['mycred_email']['ctype'] ) ) ? $_POST['mycred_email']['ctype'] : array();
				if ( ! empty( $checked_types ) ) {
					foreach ( $checked_types as $type_key ) {
						$type_key = sanitize_key( $type_key );
						if ( mycred_point_type_exists( $type_key ) && ! in_array( $type_key, $point_types ) )
							$point_types[] = $type_key;
					}
				}
				mycred_update_post_meta( $post_id, 'mycred_email_ctype', $point_types );

			}

			// Trigger changed, so we need to remove all existing instances of this email
			// before we add the new instance in.
			if ( $current != $instance ) {
				foreach ( $mycred_types as $type_id => $label ) {

					mycred_delete_email_trigger( $post_id, $type_id );

				}
			}

			if ( ! empty( $point_types ) ) {
				foreach ( $point_types as $type_id ) {

					mycred_add_email_trigger( $event, $instance, $post_id, $type_id );

				}
			}

			// If rich editing is disabled bail now
			if ( $email->emailnotices['use_html'] === false ) return;

			// Save styling
			if ( ! empty( $_POST['mycred_email']['styling'] ) )
				mycred_update_post_meta( $post_id, 'mycred_email_styling', wp_kses_post( $_POST['mycred_email']['styling'] ) );

		}

		/**
		 * Email Notice Check
		 * @since 1.1
		 * @version 1.6
		 */
		public function email_check( $ran, $request, $mycred ) {

			// Exit now if $ran is false or new settings is not yet saved.
			if ( $ran === false || ! isset( $this->emailnotices['send'] ) ) return $ran;

			$user_id        = absint( $request['user_id'] );
			$balance        = $mycred->get_users_balance( $user_id );
			$point_type     = $mycred->get_point_type_key();

			// Check for triggered emails
			$emails         = mycred_get_triggered_emails( $request, $balance );

			// No emails, bail
			if ( empty( $emails ) ) return $ran;

			$request['new'] = $balance;
			$request['old'] = $balance - $request['amount'];

			// This event might have triggered multiple emails
			foreach ( $emails as $notice_id ) {

				// Respect unsubscriptions
				if ( mycred_user_wants_email( $user_id, $notice_id ) )
					mycred_send_new_email( $notice_id, $request, $point_type );

			}

			return $ran;

		}

		/**
		 * Badge Check
		 * @since 1.7
		 * @version 1.1
		 */
		public function badge_check( $user_id, $badge_id, $level_reached ) {

			if ( $level_reached === false ) return;

			$badge       = mycred_get_badge( $badge_id );

			$instance    = 'badge_level';
			$users_level = $badge->get_users_current_level( $user_id );

			// Earning a badge
			if ( $users_level === false )
				$instance = 'badge_new';

			global $mycred_types;

			foreach ( $mycred_types as $type_id => $label ) {

				$emails     = mycred_get_event_emails( $type_id, 'generic', $instance );
				if ( empty( $emails ) ) continue;

				$mycred     = mycred( $type_id );
				$balance    = $mycred->get_users_balance( $user_id );
				$point_type = $mycred->get_point_type_key();

				$request    = array(
					'ref'      => $instance,
					'user_id'  => $user_id,
					'amount'   => 0,
					'entry'    => 'New Badge',
					'ref_id'   => $badge_id,
					'data'     => array( 'ref_type' => 'post' ),
					'type'     => $type_id,
					'level'    => $level_reached,
					'new'      => $balance,
					'old'      => $balance
				);

				foreach ( $emails as $notice_id ) {

					// Respect unsubscriptions
					if ( mycred_user_wants_email( $user_id, $notice_id ) )
						mycred_send_new_email( $notice_id, $request, $point_type );

				}

			}

		}

		/**
		 * Rank Promotions
		 * @since 1.7.6
		 * @version 1.1
		 */
		public function rank_promotion( $user_id, $rank_id, $query, $point_type ) {

			$emails     = mycred_get_event_emails( $point_type, 'generic', 'rank_up' );
			if ( empty( $emails ) ) return;

			$mycred     = mycred( $point_type );
			$balance    = $mycred->get_users_balance( $user_id );

			$request    = array(
				'ref'      => 'rank_promotion',
				'user_id'  => $user_id,
				'amount'   => 0,
				'entry'    => 'New Rank',
				'ref_id'   => $rank_id,
				'data'     => array( 'ref_type' => 'post' ),
				'type'     => $point_type,
				'new'      => $balance,
				'old'      => $balance
			);

			foreach ( $emails as $notice_id ) {

				// Respect unsubscriptions
				if ( mycred_user_wants_email( $user_id, $notice_id ) )
					mycred_send_new_email( $notice_id, $request, $point_type );

			}

		}


		/**
		 * Rank Demotions
		 * @since 1.7.6
		 * @version 1.1
		 */
		public function rank_demotion( $user_id, $rank_id, $query, $point_type ) {

			$emails     = mycred_get_event_emails( $point_type, 'generic', 'rank_down' );
			if ( empty( $emails ) ) return;

			$mycred     = mycred( $point_type );
			$balance    = $mycred->get_users_balance( $user_id );

			$request    = array(
				'ref'      => 'rank_promotion',
				'user_id'  => $user_id,
				'amount'   => 0,
				'entry'    => 'New Rank',
				'ref_id'   => $rank_id,
				'data'     => array( 'ref_type' => 'post' ),
				'type'     => $point_type,
				'new'      => $balance,
				'old'      => $balance
			);

			foreach ( $emails as $notice_id ) {

				// Respect unsubscriptions
				if ( mycred_user_wants_email( $user_id, $notice_id ) )
					mycred_send_new_email( $notice_id, $request, $point_type );

			}

		}
		
		/**
		 * Cashcred Pending
		 * @since 2.1.1
		 * @version 1.0
		 */
		public function after_payment_request( $payment_withdrawal_request , $meta_value ) {

            $point_type = $payment_withdrawal_request['point_type'];
			$user_id = $payment_withdrawal_request['user_id'];

			$status = 'pending';
			if( $meta_value == 'Approved' )
			    $status = 'approved';
			elseif( $meta_value == 'Cancelled' )
			    $status = 'cancel';
			    
			$emails  = mycred_get_event_emails( $point_type, 'generic', 'cashcred_' . $status );
			if ( empty( $emails ) ) return;
			$mycred     = mycred( $point_type );
			$balance    = $payment_withdrawal_request['user_balance'];

			$request    = array(
				'ref'      => 'cashcred_payment_process',
				'user_id'  => $user_id,
				'amount'   => $payment_withdrawal_request['points'],
				'entry'    => 'cashcred_payment_request',
				'ref_id'   => $payment_withdrawal_request['post_id'],
				'data'     => array( 'ref_type' => 'post' ),
				'type'     => $point_type,
				'new'      => $balance,
				'old'      => $balance
			);

			foreach ( $emails as $notice_id ) {
				// Respect unsubscriptions
				if ( mycred_user_wants_email( $user_id, $notice_id ) )
					mycred_send_new_email( $notice_id, $request, $point_type );

			}
		
		}

		/**
		 * Add to General Settings
		 * @since 1.1
		 * @version 1.1
		 */
		public function after_general_settings( $mycred = NULL ) {

			$this->emailnotices = mycred_apply_defaults( $this->default_prefs, $this->emailnotices );

?>
<h4><span class="dashicons dashicons-admin-plugins static"></span><?php _e( 'Email Notices', 'mycred' ); ?></h4>
<div class="body" style="display:none;">

	<div class="row">
		<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
			<h3><?php _e( 'Format', 'mycred' ); ?></h3>
			<div class="form-group">
				<div class="radio">
					<label for="<?php echo $this->field_id( array( 'use_html' => 'no' ) ); ?>"><input type="radio" name="<?php echo $this->field_name( 'use_html' ); ?>" id="<?php echo $this->field_id( array( 'use_html' => 'no' ) ); ?>" <?php checked( $this->emailnotices['use_html'], 0 ); ?> value="0" /> <?php _e( 'Plain Text', 'mycred' ); ?></label>
				</div>
				<div class="radio">
					<label for="<?php echo $this->field_id( array( 'use_html' => 'yes' ) ); ?>"><input type="radio" name="<?php echo $this->field_name( 'use_html' ); ?>" id="<?php echo $this->field_id( array( 'use_html' => 'yes' ) ); ?>" <?php checked( $this->emailnotices['use_html'], 1 ); ?> value="1" /> HTML</label>
				</div>
			</div>
		</div>
		<div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
			<h3><?php _e( 'Schedule', 'mycred' ); ?></h3>
			<div class="form-group">
				<?php if ( defined( 'DISABLE_WP_CRON' ) && DISABLE_WP_CRON ) : ?>
				<input type="hidden" name="<?php echo $this->field_name( 'send' ); ?>" value="" />
				<p class="form-control-static"><?php _e( 'WordPress Cron is disabled. Emails will be sent immediately.', 'mycred' ); ?></p>
				<?php else : ?>
				<div class="radio">
					<label for="<?php echo $this->field_id( 'send' ); ?>"><input type="radio" name="<?php echo $this->field_name( 'send' ); ?>" id="<?php echo $this->field_id( 'send' ); ?>" <?php checked( $this->emailnotices['send'], '' ); ?> value="" /> <?php _e( 'Send emails immediately', 'mycred' ); ?></label>
				</div>
				<div class="radio">
					<label for="<?php echo $this->field_id( 'send' ); ?>-hourly"><input type="radio" name="<?php echo $this->field_name( 'send' ); ?>" id="<?php echo $this->field_id( 'send' ); ?>-hourly" <?php checked( $this->emailnotices['send'], 'hourly' ); ?> value="hourly" /> <?php _e( 'Send emails once an hour', 'mycred' ); ?></label>
				</div>
				<div class="radio">
					<label for="<?php echo $this->field_id( 'send' ); ?>-daily"><input type="radio" name="<?php echo $this->field_name( 'send' ); ?>" id="<?php echo $this->field_id( 'send' ); ?>-daily" <?php checked( $this->emailnotices['send'], 'daily' ); ?> value="daily" /> <?php _e( 'Send emails once a day', 'mycred' ); ?></label>
				</div>
				<?php endif; ?>
			</div>
		</div>
		<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
			<h3><?php _e( 'Advanced', 'mycred' ); ?></h3>
			<div class="form-group">
				<div class="checkbox">
					<label for="<?php echo $this->field_id( array( 'filter' => 'subject' ) ); ?>"><input type="checkbox" name="<?php echo $this->field_name( array( 'filter' => 'subject' ) ); ?>" id="<?php echo $this->field_id( array( 'filter' => 'subject' ) ); ?>" <?php checked( $this->emailnotices['filter']['subject'], 1 ); ?> value="1" /> <?php _e( 'Filter Email Subjects', 'mycred' ); ?></label>
				</div>
				<div class="checkbox">
					<label for="<?php echo $this->field_id( array( 'filter' => 'content' ) ); ?>"><input type="checkbox" name="<?php echo $this->field_name( array( 'filter' => 'content' ) ); ?>" id="<?php echo $this->field_id( array( 'filter' => 'content' ) ); ?>" <?php checked( $this->emailnotices['filter']['content'], 1 ); ?> value="1" /> <?php _e( 'Filter Email Body', 'mycred' ); ?></label>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<div class="form-group">
				<div class="checkbox">
					<label for="<?php echo $this->field_id( 'override' ); ?>"><input type="checkbox" name="<?php echo $this->field_name( 'override' ); ?>" id="<?php echo $this->field_id( 'override' ); ?>" <?php checked( $this->emailnotices['override'], 1 ); ?> value="1" /> <?php _e( 'SMTP Debug. Enable if you are experiencing issues with wp_mail() or if you use a SMTP plugin for emails.', 'mycred' ); ?></label>
				</div>
			</div>
		</div>
	</div>

	<h3 style="margin-bottom: 0;"><?php _e( 'Available Shortcodes', 'mycred' ); ?></h3>
	<div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<p><a href="http://codex.mycred.me/shortcodes/mycred_email_subscriptions/" target="_blank">[mycred_email_subscriptions]</a></p>
		</div>
	</div>

	<h3><?php _e( 'Defaults', 'mycred' ); ?></h3>
	<div class="row">
		<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
			<div class="form-group">
				<label for="<?php echo $this->field_id( array( 'from' => 'name' ) ); ?>"><?php _e( 'Senders Name:', 'mycred' ); ?></label>
				<input type="text" name="<?php echo $this->field_name( array( 'from' => 'name' ) ); ?>" id="<?php echo $this->field_id( array( 'from' => 'name' ) ); ?>" value="<?php echo esc_attr( $this->emailnotices['from']['name'] ); ?>" class="form-control" />
			</div>
		</div>
		<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
			<div class="form-group">
				<label for="<?php echo $this->field_id( array( 'from' => 'email' ) ); ?>"><?php _e( 'Senders Email:', 'mycred' ); ?></label>
				<input type="text" name="<?php echo $this->field_name( array( 'from' => 'email' ) ); ?>" id="<?php echo $this->field_id( array( 'from' => 'email' ) ); ?>" value="<?php echo esc_attr( $this->emailnotices['from']['email'] ); ?>" class="form-control" />
			</div>
		</div>
		<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
			<div class="form-group">
				<label for="<?php echo $this->field_id( array( 'from' => 'reply_to' ) ); ?>"><?php _e( 'Reply-To:', 'mycred' ); ?></label>
				<input type="text" name="<?php echo $this->field_name( array( 'from' => 'reply_to' ) ); ?>" id="<?php echo $this->field_id( array( 'from' => 'reply_to' ) ); ?>" value="<?php echo esc_attr( $this->emailnotices['from']['reply_to'] ); ?>" class="form-control" />
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<div class="form-group">
				<label for="<?php echo $this->field_id( 'content' ); ?>"><?php _e( 'Default Email Content', 'mycred' ); ?></label>
				<textarea rows="10" cols="50" name="<?php echo $this->field_name( 'content' ); ?>" id="<?php echo $this->field_id( 'content' ); ?>" class="form-control"><?php echo esc_attr( $this->emailnotices['content'] ); ?></textarea>
				<p><span class="description"><?php _e( 'Default email content.', 'mycred' ); ?></span></p>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<div class="form-group">
				<label for="<?php echo $this->field_id( 'styling' ); ?>"><?php _e( 'Default CSS Styling', 'mycred' ); ?></label>
				<textarea rows="10" cols="50" name="<?php echo $this->field_name( 'styling' ); ?>" id="<?php echo $this->field_id( 'styling' ); ?>" class="form-control"><?php echo esc_attr( $this->emailnotices['styling'] ); ?></textarea>
				<p><span class="description"><?php _e( 'Default email CSS styling. Note that if you intend to send HTML emails, you should use inline CSS styling for best results.', 'mycred' ); ?></span></p>
			</div>
		</div>
	</div>

</div>
<?php

		}

		/**
		 * Save Settings
		 * @since 1.1
		 * @version 1.3
		 */
		public function sanitize_extra_settings( $new_data, $data, $core ) {

			$new_data['emailnotices']['use_html']          = ( isset( $data['emailnotices']['use_html'] ) ) ? absint( $data['emailnotices']['use_html'] ) : 0;

			$new_data['emailnotices']['filter']['subject'] = ( isset( $data['emailnotices']['filter']['subject'] ) ) ? 1 : 0;
			$new_data['emailnotices']['filter']['content'] = ( isset( $data['emailnotices']['filter']['content'] ) ) ? 1 : 0;

			$new_data['emailnotices']['from']['name']      = sanitize_text_field( $data['emailnotices']['from']['name'] );
			$new_data['emailnotices']['from']['email']     = sanitize_text_field( $data['emailnotices']['from']['email'] );
			$new_data['emailnotices']['from']['reply_to']  = sanitize_text_field( $data['emailnotices']['from']['reply_to'] );

			$new_data['emailnotices']['content']           = wp_kses_post( $data['emailnotices']['content'] );
			$new_data['emailnotices']['styling']           = sanitize_textarea_field( $data['emailnotices']['styling'] );

			$new_data['emailnotices']['send']              = sanitize_text_field( $data['emailnotices']['send'] );
			$new_data['emailnotices']['override']          = ( isset( $data['emailnotices']['override'] ) ) ? 1 : 0;

			return $new_data;

		}

	}

endif;

/**
 * Load Email Notice Module
 * @since 1.7
 * @version 1.0
 */
if ( ! function_exists( 'mycred_load_email_notice_addon' ) ) :
	function mycred_load_email_notice_addon( $modules, $point_types ) {

		$modules['solo']['emails'] = new myCRED_Email_Notice_Module();
		$modules['solo']['emails']->load();

		return $modules;

	}
endif;
add_filter( 'mycred_load_modules', 'mycred_load_email_notice_addon', 60, 2 );
