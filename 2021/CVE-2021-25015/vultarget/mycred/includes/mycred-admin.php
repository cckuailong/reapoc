<?php
if ( ! defined( 'myCRED_VERSION' ) ) exit;

/**
 * myCRED_Admin class
 * Manages everything concerning the WordPress admin area.
 * @since 0.1
 * @version 1.2
 */
if ( ! class_exists( 'myCRED_Admin' ) ) {
	class myCRED_Admin {

		public $core;
		public $using_bp = false;

		/**
		 * Construct
		 * @since 0.1
		 * @version 1.0
		 */
		function __construct( $settings = array() ) {
			$this->core = mycred();
		}

		/**
		 * Load
		 * @since 0.1
		 * @version 1.2
		 */
		public function load() {
			// Admin Styling
			add_action( 'admin_head',                 array( $this, 'admin_header' ) );
			add_action( 'admin_notices',              array( $this, 'admin_notices' ) );

			// Custom Columns
			add_filter( 'manage_users_columns',       array( $this, 'custom_user_column' )                );
			add_action( 'manage_users_custom_column', array( $this, 'custom_user_column_content' ), 10, 3 );

			// User Edit
			global $bp;

			// Check if BuddyPress is being used
			if ( is_object( $bp ) && isset( $bp->version ) && version_compare( $bp->version, '2.0', '>=' ) && bp_is_active( 'xprofile' ) )
				$this->using_bp = true;

			// Edit Profile
			if ( ! $this->using_bp )
				add_action( 'edit_user_profile', array( $this, 'user_nav' ) );
			else
				add_action( 'bp_members_admin_profile_nav', array( $this, 'bp_user_nav' ), 10, 2 );

			add_action( 'personal_options',   array( $this, 'show_my_balance' ) );
			add_filter( 'mycred_admin_pages', array( $this, 'edit_profile_menu' ) );
			add_action( 'mycred_init',        array( $this, 'edit_profile_actions' ) );

			// Sortable Column
			add_filter( 'manage_users_sortable_columns', array( $this, 'sortable_points_column' ) );
			add_action( 'pre_user_query',                array( $this, 'sort_by_points' )         );

			// Inline Editing
			add_action( 'wp_ajax_mycred-inline-edit-users-balance', array( $this, 'inline_edit_user_balance' ) );
			add_action( 'in_admin_footer',                          array( $this, 'admin_footer' )             );
		}

		/**
		 * Profile Actions
		 * @since 1.5
		 * @version 1.0
		 */
		public function edit_profile_actions() {

			do_action( 'mycred_edit_profile_action' );

			// Update Balance
			if ( isset( $_POST['mycred_adjust_users_balance_run'] ) && isset( $_POST['mycred_adjust_users_balance'] ) ) {

				extract( $_POST['mycred_adjust_users_balance'] );

				if ( wp_verify_nonce( $token, 'mycred-adjust-balance' ) ) {

					$ctype = sanitize_key( $ctype );
					$mycred = mycred( $ctype );

					// Enforce requirement for log entry
					if ( $mycred->can_edit_creds() && ! $mycred->can_edit_plugin() && $log == '' ) {
						wp_safe_redirect( add_query_arg( array( 'result' => 'log_error' ) ) );
						exit;
					}

					// Make sure we can edit creds
					if ( $mycred->can_edit_creds() ) {

						// Prep
						$user_id = absint( $user_id );
						$amount = $mycred->number( $amount );
						$data = apply_filters( 'mycred_manual_change', array( 'ref_type' => 'user' ), $this );

						// Run
						$mycred->add_creds(
							'manual',
							$user_id,
							$amount,
							$log,
							get_current_user_id(),
							$data,
							$ctype
						);

						wp_safe_redirect( add_query_arg( array( 'result' => 'balance_updated' ) ) );
						exit;

					}

				}

			}

			// Exclude
			elseif ( isset( $_GET['page'] ) && $_GET['page'] == 'mycred-edit-balance' && isset( $_GET['action'] ) && $_GET['action'] == 'exclude' ) {

				$ctype = sanitize_key( $_GET['ctype'] );
				$mycred = mycred( $ctype );

				// Make sure we can edit creds
				if ( $mycred->can_edit_creds() ) {

					// Make sure user is not already excluded
					$user_id = absint( $_GET['user_id'] );
					if ( ! $mycred->exclude_user( $user_id ) ) {

						// Get setttings
						$options = $mycred->core;

						// Get and clean up the exclude list
						$excludes = explode( ',', $options['exclude']['list'] );
						if ( ! empty( $excludes ) ) {
							$_excludes = array();
							foreach ( $excludes as $_user_id ) {
								$_user_id = sanitize_key( $_user_id );
								if ( $_user_id == '' ) continue;
								$_excludes[] = absint( $_user_id );
							}
							$excludes = $_excludes;
						}

						// If user ID is not yet in list
						if ( ! in_array( $user_id, $excludes ) ) {
							$excludes[] = $user_id;
							$options['exclude']['list'] = implode( ',', $excludes );

							$option_id = 'mycred_pref_core';
							if ( $ctype != 'mycred_default' )
								$option_id .= '_' . $ctype;

							mycred_update_option( $option_id, $options );

							// Remove Users balance
							mycred_delete_user_meta( $user_id, $ctype );

							global $wpdb;

							// Delete log entries
							$wpdb->delete(
								$mycred->log_table,
								array( 'user_id' => $user_id, 'ctype' => $ctype ),
								array( '%d', '%s' )
							);

							wp_safe_redirect( add_query_arg( array( 'user_id' => $user_id, 'result' => 'user_excluded' ), admin_url( 'user-edit.php' ) ) );
							exit;
						}

					}

				}

			}

		}

		/**
		 * Admin Notices
		 * @since 1.4
		 * @version 1.1
		 */
		public function admin_notices() {

			// Manual Adjustments
			if ( isset( $_GET['page'] ) && $_GET['page'] == 'mycred-edit-balance' && isset( $_GET['result'] ) ) {

				if ( $_GET['result'] == 'log_error' )
					echo '<div class="error"><p>' . __( 'A log entry is required in order to adjust this users balance', 'mycred' ) . '</p></div>';
				elseif ( $_GET['result'] == 'balance_updated' )
					echo '<div class="updated"><p>' . __( 'Users balance saved', 'mycred' ) . '</p></div>';

			}

			// Exclusions
			elseif ( isset( $_GET['user_id'] ) && isset( $_GET['result'] ) ) {

				if ( $_GET['result'] == 'user_excluded' )
					echo '<div class="updated"><p>' . __( 'Users excluded', 'mycred' ) . '</p></div>';

			}

			if ( get_option( 'mycred_buycred_reset', false ) !== false )
				echo '<div class="error"><p>' . __( 'All buyCRED Payment Gateways have been disabled! Please check your exchange rate settings and update all premium payment gateways!', 'mycred' ) . '</p></div>';

			do_action( 'mycred_admin_notices' );

		}

		/**
		 * Ajax: Inline Edit Users Balance
		 * @since 1.2
		 * @version 1.1
		 */
		public function inline_edit_user_balance() {
			// Security
			check_ajax_referer( 'mycred-update-users-balance', 'token' );

			// Check current user
			$current_user = get_current_user_id();
			if ( ! mycred_is_admin( $current_user ) )
				wp_send_json_error( 'ERROR_1' );

			// Type
			$type = sanitize_text_field( $_POST['type'] );

			$mycred = mycred( $type );

			// User
			$user_id = abs( $_POST['user'] );
			if ( $mycred->exclude_user( $user_id ) )
				wp_send_json_error( array( 'error' => 'ERROR_2', 'message' => __( 'User is excluded', 'mycred' ) ) );

			// Log entry
			$entry = trim( $_POST['entry'] );
			if ( $mycred->can_edit_creds() && ! $mycred->can_edit_plugin() && empty( $entry ) )
				wp_send_json_error( array( 'error' => 'ERROR_3', 'message' => __( 'Log Entry can not be empty', 'mycred' ) ) );

			// Amount
			if ( $_POST['amount'] == 0 || empty( $_POST['amount'] ) )
				wp_send_json_error( array( 'error' => 'ERROR_4', 'message' => __( 'Amount can not be zero', 'mycred' ) ) );
			else
				$amount = $mycred->number( $_POST['amount'] );

			// Data
			$data = apply_filters( 'mycred_manual_change', array( 'ref_type' => 'user' ), $this );

			// Execute
			$result = $mycred->add_creds(
				'manual',
				$user_id,
				$amount,
				$entry,
				$current_user,
				$data,
				$type
			);

			if ( $result !== false )
				wp_send_json_success( $mycred->get_users_cred( $user_id, $type ) );
			else
				wp_send_json_error( array( 'error' => 'ERROR_5', 'message' => __( 'Failed to update this uses balance.', 'mycred' ) ) );
		}

		/**
		 * Admin Header
		 * @since 0.1
		 * @version 1.3
		 */
		public function admin_header() {
			global $wp_version;

			// Old navigation menu
			if ( version_compare( $wp_version, '3.8', '<' ) ) {
				$image = plugins_url( 'assets/images/logo-menu.png', myCRED_THIS ); ?>

<!-- Support for pre 3.8 menus -->
<style type="text/css">
<?php foreach ( $mycred_types as $type => $label ) { if ( $mycred_type == 'mycred_default' ) $name = ''; else $name = '_' . $type; ?>
#adminmenu .toplevel_page_myCRED<?php echo $name; ?> div.wp-menu-image { background-image: url(<?php echo $image; ?>); background-position: 1px -28px; }
#adminmenu .toplevel_page_myCRED<?php echo $name; ?>:hover div.wp-menu-image, 
#adminmenu .toplevel_page_myCRED<?php echo $name; ?>.current div.wp-menu-image, 
#adminmenu .toplevel_page_myCRED<?php echo $name; ?> .wp-menu-open div.wp-menu-image { background-position: 1px 0; }
<?php } ?>
</style>
<?php
			}

			$screen = get_current_screen();
			if ( $screen->id == 'users' ) {
				wp_enqueue_script( 'mycred-inline-edit' );
				wp_enqueue_style( 'mycred-inline-edit' );
			}
		}

		/**
		 * Customize Users Column Headers
		 * @since 0.1
		 * @version 1.1
		 */
		public function custom_user_column( $columns ) {
			global $mycred_types;

			if ( count( $mycred_types ) == 1 )
				$columns['mycred_default'] = $this->core->plural();
			else {
				foreach ( $mycred_types as $type => $label ) {
					if ( $type == 'mycred_default' ) $label = $this->core->plural();
					$columns[ $type ] = $label;
				}
			}

			return $columns;
		}

		/**
		 * Sortable User Column
		 * @since 1.2
		 * @version 1.1
		 */
		public function sortable_points_column( $columns ) {
			$mycred_types = mycred_get_types();

			if ( count( $mycred_types ) == 1 )
				$columns['mycred_default'] = 'mycred_default';
			else {
				foreach ( $mycred_types as $type => $label )
					$columns[ $type ] = $type;
			}

			return $columns;
		}

		/**
		 * Sort by Points
		 * @since 1.2
		 * @version 1.3
		 */
		public function sort_by_points( $query ) {
			if ( ! is_admin() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) || ! function_exists( 'get_current_screen' ) ) return;
			$screen = get_current_screen();
			if ( $screen === NULL || $screen->id != 'users' ) return;

			if ( isset( $query->query_vars['orderby'] ) ) {
				global $wpdb;

				$mycred_types = mycred_get_types();
				$cred_id = $query->query_vars['orderby'];

				$order = 'ASC';
				if ( isset( $query->query_vars['order'] ) )
					$order = $query->query_vars['order'];

				$mycred = $this->core;
				if ( isset( $_REQUEST['ctype'] ) && array_key_exists( $_REQUEST['ctype'], $mycred_types ) )
					$mycred = mycred( $_REQUEST['ctype'] );

				// Sort by only showing users with a particular point type
				if ( $cred_id == 'balance' ) {

					$amount = $mycred->zero();
					if ( isset( $_REQUEST['amount'] ) )
						$amount = $mycred->number( $_REQUEST['amount'] );

					$query->query_from .= "
					LEFT JOIN {$wpdb->usermeta} 
						ON ({$wpdb->users}.ID = {$wpdb->usermeta}.user_id AND {$wpdb->usermeta}.meta_key = '{$mycred->cred_id}')";

					$query->query_where .= " AND meta_value = {$amount}";

				}

				// Sort a particular point type
				elseif ( array_key_exists( $cred_id, $mycred_types ) ) {

					$query->query_from .= "
					LEFT JOIN {$wpdb->usermeta} 
						ON ({$wpdb->users}.ID = {$wpdb->usermeta}.user_id AND {$wpdb->usermeta}.meta_key = '{$cred_id}')";

					$query->query_orderby = "ORDER BY {$wpdb->usermeta}.meta_value+0 {$order} ";

				}

			}
		}

		/**
		 * Customize User Columns Content
		 * @filter 'mycred_user_row_actions'
		 * @since 0.1
		 * @version 1.3.2
		 */
		public function custom_user_column_content( $value, $column_name, $user_id ) {
			global $mycred_types;

			if ( ! array_key_exists( $column_name, $mycred_types ) ) return $value;

			$mycred = mycred( $column_name );

			// User is excluded
			if ( $mycred->exclude_user( $user_id ) === true ) return __( 'Excluded', 'mycred' );

			$user = get_userdata( $user_id );

			// Show balance
			$ubalance = $mycred->get_users_cred( $user_id, $column_name );
			$balance = '<div id="mycred-user-' . $user_id . '-balance-' . $column_name . '">' . $mycred->before . ' <span>' . $mycred->format_number( $ubalance ) . '</span> ' . $mycred->after . '</div>';

			// Show total
			$total = mycred_query_users_total( $user_id, $column_name );
			$balance .= '<small style="display:block;">' . sprintf( __( 'Total: %s', 'mycred' ), $mycred->format_number( $total ) ) . '</small>';

			$page = 'myCRED';
			if ( $column_name != 'mycred_default' )
				$page .= '_' . $column_name;

			// Row actions
			$row = array();
			$row['history'] = '<a href="' . admin_url( 'admin.php?page=' . $page . '&user_id=' . $user_id ) . '">' . __( 'History', 'mycred' ) . '</a>';
			$row['adjust'] = '<a href="javascript:void(0)" class="mycred-open-points-editor" data-userid="' . $user_id . '" data-current="' . $ubalance . '" data-type="' . $column_name . '" data-username="' . $user->display_name . '">' . __( 'Adjust', 'mycred' ) . '</a>';

			$rows = apply_filters( 'mycred_user_row_actions', $row, $user_id, $mycred );
			$balance .= $this->row_actions( $rows );

			return $balance;
		}

		/**
		 * User Row Actions
		 * @since 1.5
		 * @version 1.0
		 */
		public function row_actions( $actions, $always_visible = false ) {
			$action_count = count( $actions );
			$i = 0;

			if ( !$action_count )
				return '';

			$out = '<div class="' . ( $always_visible ? 'row-actions-visible' : 'row-actions' ) . '">';
			foreach ( $actions as $action => $link ) {
				++$i;
				( $i == $action_count ) ? $sep = '' : $sep = ' | ';
				$out .= "<span class='$action'>$link$sep</span>";
			}
			$out .= '</div>';

			return $out;
		}

		/**
		 * Add Admin Page
		 * @since 1.5
		 * @version 1.0
		 */
		public function edit_profile_menu( $pages = array() ) {
			$pages[] = add_users_page(
				__( 'Edit Balance', 'mycred' ),
				__( 'Edit Balance', 'mycred' ),
				'read',
				'mycred-edit-balance',
				array( $this, 'edit_profile_screen' )
			);
			return $pages;
		}

		/**
		 * User Nav
		 * @since 1.5
		 * @version 1.0
		 */
		public function user_nav( $user, $current = NULL ) {
			$types = mycred_get_types();

			$tabs = array();
			$tabs[] = array(
				'label'   => __( 'Profile', 'mycred' ),
				'url'     => add_query_arg( array( 'user_id' => $user->ID ), admin_url( 'user-edit.php' ) ),
				'classes' => ( $current === NULL ) ? 'nav-tab nav-tab-active' : 'nav-tab'
			);

			if ( $this->using_bp )
				$tabs[] = array(
					'label'   => __( 'Extended Profile', 'mycred' ),
					'url'     => add_query_arg( array( 'page' => 'bp-profile-edit', 'user_id' => $user->ID ), admin_url( 'users.php' ) ),
					'classes' => 'nav-tab'
				);

			foreach ( $types as $type => $label ) {
				$mycred = mycred( $type );
				if ( $mycred->exclude_user( $user->ID ) ) continue;

				$classes = 'nav-tab';
				if ( isset( $_GET['ctype'] ) && $_GET['ctype'] == $type ) $classes .= ' nav-tab-active';

				$tabs[] = array(
					'label'   => $mycred->plural(),
					'url'     => add_query_arg( array( 'page' => 'mycred-edit-balance', 'user_id' => $user->ID, 'ctype' => $type ), admin_url( 'users.php' ) ),
					'classes' => $classes
				);
			}

			$tabs = apply_filters( 'mycred_edit_profile_tabs', $tabs, $user, false );

?>
<style type="text/css">
div#edit-balance-page.wrap form#your-profile, div#profile-page.wrap form#your-profile { position:relative; }
div#edit-balance-page.wrap form#your-profile h3:first-of-type { margin-top:3em; }
div#profile-page.wrap form#your-profile h3:first-of-type { margin-top:6em; }
div#edit-balance-page.wrap form#your-profile ul#profile-nav { border-bottom:solid 1px #ccc; width:100%; }
div#profile-page.wrap form#your-profile ul#profile-nav { position:absolute; top:-6em; border-bottom:solid 1px #ccc; width:100%; }
div#edit-balance-page ul#profile-nav { border-bottom:solid 1px #ccc; width:100%; margin-top:1em; margin-bottom:1em; padding:1em 0; padding-bottom: 0; height:2.4em; }
ul#profile-nav li { margin-left:0.4em; float:left;font-weight: bold;font-size: 15px;line-height: 24px;}
ul#profile-nav li a {text-decoration: none;color:#888;}
ul#profile-nav li a:hover, ul#profile-nav li.nav-tab-active a {text-decoration: none;color:#000; }
</style>
<ul id="profile-nav" class="nav-tab-wrapper">

	<?php foreach ( $tabs as $tab ) echo '<li class="' . $tab['classes'] . '"><a href="' . $tab['url'] . '">' . $tab['label'] . '</a></li>'; ?>

</ul>
<?php
		}

		/**
		 * BuddyPress User Nav
		 * @since 1.5
		 * @version 1.0
		 */
		public function bp_user_nav( $active, $user ) {
			$types = mycred_get_types();

			$tabs = array();
			foreach ( $types as $type => $label ) {
				$mycred = mycred( $type );
				if ( $mycred->exclude_user( $user->ID ) ) continue;

				$tabs[] = array(
					'label'   => $mycred->plural(),
					'url'     => add_query_arg( array( 'page' => 'mycred-edit-balance', 'user_id' => $user->ID, 'ctype' => $type ), admin_url( 'users.php' ) ),
					'classes' => 'nav-tab'
				);
			}

			$tabs = apply_filters( 'mycred_edit_profile_tabs', $tabs, $user, true );

			if ( ! empty( $tabs ) )
				foreach ( $tabs as $tab ) echo '<li class="' . $tab['classes'] . '"><a href="' . $tab['url'] . '">' . $tab['label'] . '</a></li>';
		}

		/**
		 * Edit Profile Screen
		 * @since 1.5
		 * @version 1.0
		 */
		public function edit_profile_screen() {
			if ( ! isset( $_GET['user_id'] ) ) return;

			$user_id = absint( $_GET['user_id'] );

			if ( ! isset( $_GET['ctype'] ) )
				$type = 'mycred_default';
			else
				$type = sanitize_key( $_GET['ctype'] );

			$mycred = mycred( $type );

			// Security
			if ( ! $mycred->can_edit_creds() )
				wp_die( __( 'Access Denied', 'mycred' ) );

			// User is excluded
			if ( $mycred->exclude_user( $user_id ) )
				wp_die( sprintf( __( 'This user is excluded from using %s', 'mycred' ), mycred_label() ) );

			$user = get_userdata( $user_id );
			$balance = $mycred->get_users_balance( $user_id );

			if ( $type == 'mycred_default' )
				$log_slug = 'myCRED';
			else
				$log_slug = 'myCRED_' . $type;

			$history_url = add_query_arg( array( 'page' => $log_slug, 'user_id' => $user->ID ), admin_url( 'admin.php' ) );
			$exclude_url = add_query_arg( array( 'action' => 'exclude' ) ) ?>

<style type="text/css">
div#edit-balance-page table.table { width: 100%; margin-top: 24px; }
div#edit-balance-page table.table th { text-align: left; }
div#edit-balance-page table.table td { width: 33%; font-size: 24px; line-height: 48px; }
div#edit-balance-page table tr td table tr td { vertical-align: top; }
div#edit-balance-page table.form-table { border-top: 1px solid #ccc; }
div#edit-balance-page.wrap form#your-profile h3 { margin-top: 3em; }
</style>
<div class="wrap" id="edit-balance-page">
	<h2><?php
	_e( 'Edit User', 'mycred' );
	if ( current_user_can( 'create_users' ) ) { ?>
	<a href="user-new.php" class="add-new-h2"><?php echo esc_html_x( 'Add New', 'user', 'mycred' ); ?></a>
<?php } elseif ( is_multisite() && current_user_can( 'promote_users' ) ) { ?>
	<a href="user-new.php" class="add-new-h2"><?php echo esc_html_x( 'Add Existing', 'user', 'mycred' ); ?></a>
<?php }
	?></h2>
	<form id="your-profile" action="" method="post">
		<?php echo $this->user_nav( $user, $type ); ?>

		<div class="clear clearfix"></div>
		<table class="table">
			<thead>
				<tr>
					<th><?php _e( 'Current Balance', 'mycred' ); ?></th>
					<th><?php printf( __( 'Total %s Accumulated', 'mycred' ), $mycred->plural() ); ?></th>
					<th><?php printf( __( 'Total %s Spent', 'mycred' ), $mycred->plural() ); ?></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><?php echo $mycred->format_creds( $balance ); ?></td>
					<td><?php echo $mycred->format_creds( mycred_get_users_total( $user->ID, $type ) ); ?></td>
					<td><?php echo $mycred->format_creds( $this->get_users_total_spent( $user->ID, $type ) ); ?></td>
				</tr>
			</tbody>
		</table>
		<a href="<?php echo $history_url; ?>" class="button button-secondary"><?php _e( 'View History', 'mycred' ); ?></a>
		<a href="<?php echo $exclude_url; ?>" class="button button-primary" id="mycred-exclude-this-user"><?php _e( 'Exclude User', 'mycred' ); ?></a>

		<?php do_action( 'mycred_before_edit_profile', $user, $type ); ?>

		<h3><?php _e( 'Adjust Balance', 'mycred' ); ?></h3>
		<?php $this->adjust_users_balance( $user ); ?>

		<?php do_action( 'mycred_edit_profile', $user, $type ); ?>

	</form>
	<script type="text/javascript">
jQuery(function($) {
	$( 'a#mycred-exclude-this-user' ).click(function(){
		if ( ! confirm( '<?php _e( 'Warning! Excluding this user will result in their balance being deleted along with any entries currently in your log! This can not be undone!', 'mycred' ); ?>' ) )
			return false;
	});
});
	</script>
</div>
<?php
		}

		/**
		 * Get Users Total Accumulated
		 * @since 1.5
		 * @version 1.0
		 */
		public function get_users_total_accumulated( $user_id, $type ) {
			global $wpdb;

			return $wpdb->get_var( $wpdb->prepare( "
				SELECT SUM( creds ) 
				FROM {$this->core->log_table} 
				WHERE ctype = %s 
				AND user_id = %d 
				AND creds > 0;", $type, $user_id ) );
		}

		/**
		 * Get Users Total Spending
		 * @since 1.5
		 * @version 1.0
		 */
		public function get_users_total_spent( $user_id, $type ) {
			global $wpdb;

			return $wpdb->get_var( $wpdb->prepare( "
				SELECT SUM( creds ) 
				FROM {$this->core->log_table} 
				WHERE ctype = %s 
				AND user_id = %d 
				AND creds < 0;", $type, $user_id ) );
		}

		/**
		 * Insert Ballance into Profile
		 * @since 0.1
		 * @version 1.1
		 */
		public function show_my_balance( $user ) {
			$user_id = $user->ID;
			$mycred_types = mycred_get_types();

			foreach ( $mycred_types as $type => $label ) {
				$mycred = mycred( $type );
				if ( $mycred->exclude_user( $user_id ) ) continue;

				$balance = $mycred->get_users_cred( $user_id, $type );
				$balance = $mycred->format_creds( $balance ); ?>

<tr>
	<th scope="row"><?php echo $mycred->template_tags_general( __( '%singular% balance', 'mycred' ) ); ?></th>
	<td><h2 style="margin:0;padding:0;"><?php echo $balance; ?></h2></td>
</tr>
<?php
			}
		}

		/**
		 * Adjust Users Balance
		 * @since 0.1
		 * @version 1.2
		 */
		public function adjust_users_balance( $user ) {
			if ( ! isset( $_GET['ctype'] ) )
				$type = 'mycred_default';
			else
				$type = sanitize_key( $_GET['ctype'] );

			$mycred = mycred( $type );

			if ( $mycred->can_edit_creds() && ! $mycred->can_edit_plugin() )
				$req = '(<strong>' . __( 'required', 'mycred' ) . '</strong>)'; 
			else
				$req = '(' . __( 'optional', 'mycred' ) . ')'; ?>

<table class="form-table">
	<tr>
		<th scope="row"><label for="myCRED-manual-add-points"><?php _e( 'Amount', 'mycred' ) ?></label></th>
		<td id="myCRED-adjust-users-points">
			<input type="text" name="mycred_adjust_users_balance[amount]" id="myCRED-manual-add-points" value="<?php echo $mycred->zero(); ?>" size="8" />
			<input type="hidden" name="mycred_adjust_users_balance[ctype]" value="<?php echo $type; ?>" />
			<input type="hidden" name="mycred_adjust_users_balance[user_id]" value="<?php echo $user->ID; ?>" />
			<input type="hidden" name="mycred_adjust_users_balance[token]" value="<?php echo wp_create_nonce( 'mycred-adjust-balance' ); ?>" />
		</td>
	</tr>
	<tr>
		<th scope="row"><label for="myCRED-manual-add-description"><?php _e( 'Log Entry', 'mycred' ); ?> <?php echo $req; ?></label></th>
		<td>
			<input type="text" name="mycred_adjust_users_balance[log]" id="myCRED-manual-add-description" value="" class="regular-text" /><br />
			<span class="description"><?php echo $mycred->available_template_tags( array( 'general' ) ); ?></span><br /><br />
			<?php submit_button( __( 'Update Balance', 'mycred' ), 'primary medium', 'mycred_adjust_users_balance_run', false ); ?>
		</td>
	</tr>
</table>
<?php
		}

		/**
		 * Admin Footer
		 * Inserts the Inline Edit Form modal.
		 * @since 1.2
		 * @version 1.2
		 */
		public function admin_footer() {
			$screen = get_current_screen();
			if ( $screen->id != 'users' ) return;

			if ( $this->core->can_edit_creds() && ! $this->core->can_edit_plugin() )
				$req = '(<strong>' . __( 'required', 'mycred' ) . '</strong>)'; 
			else
				$req = '(' . __( 'optional', 'mycred' ) . ')';

			ob_start(); ?>

<div id="edit-mycred-balance" style="display: none;">
	<div class="mycred-adjustment-form">
		<p class="row inline" style="width: 20%"><label><?php _e( 'ID', 'mycred' ); ?>:</label><span id="mycred-userid"></span></p>
		<p class="row inline" style="width: 40%"><label><?php _e( 'User', 'mycred' ); ?>:</label><span id="mycred-username"></span></p>
		<p class="row inline" style="width: 40%"><label><?php _e( 'Current Balance', 'mycred' ); ?>:</label> <span id="mycred-current"></span></p>
		<div class="clear"></div>
		<input type="hidden" name="mycred_update_users_balance[token]" id="mycred-update-users-balance-token" value="<?php echo wp_create_nonce( 'mycred-update-users-balance' ); ?>" />
		<input type="hidden" name="mycred_update_users_balance[type]" id="mycred-update-users-balance-type" value="" />
		<p class="row"><label for="mycred-update-users-balance-amount"><?php _e( 'Amount', 'mycred' ); ?>:</label><input type="text" name="mycred_update_users_balance[amount]" id="mycred-update-users-balance-amount" value="" /><br /><span class="description"><?php _e( 'A positive or negative value', 'mycred' ); ?>.</span></p>
		<p class="row"><label for="mycred-update-users-balance-entry"><?php _e( 'Log Entry', 'mycred' ); ?>:</label><input type="text" name="mycred_update_users_balance[entry]" id="mycred-update-users-balance-entry" value="" /><br /><span class="description"><?php echo $req; ?></span></p>
		<p class="row"><input type="button" name="mycred-update-users-balance-submit" id="mycred-update-users-balance-submit" value="<?php _e( 'Update Balance', 'mycred' ); ?>" class="button button-primary button-large" /></p>
		<div class="clear"></div>
	</div>
	<div class="clear"></div>
</div>
<?php

			$content = ob_get_contents();
			ob_end_clean();

			echo apply_filters( 'mycred_admin_inline_editor', $content );

		}
	}
}
?>