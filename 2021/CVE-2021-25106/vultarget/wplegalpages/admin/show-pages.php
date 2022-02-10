<?php
/**
 * Provide a admin area view for the show legal pages.
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @package    Wplegalpages
 * @subpackage Wplegalpages/admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="wrap">
<?php
$lp_pro_active = get_option( '_lp_pro_active' );
if ( '1' !== $lp_pro_active ) :
	?>
<div style="">
	<div style="line-height: 2.4em;" class='wplegalpages-pro-promotion'>
		<a href="https://club.wpeka.com/product/wplegalpages/?utm_source=plugin-banner&utm_campaign=wplegalpages&utm_content=upgrade-to-pro" target="_blank">
			<img alt="Upgrade to Pro" src="<?php echo esc_attr( WPL_LITE_PLUGIN_URL ) . 'admin/images/upgrade-to-pro.jpg'; ?>">
		</a>
	</div>
</div>
<div style="clear:both;"></div>
	<?php
endif;

if ( ( isset( $_REQUEST['mode'] ) && 'delete' === $_REQUEST['mode'] && current_user_can( 'manage_options' ) ) && isset( $_REQUEST['_wpnonce'] ) ) {
	if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_REQUEST['_wpnonce'] ) ), 'my-nonce' ) ) {
		wp_die( esc_attr__( 'Security Check.', 'wplegalpages' ) );
	}

	if ( isset( $_REQUEST['pid'] ) ) {
		if ( ! wp_trash_post( sanitize_text_field( wp_unslash( $_REQUEST['pid'] ) ) ) ) {
			wp_die( esc_attr__( 'Error in moving to Trash.', 'wplegalpages' ) );
		}
	}
	?>
		<div id="message" >
			<p><span class="label label-success myAlert">Legal page moved to trash.</span></p>
		</div>

	<?php
}
$current_page = isset( $_REQUEST['page'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['page'] ) ) : '';
?>
<h2 class="hndle myLabel-head"> <?php esc_attr_e( 'Available Pages', 'wplegalpages' ); ?> </h2>
<table class="widefat fixed comments table table-striped">
	<thead>
		<tr>
			<th width="5%"><?php esc_attr_e( 'S.No.', 'wplegalpages' ); ?></th>
			<th width="30%"><?php esc_attr_e( 'Page Title', 'wplegalpages' ); ?></th>
			<th width="10%"><?php esc_attr_e( 'Page ID', 'wplegalpages' ); ?></th>
			<th width="10%"><?php esc_attr_e( 'Author', 'wplegalpages' ); ?></th>
			<th width="10%"><?php esc_attr_e( 'Date', 'wplegalpages' ); ?></th>
			<th width="10%"><?php esc_attr_e( 'Action', 'wplegalpages' ); ?></th>
		</tr>
	</thead>
	<tbody>

	<?php
		global $wpdb;
		$post_tbl     = $wpdb->prefix . 'posts';
		$postmeta_tbl = $wpdb->prefix . 'postmeta';
		$pagesresult  = $wpdb->get_results( $wpdb->prepare( 'SELECT ptbl.* FROM ' . $post_tbl . ' as ptbl , ' . $postmeta_tbl . ' as pmtbl WHERE ptbl.ID = pmtbl.post_id and ptbl.post_status = %s AND pmtbl.meta_key = %s', array( 'publish', 'is_legal' ) ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared,WordPress.DB.DirectDatabaseQuery,WordPress.DB.DirectDatabaseQuery.NoCaching

	if ( $pagesresult ) {
		$nonce    = wp_create_nonce( 'my-nonce' );
		$count    = 1;
		$user_tbl = $wpdb->prefix . 'users';
		foreach ( $pagesresult as $res ) {
				$url     = get_permalink( $res->ID );
				$author  = $wpdb->get_results( $wpdb->prepare( 'SELECT utbl.user_login FROM ' . $post_tbl . ' as ptbl, ' . $user_tbl . ' as utbl WHERE ptbl.post_author = utbl.ID and ptbl.ID = %d', array( $res->ID ) ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared,WordPress.DB.DirectDatabaseQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
				$delurl  = isset( $_SERVER['PHP_SELF'] ) ? esc_url_raw( wp_unslash( $_SERVER['PHP_SELF'] ) ) : '';
				$delurl .= "?pid=$res->ID&page=$current_page&mode=delete&_wpnonce=$nonce";
			?>
			<tr>
				<td><?php echo esc_attr( $count ); ?></td>
				<td><?php echo esc_attr( $res->post_title ); ?></td>
				<td><?php echo esc_attr( $res->ID ); ?></td>
				<td><?php echo esc_attr( ucfirst( $author[0]->user_login ) ); ?></td>
				<td><?php echo esc_attr( gmdate( 'Y/m/d', strtotime( $res->post_date ) ) ); ?></td>
				<td>
					<a href="<?php echo esc_attr( get_admin_url() ); ?>/post.php?post=<?php echo esc_attr( $res->ID ); ?>&action=edit"><?php esc_attr_e( 'Edit', 'wplegalpages' ); ?></a> | <a href="<?php echo esc_url_raw( $url ); ?>"><?php esc_attr_e( 'View', 'wplegalpages' ); ?></a>| <a href="<?php echo esc_url_raw( $delurl ); ?>"><?php esc_attr_e( 'Trash', 'wplegalpages' ); ?></a>
				</td>
			</tr>
				<?php
				$count++;
		}
		?>

		<?php } else { ?>
		<tr>
			<td colspan="3"><?php esc_attr_e( 'No page yet', 'wplegalpages' ); ?></td>
		</tr>
	<?php } ?>
	</tbody>
	<tfoot>
		<tr>
			<th width="5%"><?php esc_attr_e( 'S.No.', 'wplegalpages' ); ?></th>
			<th width="30%"><?php esc_attr_e( 'Page Title', 'wplegalpages' ); ?></th>
			<th width="10%"><?php esc_attr_e( 'Page ID', 'wplegalpages' ); ?></th>
			<th width="10%"><?php esc_attr_e( 'Author', 'wplegalpages' ); ?></th>
			<th width="10%"><?php esc_attr_e( 'Date', 'wplegalpages' ); ?></th>
			<th width="10%"><?php esc_attr_e( 'Action', 'wplegalpages' ); ?></th>
		</tr>
	</tfoot>
	</table>
</div>
