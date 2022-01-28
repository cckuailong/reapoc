<?php
/**
 * html-action-send-to-email file.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Send to mail action html fields
 */
foreach ( $this->get_action_fields() as $field ) {
	$this->render_field( $field, $prefix );
}
