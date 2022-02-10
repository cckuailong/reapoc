<?php
/**
 * Upgrade Database.
 *
 * @category Core
 * @package  My Calendar
 * @author   Joe Dolson
 * @license  GPLv2 or later
 * @link     https://www.joedolson.com/my-calendar/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Check whether the My Calendar database is up to date
 */
function my_calendar_check_db() {
	if ( 'true' === get_option( 'mc_remote' ) && function_exists( 'mc_remote_db' ) ) {
		return;
	}

	global $wpdb;
	$cols         = $wpdb->get_col( 'DESC ' . my_calendar_table() ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
	$needs_update = false;

	if ( ! in_array( 'event_tickets', $cols, true ) ) {
		$needs_update = true;
	}

	if ( isset( $_POST['upgrade'] ) && 'true' === $_POST['upgrade'] ) {
		mc_upgrade_db();
		?>
		<div class='upgrade-db updated'>
			<p><?php _e( 'My Calendar Database is updated.', 'my-calendar' ); ?></p>
		</div>
		<?php
	} elseif ( $needs_update ) {
		if ( 'my-calendar-config' === $_GET['page'] ) {
			?>
			<div class='upgrade-db error'>
				<p>
					<?php _e( 'The My Calendar database needs to be updated.', 'my-calendar' ); ?>
				</p>
				<form method="post" action="<?php echo admin_url( 'admin.php?page=my-calendar-config' ); ?>">
					<div>
						<input type="hidden" name="_wpnonce" value="<?php echo wp_create_nonce( 'my-calendar-nonce' ); ?>" />
						<input type="hidden" name="upgrade" value="true" />
					</div>
					<p>
						<input type="submit" value="<?php _e( 'Update now', 'my-calendar' ); ?>" name="update-calendar" class="button-primary"/>
					</p>
				</form>
			</div>
			<?php
		} else {
			?>
			<div class='upgrade-db error'>
			<p>
				<?php _e( 'The My Calendar database needs to be updated.', 'my-calendar' ); ?>
				<a href="<?php echo admin_url( 'admin.php?page=my-calendar-config' ); ?>"><?php _e( 'Update now', 'my-calendar' ); ?></a>
			</p>
			</div>
			<?php
		}
	}
}

/**
 * Execute DB upgrade.
 */
function mc_upgrade_db() {
	$globals = mc_globals();
	foreach ( $globals as $key => $global ) {
		${$key} = $global;
	}
	global $mc_version;

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $initial_db );
	dbDelta( $initial_occur_db );
	dbDelta( $initial_cat_db );
	dbDelta( $initial_rel_db );
	dbDelta( $initial_loc_db );
	update_option( 'mc_db_version', $mc_version );
}
