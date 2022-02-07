<?php
/**
 * Database Result
 *
 * @package     Wow Plugin
 * @subpackage  Admin/Database_result
 * @author      Wow-Company <support@wow-company.com>
 * @copyright   2019 Wow-Company
 * @license     GNU Public License
 * @version     1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $wpdb;
$data = $wpdb->prefix . 'wow_' . $this->plugin['prefix'];
$info = ( isset( $_REQUEST['info'] ) ) ? sanitize_text_field( $_REQUEST['info'] ) : '';
if ( $info == 'saved' ) {
	$message_data = esc_attr__( 'Item Added', $this->plugin['text'] );
	echo '<div class="updated" id="message"><p><strong>' . $message_data . '</strong>.</p></div>';
} elseif ( $info == 'update' ) {
	$message_data = esc_attr__( 'Item Updated', $this->plugin['text'] );
	echo '<div class="updated" id="message"><p><strong>' . $message_data . '</strong>.</p></div>';
} elseif ( $info == 'delete' ) {
	$delid = absint( $_GET['did'] );
	$wpdb->query( 'delete from ' . $data . ' where id=' . $delid );
	$message_data = esc_attr__( 'Item Deleted', $this->plugin['text'] );
	echo '<div class="updated" id="message"><p><strong>' . $message_data . '</strong>.</p></div>';
}
