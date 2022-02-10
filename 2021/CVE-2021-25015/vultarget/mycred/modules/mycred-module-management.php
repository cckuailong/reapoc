<?php
if ( ! defined( 'myCRED_VERSION' ) ) exit;

/**
 * myCRED_Management_Module class
 * This module is responsible for all point management in the WordPress admin areas Users section.
 * Replaces the mycred-admin.php file.
 * @since 1.7
 * @version 1.0.1
 */
if ( ! class_exists( 'myCRED_Management_Module' ) ) :
	class myCRED_Management_Module extends myCRED_Module {

		public $manual_reference = 'manual';

		/**
		 * Construct
		 */
		public function __construct( $type = MYCRED_DEFAULT_TYPE_KEY ) {

			parent::__construct( 'myCRED_Management_Module', array(
				'module_name' => 'management',
				'accordion'   => false
			), $type );

		}

		/**
		 * Module Init
		 * @since 1.0
		 * @version 1.0.1
		 */
		public function module_init() {

			// Admin Styling
			add_action( 'admin_head',                           array( $this, 'admin_header' ) );

			// Custom Columns
			add_filter( 'manage_users_columns',                 array( $this, 'custom_user_column' ) );
			add_filter( 'manage_users_custom_column',           array( $this, 'custom_user_column_content' ), 10, 3 );

			// Sortable Column
			add_filter( 'manage_users_sortable_columns',        array( $this, 'sortable_points_column' ) );
			add_action( 'pre_user_query',                       array( $this, 'sort_by_points' ) );

			// Edit User
			add_action( 'personal_options',                     array( $this, 'show_my_balance' ) );
			add_action( 'personal_options_update',              array( $this, 'save_balance_adjustments' ), 40 );
			add_action( 'edit_user_profile_update',             array( $this, 'save_balance_adjustments' ), 40 );

			// Editor
			add_action( 'wp_ajax_mycred-admin-editor',          array( $this, 'ajax_editor_balance_update' ) );
			add_action( 'wp_ajax_mycred-admin-recent-activity', array( $this, 'ajax_get_recent_activity' ) );
			add_action( 'in_admin_footer',                      array( $this, 'admin_footer' ) );

			$this->manual_reference = apply_filters( 'mycred_editor_selected_ref', $this->manual_reference, $this );

		}

		/**
		 * AJAX: Update Balance
		 * @since 1.7
		 * @version 1.1
		 */
		public function ajax_editor_balance_update() {

			// Security
			check_ajax_referer( 'mycred-editor-token', 'token' );

			// Check current user
			$current_user    = get_current_user_id();
			if ( ! mycred_is_admin( $current_user ) )
				wp_send_json_error( 'ERROR_1' );

			// Get the form
			parse_str( $_POST['form'], $post );
			unset( $_POST );

			$submitted       = $post['mycred_manage_balance'];

			// Prep submission
			$type            = sanitize_text_field( $submitted['type'] );
			$user_id         = absint( $submitted['user_id'] );
			$amount          = sanitize_text_field( $submitted['amount'] );
			$reference       = sanitize_key( $submitted['ref'] );
			$custom_ref      = sanitize_key( $submitted['custom'] );
			$entry           = wp_kses_post( $submitted['entry'] );

			if ( ! mycred_point_type_exists( $type ) || $type == MYCRED_DEFAULT_TYPE_KEY ) {
				$type   = MYCRED_DEFAULT_TYPE_KEY;
				$mycred = $this->core;
			}
			else {
				$mycred = mycred( $type );
			}

			$result          = array(
				'current'       => 0,
				'total'         => 0,
				'decimals'      => (int) $mycred->format['decimals'],
				'label'         => esc_attr__( 'Update Balance', 'mycred' ),
				'results'       => '',
				'user_id'       => $user_id,
				'amount'        => $amount,
				'reference'     => $reference,
				'custom'        => $custom_ref,
				'entry'         => $entry,
				'type'          => $type
			);

			// Make sure we are not attempting to adjust the balance of someone who is excluded
			if ( $mycred->exclude_user( $user_id ) ) {

				$result['results'] = __( 'User is excluded', 'mycred' );
				wp_send_json_error( $result );

			}

			// Non admins must give a log entry
			if ( $mycred->user_is_point_editor() && ! $mycred->user_is_point_admin() && strlen( $entry ) == 0 ) {

				$result['results'] = __( 'Log Entry can not be empty', 'mycred' );
				wp_send_json_error( $result );

			}

			// Amount can not be zero
			if ( $amount == '' || $mycred->number( $amount ) == $mycred->zero() ) {

				$result['results'] = __( 'Amount can not be zero', 'mycred' );
				wp_send_json_error( $result );

			}

			// Format amount
			$amount          = $mycred->number( $amount );

			// Reference
			$all_references  = mycred_get_all_references();
			if ( $reference == 'mycred_custom' ) {

				if ( $custom_ref != '' )
					$reference = $custom_ref;
				else
					$reference = $this->manual_reference;

			}
			elseif ( $reference == '' || ! array_key_exists( $reference, $all_references ) )
				$reference = $this->manual_reference;

			$current_balance = $mycred->get_users_balance( $user_id, $type );

			// Data
			$data            = apply_filters( 'mycred_manual_change', array( 'ref_type' => 'user' ), $this );

			// Just a balance change without a log entry
			if ( strlen( $entry ) == 0 ) {

				$success     = true;
				$new_balance = $mycred->update_users_balance( $user_id, $amount, $type );

			}

			// Balance change with a log entry
			else {

				$success = $mycred->add_creds(
					$reference,
					$user_id,
					$amount,
					$entry,
					get_current_user_id(),
					$data,
					$type
				);
				$new_balance = $current_balance + $amount;

			}

			if ( $success ) {

				$result['current'] = $new_balance;
				$result['total']   = mycred_query_users_total( $user_id, $type );
				$result['results'] = __( 'Balance successfully updated', 'mycred' );

			}
			else {

				$result['results'] = __( 'Request declined', 'mycred' );

			}

			do_action( 'mycred_finish_without_log_entry', $result );

			wp_send_json_success( $result );
		}

		/**
		 * AJAX: Recent Activity
		 * @since 1.7
		 * @version 1.0.1
		 */
		public function ajax_get_recent_activity() {

			// Security
			check_ajax_referer( 'mycred-get-ledger', 'token' );

			$user_id = absint( $_POST['userid'] );
			$type    = sanitize_key( $_POST['type'] );

			if ( ! mycred_point_type_exists( $type ) )
				$type = MYCRED_DEFAULT_TYPE_KEY;

			$ledger  = new myCRED_Query_Log( array(
				'user_id' => $user_id,
				'number'  => 5,
				'ctype'   => $type
			) );

			if ( empty( $ledger->results ) ) {

?>
<div class="row last">
	<div class="col-xs-12">
		<p><?php _e( 'No recent activity found.', 'mycred' ); ?></p>
	</div>
</div>
<?php

			}

			else {

?>
<div class="row ledger header">
	<div class="col-xs-4"><strong><?php _e( 'Date', 'mycred' ); ?></strong></div>
	<div class="col-xs-4"><strong><?php _e( 'Time', 'mycred' ); ?></strong></div>
	<div class="col-xs-4"><strong><?php _e( 'Reference', 'mycred' ); ?></strong></div>
	<div class="col-xs-12"><strong><?php _e( 'Entry', 'mycred' ); ?></strong></div>
</div>
<?php

				$date_format = get_option( 'date_format' );
				$time_format = get_option( 'time_format' );
				$references  = mycred_get_all_references();

				foreach ( $ledger->results as $log_entry ) {

					$date = date( $date_format, $log_entry->time );
					$time = date( $time_format, $log_entry->time );

					if ( array_key_exists( $log_entry->ref, $references ) )
						$ref = $references[ $log_entry->ref ];
					else
						$ref = ucwords( strtolower( str_replace( '_', ' ', $log_entry->ref ) ) );

					$entry = $this->core->parse_template_tags( $log_entry->entry, $log_entry );

?>
<div class="row ledger">
	<div class="col-xs-4"><?php echo $date; ?></div>
	<div class="col-xs-4"><?php echo $time; ?></div>
	<div class="col-xs-4"><?php echo $ref; ?></div>
	<div class="col-xs-12"><?php echo $entry; ?></div>
</div>
<?php

				}

			}

			if ( $ledger->num_rows > 5 ) {

				$page = MYCRED_SLUG;
				if ( $type != MYCRED_DEFAULT_TYPE_KEY )
					$page .= '_' . $type;

?>
<div class="row ledger">
	<div class="col-xs-12">
		<div style="text-align:center; padding: 12px 0;"><a href="<?php echo esc_url( admin_url( 'admin.php?page=' . $page . '&user=' . $user_id ) ); ?>" style="width: auto !important;" class="button button-large button-secondary"><?php _e( 'View complete history', 'mycred' ); ?></a></div>
	</div>
</div>
<?php

			}

			die;

		}

		/**
		 * Enqueue Scripts & Styles
		 * @since 0.1
		 * @version 1.4.1
		 */
		public function admin_header() {

			$screen = get_current_screen();
			if ( ! isset( $screen->id ) ) return;

			if ( $screen->id == 'users' ) {

				wp_enqueue_style( 'mycred-bootstrap-grid' );
				wp_enqueue_style( 'mycred-edit-balance' );

				wp_localize_script(
					'mycred-edit-balance',
					'myCREDedit',
					array(
						'ajaxurl'     => admin_url( 'admin-ajax.php' ),
						'token'       => wp_create_nonce( 'mycred-editor-token' ),
						'ledgertoken' => wp_create_nonce( 'mycred-get-ledger' ),
						'defaulttype' => MYCRED_DEFAULT_TYPE_KEY,
						'title'       => esc_attr__( 'Edit Users Balance', 'mycred' ),
						'close'       => esc_attr__( 'Close', 'mycred' ),
						'working'     => esc_attr__( 'Processing...', 'mycred' ),
						'ref'         => $this->manual_reference,
						'loading'     => '<div id="mycred-processing"><div class="loading-indicator"></div></div>'
					)
				);
				wp_enqueue_script( 'mycred-edit-balance' );

			}

			elseif ( $screen->id == 'user-edit' ) {

				wp_enqueue_style( 'mycred-bootstrap-grid' );
				wp_enqueue_style( 'mycred-edit-balance' );

			}

			elseif ( $screen->id == 'profile' ) {

				wp_enqueue_style( 'mycred-bootstrap-grid' );
				wp_enqueue_style( 'mycred-edit-balance' );

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
				$columns[ MYCRED_DEFAULT_TYPE_KEY ] = $this->core->plural();

			else {

				foreach ( $mycred_types as $type => $label ) {
					if ( $type == MYCRED_DEFAULT_TYPE_KEY ) $label = $this->core->plural();
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
				$columns[ MYCRED_DEFAULT_TYPE_KEY ] = MYCRED_DEFAULT_TYPE_KEY;

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
				$cred_id      = $query->query_vars['orderby'];

				$order        = 'ASC';
				if ( isset( $query->query_vars['order'] ) )
					$order = $query->query_vars['order'];

				$mycred       = $this->core;
				if ( isset( $_REQUEST['ctype'] ) && array_key_exists( $_REQUEST['ctype'], $mycred_types ) )
					$mycred = mycred( $_REQUEST['ctype'] );

				// Sort by only showing users with a particular point type
				if ( $cred_id == 'balance' ) {

					$amount = $mycred->zero();
					if ( isset( $_REQUEST['amount'] ) )
						$amount = $mycred->number( $_REQUEST['amount'] );

					$query->query_from  .= " LEFT JOIN {$wpdb->usermeta} mycred ON ({$wpdb->users}.ID = mycred.user_id AND mycred.meta_key = '{$mycred->cred_id}')";
					$query->query_where .= " AND mycred.meta_value = {$amount}";

				}

				// Sort a particular point type
				elseif ( array_key_exists( $cred_id, $mycred_types ) ) {

					$query->query_from   .= " LEFT JOIN {$wpdb->usermeta} mycred ON ({$wpdb->users}.ID = mycred.user_id AND mycred.meta_key = '{$cred_id}')";
					$query->query_orderby = "ORDER BY mycred.meta_value+0 {$order} ";

				}

			}

		}

		/**
		 * Customize User Columns Content
		 * @filter 'mycred_user_row_actions'
		 * @since 0.1
		 * @version 1.3.4
		 */
		public function custom_user_column_content( $value, $column_name, $user_id ) {

			global $mycred_types;

			if ( ! array_key_exists( $column_name, $mycred_types ) ) return $value;

			$mycred   = mycred( $column_name );

			// User is excluded
			if ( $mycred->exclude_user( $user_id ) === true ) return __( 'Excluded', 'mycred' );

			$user     = get_userdata( $user_id );

			// Show balance
			$ubalance = $mycred->get_users_balance( $user_id, $column_name );
			$balance  = '<div id="mycred-user-' . $user_id . '-balance-' . $column_name . '">' . $mycred->before . ' <span>' . $mycred->format_number( $ubalance ) . '</span> ' . $mycred->after . '</div>';

			// Show total
			$total    = mycred_query_users_total( $user_id, $column_name );
			$balance .= '<div id="mycred-user-' . $user_id . '-balance-' . $column_name . '"><small style="display:block;">' . sprintf( '<strong>%s</strong>: <span>%s</span>', __( 'Total', 'mycred' ), $mycred->format_number( $total ) ) . '</small></div>';

			$balance  = apply_filters( 'mycred_users_balance_column', $balance, $user_id, $column_name );

			$page     = MYCRED_SLUG;
			if ( $column_name != MYCRED_DEFAULT_TYPE_KEY )
				$page .= '_' . $column_name;

			// Row actions
			$row            = array();
			$row['history'] = '<a href="' . esc_url( admin_url( 'admin.php?page=' . $page . '&user=' . $user_id ) ) . '">' . __( 'History', 'mycred' ) . '</a>';
			$row['adjust']  = '<a href="javascript:void(0)" class="mycred-open-points-editor" data-userid="' . $user_id . '" data-current="' . $mycred->format_number( $ubalance ) . '" data-total="' . $mycred->format_number( $total ) . '" data-type="' . $column_name . '" data-username="' . $user->display_name . '" data-zero="' . $mycred->zero() . '">' . __( 'Adjust', 'mycred' ) . '</a>';

			$rows     = apply_filters( 'mycred_user_row_actions', $row, $user_id, $mycred );
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
			$i            = 0;

			if ( ! $action_count )
				return '';

			$out  = '<div class="' . ( $always_visible ? 'row-actions-visible' : 'row-actions' ) . '">';
			foreach ( $actions as $action => $link ) {
				++$i;
				( $i == $action_count ) ? $sep = '' : $sep = ' | ';
				$out .= "<span class='$action'>$link$sep</span>";
			}
			$out .= '</div>';

			return $out;

		}

		/**
		 * Insert Ballance into Profile
		 * @since 0.1
		 * @version 1.2.1
		 */
		public function show_my_balance( $user ) {

			$user_id      = $user->ID;
			$editor_id    = get_current_user_id();
			$mycred_types = mycred_get_types( true );
			$balances     = array();
			$load_script  = false;

			foreach ( $mycred_types as $point_type_key => $label ) {

				$mycred      = mycred( $point_type_key );

				$row = array( 'name' => '', 'excluded' => true, 'raw' => '', 'formatted' => '', 'can_edit' => false );

				$row['name'] = $mycred->plural();

				if ( ! $mycred->exclude_user( $user_id ) ) {

					$balance          = $mycred->get_users_balance( $user_id );

					$row['excluded']  = false;
					$row['raw']       = $balance;
					$row['formatted'] = $mycred->format_creds( $balance );
					$row['can_edit']  = ( ( $mycred->user_is_point_editor( $editor_id ) ) ? true : false );

					if ( $row['can_edit'] === true && $load_script === false )
						$load_script = true;

				}

				$balances[ $point_type_key ] = $row;

			}

			if ( empty( $balances ) ) return;

?>
</table>
<hr />
<div id="mycred-edit-user-wrapper">
	<table class="form-table mycred-inline-table">
		<tr>
			<th scope="row"><?php _e( 'Balances', 'mycred' ); ?></th>
			<td>
				<fieldset id="mycred-badge-list" class="badge-list">
					<legend class="screen-reader-text"><span><?php _e( 'Balance', 'mycred' ); ?></span></legend>
<?php

			// Loop through each point type
			foreach ( $balances as $point_type => $data ) {

				// This user is excluded from this point type
				if ( $data['excluded'] ) {

?>
					<div class="mycred-wrapper balance-wrapper disabled-option color-option">
						<div><?php echo $data['name']; ?></div>
						<div class="balance-row">
							<div class="balance-view"><?php _e( 'Excluded', 'mycred' ); ?></div>
							<div class="balance-edit">&nbsp;</div>
						</div>
<?php

				}

				// Eligeble user
				else {

?>
					<div class="mycred-wrapper balance-wrapper color-option selected">
						<?php if ( $data['can_edit'] ) : ?><div class="toggle-mycred-balance-editor"><a href="javascript:void(0);" data-type="<?php echo $point_type; ?>" data-view="<?php _e( 'Edit', 'mycred' ); ?>" data-edit="<?php _e( 'Cancel', 'mycred' ); ?>"><?php _e( 'Edit', 'mycred' ); ?></a></div><?php endif; ?>
						<div><?php echo $data['name']; ?></div>
						<div class="balance-row" id="mycred-balance-<?php echo $point_type; ?>">
							<div class="balance-view"><?php echo $data['formatted']; ?></div>
							<?php if ( $data['can_edit'] ) : ?><div class="balance-edit"><input type="text" name="mycred_new_balance[<?php echo $point_type; ?>]" value="" placeholder="<?php echo $data['raw']; ?>" size="12" /></div><?php endif; ?>
						</div>
<?php

				}

?>
						<?php do_action( 'mycred_user_edit_after_balance', $point_type, $user, $data ); ?>

					</div>
<?php

			}

?>
				</fieldset>
			</td>
		</tr>
	</table>
	<hr />
<?php

			foreach ( $balances as $point_type => $data )
				do_action( 'mycred_user_edit_after_' . $point_type, $user );

			do_action( 'mycred_user_edit_after_balances', $user );

			// No need to load the script if we can't edit balances
			if ( $load_script ) {

?>
</div>
<script type="text/javascript">
jQuery(function($){

	$( '.toggle-mycred-balance-editor a' ).click(function(e){

		e.preventDefault();
		$(this).blur();

		var togglebutton = $(this);
		var pointtype    = togglebutton.data( 'type' );
		var balancebox   = $( '#mycred-balance-' + pointtype );

		

		// View mode > Edit Mode
		if ( ! balancebox.hasClass( 'edit' ) ) {

			togglebutton.text( togglebutton.data( 'edit' ) );

			$( '#mycred-balance-' + pointtype + ' .balance-view' ).hide();
			$( '#mycred-balance-' + pointtype + ' .balance-edit' ).show();

			balancebox.addClass( 'edit' );

		}

		// Edit mode > View mode
		else {

			togglebutton.text( togglebutton.data( 'view' ) );

			$( '#mycred-balance-' + pointtype + ' .balance-view' ).show();
			$( '#mycred-balance-' + pointtype + ' .balance-edit' ).hide();
			$( '#mycred-balance-' + pointtype + ' .balance-edit input' ).val( '' );

			balancebox.removeClass( 'edit' );

		}

	});

});
</script>
<?php

			}

?>
<table class="form-table">
<?php

		}

		/**
		 * Save Balance Changes
		 * @since 1.7.3
		 * @version 1.0
		 */
		public function save_balance_adjustments( $user_id ) {

			$editor_id = get_current_user_id();

			if ( isset( $_POST['mycred_new_balance'] ) && is_array( $_POST['mycred_new_balance'] ) && ! empty( $_POST['mycred_new_balance'] ) ) {

				foreach ( $_POST['mycred_new_balance'] as $point_type => $balance ) {

					$point_type = sanitize_key( $point_type );
					if ( ! mycred_point_type_exists( $point_type ) ) continue;

					$mycred = mycred( $point_type );

					// User can not be excluded and we must be allowed to change balances
					if ( ! $mycred->exclude_user( $user_id ) && $mycred->user_is_point_editor( $editor_id ) ) {

						$balance = sanitize_text_field( $balance );

						// Empty = no changes
						if ( strlen( $balance ) > 0 ) {
							$mycred->set_users_balance( $user_id, $balance );
						}

					}

				}

			}

		}

		/**
		 * Admin Footer
		 * Inserts the Inline Edit Form modal.
		 * @since 1.2
		 * @version 1.3.1
		 */
		public function admin_footer() {

			// Security
			if ( ! $this->core->user_is_point_editor() ) return;

			$screen = get_current_screen();

			if ( $screen->id == 'users' ) {

				global $mycred;

				$references = mycred_get_all_references();
				$name       = mycred_label( true );

				ob_start();

?>
<div id="edit-mycred-balance" style="display: none;">
	<?php if ( $name == 'myCRED' ) : ?><img id="mycred-token-sitting" class="hidden-sm hidden-xs" src="<?php echo plugins_url( 'assets/images/token-sitting.png', myCRED_THIS ); ?>" alt="Token looking on" /><?php endif; ?>
	<div class="mycred-container">
		<form class="form" method="post" action="" id="mycred-editor-form">
			<input type="hidden" name="mycred_manage_balance[type]" value="" id="mycred-edit-balance-of-type" />
			<input type="hidden" name="mycred_manage_balance[user_id]" value="" id="mycred-edit-balance-of-user" />

			<div class="row">
				<div class="col-sm-2 col-xs-6">
					<div class="form-group">
						<label><?php _e( 'ID', 'mycred' ); ?></label>
						<div id="mycred-userid-to-show">&nbsp;</div>
					</div>
				</div>
				<div class="col-sm-4 col-xs-6">
					<div class="form-group">
						<label><?php _e( 'Username', 'mycred' ); ?></label>
						<div id="mycred-username-to-show">&nbsp;</div>
					</div>
				</div>
				<div class="col-sm-3 col-xs-6">
					<div class="form-group">
						<label><?php _e( 'Current Balance', 'mycred' ); ?></label>
						<div id="mycred-current-to-show">&nbsp;</div>
					</div>
				</div>
				<div class="col-sm-3 col-xs-6">
					<div class="form-group">
						<label><?php _e( 'Total Balance', 'mycred' ); ?></label>
						<div id="mycred-total-to-show">&nbsp;</div>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-sm-2 col-xs-12">
					<div class="form-group">
						<label><?php _e( 'Amount', 'mycred' ); ?></label>
						<input type="text" name="mycred_manage_balance[amount]" id="mycred-editor-amount" size="8" placeholder="0" value="" />
						<span class="description"><?php _e( 'A positive or negative value', 'mycred' ); ?>.</span>
					</div>
				</div>
				<div class="col-sm-5 col-xs-12">
					<div class="form-group">
						<label><?php _e( 'Reference', 'mycred' ); ?></label>
						<select name="mycred_manage_balance[ref]" id="mycred-editor-reference">
<?php

				foreach ( $references as $ref_id => $ref_label ) {
					echo '<option value="' . $ref_id . '"';
					if ( $ref_id == $this->manual_reference ) echo ' selected="selected"';
					echo '>' . $ref_label . '</option>';
				}

				echo '<option value="mycred_custom">' . __( 'Log under a custom reference', 'mycred' ) . '</option>';

?>
						</select>
					</div>
					<div id="mycred-custom-reference-wrapper" style="display: none;">
						<input type="text" name="mycred_manage_balance[custom]" id="mycred-editor-custom-reference" placeholder="<?php _e( 'lowercase without empty spaces', 'mycred' ); ?>" class="regular-text" value="" />
					</div>
				</div>
				<div class="col-sm-5 col-xs-12">
					<div class="form-group">
						<label><?php _e( 'Log Entry', 'mycred' ); ?></label>
						<input type="text" name="mycred_manage_balance[entry]" id="mycred-editor-entry" placeholder="<?php _e( 'optional', 'mycred' ); ?>" class="regular-text" value="" />
						<span class="description"><?php echo $mycred->available_template_tags( array( 'general', 'amount' ) ); ?></span>
					</div>
				</div>
			</div>

			<div class="row last">
				<div class="col-sm-2 col-xs-3"><input type="submit" id="mycred-editor-submit" class="button button-primary button-large" value="<?php _e( 'Update', 'mycred' ); ?>" /></div>
				<div class="col-sm-1 col-xs-1"><span id="mycred-editor-indicator" class="spinner"></span></div>
				<div class="col-sm-6 col-xs-4" id="mycred-editor-results"></div>
				<div class="col-sm-3 col-xs-4 text-right"><button type="button" class="button button-secondary button-large" id="load-users-mycred-history"><?php _e( 'Recent Activity', 'mycred' ); ?></button></div>
			</div>
		</form>

		<div id="mycred-users-mini-ledger" style="display: none;">
			<div class="border">
				<div id="mycred-processing"><div class="loading-indicator"></div></div>
			</div>
		</div>
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
endif;
