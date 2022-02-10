<?php
/**
 * Addon: Coupons
 * Addon URI: http://codex.mycred.me/chapter-iii/coupons/
 * Version: 1.4
 */
if ( ! defined( 'myCRED_VERSION' ) ) exit;

define( 'myCRED_COUPONS',         __FILE__ );
define( 'myCRED_COUPONS_DIR',     myCRED_ADDONS_DIR . 'coupons/' );
define( 'myCRED_COUPONS_VERSION', '1.3' );

// Coupon Key
if ( ! defined( 'MYCRED_COUPON_KEY' ) )
	define( 'MYCRED_COUPON_KEY', 'mycred_coupon' );

require_once myCRED_COUPONS_DIR . 'includes/mycred-coupon-functions.php';
require_once myCRED_COUPONS_DIR . 'includes/mycred-coupon-object.php';
require_once myCRED_COUPONS_DIR . 'includes/mycred-coupon-shortcodes.php';

/**
 * myCRED_Coupons_Module class
 * @since 1.4
 * @version 1.4
 */
if ( ! class_exists( 'myCRED_Coupons_Module' ) ) :
	class myCRED_Coupons_Module extends myCRED_Module {

		/**
		 * Construct
		 */
		function __construct() {

			parent::__construct( 'myCRED_Coupons_Module', array(
				'module_name' => 'coupons',
				'defaults'    => mycred_get_addon_defaults( 'coupons' ),
				'register'    => false,
				'add_to_core' => true,
				'menu_pos'    => 90
			) );

			add_filter( 'mycred_parse_log_entry_coupon', array( $this, 'parse_log_entry' ), 10, 2 );

		}

		/**
		 * Hook into Init
		 * @since 1.4
		 * @version 1.0
		 */
		public function module_init() {

			$this->register_coupons();

			add_shortcode( MYCRED_SLUG . '_load_coupon', 'mycred_render_shortcode_load_coupon' );

			add_action( 'mycred_add_menu',       array( $this, 'add_to_menu' ), $this->menu_pos );
			add_action( 'admin_notices',         array( $this, 'warn_bad_expiration' ), $this->menu_pos );

		}

		/**
		 * Hook into Admin Init
		 * @since 1.4
		 * @version 1.1
		 */
		public function module_admin_init() {

			add_filter( 'post_updated_messages',   array( $this, 'post_updated_messages' ) );

			add_filter( 'parent_file',             array( $this, 'parent_file' ) );
			add_filter( 'submenu_file',            array( $this, 'subparent_file' ), 10, 2 );

			add_filter( 'enter_title_here',        array( $this, 'enter_title_here' ) );
			add_filter( 'post_row_actions',        array( $this, 'adjust_row_actions' ), 10, 2 );

			add_action( 'admin_head-post.php',     array( $this, 'edit_coupons_style' ) );
			add_action( 'admin_head-post-new.php', array( $this, 'edit_coupons_style' ) );
			add_action( 'admin_head-edit.php',     array( $this, 'coupon_style' ) );

			add_filter( 'manage_' . MYCRED_COUPON_KEY . '_posts_columns',       array( $this, 'adjust_column_headers' ) );
			add_action( 'manage_' . MYCRED_COUPON_KEY . '_posts_custom_column', array( $this, 'adjust_column_content' ), 10, 2 );
			add_filter( 'bulk_actions-edit-' . MYCRED_COUPON_KEY,               array( $this, 'bulk_actions' ) );
			add_action( 'save_post_' . MYCRED_COUPON_KEY,                       array( $this, 'save_coupon' ), 10, 2 );

		}

		/**
		 * Register Coupons Post Type
		 * @since 1.4
		 * @version 1.0.2
		 */
		protected function register_coupons() {

			$labels = array(
				'name'                 => __( 'Coupons', 'mycred' ),
				'singular_name'        => __( 'Coupon', 'mycred' ),
				'add_new'              => __( 'Create New', 'mycred' ),
				'add_new_item'         => __( 'Create New', 'mycred' ),
				'edit_item'            => __( 'Edit Coupon', 'mycred' ),
				'new_item'             => __( 'New Coupon', 'mycred' ),
				'all_items'            => __( 'Coupons', 'mycred' ),
				'view_item'            => '',
				'search_items'         => __( 'Search coupons', 'mycred' ),
				'not_found'            => __( 'No coupons found', 'mycred' ),
				'not_found_in_trash'   => __( 'No coupons found in Trash', 'mycred' ), 
				'parent_item_colon'    => '',
				'menu_name'            => __( 'Email Notices', 'mycred' )
			);
			$args = array(
				'labels'               => $labels,
				'supports'             => array( 'title' ),
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

			register_post_type( MYCRED_COUPON_KEY, apply_filters( 'mycred_register_coupons', $args ) );

		}

		/**
		 * Adjust Update Messages
		 * @since 1.4
		 * @version 1.0.2
		 */
		public function post_updated_messages( $messages ) {

			$messages[ MYCRED_COUPON_KEY ] = array(
				0  => '',
				1  => __( 'Coupon updated.', 'mycred' ),
				2  => __( 'Coupon updated.', 'mycred' ),
				3  => __( 'Coupon updated.', 'mycred' ),
				4  => __( 'Coupon updated.', 'mycred' ),
				5  => false,
				6  => __( 'Coupon published.', 'mycred' ),
				7  => __( 'Coupon updated.', 'mycred' ),
				8  => __( 'Coupon updated.', 'mycred' ),
				9  => __( 'Coupon updated.', 'mycred' ),
				10 => __( 'Coupon updated.', 'mycred' ),
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
				__( 'Coupons', 'mycred' ),
				__( 'Coupons', 'mycred' ),
				$this->core->get_point_editor_capability(),
				'edit.php?post_type=' . MYCRED_COUPON_KEY
			);

		}

		/**
		 * Parent File
		 * @since 1.7
		 * @version 1.0.2
		 */
		public function parent_file( $parent = '' ) {

			global $pagenow;

			if ( isset( $_GET['post'] ) && mycred_get_post_type( $_GET['post'] ) == MYCRED_COUPON_KEY && isset( $_GET['action'] ) && $_GET['action'] == 'edit' )
				return MYCRED_MAIN_SLUG;

			if ( $pagenow == 'post-new.php' && isset( $_GET['post_type'] ) && $_GET['post_type'] == MYCRED_COUPON_KEY )
				return MYCRED_MAIN_SLUG;

			return $parent;

		}

		/**
		 * Sub Parent File
		 * @since 1.7
		 * @version 1.0.1
		 */
		public function subparent_file( $subparent = '', $parent = '' ) {

			global $pagenow;

			if ( ( $pagenow == 'edit.php' || $pagenow == 'post-new.php' ) && isset( $_GET['post_type'] ) && $_GET['post_type'] == MYCRED_COUPON_KEY ) {

				return 'edit.php?post_type=' . MYCRED_COUPON_KEY;
			
			}

			elseif ( $pagenow == 'post.php' && isset( $_GET['post'] ) && get_post_type( $_GET['post'] ) == MYCRED_COUPON_KEY ) {

				return 'edit.php?post_type=' . MYCRED_COUPON_KEY;

			}

			return $subparent;

		}

		/**
		 * Adjust Enter Title Here
		 * @since 1.4
		 * @version 1.0.1
		 */
		public function enter_title_here( $title ) {

			global $post_type;

			if ( $post_type == MYCRED_COUPON_KEY )
				return __( 'Coupon Code', 'mycred' );

			return $title;

		}

		/**
		 * Adjust Column Header
		 * @since 1.4
		 * @version 1.1
		 */
		public function adjust_column_headers( $defaults ) {

			$columns            = array();
			$columns['cb']      = $defaults['cb'];

			// Add / Adjust
			$columns['title']   = __( 'Coupon Code', 'mycred' );
			$columns['value']   = __( 'Value', 'mycred' );
			$columns['usage']   = __( 'Used', 'mycred' );
			$columns['limits']  = __( 'Limits', 'mycred' );
			$columns['expires'] = __( 'Expires', 'mycred' );

			if ( count( $this->point_types ) > 1 )
				$columns['ctype'] = __( 'Point Type', 'mycred' );

			return $columns;

		}

		/**
		 * Adjust Column Body
		 * @since 1.4
		 * @version 1.1.3
		 */
		public function adjust_column_content( $column_name, $post_id ) {

			$coupon = mycred_get_coupon( $post_id );

			switch ( $column_name ) {

				case 'value' :

					$mycred = mycred( $coupon->point_type );

					echo $mycred->format_creds( $coupon->value );

				break;

				case 'usage' :

					if ( $coupon->used == 0 )
						echo '-';

					else {

						$set_type = $coupon->point_type;
						$page     = MYCRED_SLUG;

						if ( $set_type != MYCRED_DEFAULT_TYPE_KEY && array_key_exists( $set_type, $this->point_types ) )
							$page .= '_' . $set_type;

						$url      = add_query_arg( array( 'page' => $page, 'ref' => 'coupon', 'ref_id' => $post_id ), admin_url( 'admin.php' ) );
						echo '<a href="' . esc_url( $url ) . '">' . sprintf( _n( '1 time', '%d times', $coupon->used, 'mycred' ), $coupon->used ) . '</a>';

					}

				break;

				case 'limits' :

					printf( '%1$s: %2$d<br />%3$s: %4$d', __( 'Total', 'mycred' ), $coupon->max_global, __( 'Per User', 'mycred' ), $coupon->max_user );

				break;

				case 'expires' :

					if ( $coupon->expires === false )
						echo '-';

					else {

						if ( $coupon->expires_unix < current_time( 'timestamp' ) ) {

							mycred_trash_post( $post_id );

							echo '<span style="color:red;">' . __( 'Expired', 'mycred' ) . '</span>';

						}

						else {

							echo sprintf( __( 'In %s time', 'mycred' ), human_time_diff( $coupon->expires_unix ) ) . '<br /><small class="description">' . date( get_option( 'date_format' ), $coupon->expires_unix ) . '</small>';

						}

					}

				break;

				case 'ctype' :

					if ( isset( $this->point_types[ $coupon->point_type ] ) )
						echo $this->point_types[ $coupon->point_type ];

					else
						echo '-';

				break;

			}
		}

		/**
		 * Adjust Bulk Actions
		 * @since 1.7
		 * @version 1.0
		 */
		public function bulk_actions( $actions ) {

			unset( $actions['edit'] );
			return $actions;

		}

		/**
		 * Adjust Row Actions
		 * @since 1.4
		 * @version 1.0.1
		 */
		public function adjust_row_actions( $actions, $post ) {

			if ( $post->post_type == MYCRED_COUPON_KEY ) {
				unset( $actions['inline hide-if-no-js'] );
				unset( $actions['view'] );
			}

			return $actions;

		}

		/**
		 * Edit Coupon Style
		 * @since 1.7
		 * @version 1.0.2
		 */
		public function edit_coupons_style() {

			global $post_type;

			if ( $post_type !== MYCRED_COUPON_KEY ) return;

			wp_enqueue_style( 'mycred-bootstrap-grid' );
			wp_enqueue_style( 'mycred-forms' );

			add_filter( 'postbox_classes_' . MYCRED_COUPON_KEY . '_mycred-coupon-setup',        array( $this, 'metabox_classes' ) );
			add_filter( 'postbox_classes_' . MYCRED_COUPON_KEY . '_mycred-coupon-limits',       array( $this, 'metabox_classes' ) );
			add_filter( 'postbox_classes_' . MYCRED_COUPON_KEY . '_mycred-coupon-requirements', array( $this, 'metabox_classes' ) );
			add_filter( 'postbox_classes_' . MYCRED_COUPON_KEY . '_mycred-coupon-usage',        array( $this, 'metabox_classes' ) );

			echo '<style type="text/css">#misc-publishing-actions #visibility { display: none; }</style>';

		}

		/**
		 * Coupon Style
		 * @since 1.7
		 * @version 1.0
		 */
		public function coupon_style() { }

		/**
		 * Add Meta Boxes
		 * @since 1.4
		 * @version 1.1.1
		 */
		public function add_metaboxes( $post ) {

			add_meta_box(
				'mycred-coupon-setup',
				__( 'Coupon Setup', 'mycred' ),
				array( $this, 'metabox_coupon_setup' ),
				MYCRED_COUPON_KEY,
				'normal',
				'core'
			);

			add_meta_box(
				'mycred-coupon-limits',
				__( 'Coupon Limits', 'mycred' ),
				array( $this, 'metabox_coupon_limits' ),
				MYCRED_COUPON_KEY,
				'normal',
				'core'
			);

			add_meta_box(
				'mycred-coupon-requirements',
				__( 'Coupon Requirements', 'mycred' ),
				array( $this, 'mycred_coupon_requirements' ),
				MYCRED_COUPON_KEY,
				'side',
				'core'
			);

			if ( $post->post_status == 'publish' )
				add_meta_box(
					'mycred-coupon-usage',
					__( 'Coupon Usage', 'mycred' ),
					array( $this, 'mycred_coupon_usage' ),
					MYCRED_COUPON_KEY,
					'side',
					'core'
				);

		}

		/**
		 * Admin Notice
		 * If we are have an issue with the expiration date set for this coupon we need to warn the user.
		 * @since 1.7.5
		 * @version 1.0.1
		 */
		public function warn_bad_expiration() {

			if ( isset( $_GET['post'] ) && isset( $_GET['action'] ) && $_GET['action'] == 'edit' && get_post_type( absint( $_GET['post'] ) ) == MYCRED_COUPON_KEY ) {

				$post_id            = absint( $_GET['post'] );
				$expiration_warning = mycred_get_post_meta( $post_id, '_warning_bad_expiration', true );

				if ( $expiration_warning != '' ) {

					mycred_delete_post_meta( $post_id, '_warning_bad_expiration' );

					echo '<div id="message" class="error notice is-dismissible"><p>' . __( 'Warning. The previous expiration date set for this coupon was formatted incorrectly and was deleted. If you still want the coupon to expire, please enter a new date or leave empty to disable.', 'mycred' ) . '</p><button type="button" class="notice-dismiss"></button></div>';

				}

			}

		}

		/**
		 * Metabox: Coupon Setup
		 * @since 1.4
		 * @version 1.2.1
		 */
		public function metabox_coupon_setup( $post ) {

			$coupon = mycred_get_coupon( $post->ID );

			if ( $coupon->point_type != $this->core->cred_id )
				$mycred = mycred( $coupon->point_type );
			else
				$mycred = $this->core;

?>
<div class="form">
	<div class="row">
		<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
			<div class="form-group">
				<label for=""><?php _e( 'Value', 'mycred' ); ?></label>
				<input type="text" name="mycred_coupon[value]" class="form-control" id="mycred-coupon-value" value="<?php echo $mycred->number( $coupon->value ); ?>" />
				<span class="description"><?php echo $mycred->template_tags_general( __( 'The amount of %plural% a user receives when redeeming this coupon.', 'mycred' ) ); ?></span>
			</div>
		</div>
		<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
			<div class="form-group">
				<label for=""><?php _e( 'Point Type', 'mycred' ); ?></label>
				<?php if ( count( $this->point_types ) > 1 ) : ?>

					<?php mycred_types_select_from_dropdown( 'mycred_coupon[type]', 'mycred-coupon-type', $coupon->point_type, false, ' class="form-control"' ); ?><br />
					<span class="description"><?php _e( 'Select the point type that this coupon is applied.', 'mycred' ); ?></span>

				<?php else : ?>

					<p class="form-control-static"><?php echo $mycred->plural(); ?></p>
					<input type="hidden" name="mycred_coupon[type]" value="<?php echo MYCRED_DEFAULT_TYPE_KEY; ?>" />

				<?php endif; ?>
			</div>
		</div>
		<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
			<div class="form-group">
				<label for=""><?php _e( 'Expire', 'mycred' ); ?></label>
				<input type="text" name="mycred_coupon[expires]" class="form-control" id="mycred-coupon-expire" maxlength="10" value="<?php echo esc_attr( $coupon->expires ); ?>" placeholder="YYYY-MM-DD" />
				<span class="description"><?php _e( 'Optional date when this coupon expires. Expired coupons will be trashed.', 'mycred' ); ?></span>
			</div>
		</div>
	</div>
</div>
<?php

		}

		/**
		 * Metabox: Coupon Limits
		 * @since 1.4
		 * @version 1.1
		 */
		public function metabox_coupon_limits( $post ) {

			$coupon = mycred_get_coupon( $post->ID );

			if ( $coupon->point_type != $this->core->cred_id )
				$mycred = mycred( $coupon->point_type );
			else
				$mycred = $this->core;

?>
<div class="form">
	<div class="row">
		<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
			<div class="form-group">
				<label for="mycred-coupon-global"><?php _e( 'Global Maximum', 'mycred' ); ?></label>
				<input type="text" name="mycred_coupon[global]" class="form-control" id="mycred-coupon-global" value="<?php echo absint( $coupon->max_global ); ?>" />
				<span class="description"><?php _e( 'The maximum number of times this coupon can be used in total. Once this is reached, the coupon is automatically trashed. If 0 is selected then the coupon will not work and will automatically expire. For more info please read the <a href="https://codex.mycred.me/chapter-iii/coupons/creating-coupons/">Description</a>', 'mycred' ); ?></span>
			</div>
		</div>
		<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
			<div class="form-group">
				<label for="mycred-coupon-user"><?php _e( 'User Maximum', 'mycred' ); ?></label>
				<input type="text" name="mycred_coupon[user]" class="form-control" id="mycred-coupon-user" value="<?php echo absint( $coupon->max_user ); ?>" />
				<span class="description"><?php _e( 'The maximum number of times this coupon can be used by a user. If 0 is selected then the coupon will not work.', 'mycred' ); ?></span>
			</div>
		</div>
	</div>
</div>
<?php

		}

		/**
		 * Metabox: Coupon Requirements
		 * @since 1.4
		 * @version 1.1.1
		 */
		public function mycred_coupon_requirements( $post ) {

			$coupon = mycred_get_coupon( $post->ID );

			if ( $coupon->point_type != $this->core->cred_id )
				$mycred = mycred( $coupon->point_type );
			else
				$mycred = $this->core;

?>
<div class="form">
	<div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<div class="form-group">
				<label for="mycred-coupon-min_balance"><?php _e( 'Minimum Balance', 'mycred' ); ?></label>
				<div>
					<input type="text" name="mycred_coupon[min_balance]" <?php if ( count( $this->point_types ) > 1 ) echo 'size="8"'; else echo ' style="width: 99%;"'; ?> id="mycred-coupon-min_balance" value="<?php echo $mycred->number( $coupon->requires_min['value'] ); ?>" />
					<?php echo mycred_types_select_from_dropdown( 'mycred_coupon[min_balance_type]', 'mycred-coupon-min_balance_type', $coupon->requires_min_type, true, ' style="vertical-align: top;"' ); ?>
				</div>
				<span class="description"><?php _e( 'Optional minimum balance a user must have in order to use this coupon. Use zero to disable.', 'mycred' ); ?></span>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
			<div class="form-group">
				<label for="mycred-coupon-max_balance"><?php _e( 'Maximum Balance', 'mycred' ); ?></label>
				<div>
					<input type="text" name="mycred_coupon[max_balance]" <?php if ( count( $this->point_types ) > 1 ) echo 'size="8"'; else echo ' style="width: 99%;"'; ?> id="mycred-coupon-max_balance" value="<?php echo $mycred->number( $coupon->requires_max['value'] ); ?>" />
					<?php echo mycred_types_select_from_dropdown( 'mycred_coupon[max_balance_type]', 'mycred-coupon-max_balance_type', $coupon->requires_max_type, true, ' style="vertical-align: top;"' ); ?>
				</div>
				<span class="description"><?php _e( 'Optional maximum balance a user can have in order to use this coupon. Use zero to disable.', 'mycred' ); ?></span>
			</div>
		</div>
	</div>
</div>
<?php

		}

		/**
		 * Metabox: Coupon Usage
		 * @since 1.6
		 * @version 1.0.1
		 */
		public function mycred_coupon_usage( $post ) {

			$count = mycred_get_global_coupon_count( $post->ID );
			if ( empty( $count ) )
				echo '-';

			else {

				$set_type = mycred_get_post_meta( $post->ID, 'type', true );
				$page     = MYCRED_SLUG;

				if ( $set_type != MYCRED_DEFAULT_TYPE_KEY && array_key_exists( $set_type, $this->point_types ) )
					$page .= '_' . $set_type;

				$url = add_query_arg( array( 'page' => $page, 'ref' => 'coupon', 'data' => $post->post_title ), admin_url( 'admin.php' ) );
				echo '<a href="' . esc_url( $url ) . '">' . sprintf( _n( '1 time', '%d times', $count, 'mycred' ), $count ) . '</a>';

			}

		}

		/**
		 * Save Coupon
		 * @since 1.4
		 * @version 1.0.1
		 */
		public function save_coupon( $post_id, $post = NULL ) {

			if ( $post === NULL || ! $this->core->user_is_point_editor() || ! isset( $_POST['mycred_coupon'] ) ) return $post_id;

			foreach ( $_POST['mycred_coupon'] as $meta_key => $meta_value ) {

				$new_value = sanitize_text_field( $meta_value );

				// Make sure we provide a valid date that strtotime() can understand
				if ( $meta_key == 'expires' && $new_value != '' ) {

					// Always expires at midnight
					$check = ( strtotime( $new_value . ' midnight' ) + ( DAY_IN_SECONDS - 1 ) );

					// Thats not good. Date is in the past?
					if ( $check === false || $check < current_time( 'timestamp' ) )
						$new_value = '';

				}

				// No need to update if it's still the same value
				$old_value = mycred_get_post_meta( $post_id, $meta_key, true );
				if ( $new_value != $old_value )
					mycred_update_post_meta( $post_id, $meta_key, $new_value );

			}

		}

		/**
		 * Add to General Settings
		 * @since 1.4
		 * @version 1.1
		 */
		public function after_general_settings( $mycred = NULL ) {

			if ( ! isset( $this->coupons ) )
				$prefs = $this->default_prefs;
			else
				$prefs = mycred_apply_defaults( $this->default_prefs, $this->coupons );

?>
<h4><span class="dashicons dashicons-admin-plugins static"></span><?php _e( 'Coupons', 'mycred' ); ?></h4>
<div class="body" style="display:none;">

	<h3><?php _e( 'Message Templates', 'mycred' ); ?></h3>
	<div class="row">
		<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
			<div class="form-group">
				<label for="<?php echo $this->field_id( 'invalid' ); ?>"><?php _e( 'Invalid Coupon Message', 'mycred' ); ?></label>
				<input type="text" name="<?php echo $this->field_name( 'invalid' ); ?>" id="<?php echo $this->field_id( 'invalid' ); ?>" class="form-control" placeholder="<?php _e( 'Required', 'mycred' ); ?>" value="<?php echo esc_attr( $prefs['invalid'] ); ?>" />
				<p><span class="description"><?php printf( '%s %s', __( 'Message to show when users try to use a coupon that does not exists.', 'mycred' ), $this->available_template_tags( array( 'general' ) ) ); ?></span></p>
			</div>
		</div>
		<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
			<div class="form-group">
				<label for="<?php echo $this->field_id( 'expired' ); ?>"><?php _e( 'Expired Coupon Message', 'mycred' ); ?></label>
				<input type="text" name="<?php echo $this->field_name( 'expired' ); ?>" id="<?php echo $this->field_id( 'expired' ); ?>" class="form-control" placeholder="<?php _e( 'Required', 'mycred' ); ?>" value="<?php echo esc_attr( $prefs['expired'] ); ?>" />
				<p><span class="description"><?php printf( '%s %s', __( 'Message to show when users try to use that has expired.', 'mycred' ), $this->available_template_tags( array( 'general' ) ) ); ?></span></p>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
			<div class="form-group">
				<label for="<?php echo $this->field_id( 'min' ); ?>"><?php _e( 'Minimum Balance Message', 'mycred' ); ?></label>
				<input type="text" name="<?php echo $this->field_name( 'min' ); ?>" id="<?php echo $this->field_id( 'min' ); ?>" class="form-control" placeholder="<?php _e( 'Required', 'mycred' ); ?>" value="<?php echo esc_attr( $prefs['min'] ); ?>" />
				<p><span class="description"><?php printf( '%s %s', __( 'Message to show when a user does not meet the minimum balance requirement. (if used)', 'mycred' ), $this->available_template_tags( array( 'general' ) ) ); ?></span></p>
			</div>
		</div>
		<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
			<div class="form-group">
				<label for="<?php echo $this->field_id( 'max' ); ?>"><?php _e( 'Maximum Balance Message', 'mycred' ); ?></label>
				<input type="text" name="<?php echo $this->field_name( 'max' ); ?>" id="<?php echo $this->field_id( 'max' ); ?>" class="form-control" placeholder="<?php _e( 'Required', 'mycred' ); ?>" value="<?php echo esc_attr( $prefs['max'] ); ?>" />
				<p><span class="description"><?php printf( '%s %s', __( 'Message to show when a user does not meet the maximum balance requirement. (if used)', 'mycred' ), $this->available_template_tags( array( 'general' ) ) ); ?></span></p>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
			<div class="form-group">
				<label for="<?php echo $this->field_id( 'user_limit' ); ?>"><?php _e( 'User Limit Message', 'mycred' ); ?></label>
				<input type="text" name="<?php echo $this->field_name( 'user_limit' ); ?>" id="<?php echo $this->field_id( 'user_limit' ); ?>" class="form-control" placeholder="<?php _e( 'Required', 'mycred' ); ?>" value="<?php echo esc_attr( $prefs['user_limit'] ); ?>" />
				<p><span class="description"><?php printf( '%s %s', __( 'Message to show when the user limit has been reached for the coupon.', 'mycred' ), $this->available_template_tags( array( 'general' ) ) ); ?></span></p>
			</div>
		</div>
		<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
			<div class="form-group">
				<label for="<?php echo $this->field_id( 'excluded' ); ?>"><?php _e( 'Excluded Message', 'mycred' ); ?></label>
				<input type="text" name="<?php echo $this->field_name( 'excluded' ); ?>" id="<?php echo $this->field_id( 'excluded' ); ?>" class="form-control" placeholder="<?php _e( 'Required', 'mycred' ); ?>" value="<?php echo esc_attr( $prefs['excluded'] ); ?>" />
				<p><span class="description"><?php printf( '%s %s', __( 'Message to show when a user is excluded from the point type the coupon gives.', 'mycred' ), $this->available_template_tags( array( 'general' ) ) ); ?></span></p>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
			<div class="form-group">
				<label for="<?php echo $this->field_id( 'success' ); ?>"><?php _e( 'Success Message', 'mycred' ); ?></label>
				<input type="text" name="<?php echo $this->field_name( 'success' ); ?>" id="<?php echo $this->field_id( 'success' ); ?>" class="form-control" placeholder="<?php _e( 'Required', 'mycred' ); ?>" value="<?php echo esc_attr( $prefs['success'] ); ?>" />
				<p><span class="description"><?php printf( '%s %s', __( 'Message to show when a coupon was successfully deposited to a users account.', 'mycred' ), $this->available_template_tags( array( 'general', 'amount' ) ) ); ?></span></p>
			</div>
		</div>
		<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
			<div class="form-group">
				<label for="<?php echo $this->field_id( 'log' ); ?>"><?php _e( 'Log Template', 'mycred' ); ?></label>
				<input type="text" name="<?php echo $this->field_name( 'log' ); ?>" id="<?php echo $this->field_id( 'log' ); ?>" class="form-control" placeholder="<?php _e( 'Required', 'mycred' ); ?>" value="<?php echo esc_attr( $prefs['log'] ); ?>" />
				<p><span class="description"><?php printf( '%s %s', __( 'Log entry for successful coupon redemption. Use %coupon% to show the coupon code.', 'mycred' ), $this->available_template_tags( array( 'general', 'amount' ) ) ); ?></span></p>
			</div>
		</div>
	</div>

</div>
<?php

		}

		/**
		 * Save Settings
		 * @since 1.4
		 * @version 1.0.1
		 */
		public function sanitize_extra_settings( $new_data, $data, $core ) {

			$new_data['coupons']['log']        = sanitize_text_field( $data['coupons']['log'] );
			$new_data['coupons']['invalid']    = sanitize_text_field( $data['coupons']['invalid'] );
			$new_data['coupons']['expired']    = sanitize_text_field( $data['coupons']['expired'] );
			$new_data['coupons']['user_limit'] = sanitize_text_field( $data['coupons']['user_limit'] );
			$new_data['coupons']['min']        = sanitize_text_field( $data['coupons']['min'] );
			$new_data['coupons']['max']        = sanitize_text_field( $data['coupons']['max'] );
			$new_data['coupons']['excluded']   = sanitize_text_field( $data['coupons']['excluded'] );
			$new_data['coupons']['success']    = sanitize_text_field( $data['coupons']['success'] );

			return $new_data;

		}

		/**
		 * Parse Log Entries
		 * @since 1.4
		 * @version 1.0
		 */
		public function parse_log_entry( $content, $log_entry ) {

			return str_replace( '%coupon%', $log_entry->data, $content );

		}

	}
endif;

/**
 * Load Coupons Module
 * @since 1.7
 * @version 1.0
 */
if ( ! function_exists( 'mycred_load_coupons_addon' ) ) :
	function mycred_load_coupons_addon( $modules, $point_types ) {

		$modules['solo']['coupons'] = new myCRED_Coupons_Module();
		$modules['solo']['coupons']->load();

		return $modules;

	}
endif;
add_filter( 'mycred_load_modules', 'mycred_load_coupons_addon', 50, 2 );
